# Deployment Path — Mac → Cousin's PC (repeatable)

## 🖥️ Part A — On your Mac (build & export)

| # | Step | Command / Action |
|---|------|------------------|
| 1 | Finish dev + test locally | `docker compose up -d --build` → test in browser |
| 2 | Build app image for **AMD64** | `docker buildx build --platform linux/amd64 -t milkyway-erp-app:latest --load .` |
| 3 | Build MySQL for AMD64 *(only first time)* | Pull via **explicit AMD64 digest** (see note below) |
| 4 | Dump the database | `docker exec milkyway_db mysqldump -u root -prootpass --databases milky_way --single-transaction --routines --triggers > ~/milkyway-deploy/milky_way_dump.sql` |
| 5 | Save app image | `docker save -o ~/milkyway-deploy/milkyway-erp-app.tar milkyway-erp-app:latest` |
| 6 | Save MySQL image *(only first time)* | `docker save -o ~/milkyway-deploy/mysql-8.0.tar mysql:8.0` |
| 7 | Copy folder to USB | `cp -R ~/milkyway-deploy /Volumes/USB/` |

> ⚠️ **Step 3 — MySQL AMD64:** `docker pull --platform linux/amd64 mysql:8.0` is unreliable on Apple Silicon (keeps ARM manifest cached). Use the explicit digest method:
> ```bash
> docker manifest inspect mysql:8.0 | python3 -c "import json,sys; d=json.load(sys.stdin); [print(m['digest']) for m in d['manifests'] if m['platform']['architecture']=='amd64']"
> docker rmi mysql:8.0
> docker pull mysql:8.0@sha256:<that-digest>
> docker tag mysql@sha256:<that-digest> mysql:8.0
> ```

---

## 📁 Part B — The `milkyway-deploy/` folder contents

| File | Purpose | Changes per deploy? |
|------|---------|---------------------|
| `milkyway-erp-app.tar` | App image | ✅ **Yes** every time |
| `mysql-8.0.tar` | MySQL image | ❌ No (first time only) |
| `milky_way_dump.sql` | Database data | ✅ **Yes** when data changes |
| `docker-compose.yml` | Container config | ❌ No (unless ports/volumes change) |
| `env.app` | CI4 `.env` (production) | ❌ No (unless IP/key changes) |
| `Lancer-ERP.bat` | Launcher | ❌ No (works) |
| `Arreter-ERP.bat` | Shutdown | ❌ No |
| `ssl/` (localhost.pem + key) | HTTPS certs | ❌ No (unless expired) |

---

## 💻 Part C — On the cousin's PC (deploy)

| # | Step | Command (in `cmd`) |
|---|------|--------------------|
| 1 | Copy folder from USB to `C:\Users\<user>\Downloads\milkyway-deploy` | Explorer |
| 2 | Stop everything + wipe DB volume | `docker compose down -v` |
| 3 | Remove old app image | `docker rmi milkyway-erp-app:latest` |
| 4 | Load new app image | `docker load -i milkyway-erp-app.tar` |
| 5 | *(first time only)* Load MySQL image | `docker load -i mysql-8.0.tar` |
| 6 | Start via launcher | Double-click **`Lancer-ERP.bat`** |
| 7 | Verify PC | Browser opens → login → test |
| 8 | Verify tablet | `https://192.168.1.143:8443/` on same Wi-Fi |

> 💡 **Step 2 (`down -v`)** wipes the MySQL volume so the fresh dump imports on next start. **Skip it** if data hasn't changed — just `docker compose down`.

---

## 🔁 Quick "update only" flow (no DB change)

When you only fixed UI / bugs:

```
Mac:    buildx build --platform linux/amd64 → docker save
USB:    copy milkyway-erp-app.tar
Cousin: docker compose down → docker rmi app → docker load -i app.tar → Lancer-ERP.bat
```

## 🔁 Quick "update with data" flow

When you also added/changed data:

```
Mac:    dump DB → buildx build → docker save
USB:    copy milkyway-erp-app.tar + milky_way_dump.sql
Cousin: docker compose down -v → docker rmi app → docker load -i app.tar → Lancer-ERP.bat
```

---

## ⚠️ Things to remember (gotchas you already hit)

| Gotcha | Lesson |
|--------|--------|
| ARM64 images on x64 PC | Always build with `--platform linux/amd64` |
| `docker pull --platform` unreliable | Use explicit `@sha256:<amd64-digest>` for MySQL |
| `.bat` corrupted by Mac→Windows | Recreate `.bat` directly on Windows with `notepad` |
| Missing SSL certs → container crash | Keep `ssl/` folder in deploy bundle, mount it in compose |
| Wrong folder path | `.bat` uses `%~dp0` so it works from any location |
| IP changed → tablet breaks | Static IP `192.168.1.143` set manually in Windows |
| Firewall blocks tablet | `netsh advfirewall firewall add rule name="MilkyWay" dir=in action=allow protocol=TCP localport=8443,8080` |

---

**Save this list — it's your reusable deployment checklist.** Print it or keep it in a `.md` file for next time.