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
}
