# Lancer les migrations (crée les tables)
php spark migrate

# Lancer un refresh
php spark migrate:refresh

# Annuler la dernière migration (si erreur)
php spark migrate:rollback

# Voir le statut des migrations
php spark migrate:status

# insertion des seeders
php spark db:seed ProduitSeeder
php spark db:seed MouvementSeeder
php spark db:seed ClientSeeder