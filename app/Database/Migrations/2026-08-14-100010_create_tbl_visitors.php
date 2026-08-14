<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblVisitors extends Migration
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
            'visited_at' => [
                'type' => 'DATETIME',
            ],
            'client_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'user_agent' => [
                'type' => 'TEXT',
            ],
            'browser' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'is_robot' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'is_mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'operating_system' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'referrer' => [
                'type' => 'TEXT',
            ],
            'http_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_visitors', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_visitors', true);
    }
}
