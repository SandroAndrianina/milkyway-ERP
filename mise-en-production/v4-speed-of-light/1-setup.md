Your project already exists, so Setup is 6 small steps.

**Setup to-do list**

1. **Add the packages** (in `milkyway_mobile/`):
   ```bash
   flutter pub add sqflite path flutter_secure_storage http uuid
   ```

2. **Android config**
   - In `android/app/build.gradle(.kts)`: set `minSdk = 23` (required by `flutter_secure_storage`).
   - In `android/app/src/main/AndroidManifest.xml`, before `<application>`:
     ```xml
     <uses-permission android:name="android.permission.INTERNET"/>
     ```

3. **Create the config file** `lib/config.dart`:
   ```dart
   const String serverHost = '192.168.1.100'; // your cousin's PC IP
   const String baseUrl = 'https://$serverHost';
   ```

4. **Create the HTTP client** `lib/services/api_client.dart`:
   ```dart
   import 'dart:io';
   import 'package:http/http.dart' as http;
   import 'package:http/io_client.dart';
   import '../config.dart';

   http.Client buildClient() {
     final hc = HttpClient()
       ..badCertificateCallback = (cert, host, port) => host == serverHost;
     return IOClient(hc);
   }
   ```
   Only that IP is accepted, every other certificate stays rejected.

5. **Quick test** in `main.dart`: call `buildClient().get(Uri.parse('$baseUrl/api/health'))` and print the status code.

6. **Run on a real phone** connected to the same WiFi as the PC: `flutter run`.

**Step is done when:** `/api/health` returns 200 on the phone.

If it fails: check the Windows Firewall (port 443) and that the certificate was generated for that exact IP.

Your message is in English, so there is no French to correct this time.