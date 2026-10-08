# User Configuration Guide

This document lists all environment variables that users and operators can configure in `.env` and `docker-compose.yml`.

---

## Environment Variables Reference

| Variable | Service | Required | Default | Purpose |
|---|---|---|---|---|
| `CI_ENVIRONMENT` | WebApp | No | `development` | Set to `development` for verbose debug bars or `production` for production deployments. |
| `app.baseURL` | WebApp | Yes | `http://localhost:9004/` | The public URL through which the browser and Android app reach the server. |
| `app.forceGlobalSecureRequests` | WebApp | No | `false` | When `true`, forces HTTPS redirect across all routes. |
| `database.default.hostname` | WebApp / DB | Yes | `mysql` | Hostname of the MySQL server (`mysql` inside Docker Compose, or `localhost` natively). |
| `database.default.database` | WebApp / DB | Yes | `db_p2p_copier` | Name of the relational database. |
| `database.default.username` | WebApp / DB | Yes | `root` | Database user account. |
| `database.default.password` | WebApp / DB | Yes | `root_password` | Database password. |
| `database.default.DBDriver` | WebApp / DB | Yes | `MySQLi` | Database driver (MySQLi). |
| `database.default.port` | WebApp / DB | Yes | `3306` | Internal MySQL connection port. |
| `encryption.key` | WebApp | No | Empty | Application master encryption key. Generate via `php spark key:generate`. |

---

## Docker Compose Port Mappings

If default ports collide with services already running on your host machine, modify `docker-compose.yml`:

| Container Service | Default Host Port | Container Port | Purpose |
|---|---|---|---|
| `p2p-copier-webapp` | `9004` | `80` | Web application & REST API |
| `shared-phpmyadmin` | `9000` | `80` | phpMyAdmin web management UI |
| `shared-mysql` | `9306` | `3306` | MySQL server host port |
