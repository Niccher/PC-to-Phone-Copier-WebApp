<?php

namespace App\Models;

use CodeIgniter\Model;

class ModUpload extends Model
{
    public function file_register_uploaded($file_info)
    {
        return $this->db->table('tbl_files_uploaded')->insert($file_info);
    }

    public function get_session_storage_used($sess_id)
    {
        $active_query = $this->db->table('tbl_files_uploaded')
            ->selectSum('up_file_Size', 'total_size')
            ->where('up_file_session_id', $sess_id)
            ->get()->getRow();
        $active_size = $active_query->total_size ?? 0;

        $deleted_size = 0;
        // Check trash to ensure quota realistically accounts for physical storage presence
        $columns = $this->db->query("SHOW TABLES LIKE 'tbl_files_uploaded_deleted'")->getResult();
        if (!empty($columns)) {
            $deleted_query = $this->db->table('tbl_files_uploaded_deleted')
                ->selectSum('up_file_Size', 'total_size')
                ->where('up_file_session_id', $sess_id)
                ->get()->getRow();
            $deleted_size = $deleted_query->total_size ?? 0;
        }

        return $active_size + $deleted_size;
    }

    function bytes_to_human_filesize($bytes, $dec = 2): string
    {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor == 0)
            $dec = 0;
        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }

    public function file_get_uploaded_files($sess_id, $limit = null)
    {
        $this->ensureColumnsExist();
        $this->cleanupExpiredFiles($sess_id);

        $builder = $this->db->table('tbl_files_uploaded');
        $builder->orderBy('up_file_Created_at', 'DESC')
            ->where('up_file_session_id', $sess_id);

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->get()->getResult();
    }

    public function file_get_uploaded_by_devid($devid)
    {
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_dev_id', $devid)
            ->get();
        return $get_all->getResult();
    }

    public function file_get_uploaded_by_file_uuid($file_uuid)
    {
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_uuid', $file_uuid)
            ->get();
        return $get_all->getResult();
    }

    public function file_get_uploaded_files_by_session_and_devid($sess_id, $devid)
    {
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_session_id', $sess_id)
            ->where('up_file_dev_id', $devid)
            ->get();
        return $get_all->getResult();
    }

    public function file_uploaded_by_phone_session_download($phone_file_id, $phone_sess_id, $phone_dev_id)
    {
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_uuid', $phone_file_id)
            ->where('up_file_session_id', $phone_sess_id)
            ->where('up_file_dev_id', $phone_dev_id)
            ->get();
        return $get_all->getResult()[0];
    }

    public function file_to_delete($phone_file_uuid, $phone_file_name)
    {
        $builder = $this->db->table('tbl_files_uploaded');
        $get_info = $builder
            ->where('up_file_uuid', $phone_file_uuid)
            ->where('up_file_name', $phone_file_name)
            ->get();

        if ($get_info->getNumRows() == 0)
            return false;

        $file_info = (array)$get_info->getRow();

        // Delete from main table
        $this->db->table('tbl_files_uploaded')
            ->where('up_file_uuid', $phone_file_uuid)
            ->where('up_file_name', $phone_file_name)
            ->delete();

        // Filter keys for tbl_files_uploaded_deleted
        $target_fields = $this->db->getFieldNames('tbl_files_uploaded_deleted');
        $insert_data = array_intersect_key($file_info, array_flip($target_fields));

        // Only add deleted_at if the column exists
        if (in_array('deleted_at', $target_fields)) {
            $insert_data['deleted_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->table('tbl_files_uploaded_deleted')->ignore(true)->insert($insert_data);
    }

    // New methods for file preview and management

    public function update_file_metadata($file_uuid, $metadata)
    {
        return $this->db->table('tbl_files_uploaded')
            ->where('up_file_uuid', $file_uuid)
            ->update($metadata);
    }

    public function update_file_description($file_uuid, $description)
    {
        return $this->update_file_metadata($file_uuid, ['up_file_description' => $description]);
    }

    public function search_files($sess_id, $search_term = '', $category = '', $tags = [], $file_type = '')
    {
        $this->ensureColumnsExist();
        $this->cleanupExpiredFiles($sess_id);

        $builder = $this->db->table('tbl_files_uploaded');

        $builder->where('up_file_session_id', $sess_id);

        if (!empty($search_term)) {
            $builder->groupStart()
                ->like('up_file_Orig_Name', $search_term)
                ->orLike('up_file_tags', $search_term)
                ->orLike('up_file_description', $search_term)
                ->groupEnd();
        }

        if (!empty($category)) {
            $builder->where('up_file_category', $category);
        }

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $builder->like('up_file_tags', $tag);
            }
        }

        if (!empty($file_type)) {
            $builder->where('up_file_Extension', $file_type);
        }

        return $builder->orderBy('up_file_Created_at', 'DESC')->get()->getResult();
    }

    public function get_file_categories()
    {
        return $this->db->table('tbl_file_categories')
            ->orderBy('category_name')
            ->get()
            ->getResult();
    }

    public function get_file_tags()
    {
        return $this->db->table('tbl_file_tags')
            ->orderBy('tag_name')
            ->get()
            ->getResult();
    }

    public function add_file_tag($file_uuid, $tag_name)
    {
        $file = $this->file_get_uploaded_by_file_uuid($file_uuid);
        if (empty($file))
            return false;

        $current_tags = $file[0]->up_file_tags ? explode(',', $file[0]->up_file_tags) : [];

        if (!in_array($tag_name, $current_tags)) {
            $current_tags[] = $tag_name;
            $tags_string = implode(',', $current_tags);

            return $this->update_file_metadata($file_uuid, ['up_file_tags' => $tags_string]);
        }

        return true;
    }

    public function remove_file_tag($file_uuid, $tag_name)
    {
        $file = $this->file_get_uploaded_by_file_uuid($file_uuid);
        if (empty($file))
            return false;

        $current_tags = $file[0]->up_file_tags ? explode(',', $file[0]->up_file_tags) : [];
        $current_tags = array_diff($current_tags, [$tag_name]);
        $tags_string = implode(',', $current_tags);

        return $this->update_file_metadata($file_uuid, ['up_file_tags' => $tags_string]);
    }

    public function rename_file($file_uuid, $new_name)
    {
        return $this->update_file_metadata($file_uuid, ['up_file_Orig_Name' => $new_name]);
    }

    public function batch_update_files($file_uuids, $updates)
    {
        return $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->update($updates);
    }

    public function batch_delete_files($file_uuids)
    {
        // Get file info first
        $files = $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->get()
            ->getResult();

        // Move to deleted table with deleted_at timestamp
        $target_fields = $this->db->getFieldNames('tbl_files_uploaded_deleted');
        foreach ($files as $file) {
            $fileArray = (array)$file;
            $insert_data = array_intersect_key($fileArray, array_flip($target_fields));

            // Only add deleted_at if the column exists
            if (in_array('deleted_at', $target_fields)) {
                $insert_data['deleted_at'] = date('Y-m-d H:i:s');
            }
            $this->db->table('tbl_files_uploaded_deleted')->ignore(true)->insert($insert_data);
        }

        // Delete from main table
        return $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->delete();
    }

    public function get_deleted_files($sess_id)
    {
        // Try to order by deleted_at if column exists, otherwise use up_file_Created_at
        $query = $this->db->table('tbl_files_uploaded_deleted')
            ->where('up_file_session_id', $sess_id);

        // Check if deleted_at column exists
        $columns = $this->db->query("SHOW COLUMNS FROM `tbl_files_uploaded_deleted` LIKE 'deleted_at'")->getResult();
        if (!empty($columns)) {
            $query->orderBy('deleted_at', 'DESC');
        }
        else {
            $query->orderBy('up_file_Created_at', 'DESC');
        }

        return $query->get()->getResult();
    }

    public function get_deleted_file_by_uuid($file_uuid, $sess_id)
    {
        return $this->db->table('tbl_files_uploaded_deleted')
            ->where('up_file_uuid', $file_uuid)
            ->where('up_file_session_id', $sess_id)
            ->get()
            ->getRow();
    }

    public function restore_file($file)
    {
        // Insert back to main table
        $this->db->table('tbl_files_uploaded')->insert((array)$file);
        // Remove from deleted table
        return $this->db->table('tbl_files_uploaded_deleted')
            ->where('up_file_uuid', $file->up_file_uuid)
            ->delete();
    }

    public function permanent_delete_file($file_uuid, $sess_id)
    {
        return $this->db->table('tbl_files_uploaded_deleted')
            ->where('up_file_uuid', $file_uuid)
            ->where('up_file_session_id', $sess_id)
            ->delete();
    }

    public function empty_trash_files($sess_id)
    {
        return $this->db->table('tbl_files_uploaded_deleted')
            ->where('up_file_session_id', $sess_id)
            ->delete();
    }

    /**
     * Ensure preview-related columns exist in tbl_files_uploaded.
     * Safe to call multiple times — uses SHOW COLUMNS before ALTER.
     */
    public function ensureColumnsExist()
    {
        $table = 'tbl_files_uploaded';
        $columns = [
            'up_file_thumbnail' => "VARCHAR(255) DEFAULT NULL",
            'up_file_tags' => "TEXT DEFAULT NULL",
            'up_file_category' => "VARCHAR(100) DEFAULT NULL",
            'up_file_description' => "TEXT DEFAULT NULL",
            'up_file_preview_available' => "TINYINT(1) DEFAULT 0",
            'up_file_width' => "INT(11) DEFAULT NULL",
            'up_file_height' => "INT(11) DEFAULT NULL",
            'up_file_expiration_policy' => "TINYINT(1) DEFAULT 0",
            'up_file_expires_at' => "DATETIME DEFAULT NULL",
        ];

        $existing = [];
        $result = $this->db->query("SHOW COLUMNS FROM `{$table}`")->getResult();
        foreach ($result as $col) {
            $existing[] = $col->Field;
        }

        foreach ($columns as $col => $definition) {
            if (!in_array($col, $existing)) {
                $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$definition}");
            }
        }
    }

    public function cleanupExpiredFiles($sess_id)
    {
        $expired = $this->db->table('tbl_files_uploaded')
            ->where('up_file_session_id', $sess_id)
            ->where('up_file_expires_at IS NOT NULL')
            ->where('up_file_expires_at <', date('Y-m-d H:i:s'))
            ->get()->getResult();

        if (!empty($expired)) {
            $uuids = array_column($expired, 'up_file_uuid');
            $this->batch_delete_files($uuids);
        }
    }

    public function getFileIconClass($extension)
    {
        $ext = strtolower($extension);
        $map = [
            'pdf' => ['icon' => 'file-pdf-box', 'color' => 'text-danger'],
            'doc' => ['icon' => 'file-word-box', 'color' => 'text-primary'],
            'docx' => ['icon' => 'file-word-box', 'color' => 'text-primary'],
            'xls' => ['icon' => 'file-excel-box', 'color' => 'text-success'],
            'xlsx' => ['icon' => 'file-excel-box', 'color' => 'text-success'],
            'ppt' => ['icon' => 'file-powerpoint-box', 'color' => 'text-warning'],
            'pptx' => ['icon' => 'file-powerpoint-box', 'color' => 'text-warning'],
            'txt' => ['icon' => 'file-document-box', 'color' => 'text-secondary'],
            'jpg' => ['icon' => 'file-image', 'color' => 'text-info'],
            'jpeg' => ['icon' => 'file-image', 'color' => 'text-info'],
            'png' => ['icon' => 'file-image', 'color' => 'text-info'],
            'gif' => ['icon' => 'file-image', 'color' => 'text-info'],
            'webp' => ['icon' => 'file-image', 'color' => 'text-info'],
            'bmp' => ['icon' => 'file-image', 'color' => 'text-info'],
            'mp4' => ['icon' => 'file-video', 'color' => 'text-danger'],
            'avi' => ['icon' => 'file-video', 'color' => 'text-danger'],
            'mov' => ['icon' => 'file-video', 'color' => 'text-danger'],
            'wmv' => ['icon' => 'file-video', 'color' => 'text-danger'],
            'webm' => ['icon' => 'file-video', 'color' => 'text-danger'],
            'mp3' => ['icon' => 'file-music', 'color' => 'text-warning'],
            'wav' => ['icon' => 'file-music', 'color' => 'text-warning'],
            'flac' => ['icon' => 'file-music', 'color' => 'text-warning'],
            'aac' => ['icon' => 'file-music', 'color' => 'text-warning'],
            'ogg' => ['icon' => 'file-music', 'color' => 'text-warning'],
            'zip' => ['icon' => 'folder-zip-outline', 'color' => 'text-secondary'],
            'rar' => ['icon' => 'folder-zip-outline', 'color' => 'text-secondary'],
            '7z' => ['icon' => 'folder-zip-outline', 'color' => 'text-secondary'],
            'tar' => ['icon' => 'folder-zip-outline', 'color' => 'text-secondary'],
            'gz' => ['icon' => 'folder-zip-outline', 'color' => 'text-secondary'],
            'html' => ['icon' => 'language-html5', 'color' => 'text-danger'],
            'css' => ['icon' => 'language-css3', 'color' => 'text-primary'],
            'js' => ['icon' => 'language-javascript', 'color' => 'text-warning'],
            'php' => ['icon' => 'language-php', 'color' => 'text-info'],
            'py' => ['icon' => 'language-python', 'color' => 'text-success'],
            'json' => ['icon' => 'code-json', 'color' => 'text-secondary'],
            'xml' => ['icon' => 'file-xml-box', 'color' => 'text-secondary'],
            'sql' => ['icon' => 'database', 'color' => 'text-info'],
        ];
        return $map[$ext] ?? ['icon' => 'file-outline', 'color' => 'text-muted'];
    }
}