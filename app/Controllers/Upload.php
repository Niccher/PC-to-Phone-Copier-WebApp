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
        $mod_upload->ensureColumnsExist();
        $dated = date('Y-m-d H:i:s');
        $uuid = random_string('alnum', 16);

        if ($this->request->getFile('file')){
            $uploaded_File = $this->request->getFile('file');

            // Validate file
            $validation_result = $this->validateFile($uploaded_File);
            if (!$validation_result['valid']) {
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => $validation_result['message'],
                    'error_type' => $validation_result['error_type']
                ]);
            }

            // Move file to destination
            $upload_path = WRITEPATH . 'uploads/copied_files';
            $full_file_path = $upload_path . '/' . $uploaded_File->getName();

            if (!$uploaded_File->move($upload_path, $uploaded_File->getName())) {
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "Failed to save file to server",
                    'error_type' => 'server_error'
                ]);
            }

            // Generate thumbnail for images
            $thumbnail_path = null;
            $image_dimensions = null;
            $preview_available = 0;

            $extension = strtolower($uploaded_File->getClientExtension());
            $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

            if (in_array($extension, $image_extensions)) {
                $thumbnail_path = $this->generateThumbnail($full_file_path, $uuid, $extension);
                $image_dimensions = $this->getImageDimensions($full_file_path);
                $preview_available = 1;
            }

            // Auto-categorize file
            $category = $this->categorizeFile($extension);

            $uploaded_file_info = [
                'up_file_uuid' =>  $uuid,
                'up_file_session_id' =>  $this->session->get('sess_id'),
                'up_file_dev_id' =>  $this->session->get('phone_id'),
                'up_file_Name' =>  $uploaded_File->getName(),
                'up_file_Orig_Name' =>  $uploaded_File->getClientName(),
                'up_file_Type'  => $uploaded_File->getClientMimeType(),
                'up_file_Extension'  => $uploaded_File->getClientExtension(),
                'up_file_Orig_Extension'  => $uploaded_File->getClientExtension(),
                'up_file_Size'  => $uploaded_File->getSize(),
                'up_file_Source'  => "Browser Upload",
                'up_file_Created_at'  => $dated,
                'up_file_thumbnail' => $thumbnail_path,
                'up_file_category' => $category,
                'up_file_preview_available' => $preview_available,
                'up_file_width' => $image_dimensions ? $image_dimensions['width'] : null,
                'up_file_height' => $image_dimensions ? $image_dimensions['height'] : null,
            ];

            $pushed = $mod_upload->file_register_uploaded($uploaded_file_info);

            if ($pushed){
                return $this->respond([
                    'status' => 1,
                    'time' => $dated,
                    'message' => "File Uploaded Successfully",
                    'file_uuid' => $uuid,
                    'file_name' => $uploaded_File->getClientName(),
                    'file_size' => $uploaded_File->getSize()
                ]);
            }else{
                // Clean up uploaded file if database insert failed
                @unlink($upload_path . '/' . $uploaded_File->getName());
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "Database error occurred while saving file information",
                    'error_type' => 'database_error'
                ]);
            }
        }else{
            return $this->respond([
                'status' => 0,
                'time' => $dated,
                'message' => "No file received",
                'error_type' => 'no_file'
            ]);
        }
    }

    public function file_uploaded_by_phone(){
        $mod_upload = new ModUpload();

        $dated = date('Y-m-d H:i:s');
        $uuid = random_string('alnum', 16);
        $ret = array();

        if ($this->request->getPost()){
            $file_dev_id = $this->request->getVar('varDevId');
            $file_sess_id = $this->request->getVar('varSessId');

            $uploaded_File = $this->request->getFile('uploaded_file');
            //echo $uploaded_File->getSize();
            if (empty($ret)){
                echo "";
            }

            $uploaded_File->move(WRITEPATH . 'uploads/copied_files/');

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
                $ret = $this->respond([
                    'status' => 1,
                    'time' => $dated,
                    'message' => "File Uploaded Successfully"
                ]);
            }else{
                $ret = $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "File Uploaded has encountered an error"
                ]);
            }

            return $ret;

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
        $uploaded_files_by_devid = $mod_upload->file_get_uploaded_by_devid($phone_dev_id);

        if (!empty($uploaded_files_by_session_and_devid)){
            return $this->respond([
                'status' => 1,
                'time' => $dated,
                'file_info' => $uploaded_files_by_session_and_devid,
                'file_info_all' => $uploaded_files_by_devid,
            ]);
        }else{
            return $this->respond([
                'status' => 2,
                'time' => $dated,
            ]);
        }
    }

    private function validateFile($file) {
        // Check if file is valid
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => "Invalid file uploaded",
                'error_type' => 'invalid_file'
            ];
        }

        // Check for upload errors
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => "File size exceeds server limit",
                UPLOAD_ERR_FORM_SIZE => "File size exceeds form limit",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "File upload stopped by extension"
            ];

            $error_msg = isset($error_messages[$file->getError()])
                ? $error_messages[$file->getError()]
                : "Unknown upload error";

            return [
                'valid' => false,
                'message' => $error_msg,
                'error_type' => 'upload_error'
            ];
        }

        // Define allowed file types and size limits
        $allowed_types = [
            // Documents
            'pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx',
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp',
            // Archives
            'zip', 'rar', '7z', 'tar', 'gz',
            // Audio
            'mp3', 'wav', 'flac', 'aac', 'ogg',
            // Video
            'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm',
            // Code files
            'html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'h',
            'xml', 'json', 'yaml', 'yml'
        ];

        $max_file_size = 50 * 1024 * 1024; // 50MB
        $client_name = $file->getClientName();
        $extension = strtolower($file->getClientExtension());
        $file_size = $file->getSize();

        // Check file extension
        if (!in_array($extension, $allowed_types)) {
            return [
                'valid' => false,
                'message' => "File type not allowed. Allowed types: " . implode(', ', array_slice($allowed_types, 0, 10)) . "...",
                'error_type' => 'invalid_type'
            ];
        }

        // Check file size
        if ($file_size > $max_file_size) {
            return [
                'valid' => false,
                'message' => "File size too large. Maximum allowed size is " . $this->formatBytes($max_file_size),
                'error_type' => 'file_too_large'
            ];
        }

        // Check for minimum file size (0 bytes)
        if ($file_size == 0) {
            return [
                'valid' => false,
                'message' => "Empty file uploaded",
                'error_type' => 'empty_file'
            ];
        }

        // Check filename length
        if (strlen($client_name) > 255) {
            return [
                'valid' => false,
                'message' => "Filename too long",
                'error_type' => 'filename_too_long'
            ];
        }

        // Check for suspicious filenames
        $suspicious_patterns = ['/../', '/..\\', '<script', 'javascript:', 'vbscript:'];
        foreach ($suspicious_patterns as $pattern) {
            if (stripos($client_name, $pattern) !== false) {
                return [
                    'valid' => false,
                    'message' => "Suspicious filename detected",
                    'error_type' => 'suspicious_file'
                ];
            }
        }

        return ['valid' => true];
    }

    private function generateThumbnail($file_path, $file_uuid, $extension) {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        if (!in_array(strtolower($extension), $image_extensions)) {
            return null; // Not an image
        }

        if (!function_exists('imagecreatetruecolor')) {
            return null; // GD library not available
        }

        try {
            // Create thumbnails directory if it doesn't exist
            $thumb_dir = WRITEPATH . 'uploads/thumbnails';
            if (!is_dir($thumb_dir)) {
                mkdir($thumb_dir, 0755, true);
            }

            // Get image info
            $image_info = getimagesize($file_path);
            if (!$image_info) {
                return null;
            }

            $width = $image_info[0];
            $height = $image_info[1];
            $mime = $image_info['mime'];

            // Calculate thumbnail size (max 200px)
            $max_thumb_size = 200;
            if ($width > $height) {
                $thumb_width = $max_thumb_size;
                $thumb_height = intval($height * $max_thumb_size / $width);
            } else {
                $thumb_height = $max_thumb_size;
                $thumb_width = intval($width * $max_thumb_size / $height);
            }

            // Create thumbnail
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

            // Handle transparency for PNG/GIF
            if ($mime == 'image/png' || $mime == 'image/gif') {
                imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }

            // Load source image
            $source = null;
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file_path);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file_path);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($file_path);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $source = imagecreatefromwebp($file_path);
                    }
                    break;
            }

            if (!$source) {
                return null;
            }

            // Resize image
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

            // Save thumbnail
            $thumb_filename = $file_uuid . '_thumb.jpg';
            $thumb_path = $thumb_dir . '/' . $thumb_filename;

            imagejpeg($thumb, $thumb_path, 85);

            // Clean up memory
            imagedestroy($source);
            imagedestroy($thumb);

            // Return thumbnail path relative to base_url
            return 'uploads/thumbnails/' . $thumb_filename;

        } catch (Exception $e) {
            return null;
        }
    }

    private function getImageDimensions($file_path) {
        $image_info = getimagesize($file_path);
        if ($image_info) {
            return [
                'width' => $image_info[0],
                'height' => $image_info[1]
            ];
        }
        return null;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function categorizeFile($extension) {
        $extension = strtolower($extension);

        $categories = [
            'Documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods'],
            'Images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp', 'svg', 'ico'],
            'Videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'mpg', 'mpeg'],
            'Audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma', 'm4a'],
            'Archives' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
            'Code' => ['html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'h', 'xml', 'json', 'yaml', 'yml', 'sql']
        ];

        foreach ($categories as $category => $extensions) {
            if (in_array($extension, $extensions)) {
                return $category;
            }
        }

        return 'Other';
    }
}
