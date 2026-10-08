# Database & Migrations Engineering

This guide details database lifecycle management, migration rules, and soft-delete conventions in P2P Copier WebApp.

---

## 1. Migration Standards

All database changes must be executed via CodeIgniter 4 migrations in `app/Database/Migrations/`.

### File Naming Convention
Migrations follow sequential UTC timestamps:
`YYYY-MM-DD-HHMMSS_create_table_name.php`

### Implementation Pattern
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewFeatureTable extends Migration
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
                'constraint' => 64,
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_new_feature', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_new_feature', true);
    }
}
```

---

## 2. Soft-Delete Architecture

To prevent accidental data loss and preserve an audit trail:
- Active files live in `tbl_files`; soft-deleted items move to `tbl_files_trash`.
- Active text items live in `tbl_texts`; soft-deleted items move to `tbl_texts_trash`.
- Trashed items can be restored via `POST /api/restore-file` or permanently purged via `POST /api/permanent-delete-file`.

---

## 3. Applying and Rolling Back Migrations

```bash
# Apply all pending migrations across all namespaces
php spark migrate --all

# View migration status
php spark migrate:status

# Roll back the most recent migration batch
php spark migrate:rollback
```
