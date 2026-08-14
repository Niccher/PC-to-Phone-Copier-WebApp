<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTextsTrash extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'device_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Browser Text',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'copy_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('session_id');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('tbl_texts_trash', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_texts_trash', true);
    }
}
