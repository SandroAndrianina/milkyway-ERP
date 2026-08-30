<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les rôles
        $adminRole = $this->db->table('roles')->where('nom', 'admin')->get()->getRow();
        $venteRole = $this->db->table('roles')->where('nom', 'vente')->get()->getRow();
        $stocksRole = $this->db->table('roles')->where('nom', 'stocks')->get()->getRow();

        // Si les rôles n'existent pas, les créer via RoleSeeder
        if (!$adminRole || !$venteRole || !$stocksRole) {
            $this->call('RoleSeeder');
            // Recharger les rôles
            $adminRole = $this->db->table('roles')->where('nom', 'admin')->get()->getRow();
            $venteRole = $this->db->table('roles')->where('nom', 'vente')->get()->getRow();
            $stocksRole = $this->db->table('roles')->where('nom', 'stocks')->get()->getRow();
        }

        // 1. Admin
        $this->db->table('users')->insert([
            'nom'       => 'Andry',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
            'role_id'   => $adminRole->id,
            'status'    => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Utilisateur Vente
        $this->db->table('users')->insert([
            'nom'       => 'Vendeur',
            'password'  => password_hash('vente123', PASSWORD_DEFAULT),
            'role_id'   => $venteRole->id,
            'status'    => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 3. Utilisateur Stocks
        $this->db->table('users')->insert([
            'nom'       => 'Gestionnaire Stock',
            'password'  => password_hash('stock123', PASSWORD_DEFAULT),
            'role_id'   => $stocksRole->id,
            'status'    => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 4. Utilisateur en attente (pending) pour tester la validation
        $this->db->table('users')->insert([
            'nom'       => 'En Attente',
            'password'  => password_hash('pending123', PASSWORD_DEFAULT),
            'role_id'   => $venteRole->id,
            'status'    => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}