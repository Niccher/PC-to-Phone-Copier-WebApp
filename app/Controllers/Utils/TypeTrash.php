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

        $sess_id = session()->get('sess_id');

        // Get deleted files and texts
        $deleted_files = $mod_upload->get_deleted_files($sess_id);
        $deleted_texts = $mod_text->get_deleted_texts($sess_id);

        $data['deleted_files'] = $deleted_files;
        $data['deleted_texts'] = $deleted_texts;
        $data['total_deleted'] = count($deleted_files) + count($deleted_texts);

        $title['title'] = "trash";
        return view('includes/header')
            .view('includes/sidebar', $title)
            .view('home/trash', $data)
            .view('includes/footer_trash');
    }
}
