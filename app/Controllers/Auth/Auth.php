<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\QR_Generator;

class Auth extends BaseController{
	use ResponseTrait;

	public function landing(){
		//QR_Scan
		$this->qr_scan = new QR_Generator();

		$data_alphanum =  random_string('alnum', 16);
		$data_num =  random_string('numeric', 8);

		$hex_data   = bin2hex($data_alphanum);
		$save_name  = $hex_data . '.png';

		/* QR Code File Directory Initialize */
		$dir = 'assets/media/';
		if (! file_exists($dir)) {
			mkdir($dir, 0775, true);
		}

		/* QR Configuration  */
		$config['cacheable']    = true;
		$config['imagedir']     = $dir;
		$config['quality']      = true;
		$config['size']         = '1024';
		$config['black']        = [255, 255, 255];
		$config['white']        = [255, 255, 255];
		$this->qr_scan->initialize($config);

		/* QR Data  */
		$params['data']     = $data_alphanum;
		$params['level']    = 'M'; // L M Q H
		$params['size']     = 10; // 1 2 3 4 5 6 7 8 9 10
		$params['savename'] = FCPATH . $config['imagedir'] . $save_name;
		$params['filepath'] =  $config['imagedir'] . $save_name;
		$params['data_num'] =  $data_num;
		$this->qr_scan->generate($params);

		return view('auth/landing', $params);
	}

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
