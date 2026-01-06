<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;
use App\Models\ModUpload;

class TypeFile extends BaseController
{
    public function index(){
        $title['title'] = "files";

        $mod_upload = new ModUpload();

        $sess_id = $this->session->get('sess_id');

        $files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
        $data['file_list'] = "";
        $count = 0;

        foreach ($files_uploaded as $file){
            $count++;
            if ($count < 4){
                // Get file icon based on extension
                $extension = strtolower($file->up_file_Extension);
                $icon_class = $this->getFileIcon($extension);

                $data['file_list'] .= '
					<div class="col-xxl-3 col-lg-6">
						<div class="card m-1 shadow-none border">
							<div class="p-2">
								<div class="row align-items-center">
									<div class="col-auto">
										<div class="avatar-sm">
											<span class="avatar-title bg-light text-secondary rounded">
												<i class="mdi mdi-'.$icon_class.' font-16"></i>
											</span>
										</div>
									</div>
									<div class="col ps-0">
										<a href="javascript:void(0);" class="text-muted fw-bold"> '.htmlspecialchars($file->up_file_Orig_Name).' </a>
										<p class="mb-0 font-13">'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</p>
									</div>
									<div class="col-auto">
										<div class="dropdown">
											<a href="#" class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
												<i class="mdi mdi-dots-vertical"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="'.base_url("saved/download/".$file->up_file_uuid).'" class="dropdown-item">
													<i class="mdi mdi-download me-2"></i>Download
												</a>
												<a href="'.base_url("saved/delete/".$file->up_file_uuid).'" class="dropdown-item text-danger">
													<i class="mdi mdi-trash-can me-2"></i>Delete
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				';
            }
        }

        return view('includes/header')
            .view('includes/sidebar', $title)
            .view('home/files', $data)
            .view('includes/footer_files');
    }

    private function getFileIcon($extension) {
        $icon_map = [
            // Documents
            'pdf' => 'file-pdf-outline',
            'doc' => 'file-word-outline',
            'docx' => 'file-word-outline',
            'xls' => 'file-excel-outline',
            'xlsx' => 'file-excel-outline',
            'ppt' => 'file-powerpoint-outline',
            'pptx' => 'file-powerpoint-outline',
            'txt' => 'file-document-outline',

            // Images
            'jpg' => 'file-image-outline',
            'jpeg' => 'file-image-outline',
            'png' => 'file-image-outline',
            'gif' => 'file-image-outline',
            'bmp' => 'file-image-outline',
            'tiff' => 'file-image-outline',
            'webp' => 'file-image-outline',

            // Archives
            'zip' => 'folder-zip-outline',
            'rar' => 'folder-zip-outline',
            '7z' => 'folder-zip-outline',
            'tar' => 'folder-zip-outline',
            'gz' => 'folder-zip-outline',

            // Audio
            'mp3' => 'file-music-outline',
            'wav' => 'file-music-outline',
            'flac' => 'file-music-outline',
            'aac' => 'file-music-outline',
            'ogg' => 'file-music-outline',

            // Video
            'mp4' => 'file-video-outline',
            'avi' => 'file-video-outline',
            'mov' => 'file-video-outline',
            'wmv' => 'file-video-outline',
            'flv' => 'file-video-outline',
            'webm' => 'file-video-outline',

            // Code files
            'html' => 'language-html5',
            'css' => 'language-css3',
            'js' => 'language-javascript',
            'php' => 'language-php',
            'py' => 'language-python',
            'java' => 'language-java',
            'cpp' => 'language-cpp',
            'c' => 'language-c',
            'xml' => 'file-xml',
            'json' => 'code-json',
        ];

        return isset($icon_map[$extension]) ? $icon_map[$extension] : 'file-outline';
    }
}
