<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    // Lister tous les utilisateurs (sans password)
    public function index()
    {
        $users = $this->userModel->getUsersWithRoles();
        // Supprimer le champ password de chaque utilisateur
        foreach ($users as &$user) {
            unset($user['password']);
        }
        return $this->response->setJSON($users);
    }

    // Valider un compte (pending -> active)
    public function validateUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Utilisateur introuvable']);
        }

        if ($user['status'] !== 'pending') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Ce compte n\'est pas en attente de validation']);
        }

        $this->userModel->update($id, ['status' => 'active']);
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Compte validé']);
    }

    // Désactiver un compte (active -> disabled)
    public function disableUser($id)
    {
        $userId = (int) $id;

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Utilisateur introuvable']);
        }

        // Empêcher de se désactiver soi-même
        if ($userId === (int) session('user_id')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        if ($user['status'] === 'disabled') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Ce compte est déjà désactivé']);
        }

        $this->userModel->update($userId, ['status' => 'disabled']);
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Compte désactivé']);
    }

    // Changer le rôle d'un utilisateur
    public function changeRole($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Utilisateur introuvable']);
        }

        $newRoleId = $this->request->getPost('role_id');
        if (!$newRoleId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'role_id requis']);
        }

        $role = $this->roleModel->find($newRoleId);
        if (!$role) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Rôle invalide']);
        }

        $this->userModel->update($id, ['role_id' => $newRoleId]);
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Rôle modifié']);
    }

    // Réinitialiser le mot de passe (génère un nouveau)
    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Utilisateur introuvable']);
        }

        $newPassword = $this->userModel->resetPassword($id);
        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Mot de passe réinitialisé',
            'new_password' => $newPassword, // À afficher une seule fois
        ]);
    }

    // Réactiver un compte (disabled -> active)
    public function reactivateUser($id)
    {
        $userId = (int) $id;

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Utilisateur introuvable']);
        }

        if ($user['status'] !== 'disabled') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Ce compte n\'est pas désactivé']);
        }

        $this->userModel->update($userId, ['status' => 'active']);
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Compte réactivé']);
    }

    // Créer un nouvel utilisateur admin
    public function createAdminUser()
    {
        $data = $this->request->getJSON(true);
        $nom = $data['nom'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($nom) || empty($password)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Nom et mot de passe requis']);
        }

        $userModel = new UserModel();

        // Vérifier si le nom existe déjà
        if ($userModel->where('nom', $nom)->first()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Ce nom d\'utilisateur est déjà pris']);
        }

        // Récupérer l'ID du rôle admin
        $roleModel = new \App\Models\RoleModel();
        $adminRole = $roleModel->where('nom', 'admin')->first();

        if (!$adminRole) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Rôle admin introuvable']);
        }

        $userModel->insert([
            'nom'      => $nom,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => $adminRole['id'],
            'status'   => 'active',
            'must_change_password' => 1, // Forcer le changement à la première connexion
        ]);

        return $this->response->setStatusCode(201)
            ->setJSON(['status' => 'ok', 'message' => 'Administrateur créé avec succès']);
    }
}