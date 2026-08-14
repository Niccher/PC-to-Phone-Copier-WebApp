<div align="center">

# P2P Copier WebApp

**A relay server that pairs a desktop browser with an Android device for peer-to-peer file and clipboard sharing over a local network.**

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker_Compose-3.8-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

</div>

---

## About the Project

P2P Copier WebApp is the server-side component of a two-repo ecosystem. It acts as a lightweight relay — the browser and the [P2P Copier Android App](https://github.com/niccher/P2P_Copier_App) both connect to it independently. When a user pastes text or uploads a file on one device, it becomes available on the other via a shared session authenticated by QR code or numeric code.

The backend is a CodeIgniter 4 PHP application containerized with Docker Compose alongside MySQL 8.4 and phpMyAdmin. There is no third-party cloud dependency — all data stays on the host.

---

## Architecture

```
┌──────────────────────┐       ┌──────────────────────────────────┐
│   Desktop Browser    │       │   Android App (P2P_Copier_App)   │
│                      │       │                                  │
│  ┌────────────────┐  │       │  ┌─────────────────────────────┐ │
│  │ Web UI (Views) │  │       │  │ Retrofit HTTP Client        │ │
│  │ POST /home/    │  │       │  │ POST /device/register       │ │
│  │ file/upload    │  │       │  │ POST /auth/register         │ │
│  │ GET /saved/    │  │       │  │ POST /home/phone/upload     │ │
│  │ download/:uuid │  │       │  │ POST /home/phone/           │ │
│  └────────┬───────┘  │       │  │   get_files_uploaded_by_    │ │
│           │          │       │  │   session                   │ │
│           │ HTTPS    │       │  │ POST /home/phone/           │ │
│           │ / JSON   │       │  │   get_files_uploaded_by_    │ │
│           │          │       │  │   session_download          │ │
│           ▼          │       │  └─────────────┬───────────────┘ │
│  ┌────────────────┐  │       │                │                 │
│  │ App\Controllers│  │       │                │ HTTPS / POST    │
│  │ (PHP)          │◄─┼───────┼────────────────┘                 │
│  │                │  │       │                                  │
│  └────────┬───────┘  │       └──────────────────────────────────┘
│           │          │
│           ▼          │
│  ┌────────────────┐  │
│  │    MySQL 8.4   │  │
│  │  db_p2p_copier │  │
│  └────────────────┘  │
│                      │
│  ┌────────────────┐  │
│  │   phpMyAdmin   │  │  Port 9000
│  │   (optional)   │  │
│  └────────────────┘  │
└──────────────────────┘
         Port 9004
```

The system uses **session-based authentication**: the server generates a QR code and a 6-digit numeric code on the landing page. The Android app scans the QR (or enters the code) via `POST /auth/register`, which validates the code against `tbl_auth_codes`. After authentication, both devices share the same session ID and can upload/download files and text.

File transfers are stored on the host filesystem under `writable/uploads/copied_files/`. No WebRTC, no signalling server — the server is the relay.

---

## Features

### Session Pairing
| Feature | Description |
|---|---|
| **QR Code Pairing** | Server generates a QR code on the landing page; Android scans it via ZXing to authenticate |
| **Numeric Code** | 6-digit fallback code displayed alongside the QR; manually entered on the phone |
| **Device Fingerprinting** | Android sends Build fingerprint data; backend deduplicates and returns a persistent device UUID |

### File Transfer
| Feature | Description |
|---|---|
| **Browser Upload** | Drag-and-drop or file picker upload with category detection by extension |
| **Android Upload** | Multipart POST from the app; files stored under the session |
| **Browser Download** | Direct download or inline preview with MIME type detection |
| **Android Download** | Sequential batch download to the device's Downloads directory |
| **Burn-after-reading** | Files tagged with expiration policy 2 are deleted after the first download |
| **1-Hour Expiry** | Files tagged with policy 1 auto-expire and are cleaned up by the model |
| **500 MB Quota** | Per-session storage cap checked before accepting new uploads |

### Text Transfer
| Feature | Description |
|---|---|
| **Web Clipboard** | Text typed or pasted in the browser is saved to `tbl_texts_uploaded` |
| **OCR Text** | Android app captures a photo and extracts text via Firebase ML Kit on-device |
| **Public Text View** | Shareable UUID links to view text in the browser |

### File Management
| Feature | Description |
|---|---|
| **Categorization** | Files auto-sorted into Documents, Images, Videos, Audio, Archives, Code, or Other |
| **Tagging** | User-defined tags with color labels |
| **Search** | Search by filename, category, tags, or file type |
| **Batch Operations** | Multi-select delete, tag, and download (as ZIP) |
| **Trash** | Soft-delete with restore; permanent delete or empty-trash |

### Real-Time UI
| Feature | Description |
|---|---|
| **Server-Sent Events** | `GET /api/events/stream` polls DB every 2 s for 30 s; pushes `data: reload` on change |

### Security
| Feature | Description |
|---|---|
| **Session Auth** | No passwords — one-time QR/text codes expire after use |
| **Encryption Key** | `encryption.key` in `.env` for CodeIgniter session encryption |
| **Device Binding** | Auth code validation is linked to a registered device UUID |

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| **Backend Framework** | CodeIgniter 4 | ^4.0 |
| **Language** | PHP | ^7.4 \|\| ^8.0 (Docker uses 8.3) |
| **Database** | MySQL | 8.4 |
| **DB Admin** | phpMyAdmin | latest |
| **Containerization** | Docker Compose | 3.8 |
| **QR Code** | phpqrcode (custom) | in-repo `ThirdParty/qrcode/` |
| **Unit Testing** | PHPUnit | ^9.1 |

---

## Prerequisites

- **Docker** & **Docker Compose** (recommended path)
- **Git**

> For local (non-Docker) development: PHP 8.0+, Composer, MySQL 8.4, `mysqli` PHP extension.

---

## Installation & Setup

### Docker (recommended)

```bash
git clone <repo-url>
cd "P2P Copier WebApp"
cp .env.example .env
docker compose up --build -d
```

Services and ports:

| Service | Container | Host Port |
|---|---|---|
| **WebApp** | `p2p-copier-webapp` | `9004:80` |
| **MySQL** | `shared-mysql` | `9306:3306` |
| **phpMyAdmin** | `shared-phpmyadmin` | `9000:80` |

The entrypoint script (`entrypoint.sh`) waits for MySQL and runs `php spark migrate --all` automatically on first boot.

- **Application:** `http://localhost:9004`
- **phpMyAdmin:** `http://localhost:9000` (root / root_password)

**Useful commands:**
```bash
docker compose logs -f p2p-copier          # live app logs
docker compose down                         # stop
docker compose restart p2p-copier           # restart app only
```

### Local (without Docker)

```bash
git clone <repo-url>
cd "P2P Copier WebApp"
cp .env.example .env
composer install
# update .env with your MySQL credentials
php spark migrate --all
php spark serve
```

---

## Database Configuration

The production config (`app/Config/Database.php`) defaults to the live host. All values are overridable via `.env` variables.

### Tables (custom P2P)

| Table | Purpose | Key Columns |
|---|---|---|
| `tbl_files_uploaded` | Active uploaded files | `up_file_uuid`, `up_file_session_id`, `up_file_Orig_Name`, `up_file_Sys_Name`, `up_file_Type`, `up_file_Size`, `up_file_category`, `up_file_tags`, `up_file_expiration_policy`, `up_file_expires_at` |
| `tbl_files_uploaded_deleted` | Soft-deleted files | Same as above + `deleted_at` |
| `tbl_texts_uploaded` | Active text entries | `text_uuid`, `text_session_id`, `text_content`, `text_source`, `text_count` |
| `tbl_texts_uploaded_deleted` | Soft-deleted texts | Same as above + `deleted_at` |
| `tbl_auth_codes` | Generated QR + text auth codes | `auth_codes_uuid`, `auth_codes` |
| `tbl_checked_auth_codes` | Audit of which device validated which code | `checked_auth_code_id`, `auth_codes_uuid` |
| `tbl_visitors` | Visitor analytics | `visitor_time`, `visitor_ip`, `visitor_user_agent`, `visitor_browser`, `visitor_OS` |
| `tbl_file_tags` | User-defined tag vocabulary | `tag_id`, `tag_name`, `tag_color` |
| `tbl_file_categories` | File category definitions | `category_id`, `category_name`, `category_icon`, `category_color` |

The CodeIgniter Shield auth tables (`users`, `auth_identities`, `auth_logins`, `auth_token_logins`, `auth_remember_tokens`, `auth_groups_users`, `auth_permissions_users`) are present but unused by the current authentication flow.

---

## API Reference

All endpoints are served from `app.baseURL` (default `https://p2p.chegecache.co.ke/`).

### Session & Auth

| Method | Path | Description |
|---|---|---|
| GET | `/` | Landing page — generates QR code + 6-digit numeric code, logs visitor |
| POST | `/auth/register` | Validates a QR or text code against the server; returns `auth_status` + `auth_auth_code_id` |
| GET | `/auth/login` | Login page |
| GET | `/auth/logout` | Destroys session |

### Device Registration

| Method | Path | Description |
|---|---|---|
| POST | `/device/register` | Registers (or recovers) a device fingerprint; returns `dev_uuid` |

### Browser Pages

| Method | Path | Description |
|---|---|---|
| GET | `/home` | Dashboard with recent files and texts |
| GET | `/home/files` | All uploaded files |
| GET | `/home/texts` | All saved texts |
| GET | `/home/trashed` | Trash (deleted files + texts) |

### File Upload / Download (Browser)

| Method | Path | Description |
|---|---|---|
| POST | `/home/file/upload` | Upload file from browser (multipart) |
| GET | `/saved/download/{uuid}` | Download file by UUID |
| GET | `/saved/view/{uuid}` | Stream file inline in browser |
| GET | `/saved/delete/{uuid}` | Soft-delete a file |

### File / Text Operations (Android → Server)

| Method | Path | Description |
|---|---|---|
| POST | `/home/phone/upload` | Multipart file upload from Android |
| POST | `/home/phone/get_files_uploaded_by_session` | List files for a session+device |
| POST | `/home/phone/get_files_uploaded_by_session_download` | Download file by UUID |
| POST | `/home/phone/set_files_to_delete` | Soft-delete a file |

### Text Operations

| Method | Path | Description |
|---|---|---|
| POST | `/text/save` | Save text content |
| GET | `/text/view/{uuid}` | Public view of a text snippet |
| GET | `/text/delete/{uuid}` | Soft-delete a text |

### File Management API

| Method | Path | Description |
|---|---|---|
| POST | `/files/search` | Search files by term, category, tags |
| POST | `/files/rename` | Rename file |
| POST | `/files/add-tag` | Add tag to file |
| POST | `/files/remove-tag` | Remove tag from file |
| POST | `/files/update-category` | Update file category |
| POST | `/files/update-description` | Update file description |
| POST | `/files/batch-delete` | Batch soft-delete |
| POST | `/files/batch-add-tag` | Batch tag |
| POST | `/files/batch-download` | Download multiple files as ZIP |
| GET | `/files/preview/{uuid}` | Get full file details |
| GET | `/files/metadata` | List all categories and tags |

### Trash API

| Method | Path | Description |
|---|---|---|
| POST | `/api/restore-file` | Restore file from trash |
| POST | `/api/restore-text` | Restore text from trash |
| POST | `/api/permanent-delete-file` | Permanently delete file |
| POST | `/api/permanent-delete-text` | Permanently delete text |
| POST | `/api/empty-trash` | Empty all trash for session |

### Real-Time

| Method | Path | Description |
|---|---|---|
| GET | `/api/events/stream` | SSE stream — pushes `data: reload` on data change |

### Debug / Setup

| Method | Path | Description |
|---|---|---|
| GET | `/debug/info` | Session and file debug info *(remove in production)* |
| GET | `/setup/text-tables` | One-off creation of text tables |

---

## Project Structure

```
.
├── app/
│   ├── Config/              # CodeIgniter config (App, Database, Routes, Filters, Security, etc.)
│   ├── Controllers/         # Auth, Device, Upload, Download, FileManager, Debug, Home
│   │   └── Api/             # Events (SSE), TrashApi
│   │   └── Utils/           # TypeFile, TypeText, TypeTrash
│   ├── Database/
│   │   └── Migrations/      # Shield auth tables + custom P2P tables migration
│   ├── Libraries/           # QR_Generator wrapper
│   ├── Models/              # ModUpload, ModText, ModDevice, ModVisitors, ModAndroid
│   ├── ThirdParty/qrcode/   # phpqrcode library (13 PHP files)
│   └── Views/               # Auth, Home, Includes, Error pages
├── public/                  # Document root (index.php, assets)
├── writable/                # Cache, logs, sessions, uploads (copied_files, copied_files_deleted)
├── vendor/                  # Composer dependencies
├── docker-compose.yml       # Service orchestration (webapp, mysql, phpmyadmin)
├── Dockerfile               # php:8.3-apache with mysqli/pdo_mysql
├── entrypoint.sh            # Waits for MySQL, runs migrations, starts Apache
├── composer.json
└── spark                    # CodeIgniter CLI entry point
```

---

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `CI_ENVIRONMENT` | `development` | CodeIgniter environment (`production` / `testing`) |
| `app.baseURL` | `https://p2p.chegecache.co.ke/` | Application base URL (must end with `/`) |
| `app.forceGlobalSecureRequests` | `false` | Force HTTPS on all requests |
| `database.default.hostname` | `localhost` | MySQL host |
| `database.default.database` | `chegecac_p2p_copier` | MySQL database |
| `database.default.username` | `chegecac_p2p` | MySQL user |
| `database.default.password` | — | MySQL password |
| `database.default.DBDriver` | `MySQLi` | Database driver |
| `database.default.port` | `3306` | MySQL port |
| `encryption.key` | — | 32-byte hex-encoded session encryption key |
| `AUTH_SEND_EMAIL_ON_REGISTER` | `false` | Disable registration emails |
| `email.*` | — | SMTP settings (Mailtrap placeholder) |

---

## Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature/your-feature-name`.
3. Commit your changes.
4. Push to the branch.
5. Open a Pull Request.

Please adhere to the `.editorconfig` and PSR-12 coding standards.

---

## License

Distributed under the MIT License. See `LICENSE` for details.

---

## Support / Contact

- **Website:** [chegecache.co.ke](https://chegecache.co.ke)
- **Email:** [info@chegecache.co.ke](mailto:info@chegecache.co.ke)
- **Sibling repo:** [P2P Copier Android App](https://github.com/niccher/P2P_Copier_App)
