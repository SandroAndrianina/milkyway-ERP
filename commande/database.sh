# Lancer les migrations (crée les tables)
php spark migrate

# Annuler la dernière migration (si erreur)
php spark migrate:rollback

# Voir le statut des migrations
php spark migrate:status