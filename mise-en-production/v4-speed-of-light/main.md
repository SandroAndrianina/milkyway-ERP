**To-do list: finish today (mobile + LAN sync)**

1. **Setup**: Flutter project, `sqflite`, `flutter_secure_storage`, `http`. Allow the cousin's PC certificate (`badCertificateCallback`, that IP only).
2. **Login**: `POST /api/auth/login`, store the JWT securely.
3. **Local database**: catalog tables (products, prices, clients) + `movements` table (client-side UUID, status `pending`/`sent`).
4. **Pull**: `GET /api/sync/pull?since=...`, fill the local catalog, save `server_time` for the next call.
5. **Offline sale screen**: read the local catalog, create a movement (never edit the catalog).
6. **Push**: send pending movements, mark them `sent` only after server confirmation.
7. **Sync logic**: check `GET /api/health` (2s timeout), then push first, pull second.
8. **Release APK**: `flutter build apk --release`, install on a real phone.
9. **Real test**: one sale offline, then sync on the office WiFi, then check it in the admin.

**Later:** Supabase remote mode, backup to Google Drive, handover documentation.