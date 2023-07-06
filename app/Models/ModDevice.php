<?php

namespace App\Models;

use CodeIgniter\Model;

class ModDevice extends Model
{
	public function device_make_print($device_info){
		return $this->db->table('tbl_android_devices')->insert($device_info);
	}

	public function device_check_print($device_info){
		$builder = $this->db->table('tbl_android_devices');
		$get_all = $builder->select('dev_uuid')
			->where($device_info)
			->get();
		return $get_all->getResult();
	}
}
