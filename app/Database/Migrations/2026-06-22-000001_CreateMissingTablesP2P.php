<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMissingTablesP2P extends Migration
{
    public function up()
    {
        // tbl_visitors
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'visitor_time' => [
                'type' => 'DATETIME',
            ],
            'visitor_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'visitor_user_agent' => [
                'type' => 'TEXT',
            ],
            'visitor_browser' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'visitor_robot' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'visitor_is_mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'visitor_OS' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'visitor_referrer' => [
                'type' => 'TEXT',
            ],
            'visitor_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_visitors', true);

        // tbl_files_uploaded
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'up_file_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'up_file_session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'up_file_Orig_Name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'up_file_Sys_Name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'up_file_Type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'up_file_Size' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'up_file_Created_at' => [
                'type' => 'DATETIME',
            ],
            'up_file_thumbnail' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'up_file_tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'up_file_category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'up_file_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'up_file_preview_available' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'up_file_width' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'up_file_height' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'up_file_expiration_policy' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'up_file_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_files_uploaded', true);

        // tbl_auth_codes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'auth_codes_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'auth_codes' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_auth_codes', true);

        // tbl_checked_auth_codes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'checked_auth_code_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'auth_codes_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_checked_auth_codes', true);
    }


        // tbl_file_tags
        $this->forge->addField([
            'tag_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'tag_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tag_color' => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#007bff'],
            'tag_created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('tag_id', true);
        $this->forge->addUniqueKey('tag_name');
        $this->forge->createTable('tbl_file_tags', true);

        // tbl_file_categories
        $this->forge->addField([
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'category_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'category_icon' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'mdi-folder'],
            'category_color' => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#6c757d'],
            'category_description' => ['type' => 'TEXT', 'null' => true],
            'category_created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('category_id', true);
        $this->forge->addUniqueKey('category_name');
        $this->forge->createTable('tbl_file_categories', true);

        // tbl_texts_uploaded
        $this->forge->addField([
            'text_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'text_uuid' => ['type' => 'VARCHAR', 'constraint' => 20],
            'text_session_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'text_dev_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'text_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'text_content' => ['type' => 'LONGTEXT'],
            'text_source' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Browser Text'],
            'text_created_at' => ['type' => 'DATETIME'],
            'text_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('text_id', true);
        $this->forge->addUniqueKey('text_uuid');
        $this->forge->addKey('text_session_id');
        $this->forge->addKey('text_dev_id');
        $this->forge->addKey('text_created_at');
        $this->forge->createTable('tbl_texts_uploaded', true);

        // tbl_texts_uploaded_deleted
        $this->forge->addField([
            'text_id' => ['type' => 'INT', 'constraint' => 11],
            'text_uuid' => ['type' => 'VARCHAR', 'constraint' => 20],
            'text_session_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'text_dev_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'text_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'text_content' => ['type' => 'LONGTEXT'],
            'text_source' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Browser Text'],
            'text_created_at' => ['type' => 'DATETIME'],
            'text_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'deleted_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('text_id', true);
        $this->forge->addKey('text_uuid');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('tbl_texts_uploaded_deleted', true);

        // tbl_files_uploaded_deleted
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'up_file_uuid' => ['type' => 'VARCHAR', 'constraint' => 100],
            'up_file_session_id' => ['type' => 'VARCHAR', 'constraint' => 100],
            'up_file_Orig_Name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'up_file_Sys_Name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'up_file_Type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'up_file_Size' => ['type' => 'INT', 'constraint' => 11],
            'up_file_Created_at' => ['type' => 'DATETIME'],
            'up_file_thumbnail' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'up_file_tags' => ['type' => 'TEXT', 'null' => true],
            'up_file_category' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'up_file_description' => ['type' => 'TEXT', 'null' => true],
            'up_file_preview_available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'up_file_width' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'up_file_height' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'up_file_expiration_policy' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'up_file_expires_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_files_uploaded_deleted', true);

    public function down()
    {

        $this->forge->dropTable('tbl_files_uploaded_deleted', true);
        $this->forge->dropTable('tbl_texts_uploaded_deleted', true);
        $this->forge->dropTable('tbl_texts_uploaded', true);
        $this->forge->dropTable('tbl_file_categories', true);
        $this->forge->dropTable('tbl_file_tags', true);
        $this->forge->dropTable('tbl_visitors', true);
        $this->forge->dropTable('tbl_files_uploaded', true);
        $this->forge->dropTable('tbl_auth_codes', true);
        $this->forge->dropTable('tbl_checked_auth_codes', true);
    }
}
