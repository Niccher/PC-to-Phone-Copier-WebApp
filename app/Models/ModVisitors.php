<?php

namespace App\Models;

use CodeIgniter\Model;

class ModVisitors extends Model
{
	public function vistor_register($data){
		return $this->db ->table('tbl_visitors ')->insert($data);
	}

	public function auth_codes_register($data){
		return $this->db ->table('tbl_auth_codes ')->insert($data);
	}
}
