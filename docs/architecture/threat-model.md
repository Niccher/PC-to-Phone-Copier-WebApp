# Threat Model & LAN Security Hardening

This document provides a threat model analysis (STRIDE) and security hardening guidelines for the P2P Copier ecosystem operating across local Wi-Fi and untrusted networks.

---

## 1. Threat Analysis (STRIDE Model)

| Threat Category | Attack Vector | Risk | Mitigation in Codebase |
|---|---|---|---|
| **Spoofing** | Attacker impersonates an authorized device to query session files. | Medium | Requests require valid `dev_uuid` cross-referenced with `tbl_paired_sessions`. |
| **Tampering** | Man-in-the-Middle (MitM) alters files or text in transit over unencrypted Wi-Fi. | High | Local testing uses HTTP, but production staging enforces HTTPS / TLS 1.3. Android app verifies certificate pinning in production. |
| **Repudiation** | Attacker claims unauthorized file upload or deletion. | Low | All uploads, soft-deletes, and events logged with IP and timestamp in `tbl_security_audit` and `tbl_visitors`. |
| **Information Disclosure** | An unauthorized third party brute-forces the 6-digit PIN on the local network. | High | Codes expire after 10 minutes; failed attempts trigger exponential backoff and rate-limiting. |
| **Denial of Service** | Flooding multipart file upload endpoint to exhaust server disk or RAM. | High | File size caps enforced in `FilesApi.php` (default 50MB) and web server request body restrictions. |
| **Elevation of Privilege** | Insecure Direct Object Reference (IDOR) to access files from another session. | Critical | All database lookups strictly scoped by `session_uuid` using `groupStart()` and `groupEnd()` operators. |

---

## 2. Pairing PIN Brute-Force Hardening

A 6-digit numeric pairing code provides \(10^6 = 1,000,000\) combinations. On a fast local LAN, an automated script could attempt hundreds of requests per second if unthrottled.

### Defense Mechanisms:
1. **Short Expiration Window**: Numeric codes in `tbl_pairing_codes` are invalidated after 10 minutes or upon successful pairing.
2. **Rate Limiting**: `AuthApi::pair` tracks failed attempts per IP address; after 5 consecutive failures, a 60-second cooldown is enforced.
3. **Audit Logging**: Every validation failure is written to `tbl_security_audit` with client IP and device fingerprint.

---

## 3. IDOR & Query Scoping Verification

To prevent cross-tenant data leaks when multiple browser tabs or devices use the relay server concurrently:
- File downloads (`/saved/download/:uuid` and `/api/v1/files/download`) query both `uuid` AND `session_id`.
- Soft-deletion endpoints require session ownership validation before writing to `tbl_files_trash`.
