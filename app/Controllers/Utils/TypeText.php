<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;

class TypeText extends BaseController
{
    public function index(){
	    return view('includes/header')
		    .view('includes/sidebar')
		    .view('home/textual')
		    .view('includes/footer_texts');
    }
}
