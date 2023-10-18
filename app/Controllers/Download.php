<?php

namespace App\Controllers;

use App\Models\ModDevice;
use App\Models\ModUpload;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;

class Download extends BaseController
{
	use ResponseTrait;

	public function file_uploaded_by_phone_session_download(){
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');

		$phone_file_id = $this->request->getVar('var_file_id');
		$phone_dev_id = $this->request->getVar('var_dev_id');
		$phone_sess_id = $this->request->getVar('var_sess_id');

		$uploaded_files_by_session_and_devid = $mod_upload->file_uploaded_by_phone_session_download($phone_file_id,$phone_sess_id, $phone_dev_id);

		if (!empty($uploaded_files_by_session_and_devid)){
			$filePath = WRITEPATH .'/uploads/copied_files/'.$uploaded_files_by_session_and_devid->up_file_Name;
			return $this->response->download($filePath, null);
		}else{
			return $this->respond([
				'status' => 2,
				'time' => $dated,
				'phone_file_id' => $phone_file_id,
				'phone_dev_id' => $phone_dev_id,
				'phone_sess_id' => $phone_sess_id,
				'phone_file_status' => "Failed to download",
			]);
		}
	}

	public function file_action_delete(){
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');

		$phone_file_uuid = $this->request->getVar('var_file_uuid');
		$phone_file_name = $this->request->getVar('var_file_name');
		//$phone_dev_id = $this->request->getVar('var_dev_id');
		//$phone_sess_id = $this->request->getVar('var_sess_id');

		$f_path_old = WRITEPATH .'/uploads/copied_files/';
		$f_path_new = WRITEPATH .'/uploads/copied_files_deleted/';

		try {
			rename($f_path_old.$phone_file_name, $f_path_new.$phone_file_name);
			$mod_upload->file_to_delete($phone_file_uuid,$phone_file_name);

			return $this->respond([
				'status' => "1",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}catch (\Exception $ex){
			return $this->respond([
				'status' => "2",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}
	}
}
