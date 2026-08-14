<?php

namespace App\Models;

use CodeIgniter\Model;

class ModText extends Model
{
    public function text_save($text_info) {
        $data = [
            'uuid'       => $text_info['text_uuid'] ?? $text_info['uuid'] ?? null,
            'session_id' => $text_info['text_session_id'] ?? $text_info['session_id'] ?? null,
            'device_uuid'=> $text_info['text_dev_id'] ?? $text_info['device_uuid'] ?? null,
            'title'      => $text_info['text_title'] ?? $text_info['title'] ?? null,
            'content'    => $text_info['text_content'] ?? $text_info['content'] ?? null,
            'source'     => $text_info['text_source'] ?? $text_info['source'] ?? 'Browser Text',
            'created_at' => $text_info['text_created_at'] ?? $text_info['created_at'] ?? date('Y-m-d H:i:s'),
        ];
        return $this->db->table('tbl_texts')->insert($data);
    }

    public function text_register_uploaded($text_info){
        return $this->text_save($text_info);
    }

    public function text_get_uploaded_texts($sess_id, $limit = null){
        $builder = $this->db->table('tbl_texts');
        $builder->select('*, id as text_id, uuid as text_uuid, session_id as text_session_id, device_uuid as text_dev_id, title as text_title, content as text_content, source as text_source, created_at as text_created_at, copy_count as text_count')
            ->orderBy('created_at', 'DESC');

        if ($sess_id !== 'general') {
            $builder->groupStart()
                ->where('session_id', $sess_id)
                ->orWhere('session_id', 'general')
                ->groupEnd();
        } else {
            $builder->where('session_id', 'general');
        }

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->get()->getResult();
    }

    public function text_get_uploaded_by_uuid($text_uuid){
        $builder = $this->db->table('tbl_texts');
        $get_all = $builder
            ->select('*, id as text_id, uuid as text_uuid, session_id as text_session_id, device_uuid as text_dev_id, title as text_title, content as text_content, source as text_source, created_at as text_created_at, copy_count as text_count')
            ->orderBy('copy_count', 'DESC')
            ->where('uuid', $text_uuid)
            ->get();
        return $get_all->getResult();
    }

    public function text_to_delete($text_uuid){
        $builder = $this->db->table('tbl_texts');
        $get_info = $builder
            ->where('uuid', $text_uuid)
            ->get();

        $rows = $get_info->getResultArray();
        if (empty($rows)) return false;

        $text_info = $rows[0];
        $text_info['deleted_at'] = date('Y-m-d H:i:s');

        $builder
            ->where('uuid', $text_uuid)
            ->delete();

        return $this->db->table('tbl_texts_trash')->insert($text_info);
    }

    public function get_deleted_texts($sess_id) {
        return $this->db->table('tbl_texts_trash')
            ->select('*, id as text_id, uuid as text_uuid, session_id as text_session_id, device_uuid as text_dev_id, title as text_title, content as text_content, source as text_source, created_at as text_created_at, copy_count as text_count')
            ->where('session_id', $sess_id)
            ->orderBy('deleted_at', 'DESC')
            ->get()
            ->getResult();
    }

    public function get_deleted_text_by_uuid($text_uuid, $sess_id) {
        return $this->db->table('tbl_texts_trash')
            ->select('*, id as text_id, uuid as text_uuid, session_id as text_session_id, device_uuid as text_dev_id, title as text_title, content as text_content, source as text_source, created_at as text_created_at, copy_count as text_count')
            ->where('uuid', $text_uuid)
            ->where('session_id', $sess_id)
            ->get()
            ->getRow();
    }

    public function restore_text($text) {
        $arr = (array)$text;
        unset($arr['deleted_at']);
        $this->db->table('tbl_texts')->insert($arr);
        $uuid = $text->uuid ?? $text->text_uuid;
        return $this->db->table('tbl_texts_trash')
            ->where('uuid', $uuid)
            ->delete();
    }

    public function permanent_delete_text($text_uuid, $sess_id) {
        return $this->db->table('tbl_texts_trash')
            ->where('uuid', $text_uuid)
            ->where('session_id', $sess_id)
            ->delete();
    }

    public function empty_trash_texts($sess_id) {
        return $this->db->table('tbl_texts_trash')
            ->where('session_id', $sess_id)
            ->delete();
    }
}
