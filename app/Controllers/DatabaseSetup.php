<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DatabaseSetup extends Controller
{
    public function createTextTables()
    {
        $db = \Config\Database::connect();

        //For the textual data

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
            echo "<h2>Success!</h2>";
            echo "<p>Text tables created successfully.</p>";
            echo "<p>You can now use the text copying functionality.</p>";
            echo "<a href='" . base_url('home/texts') . "'>Go to Text Page</a>";
        } catch (\Exception $e) {
            echo "<h2>Error!</h2>";
            echo "<p>Failed to create tables: " . $e->getMessage() . "</p>";
        }
    }
}
