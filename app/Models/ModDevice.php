<?php

namespace App\Models;

use CodeIgniter\Model;

class ModDevice extends Model
{
	public function device_make_print($device_info){
		$data = [
			'uuid'          => $device_info['dev_uuid'] ?? $device_info['uuid'] ?? null,
			'device_name'   => $device_info['dev_device'] ?? $device_info['device_name'] ?? null,
			'product'       => $device_info['dev_product'] ?? $device_info['product'] ?? null,
			'bootloader'    => $device_info['dev_bootloader'] ?? $device_info['bootloader'] ?? null,
			'device_type'   => $device_info['dev_type'] ?? $device_info['device_type'] ?? null,
			'tags'          => $device_info['dev_tags'] ?? $device_info['tags'] ?? null,
			'host'          => $device_info['dev_host'] ?? $device_info['host'] ?? null,
			'display'       => $device_info['dev_display'] ?? $device_info['display'] ?? null,
			'hardware'      => $device_info['dev_hardware'] ?? $device_info['hardware'] ?? null,
			'fingerprint'   => $device_info['dev_fingerprint'] ?? $device_info['fingerprint'] ?? null,
			'manufacturer'  => $device_info['dev_manufacturer'] ?? $device_info['manufacturer'] ?? null,
			'brand'         => $device_info['dev_brand'] ?? $device_info['brand'] ?? null,
			'board'         => $device_info['dev_board'] ?? $device_info['board'] ?? null,
			'model'         => $device_info['dev_model'] ?? $device_info['model'] ?? null,
			'serial_number' => $device_info['dev_serial'] ?? $device_info['serial_number'] ?? null,
			'user'          => $device_info['dev_user'] ?? $device_info['user'] ?? null,
			'created_at'    => $device_info['dev_created_at'] ?? $device_info['created_at'] ?? date('Y-m-d H:i:s'),
		];
		return $this->db->table('tbl_devices')->insert(array_filter($data, fn($v) => !is_null($v)));
	}

	public function device_check_print($device_info){
		$builder = $this->db->table('tbl_devices');
		$query_data = [
			'brand'        => $device_info['dev_brand'] ?? $device_info['brand'] ?? null,
			'model'        => $device_info['dev_model'] ?? $device_info['model'] ?? null,
			'manufacturer' => $device_info['dev_manufacturer'] ?? $device_info['manufacturer'] ?? null,
		];
		$get_all = $builder->select('uuid as dev_uuid, uuid')
			->where(array_filter($query_data, fn($v) => !is_null($v)))
			->get();
		return $get_all->getResult();
	}
}
