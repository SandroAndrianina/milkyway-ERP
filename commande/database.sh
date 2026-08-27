# Lancer les migrations (crée les tables)
php spark migrate

# Lancer un refresh
php spark migrate:refresh

# Annuler la dernière migration (si erreur)
php spark migrate:rollback

# Voir le statut des migrations
php spark migrate:status

php spark db:seed ProduitSeeder