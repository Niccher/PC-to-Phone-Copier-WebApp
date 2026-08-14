<?php

namespace App\Controllers\Utils;

use App\Controllers\BaseController;
use App\Models\ModText;
use CodeIgniter\API\ResponseTrait;

class TypeText extends BaseController
{
    use ResponseTrait;

    public function index(){
        // Create tables if they don't exist
        $this->createTablesIfNotExist();

        $mod_text = new ModText();
        $title['title'] = "text";
        $sess_id = $this->session->get('sess_id');
        $texts_uploaded = $mod_text->text_get_uploaded_texts($sess_id);

        $data = [
            'texts' => $texts_uploaded,
            'text_list' => "",
            'text_list_all' => "",
            'title' => "text"
        ];
        
        // Use getSidebarData to simplify count passing
        $sidebarData = $this->getSidebarData('text');
        $data = array_merge($data, $sidebarData);

        $count = 0;

        foreach ($texts_uploaded as $text){
            $count++;
            if ($count < 4){
                $truncated_content = strlen($text->text_content) > 100 ?
                    substr($text->text_content, 0, 100) . '...' : $text->text_content;

                $data['text_list'] .= '
                    <div class="col-xxl-4 col-lg-6">
                        <div class="card m-1 shadow-none border">
                            <div class="p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-primary text-white rounded">
                                                <i class="mdi mdi-text font-16"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <a href="javascript:void(0);" class="text-muted fw-bold copy-text-btn" data-text="' . htmlspecialchars($text->text_content) . '">
                                            ' . htmlspecialchars($text->text_title ?: 'Untitled Text') . '
                                        </a>
                                        <p class="mb-0 font-13">' . htmlspecialchars($truncated_content) . '</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }

            $truncated_content_full = strlen($text->text_content) > 200 ?
                substr($text->text_content, 0, 200) . '...' : $text->text_content;

            $data['text_list_all'] .= '
                <tr>
                    <td>
                        <span class="fw-semibold">
                            <a href="javascript: void(0);" class="text-reset copy-text-btn" data-text="' . htmlspecialchars($text->text_content) . '">' . htmlspecialchars($text->text_title ?: 'Untitled Text') . '</a>
                        </span>
                    </td>
                    <td class="text-nowrap text-muted">' . date('M j, Y H:i', strtotime($text->text_created_at)) . '</td>
                    <td><span class="badge bg-light text-dark">' . str_replace("Text","", $text->text_source) . '</span></td>
                    <td class="text-muted">' . strlen($text->text_content) . ' chars</td>
                    <td class="text-center text-nowrap">
                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary py-0 px-2 me-1 copy-text-btn" data-text="' . htmlspecialchars($text->text_content) . '" title="Copy">
                            <i class="mdi mdi-content-copy"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-info py-0 px-2 me-1 share-qr-btn"
                            data-url="' . base_url('text/view/' . $text->text_uuid) . '"
                            data-title="' . htmlspecialchars($text->text_title ?: 'Untitled Text') . '"
                            data-size="' . strlen($text->text_content) . ' chars"
                            data-date="' . date('M j, Y H:i', strtotime($text->text_created_at)) . '"
                            title="Share QR">
                            <i class="mdi mdi-qrcode"></i>
                        </a>
                        <a href="' . base_url("text/delete/" . $text->text_uuid) . '" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Delete this text?\')" title="Delete">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </td>
                </tr>
            ';
        }

        return view('includes/header')
            .view('includes/sidebar', $data)
            .view('home/textual', $data)
            .view('includes/footer_texts', $data);
    }

