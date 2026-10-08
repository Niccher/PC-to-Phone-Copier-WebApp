# Engineering Documentation

Welcome to the internal engineering documentation for **P2P Copier WebApp** (CodeIgniter 4 backend relay).

## Documentation Navigation

| I want to… | Go here |
|---|---|
| **Run the system without coding** | [../README.md](../README.md) |
| **Understand system architecture & C4 model** | [architecture/overview.md](architecture/overview.md) |
| **See how Web, Android, and Server communicate** | [architecture/communication.md](architecture/communication.md) |
| **Inspect MySQL schemas and storage layers** | [architecture/data-and-storage.md](architecture/data-and-storage.md) |
| **Review deployment & container topology** | [architecture/deployment.md](architecture/deployment.md) |
| **Review Threat Model & LAN security analysis** | [architecture/threat-model.md](architecture/threat-model.md) |
| **Inspect REST API v1 contract & payloads** | [api/contract.md](api/contract.md) |
| **Inspect OpenAPI 3.0 YAML specification** | [api/openapi.yaml](api/openapi.yaml) |
| **Work on CodeIgniter 4 backend** | [services/codeigniter.md](services/codeigniter.md) |
| **Set up a native development environment** | [engineering/local-development.md](engineering/local-development.md) |
| **Change behavior safely (Task routing & DoD)** | [engineering/making-changes.md](engineering/making-changes.md) |
| **Manage database migrations & seeders** | [engineering/database.md](engineering/database.md) |
| **Run test suites (PHPUnit)** | [engineering/testing.md](engineering/testing.md) |
| **Continuous integration & CI workflows** | [engineering/ci.md](engineering/ci.md) |
| **Version matrix & breaking-change policies** | [engineering/release.md](engineering/release.md) |
| **Security, session boundaries & auth filters** | [engineering/security.md](engineering/security.md) |
| **Review engineering troubleshooting & logs** | [engineering/troubleshooting.md](engineering/troubleshooting.md) |
| **Contributing & Pull Request guidelines** | [engineering/contributing.md](engineering/contributing.md) |
| **Runbook: Service restart & recovery** | [runbooks/restart.md](runbooks/restart.md) |
| **Runbook: Backup & disaster recovery** | [runbooks/backup-and-restore.md](runbooks/backup-and-restore.md) |
| **ADR-0001: Hybrid QR & Numeric Pairing** | [adr/0001-hybrid-qr-numeric-pairing.md](adr/0001-hybrid-qr-numeric-pairing.md) |
| **ADR-0002: Unified Feed Aggregation** | [adr/0002-unified-feed-aggregation.md](adr/0002-unified-feed-aggregation.md) |
| **ADR-0003: On-Device ML Kit OCR Processing** | [adr/0003-on-device-ocr-via-ml-kit.md](adr/0003-on-device-ocr-via-ml-kit.md) |

## Polyrepo Ecosystem Links

| Component | Responsibility | Repository URL | Documentation |
|---|---|---|---|
| **Android Client** | Kotlin/Java Android Client | [P2P_Copier_App](https://github.com/niccher/PC-to-Phone-Copier-App) | [docs/](https://github.com/niccher/PC-to-Phone-Copier-App/tree/main/docs) |
| **Backend Relay** | CodeIgniter 4 Web UI, API & Relay | [P2P Copier WebApp](https://github.com/niccher/P2P_Copier_WebApp) | [docs/](README.md) |
