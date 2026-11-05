# Path after debug + redesign

| # | Step | Where |
|---|------|-------|
| 1 | **Test everything locally** (PC browser + tablet) on your Mac | Mac |
| 2 | **Rebuild app image for AMD64** | Mac |
| 3 | **Dump DB with real data** | Mac |
| 4 | **Save new `.tar`** (app only) | Mac |
| 5 | **Copy to USB**: `milkyway-erp-app.tar` + `milky_way_dump.sql` | Mac |
| 6 | **On cousin's PC**: replace `.tar` + `.sql` | Cousin |
| 7 | **Run**: `docker compose down -v` | Cousin |
| 8 | **Reload image**: `docker rmi milkyway-erp-app:latest` + `docker load -i milkyway-erp-app.tar` | Cousin |
| 9 | **Restart**: `Lancer-ERP.bat` | Cousin |
| 10 | **Verify**: login + check data + tablet test | Cousin |

**Do NOT resend** `mysql-8.0.tar` (unchanged) or the `.bat` (already works).