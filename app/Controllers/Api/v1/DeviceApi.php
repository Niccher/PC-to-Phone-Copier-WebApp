<?php

namespace App\Controllers\Api\v1;

use App\Models\ModDevice;

class DeviceApi extends ApiController
{
    public function register()
    {
        $mod_device = new ModDevice();
        $dated = date('Y-m-d H:i:s');
        $uuid = random_string('alnum', 16);

        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $d_info = [
            'dev_device'       => $json['device_Device'] ?? $json['device_name'] ?? null,
            'dev_product'      => $json['device_Product'] ?? $json['product'] ?? null,
            'dev_bootloader'   => $json['device_Bootloader'] ?? null,
            'dev_type'         => $json['device_Type'] ?? null,
            'dev_tags'         => $json['device_Tags'] ?? null,
            'dev_host'         => $json['device_Host'] ?? null,
            'dev_display'      => $json['device_Display'] ?? null,
            'dev_hardware'     => $json['device_Hardware'] ?? $json['hardware'] ?? null,
            'dev_fingerprint'  => $json['device_Fingerprint'] ?? null,
            'dev_manufacturer' => $json['device_Manufacturer'] ?? $json['manufacturer'] ?? null,
            'dev_brand'        => $json['device_Brand'] ?? $json['brand'] ?? null,
            'dev_board'        => $json['device_Board'] ?? $json['board'] ?? null,
            'dev_model'        => $json['device_Model'] ?? $json['model'] ?? null,
            'dev_serial'       => $json['device_Serial'] ?? null,
        ];

        $dev_check = $mod_device->device_check_print($d_info);
        if (empty($dev_check)) {
            $d_info['dev_uuid'] = $uuid;
            $d_info['dev_created_at'] = $dated;
            $d_info['dev_user'] = $json['device_User'] ?? null;
            $dev_make = $mod_device->device_make_print($d_info);
            if ($dev_make) {
                $dev_check = $mod_device->device_check_print($d_info);
                $dev_uuid = $dev_check[0]->dev_uuid;
                cache()->save('dev_auth_' . md5($dev_uuid), true, 86400);

                return $this->respondSuccess([
                    'dev_uuid'    => $dev_uuid,
                    'dev_status'  => 'success',
                    'dev_message' => 'created',
                    'dev_time'    => $dated,
                ], 'Device registered successfully', 201);
            }
            return $this->respondError('Failed to create device registration', 500);
        }

        $dev_uuid = $dev_check[0]->dev_uuid;
        cache()->save('dev_auth_' . md5($dev_uuid), true, 86400);

        return $this->respondSuccess([
            'dev_uuid'    => $dev_uuid,
            'dev_status'  => 'success',
            'dev_message' => 'recovered',
            'dev_time'    => $dated,
        ], 'Device registration recovered');
    }

    public function metrics()
    {
        $db = \Config\Database::connect();
        $dated = date('Y-m-d H:i:s');
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $data = [
            'device_uuid'  => $json['device_uuid'] ?? $this->request->getHeaderLine('X-Device-UUID'),
            'brand'        => $json['brand'] ?? $this->request->getHeaderLine('X-Device-Brand'),
            'manufacturer' => $json['manufacturer'] ?? $this->request->getHeaderLine('X-Device-Manufacturer'),
            'model'        => $json['model'] ?? $this->request->getHeaderLine('X-Device-Model'),
            'device_name'  => $json['device_name'] ?? null,
            'product'      => $json['product'] ?? null,
            'hardware'     => $json['hardware'] ?? null,
            'board'        => $json['board'] ?? null,
            'android_os'   => $json['android_os'] ?? $this->request->getHeaderLine('X-Device-OS'),
            'sdk_int'      => $json['sdk_int'] ?? null,
            'app_version'  => $json['app_version'] ?? null,
            'screen_resolution' => $json['screen_res'] ?? $json['screen_resolution'] ?? null,
            'locale'            => $json['locale'] ?? null,
            'timezone'          => $json['timezone'] ?? null,
            'client_ip'         => $this->request->getIPAddress(),
            'user_agent'        => (string)$this->request->getUserAgent(),
            'logged_at'         => $dated,
        ];

        try {
            $db->table('tbl_device_metrics')->insert($data);
            return $this->respondSuccess(['logged_at' => $dated], 'Device metrics logged successfully');
        } catch (\Exception $e) {
            return $this->respondError($e->getMessage(), 500);
        }
    }

    public function ping()
    {
        return $this->respondSuccess([
            'status'      => 'online',
            'app'         => 'P2P Copier WebApp',
            'version'     => '1.0.0',
            'api_version' => 'v1',
            'timestamp'   => date('Y-m-d H:i:s'),
        ], 'P2P Copier Server Ping OK');
    }
}
