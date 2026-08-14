<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblFileCategories extends Migration
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
                'constraint' => '100',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'mdi-folder',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'default'    => '#6c757d',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('tbl_file_categories', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_file_categories', true);
    }
}
