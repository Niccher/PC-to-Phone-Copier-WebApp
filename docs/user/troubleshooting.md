# Operator & User Troubleshooting

Solutions for common operational and runtime issues when launching P2P Copier WebApp.

---

## 1. Port Already Allocated (`listen tcp 0.0.0.0:80: bind: address already in use`)

**Cause**: Another process on your machine (e.g. system Apache or Nginx) is using port 80, 9000, 9001, or 3306.

**Remediation**:
1. Check what is using the port:
   ```bash
   sudo lsof -i :80
   ```
2. Either stop the competing process, or edit `docker-compose.yml` to adjust host ports if needed:
   ```yaml
   ports:
     - "8080:80"  # For local desktop testing if port 80 is occupied
   ```
3. Update `app.baseURL` in `.env` to match the active host port (`http://localhost:8080/`).

---

## 2. Database Connection Refused on First Launch

**Cause**: MySQL 8.4 takes 15–25 seconds on its initial launch to create tablespace files and initialize database catalogs.

**Remediation**:
- `entrypoint.sh` automatically waits up to 60 seconds with PDO retries.
- Check MySQL initialization logs:
   ```bash
   docker compose logs -f mysql
   ```
- Once MySQL logs indicate `mysqld: ready for connections`, the webapp container will complete migrations automatically.

---

## 3. Directory Permission Denied on `writable/`

**Cause**: The Apache web server inside the container runs as `www-data` and needs write access to upload files, session locks, and logs.

**Remediation**:
On the host machine, grant read/write access:
```bash
chmod -R 777 writable/
```

---

## 4. Android Client Cannot Connect to WebApp

**Cause**: The Android device or emulator cannot reach the backend server over the network.

**Remediation**:
- **Android Emulator**: In the Android app settings, set the backend address to `http://10.0.2.2/`. Android emulators use `10.0.2.2` as an alias for the host development machine on standard port 80.
- **Physical Device via USB**: Forward the port with ADB:
   ```bash
   adb reverse tcp:80 tcp:80
   ```
   Then configure the app to connect to `http://127.0.0.1/`.
- **Physical Device over Wi-Fi / Cloud GCP**: Determine your GCP public IP or host local IP (`ip -br a` or `ifconfig`), e.g. `http://34.x.x.x/` or `http://192.168.1.50/`, and configure this URL in the Android app's Backend Configuration screen.
