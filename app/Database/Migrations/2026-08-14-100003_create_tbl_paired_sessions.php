<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblPairedSessions extends Migration
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
            'pairing_code_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'session_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'device_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'paired_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('session_uuid');
        $this->forge->addKey('device_uuid');
        $this->forge->createTable('tbl_paired_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_paired_sessions', true);
    }
}
