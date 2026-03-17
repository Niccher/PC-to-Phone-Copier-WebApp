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

        $data['text_list'] = "";
        $data['text_list_all'] = "";
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
                            data-url="' . base_url('home/texts?view=' . $text->text_uuid) . '"
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
            .view('includes/sidebar', $title)
            .view('home/textual', $data)
            .view('includes/footer_texts');
    }

    public function text_save(){
        $mod_text = new ModText();
        $dated = date('Y-m-d H:i:s');
        $uuid = random_string('alnum', 16);

        if ($this->request->getPost()) {
            $text_content = $this->request->getVar('text_content');
            $text_title = $this->request->getVar('text_title') ?: 'Untitled Text';

            if (empty(trim($text_content))) {
                return $this->respond([
                    'status' => 0,
                    'time' => $dated,
                    'message' => "Text content cannot be empty"
                ]);
            }

            $text_info = [
                'text_uuid' => $uuid,
                'text_session_id' => $this->session->get('sess_id'),
                'text_dev_id' => $this->session->get('phone_id'),
                'text_title' => $text_title,
                'text_content' => $text_content,
                'text_source' => "Browser Text",
                'text_created_at' => $dated,
            ];

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

    public function text_delete($text_uuid){
        $mod_text = new ModText();
        try {
            $mod_text->text_to_delete($text_uuid);
            return redirect()->to(base_url('home/texts'))->with('message', 'Text deleted successfully');
        } catch (\Exception $ex) {
            return redirect()->to(base_url('home/texts'))->with('error', 'Failed to delete text');
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
