<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Token manquant']);
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
        } catch (\Exception $e) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Token invalide ou expiré']);
        }

        // Vérification du rôle si un rôle requis est passé en argument
        if ($arguments && !in_array($decoded->role, $arguments)) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'Rôle non autorisé']);
        }

        $request->user_id = $decoded->user_id;
        $request->role = $decoded->role;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}