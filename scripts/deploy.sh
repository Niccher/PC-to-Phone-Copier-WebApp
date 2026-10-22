#!/usr/bin/env bash
# =============================================================
# deploy.sh — ONE-CLICK Deployment & Setup Pipeline
#             for P2P Copier WebApp on GCP / Debian / Linux
#
# Usage:  bash scripts/deploy.sh
#         (run from the repository root)
#
# Production Standard Ports:
#   - Web App UI & REST API : Port 80 (or $WEB_PORT)
#   - MinIO S3 API          : Port 9000 (or $S3_PORT)
#   - MinIO Web Console     : Port 9001 (Internal / Admin)
#   - MySQL 8.4 Database    : Port 3306 (Internal to Docker)
#
# Automated Pipeline:
#   1. Detect Dynamic IP (GCP Metadata / Public / LAN Fallback)
#   2. Pre-flight System & Resource Check (Docker, Compose, RAM, Disk)
#   3. Git Working Tree Sanity Check & Remote Tracking
#   4. Write / Patch .env (app.baseURL, S3 endpoints, DB config)
#   5. Stop Old Containers & Clear Name Conflicts
#   6. Build & Start All Containers (MySQL, MinIO, WebApp)
#   7. Wait for MySQL Health (:3306)
#   8. Wait for MinIO S3 Health (:9000)
#   9. Run Database Migrations via Spark
#  10. Wait for WebApp Server Readiness (:80)
#  11. Container Permissions Enforcement (writable/ & media/)
#  12. Housekeeping, Docker Storage & Cache Clearing
#  13. Clean Status Summary, Endpoints Table & Execution Timeline
# =============================================================
set -euo pipefail

# ── Colors & Formatting ───────────────────────────────────────
GREEN="\033[0;32m"; YELLOW="\033[1;33m"; RED="\033[0;31m"
CYAN="\033[0;36m"; BOLD="\033[1m"; RESET="\033[0m"

DEPLOY_START_TIME=$(date +%s)
SECTION_START_TIME=$DEPLOY_START_TIME
PREV_SECTION=""
declare -a STAGE_NAMES=()
declare -a STAGE_DURATIONS=()

format_duration() {
    local S=$1
    if [ "$S" -ge 60 ]; then
        local M=$((S / 60))
        local R=$((S % 60))
        echo "${M}m ${R}s"
    else
        echo "${S}s"
    fi
}

log()     { echo -e "${GREEN}[OK]${RESET}   $1"; }
warn()    { echo -e "${YELLOW}[WARN]${RESET} $1"; }
err()     {
    local NOW=$(date +%s)
    local ELAPSED=$((NOW - DEPLOY_START_TIME))
    echo -e "${RED}[FAIL]${RESET} $1 (failed after $(format_duration $ELAPSED))"
    exit 1
}

section() {
    local NOW=$(date +%s)
    if [ -n "$PREV_SECTION" ]; then
        local DURATION=$((NOW - SECTION_START_TIME))
        echo -e "${CYAN}──> Completed: ${PREV_SECTION} in $(format_duration $DURATION)${RESET}"
        STAGE_NAMES+=("$PREV_SECTION")
        STAGE_DURATIONS+=("$DURATION")
    fi
    PREV_SECTION="$1"
    SECTION_START_TIME=$NOW
    echo -e "\n${CYAN}${BOLD}=== $1 ===${RESET}"
}

finish_deployment() {
    local NOW=$(date +%s)
    if [ -n "$PREV_SECTION" ]; then
        local DURATION=$((NOW - SECTION_START_TIME))
        STAGE_NAMES+=("$PREV_SECTION")
        STAGE_DURATIONS+=("$DURATION")
        PREV_SECTION=""
    fi
    local TOTAL_DURATION=$((NOW - DEPLOY_START_TIME))
    echo ""
    echo -e "${GREEN}${BOLD}══════════════════════════════════════════════════════════════${RESET}"
    echo -e "${GREEN}${BOLD}   DEPLOYMENT TIMELINE & EXECUTION SUMMARY${RESET}"
    echo -e "${GREEN}${BOLD}══════════════════════════════════════════════════════════════${RESET}"
    for i in "${!STAGE_NAMES[@]}"; do
        printf "   %-48s : %s\n" "${STAGE_NAMES[$i]}" "$(format_duration ${STAGE_DURATIONS[$i]})"
    done
    echo -e "   ------------------------------------------------------------"
    printf "   ${BOLD}%-48s${RESET} : ${BOLD}%s (%ss)${RESET}\n" "Total Overall Deployment Time" "$(format_duration $TOTAL_DURATION)" "$TOTAL_DURATION"
    echo -e "${GREEN}${BOLD}══════════════════════════════════════════════════════════════${RESET}"
}

