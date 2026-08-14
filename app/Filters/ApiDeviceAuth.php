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
        // Strictly enforce header authentication
        $deviceUuid   = trim($request->getHeaderLine('X-Device-UUID'));
        $deviceBrand  = trim($request->getHeaderLine('X-Device-Brand'));
        $deviceModel  = trim($request->getHeaderLine('X-Device-Model'));
        $deviceOs     = trim($request->getHeaderLine('X-Device-OS'));
        $fingerprint  = trim($request->getHeaderLine('X-Device-Fingerprint'));

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
                    'message' => 'Missing required device authentication header (X-Device-UUID)'
                ]);
        }

        // Fast Cache Lookup Layer
        $cacheKey = 'dev_auth_' . md5($deviceUuid);
        $isAuth = cache()->get($cacheKey);

        if ($isAuth === null) {
            $db = \Config\Database::connect();
            $device = $db->table('tbl_devices')
                ->where('uuid', $deviceUuid)
                ->get()
                ->getRow();

            $isAuth = $device ? true : false;
            cache()->save($cacheKey, $isAuth, 86400); // Cache verification for 24h

            // Sync device headers into tbl_devices metadata if present
            if ($device && (!empty($deviceBrand) || !empty($deviceModel))) {
                $db->table('tbl_devices')
                    ->where('uuid', $deviceUuid)
                    ->update([
                        'brand'       => $deviceBrand ?: $device->brand,
                        'model'       => $deviceModel ?: $device->model,
                        'os_version'  => $deviceOs ?: $device->os_version,
                        'fingerprint' => $fingerprint ?: ($device->fingerprint ?? null),
                        'updated_at'  => date('Y-m-d H:i:s')
                    ]);
            }
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
