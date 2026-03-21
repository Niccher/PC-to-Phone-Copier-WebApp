<?php

namespace App\Controllers;

use App\Models\ModUpload;

class RefreshIcons extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_files_uploaded');
        $files = $builder->get()->getResult();

        $updated_count = 0;
        $writepath = WRITEPATH;
        $mod_upload = new ModUpload();

        foreach ($files as $row) {
            $uuid = $row->up_file_uuid;
            $filename = $row->up_file_Name;
            $ext = strtolower($row->up_file_Extension);

            // Determine category
            $category = 'Other';
            $categories = [
                'Documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods'],
                'Images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp', 'svg', 'ico'],
                'Videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'mpg', 'mpeg'],
                'Audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma', 'm4a'],
                'Archives' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
                'Code' => ['html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'h', 'xml', 'json', 'yaml', 'yml', 'sql']
            ];

            foreach ($categories as $cat => $extensions) {
                if (in_array($ext, $extensions)) {
                    $category = $cat;
                    break;
                }
            }

            $thumbnail_path = $row->up_file_thumbnail;
            $preview_available = 0;
            $width = $row->up_file_width;
            $height = $row->up_file_height;

            $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
            if (in_array($ext, $image_extensions)) {
                $full_file_path = $writepath . "uploads/copied_files/" . $filename;

                if (file_exists($full_file_path)) {
                    $image_info = @getimagesize($full_file_path);
                    if ($image_info) {
                        $width = $image_info[0];
                        $height = $image_info[1];
                        $preview_available = 1;

                        // Thumbnail generation removed to save computational resources
                    }
                }
            }

            // Update database
            $builder->where('up_file_uuid', $uuid)->update([
                'up_file_category' => $category,
                'up_file_thumbnail' => $thumbnail_path,
                'up_file_preview_available' => $preview_available,
                'up_file_width' => $width,
                'up_file_height' => $height
            ]);

            $updated_count++;
        }

        return $this->response->setJSON([
            'status' => 1,
            'message' => "Successfully refreshed $updated_count files."
        ]);
    }
}
