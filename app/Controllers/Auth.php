<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModVisitors;
use App\Models\ModAndroid;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\QR_Generator;

class Auth extends BaseController{
	use ResponseTrait;

	public function landing(){
		//QR_Scan
		$this->qr_scan = new QR_Generator();

		$data_alphanum =  random_string('alnum', 12);
		$data_num =  random_string('numeric', 6);

		$save_name  = bin2hex($data_alphanum) . '.png';

		/* QR Code File Directory Initialize */
		$dir = FCPATH . 'assets/media/';
		if (! file_exists($dir)) {
			mkdir($dir, 0775, true);
		}

		/* QR Configuration  */
		$config['cacheable']    = true;
		$config['imagedir']     = 'assets/media/';
		$config['quality']      = true;
		$config['size']         = '1024';
		$config['black']        = [0, 0, 0];
		$config['white']        = [255, 255, 255];
		$this->qr_scan->initialize($config);

		/* QR Data  */
		$params['data']     = $data_alphanum;
		$params['level']    = 'M'; // L M Q H
		$params['size']     = 10; // 1 2 3 4 5 6 7 8 9 10
		$params['savename'] = $dir . $save_name;
		$params['filepath'] = $config['imagedir'] . $save_name;
		$params['data_num'] =  $data_num;
		$params['data_codes'] =  base64_encode($data_num . "---". $data_alphanum);
		$this->qr_scan->generate($params);

		$mod_visitors = new ModVisitors();
		$dated = date('Y-m-d H:i:s');

		$agent = $this->request->getUserAgent();

		$visitor_data = [
			'visited_at'       => $dated,
			'client_ip'        => $this->request->getIPAddress(),
			'user_agent'       => (string)$agent,
			'browser'          => ($agent->isBrowser()) ? $agent->getBrowser() . ' ' . $agent->getVersion() : 'Unknown',
			'is_robot'         => ($agent->isRobot()) ? $agent->getRobot() : 'Unknown',
			'is_mobile'        => ($agent->isMobile()) ? $agent->getMobile() : 'Unknown',
			'operating_system' => $agent->getPlatform(),
			'referrer'         => ($agent->isReferral()) ? $agent->referrer() : 'Unknown',
			'http_method'      => $this->request->getMethod(),
		];

		$auth_data = [
			'session_uuid' => bin2hex($data_alphanum),
			'pairing_code' => $data_num,
		];

		$mod_visitors->vistor_register($visitor_data);
		$mod_visitors->auth_codes_register($auth_data);

		return view('auth/landing', $params);
	}

	public function login(){
		return view('includes/header_auth')
			.view('auth/login')
			.view('includes/footer_auth');
	}

	public function register(){
		$dated = date('Y-m-d H:i:s');
		$mod_android = new ModAndroid();

		if ($this->request->getPost()){
			$u_type = $this->request->getVar('var_auth_type');
			$u_code = $this->request->getVar('var_auth_code');
			$u_dev_id = $this->request->getVar('var_dev_uuid');

			$auth_data_tested = [
				'checked_at'        => $dated,
				'checked_type'      => $u_type,
				'checked_auth_code' => $u_code,
				'checked_by'        => $u_dev_id,
			];

			$auth_data_test = ['pairing_code' => $u_code];
			$is_code_present = $mod_android->android_test_auth_codes($auth_data_test);
			if (empty($is_code_present)){
				$auth_data_tested['checked_is_valid']  = "invalid";
				$auth_data_tested['checked_auth_code_id']  = "invalid";
				$mod_android->android_register_test($auth_data_tested);

				return $this->respond([
					'auth_status' => "False",
					'auth_type' => $u_type,
					'auth_auth_code' => $u_code,
					'auth_auth_code_id' => "invalid",
					'auth_message' => "incorrect code",
					'auth_time' => $dated
				]);
			}else{
				$auth_data_tested['checked_is_valid']  = "valid";
				$auth_data_tested['checked_auth_code_id']  = $is_code_present[0]->auth_id;
				$mod_android->android_register_test($auth_data_tested);

				return $this->respond([
					'auth_status' => "True",
					'auth_type' => $u_type,
					'auth_auth_code' => $u_code,
					'auth_auth_code_id' => $is_code_present[0]->auth_id,
					'auth_message' => "correct code",
					'auth_time' => $dated
				]);
			}
		}else{
			return $this->respond([
				'auth_status' => "False",
				'auth_type' => "False",
				'auth_auth_code' => "False",
				'auth_message' => "unknown error",
				'auth_auth_code_id' => "False",
				'auth_time' => $dated
			]);
		}
	}

	public function user_logout(){
		$session = session();
		$session->destroy();
		return redirect()->to('/auth/login');
	}
}
