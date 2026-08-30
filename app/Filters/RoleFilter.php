<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Vérifier la session
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // Vérifier le rôle
        $userRole = session()->get('role');
        if (empty($arguments) || !in_array($userRole, $arguments)) {
            // ✅ Renvoyer une erreur 403 au lieu de rediriger
            return service('response')
                ->setStatusCode(403)
                ->setBody('<h1>Accès interdit</h1><p>Vous n\'avez pas les droits nécessaires pour accéder à cette page.</p>');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien
    }
}