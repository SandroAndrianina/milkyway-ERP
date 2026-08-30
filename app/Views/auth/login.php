<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Milky Way</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">

            <img src="/assets/images/logo-remove.png" alt="Milky Way" class="logo-img">

            <h1 class="auth-title">Milky Way</h1>
            <p class="auth-subtitle">Laiterie d'Antsirabe</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="/auth/doLogin" method="POST">
                <?= csrf_field() ?>
                <div style="margin-bottom:1rem;">
                    <label for="nom" class="auth-label">Nom d'utilisateur</label>
                    <input type="text" name="nom" id="nom" class="auth-input" placeholder="Entrez votre nom" required>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label for="password" class="auth-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="auth-input" placeholder="Entrez votre mot de passe" required>
                </div>
                <button type="submit" class="auth-btn">Se connecter</button>
            </form>

            <div class="auth-footer">
                Pas encore de compte ? <a href="/register">Créer un compte</a>
            </div>

            <div class="auth-test-accounts">
                <p>Comptes de test :</p>
                <p>Admin: admin / admin123 | Vendeur: Vendeur / vente123 | Stock: Gestionnaire Stock / stock123</p>
            </div>
        </div>
    </div>
</body>
</html>