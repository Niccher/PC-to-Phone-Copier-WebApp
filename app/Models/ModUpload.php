<?php

namespace App\Models;

use CodeIgniter\Model;

class ModUpload extends Model
{
	public function file_register_uploaded($file_info){
		return $this->db->table('tbl_files_uploaded')->insert($file_info);
	}

	function bytes_to_human_filesize($bytes, $dec = 2): string {
		$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		if ($factor == 0) $dec = 0;
		return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
	}

	public function file_get_uploaded_files($sess_id){
		$builder = $this->db->table('tbl_files_uploaded');
		$get_all = $builder
			->orderBy('up_file_count', 'DESC')
			->where('up_file_session_id', $sess_id)
			->get();
		return $get_all->getResult();
	}

	public function file_get_uploaded_by_devid($devid){
		$builder = $this->db->table('tbl_files_uploaded');
		$get_all = $builder
			->orderBy('up_file_count', 'DESC')
			->where('up_file_dev_id', $devid)
			->get();
		return $get_all->getResult();
	}

	public function file_get_uploaded_files_by_session_and_devid($sess_id, $devid){
		$builder = $this->db->table('tbl_files_uploaded');
		$get_all = $builder
			->orderBy('up_file_count', 'DESC')
			->where('up_file_session_id', $sess_id)
			->where('up_file_dev_id', $devid)
			->get();
		return $get_all->getResult();
	}

	public function file_uploaded_by_phone_session_download($phone_file_id,$phone_sess_id, $phone_dev_id){
		$builder = $this->db->table('tbl_files_uploaded');
		$get_all = $builder
			->orderBy('up_file_count', 'DESC')
			->where('up_file_uuid', $phone_file_id)
			->where('up_file_session_id', $phone_sess_id)
			->where('up_file_dev_id', $phone_dev_id)
			->get();
		return $get_all->getResult()[0];
	}
}
