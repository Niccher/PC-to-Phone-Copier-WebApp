<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController{
	use ResponseTrait;

	public function login(){
		return view('includes/header_auth')
			.view('auth/login')
			.view('includes/footer_auth');
	}

	public function user_logout(){
		$session = session();
		$session->destroy();
		return redirect()->to('/auth/login');
	}
}
