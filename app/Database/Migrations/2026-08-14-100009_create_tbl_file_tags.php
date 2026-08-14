<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblFileTags extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'default'    => '#007bff',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('tbl_file_tags', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_file_tags', true);
    }
}
