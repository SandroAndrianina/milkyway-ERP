# Créer une migration
php spark make:migration CreateProduits
php spark make:migration CreateClients
php spark make:migration CreateMouvements

# Créer un model
php spark make:model ProduitModel

# Créer un controller
php spark make:controller Api/ProduitController
php spark make:controller Api/DlcController

# Modèles
php spark make:model ClientModel
php spark make:model MouvementModel

# Controllers API (Écoulement)
php spark make:controller Api/ClientController
php spark make:controller Api/MouvementController

# créer un controller séparé pour Écoulement (création/gestion complète)
php spark make:controller Api/EcoulementProduitController

# creation de seeders 
php spark make:seeder ProduitSeeder
php spark make:seeder ClientSeeder
php spark make:seeder MouvementSeeder