# Making Changes Safely

This guide maps common engineering tasks to relevant codebase files and defines the Definition of Done (DoD) for cross-service contract changes.

---

## 1. Task Routing Table

| If you want to… | Touch these files / directories |
|---|---|
| **Add a new REST API endpoint** | `app/Config/Routes.php`<br>`app/Controllers/Api/v1/`<br>`docs/api/contract.md`<br>Android DTO / RetrofitInterface in `P2P_Copier_App` |
| **Add a web browser page or feature** | `app/Config/Routes.php`<br>`app/Controllers/Home.php`<br>`app/Views/` |
| **Modify or add database tables** | `app/Database/Migrations/`<br>`app/Models/`<br>`docs/architecture/data-and-storage.md` |
| **Change pairing authentication logic** | `app/Controllers/Api/v1/AuthApi.php`<br>`app/Filters/FilterDeviceAuth.php`<br>`docs/architecture/communication.md`<br>Android `AuthSession.java` |
| **Adjust real-time event streaming** | `app/Controllers/Api/Events.php`<br>`public/js/` web event listeners |
| **Alter file upload limits or validation** | `app/Controllers/Api/v1/FilesApi.php`<br>`Dockerfile` (`upload_max_filesize`) |

---

## 2. Definition of Done (Cross-Service Contract Changes)

When making changes that affect the REST API or database schema shared with the Android client:

- [ ] **Code Implemented**: Server-side logic completed with strict parameter validation.
- [ ] **Migrations Committed**: Safe, reversible database migrations with down methods.
- [ ] **API Contract Updated**: `docs/api/contract.md` updated with request/response schemas.
- [ ] **Polyrepo Sync**: Sibling Android models and `RetrofitInterface` updated in `P2P_Copier_App`.
- [ ] **Backward Compatibility**: Existing app versions supported or versioned at `/api/v2/`.
- [ ] **Documentation Linter**: Passed `python scripts/lint-docs.py .` with zero errors.
- [ ] **Tests Verified**: PHPUnit tests passing via `vendor/bin/phpunit`.
