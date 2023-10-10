<?php

namespace App\Controllers;

use App\Models\ModDevice;
use App\Models\ModUpload;
use CodeIgniter\API\ResponseTrait;

class Upload extends BaseController
{
	use ResponseTrait;

	public function file_uploaded_by_browser(){
		$mod_upload = new ModUpload();
		//$mod_device = new ModDevice();
		$dated = date('Y-m-d H:i:s');
		$uuid = random_string('alnum', 16);

		if ($this->request->getFile('file')){
			$uploaded_File = $this->request->getFile('file');
			$uploaded_File->move(WRITEPATH . 'uploads/copied_files');
			$uploaded_file_info = [
				'up_file_uuid' =>  $uuid,
				'up_file_session_id' =>  $this->session->get('sess_id'),
				//'up_file_dev_id' =>  $file_dev_id,
				'up_file_Name' =>  $uploaded_File->getName(),
				'up_file_Orig_Name' =>  $uploaded_File->getClientName(),
				'up_file_Type'  => $uploaded_File->getClientMimeType(),
				'up_file_Extension'  => $uploaded_File->getClientExtension(),//guessExtension(),
				'up_file_Orig_Extension'  => $uploaded_File->getClientExtension(),
				'up_file_Size'  => $uploaded_File->getSize(),
				'up_file_Source'  => "Browser Upload",
				'up_file_Created_at'  => $dated,
			];

			$pushed = $mod_upload->file_register_uploaded($uploaded_file_info);

			if ($pushed){
				return $this->respond([
					'status' => 1,
					'time' => $dated,
					'message' => "File Uploaded Successfully"
				]);
			}else{
				return $this->respond([
					'status' => 0,
					'time' => $dated,
					'message' => "File Uploaded has encountered an error"
				]);
			}
		}else{
			return $this->respond([
				'dev_uuid' => "null",
				'dev_status' => "failed",
				'dev_message' => "unknown error",
				'dev_time' => $dated,
			]);
		}
	}

	public function file_uploaded_by_phone(){
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');
		$uuid = random_string('alnum', 16);

		if ($this->request->getPost()){
			$file_dev_id = $this->request->getVar('varDevId');
			$file_sess_id = $this->request->getVar('varSessId');
			$uploaded_File = $this->request->getFile('uploaded_file');
			$uploaded_File->move(WRITEPATH . 'uploads/copied_files');

			$data = [
				'up_file_uuid' =>  $uuid,
				'up_file_session_id' =>  $file_sess_id,
				'up_file_dev_id' =>  $file_dev_id,
				'up_file_Name' =>  $uploaded_File->getName(),
				'up_file_Orig_Name' =>  $uploaded_File->getClientName(),
				'up_file_Type'  => $uploaded_File->getClientMimeType(),
				'up_file_Extension'  => $uploaded_File->getClientExtension(),
				'up_file_Orig_Extension'  => $uploaded_File->getClientExtension(),
				'up_file_Size'  => $uploaded_File->getSize(),
				'up_file_Source'  => "Android Upload",
				'up_file_Created_at'  => $dated,
			];

			$pushed = $mod_upload->file_register_uploaded($data);

			if ($pushed){
				return $this->respond([
					'status' => 1,
					'time' => $dated,
					'message' => "File Uploaded Successfully"
				]);
			}else{
				return $this->respond([
					'status' => 0,
					'time' => $dated,
					'message' => "File Uploaded has encountered an error"
				]);
			}

		}else{
			return $this->respond([
				'status' => 2,
				'time' => $dated,
				'message' => "Unexpected request sent"
			]);
		}
	}

	public function file_uploaded_by_phone_session(){
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');

		$phone_dev_id = $this->request->getVar('var_dev_uuid');
		$phone_sess_id = $this->request->getVar('var_auth_code_id');

		$uploaded_files_by_session_and_devid = $mod_upload->file_get_uploaded_files_by_session_and_devid($phone_sess_id, $phone_dev_id);

		if (!empty($uploaded_files_by_session_and_devid)){
			return $this->respond([
				'status' => 1,
				'time' => $dated,
				'file_info' => $uploaded_files_by_session_and_devid,
			]);
		}else{
			return $this->respond([
				'status' => 2,
				'time' => $dated,
			]);
		}
	}
}