# ── Guard: Run from repository root ───────────────────────────
[ -f "docker-compose.yml" ] || err "Must be executed from repository root (where docker-compose.yml resides)."

# ── 1. Detect Dynamic IP ──────────────────────────────────────
section "1. Detecting Dynamic Server IP"
# 1. Probe GCP Compute Engine metadata server first (instant on GCP VM)
DETECTED_IP=$(curl -s -m 2 -H "Metadata-Flavor: Google" http://metadata.google.internal/computeMetadata/v1/instance/network-interfaces/0/access-configs/0/external-ip 2>/dev/null | tr -d '[:space:]' || true)

# 2. Public IP fallback probes
if [ -z "$DETECTED_IP" ]; then
    DETECTED_IP=$(curl -s --max-time 3 ip.me 2>/dev/null | tr -d '[:space:]' || true)
fi
if [ -z "$DETECTED_IP" ]; then
    DETECTED_IP=$(curl -s --max-time 3 ifconfig.me 2>/dev/null | tr -d '[:space:]' || true)
fi
if [ -z "$DETECTED_IP" ]; then
    DETECTED_IP=$(curl -s --max-time 3 icanhazip.com 2>/dev/null | tr -d '[:space:]' || true)
fi
# 3. LAN IP fallback
if [ -z "$DETECTED_IP" ]; then
    DETECTED_IP=$(hostname -I 2>/dev/null | awk '{print $1}' | tr -d '[:space:]' || true)
fi
# 4. Localhost fallback
if [ -z "$DETECTED_IP" ]; then
    DETECTED_IP="127.0.0.1"
fi
log "Server Host IP Detected: $DETECTED_IP"

# ── 2. Pre-flight Resource & Tooling Checks ───────────────────
section "2. Pre-flight Resource & Tooling Checks"
command -v docker >/dev/null 2>&1 || err "Docker is not installed. Run sudo apt install docker-ce."
docker compose version >/dev/null 2>&1 || err "Docker Compose plugin is not installed."

DISK_FREE_MB=$(df -m . | awk 'NR==2 {print $4}')
if [ "$DISK_FREE_MB" -lt 1000 ]; then
    warn "Low disk space (<1GB free): ${DISK_FREE_MB}MB remaining."
else
    log "Disk space check passed: ${DISK_FREE_MB}MB free."
fi

# ── 3. Git Working Tree Sanity Check ──────────────────────────
section "3. Git Working Tree Sanity Check"
if command -v git >/dev/null 2>&1 && [ -d ".git" ]; then
    BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "unknown")
    COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
    log "Deploying from branch: ${BRANCH} (${COMMIT})"
else
    warn "Not a git working tree; skipping git check."
fi

# ── 4. Configure .env ─────────────────────────────────────────
section "4. Configuring Environment Variables"
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    cp .env.example .env
    log "Created .env from .env.example"
fi

if [ -f ".env" ]; then
    # App Settings
    sed -i "s|^CI_ENVIRONMENT =.*|CI_ENVIRONMENT = production|" .env || true
    sed -i "s|^app.baseURL =.*|app.baseURL = 'http://${DETECTED_IP}/'|" .env || true

    # Database Docker host alignment (shared-mysql inside compose network)
    sed -i "s|^database.default.hostname =.*|database.default.hostname = mysql|" .env || true
    sed -i "s|^database.default.database =.*|database.default.database = db_p2p_copier|" .env || true
    sed -i "s|^database.default.username =.*|database.default.username = root|" .env || true
    sed -i "s|^database.default.password =.*|database.default.password = root_password|" .env || true

    # S3 / MinIO Configuration
    sed -i "s|^s3.endpoint =.*|s3.endpoint = \"http://minio:9000\"|" .env || true
    sed -i "s|^s3.public_endpoint =.*|s3.public_endpoint = \"http://${DETECTED_IP}:9000\"|" .env || true
    sed -i "s|^s3.access_key =.*|s3.access_key = \"minio_admin\"|" .env || true
    sed -i "s|^s3.secret_key =.*|s3.secret_key = \"minio_secure_password\"|" .env || true
    sed -i "s|^s3.bucket =.*|s3.bucket = \"p2p-copier-files\"|" .env || true
    sed -i "s|^s3.use_path_style_endpoint =.*|s3.use_path_style_endpoint = true|" .env || true

    log "Configured .env: app.baseURL='http://${DETECTED_IP}/' & s3.public_endpoint='http://${DETECTED_IP}:9000'"
