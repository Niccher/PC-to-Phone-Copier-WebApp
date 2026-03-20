<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;
use App\Models\ModUpload;
use App\Models\ModText;

class TypeTrash extends BaseController
{
    public function index(){
        $mod_upload = new ModUpload();
        $mod_text = new ModText();

        $sess_id = $this->session->get('sess_id');
        // Get deleted files and texts
        $deleted_files = $mod_upload->get_deleted_files($sess_id);
        $deleted_texts = $mod_text->get_deleted_texts($sess_id);
        
        $files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
        $texts_uploaded = $mod_text->text_get_uploaded_texts($sess_id);

        $data['deleted_files'] = $deleted_files;
        $data['deleted_texts'] = $deleted_texts;
        $data['total_deleted'] = count($deleted_files) + count($deleted_texts);
        
        $data['files_count'] = count($files_uploaded);
        $data['texts_count'] = count($texts_uploaded);
        $data['trash_count'] = $data['total_deleted'];
        
        $recent_files = $mod_upload->file_get_uploaded_files($sess_id, 10);
        $recent_texts = $mod_text->text_get_uploaded_texts($sess_id, 10);
        $data['recent_count'] = count($recent_files) + count($recent_texts);

        $data['title'] = "trash";
        return view('includes/header')
            .view('includes/sidebar', $data)
            .view('home/trash', $data)
            .view('includes/footer_trash', $data);
    }
}
