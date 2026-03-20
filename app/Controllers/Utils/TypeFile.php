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
        
        $data = [
            'files' => $files_uploaded
        ];
        $sidebarData = $this->getSidebarData('files');
        $data = array_merge($data, $sidebarData);

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
