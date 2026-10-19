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
| `s3.endpoint` | WebApp / S3 | Yes | `http://minio:9000` | Internal MinIO/S3 API endpoint for container communication. |
| `s3.public_endpoint` | WebApp / S3 | Yes | `http://localhost:9000` | Publicly reachable MinIO/S3 endpoint for presigned direct download URLs. |
| `s3.access_key` | WebApp / S3 | Yes | `minio_admin` | S3 Access Key. |
| `s3.secret_key` | WebApp / S3 | Yes | `minio_secure_password` | S3 Secret Key. |
| `s3.bucket` | WebApp / S3 | Yes | `p2p-copier-files` | S3 target bucket name. |
| `encryption.key` | WebApp | No | Empty | Application master encryption key. Generate via `php spark key:generate`. |

---

## Docker Compose Port Mappings

Standard production ports for cloud deployment (GCP / Docker):

| Container Service | Default Host Port | Container Port | Purpose |
|---|---|---|---|
| `p2p-copier-webapp` | `80` | `80` | Web application & REST API |
| `shared-minio` | `9000` | `9000` | MinIO S3 API |
| `shared-minio` | `9001` | `9001` | MinIO Web Console UI |
| `shared-mysql` | `3306` | `3306` | MySQL database host port |
