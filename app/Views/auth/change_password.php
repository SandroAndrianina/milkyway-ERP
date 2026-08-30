<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe - Milky Way</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">

            <img src="/assets/images/logo-remove.png" alt="Milky Way" class="logo-img">

            <h1 class="auth-title">Milky Way</h1>
            <p class="auth-subtitle">Changer votre mot de passe</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <p class="text-sm text-gray-500 mb-4">
                Vous devez changer votre mot de passe avant de continuer.
            </p>

            <form action="/auth/doChangePassword" method="POST">
                <?= csrf_field() ?>
                <div style="margin-bottom:1rem;">
                    <label for="new_password" class="auth-label">Nouveau mot de passe</label>
                    <input type="password" name="new_password" id="new_password" class="auth-input" placeholder="Minimum 4 caractères" required>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label for="confirm_password" class="auth-label">Confirmer le mot de passe</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="auth-input" placeholder="Retapez le mot de passe" required>
                </div>
                <button type="submit" class="auth-btn">Changer le mot de passe</button>
            </form>

            <div class="auth-footer mt-4">
                <a href="/logout">Se déconnecter</a>
            </div>
        </div>
    </div>
</body>
</html>