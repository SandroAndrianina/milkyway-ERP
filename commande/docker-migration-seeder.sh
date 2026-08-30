# Lancer les migrations (crée les tables)
docker compose exec app php spark migrate

# Lancer un refresh
docker compose exec app php spark migrate:refresh

# Annuler la dernière migration (si erreur)
docker compose exec app php spark migrate:rollback

# Voir le statut des migrations
docker compose exec app php spark migrate:status

# Insertion des seeders
docker compose exec app php spark db:seed ProduitSeeder
docker compose exec app php spark db:seed MouvementSeeder
docker compose exec app php spark db:seed ClientSeeder
docker compose exec app php spark db:seed RoleSeeder
docker compose exec app php spark db:seed UserSeeder