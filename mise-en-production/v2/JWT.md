Étape par étape (dev, dans le container) :

## 1. Installe la librairie JWT
```bash
docker exec -it milkyway_app composer require firebase/php-jwt
```
*(Comme t'es en bind mount, ça écrit directement dans ton dossier local — pas besoin de rebuild l'image.)*

## 2. Ajoute une clé secrète dans `.env`
```
JWT_SECRET_KEY=une_longue_chaine_aleatoire_ici_min_32_caracteres
```
Génère-la proprement (pas "azerty123") :
```bash
openssl rand -base64 32
```

## 3. Crée le contrôleur `app/Controllers/Api/AuthController.php`
```php
<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use Firebase\JWT\JWT;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login()
    {
        $login = $this->request->getPost('login');
        $motDePasse = $this->request->getPost('mot_de_passe');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('login', $login)->first();

        if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Identifiants invalides'
            ]);
        }

        $payload = [
            'user_id'   => $user['id'],
            'role'      => $user['role'],
            'device_id' => $this->request->getPost('device_id') ?? null,
            'iat'       => time(),
            'exp'       => time() + (30 * 24 * 60 * 60), // 30 jours
        ];

        $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');

        return $this->response->setJSON(['token' => $jwt]);
    }
}
```

## 4. Ajoute la route dans `app/Config/Routes.php`
```php
$routes->post('api/auth/login', 'Api\AuthController::login');
```

## 5. Vérifie que ton `UserModel` existe avec les bons champs
Confirme que la table `users` a bien : `login`, `mot_de_passe` (hashé avec `password_hash()`), `role` (vente/stocks/admin). Si pas encore fait, corrige avant de tester.

## 6. Teste avec curl
```bash
curl -k -X POST https://localhost:8443/api/auth/login \
  -d "login=admin&mot_de_passe=motdepasse123"
```
Tu dois recevoir un JSON `{"token": "eyJ..."}`.

## 7. Côté Flutter (à faire à l'Étape 7, pas maintenant)
Juste pour info, plus tard : `flutter_secure_storage` stocke ce token de façon chiffrée (Keychain iOS / Keystore Android) — retiens juste le nom du package, tu l'utiliseras à l'Étape 7.

---

Une fois le login qui renvoie bien un JWT valide, tu passes à l'Étape 5 (middleware qui vérifie ce token sur les endpoints `/api/sync/*`).