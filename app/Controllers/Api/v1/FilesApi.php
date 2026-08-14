<?php

namespace App\Controllers\Api\v1;

use App\Models\ModUpload;
use App\Models\ModVisitors;

class FilesApi extends ApiController
{
    public function list()
    {
        $mod_visitors = new ModVisitors();
        $mod_upload = new ModUpload();

        $auth_code_id = $this->request->getGet('auth_code_id') ?? $this->request->getVar('varAuthCodeId');
        $session_id = $this->request->getGet('session_id') ?? $this->request->getVar('varSessId');

        if (empty($session_id) && !empty($auth_code_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (empty($session_id)) {
            return $this->respondError('Session identifier required', 400);
        }

        $files = $mod_upload->file_get_uploaded_files($session_id);

        return $this->respondSuccess([
            'files' => $files,
            'count' => count($files)
        ], 'Files retrieved successfully');
    }

    public function upload()
    {
        $mod_upload = new ModUpload();
        $mod_upload->ensureColumnsExist();

        $file = $this->request->getFile('file') ?: $this->request->getFile('varFile');
        $session_id = $this->request->getVar('varSessId') ?: $this->request->getVar('session_id');
        $dev_id = $this->request->getVar('varDevId') ?: $this->request->getHeaderLine('X-Device-UUID');

        if (!$file || !$file->isValid()) {
            return $this->respondError('No valid file provided for upload', 400);
        }

        $original_name = $file->getName();
        $file_type = $file->getClientMimeType();
        $file_size = $file->getSize();
        $sys_name = $file->getRandomName();

        $dir = FCPATH . 'assets/media/uploads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($file->move($dir, $sys_name)) {
            $data = [
                'up_file_uuid'       => random_string('alnum', 16),
                'up_file_session_id' => $session_id ?: 'general',
                'up_file_Orig_Name'  => $original_name,
                'up_file_Sys_Name'   => $sys_name,
                'up_file_Type'       => $file_type,
                'up_file_Size'       => $file_size,
                'up_file_Created_at' => date('Y-m-d H:i:s'),
            ];

            $mod_upload->file_make_uploaded_files($data);

            return $this->respondSuccess($data, 'File uploaded successfully', 201);
        }

        return $this->respondError('Failed to move uploaded file', 500);
    }

    public function delete($id = null)
    {
        $mod_upload = new ModUpload();
        $json = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $file_uuid = $id ?: ($json['file_uuid'] ?? $this->request->getVar('file_uuid'));

        if (empty($file_uuid)) {
            return $this->respondError('File UUID is required for deletion', 400);
        }

        $deleted = $mod_upload->file_delete_uploaded_files(['up_file_uuid' => $file_uuid]);

        if ($deleted) {
            return $this->respondSuccess(['file_uuid' => $file_uuid], 'File deleted successfully');
        }

        return $this->respondError('File not found or already deleted', 404);
    }
}
