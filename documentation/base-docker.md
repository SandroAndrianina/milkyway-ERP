Direct. Corrigeons ta mémoire, puis je t'explique le **seul concept théorique** qui compte pour l'étape 1.

**Ce que tu dois corriger tout de suite :**
- **Image ≠ structure d'OS.** Une image Docker est une **"photographie figée"** (snapshot) contenant : un système de fichiers minimal (Alpine/Debian) + tous les logiciels installés (PHP, Apache) + TON code (copié). Elle ne contient PAS le noyau Linux (celui de ton Mac/WSL fait le pont).
- **Container ≠ simple instance.** Un container, c'est cette image **en train de tourner** (en vie) avec sa propre mémoire, ses processus, et sa petite couche d'écriture.

---

**Les 3 notions théoriques OBLIGATOIRES pour l'étape 1 (le "coder en direct") :**

**1. Le Bind Mount (le plus important)**
C'est un **"miroir magique"** entre un dossier de ton Mac et un dossier dans le container.
- Dans `docker-compose.yml`, tu écris : `volumes: - ./src:/var/www/html`
- **Traduction :** "Ce que je modifie dans le dossier `src` de mon Mac, le container le voit instantanément dans `/var/www/html`".
- **Conséquence :** Tu codes sur ton Mac avec VSCode, tu sauvegardes, tu raffraichis le navigateur (`localhost`). Le changement est appliqué dans le container **SANS REBUILD**, SANS redémarrer. C'est comme si tu codais en local, mais c'est Docker qui l'exécute.

**2. Le docker-compose (orchestrateur)**
Ce n'est pas juste un outil. C'est le chef d'orchestre qui lance **2 containers en parallèle** :
- Container `app` (ton code PHP + Apache).
- Container `db` (MySQL).
Ils se voient automatiquement sur un réseau privé interne. Ton code PHP se connectera à la base via le nom de service `db` (pas `localhost`).

**3. Le mapping des ports**
- Tu écris : `ports: - 8080:80`
- **Traduction :** Le port 80 (interne Apache) est projeté sur le port 8080 de ton Mac.
- Tu ouvres ton navigateur Mac sur `http://localhost:8080` → ça atterrit directement dans le container Apache.

---

**En pratique pour l'étape 1 (ce qui va se passer) :**

1. Tu écris ton `Dockerfile` (la recette).
2. Tu écris ton `docker-compose.yml` (l'orchestre) avec le bind mount `./src:/var/www/html`.
3. Tu lances : `docker compose up -d`.
4. Tu ouvres ton Mac, tu vas dans le dossier `src`, tu crées `index.php`.
5. Tu ouvres Chrome sur `http://localhost:8080` → tu vois ton `index.php` s'exécuter.

**Tu ne rebuilds JAMAIS l'image pendant que tu codes. Tu codes, tu F5, tu codes, tu F5.** C'est pour ça qu'on fait ça en étape 1, pas après.

C'est clair ? Tu veux que je te donne le squelette exact du `Dockerfile` et du `docker-compose.yml` pour CodeIgniter 4 qui colle à cette théorie ?