    public function public_view($uuid) {
        $mod_text = new ModText();
        $text_data = $mod_text->text_get_uploaded_by_uuid($uuid);

        if (empty($text_data)) {
            return "Text not found or has already been burned.";
        }

        $text = $text_data[0];

        // Perform Burn After Reading if policy is 2
        if ($text->text_expiration_policy == 2) {
            $mod_text->text_to_delete($uuid);
        }

        // Return a simple, clean view for the shared text
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($text->text_title) . '</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
            <style>
                body { background-color: #f8f9fa; padding-top: 50px; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
                .text-card { max-width: 800px; margin: auto; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
                .burn-badge { background-color: #ff4d4d; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
                .content-box { background-color: #fff; border: 1px solid #dee2e6; border-radius: 10px; padding: 20px; min-height: 200px; }
            </style>
        </head>
        <body>
            <div class="container mb-5">
                <div class="card text-card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h4 class="mb-0 text-primary">' . htmlspecialchars($text->text_title) . '</h4>
                        ' . ($text->text_expiration_policy == 2 ? '<span class="burn-badge"><i class="mdi mdi-fire"></i> BURNT AFTER READING</span>' : '') . '
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="text-muted small mb-3"><i class="mdi mdi-clock-outline me-1"></i>Created: ' . date('M j, Y H:i', strtotime($text->text_created_at)) . '</div>
                        <div class="content-box shadow-sm">' . ($text->text_content) . '</div>
                        ' . ($text->text_expiration_policy == 2 ? '<div class="alert alert-warning mt-4 py-2 small"><i class="mdi mdi-alert-circle-outline me-1"></i>This text has been permanently deleted from the server now that you have viewed it.</div>' : '') . '
                    </div>
                    <div class="card-footer bg-light text-center py-3 border-0 rounded-bottom-15">
                        <p class="mb-0 text-muted small">Securely shared via <a href="' . base_url() . '" class="text-decoration-none fw-bold">P2P Web Copier</a></p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    public function text_save(){
        $mod_text = new ModText();
        $dated = date('Y-m-d H:i:s');
        $uuid = random_string('alnum', 16);

        if ($this->request->getPost()) {
            $text_content = $this->request->getVar('text_content');
            $text_content_base64 = $this->request->getVar('text_content_base64');
            
            if (!empty($text_content_base64)) {
                $text_content = base64_decode($text_content_base64);
            }
            
            $text_title = $this->request->getVar('text_title') ?: 'Untitled Text';
            $text_source = $this->request->getVar('text_source') ?: "Android Text";

            $sess_id = $this->session->get('sess_id') ?: $this->request->getVar('var_auth_code_id');
            $dev_id = $this->session->get('phone_id') ?: $this->request->getVar('var_dev_uuid');

            if (empty(trim((string)$text_content))) {
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "Text content cannot be empty"
                ]);
            }

            $text_info = [
                'text_uuid' => $uuid,
                'text_session_id' => $sess_id ?: 'default_session',
                'text_dev_id' => $dev_id ?: 'default_device',
                'text_title' => $text_title,
                'text_content' => $text_content,
                'text_source' => $text_source,
                'text_created_at' => $dated,
            ];

            $expiration_policy = $this->request->getVar('expiration_policy') ?: 0;
            $expires_at = null;
            if ($expiration_policy == 1) {
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            }
            
            $text_info['text_expiration_policy'] = $expiration_policy;
            $text_info['text_expires_at'] = $expires_at;

            $pushed = $mod_text->text_register_uploaded($text_info);

            if ($pushed) {
                return $this->respond([
                    'status' => 1,
                    'time' => $dated,
                    'message' => "Text saved successfully",
                    'text_uuid' => $uuid
                ]);
            } else {
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "Failed to save text"
                ]);
            }
        } else {
            return $this->respond([
                'status' => 0,
                'time' => $dated,
                'message' => "Invalid request method"
            ]);
        }
    }

    public function text_get_by_session(){
        $mod_text = new ModText();
        $dated = date('Y-m-d H:i:s');

        $phone_dev_id = $this->request->getVar('var_dev_uuid');
        $phone_sess_id = $this->request->getVar('var_auth_code_id');

        if (empty($phone_sess_id)) {
            $phone_sess_id = $this->session->get('sess_id');
        }

        $texts = $mod_text->text_get_uploaded_texts($phone_sess_id);

        return $this->respond([
            'status' => 1,
            'time' => $dated,
            'message' => "Texts retrieved successfully",
            'texts' => $texts
        ]);
    }

    public function text_delete($text_uuid){
        $mod_text = new ModText();
        try {
            $mod_text->text_to_delete($text_uuid);
            return redirect()->back()->with('message', 'Text deleted successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Failed to delete text');
        }
    }

    private function createTablesIfNotExist(){
        $db = \Config\Database::connect();

        // Create main text table
        $sql1 = "CREATE TABLE IF NOT EXISTS `tbl_texts_uploaded` (
            `text_id` int(11) NOT NULL AUTO_INCREMENT,
            `text_uuid` varchar(20) NOT NULL,
            `text_session_id` varchar(20) NOT NULL,
            `text_dev_id` varchar(100) DEFAULT NULL,
            `text_title` varchar(255) DEFAULT NULL,
            `text_content` longtext NOT NULL,
            `text_source` varchar(50) NOT NULL DEFAULT 'Browser Text',
            `text_created_at` datetime NOT NULL,
            `text_count` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`text_id`),
            UNIQUE KEY `text_uuid` (`text_uuid`),
            KEY `text_session_id` (`text_session_id`),
            KEY `text_dev_id` (`text_dev_id`),
            KEY `text_created_at` (`text_created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        // Create deleted text table
        $sql2 = "CREATE TABLE IF NOT EXISTS `tbl_texts_uploaded_deleted` (
            `text_id` int(11) NOT NULL,
            `text_uuid` varchar(20) NOT NULL,
            `text_session_id` varchar(20) NOT NULL,
            `text_dev_id` varchar(100) DEFAULT NULL,
            `text_title` varchar(255) DEFAULT NULL,
            `text_content` longtext NOT NULL,
            `text_source` varchar(50) NOT NULL DEFAULT 'Browser Text',
            `text_created_at` datetime NOT NULL,
            `text_count` int(11) NOT NULL DEFAULT 0,
            `deleted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`text_id`),
            KEY `text_uuid` (`text_uuid`),
            KEY `deleted_at` (`deleted_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        try {
            $db->query($sql1);
            $db->query($sql2);
        } catch (\Exception $e) {
            // Log error but don't stop execution
            log_message('error', 'Failed to create text tables: ' . $e->getMessage());
        }
    }
}
