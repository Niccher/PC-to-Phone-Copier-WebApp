<?php

namespace App\Models;

use CodeIgniter\Model;

class ModUpload extends Model
{
    protected $table = 'tbl_files';
    protected $primaryKey = 'id';

    public function file_make_uploaded_files($file_info)
    {
        $data = [
            'uuid'          => $file_info['up_file_uuid'] ?? $file_info['uuid'] ?? null,
            'session_id'    => $file_info['up_file_session_id'] ?? $file_info['session_id'] ?? null,
            'original_name' => $file_info['up_file_Orig_Name'] ?? $file_info['original_name'] ?? null,
            'system_name'   => $file_info['up_file_Sys_Name'] ?? $file_info['system_name'] ?? null,
            'file_type'     => $file_info['up_file_Type'] ?? $file_info['file_type'] ?? null,
            'file_size'     => $file_info['up_file_Size'] ?? $file_info['file_size'] ?? 0,
            'created_at'    => $file_info['up_file_Created_at'] ?? $file_info['created_at'] ?? date('Y-m-d H:i:s'),
        ];
        return $this->db->table('tbl_files')->insert($data);
    }

    public function file_register_uploaded($file_info)
    {
        return $this->file_make_uploaded_files($file_info);
    }

    public function get_session_storage_used($sess_id)
    {
        $active_query = $this->db->table('tbl_files')
            ->selectSum('file_size', 'total_size')
            ->where('session_id', $sess_id)
            ->get()->getRow();
        $active_size = $active_query->total_size ?? 0;

        $deleted_size = 0;
        if ($this->db->tableExists('tbl_files_trash')) {
            $deleted_query = $this->db->table('tbl_files_trash')
                ->selectSum('file_size', 'total_size')
                ->where('session_id', $sess_id)
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
        $builder = $this->db->table('tbl_files');
        $builder->select('*, uuid as up_file_uuid, session_id as up_file_session_id, session_id as up_file_dev_id, original_name as up_file_Name, original_name as up_file_Orig_Name, system_name as up_file_Sys_Name, file_type as up_file_Type, SUBSTRING_INDEX(original_name, ".", -1) as up_file_Extension, file_size as up_file_Size, created_at as up_file_Created_at, thumbnail_path as up_file_thumbnail')
            ->orderBy('created_at', 'DESC')
            ->where('session_id', $sess_id);

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->get()->getResult();
    }

    public function file_get_uploaded_by_file_uuid($file_uuid)
    {
        $builder = $this->db->table('tbl_files');
        $get_all = $builder
            ->select('*, uuid as up_file_uuid, session_id as up_file_session_id, original_name as up_file_Orig_Name, system_name as up_file_Sys_Name, file_type as up_file_Type, file_size as up_file_Size, created_at as up_file_Created_at, thumbnail_path as up_file_thumbnail')
            ->where('uuid', $file_uuid)
            ->get();
        return $get_all->getResult();
    }

    public function file_delete_uploaded_files($where)
    {
        $uuid = $where['up_file_uuid'] ?? $where['uuid'] ?? null;
        if (!$uuid) return false;

        $builder = $this->db->table('tbl_files');
        $get_info = $builder->where('uuid', $uuid)->get()->getRowArray();
        if (!$get_info) return false;

        $get_info['deleted_at'] = date('Y-m-d H:i:s');
        $this->db->table('tbl_files')->where('uuid', $uuid)->delete();
        return $this->db->table('tbl_files_trash')->ignore(true)->insert($get_info);
    }

    public function file_to_delete($phone_file_uuid, $phone_file_name = null)
    {
        return $this->file_delete_uploaded_files(['uuid' => $phone_file_uuid]);
    }

    public function get_deleted_files($sess_id)
    {
        return $this->db->table('tbl_files_trash')
            ->select('*, uuid as up_file_uuid, session_id as up_file_session_id, original_name as up_file_Orig_Name, system_name as up_file_Sys_Name, file_type as up_file_Type, file_size as up_file_Size, created_at as up_file_Created_at, thumbnail_path as up_file_thumbnail')
            ->where('session_id', $sess_id)
            ->orderBy('deleted_at', 'DESC')
            ->get()
            ->getResult();
    }

    public function get_deleted_file_by_uuid($file_uuid, $sess_id)
    {
        return $this->db->table('tbl_files_trash')
            ->select('*, uuid as up_file_uuid, session_id as up_file_session_id, original_name as up_file_Orig_Name, system_name as up_file_Sys_Name, file_type as up_file_Type, file_size as up_file_Size, created_at as up_file_Created_at, thumbnail_path as up_file_thumbnail')
            ->where('uuid', $file_uuid)
            ->where('session_id', $sess_id)
            ->get()
            ->getRow();
    }

    public function restore_file($file)
    {
        $arr = (array)$file;
        unset($arr['deleted_at']);
        $this->db->table('tbl_files')->insert($arr);
        $uuid = $file->uuid ?? $file->up_file_uuid;
        return $this->db->table('tbl_files_trash')
            ->where('uuid', $uuid)
            ->delete();
    }

    public function permanent_delete_file($file_uuid, $sess_id)
    {
        return $this->db->table('tbl_files_trash')
            ->where('uuid', $file_uuid)
            ->where('session_id', $sess_id)
            ->delete();
    }

    public function empty_trash_files($sess_id)
    {
        return $this->db->table('tbl_files_trash')
            ->where('session_id', $sess_id)
            ->delete();
    }

    public function ensureColumnsExist()
    {
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