<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;
use App\Models\ModUpload;

class TypeFile extends BaseController
{
    public function index(){
        $title['title'] = "files";

        $mod_upload = new ModUpload();
        $mod_upload->ensureColumnsExist();

        $sess_id = $this->session->get('sess_id');
        $files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
        
        $mod_text = new \App\Models\ModText();
        $all_texts = $mod_text->text_get_uploaded_texts($sess_id);
        $deleted_files = $mod_upload->get_deleted_files($sess_id);
        $deleted_texts = $mod_text->get_deleted_texts($sess_id);

        $data['files'] = $files_uploaded;
        $data['files_count'] = count($files_uploaded);
        $data['texts_count'] = count($all_texts);
        $data['trash_count'] = count($deleted_files) + count($deleted_texts);
        
        $recent_files = $mod_upload->file_get_uploaded_files($sess_id, 10);
        $recent_texts = $mod_text->text_get_uploaded_texts($sess_id, 10);
        $data['recent_count'] = count($recent_files) + count($recent_texts);
        $data['title'] = "files";

        return view('includes/header')
            .view('includes/sidebar', $data)
            .view('home/files', $data)
            .view('includes/footer_files', $data);
    }

    private function getFileIcon($extension) {
        $mod_upload = new ModUpload();
        return $mod_upload->getFileIconClass($extension);
    }
}
