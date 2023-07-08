<?php

namespace App\Models;

use CodeIgniter\Model;

class ModUpload extends Model
{
	public function file_register_uploaded($file_info){
		return $this->db->table('tbl_files_uploaded')->insert($file_info);
	}
}
