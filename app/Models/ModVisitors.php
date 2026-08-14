<?php

namespace App\Models;

use CodeIgniter\Model;

class ModVisitors extends Model
{
	public function vistor_register($data){
		return $this->db->table('tbl_visitors')->insert($data);
	}

	public function auth_codes_register($data){
		$insert = [
			'session_uuid' => $data['auth_codes_uuid'] ?? $data['session_uuid'] ?? null,
			'pairing_code' => $data['auth_codes'] ?? $data['pairing_code'] ?? null,
		];
		return $this->db->table('tbl_pairing_codes')->insert($insert);
	}

	public function auth_codes_get_uuid($auth_codes){
		$code = is_array($auth_codes) ? ($auth_codes['auth_text_code'] ?? $auth_codes['auth_qr_code'] ?? $auth_codes['pairing_code'] ?? reset($auth_codes)) : $auth_codes;
		$builder = $this->db->table('tbl_pairing_codes');
		$builder->select('id, session_uuid as auth_codes_uuid, session_uuid, pairing_code as auth_codes, pairing_code')
			->groupStart()
				->where('pairing_code', $code)
				->orWhere('session_uuid', $code)
				->orWhere('session_uuid', bin2hex($code))
			->groupEnd();
		$get_all = $builder->get();
		return $get_all->getResult();
	}

	public function auth_codes_get_phone_by_auth_code_id($auth_code_id){
		$builder = $this->db->table('tbl_paired_sessions');
		$get_all = $builder
			->select('id, pairing_code_id as checked_auth_code_id, session_uuid as auth_codes_uuid, device_uuid as dev_uuid')
			->where('pairing_code_id', $auth_code_id)
			->get();
		return $get_all->getResult();
	}

	public function auth_codes_has_tested_valid($auth_codes_uuid){
		$sess_id = is_array($auth_codes_uuid) ? ($auth_codes_uuid['auth_codes_uuid'] ?? $auth_codes_uuid['session_uuid'] ?? null) : $auth_codes_uuid;
		$builder = $this->db->table('tbl_paired_sessions');
		$get_all = $builder
			->where('session_uuid', $sess_id)
			->get();
		return $get_all->getResult();
	}
}
