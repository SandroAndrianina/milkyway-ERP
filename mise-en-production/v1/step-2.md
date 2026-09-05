# To-do – Étape 2 (partie 1) : Gestion des utilisateurs + authentification + rôles

## 1. Base de données
- [ ] Migration `users` : `id`, `nom`, `email`, `password` (haché), `role` (ENUM `vente`/`stocks`/`admin`), `status` (ENUM `pending`/`active`/`disabled`), timestamps.
- [ ] Seeder pour créer le tout premier compte admin manuellement (pas d'auto-inscription admin possible).

## 2. Authentification (custom, pas de librairie type Shield)
- [ ] Page `/login` classique (formulaire HTML, pas d'API JSON).
- [ ] Hash des mots de passe avec `password_hash()` / vérification avec `password_verify()` — jamais en clair.
- [ ] Stocker `user_id` + `role` en session CI4 (`session()->set(...)`) après login réussi.
- [ ] Login refusé si `status != 'active'`.
- [ ] Page/bouton de déconnexion qui détruit la session (`session()->destroy()`).

## 3. Validation de compte par l'admin
- [ ] Inscription publique (si prévue) crée un compte avec `status = 'pending'`.
- [ ] Écran admin listant les comptes `pending` avec bouton "valider" → passe `status` à `active`.

## 4. Filtre de rôle CI4
- [ ] Créer `app/Filters/RoleFilter.php` : vérifie la session (sinon redirect `/login`), puis vérifie que `session('role')` est dans la liste de rôles passée en paramètre.
- [ ] Déclarer l'alias `role` dans `Config/Filters.php`.
- [ ] Grouper les routes dans `Routes.php` par rôle :
  - `role:vente,admin` → routes Ventes
  - `role:stocks,admin` → routes Stock
  - `role:admin` → gestion utilisateurs, DLC
- [ ] **Protéger aussi les routes `/api/...`**, pas seulement les pages — sinon l'API reste accessible en direct malgré les boutons cachés en JS.

## 5. Adapter les Services pour la finance (rôle stocks)
- [ ] Modifier `StockService::getStockGlobal()` pour accepter un paramètre (ex. `$includeFinance: bool`).
- [ ] `StockController::index()` lit `session('role')` et passe `false` pour le rôle stocks → `prix_vente` et `total` absents de la réponse JSON, pas juste cachés en CSS.

## 6. Réactiver le CSRF
- [ ] Réactiver le filtre `csrf` sur toutes les routes POST/PUT/DELETE (redevient nécessaire dès qu'on authentifie par cookie de session).

## 7. Abandonner l'écran "Mouvements de stock" actuel
- [ ] Supprimer l'ancien écran unique (formulaire avec radio type + dropdown cause + client conditionnel).
- [ ] Construire l'écran **Ventes** (route `role:vente,admin`) : produit, quantité, client, date — toujours `type=sortie`/`cause=vente`.
- [ ] Construire l'écran **Stock** (route `role:stocks,admin`) : radio entrée/sortie, produit, quantité, date — pas de client, `cause=non_conforme` implicite si sortie.
- [ ] Admin voit les deux écrans dans son menu (accès cumulé, pas un 3e écran séparé).

## 8. Checklist de vérification avant de continuer
- [ ] Un compte `vente` ne peut pas atteindre une route stock via l'URL directe (tester avec curl/Postman, pas juste le navigateur).
- [ ] Un compte `stocks` ne voit aucun prix dans la réponse JSON de l'état de stock.
- [ ] Déconnexion détruit bien la session.
- [ ] Un compte `pending` ne peut pas se connecter.
- [ ] Un formulaire sans token CSRF est rejeté.
