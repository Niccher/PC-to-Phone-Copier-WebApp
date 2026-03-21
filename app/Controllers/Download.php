<?php

namespace App\Controllers;

use App\Models\ModDevice;
use App\Models\ModUpload;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;

class Download extends BaseController
{
	use ResponseTrait;

	public function file_uploaded_by_phone_session_download()
	{
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');

		$phone_file_id = $this->request->getVar('var_file_id');
		$phone_dev_id = $this->request->getVar('var_dev_id');
		$phone_sess_id = $this->request->getVar('var_sess_id');

		$uploaded_files_by_session_and_devid = $mod_upload->file_uploaded_by_phone_session_download($phone_file_id, $phone_sess_id, $phone_dev_id);

		if (!empty($uploaded_files_by_session_and_devid)) {
			$filePath = WRITEPATH . '/uploads/copied_files/' . $uploaded_files_by_session_and_devid->up_file_Name;
			return $this->response->download($filePath, null);
		}
		else {
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

	public function file_action_delete()
	{
		$mod_upload = new ModUpload();

		$dated = date('Y-m-d H:i:s');

		$phone_file_uuid = $this->request->getVar('var_file_uuid');
		$phone_file_name = $this->request->getVar('var_file_name');
		//$phone_dev_id = $this->request->getVar('var_dev_id');
		//$phone_sess_id = $this->request->getVar('var_sess_id');

		$f_path_old = WRITEPATH . '/uploads/copied_files/';
		$f_path_new = WRITEPATH . '/uploads/copied_files_deleted/';

		try {
			rename($f_path_old . $phone_file_name, $f_path_new . $phone_file_name);
			$mod_upload->file_to_delete($phone_file_uuid, $phone_file_name);

			return $this->respond([
				'status' => "1",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}
		catch (\Exception $ex) {
			return $this->respond([
				'status' => "2",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}
	}

	public function browser_file_download($file_uuid)
	{
		$mod_upload = new ModUpload();
		$mod_upload->ensureColumnsExist();
		$dated = date('Y-m-d H:i:s');

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);
		if (empty($file_data)) {
			// Check deleted table
			$sess_id = session()->get('sess_id');
			if ($sess_id) {
				$deleted = $mod_upload->get_deleted_file_by_uuid($file_uuid, $sess_id);
				if ($deleted) $file_data = [$deleted];
			}
		}

		if (!empty($file_data)) {
			$file = $file_data[0];
			$filePath = WRITEPATH . '/uploads/copied_files/' . $file->up_file_Orig_Name;
			if (!file_exists($filePath)) {
				$filePath = WRITEPATH . '/uploads/copied_files_deleted/' . $file->up_file_Orig_Name;
			}

			// Handle Burn After Reading
			if (isset($file->up_file_expiration_policy) && $file->up_file_expiration_policy == 2) {
				register_shutdown_function(function () use ($mod_upload, $file_uuid) {
					$mod_upload->batch_delete_files([$file_uuid]);
				});
			}

			return $this->response->download($filePath, null);
		}
		else {
			return $this->respond([
				'status' => 2,
				'time' => $dated,
				'message' => "Unable to download specified file",
			]);
		}
	}

	public function browser_file_view($file_uuid)
	{
		$mod_upload = new ModUpload();
		$mod_upload->ensureColumnsExist();
		$dated = date('Y-m-d H:i:s');

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);
		if (empty($file_data)) {
			// Check deleted table
			$sess_id = session()->get('sess_id');
			if ($sess_id) {
				$deleted = $mod_upload->get_deleted_file_by_uuid($file_uuid, $sess_id);
				if ($deleted) $file_data = [$deleted];
			}
		}

		if (!empty($file_data)) {
			$file = $file_data[0];
			$filePath = WRITEPATH . '/uploads/copied_files/' . $file->up_file_Orig_Name;
			
			if (!file_exists($filePath)) {
				$filePath = WRITEPATH . '/uploads/copied_files_deleted/' . $file->up_file_Orig_Name;
			}
			if (!file_exists($filePath)) {
				return $this->respond(['status' => 2, 'message' => 'File not found on disk'], 404);
			}

			// Handle Burn After Reading
			if (isset($file->up_file_expiration_policy) && $file->up_file_expiration_policy == 2) {
				register_shutdown_function(function () use ($mod_upload, $file_uuid) {
					$mod_upload->batch_delete_files([$file_uuid]);
				});
			}

			$mimeType = mime_content_type($filePath);
			
			// For text files, we might want to force utf-8
			if (strpos($mimeType, 'text/') === 0) {
				$mimeType .= '; charset=UTF-8';
			}

			return $this->response
				->setHeader('Content-Type', $mimeType)
				->setHeader('Content-Disposition', 'inline; filename="' . $file->up_file_Orig_Name . '"')
				->setBody(file_get_contents($filePath));
		}
		else {
			return $this->respond([
				'status' => 2,
				'time' => $dated,
				'message' => "Unable to find specified file",
			], 404);
		}
	}

	public function browser_file_delete($file_uuid)
	{
		$mod_upload = new ModUpload();
		$mod_upload->ensureColumnsExist();
		$dated = date('Y-m-d H:i:s');

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);
		if (empty($file_data)) {
			return redirect()->to(base_url('home/recent'))->with('error', 'File not found');
		}

		$f_path_old = WRITEPATH . '/uploads/copied_files/';
		$f_path_new = WRITEPATH . '/uploads/copied_files_deleted/';
		$file_name = $file_data[0]->up_file_Orig_Name;
		$file_uuid = $file_data[0]->up_file_uuid;

		try {
			rename($f_path_old . $file_name, $f_path_new . $file_name);
			$mod_upload->file_to_delete($file_uuid, $file_name);
			return redirect()->back()->with('message', "File " . $file_name . " moved to trash");
		}
		catch (\Exception $ex) {
			return redirect()->back()->with('error', "Unable to delete file " . $file_name);
		}
	}
}