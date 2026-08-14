<?php

namespace App\Controllers\Api\v1;

class AnalyticsApi extends ApiController
{
    public function summary()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $dev_uuid = $json['var_dev_uuid'] ?? $json['dev_uuid'] ?? $this->request->getHeaderLine('X-Device-UUID');
        $auth_code_id = $json['var_auth_code_id'] ?? $json['auth_code_id'] ?? null;

        $session_id = $auth_code_id;
        if (!empty($auth_code_id) && is_numeric($auth_code_id)) {
            try {
                $mod_visitors = new \App\Models\ModVisitors();
                $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);
                if (!empty($sess)) {
                    $session_id = $sess[0]->auth_codes_uuid;
                }
            } catch (\Throwable $e) {}
        }

        // 1. Files count & latest upload date
        $totalFiles = 0;
        $lastUpload = date('Y-m-d H:i:s');
        try {
            if ($db->tableExists('tbl_files')) {
                $filesQuery = $db->table('tbl_files')
                    ->select('COUNT(id) as total, MAX(created_at) as latest')
                    ->where('session_id', $session_id)
                    ->get()
                    ->getRow();
                $totalFiles = (int)($filesQuery->total ?? 0);
                if (!empty($filesQuery->latest)) {
                    $lastUpload = $filesQuery->latest;
                }
            }
        } catch (\Throwable $e) {}

        // 2. Texts count & OCR/QR breakdown
        $totalTexts = 0;
        $totalOcr = 0;
        $totalQrScans = 0;
        try {
            if ($db->tableExists('tbl_texts')) {
                $builder = $db->table('tbl_texts');
                if (!empty($session_id) && $session_id !== 'general') {
                    $builder->groupStart()
                        ->where('session_id', $session_id)
                        ->orWhere('session_id', 'general')
                        ->groupEnd();
                }
                $textsList = $builder->get()->getResult();
                $totalTexts = count($textsList);

                foreach ($textsList as $txt) {
                    $src = strtolower($txt->source ?? '');
                    if (strpos($src, 'ocr') !== false || strpos($src, 'image') !== false) {
                        $totalOcr++;
                    } elseif (strpos($src, 'qr') !== false || strpos($src, 'scan') !== false) {
                        $totalQrScans++;
                    }
                }
            }
        } catch (\Throwable $e) {}

        // Fallback checks on security audit log for extra QR/OCR events if not logged in tbl_texts
        try {
            if ($db->tableExists('tbl_security_audit')) {
                $qrQuery = $db->table('tbl_security_audit')
                    ->select('COUNT(id) as total')
                    ->where('user_agent', $dev_uuid)
                    ->whereIn('event_type', ['data_qr_scan', 'data_qr', 'qr_data'])
                    ->get()
                    ->getRow();
                $auditQr = (int)($qrQuery->total ?? 0);
                if ($auditQr > $totalQrScans) {
                    $totalQrScans = $auditQr;
                }

                $ocrQuery = $db->table('tbl_security_audit')
                    ->select('COUNT(id) as total')
                    ->where('user_agent', $dev_uuid)
                    ->whereIn('event_type', ['ocr_scan', 'ocr_text', 'image_ocr'])
                    ->get()
                    ->getRow();
                $auditOcr = (int)($ocrQuery->total ?? 0);
                if ($auditOcr > $totalOcr) {
                    $totalOcr = $auditOcr;
                }
            }
        } catch (\Throwable $e) {}

        // 5. Last download action
        $lastDownload = 'Not downloaded yet';

        return $this->respondSuccess([
            'total_files'          => $totalFiles,
            'total_texts'          => $totalTexts,
            'total_qr_scans'       => $totalQrScans,
            'total_ocr_extractions'=> $totalOcr,
            'last_sync'            => date('Y-m-d H:i:s'),
            'last_upload'          => $lastUpload,
            'last_download'        => $lastDownload
        ], 'Activity summary retrieved successfully');
    }

    public function logEvent()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $dev_uuid = $json['var_dev_uuid'] ?? $json['dev_uuid'] ?? $this->request->getHeaderLine('X-Device-UUID');
        $event_type = $json['event_type'] ?? 'general';

        if (empty($dev_uuid)) {
            return $this->respondError('Device UUID required', 400);
        }

        try {
            if ($db->tableExists('tbl_security_audit')) {
                $db->table('tbl_security_audit')->insert([
                    'event_type'  => $event_type,
                    'ip_address'  => $this->request->getIPAddress(),
                    'user_agent'  => $dev_uuid,
                    'created_at'  => date('Y-m-d H:i:s')
                ]);
            }
        } catch (\Throwable $e) {}

        return $this->respondSuccess([
            'logged'     => true,
            'event_type' => $event_type,
            'timestamp'  => date('Y-m-d H:i:s')
        ], 'Telemetry event recorded');
    }
}
