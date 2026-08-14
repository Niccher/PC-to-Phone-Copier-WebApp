<?php

namespace App\Controllers\Api\v1;

use App\Models\ModAndroid;
use App\Models\ModVisitors;

class AuthApi extends ApiController
{
    public function pair()
    {
        $mod_android = new ModAndroid();
        $mod_visitors = new ModVisitors();

        $dated = date('Y-m-d H:i:s');
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $auth_code = $json['var_auth_code'] ?? $json['auth_code'] ?? null;
        $dev_uuid  = $json['var_dev_uuid'] ?? $json['dev_uuid'] ?? $this->request->getHeaderLine('X-Device-UUID');
        $auth_type = $json['var_auth_type'] ?? $json['auth_type'] ?? 'code_manual';

        if (empty($auth_code)) {
            return $this->respondError('Pairing code is required', 400);
        }

        $auth_data['auth_text_code'] = $auth_code;
        $auth_data['auth_qr_code']   = $auth_code;

        $auth_code_uuid = $mod_visitors->auth_codes_get_uuid($auth_data);

        if (!empty($auth_code_uuid)) {
            $sess_uuid = $auth_code_uuid[0]->auth_codes_uuid;
            $auth_code_id = $auth_code_uuid[0]->id;

            $reg_data = [
                'checked_auth_code_id' => $auth_code_id,
                'auth_codes_uuid'      => $sess_uuid,
                'dev_uuid'             => $dev_uuid,
                'created_at'           => $dated,
            ];

            $check_validity = $mod_visitors->auth_codes_has_tested_valid(['auth_codes_uuid' => $sess_uuid]);

            if (empty($check_validity)) {
                $mod_android->android_register_test($reg_data);
            }

            return $this->respondSuccess([
                'auth_status'       => 'True',
                'auth_type'         => $auth_type,
                'auth_auth_code'    => $auth_code,
                'auth_auth_code_id' => (string)$auth_code_id,
                'auth_session_uuid' => $sess_uuid,
                'auth_message'      => 'authenticated',
                'auth_time'         => $dated,
            ], 'Pairing code verified successfully');
        }

        return $this->respondError('Invalid or expired pairing code', 401, [
            'auth_status' => 'False',
            'auth_type'   => 'False',
        ]);
    }

    public function sessions()
    {
        $db = \Config\Database::connect();
        $dev_uuid = $this->request->getHeaderLine('X-Device-UUID') ?: $this->request->getVar('dev_uuid');

        if (empty($dev_uuid)) {
            return $this->respondError('Device UUID required', 400);
        }

        $sessions = $db->table('tbl_paired_sessions ps')
            ->select('ps.session_uuid, ps.paired_at, pc.pairing_code')
            ->join('tbl_pairing_codes pc', 'pc.session_uuid = ps.session_uuid', 'left')
            ->where('ps.device_uuid', $dev_uuid)
            ->orderBy('ps.id', 'DESC')
            ->get()
            ->getResult();

        return $this->respondSuccess([
            'sessions' => $sessions,
            'count'    => count($sessions)
        ], 'Device paired sessions retrieved');
    }

    public function reactivate()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $code = $json['pairing_code'] ?? $json['auth_code'] ?? null;
        $sess_uuid = $json['session_uuid'] ?? null;

        $builder = $db->table('tbl_pairing_codes');
        if ($sess_uuid) {
            $builder->where('session_uuid', $sess_uuid);
        } else if ($code) {
            $builder->where('pairing_code', $code);
        } else {
            return $this->respondError('Pairing code or session UUID required', 400);
        }

        $found = $builder->get()->getRow();
        if (!$found) {
            return $this->respondError('Session code not found', 404);
        }

        $session = session();
        $session->set('sess_id', $found->session_uuid);
        $session->set('auth_code', $found->pairing_code);

        return $this->respondSuccess([
            'session_uuid' => $found->session_uuid,
            'pairing_code' => $found->pairing_code,
            'redirect_url' => base_url('home')
        ], 'Session reactivated successfully');
    }

    public function sessionStatus()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $dev_uuid = $json['var_dev_uuid'] ?? $json['dev_uuid'] ?? $this->request->getHeaderLine('X-Device-UUID');
        $auth_code_id = $json['var_auth_code_id'] ?? $json['auth_code_id'] ?? null;

        if (empty($dev_uuid) || empty($auth_code_id)) {
            return $this->respondError('Device UUID and auth code ID required', 400);
        }

        $mod_visitors = new \App\Models\ModVisitors();
        $sessions = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);

        if (!empty($sessions)) {
            $sessionRow = $sessions[0];
            return $this->respondSuccess([
                'active'        => true,
                'auth_status'   => 'True',
                'auth_code_id'  => (string)$auth_code_id,
                'session_uuid'  => $sessionRow->auth_codes_uuid ?? '',
                'dev_uuid'      => $sessionRow->dev_uuid ?? $dev_uuid
            ], 'Session active and verified');
        }

        // Fallback check in tbl_pairing_codes
        $codeRow = $db->table('tbl_pairing_codes')->where('id', $auth_code_id)->get()->getRow();
        if ($codeRow) {
            return $this->respondSuccess([
                'active'        => true,
                'auth_status'   => 'True',
                'auth_code_id'  => (string)$auth_code_id,
                'session_uuid'  => $codeRow->session_uuid ?? '',
                'created_at'    => date('Y-m-d H:i:s')
            ], 'Session active and verified');
        }

        return $this->respondError('Session revoked or not found', 401, [
            'active'      => false,
            'auth_status' => 'False'
        ]);
    }
}
