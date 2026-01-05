<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;
use App\Models\ModUpload;

class TypeFile extends BaseController
{
    public function index(){
	    $title['title'] = "file";

	    $mod_upload = new ModUpload();

	    $sess_id = $this->session->get('sess_id');

	    $files_uploaded = $mod_upload->file_get_uploaded_files($sess_id);
	    $data['file_list'] = "";
	    $data['file_list_all'] = "";
	    $count = 0;

	    foreach ($files_uploaded as $file){
		    $count++;
		    if ($count < 4){
			    $data['file_list'] .= '
					<div class="col-xxl-4 col-lg-6">
						<div class="card m-1 shadow-none border">
							<div class="p-2">
								<div class="row align-items-center">
									<div class="col-auto">
										<div class="avatar-sm">
											<span class="avatar-title bg-light text-secondary rounded">
												<i class="mdi mdi-folder font-16"></i>
											</span>
										</div>
									</div>
									<div class="col ps-0">
										<a href="javascript:void(0);" class="text-muted fw-bold"> '.$file->up_file_Name.' </a>
										<p class="mb-0 font-13">'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</p>
									</div>
								</div>
								<!-- end row -->
							</div>
							<!-- end .p-2-->
						</div>
						<!-- end col -->
					</div>
				';
		    }

		    $data['file_list_all'] .= '
				<tr>
					<td>
					    <span class="fw-semibold">
					        <a href="javascript: void(0);" class="text-reset">'.$file->up_file_Name.'</a>
					    </span>
					    <br>
					    <span class="font-12">'.$file->up_file_Created_at.'</span>
					</td>
					<td>'.str_replace("Upload","",$file->up_file_Source).'</td>
					<td>'.$mod_upload->bytes_to_human_filesize($file->up_file_Size).'</td>
					<td class="">
					    <div class="file_download" id="'.$file->up_file_uuid.'" onclick="setFileDelete()">
					        <i class="mdi mdi-download widget-icon"></i>
						</a>
					    <a href="'.base_url("saved/delete/".$file->up_file_uuid).'">
					    	<i class="mdi mdi-trash-can widget-icon"></i>
				        </a>
					</td>
					</tr>
			';
	    }
	    //echo $this->session->get('sess_id');

	    return view('includes/header')
		    .view('includes/sidebar', $title)
		    .view('home/files', $data)
		    .view('includes/footer_files');
    }
}
