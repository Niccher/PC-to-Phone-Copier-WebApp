<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

	public function home(){
		$title['title'] = "recent";
		return view('includes/header')
			.view('includes/sidebar', $title)
			.view('home/home')
			.view('includes/footer');
	}
}
