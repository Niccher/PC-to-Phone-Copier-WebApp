<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblSecurityAudit extends Migration
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
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'severity' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'MEDIUM',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'device_fingerprint' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_security_audit', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_security_audit', true);
    }
}
