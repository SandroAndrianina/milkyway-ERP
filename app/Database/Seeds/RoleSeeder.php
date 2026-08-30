<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['nom' => 'vente', 'description' => 'Accès aux ventes et clients'],
            ['nom' => 'stocks', 'description' => 'Accès à la gestion des stocks'],
            ['nom' => 'admin', 'description' => 'Accès complet (admin)'],
        ];

        foreach ($roles as $role) {
            $this->db->table('roles')->insert($role);
        }
    }
}