# Docker Deployment — Main Steps

| # | Step | Where |
|---|------|-------|
| **1** | **Prepare Docker Desktop offline installer** (`.exe`) + WSL2 package | Download on your PC → USB |
| **2** | **Install Docker Desktop** on cousin's PC (offline, admin rights) | Cousin PC |
| **3** | **Build & save images**: `docker save` app + mysql into `.tar` | Your PC |
| **4** | **Export DB dump**: `mysqldump milky_way > dump.sql` | Your PC |
| **5** | **Create `docker-compose.yml`** with `restart: unless-stopped` and volume for MySQL | Your PC |
| **6** | **Write `Lancer-ERP.bat`**: load images, `docker compose up -d`, open browser | Your PC |
| **7** | **Find cousin PC local IP** (`ipconfig`) + open firewall ports 8080/8443 | Cousin PC |
| **8** | **Test from tablet**: `https://<PC_IP>:8443` | Tablet |

**→ Start with Step 1.** Say "go" and I'll give you the exact commands for that step.