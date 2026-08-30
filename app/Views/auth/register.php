<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Milky Way</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">

            <img src="/assets/images/logo-remove.png" alt="Milky Way" class="logo-img">

            <h1 class="auth-title">Milky Way</h1>
            <p class="auth-subtitle">Créez votre compte</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="/auth/doRegister" method="POST">
                <?= csrf_field() ?>
                <div style="margin-bottom:1rem;">
                    <label for="nom" class="auth-label">Nom d'utilisateur</label>
                    <input type="text" name="nom" id="nom" class="auth-input" placeholder="Entrez votre nom" required>
                </div>
                <div style="margin-bottom:1rem;">
                    <label for="password" class="auth-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="auth-input" placeholder="Choisissez un mot de passe" required>
                </div>
                <div style="margin-bottom:1rem;">
                    <label for="password_confirm" class="auth-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="auth-input" placeholder="Retapez le mot de passe" required>
                </div>

                <!-- Sélection du rôle -->
                <div style="margin-bottom:1.5rem;">
                    <label for="role_id" class="auth-label">Rôle souhaité</label>
                    <select name="role_id" id="role_id" class="auth-input" required>
                        <option value="">-- Choisissez un rôle --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= ucfirst($role['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p style="font-size:0.75rem; color:#6b7280; margin-top:0.25rem;">
                        L'administrateur devra valider votre compte avant de pouvoir vous connecter.
                    </p>
                </div>

                <button type="submit" class="auth-btn">S'inscrire</button>
            </form>

            <div class="auth-footer">
                Déjà un compte ? <a href="/login">Connectez-vous</a>
            </div>
        </div>
    </div>
</body>
</html>