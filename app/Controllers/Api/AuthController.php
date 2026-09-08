<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use Firebase\JWT\JWT;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login(): ResponseInterface
    {
        // 1. Récupérer le corps JSON
        $data = $this->request->getJSON(true);
        
        // 2. Valider les champs requis
        if (!isset($data['login']) || !isset($data['mot_de_passe'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Les champs login et mot_de_passe sont requis']);
        }

        $login = $data['login'];
        $motDePasse = $data['mot_de_passe'];
        $device_id = $data['device_id'] ?? null;

        // 3. Vérifier l'utilisateur avec jointure pour récupérer le nom du rôle
        $userModel = model(\App\Models\UserModel::class);
        $user = $userModel
            ->select('users.*, roles.nom as role_name') // supposons que la table roles a une colonne 'nom'
            ->join('roles', 'roles.id = users.role_id', 'left') // left join pour les utilisateurs sans rôle
            ->where('users.nom', $login)
            ->first();

        // 4. Vérifier les identifiants (attention: colonne password, pas mot_de_passe)
        if (!$user || !password_verify($motDePasse, $user['password'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['error' => 'Identifiants invalides']);
        }

        // 5. Définir le rôle (valeur par défaut si jointure vide)
        $role = $user['role_name'] ?? 'user';

        // 6. Construire le JWT
        $payload = [
            'user_id'   => $user['id'],
            'role'      => $role,
            'device_id' => $device_id,
            'iat'       => time(),
            'exp'       => time() + (30 * 24 * 60 * 60), // 30 jours
        ];

        $secretKey = getenv('JWT_SECRET_KEY');
        if (!$secretKey || strlen($secretKey) < 32) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Clé JWT non configurée ou trop courte']);
        }

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        // 7. Réponse
        return $this->response->setJSON(['token' => $jwt]);
    }
}