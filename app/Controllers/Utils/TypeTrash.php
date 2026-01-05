<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;

class TypeTrash extends BaseController
{
    public function index(){
	    $title['title'] = "trash";
	    return view('includes/header')
		    .view('includes/sidebar', $title)
		    .view('home/textual')
		    .view('includes/footer_texts');
    }
}
