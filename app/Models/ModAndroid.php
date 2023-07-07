<?php

namespace App\Models;

use CodeIgniter\Model;

class ModAndroid extends Model
{
	public function android_register_test($device_info){
		return $this->db->table('tbl_checked_auth_codes')->insert($device_info);
	}

	public function android_test_auth_codes($auth_code_data){
		$builder = $this->db->table('tbl_auth_codes');
		//$get_all = $builder->select('auth_id');
		$get_all = $builder
			->where($auth_code_data)
			->get();
		return $get_all->getResult();
	}

	public function android_last_tested(){
		$builder = $this->db->table('tbl_auth_codes');
		$get_all = $builder
			->get();
		return $get_all->getResult();
	}
}
