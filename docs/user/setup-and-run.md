# Setup and Run Guide

This guide details how to run the P2P Copier WebApp for users, operators, and testers.

---

## 1. Quick Start via Docker Compose (Recommended)

Docker Compose provides a zero-dependency setup containing PHP 8.3 (Apache), MySQL 8.4, and phpMyAdmin.

### Step 1: Clone Repository
```bash
git clone https://github.com/niccher/P2P_Copier_WebApp.git
cd "P2P Copier WebApp"
```

### Step 2: Configure Environment
Copy the example environment configuration:
```bash
cp .env.example .env
```
Default values in `.env` are pre-configured to work directly with Docker Compose services.

### Step 3: Launch Containers
Build images and start containers in detached mode:
```bash
docker compose up --build -d
```

### Step 4: Verify Container Health
Check container statuses:
```bash
docker compose ps
```
A healthy cluster displays:
- `p2p-copier-webapp`: `Up` (port 9004)
- `shared-mysql`: `Up (healthy)` (port 9306)
- `shared-phpmyadmin`: `Up` (port 9000)

Verify API health via curl:
```bash
curl -s http://localhost:9004/api/v1/ping
```
Expected output:
```json
{"status":"ok","message":"pong"}
```

### Step 5: Access Web Interfaces
- **Web App Interface**: [http://localhost:9004](http://localhost:9004)
- **phpMyAdmin**: [http://localhost:9000](http://localhost:9000)
  - Username: `root`
  - Password: `root_password`

### Step 6: Stopping the System
When finished:
```bash
docker compose down
```
To also remove database volumes and start fresh:
```bash
docker compose down -v
```

---

## 2. Native Setup (Without Docker)

For running directly on host Linux / macOS without Docker:

### Prerequisites
- PHP 8.3 with extensions: `php8.3-cli`, `php8.3-mysql`, `php8.3-intl`, `php8.3-mbstring`, `php8.3-curl`
- MySQL 8.4
- Composer 2.x

### Steps
1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Prepare database in MySQL:
   ```sql
   CREATE DATABASE db_p2p_copier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Copy and adjust `.env`:
   ```bash
   cp .env.example .env
   ```
   Set `database.default.hostname = localhost`, `database.default.database = db_p2p_copier`, and your MySQL username/password.
4. Run migrations:
   ```bash
   php spark migrate --all
   ```
5. Ensure `writable/` directory has proper write permissions:
   ```bash
   chmod -R 775 writable
   ```
6. Start the development server:
   ```bash
   php spark serve --host 0.0.0.0 --port 9004
   ```
7. Open [http://localhost:9004](http://localhost:9004).
