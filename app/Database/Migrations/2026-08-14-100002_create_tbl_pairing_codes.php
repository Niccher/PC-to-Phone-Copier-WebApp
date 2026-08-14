<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblPairingCodes extends Migration
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
            'session_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pairing_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('session_uuid');
        $this->forge->addKey('pairing_code');
        $this->forge->createTable('tbl_pairing_codes', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pairing_codes', true);
    }
}
