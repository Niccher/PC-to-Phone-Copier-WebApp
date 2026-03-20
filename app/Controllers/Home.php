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
        $auth_codes = explode("---", base64_decode($this->request->getVar('a_num_code')));

        $code_data = [
            'auth_text_code' => $auth_codes[0],
            'auth_qr_code' => $auth_codes[1]
        ];

        $get_auth_id = ($mod_visitors->auth_codes_get_uuid($code_data))[0]->auth_id;
        $get_auth_phone_uuid = ($mod_visitors->auth_codes_get_phone_by_auth_code_id($get_auth_id))[0]->checked_by;

        header('Access-Control-Allow-Headers: Origin');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Origin: *');

        if (!empty($get_auth_id)) {
            $code_data_is_valid = [
                'checked_auth_code_id' => $get_auth_id,
                //'checked_phone_uuid'=> $get_auth_id,
                'checked_is_valid' => "valid",
            ];
            $is_code_validated = count($mod_visitors->auth_codes_has_tested_valid($code_data_is_valid));
            if ($is_code_validated > 0) {
                $sess_cookie = array(
                    'name' => 'sess_id',
                    'value_sess_id' => $get_auth_id,
                    'value_phone_uuid' => $get_auth_phone_uuid,
                    'expire' => '144000',
                );
                $_SESSION["sess_id"] = $get_auth_id;
                $_SESSION["phone_id"] = $get_auth_phone_uuid;
                set_cookie($sess_cookie);
            }
            echo ($is_code_validated > 0) ? "valid" : "invalid";
        }
    }

}