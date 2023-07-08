<?php

namespace App\Controllers;

use App\Models\ModVisitors;
use CodeIgniter\API\ResponseTrait;

use function PHPUnit\Framework\isEmpty;

class Home extends BaseController
{
	use ResponseTrait;
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

	public function home_ajax_code_check(){
		$mod_visitors = new ModVisitors();
		$auth_codes = explode("---", base64_decode($this->request->getVar('a_num_code')));

		$code_data = [
			'auth_text_code'    => $auth_codes[0],
			'auth_qr_code'      => $auth_codes[1]
		];

		$get_auth_id = ($mod_visitors->auth_codes_get_uuid($code_data))[0]->auth_id;
		
		if (!empty($get_auth_id)){
			$code_data_is_valid = [
				'checked_auth_code_id'=> $get_auth_id,
				'checked_is_valid'=> "valid",
			];
			$is_code_validated = count($mod_visitors->auth_codes_has_tested_valid($code_data_is_valid));
			echo ($is_code_validated > 0) ? "valid" : "invalid";
		}
	}
}
