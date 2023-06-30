<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;

class TypeText extends BaseController
{
    public function index(){
	    $title['title'] = "text";
	    return view('includes/header')
		    .view('includes/sidebar', $title)
		    .view('home/textual')
		    .view('includes/footer_texts');
    }
}
