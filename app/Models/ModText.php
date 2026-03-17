<?php

namespace App\Models;

use CodeIgniter\Model;

class ModText extends Model
{
    public function text_register_uploaded($text_info){
        return $this->db->table('tbl_texts_uploaded')->insert($text_info);
    }

    public function text_get_uploaded_texts($sess_id, $limit = null){
        $builder = $this->db->table('tbl_texts_uploaded');
        $builder->orderBy('text_created_at', 'DESC')
            ->where('text_session_id', $sess_id);

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->get()->getResult();
    }

    public function text_get_uploaded_by_uuid($text_uuid){
        $builder = $this->db->table('tbl_texts_uploaded');
        $get_all = $builder
            ->orderBy('text_count', 'DESC')
            ->where('text_uuid', $text_uuid)
            ->get();
        return $get_all->getResult();
    }

    public function text_to_delete($text_uuid){
        $builder = $this->db->table('tbl_texts_uploaded');
        $get_info = $builder
            ->where('text_uuid', $text_uuid)
            ->get();

        $text_info = $get_info->getResult()[0];

        $builder
            ->where('text_uuid', $text_uuid)
            ->delete();

        return $this->db->table('tbl_texts_uploaded_deleted')->insert($text_info);
    }

    public function get_deleted_texts($sess_id) {
        // Try to order by deleted_at if column exists, otherwise use text_created_at
        $query = $this->db->table('tbl_texts_uploaded_deleted')
            ->where('text_session_id', $sess_id);

        // Check if deleted_at column exists
        $columns = $this->db->query("SHOW COLUMNS FROM `tbl_texts_uploaded_deleted` LIKE 'deleted_at'")->getResult();
        if (!empty($columns)) {
            $query->orderBy('deleted_at', 'DESC');
        } else {
            $query->orderBy('text_created_at', 'DESC');
        }

        return $query->get()->getResult();
    }

    public function get_deleted_text_by_uuid($text_uuid, $sess_id) {
        return $this->db->table('tbl_texts_uploaded_deleted')
            ->where('text_uuid', $text_uuid)
            ->where('text_session_id', $sess_id)
            ->get()
            ->getRow();
    }

    public function restore_text($text) {
        // Insert back to main table
        $this->db->table('tbl_texts_uploaded')->insert((array)$text);
        // Remove from deleted table
        return $this->db->table('tbl_texts_uploaded_deleted')
            ->where('text_uuid', $text->text_uuid)
            ->delete();
    }

    public function permanent_delete_text($text_uuid, $sess_id) {
        return $this->db->table('tbl_texts_uploaded_deleted')
            ->where('text_uuid', $text_uuid)
            ->where('text_session_id', $sess_id)
            ->delete();
    }

    public function empty_trash_texts($sess_id) {
        return $this->db->table('tbl_texts_uploaded_deleted')
            ->where('text_session_id', $sess_id)
            ->delete();
    }
}
