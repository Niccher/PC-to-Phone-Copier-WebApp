<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

	public function home(){
		return view('includes/header')
			.view('includes/sidebar')
			.view('home/home')
			.view('includes/footer');
	}
}
