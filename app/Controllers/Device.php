<?php

namespace App\Controllers;

use App\Models\ModDevice;
use CodeIgniter\API\ResponseTrait;

class Device extends BaseController
{
	use ResponseTrait;

	public function device_register(){
		$mod_device = new ModDevice();

		$dated = date('Y-m-d H:i:s');
		$uuid = random_string('alnum', 16);

		if ($this->request->getPost()){
			$d_info['dev_device'] = $this->request->getVar('device_Device');
			$d_info['dev_product'] = $this->request->getVar('device_Product');
			$d_info['dev_bootloader'] = $this->request->getVar('device_Bootloader');
			$d_info['dev_type'] = $this->request->getVar('device_Type');
			$d_info['dev_tags'] = $this->request->getVar('device_Tags');
			$d_info['dev_host'] = $this->request->getVar('device_Host');
			$d_info['dev_display'] = $this->request->getVar('device_Display');
			$d_info['dev_hardware'] = $this->request->getVar('device_Hardware');
			$d_info['dev_fingerprint'] = $this->request->getVar('device_Fingerprint');
			$d_info['dev_manufacturer'] = $this->request->getVar('device_Manufacturer');
			$d_info['dev_brand'] = $this->request->getVar('device_Brand');
			$d_info['dev_board'] = $this->request->getVar('device_Board');
			$d_info['dev_model'] = $this->request->getVar('device_Model');
			$d_info['dev_serial'] = $this->request->getVar('device_Serial');

			$dev_check = $mod_device->device_check_print($d_info);
			if (empty($dev_check)){
				$d_info['dev_uuid'] = $uuid;
				$d_info['dev_created_at'] = $dated;
				$d_info['dev_user'] = $this->request->getVar('device_User');
				$dev_make = $mod_device->device_make_print($d_info);
				if ($dev_make){
					$dev_check = $mod_device->device_check_print($d_info);
					return $this->respond([
						'dev_uuid' => $dev_check[0]->dev_uuid,
						'dev_status' => "success",
						'dev_message' => "created",
						'dev_time' => $dated,
					]);
				}else{}
			}else{
				return $this->respond([
					'dev_uuid' => $dev_check[0]->dev_uuid, //$dev_check[0]->device_Uuid
					'dev_status' => "success",
					'dev_message' => "recovered",
					'dev_time' => $dated,
				]);
			}
		}else{
			return $this->respond([
				'dev_uuid' => "null",
				'dev_status' => "failed",
				'dev_message' => "unknown error",
				'dev_time' => $dated,
			]);
		}
	}
}
