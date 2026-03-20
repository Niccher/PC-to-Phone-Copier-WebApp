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
        
        $data = [
            'deleted_files' => $deleted_files,
            'deleted_texts' => $deleted_texts,
            'total_deleted' => count($deleted_files) + count($deleted_texts)
        ];
        
        $sidebarData = $this->getSidebarData('trash');
        $data = array_merge($data, $sidebarData);

        return view('includes/header')
            .view('includes/sidebar', $data)
            .view('home/trash', $data)
            .view('includes/footer_trash', $data);
    }
}
