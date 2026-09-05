# To-Do list – Déploiement Milky Way (v2)

## Étape 0 – Prérequis (sur ton Mac)
- Installe Docker Desktop.
- Vérifie que WSL2 est bien activé côté PC du cousin (pour Windows 11).

---

## Étape 1 – Dockeriser (sur ton Mac, en mode DEV)
- Dockerfile + `docker-compose.yml` avec volumes persistants MySQL, `restart: unless-stopped`, extensions PHP (curl, mysqli, pdo, zip, openssl), et fichier `.env`.

---

## Étape 2 – Code utilisateurs + rôles + responsive (DANS le container)
- Gestion utilisateurs avec séparation rôles (vente, stocks, admin).
- Hash des mots de passe (password_hash) en BDD.
- **Responsive design (CSS media queries)** pour l’interface admin (consultation occasionnelle sur navigateur mobile/tablette).

---

## Étape 3 – Activer HTTPS (même en local) – NON NÉGOCIABLE
- Génère un certificat auto-signé avec `mkcert` ou OpenSSL pour l’IP fixe du PC du cousin (ex: `192.168.1.100`).
- Configure Apache/Nginx dans le container pour écouter en HTTPS.
- Dans le `.env` CI4 : `CI_ENVIRONMENT = production` et **surtout** `forceHTTPS = true` (ne jamais désactiver).
- Le mobile Flutter acceptera ce certificat via un `badCertificateCallback` **uniquement** pour cette IP spécifique (pas pour toute la toile).

---

## Étape 4 – API : Authentification et délivrance du JWT (AVANT les endpoints de synchro)
- Crée l’endpoint **`POST /api/auth/login`**.
  - Reçoit `login` + `mot_de_passe` depuis le mobile.
  - Vérifie en BDD, génère un **JWT** contenant : `user_id`, `role`, `device_id` (si fourni), et `exp` (expiration à 30 jours par exemple).
  - Renvoie ce JWT au mobile.
- Le mobile stocke ce JWT de manière sécurisée via `flutter_secure_storage` (jamais en `shared_preferences` en clair).

---

## Étape 5 – Middleware de sécurité + endpoints de synchro (côté CI4)
- Crée un **middleware** qui vérifie le JWT sur **tous** les endpoints préfixés par `/api/sync/*`.
  - Vérifie la signature, l’expiration, et **le rôle** (ex: un token "vente" ne peut pas appeler un endpoint admin).
- Endpoint **`POST /api/sync/push`** :
  - Reçoit un tableau de mouvements (avec UUID généré par le mobile).
  - Vérifie l’unicité de chaque UUID (idempotence).
  - **Validation métier** : vérifie que chaque `product_id` existe et n’est pas en `is_deleted = 1`. Si un produit est invalide, on rejette **seulement cette ligne** (les autres passent) et on logge l’erreur pour que l’admin puisse corriger plus tard.
  - Insertion transactionnelle (avec `INSERT IGNORE` ou `ON DUPLICATE KEY` géré manuellement pour les UUID).
- Endpoint **`GET /api/sync/pull?since=timestamp`** :
  - Renvoie tous les changements (produits, prix, clients) depuis `since`.
  - **Réponse obligatoire** : inclut un champ `server_time` (timestamp actuel du serveur) dans le JSON.
  - Le mobile utilisera **ce `server_time`** comme base pour son prochain appel `since`, jamais son horloge locale (pour éviter les décalages horaires).

---

## Étape 6 – Configurer Supabase (boîte aux lettres pour le mode distant)
- Crée un projet Supabase (plan gratuit).
- Table relais : `id`, `uuid_mouvement`, `role_expediteur`, `payload` (JSON du mouvement), `device_id`, `created_at`, `statut` (par défaut `'pending'`).
- **Sécurité impérative** :
  - Active **Row Level Security (RLS)**.
  - Crée une politique **UNIQUEMENT en INSERT** pour la clé utilisée dans le mobile.
  - Génère une clé `anon` **restreinte** (c’est celle que tu mets dans l’app mobile). **Jamais** la clé `service_role` dans le mobile.
  - Vérifie en console SQL qu’avec cette clé, on ne peut PAS faire de `SELECT` ou `UPDATE` sur cette table.

---

## Étape 7 – Développer l’app mobile Flutter (offline-first)
- Setup : `sqflite` ou `drift` pour la base locale.
- **Règle d’or** : Le mobile ne modifie **jamais** le catalogue (produits/prix/clients) en local. Il ne fait que :
  - Lire le catalogue local pour faire une vente.
  - Créer des **mouvements** (ventes, non-conformes) avec un **UUID généré côté client**.
- Écrans 100% utilisables hors-ligne.
- **Logique réseau** :
  1. Tente une requête `GET /api/health` sur l’IP fixe du PC (timeout 2s). Si OK → **mode LAN**.
  2. En mode LAN : **PUSH d’abord** (envoie tous les mouvements en attente), **ensuite PULL** (récupère les nouveaux produits/prix/clients depuis `since` stocké).
  3. Si le PC ne répond pas mais qu’Internet est dispo → **mode distant** : PUSH uniquement vers Supabase (avec la clé restreinte).
  4. Si aucun réseau → tout reste en attente locale.

---

