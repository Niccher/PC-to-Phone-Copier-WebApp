<?php

namespace App\Controllers;

use App\Libraries\StorageService;
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
			$diskName = basename($uploaded_files_by_session_and_devid->up_file_Sys_Name ?? $uploaded_files_by_session_and_devid->up_file_Name);
			$origName = $uploaded_files_by_session_and_devid->up_file_Orig_Name ?? $diskName;

			$storage = StorageService::getInstance();
			$fileData = $storage->downloadStream("active/{$diskName}") ?? $storage->downloadStream($diskName);

			if (!$fileData) {
				return $this->respond([
					'status' => 2,
					'time' => $dated,
					'phone_file_id' => $phone_file_id,
					'phone_dev_id' => $phone_dev_id,
					'phone_sess_id' => $phone_sess_id,
					'phone_file_status' => "File missing in storage",
				]);
			}

			$this->response->setHeader('Content-Type', $fileData['type'] ?: 'application/octet-stream');
			$this->response->setHeader('Content-Disposition', 'attachment; filename="' . basename($origName) . '"');
			if (!empty($fileData['size'])) {
				$this->response->setHeader('Content-Length', (string) $fileData['size']);
			}

			if (is_resource($fileData['stream'])) {
				$this->response->sendHeaders();
				fpassthru($fileData['stream']);
				fclose($fileData['stream']);
				exit;
			}

			return $this->response->download($origName, $fileData['stream']);
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
		$phone_file_name = basename($this->request->getVar('var_file_name'));

		$file_data = $mod_upload->file_get_uploaded_by_file_uuid($phone_file_uuid);
		if (empty($file_data)) {
			return $this->respond([
				'status' => "2",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}

		$stored_name = basename($file_data[0]->up_file_Sys_Name ?? $file_data[0]->up_file_Name);
		if ($stored_name !== $phone_file_name && basename($file_data[0]->up_file_Orig_Name) !== $phone_file_name) {
			return $this->respond([
				'status' => "2",
				'time' => $dated,
				'name' => $phone_file_name,
				'uuid' => $phone_file_uuid,
			]);
		}

		try {
			$storage = StorageService::getInstance();
			$storage->move("active/{$stored_name}", "trash/{$stored_name}");
			$mod_upload->file_to_delete($phone_file_uuid, $stored_name);

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
			$diskName = basename($file->up_file_Sys_Name ?? $file->up_file_Name);
			$origName = $file->up_file_Orig_Name ?? $diskName;

			$storage = StorageService::getInstance();
			$fileData = $storage->downloadStream("active/{$diskName}")
				?? $storage->downloadStream("trash/{$diskName}")
				?? $storage->downloadStream($diskName);

			if (!$fileData) {
				return $this->respond([
					'status' => 2,
					'time' => $dated,
					'message' => "Unable to find file in storage",
				], 404);
			}

			// Handle Burn After Reading
			if (isset($file->up_file_expiration_policy) && $file->up_file_expiration_policy == 2) {
				register_shutdown_function(function () use ($mod_upload, $file_uuid, $storage, $diskName) {
					$mod_upload->file_delete_uploaded_files(['uuid' => $file_uuid]);
					$storage->move("active/{$diskName}", "trash/{$diskName}");
				});
			}

			$this->response->setHeader('Content-Type', $fileData['type'] ?: 'application/octet-stream');
			$this->response->setHeader('Content-Disposition', 'attachment; filename="' . basename($origName) . '"');
			if (!empty($fileData['size'])) {
				$this->response->setHeader('Content-Length', (string) $fileData['size']);
			}

			if (is_resource($fileData['stream'])) {
				$this->response->sendHeaders();
				fpassthru($fileData['stream']);
				fclose($fileData['stream']);
				exit;
			}

			return $this->response->download($origName, $fileData['stream']);
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
			$diskName = basename($file->up_file_Sys_Name ?? $file->up_file_Name);
			$origName = $file->up_file_Orig_Name ?? $diskName;

			$storage = StorageService::getInstance();
			$fileData = $storage->downloadStream("active/{$diskName}")
				?? $storage->downloadStream("trash/{$diskName}")
				?? $storage->downloadStream($diskName);

			if (!$fileData) {
				return $this->respond(['status' => 2, 'message' => 'File not found in storage'], 404);
			}

			// Handle Burn After Reading
			if (isset($file->up_file_expiration_policy) && $file->up_file_expiration_policy == 2) {
				register_shutdown_function(function () use ($mod_upload, $file_uuid, $storage, $diskName) {
					$mod_upload->file_delete_uploaded_files(['uuid' => $file_uuid]);
					$storage->move("active/{$diskName}", "trash/{$diskName}");
				});
			}

			$mimeType = $fileData['type'] ?: 'application/octet-stream';
			if (strpos($mimeType, 'text/') === 0) {
				$mimeType .= '; charset=UTF-8';
			}

			$this->response->setHeader('Content-Type', $mimeType);
			$this->response->setHeader('Content-Disposition', 'inline; filename="' . basename($origName) . '"');
			if (!empty($fileData['size'])) {
				$this->response->setHeader('Content-Length', (string) $fileData['size']);
			}

			if (is_resource($fileData['stream'])) {
				$this->response->sendHeaders();
				fpassthru($fileData['stream']);
				fclose($fileData['stream']);
				exit;
			}

			return $this->response->setBody((string) $fileData['stream']);
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

		$diskName = basename($file_data[0]->up_file_Sys_Name ?? $file_data[0]->up_file_Name);
		$stored_orig_name = $file_data[0]->up_file_Orig_Name ?? $diskName;
		$file_uuid = $file_data[0]->up_file_uuid;

		try {
			$storage = StorageService::getInstance();
			$storage->move("active/{$diskName}", "trash/{$diskName}");
			$mod_upload->file_to_delete($file_uuid, $diskName);
			return redirect()->back()->with('message', "File " . $stored_orig_name . " moved to trash");
		}
		catch (\Exception $ex) {
			return redirect()->back()->with('error', "Unable to delete file " . $stored_orig_name);
		}
	}

	public function download_all_session_zip()
	{
		$mod_upload = new ModUpload();
		$sess_id = session()->get('sess_id');
		if (empty($sess_id)) {
			return redirect()->to(base_url())->with('error', 'Session expired');
		}

		$files = $mod_upload->file_get_uploaded_files($sess_id);
		if (empty($files)) {
			return redirect()->back()->with('error', 'No active files found to download');
		}

		$zipDir = WRITEPATH . 'uploads/';
		if (!is_dir($zipDir)) {
			@mkdir($zipDir, 0777, true);
		}
		$zipFile = $zipDir . 'P2P_Session_' . substr(md5($sess_id . time()), 0, 8) . '.zip';
		$zip = new \ZipArchive();

		if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
			return redirect()->back()->with('error', 'Could not create ZIP archive.');
		}

		$storage = StorageService::getInstance();
		$filesAdded = 0;
		foreach ($files as $file) {
			$diskName = basename($file->up_file_Sys_Name ?? $file->up_file_Name ?? $file->system_name ?? '');
			$origName = basename($file->up_file_Orig_Name ?? $file->original_name ?? $diskName);

			$fileData = $storage->downloadStream("active/{$diskName}") ?? $storage->downloadStream($diskName);
			if ($fileData && is_resource($fileData['stream'])) {
				$content = stream_get_contents($fileData['stream']);
				fclose($fileData['stream']);
				$zip->addFromString($origName, $content);
				$filesAdded++;
			}
		}

		$zip->close();

		if ($filesAdded === 0) {
			if (file_exists($zipFile)) {
				@unlink($zipFile);
			}
			return redirect()->back()->with('error', 'None of the session files could be found in storage');
		}

		$fileContent = file_get_contents($zipFile);
		@unlink($zipFile);
		return $this->response->download('P2P_Files_' . date('Ymd_His') . '.zip', $fileContent);
	}
}