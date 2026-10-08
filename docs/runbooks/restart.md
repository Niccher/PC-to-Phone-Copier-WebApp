# Runbook: Service Restart & Container Recovery

This runbook details standard operating procedures for gracefully stopping, rebuilding, and recovering containers for the P2P Copier WebApp stack.

---

## 1. Graceful Restart (Zero Data Loss)

Use when deploying code updates or applying minor `.env` configuration changes:

```bash
# 1. Gracefully stop services
docker compose stop

# 2. Restart services
docker compose start

# 3. Verify health
docker compose ps
```

---

## 2. Full Clean Rebuild (Without Losing Database Data)

Use when modifying `Dockerfile`, `composer.json`, or PHP system extensions:

```bash
# 1. Stop and remove existing containers (preserves mysql-data volume)
docker compose down

# 2. Rebuild images from scratch without layer cache
docker compose build --no-cache

# 3. Start containers in detached mode
docker compose up -d

# 4. Stream startup logs to verify migrations
docker compose logs -f p2p-copier
```

---

## 3. Database Deadlock & MySQL Recovery

Use when MySQL encounters a lock wait timeout or aborted startup:

```bash
# 1. Stop all services
docker compose stop

# 2. Restart only the MySQL container
docker compose up -d mysql

# 3. Wait until healthcheck passes
docker compose ps mysql

# 4. Start remaining application services
docker compose up -d
```

---

## 4. Nuclear Reset (Destroys All Local Data)

> [!WARNING]
> This command completely wipes the database volume and all local file uploads.

```bash
# Tear down containers, networks, and persistent volumes
docker compose down -v

# Clean uploads directory
rm -rf writable/uploads/*

# Rebuild and start fresh
docker compose up --build -d
```
