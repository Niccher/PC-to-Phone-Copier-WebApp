# P2P Copier WebApp

A CodeIgniter 4 backend relay server pairing desktop browsers with the Android client for peer-to-peer file, text, OCR, and QR data sharing over local networks.

Stack: CodeIgniter 4 (PHP 8.3), MySQL 8.4, Docker Compose.

**If you only need to run the system, this page is enough.**  
Software engineers: [docs/README.md](docs/README.md).

## What “running” looks like

| Component | URL / Endpoint | Port | Health Check / Dev Login |
|-----------|----------------|------|--------------------------|
| Web UI | http://localhost:9004 | 9004 | Landing pairing page with QR code |
| REST API | http://localhost:9004/api/v1/ping | 9004 | Returns JSON `status: "ok"` |
| phpMyAdmin | http://localhost:9000 | 9000 | User: `root` / Pass: `root_password` |
| MySQL | localhost:9306 | 9306 | Internal DB: `db_p2p_copier` |

## Prerequisites

### Option A — Docker (recommended for users)

- Git
- Docker Engine & Docker Compose v2 (or Docker Desktop)

### Option B — Without Docker (native)

- PHP 8.3+ with `intl`, `mbstring`, `mysqli`, `curl` extensions
- Composer 2.x
- MySQL 8.4+

## Setup and run

From a fresh machine, numbered, copy-pasteable:

1. Clone the repository:
   ```bash
   git clone https://github.com/niccher/P2P_Copier_WebApp.git
   cd "P2P Copier WebApp"
   ```
2. Copy the environment configuration template:
   ```bash
   cp .env.example .env
   ```
3. Build and start the containers (MySQL, phpMyAdmin, and WebApp):
   ```bash
   docker compose up --build -d
   ```
4. Verify all containers are running and healthy:
   ```bash
   docker compose ps
   ```
5. Open the Web UI at [http://localhost:9004](http://localhost:9004).
6. When finished, stop the stack:
   ```bash
   docker compose down
   ```

## Configuration users may change

| Variable | Default | Purpose |
|----------|---------|---------|
| `CI_ENVIRONMENT` | `development` | Environment mode (`development` / `production`) |
| `app.baseURL` | `http://localhost:9004/` | Web application base URL |
| `database.default.hostname` | `mysql` | MySQL service name (Docker) or `localhost` |
| `database.default.database` | `db_p2p_copier` | Active MySQL database name |
| `database.default.port` | `3306` | MySQL port inside the network |

Full configuration reference: [docs/user/configuration.md](docs/user/configuration.md).

## Something went wrong?

- **Port 9004 or 9000 in use**: Stop conflicting services or adjust published ports in `docker-compose.yml`.
- **Database connection failure**: Wait 20 seconds on first run while MySQL initializes its storage volume and seeds tables.
- **Upload folder permission denied**: Ensure `writable/` is writable: `chmod -R 777 writable`.

Detailed troubleshooting: [docs/user/troubleshooting.md](docs/user/troubleshooting.md).

## Sibling Ecosystem Repositories

| Component | Responsibility | Repository URL | Documentation |
|---|---|---|---|
| **Android App** | Kotlin/Java Client (ML Kit OCR, ZXing QR) | [P2P_Copier_App](https://github.com/niccher/PC-to-Phone-Copier-App) | [docs/](https://github.com/niccher/PC-to-Phone-Copier-App/tree/main/docs) |
| **Web App** | CodeIgniter 4 Relay Server & Web UI | [P2P Copier WebApp](https://github.com/niccher/P2P_Copier_WebApp) | [docs/](docs/README.md) |

## Software engineers

- Master Index: [docs/README.md](docs/README.md)
- Architecture Overview: [docs/architecture/overview.md](docs/architecture/overview.md)
- Communication & Pairing: [docs/architecture/communication.md](docs/architecture/communication.md)
- Database & Schemas: [docs/architecture/data-and-storage.md](docs/architecture/data-and-storage.md)
- API Contract: [docs/api/contract.md](docs/api/contract.md)
- CodeIgniter 4 Service Guide: [docs/services/codeigniter.md](docs/services/codeigniter.md)
- Making Changes Safely: [docs/engineering/making-changes.md](docs/engineering/making-changes.md)
