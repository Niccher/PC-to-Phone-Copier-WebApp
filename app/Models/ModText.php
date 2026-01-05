<?php

namespace App\Models;

use CodeIgniter\Model;

class ModText extends Model
{
    public function text_register_uploaded($text_info){
        return $this->db->table('tbl_texts_uploaded')->insert($text_info);
    }

    public function text_get_uploaded_texts($sess_id){
        $builder = $this->db->table('tbl_texts_uploaded');
        $get_all = $builder
            ->orderBy('text_count', 'DESC')
            ->where('text_session_id', $sess_id)
            ->get();
        return $get_all->getResult();
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
}
