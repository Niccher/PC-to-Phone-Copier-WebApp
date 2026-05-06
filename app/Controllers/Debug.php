<?php

namespace App\Controllers;

use App\Models\ModUpload;
use CodeIgniter\API\ResponseTrait;

class Debug extends BaseController
{
    use ResponseTrait;

    /**
     * Quick diagnostic page — visit /debug/info after scanning the QR code.
     * Shows your current session ID and all files attached to that session.
     * REMOVE THIS FILE BEFORE GOING TO PRODUCTION.
     */
    public function info()
    {
        $sess_id = $this->session->get('sess_id');
        $phone_id = $this->session->get('phone_id');

        $mod_upload = new ModUpload();
        $files = [];
        if ($sess_id) {
            $files = $mod_upload->file_get_uploaded_files($sess_id);
        }

        echo "<h2>P2P Debug Info</h2>";
        echo "<p><b>sess_id (session):</b> " . htmlspecialchars((string)$sess_id) . "</p>";
        echo "<p><b>phone_id (session):</b> " . htmlspecialchars((string)$phone_id) . "</p>";
        echo "<p><b>Files for this session (" . count($files) . " total):</b></p>";

        if (empty($files)) {
            echo "<p style='color:red'>⚠️ No files found for sess_id = $sess_id</p>";
        } else {
            echo "<table border='1' cellpadding='6'>";
            echo "<tr><th>UUID</th><th>Orig Name</th><th>Size</th><th>Source</th><th>Session ID stored</th><th>Created</th></tr>";
            foreach ($files as $f) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($f->up_file_uuid) . "</td>";
                echo "<td>" . htmlspecialchars($f->up_file_Orig_Name) . "</td>";
                echo "<td>" . htmlspecialchars((string)$f->up_file_Size) . "</td>";
                echo "<td>" . htmlspecialchars($f->up_file_Source ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($f->up_file_session_id) . "</td>";
                echo "<td>" . htmlspecialchars($f->up_file_Created_at) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        echo "<hr><h3>All recent uploads (any session, last 20)</h3>";
        $all = db()->table('tbl_files_uploaded')->orderBy('up_file_id', 'DESC')->limit(20)->get()->getResult();
        if (empty($all)) {
            echo "<p style='color:red'>No rows in tbl_files_uploaded at all.</p>";
        } else {
            echo "<table border='1' cellpadding='6'>";
            echo "<tr><th>ID</th><th>Session ID</th><th>Orig Name</th><th>Source</th><th>Created</th></tr>";
            foreach ($all as $r) {
                $highlight = ($r->up_file_session_id == $sess_id) ? " style='background:#d4edda'" : "";
                echo "<tr$highlight>";
                echo "<td>" . htmlspecialchars($r->up_file_id) . "</td>";
                echo "<td>" . htmlspecialchars($r->up_file_session_id) . "</td>";
                echo "<td>" . htmlspecialchars($r->up_file_Orig_Name) . "</td>";
                echo "<td>" . htmlspecialchars($r->up_file_Source ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($r->up_file_Created_at) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}
