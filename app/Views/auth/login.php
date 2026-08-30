<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Milky Way</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Work Sans', sans-serif; background-color: #F9F6F0; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                <img src="/assets/images/logo-remove.png" alt="Milky Way" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-primary">Milky Way</h1>
            <p class="text-gray-500 text-sm">Laiterie d'Antsirabe</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="/auth/doLogin" method="POST">
            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                <input type="text" name="nom" id="nom" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                       placeholder="Entrez votre nom" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                       placeholder="Entrez votre mot de passe" required>
            </div>
            <button type="submit" 
                    class="w-full bg-primary text-white font-semibold py-2.5 rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                Se connecter
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Comptes de test :</p>
            <p class="text-xs">Admin: admin / admin123 | Vente: Vendeur / vente123 | Stocks: Gestionnaire Stock / stock123</p>
        </div>
    </div>
</body>
</html>