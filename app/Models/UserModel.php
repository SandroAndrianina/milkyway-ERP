<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nom', 'password', 'role_id', 'status', 'must_change_password'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getRole()
    {
        return $this->db->table('roles')
                        ->where('id', $this->role_id)
                        ->get()
                        ->getRow();
    }

    // Récupère le nom du rôle
    public function getRoleName(int $userId): ?string
    {
        $user = $this->find($userId);
        if (!$user) return null;

        $role = $this->db->table('roles')
                         ->where('id', $user['role_id'])
                         ->get()
                         ->getRow();

        return $role ? $role->nom : null;
    }

    // Récupère les utilisateurs avec leur rôle
    public function getUsersWithRoles(): array
    {
        return $this->db->table('users')
                        ->select('users.*, roles.nom as role_nom')
                        ->join('roles', 'roles.id = users.role_id')
                        ->get()
                        ->getResultArray();
    }

    // Génère un mot de passe aléatoire
    public function generateRandomPassword(int $length = 4): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($chars), 0, $length);
    }

    // Réinitialise le mot de passe
    public function resetPassword(int $userId): string
    {
        $newPassword = $this->generateRandomPassword();
        $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);
        return $newPassword;
    }
}