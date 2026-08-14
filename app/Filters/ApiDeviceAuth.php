<?php

namespace App\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiDeviceAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Strictly enforce header authentication (No POST payload scanning)
        $deviceUuid = trim($request->getHeaderLine('X-Device-UUID'));

        // Exempt public endpoints (device registration, status check, pairing, metrics)
        $path = $request->getUri()->getPath();
        if (
            str_contains($path, 'device/register') ||
            str_contains($path, 'device/check') ||
            str_contains($path, 'auth/register') ||
            str_contains($path, 'device/log_metrics')
        ) {
            return;
        }

        if (empty($deviceUuid)) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Missing device authentication header (X-Device-UUID)'
                ]);
        }

        // Fast Cache Lookup Layer (Zero DB query overhead for cached devices)
        $cacheKey = 'dev_auth_' . md5($deviceUuid);
        $isAuth = cache()->get($cacheKey);

        if ($isAuth === null) {
            $db = \Config\Database::connect();
            $device = $db->table('tbl_devices')
                ->where('uuid', $deviceUuid)
                ->get()
                ->getRow();

            $isAuth = $device ? true : false;
            cache()->save($cacheKey, $isAuth, 86400); // Cache verification status for 24 hours
        }

        if (!$isAuth) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Unrecognized or invalid device identifier. Please re-register device.'
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action required after
    }
}
