# REST API v1 Specification & Contract

This document provides the canonical API contract for the P2P Copier REST API (`/api/v1/`).

---

## 1. Global Conventions

- **Base URL**: `http://<host>:<port>/api/v1/`
- **Content-Type**: `application/json` (except file uploads which use `multipart/form-data`)
- **Authentication**: Devices must authenticate using `dev_uuid` and `session_uuid` passed either via request headers or body parameters.

### Standard Response Envelope

```json
{
  "status": "success",
  "code": 200,
  "message": "Operation completed successfully",
  "data": {}
}
```

### Standard Error Envelope

```json
{
  "status": "error",
  "code": 400,
  "message": "Validation failed or invalid session token",
  "errors": {}
}
```

---

## 2. Device & Auth Endpoints

### Ping
- **Endpoint**: `GET /api/v1/ping`
- **Auth**: None
- **Response**:
  ```json
  { "status": "ok", "message": "pong" }
  ```

### Device Registration
- **Endpoint**: `POST /api/v1/device/register`
- **Auth**: None
- **Payload**:
  ```json
  {
    "dev_fingerprint": "a1b2c3d4e5f6",
    "dev_model": "Pixel 8 Pro",
    "dev_brand": "Google",
    "sdk_version": 35
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "dev_uuid": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "message": "Device registered"
  }
  ```

### Session Pairing
- **Endpoint**: `POST /api/v1/auth/pair`
- **Auth**: `dev_uuid`
- **Payload**:
  ```json
  {
    "dev_uuid": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "pairing_code": "847291"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "paired",
    "session_uuid": "e6a2b851-419b-44ec-9c76-2fef63402421",
    "message": "Pairing successful"
  }
  ```

### Session Status
- **Endpoint**: `POST /api/v1/auth/session-status`
- **Payload**:
  ```json
  {
    "dev_uuid": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "session_uuid": "e6a2b851-419b-44ec-9c76-2fef63402421"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "active",
    "session_active": true
  }
  ```

---

## 3. Content Transfer & Unified Feed

### Unified Uploaded Feed
- **Endpoint**: `POST /api/v1/uploaded`
- **Payload**:
  ```json
  {
    "session_uuid": "e6a2b851-419b-44ec-9c76-2fef63402421"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "items": [
      {
        "type": "file",
        "uuid": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
        "name": "report.pdf",
        "size": 1048576,
        "extension": "pdf",
        "created_at": "2026-09-06 18:30:00"
      },
      {
        "type": "text",
        "uuid": "4fa85f64-5717-4562-b3fc-2c963f66afa7",
        "title": "OCR Scan",
        "content": "Invoice #4819 Total: $120.00",
        "source": "OCR",
        "created_at": "2026-09-06 18:25:00"
      }
    ]
  }
  ```

### Multipart File Upload
- **Endpoint**: `POST /api/v1/files/upload`
- **Content-Type**: `multipart/form-data`
- **Fields**:
  - `file`: binary file payload
  - `session_uuid`: active session token
  - `dev_uuid`: registered device identifier
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "file_uuid": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
    "filename": "document.pdf",
    "message": "File uploaded successfully"
  }
  ```

### Text / OCR Submission
- **Endpoint**: `POST /api/v1/texts`
- **Payload**:
  ```json
  {
    "session_uuid": "e6a2b851-419b-44ec-9c76-2fef63402421",
    "dev_uuid": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "title": "Scanned Document",
    "content": "Sample extracted text content",
    "source": "OCR"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "text_uuid": "4fa85f64-5717-4562-b3fc-2c963f66afa7",
    "message": "Text stored successfully"
  }
  ```

---

## 4. Server-Sent Events (SSE)

- **Endpoint**: `GET /api/events/stream`
- **Headers**: `Accept: text/event-stream`
- **Events Emitted**:
  - `paired`: Emitted when Android pairs with the web session.
  - `content_updated`: Emitted whenever a file or text item is uploaded or deleted.
