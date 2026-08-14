<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTexts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey('session_id');
        $this->forge->addKey('device_uuid');
        $this->forge->addKey('created_at');
        $this->forge->createTable('tbl_texts', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_texts', true);
    }
}
