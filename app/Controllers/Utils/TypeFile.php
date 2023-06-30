<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;

class TypeFile extends BaseController
{
    public function index(){
	    $title['title'] = "file";
	    return view('includes/header')
		    .view('includes/sidebar', $title)
		    .view('home/files')
		    .view('includes/footer_files');
    }
}
