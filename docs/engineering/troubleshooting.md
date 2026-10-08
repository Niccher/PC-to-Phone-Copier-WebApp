# Developer Troubleshooting Guide

Diagnostic steps for software engineers facing build, test, or container compilation issues in P2P Copier WebApp.

---

## 1. Class Not Found / Autoload Failures

**Symptom**: `Class 'App\Controllers\Api\v1\FooApi' not found` or namespace resolution failure.

**Remediation**:
Regenerate Composer classmap and autoload bindings:
```bash
composer dump-autoload
```

---

## 2. Migration Deadlocks or "Table Already Exists"

**Symptom**: `php spark migrate` fails with table already exists error after manual schema intervention.

**Remediation**:
Inspect the `migrations` table in MySQL to reconcile batch state:
```sql
SELECT * FROM migrations ORDER BY version DESC;
```
If a table was partially created during an aborted script, drop the incomplete table manually via phpMyAdmin or MySQL CLI, then re-run:
```bash
php spark migrate --all
```

---

## 3. Docker Volume Out-of-Sync

**Symptom**: Newly added PHP files in `app/` are not recognized inside the running Apache container.

**Remediation**:
Restart the webapp container:
```bash
docker compose restart p2p-copier
```
If volume sync issues persist on Linux due to filesystem permissions:
```bash
chmod -R 775 app/ public/ writable/
```
