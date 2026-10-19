# CodeIgniter 4 Service Guide

This guide details the internal structure, routing, filters, and developer workflows for the **CodeIgniter 4** backend in P2P Copier WebApp.

---

## 1. Service Specifications

- **Framework**: CodeIgniter 4 (^4.0)
- **Runtime**: PHP 8.3 Apache
- **Document Root**: `public/`
- **Application Directory**: `app/`
- **Uploads & Logs**: `writable/`

---

## 2. Directory Layout

```
app/
├── Config/
│   ├── App.php              # Base URL and session settings
│   ├── Database.php         # MySQL connection properties
│   ├── Filters.php          # Filter alias registrations
│   └── Routes.php           # Explicit route definitions
├── Controllers/
│   ├── Api/
│   │   ├── v1/
│   │   │   ├── AnalyticsApi.php  # Event logging and summary metrics
│   │   │   ├── AuthApi.php       # Pairing verification and session status
│   │   │   ├── DeviceApi.php     # Device registration & hardware telemetry
│   │   │   ├── FilesApi.php      # Upload, download, delete, and unified feed
│   │   │   └── TextsApi.php      # Text, OCR, and QR item management
│   │   ├── Events.php            # SSE event streaming
│   │   └── TrashApi.php          # Trashed item recovery and purging
│   ├── Auth.php             # Web landing page and pairing QR generator
│   └── Home.php             # Main browser dashboard controller
├── Database/
│   └── Migrations/          # Automated database schema migrations
├── Filters/
│   ├── FilterDeviceAuth.php # Authenticates mobile requests via dev_uuid
│   └── FilterSessionAuth.php# Authenticates web requests via session cookie
├── Models/
│   ├── ModDevice.php        # tbl_devices query layer
│   ├── ModText.php          # tbl_texts query layer
│   ├── ModUpload.php        # tbl_files query layer
│   └── ModVisitors.php      # tbl_visitors query layer
└── Views/                   # Web interface PHP/HTML templates
```

---

## 3. Filters & Security Middleware

Registered in `app/Config/Filters.php`:

1. **`session_auth` (`App\Filters\SessionAuth`)**:
   - Applied to web routes (`/home/*`, `/saved/*`) and session-authenticated Web API endpoints under `/api/v1/*` (SSE stream, file metadata/management, trash).
   - Ensures an active PHP web session exists. Returns 401 JSON on API routes or redirects unauthenticated browser requests.
2. **`device_auth` (`App\Filters\ApiDeviceAuth`)**:
   - Applied to device endpoints under `/api/v1/*`.
   - Validates `X-Device-UUID` header against `tbl_devices`, exempting public endpoints (`register`, `check`, `pair`, `metrics`). Fast cached for 24 hours.

---

## 4. Developer Recipes

### Recipe A: Adding a New API Endpoint
1. Add route in `app/Config/Routes.php` inside the `api/v1` group:
   ```php
   $routes->post('feature/action', 'Api\v1\FeatureApi::action');
   ```
2. Create or extend the controller in `app/Controllers/Api/v1/FeatureApi.php`:
   ```php
   namespace App\Controllers\Api\v1;

   use CodeIgniter\RESTful\ResourceController;

   class FeatureApi extends ResourceController
   {
       public function action()
       {
           $payload = $this->request->getJSON(true);
           return $this->respond([
               'status' => 'success',
               'data'   => $payload,
           ]);
       }
   }
   ```

### Recipe B: Creating and Running a Migration
1. Run the generator inside container or on host:
   ```bash
   php spark make:migration AddTelemetryColumn
   ```
2. Define schema changes in the generated file in `app/Database/Migrations/`.
3. Apply pending migrations:
   ```bash
   php spark migrate --all
   ```
4. Roll back if needed:
   ```bash
   php spark migrate:rollback
   ```
