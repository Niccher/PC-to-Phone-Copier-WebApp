# Runbook: Backup and Disaster Recovery

Procedures for snapshotting and restoring the MySQL relational database and uploaded binary files in P2P Copier WebApp.

---

## 1. Database Backup (`mysqldump`)

### Creating a Snapshot
Run this command from the repository root:

```bash
# Create timestamped SQL dump
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
docker compose exec -T mysql mysqldump \
  -u root -proot_password \
  --single-transaction \
  --quick \
  db_p2p_copier > "backup_p2p_copier_${TIMESTAMP}.sql"

echo "Backup created: backup_p2p_copier_${TIMESTAMP}.sql"
```

---

## 2. Media & Uploads Storage Backup

User-uploaded files live in `writable/uploads/`. To archive them alongside the SQL snapshot:

```bash
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
tar -czvf "backup_uploads_${TIMESTAMP}.tar.gz" writable/uploads/
```

---

## 3. Database Restoration

To restore a database dump into a fresh or recovered MySQL instance:

```bash
# 1. Ensure MySQL container is running
docker compose up -d mysql

# 2. Pipe the SQL backup directly into MySQL
cat backup_p2p_copier_YYYYMMDD_HHMMSS.sql | \
  docker compose exec -T mysql mysql -u root -proot_password db_p2p_copier

# 3. Run migrations to reconcile any new schema changes
docker compose exec -T p2p-copier php spark migrate --all
```

---

## 4. Media Restoration

```bash
# Extract files back to writable/uploads
tar -xzvf backup_uploads_YYYYMMDD_HHMMSS.tar.gz -C ./

# Ensure write permissions
chmod -R 775 writable/uploads/
```
