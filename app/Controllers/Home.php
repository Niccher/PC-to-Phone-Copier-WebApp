<?php

namespace App\Controllers;

use App\Models\ModUpload;
use App\Models\ModVisitors;
use CodeIgniter\API\ResponseTrait;

use function PHPUnit\Framework\isEmpty;

class Home extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('welcome_message');
    }

    public function home()
    {
        $mod_upload = new ModUpload();
        $mod_text = new \App\Models\ModText();

        $sess_id = $this->session->get('sess_id');

        // Get recent files (last 10)
        $recent_files = $mod_upload->file_get_uploaded_files($sess_id, 10);

        // Get recent texts (last 10)
        $recent_texts = $mod_text->text_get_uploaded_texts($sess_id, 10);

        $data = [
            'recent_files' => $recent_files,
            'recent_texts' => $recent_texts
        ];
        $sidebarData = $this->getSidebarData('recent');
        $data = array_merge($data, $sidebarData);

        return view('includes/header')
            . view('includes/sidebar', $data)
            . view('home/home', $data)
            . view('includes/footer_home', $data);
    }

    public function home_ajax_code_check()
    {
        $mod_visitors = new ModVisitors();
        $raw_input = $this->request->getVar('a_num_code') ?: $this->request->getVar('code');

        if (empty($raw_input)) {
            echo "invalid";
            return;
        }

        $decoded = base64_decode($raw_input, true);
        if ($decoded !== false && str_contains($decoded, '---')) {
            $auth_codes = explode("---", $decoded);
            $code_data = [
                'auth_text_code' => $auth_codes[0],
                'auth_qr_code'   => $auth_codes[1] ?? $auth_codes[0]
            ];
        } else if (str_contains($raw_input, '---')) {
            $auth_codes = explode("---", $raw_input);
            $code_data = [
                'auth_text_code' => $auth_codes[0],
                'auth_qr_code'   => $auth_codes[1] ?? $auth_codes[0]
            ];
        } else {
            $code_data = [
                'auth_text_code' => $raw_input,
                'auth_qr_code'   => $raw_input
            ];
        }

        $auth_result = $mod_visitors->auth_codes_get_uuid($code_data);
        if (empty($auth_result)) {
            echo "invalid";
            return;
        }

        $auth_record = $auth_result[0];
        $auth_id     = $auth_record->id;
        $sess_uuid   = $auth_record->session_uuid;
        $pair_code   = $auth_record->pairing_code;

        $db = \Config\Database::connect();
        $paired_session = $db->table('tbl_paired_sessions')
            ->groupStart()
                ->where('session_uuid', $sess_uuid)
                ->orWhere('pairing_code_id', (string)$auth_id)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        header('Access-Control-Allow-Headers: Origin, Content-Type');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Origin: *');

        if ($paired_session) {
            $session = session();
            // Store session_uuid so tbl_files and tbl_texts queries match uploaded items
            $session->set('sess_id', $sess_uuid);
            $session->set('auth_code_id', (string)$auth_id);
            $session->set('auth_code', $pair_code);
            $session->set('phone_id', $paired_session->device_uuid ?? null);

            echo "valid";
        } else {
            echo "invalid";
        }
    }

}