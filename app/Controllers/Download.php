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

	public function browser_file_download($file_uuid){
		$mod_upload = new ModUpload();
		$mod_upload->ensureColumnsExist();
		$dated = date('Y-m-d H:i:s');

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);

		if (!empty($file_data)){
			$filePath = WRITEPATH .'/uploads/copied_files/'.$file_data[0]->up_file_Orig_Name;
			return $this->response->download($filePath, null);
		}else{
			return $this->respond([
				'status' => 2,
				'time' => $dated,
				'message' => "Unable to download specified file",
			]);
		}
	}

	public function browser_file_delete($file_uuid){
		$mod_upload = new ModUpload();
		$mod_upload->ensureColumnsExist();
		$dated = date('Y-m-d H:i:s');

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);
		if (empty($file_data)) {
			return redirect()->to(base_url('home/recent'))->with('error', 'File not found');
		}

		$f_path_old = WRITEPATH .'/uploads/copied_files/';
		$f_path_new = WRITEPATH .'/uploads/copied_files_deleted/';
		$file_name = $file_data[0]->up_file_Orig_Name;
		$file_uuid = $file_data[0]->up_file_uuid;

        try {
            rename($f_path_old.$file_name, $f_path_new.$file_name);
            $mod_upload->file_to_delete($file_uuid,$file_name);
            return redirect()->back()->with('message', "File ".$file_name." moved to trash");
        }catch (\Exception $ex){
            return redirect()->back()->with('error', "Unable to delete file ".$file_name);
        }
    }
}
