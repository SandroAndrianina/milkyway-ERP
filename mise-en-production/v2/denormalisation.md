## Mini to-do

### OK 1. Migration — ajoute la colonne sur `mouvements`

```bash
docker exec -it milkyway_app php spark migrate
```

### OK 2. Corrige la saisie côté web (Écoulement)
Dans le contrôleur/service qui enregistre un mouvement : va chercher le prix actuel du produit **au moment de l'insertion**, et le copie dans `prix_unitaire` — jamais de jointure vers `produits.prix_vente` pour l'affichage a posteriori.

### OK 3. Corrige `SyncController::push` (côté mobile)
Le payload du mobile doit **déjà contenir** le prix (capturé côté Flutter au moment de la vente, voir point 4) :
```php
$mouvementModel->insert([
    'uuid'          => $m['uuid'],
    'product_id'    => $m['product_id'],
    'quantite'      => $m['quantite'],
    'prix_unitaire' => $m['prix_unitaire'], // vient du payload, pas de la table produits
    'type'          => $m['type'],
    'user_id'       => $this->request->user_id,
]);
```
Ajoute une validation : rejette la ligne si `prix_unitaire` est absent du payload (comme pour `product_id` invalide).

### 4. Côté Flutter — capture le prix au moment de la vente
Quand le livreur crée un mouvement, lis le prix depuis le **cache local** (table `produits` en SQLite) et écris-le directement dans l'enregistrement `mouvements` local — pas une référence à récupérer plus tard.

### OK 5. Corrige les écrans qui affichent l'historique/récapitulation
Vérifie tous les endroits qui calculent du chiffre d'affaires ou affichent un mouvement passé (Récapitulation, Détails client) — ils doivent lire `mouvements.prix_unitaire`, pas faire de jointure vers `produits.prix_vente`.

Une fois ça fait, tu élimines une vraie faille de cohérence historique — bon réflexe de l'avoir repéré.