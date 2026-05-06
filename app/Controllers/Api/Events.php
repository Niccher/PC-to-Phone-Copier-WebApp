<?php

namespace App\Controllers\Api;

use App\Models\ModUpload;
use CodeIgniter\Controller;

class Events extends Controller
{
    public function stream()
    {
        $session = session();
        $sess_id = $session->get('sess_id');
        
        if (empty($sess_id)) {
            return $this->response->setStatusCode(401)->setBody("Unauthorized");
        }

        // CRITICAL: Close the PHP session lock so other HTTP requests from this browser don't freeze indefinitely!
        session_write_close();

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("Access-Control-Allow-Origin: *");

        $mod_upload = new ModUpload();
        $db = \Config\Database::connect();
        $last_hash = '';
        $start_time = time();

        // Run for a maximum of 30 seconds to prevent lingering Apache threads
        while (time() - $start_time < 30) {
            if (connection_aborted()) {
                break;
            }

            // Calculate current state hash based on active DB records
            $active_count = $db->table('tbl_files_uploaded')->where('up_file_session_id', $sess_id)->countAllResults();
            
            $deleted_count = 0;
            if ($db->tableExists('tbl_files_uploaded_deleted')) {
                $deleted_count = $db->table('tbl_files_uploaded_deleted')
                                    ->where('up_file_session_id', $sess_id)->countAllResults();
            }
            
            $text_count = 0;
            if ($db->tableExists('tbl_text')) {
                $text_count = $db->table('tbl_text')->where('up_text_session_id', $sess_id)->countAllResults();
            }

            $current_hash = $active_count . "_" . $deleted_count . "_" . $text_count;

            if ($last_hash !== '' && $current_hash !== $last_hash) {
                echo "data: reload\n\n";
                ob_flush();
                flush();
                break; // Break so the frontend explicitly reconnects 
            }

            $last_hash = $current_hash;
            sleep(2); // DB Polling Interval
        }
        
        // Keep-alive ping if nothing changed
        echo ": keep-alive\n\n";
        ob_flush();
        flush();
    }
}
