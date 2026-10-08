# Local Development Setup (Native)

This guide is for software engineers developing on CodeIgniter 4 natively without Docker.

---

## 1. System Requirements

- **PHP**: 8.3 or higher
  - Required Extensions: `php8.3-curl`, `php8.3-intl`, `php8.3-mbstring`, `php8.3-mysql`, `php8.3-xml`, `php8.3-gd`
- **Composer**: 2.6+
- **MySQL**: 8.4+

---

## 2. Installation Steps

1. Install Composer dependencies:
   ```bash
   composer install
   ```
2. Copy environment file:
   ```bash
   cp .env.example .env
   ```
3. Update `.env` with your local database credentials:
   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = db_p2p_copier
   database.default.username = root
   database.default.password = your_local_password
   ```
4. Run migrations:
   ```bash
   php spark migrate --all
   ```
5. Ensure permissions:
   ```bash
   chmod -R 775 writable/
   ```
6. Start the local server:
   ```bash
   php spark serve --host 0.0.0.0 --port 8080
   ```

---

## 3. Useful Spark CLI Commands

```bash
# List all available spark commands
php spark list

# List all registered application routes
php spark routes

# Check migration status
php spark migrate:status

# Clear cached views and logs
php spark cache:clear
```
