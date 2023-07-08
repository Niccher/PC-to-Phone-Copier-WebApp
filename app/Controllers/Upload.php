<?php

namespace App\Controllers;

use App\Models\ModDevice;
use CodeIgniter\API\ResponseTrait;

class Upload extends BaseController
{
	use ResponseTrait;

	public function file_uploaded(){
		//$mod_device = new ModDevice();
		$dated = date('Y-m-d H:i:s');
		$uuid = random_string('alnum', 16);

		if ($this->request->getFile('file')){
			$uploaded_File = $this->request->getFile('file');
			$uploaded_File->move(WRITEPATH . 'uploads/copied_files');
			$uploaded_file_info = [
				'up_file_uuid' =>  $uuid,
				'up_file_Name' =>  $uploaded_File->getName(),
				'up_file_Orig_Name' =>  $uploaded_File->getClientName(),
				'up_file_Type'  => $uploaded_File->getClientMimeType(),
				'up_file_Extension'  => $uploaded_File->getClientExtension(),//guessExtension(),
				'up_file_Orig_Extension'  => $uploaded_File->getClientExtension(),
				'up_file_Size'  => $uploaded_File->getSize(),
				'up_file_Created_at'  => $dated,
			];
			return $this->respond([
				$uploaded_file_info
			]);
		}else{
			return $this->respond([
				'dev_uuid' => "null",
				'dev_status' => "failed",
				'dev_message' => "unknown error",
				'dev_time' => $dated,
			]);
		}
	}
}
