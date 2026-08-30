# To-do list – Déploiement Milky Way

## OK Étape 0 – Prérequis (sur ton Mac)
- Installe Docker Desktop.
- Vérifie que WSL2 est bien activé par l'installateur Docker Desktop côté PC du cousin (pas une étape manuelle séparée, juste à confirmer plus tard).

## OK Étape 1 – Dockeriser (sur ton Mac, en mode DEV)
- Crée `Dockerfile` + `docker-compose.yml` avec volumes bind mounts (`./src:/var/www/html`) pour coder en direct.
- Ajoute un **volume persistant pour MySQL** (`volumes: db_data:/var/lib/mysql`) — sans ça, les données disparaissent à chaque recréation du container.
- Ajoute `restart: always` sur les deux services (`app` et `db`) — sans ça, le démarrage automatique côté cousin ne sert à rien si un container crash.
- Ajoute les extensions PHP (mysqli, pdo_mysql, intl).
- Configure les variables d'env (`.env`) pour MySQL (user/mdp) dès maintenant.
- Lance `docker compose up -d` et vérifie que l'app s'affiche sur `http://localhost`.

## Étape 2 – Coder login + rôles + responsive (DANS le container)
- Code la gestion des utilisateurs et la séparation des rôles (**vente, stocks, admin**) en tournant sur les containers (modification en direct via le volume bind).
- Sécurise : désactive `forcehttps` dans `Config/Filters.php`, assainis les entrées.
- Rends le design responsive (CSS media queries pour tablettes/smartphones).

## Étape 3 – Finaliser l'image de prod (sur ton Mac)
- Remplace le volume bind par un `COPY . /var/www/html` dans le Dockerfile.
- Rebuild l'image finale (`docker build -t milkyway-prod .`).
- Sauvegarde l'image : `docker save milkyway-prod > milkyway.tar` (prévoir une clé USB pour le transfert, pas un envoi par mail/Drive vu la taille).

## Étape 4 – Déployer sur le PC Windows 11 du cousin
- Installe Docker Desktop (WSL2 s'active normalement automatiquement avec — juste vérifier que c'est bien le cas).
- Copie le fichier `milkyway.tar` et fais `docker load < milkyway.tar`.
- Configure l'IP fixe (192.168.1.99 ou similaire) dans le routeur.
- **Désactive la mise en veille** dans les paramètres d'alimentation Windows — sinon tout le reste ne sert à rien après 30 min d'inactivité.
- Crée le script `.bat` et mets-le dans `shell:startup` (démarrage auto).
- Génère le QR code avec `http://IP_FIXE` et imprime-le.

## Étape 5 – Tester avec tablettes/smartphones (réel)
- Lance l'app sur le PC.
- Scanne le QR code avec une tablette Android et un smartphone Android.
- Vérifie le login, les rôles, et l'affichage responsive.

## Étape 6 – Tester avec les vraies données de ton cousin
- Importe son export CSV/Excel actuel dans la base.
- Fais des saisies réelles (commandes, clients) pour valider le métier.

## Étape 7 – Mettre en place le backup automatique vers Google Drive
- Configure un script (ou cron via tâche planifiée Windows) qui exporte la base en `.sql` toutes les nuits et le synchronise avec Google Drive (via rclone ou Backup and Sync).
- **Teste obligatoirement la restauration** : supprime la base, importe le `.sql` et vérifie que tout revient.

## Étape 8 – Documentation de passation
- Rédige un petit document avec : les identifiants des 3 comptes (vente, stocks, admin), la marche à suivre si le PC plante ou redémarre, et qui contacter en cas de problème.