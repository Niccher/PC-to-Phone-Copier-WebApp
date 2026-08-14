<div align="center">

# P2P Copier WebApp

**A CodeIgniter 4 relay server that pairs desktop web browsers with the Android client for peer-to-peer file, text, OCR, and QR data sharing over local networks.**

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker_Compose-3.8-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

</div>

---

## 📖 1. About the Project

P2P Copier WebApp is the backend relay server component of a two-repo ecosystem. Desktop web browsers and the [P2P Copier Android App](https://github.com/niccher/PC-to-Phone-Copier-App) connect to it independently to exchange files, typed text, OCR text extractions, and QR code scans.

Session pairing is established when the server generates a QR code and 6-digit numeric fallback code on the landing page. Once the Android app pairs, both clients share session context and can push or pull content dynamically.

---

## 🏛️ 2. Architecture

```
┌──────────────────────┐       ┌──────────────────────────────────┐
│   Desktop Browser    │       │   Android App (P2P_Copier_App)   │
│                      │       │                                  │
│  ┌────────────────┐  │       │  ┌─────────────────────────────┐ │
│  │ Web UI (Views) │  │       │  │ Retrofit HTTP Client        │ │
│  │ POST /home/    │  │       │  │ POST /api/v1/device/register│ │
│  │ file/upload    │  │       │  │ POST /api/v1/auth/register  │ │
│  │ GET /saved/    │  │       │  │ POST /api/v1/uploaded       │ │
│  │ download/:uuid │  │       │  │ POST /api/v1/files/upload   │ │
│  └────────┬───────┘  │       │  └─────────────┬───────────────┘ │
│           │          │       │                │                 │
│           │ HTTP     │       │                │ HTTP / POST     │
│           │ / JSON   │       │                │                 │
│           ▼          │       │                │                 │
│  ┌────────────────┐  │       │                │                 │
│  │ App\Controllers│◄─┼───────┼────────────────┘                 │
│  │ (CodeIgniter 4)│  │       │                                  │
│  └────────┬───────┘  │       └──────────────────────────────────┘
│           │          │
│           ▼          │
│  ┌────────────────┐  │
│  │    MySQL 8.4   │  │
│  │  chegecac_p2p  │  │
│  └────────────────┘  │
└──────────────────────┘
         Port 9004
```

---

## ✨ 3. Features

- 🐳 **Docker-First Environment**: Automated setup using Docker Compose (CodeIgniter 4, MySQL 8.4, and phpMyAdmin).
- 🔄 **Auto Migration & Database Setup**: Migrations run automatically on boot via `entrypoint.sh`.
- 🔐 **QR & Numeric Pairing**: Landing page generates a QR code and a 6-digit code validated via `/api/v1/auth/register`.
- 🚀 **Unified Uploaded Feed**: Single endpoint `POST /api/v1/uploaded` merges files and text entries into a single array sorted by `created_at` DESC.
- 📊 **Activity Log & Analytics**: `AnalyticsApi::summary()` computes file counts, text counts, OCR extractions, and QR scans with session fallback logic.
- 📡 **Real-Time Stream**: Server-Sent Events (`/api/events/stream`) push updates when content changes.

---

## 🛠️ 4. Tech Stack

| Layer | Technology | Version | Purpose |
|---|---|---|---|
| **Backend Framework** | CodeIgniter 4 | ^4.0 | PHP Web Framework |
| **Language** | PHP | 8.3 (Docker container) | Application backend |
| **Database** | MySQL | 8.4 | Persistent relational data store |
| **DB Administration** | phpMyAdmin | latest | Visual database manager (Port 9000) |
| **Containerization** | Docker Compose | 3.8 | Environment orchestration |
| **QR Library** | phpqrcode | in-repo | Dynamic QR generation |

---

## 📋 5. Prerequisites

- **Docker** & **Docker Compose**
- **Git**

*(Non-Docker setup requires PHP 8.3+, Composer, and MySQL 8.4).*

---

## 🚀 6. Installation & Setup

### Docker Setup (Preferred)

1. **Clone repository**:
   ```bash
   git clone https://github.com/niccher/P2P_Copier_WebApp.git
   cd "P2P Copier WebApp"
   ```
2. **Configure Environment Variables**:
   ```bash
   cp .env.example .env
   ```
3. **Build & Start Containers**:
   ```bash
   docker compose up --build -d
   ```
4. **Access Web Services**:
   - **Web Application**: [http://localhost:9004](http://localhost:9004)
   - **phpMyAdmin**: [http://localhost:9000](http://localhost:9000) (root / root_password)

---

## 🗄️ 7. Database Configuration & Schema

Production config (`app/Config/Database.php`) defaults to MySQL 8.4 overridable via `.env`.

### MySQL Tables

| Table Name | Purpose | Primary Columns |
|---|---|---|
| `tbl_files` | Active uploaded files | `id`, `uuid`, `session_id`, `original_name`, `system_name`, `file_type`, `file_size`, `created_at` |
| `tbl_files_deleted` | Soft-deleted files | Same structure as `tbl_files` + `deleted_at` |
| `tbl_texts` | Active text, OCR, & QR entries | `id`, `uuid`, `session_id`, `device_uuid`, `title`, `content`, `source`, `created_at`, `copy_count` |
| `tbl_texts_deleted` | Soft-deleted texts | Same structure as `tbl_texts` + `deleted_at` |
| `tbl_pairing_codes` | Active pairing QR & 6-digit codes | `id`, `session_uuid`, `pairing_code`, `created_at` |
| `tbl_paired_sessions` | Device-session pairing audit | `id`, `pairing_code_id`, `session_uuid`, `device_uuid`, `created_at` |
| `tbl_visitors` | Visitor IP & user agent logs | `id`, `visitor_ip`, `visitor_user_agent`, `visitor_time` |

---

## 🌐 8. API v1 Reference (`App\Controllers\Api\v1`)

### 🔐 Session & Auth
| Method | Path | Controller Action | Purpose |
|---|---|---|---|
| POST | `/api/v1/device/register` | `DeviceApi::register` | Register device fingerprint & obtain `dev_uuid` |
| POST | `/api/v1/auth/register` | `AuthApi::register` | Validate QR or 6-digit numeric pairing code |
| POST | `/api/v1/auth/session-status` | `AuthApi::sessionStatus` | Check if session pairing is active |

### 🚀 Unified Feed
| Method | Path | Controller Action | Purpose |
|---|---|---|---|
| POST/GET | `/api/v1/uploaded` | `FilesApi::uploaded` | Returns **1 single merged response** containing files AND text items sorted by `created_at` DESC |

### 📁 Files API
| Method | Path | Controller Action | Purpose |
|---|---|---|---|
| POST | `/api/v1/files/list` | `FilesApi::list` | List active files for session |
| POST | `/api/v1/files/upload` | `FilesApi::upload` | Multipart file upload from client |
| POST | `/api/v1/files/download` | `FilesApi::download` | File download handler |
| POST | `/api/v1/files/delete` | `FilesApi::delete` | Soft-delete a file |
| POST | `/api/v1/files/batch-delete` | `FilesApi::batchDelete` | Batch soft-delete files |

### 📝 Texts API
| Method | Path | Controller Action | Purpose |
|---|---|---|---|
| POST | `/api/v1/texts/list` | `TextsApi::list` | List text entries for session |
| POST | `/api/v1/texts` | `TextsApi::create` | Create new text, OCR, or QR entry |
| POST | `/api/v1/texts/delete` | `TextsApi::delete` | Soft-delete a text entry |

### 📊 Analytics API
| Method | Path | Controller Action | Purpose |
|---|---|---|---|
| GET/POST | `/api/v1/analytics/summary` | `AnalyticsApi::summary` | Activity log summary (file count, text count, OCR/QR counts) |
| POST | `/api/v1/analytics/event` | `AnalyticsApi::logEvent` | Log telemetry event |

---

## 📁 9. Project Structure

```
.
├── app/
│   ├── Config/              # App, Database, Routes, Security configuration
│   ├── Controllers/
│   │   ├── Api/v1/          # REST API Controllers (FilesApi, TextsApi, AuthApi, AnalyticsApi)
│   │   └── Home.php         # Browser Web UI Controller
│   ├── Database/
│   │   └── Migrations/      # Automated DB table migrations
│   ├── Models/              # ModUpload, ModText, ModVisitors, ModDevice
│   ├── ThirdParty/qrcode/   # In-repo phpqrcode library
│   └── Views/               # Web UI views
├── docker-compose.yml       # Docker service orchestration
├── Dockerfile               # PHP 8.3 Apache container build
└── entrypoint.sh            # MySQL wait & auto-migration script
```

---

## ⚙️ 10. Environment Variables

| Variable | Default | Purpose |
|---|---|---|
| `CI_ENVIRONMENT` | `development` | Environment mode (`development` / `production`) |
| `app.baseURL` | `http://localhost:9004/` | Application base URL |
| `database.default.hostname` | `mysql` | MySQL container hostname |
| `database.default.database` | `chegecac_p2p` | MySQL database name |
| `database.default.username` | `root` | MySQL username |
| `database.default.password` | `root_password` | MySQL password |

---

## 🤝 11. Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature/your-feature-name`.
3. Commit your changes.
4. Push to the branch and open a Pull Request.

---

## 📄 12. License

Distributed under the MIT License. See `LICENSE` for details.

---

## 💬 13. Support & Acknowledgments

- **Website:** [chegecache.co.ke](https://chegecache.co.ke)
- **Sibling repo:** [P2P Copier Android App](https://github.com/niccher/PC-to-Phone-Copier-App)
