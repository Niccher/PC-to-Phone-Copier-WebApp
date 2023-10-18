<?php

namespace App\Controllers;

use App\Models\ModUpload;
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
		$mod_upload = new ModUpload();

		$sess_id = $this->session->get('sess_id');

		$files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
		$data['file_list'] = "";
		$data['file_list_all'] = "";
		$count = 0;

		foreach ($files_uploaded as $file){
			$count++;
			if ($count < 4){
				$data['file_list'] .= '
					<div class="col-xxl-4 col-lg-6">
						<div class="card m-1 shadow-none border">
							<div class="p-2">
								<div class="row align-items-center">
									<div class="col-auto">
										<div class="avatar-sm">
											<span class="avatar-title bg-light text-secondary rounded">
												<i class="mdi mdi-folder font-16"></i>
											</span>
										</div>
									</div>
									<div class="col ps-0">
										<a href="javascript:void(0);" class="text-muted fw-bold"> '.$file->up_file_Name.' </a>
										<p class="mb-0 font-13">'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</p>
									</div>
								</div>
								<!-- end row -->
							</div>
							<!-- end .p-2-->
						</div>
						<!-- end col -->
					</div>
				';
			}

			$data['file_list_all'] .= '
				<tr>
					<td>
					    <span class="fw-semibold">
					        <a href="javascript: void(0);" class="text-reset">'.$file->up_file_Name.'</a>
					    </span>
					    <br>
					    <span class="font-12">'.$file->up_file_Created_at.'</span>
					</td>
					<td>'.str_replace("Upload","",$file->up_file_Source).'</td>
					<td>'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</td>
					<td class="">
					    <a href="'.base_url("saved/download/".$file->up_file_uuid).'"><i class="mdi mdi-download widget-icon"></i></a>
					    <a href="'.base_url("saved/delete/".$file->up_file_uuid).'"><i class="mdi mdi-trash-can widget-icon"></i></a>
					</td>
					</tr>
			';
		}

		$title['title'] = "recent";
		//echo $this->session->get('sess_id');

		return view('includes/header')
			.view('includes/sidebar', $title)
			.view('home/home', $data)
			.view('includes/footer_home');
	}

	public function home_ajax_code_check(){
		$mod_visitors = new ModVisitors();
		$auth_codes = explode("---", base64_decode($this->request->getVar('a_num_code')));

		$code_data = [
			'auth_text_code'    => $auth_codes[0],
			'auth_qr_code'      => $auth_codes[1]
		];

		$get_auth_id = ($mod_visitors->auth_codes_get_uuid($code_data))[0]->auth_id;
		$get_auth_phone_uuid = ($mod_visitors->auth_codes_get_phone_by_auth_code_id($get_auth_id))[0]->checked_by;

		header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Origin: *');

		if (!empty($get_auth_id)){
			$code_data_is_valid = [
				'checked_auth_code_id'=> $get_auth_id,
				//'checked_phone_uuid'=> $get_auth_id,
				'checked_is_valid'=> "valid",
			];
			$is_code_validated = count($mod_visitors->auth_codes_has_tested_valid($code_data_is_valid));
			if ($is_code_validated > 0){
				$sess_cookie= array(
					'name'   => 'sess_id',
					'value_sess_id'  => $get_auth_id,
					'value_phone_uuid'  => $get_auth_phone_uuid,
					'expire' => '144000',
				);
				$_SESSION["sess_id"] = $get_auth_id;
				$_SESSION["phone_id"] = $get_auth_phone_uuid;
				set_cookie($sess_cookie);
			}
			echo ($is_code_validated > 0) ? "valid" : "invalid";
		}
	}

	public function home_ajax_get_file_recent_uploaded(){
		$mod_upload = new ModUpload();

		$sess_id = $this->session->get('sess_id');

		$files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
		$data['file_list'] = "";
		$count = 0;

		foreach ($files_uploaded as $file){
			$count++;
			if ($count < 4){
				$data['file_list'] .= '
					<div class="col-xxl-4 col-lg-6">
						<div class="card m-1 shadow-none border">
							<div class="p-2">
								<div class="row align-items-center">
									<div class="col-auto">
										<div class="avatar-sm">
											<span class="avatar-title bg-light text-secondary rounded">
												<i class="mdi mdi-folder font-16"></i>
											</span>
										</div>
									</div>
									<div class="col ps-0">
										<a href="javascript:void(0);" class="text-muted fw-bold"> '.$file->up_file_Name.' </a>
										<p class="mb-0 font-13">'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</p>
									</div>
								</div>
								<!-- end row -->
							</div>
							<!-- end .p-2-->
						</div>
						<!-- end col -->
					</div>
				';
			}
		}
		echo $data['file_list'];
	}

	public function home_ajax_get_file_all_uploaded(){
		$mod_upload = new ModUpload();

		$sess_id = $this->session->get('sess_id');

		$files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
		$data['file_list_all'] = "";

		foreach ($files_uploaded as $file){
			$data['file_list_all'] .= '
				<tr>
					<td>
					    <span class="fw-semibold">
					        <a href="javascript: void(0);" class="text-reset">'.$file->up_file_Name.'</a>
					    </span>
					    <br>
					    <span class="font-12">'.$file->up_file_Created_at.'</span>
					</td>
					<td>'.str_replace("Upload","",$file->up_file_Source).'</td>
					<td>'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</td>
					<td class="">
					    <a href="'.base_url("saved/download/".$file->up_file_uuid).'"><i class="mdi mdi-download widget-icon"></i></a>
					    <a href="'.base_url("saved/delete/".$file->up_file_uuid).'"><i class="mdi mdi-trash-can widget-icon"></i></a>
					</td>
				</tr>
			';
		}
		echo $data['file_list_all'];
		//print("<pre>".print_r($files_upload,true)."</pre>");
	}
}
