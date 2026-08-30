<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nom', 'password', 'role_id', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    public function getRole()
    {
        return $this->db->table('roles')
                        ->where('id', $this->role_id)
                        ->get()
                        ->getRow();
    }

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
}