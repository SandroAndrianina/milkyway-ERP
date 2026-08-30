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

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }

        // Vérifier le statut
        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Votre compte est en attente de validation ou désactivé.');
        }

        // Récupérer le nom du rôle
        $role = $userModel->getRoleName($user['id']);

        // Stocker en session
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
        return view('auth/register');
    }

    public function doRegister()
    {
        $nom = $this->request->getPost('nom');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if (empty($nom) || empty($password)) {
            return redirect()->back()->with('error', 'Tous les champs sont requis.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Les mots de passe ne correspondent pas.');
        }

        $userModel = new UserModel();

        // Vérifier si le nom existe déjà
        if ($userModel->where('nom', $nom)->first()) {
            return redirect()->back()->with('error', 'Ce nom d\'utilisateur est déjà pris.');
        }

        // Récupérer le rôle "vente" par défaut (le plus basique)
        $role = $this->db->table('roles')->where('nom', 'vente')->get()->getRow();

        $userModel->insert([
            'nom'      => $nom,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => $role->id,
            'status'   => 'pending',
        ]);

        return redirect()->to('/login')->with('success', 'Inscription réussie. En attente de validation par l\'admin.');
    }
    
}