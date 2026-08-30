<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Si déjà connecté, rediriger
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('auth/login');
    }

    public function doLogin()
    {
        $nom = $this->request->getPost('nom');
        $password = $this->request->getPost('password');

        if (empty($nom) || empty($password)) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('nom', $nom)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Votre compte est en attente de validation ou désactivé.');
        }

        // ✅ Vérifier si l'utilisateur doit changer son mot de passe
        if ($user['must_change_password'] == 1) {
            session()->set([
                'must_change_password' => true,
                'user_id' => $user['id'],
                'isLoggedIn' => false, // pas encore autorisé
            ]);
            return redirect()->to('/change-password');
        }

        // Stocker en session
        $role = $userModel->getRoleName($user['id']);
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'user_nom'   => $user['nom'],
            'role'       => $role,
        ]);

        // Redirection selon le rôle
        if ($role === 'admin') {
            return redirect()->to('/dlc/catalogue');
        } elseif ($role === 'vente') {
            return redirect()->to('/clients');
        } elseif ($role === 'stocks') {
            return redirect()->to('/etat-stock');
        }

        return redirect()->to('/');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function register()
    {
        $roleModel = new \App\Models\RoleModel();
        $roles = $roleModel->getRolesExceptAdmin();

        return view('auth/register', ['roles' => $roles]);
    }

    public function doRegister()
    {
        $nom = $this->request->getPost('nom');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $roleId = $this->request->getPost('role_id');

        if (empty($nom) || empty($password) || empty($roleId)) {
            return redirect()->back()->with('error', 'Tous les champs sont requis.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Les mots de passe ne correspondent pas.');
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->where('nom', $nom)->first()) {
            return redirect()->back()->with('error', 'Ce nom d\'utilisateur est déjà pris.');
        }

        $roleModel = new \App\Models\RoleModel();
        $role = $roleModel->findNonAdminRoleById($roleId);
        if (!$role) {
            return redirect()->back()->with('error', 'Rôle invalide.');
        }

        $userModel->insert([
            'nom'      => $nom,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => $role['id'],
            'status'   => 'pending',
        ]);

        return redirect()->to('/login')->with('success', 'Inscription réussie. En attente de validation.');
    }

    public function changePassword()
    {
        // Vérifier si l'utilisateur est connecté et doit changer son mot de passe
        if (!session()->get('must_change_password')) {
            return redirect()->to('/');
        }

        return view('auth/change_password');
    }

    public function doChangePassword()
    {
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'Tous les champs sont requis.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Les mots de passe ne correspondent pas.');
        }

        if (strlen($newPassword) < 4) {
            return redirect()->back()->with('error', 'Le mot de passe doit contenir au moins 4 caractères.');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);

        // Mettre à jour la session
        session()->remove('must_change_password');

        return redirect()->to('/')->with('success', 'Mot de passe modifié avec succès.');
    }

}