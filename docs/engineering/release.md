# Release Train & Version Compatibility Matrix

This document defines the release cycle, compatibility matrix, and deprecation policies across the P2P Copier polyrepo ecosystem.

---

## 1. Ecosystem Version Matrix

| Component | Repository | Current Version | Compatibility Target | Notes |
|---|---|---|---|---|
| **Web Relay Application** | `P2P Copier WebApp` | `v1.1.0` | API `/api/v1/` | PHP 8.3 / CodeIgniter 4 |
| **Android Mobile Client** | `P2P_Copier_App` | `v1.1` (Build 2) | API `/api/v1/` | SDK 24–35 / Kotlin 1.9 |
| **Database Schema** | `db_p2p_copier` | Migration `2026-08-14-100012` | WebApp `v1.1.0` | MySQL 8.4 InnoDB |

---

## 2. Compatibility Guarantees

1. **Forward Compatibility**:
   - The Android client ignores unexpected fields in JSON responses via Gson's lenient parsing.
   - The WebApp server accepts extra telemetry keys without failing requests.
2. **Breaking Change Protocol**:
   - Any modification that removes or renames a field in `docs/api/contract.md` constitutes a breaking change.
   - Breaking changes require bumping the API prefix to `/api/v2/`.
   - The backend must maintain the legacy `/api/v1/` routes until all active Android client releases have transitioned.

---

## 3. Release Checklist

Before tagging a production release:

1. [ ] **Migrations Tested**: Forward and rollback migrations verified via `php spark migrate --all` and `php spark migrate:rollback`.
2. [ ] **Automated Tests Pass**: All PHPUnit test suites execute cleanly.
3. [ ] **Contract Validated**: OpenAPI 3.0 specification (`docs/api/openapi.yaml`) matches active controller routes.
4. [ ] **Doc Linter Verified**: `python scripts/lint-docs.py .` passes with zero errors and warnings.
5. [ ] **Version Tagged**: Git tag pushed (`git tag -a v1.1.0 -m "Release v1.1.0"`).
