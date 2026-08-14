<?php

namespace App\Models;

use CodeIgniter\Model;

class ModAndroid extends Model
{
	public function android_register_test($device_info){
		$sess_uuid = $device_info['auth_codes_uuid'] ?? $device_info['session_uuid'] ?? null;
		if (empty($sess_uuid)) {
			return false;
		}
		$data = [
			'pairing_code_id' => (string)($device_info['checked_auth_code_id'] ?? $device_info['pairing_code_id'] ?? ''),
			'session_uuid'    => $sess_uuid,
			'device_uuid'     => $device_info['dev_uuid'] ?? $device_info['device_uuid'] ?? null,
			'paired_at'       => $device_info['created_at'] ?? $device_info['paired_at'] ?? date('Y-m-d H:i:s'),
		];
		return $this->db->table('tbl_paired_sessions')->insert($data);
	}

	public function android_test_auth_codes($auth_code_data){
		$code = is_array($auth_code_data) ? ($auth_code_data['pairing_code'] ?? $auth_code_data['auth_text_code'] ?? $auth_code_data['auth_qr_code'] ?? reset($auth_code_data)) : $auth_code_data;
		$builder = $this->db->table('tbl_pairing_codes');
		$get_all = $builder
			->select('id as auth_id, id, session_uuid as auth_codes_uuid, session_uuid, pairing_code as auth_codes, pairing_code')
			->groupStart()
				->where('pairing_code', $code)
				->orWhere('session_uuid', $code)
				->orWhere('session_uuid', bin2hex($code))
			->groupEnd()
			->get();
		return $get_all->getResult();
	}

	public function android_last_tested(){
		$builder = $this->db->table('tbl_pairing_codes');
		$get_all = $builder
			->get();
		return $get_all->getResult();
	}
}
