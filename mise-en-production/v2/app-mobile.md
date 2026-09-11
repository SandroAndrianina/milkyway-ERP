## Mini to-do — Étape 7 (mise à jour)

### OK 1. Régénère le certificat mkcert avec l'IP de ton Mac
```bash
ipconfig getifaddr en0
```
Note cette IP (ex: `192.168.1.42`), puis :
```bash
mkcert -key-file ssl/localhost-key.pem -cert-file ssl/localhost.pem localhost 127.0.0.1 192.168.1.191
docker compose restart app
```
Teste depuis un navigateur du téléphone (même wifi) : `https://192.168.1.42:8443` — le cadenas doit apparaître aussi côté mobile.

### OK 2. Crée le projet Flutter
```bash
flutter create milkyway_mobile
cd milkyway_mobile
flutter pub get
```
Dépendances (`pubspec.yaml`) : `sqflite`, `path_provider`, `uuid`, `connectivity_plus`, `http`, `flutter_secure_storage`.

### 3. Schéma base locale (sqflite)
Tables : `produits` (cache lecture seule), `clients` (cache lecture seule), `mouvements` (uuid, product_id, quantite, type, synced, created_at), `meta` (`derniere_sync_lan`).

### 4. Endpoint `/api/health` côté CI4
```php
$routes->get('api/health', 'Api\HealthController::index');
```
Sans filtre JWT (juste un test de présence).

### 5. UI mobile — écrans à construire (Dart/Flutter, indépendants du site web)
Adapte-les pour fonctionner en responsive Flutter natif (smartphone **et** tablette, via `LayoutBuilder`/`MediaQuery`) :
- **Écran login** : formulaire login/mot de passe → appelle `/api/auth/login`, stocke le JWT via `flutter_secure_storage`
- **Écran catalogue produits** : liste en lecture seule (cache local), recherche/filtre
- **Écran nouvelle vente** : sélection produit + quantité + client → génère un `mouvement` avec UUID, écrit en local
- **Écran nouveau mouvement stock** (rôle stocks) : entrée/sortie non-conforme, même logique UUID
- **Écran historique/en attente** : liste des mouvements locaux avec statut (`synced` ou non) — utile pour que le livreur voie ce qui reste à envoyer
- **Bouton "Synchroniser"** visible sur l'écran principal (en plus de l'auto-sync)
- Layout : sur tablette (largeur > ~600dp), passe en vue 2 colonnes (liste + détail côte à côte) ; sur smartphone, vue simple empilée

### 6. `SyncService` (logique déjà posée)
```dart
Future<void> synchroniser() async {
  final lanOk = await _testerLan(); // GET /api/health, timeout 2s
  if (lanOk) { await _pushVersPc(); await _pullDepuisPc(); return; }
  if (await Connectivity().checkConnectivity() != ConnectivityResult.none) {
    await _pushVersSupabase();
  }
}
```

---

## Transférer l'app sur smartphone/tablette Android — étape par étape

### Pendant le développement (rechargement à chaud, pratique pour tester vite)
1. Sur le téléphone/tablette : **Paramètres → À propos du téléphone → taper 7 fois sur "Numéro de build"** → active le mode développeur
2. **Paramètres → Options pour développeurs → active "Débogage USB"**
3. Connecte l'appareil au Mac en USB, accepte la popup "Autoriser le débogage USB ?" sur l'écran du téléphone
4. Vérifie que Flutter le détecte :
   ```bash
   flutter devices
   ```
5. Lance directement dessus :
   ```bash
   flutter run -d <device_id>
   ```
   L'app s'installe et se lance automatiquement, avec rechargement à chaud à chaque modif de code (`r` dans le terminal).

### Pour donner une version "finale" sans câble (ex: au livreur/cousin)
6. Build le fichier installable :
   ```bash
   flutter build apk --release
   ```
   Le fichier `.apk` se trouve dans `build/app/outputs/flutter-apk/app-release.apk`
7. Transfère-le sur l'appareil (câble USB, Drive, Bluetooth — peu importe)
8. Sur le téléphone : autorise **"Installer des applications inconnues"** pour l'app utilisée pour l'ouvrir (Fichiers, Drive, etc.) — Android le demande automatiquement à la première tentative d'installation
9. Ouvre le fichier `.apk` → **Installer**

Fais le test #4 (`flutter devices`) en premier avant d'aller plus loin — dis-moi ce qu'il retourne.