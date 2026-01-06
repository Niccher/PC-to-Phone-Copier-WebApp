<?php

namespace App\Models;

use CodeIgniter\Model;

class ModUpload extends Model
{
    public function file_register_uploaded($file_info){
        return $this->db->table('tbl_files_uploaded')->insert($file_info);
    }

    function bytes_to_human_filesize($bytes, $dec = 2): string {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor == 0) $dec = 0;
        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }

    public function file_get_uploaded_files($sess_id, $limit = null){
        $builder = $this->db->table('tbl_files_uploaded');
        $builder->orderBy('up_file_Created_at', 'DESC')
            ->where('up_file_session_id', $sess_id);

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->get()->getResult();
    }

    public function file_get_uploaded_by_devid($devid){
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_dev_id', $devid)
            ->get();
        return $get_all->getResult();
    }

    public function file_get_uploaded_by_file_uuid($file_uuid){
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_uuid', $file_uuid)
            ->get();
        return $get_all->getResult();
    }

    public function file_get_uploaded_files_by_session_and_devid($sess_id, $devid){
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_session_id', $sess_id)
            ->where('up_file_dev_id', $devid)
            ->get();
        return $get_all->getResult();
    }

    public function file_uploaded_by_phone_session_download($phone_file_id,$phone_sess_id, $phone_dev_id){
        $builder = $this->db->table('tbl_files_uploaded');
        $get_all = $builder
            ->orderBy('up_file_count', 'DESC')
            ->where('up_file_uuid', $phone_file_id)
            ->where('up_file_session_id', $phone_sess_id)
            ->where('up_file_dev_id', $phone_dev_id)
            ->get();
        return $get_all->getResult()[0];
    }

    public function file_to_delete($phone_file_uuid, $phone_file_name){
        $builder = $this->db->table('tbl_files_uploaded');
        $get_info = $builder
            ->where('up_file_uuid', $phone_file_uuid)
            ->where('up_file_name', $phone_file_name)
            //->where('up_file_dev_id', $phone_dev_id)
            ->get();

        $file_info =  $get_info->getResult()[0];

        $builder
            ->where('up_file_uuid', $phone_file_uuid)
            ->where('up_file_name', $phone_file_name)
            ->delete();

        return $this->db->table('tbl_files_uploaded_deleted')->insert($file_info);
    }

    // New methods for file preview and management

    public function update_file_metadata($file_uuid, $metadata) {
        return $this->db->table('tbl_files_uploaded')
            ->where('up_file_uuid', $file_uuid)
            ->update($metadata);
    }

    public function update_file_description($file_uuid, $description) {
        return $this->update_file_metadata($file_uuid, ['up_file_description' => $description]);
    }

    public function search_files($sess_id, $search_term = '', $category = '', $tags = [], $file_type = '') {
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

    public function get_file_categories() {
        return $this->db->table('tbl_file_categories')
            ->orderBy('category_name')
            ->get()
            ->getResult();
    }

    public function get_file_tags() {
        return $this->db->table('tbl_file_tags')
            ->orderBy('tag_name')
            ->get()
            ->getResult();
    }

    public function add_file_tag($file_uuid, $tag_name) {
        $file = $this->file_get_uploaded_by_file_uuid($file_uuid);
        if (empty($file)) return false;

        $current_tags = $file[0]->up_file_tags ? explode(',', $file[0]->up_file_tags) : [];

        if (!in_array($tag_name, $current_tags)) {
            $current_tags[] = $tag_name;
            $tags_string = implode(',', $current_tags);

            return $this->update_file_metadata($file_uuid, ['up_file_tags' => $tags_string]);
        }

        return true;
    }

    public function remove_file_tag($file_uuid, $tag_name) {
        $file = $this->file_get_uploaded_by_file_uuid($file_uuid);
        if (empty($file)) return false;

        $current_tags = $file[0]->up_file_tags ? explode(',', $file[0]->up_file_tags) : [];
        $current_tags = array_diff($current_tags, [$tag_name]);
        $tags_string = implode(',', $current_tags);

        return $this->update_file_metadata($file_uuid, ['up_file_tags' => $tags_string]);
    }

    public function rename_file($file_uuid, $new_name) {
        return $this->update_file_metadata($file_uuid, ['up_file_Orig_Name' => $new_name]);
    }

    public function batch_update_files($file_uuids, $updates) {
        return $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->update($updates);
    }

    public function batch_delete_files($file_uuids) {
        // Get file info first
        $files = $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->get()
            ->getResult();

        // Move to deleted table
        foreach ($files as $file) {
            $this->db->table('tbl_files_uploaded_deleted')->insert((array)$file);
        }

        // Delete from main table
        return $this->db->table('tbl_files_uploaded')
            ->whereIn('up_file_uuid', $file_uuids)
            ->delete();
    }
}