## Étape 8 – Traitement de la file Supabase par le PC (récupération des mouvements distants)
- Crée un endpoint interne dans CI4 (non exposé publiquement) : **`GET /api/cron/process-supabase`**.
  - Ce endpoint interroge Supabase (via sa clé `service_role` stockée **uniquement** dans le `.env` du PC) pour récupérer les lignes `statut = 'pending'`.
  - Pour chaque ligne : vérifie l’UUID, insère dans MySQL, puis **met à jour** le statut Supabase en `'processed'`.
  - Gère les erreurs : si un mouvement est invalide (produit inconnu), on le passe en `'rejected'` avec un message d’erreur dans une colonne `error_log`.
  - **Housekeeping** : supprime les lignes `processed` et `rejected` de plus de 7 jours pour ne pas exploser le quota Supabase.
- **Planification Windows** (et non `docker exec`) :
  - Crée une tâche planifiée Windows qui lance toutes les 5 minutes un simple `curl -X GET http://localhost:8080/api/cron/process-supabase` (le port interne du container).
  - Ajoute un **bouton manuel** "Synchroniser maintenant" dans l’admin CI4, qui déclenche ce même endpoint.

---

## Étape 9 – Tester les 2 modes de synchro (en conditions simulées)
- **Mode LAN** : Puis PUSH puis PULL. Vérifie que le `server_time` est bien utilisé pour le prochain `since`.
- **Mode distant** : Coupe le WiFi du PC, pousse un mouvement depuis le mobile vers Supabase, rallume le PC, vérifie que la tâche planifiée récupère le mouvement sans doublon.

---

## Étape 10 – Finaliser l’image de production (sur ton Mac)
- `COPY . /var/www/html`, rebuild l’image.
- Export avec `docker save -o milkyway.tar nom_image:tag` sur une clé USB (pour éviter de pull depuis Docker Hub).

---

## Étape 11 – Déployer sur le PC Windows 11 du cousin
- Installer Docker Desktop.
- `docker load -i milkyway.tar` et `docker compose up -d`.
- Configurer l’IP fixe sur le PC (ex: `192.168.1.100`).
- Désactiver la veille du PC.
- Créer un script `.bat` sur le bureau : `docker compose up -d` (pour les redémarrages manuels).
- Ajouter un **raccourci au démarrage automatique** de Windows pour lancer Docker + le compose.
- Générer un QR code contenant l’IP du PC pour que les mobiles se connectent facilement.

---

## Étape 12 – Tester avec les vraies tablettes/smartphones (réel)
- Navigateur (responsive) pour l’admin (sur le PC ou un autre écran).
- **App mobile Flutter** pour les rôles vente/stocks.
- Tester les 2 modes de synchro en conditions réelles (LAN bureau + 4G en ville).

---

## Étape 13 – Importer les vraies données du cousin
- Importer les produits, clients, stocks initiaux depuis CSV/Excel.
- Vérifier que les prix et les durées de conservation sont bien repris.

---

## Étape 14 – Backup automatique vers Google Drive (ESSENTIEL)
- Script nocturne (tâche planifiée Windows) qui exécute :
  ```bash
  docker exec nom_container_mysql mysqldump --single-transaction --quick -u root -p[mdp] nom_bdd > /chemin/backup_$(date).sql
  ```
  *(Le `--single-transaction` est crucial pour ne pas corrompre le dump en cas d’écriture simultanée)*.
- Utiliser `rclone` pour synchroniser ce fichier vers Google Drive.
- **Teste obligatoirement la restauration** sur un container vierge (pour valider que le dump est sain). Ce backup couvre aussi les données synchronisées (pas besoin de backup Supabase, ce n’est qu’un relais).

---

## Étape 15 – Documentation de passation (pour le cousin)
- Identifiants des comptes par défaut (vente, stocks, admin) et procédure pour changer les mots de passe.
- **Clés Supabase** : où sont stockées (`service_role` dans `.env` du PC, `anon` restreinte dans le code Flutter). Procédure de rotation si une clé fuit.
- **Marche à suivre en cas de panne** :
  - Si le PC plante : redémarrer, lancer le `.bat`, la file Supabase s’accumule sans perte.
  - Comment vérifier que le container tourne (Docker Desktop).
  - Qui contacter (toi) pour un problème bloquant.
- Expliquer que le mobile **doit** effectuer une synchro LAN régulièrement pour mettre à jour son catalogue (les prix changent sur le PC uniquement).

---

### Récapitulatif des changements majeurs par rapport à ta liste initiale :

| Point ajouté / modifié | Raison |
| :--- | :--- |
| **HTTPS obligatoire** (Étape 3) | Sécurité impérative, même en LAN (interception possible). |
| **Endpoint `/api/auth/login`** (Étape 4) | Indispensable pour obtenir le JWT avant toute synchro. |
| **`server_time` dans le `pull`** (Étape 5) | Évite les bugs de décalage horaire entre mobile et PC. |
| **Validation métier** (produit existe) dans le `push` (Étape 5) | Évite d’insérer des ventes sur des produits supprimés. |
| **Ordre PUSH puis PULL** en mode LAN (Étape 7) | Garantit que la vente est enregistrée avec l’ancien prix avant que le catalogue ne soit mis à jour. |
| **Traitement Supabase via endpoint interne + `curl` Windows** (Étape 8) | Évite les problèmes de `docker exec` depuis une tâche planifiée. |
| **`--single-transaction`** pour le backup (Étape 14) | Garantit un dump cohérent. |

---

Cette liste est maintenant **prête pour une exécution séquentielle**. Tu ne risques plus d’oublier un maillon essentiel en cours de route. Veux-tu que je détaille un point particulier (ex: code du middleware JWT dans CI4, ou la configuration du `badCertificateCallback` dans Flutter) ?