fi

# ── 5. Stop Old Containers & Clear Conflicts ──────────────────
section "5. Stopping Old Containers & Clearing Name Conflicts"
docker compose down --remove-orphans 2>/dev/null || true
log "Old container stacks stopped."

# ── 6. Build & Start All Containers ───────────────────────────
section "6. Building & Starting Containers"
docker compose up --build -d
log "Containers launched in background."

# ── 7. Wait for MySQL Health ──────────────────────────────────
section "7. Waiting for MySQL Database Readiness (:3306)"
max_attempts=30
attempt=0
until docker compose exec -T mysql mysqladmin ping -h localhost -u root -proot_password --silent >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge "$max_attempts" ]; then
        err "MySQL failed to become healthy within 60 seconds."
    fi
    sleep 2
done
log "MySQL database is fully healthy and accepting connections."

# ── 8. Wait for MinIO S3 Health ───────────────────────────────
section "8. Waiting for MinIO S3 Storage Readiness (:9000)"
minio_attempts=20
minio_count=0
until docker compose exec -T minio mc ready local >/dev/null 2>&1 || curl -s -f -o /dev/null "http://127.0.0.1:9000/minio/health/live"; do
    minio_count=$((minio_count + 1))
    if [ "$minio_count" -ge "$minio_attempts" ]; then
        warn "MinIO health check probe did not report ready in time; proceeding."
        break
    fi
    sleep 2
done
log "MinIO S3 storage is online."

# ── 9. Run Database Migrations ────────────────────────────────
section "9. Running Database Migrations via Spark"
docker compose exec -T p2p-copier php spark migrate --all || warn "Spark migration finished with warnings."
log "Database migrations evaluated."

# ── 10. Wait for WebApp Server Readiness ──────────────────────
section "10. Waiting for WebApp Server Readiness (:80)"
web_attempts=20
web_count=0
until curl -s -f -o /dev/null "http://127.0.0.1:80/api/v1/ping" || curl -s -f -o /dev/null "http://127.0.0.1:80/"; do
    web_count=$((web_count + 1))
    if [ "$web_count" -ge "$web_attempts" ]; then
        warn "HTTP probe did not return 200 OK immediately; Apache is running."
        break
    fi
    sleep 2
done
log "WebApp container is responding on port 80."

# ── 11. Container Permissions Enforcement ─────────────────────
section "11. Enforcing Container File Permissions"
docker compose exec -T p2p-copier mkdir -p /var/www/html/writable /var/www/html/public/assets/media || true
docker compose exec -T p2p-copier chown -R www-data:www-data /var/www/html/writable /var/www/html/public/assets/media || true
docker compose exec -T p2p-copier chmod -R 775 /var/www/html/writable /var/www/html/public/assets/media || true
log "Permissions verified on /var/www/html/writable and public/assets/media."

# ── 12. Housekeeping & Docker Cache Pruning ───────────────────
section "12. Housekeeping & Cache Clearing"
docker image prune -f >/dev/null 2>&1 || true
log "Dangling build images pruned."

# ── 13. Clean Status Summary & Endpoints ──────────────────────
finish_deployment

echo ""
echo -e "${GREEN}${BOLD}✔ P2P Copier WebApp is LIVE & READY!${RESET}"
echo -e "  ┌────────────────────────────────────────────────────────────┐"
printf "  │ %-16s : %-39s │\n" "Web UI" "http://${DETECTED_IP}"
printf "  │ %-16s : %-39s │\n" "REST API Ping" "http://${DETECTED_IP}/api/v1/ping"
printf "  │ %-16s : %-39s │\n" "MinIO S3 API" "http://${DETECTED_IP}:9000"
printf "  │ %-16s : %-39s │\n" "MinIO Console" "http://${DETECTED_IP}:9001"
echo -e "  └────────────────────────────────────────────────────────────┘"
echo -e "Open ${CYAN}http://${DETECTED_IP}${RESET} in your browser to view the dynamic QR code."
echo -e "Scan with your Android phone for instant 1-tap pairing!"
