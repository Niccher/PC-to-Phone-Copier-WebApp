<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;

class TypeFile extends BaseController
{
    public function index(){
	    return view('includes/header')
		    .view('includes/sidebar')
		    .view('home/files')
		    .view('includes/footer_files');
    }
}
