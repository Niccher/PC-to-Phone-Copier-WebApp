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

	public function auth_codes_get_uuid($auth_codes){
		$builder = $this->db->table('tbl_auth_codes');
		$get_all = $builder
			->where($auth_codes)
			->get();
		return $get_all->getResult();
	}

	public function auth_codes_has_tested_valid($auth_codes_uuid){
		$builder = $this->db->table('tbl_checked_auth_codes');
		$get_all = $builder
			->where($auth_codes_uuid)
			->get();
		return $get_all->getResult();
	}
}
