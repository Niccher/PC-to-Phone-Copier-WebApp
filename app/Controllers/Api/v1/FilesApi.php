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

        $auth_code_id = $this->request->getVar('var_auth_code_id') ?? $this->request->getGet('auth_code_id') ?? $this->request->getVar('varAuthCodeId');
        $session_id = $this->request->getGet('session_id') ?? $this->request->getVar('varSessId');

        if (empty($session_id) && !empty($auth_code_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (empty($session_id)) {
            $session_id = 'general';
        }

        $files = $mod_upload->file_get_uploaded_files($session_id);

        return $this->respondSuccess([
            'files' => $files,
            'count' => count($files)
        ], 'Files retrieved successfully');
    }

    public function uploaded()
    {
        $mod_visitors = new ModVisitors();
        $mod_upload   = new ModUpload();
        $mod_text     = new \App\Models\ModText();

        $auth_code_id = $this->request->getVar('var_auth_code_id') ?? $this->request->getGet('auth_code_id') ?? $this->request->getVar('varAuthCodeId');
        $session_id   = $this->request->getGet('session_id') ?? $this->request->getVar('varSessId');

        if (empty($session_id) && !empty($auth_code_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (empty($session_id)) {
            $session_id = 'general';
        }

        $rawFiles = $mod_upload->file_get_uploaded_files($session_id);
        $items = [];

        foreach ($rawFiles as $file) {
            $item = (array)$file;
            $item['is_text'] = 0;
            $items[] = $item;
        }

        $rawTexts = $mod_text->text_get_uploaded_texts($session_id);
        foreach ($rawTexts as $txt) {
            $src = $txt->text_source ?? 'Mobile Text';
            $ext = 'TEXT';
            $mime = 'text/plain';
            if (stripos($src, 'ocr') !== false || stripos($src, 'image') !== false) {
                $ext = 'OCR';
                $mime = 'text/ocr';
            } elseif (stripos($src, 'qr') !== false || stripos($src, 'scan') !== false) {
                $ext = 'QR';
                $mime = 'text/qr';
            }

            $cleanText = trim(preg_replace('/\s+/', ' ', $txt->text_content ?? ''));
            $dispName  = mb_strlen($cleanText) > 45 ? mb_substr($cleanText, 0, 45) . '...' : ($cleanText ?: 'Text Item');

            $items[] = [
                'status'             => 1,
                'is_text'            => 1,
                'up_file_uuid'       => $txt->text_uuid,
                'up_file_session_id' => $txt->text_session_id,
                'up_file_dev_id'     => $txt->text_dev_id,
                'up_file_Name'       => $dispName,
                'up_file_Orig_Name'  => $dispName,
                'up_file_Sys_Name'   => $txt->text_uuid,
                'up_file_Type'       => $mime,
                'up_file_Extension'  => $ext,
                'up_file_Size'       => (string)strlen($txt->text_content ?? ''),
                'up_file_Created_at' => $txt->text_created_at,
                'text_content'       => $txt->text_content,
                'text_source'        => $src
            ];
        }

        usort($items, function ($a, $b) {
            $dateA = $a['up_file_Created_at'] ?? '';
            $dateB = $b['up_file_Created_at'] ?? '';
            return strcmp($dateB, $dateA);
        });

        return $this->respondSuccess([
            'items' => $items,
            'count' => count($items)
        ], 'Uploaded items retrieved successfully');
    }

    public function download($id = null)
    {
        $mod_upload = new ModUpload();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();
        $file_uuid = $id ?: ($json['var_file_id'] ?? $json['file_uuid'] ?? $this->request->getVar('file_uuid'));

        if (empty($file_uuid)) {
            return $this->respondError('File ID required for download', 400);
        }

        $found = $mod_upload->file_get_uploaded_files_by_uuid($file_uuid);
        if (empty($found)) {
            return $this->respondError('File not found', 404);
        }

        $fileName = $found[0]->up_file_Sys_Name ?? $found[0]->up_file_Name;
        $filePath = WRITEPATH . 'uploads/copied_files/' . $fileName;

        if (!file_exists($filePath)) {
            $filePath = FCPATH . 'assets/media/uploads/' . $fileName;
        }

        if (!file_exists($filePath)) {
            return $this->respondError('File asset missing on server disk', 404);
        }

        return $this->response->download($filePath, null)->setFileName($found[0]->up_file_Orig_Name ?? $fileName);
    }

    public function upload()
    {
        $mod_upload = new ModUpload();
        $mod_visitors = new ModVisitors();
        $mod_upload->ensureColumnsExist();

        $file = $this->request->getFile('uploaded_file')
            ?: $this->request->getFile('file')
            ?: $this->request->getFile('varFile');

        $session_id = $this->request->getVar('varSessId') ?: $this->request->getVar('var_auth_code_id') ?: $this->request->getVar('session_id');
        $dev_id = $this->request->getVar('varDevId') ?: $this->request->getVar('var_dev_uuid') ?: $this->request->getHeaderLine('X-Device-UUID');

        if (!empty($session_id) && is_numeric($session_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($session_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (!$file || !$file->isValid()) {
            return $this->respondError('No valid file provided for upload', 400);
        }

        $original_name = $file->getClientName();
        $extension = strtolower($file->getClientExtension());
        $uuid = random_string('alnum', 16);
        $diskName = $uuid . '.' . $extension;
        $file_type = $file->getClientMimeType();
        $file_size = $file->getSize();

        $dir = WRITEPATH . 'uploads/copied_files/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($file->move($dir, $diskName)) {
            $data = [
                'up_file_uuid'       => $uuid,
                'up_file_session_id' => $session_id ?: 'general',
                'up_file_dev_id'     => $dev_id,
                'up_file_Name'       => $diskName,
                'up_file_Orig_Name'  => $original_name,
                'up_file_Sys_Name'   => $diskName,
                'up_file_Extension'  => $extension,
                'up_file_Orig_Extension' => $extension,
                'up_file_Type'       => $file_type,
                'up_file_Size'       => $file_size,
                'up_file_Source'     => 'Android Upload',
                'up_file_Created_at' => date('Y-m-d H:i:s'),
            ];

            $mod_upload->file_register_uploaded($data);

            return $this->respond([
                'success' => true,
                'code'    => 201,
                'status'  => 1,
                'message' => 'File uploaded successfully',
                'data'    => [
                    'status'    => 1,
                    'file_uuid' => $uuid,
                    'file_name' => $original_name,
                    'file_size' => $file_size
                ]
            ], 201);
        }

        return $this->respondError('Failed to move uploaded file on server', 500);
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

    public function batchDelete()
    {
        $mod_upload = new ModUpload();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $file_uuids = $json['var_file_ids'] ?? $json['file_uuids'] ?? [];

        if (is_string($file_uuids)) {
            $file_uuids = array_filter(explode(',', $file_uuids));
        }

        if (empty($file_uuids) || !is_array($file_uuids)) {
            return $this->respondError('Array of file UUIDs required for batch deletion', 400);
        }

        $deleted_count = 0;
        foreach ($file_uuids as $uuid) {
            $uuid = trim($uuid);
            if (!empty($uuid)) {
                $deleted = $mod_upload->file_delete_uploaded_files(['up_file_uuid' => $uuid]);
                if ($deleted) $deleted_count++;
            }
        }

        return $this->respondSuccess([
            'deleted_count'   => $deleted_count,
            'total_requested' => count($file_uuids)
        ], 'Batch deletion completed');
    }
}
