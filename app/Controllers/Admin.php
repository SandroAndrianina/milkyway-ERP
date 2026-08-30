<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    public function users()
    {
        $userModel = new UserModel();
        $users = $userModel->where('status', 'pending')->findAll();

        return view('admin/users', ['users' => $users]);
    }

    public function validateUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur introuvable.');
        }

        $userModel->update($id, ['status' => 'active']);

        return redirect()->to('/admin/users')->with('success', 'Compte validé.');
    }
}