<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblFilesTrash extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'system_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'file_size' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'thumbnail_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'preview_available' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'width' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'height' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'expiration_policy' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('session_id');
        $this->forge->createTable('tbl_files_trash', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_files_trash', true);
    }
}
