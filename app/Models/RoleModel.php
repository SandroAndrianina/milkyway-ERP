<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nom', 'description'];
    protected $useTimestamps    = true;

    public function getRolesExceptAdmin(): array
    {
        return $this->where('nom !=', 'admin')
                    ->findAll();
    }

    public function findNonAdminRoleById(int $id)
    {
        return $this->where('id', $id)
                    ->where('nom !=', 'admin')
                    ->first();
    }

}