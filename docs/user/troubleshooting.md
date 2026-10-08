# Operator & User Troubleshooting

Solutions for common operational and runtime issues when launching P2P Copier WebApp.

---

## 1. Port Already Allocated (`listen tcp 0.0.0.0:9004: bind: address already in use`)

**Cause**: Another process on your machine is using port 9004, 9000, or 9306.

**Remediation**:
1. Check what is using the port:
   ```bash
   sudo lsof -i :9004
   ```
2. Either stop the competing process, or edit `docker-compose.yml` to change the host port:
   ```yaml
   ports:
     - "9104:80"  # Changes host port from 9004 to 9104
   ```
3. Update `app.baseURL` in `.env` to match the new port (`http://localhost:9104/`).

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
- **Android Emulator**: In the Android app settings, set the backend address to `http://10.0.2.2:9004/`. Android emulators use `10.0.2.2` as an alias for the host development machine.
- **Physical Device via USB**: Forward the port with ADB:
  ```bash
  adb reverse tcp:9004 tcp:9004
  ```
  Then configure the app to connect to `http://127.0.0.1:9004/`.
- **Physical Device over Wi-Fi**: Determine your host's local IP (`ip -br a` or `ifconfig`), e.g. `http://192.168.1.50:9004/`, and configure this URL in the Android app's Backend Configuration screen.
