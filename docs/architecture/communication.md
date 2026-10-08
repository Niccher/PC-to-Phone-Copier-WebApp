# Communication & Protocol Architecture

This document details inter-service network protocols, authentication handshakes, sequence flows, and event-driven synchronization between the CodeIgniter 4 WebApp and the Android client.

---

## 1. Inter-Service Communication Matrix

| Source | Destination | Protocol / Transport | Auth Mechanism | Base URL (Dev) | Description |
|---|---|---|---|---|---|
| **Web Browser** | CI4 Server | HTTP / HTTPS (JSON & Multipart) | PHP Session Cookie (`session_auth`) | `http://localhost:9004/` | Landing page, file uploads, text submissions, and deletions. |
| **Web Browser** | CI4 Server | Server-Sent Events (SSE) | PHP Session Cookie (`session_auth`) | `http://localhost:9004/api/events/stream` | Real-time event stream alerting browser to new uploads or pairing events. |
| **Android App** | CI4 Server | HTTP / JSON & Multipart | Device UUID (`device_auth`) | `http://10.0.2.2:9004/` (Emulator)<br>`http://192.168.x.x:9004/` (LAN) | Device registration, pairing, unified feed querying, and uploads. |
| **CI4 Server** | MySQL 8.4 | MySQL TCP Protocol | Native MySQL Authentication | `mysql:3306` | Relational query execution via PDO / MySQLi. |

---

## 2. Session Pairing Sequence

Session pairing establishes a secure cryptographic link between the desktop browser session and the Android device.

```mermaid
sequenceDiagram
    autonumber
    actor User
    participant Browser as Desktop Browser
    participant Server as CI4 WebApp
    participant DB as MySQL 8.4
    participant App as Android Client

    User->>Browser: Open http://localhost:9004/
    Browser->>Server: GET / (Auth::landing)
    Server->>DB: Generate & insert session_uuid + 6-digit code in tbl_pairing_codes
    Server-->>Browser: Render Landing View (QR Code + Numeric Code)
    Browser->>Server: GET /api/events/stream (SSE listener)

    User->>App: Launch App & Scan QR (or type code)
    App->>Server: POST /api/v1/device/register (Specs & fingerprint)
    Server->>DB: Insert device fingerprint into tbl_devices
    Server-->>App: Return dev_uuid

    App->>Server: POST /api/v1/auth/pair {dev_uuid, pairing_code}
    Server->>DB: Validate pairing code in tbl_pairing_codes
    Server->>DB: Record link in tbl_paired_sessions
    Server-->>App: Return 200 OK {status: "paired", session_uuid}

    Server--)Browser: SSE Event "paired" {session_uuid}
    Browser->>Server: GET /home (Redirect to paired dashboard)
```

---

## 3. Data Transfer Sequence (Unified Feed)

```mermaid
sequenceDiagram
    autonumber
    participant App as Android Client
    participant Server as CI4 WebApp
    participant DB as MySQL 8.4
    participant Disk as Local Storage
    participant Browser as Desktop Browser

    Note over App,Server: Android Uploads File or OCR Text
    alt File Upload
        App->>Server: POST /api/v1/files/upload (MultipartFormData + dev_uuid + session_uuid)
        Server->>Disk: Store file in writable/uploads/
        Server->>DB: INSERT into tbl_files (uuid, filename, size, session_id)
        Server--)Browser: SSE Event "content_updated"
        Server-->>App: Return 200 OK {status: "success", file_uuid}
    else Text / OCR Upload
        App->>Server: POST /api/v1/texts {content, source: "OCR", session_uuid}
        Server->>DB: INSERT into tbl_texts (uuid, content, source, session_id)
        Server--)Browser: SSE Event "content_updated"
        Server-->>App: Return 200 OK {status: "success", text_uuid}
    end

    Note over App,Server: Unified History Retrieval
    App->>Server: POST /api/v1/uploaded {session_uuid}
    Server->>DB: SELECT from tbl_files UNION SELECT from tbl_texts ORDER BY created_at DESC
    Server-->>App: Single unified array of files & text items
```

---

## 4. Emulator & Local Network Topology

```mermaid
flowchart TD
    subgraph Host["Host Development Machine"]
        ServerApp["P2P Copier WebApp (Port 9004)"]
        MySQLApp["MySQL Container (Port 9306 / 3306)"]
    end

    subgraph AndroidClient["Android Client Devices"]
        Emulator["Android Emulator\nConnects to http://10.0.2.2:9004"]
        PhysicalUSB["Physical Device via USB ADB\nConnects to http://127.0.0.1:9004\n(via adb reverse tcp:9004 tcp:9004)"]
        PhysicalLAN["Physical Device via Wi-Fi\nConnects to http://192.168.x.x:9004"]
    end

    Emulator -->|10.0.2.2 Virtual Host NAT| ServerApp
    PhysicalUSB -->|ADB Reverse Tunnel| ServerApp
    PhysicalLAN -->|Local Network IP| ServerApp
    ServerApp --> MySQLApp
```
