<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProduitSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'nom' => 'yaourt milky',      'duree_conservation' => 45,  'prix_vente' => null, 'created_at' => '2026-08-26 19:15:46', 'updated_at' => '2026-08-26 19:28:07', 'deleted_at' => null],
            ['id' => 2, 'nom' => 'creme fraiche',     'duree_conservation' => 31,  'prix_vente' => null, 'created_at' => '2026-08-26 19:16:11', 'updated_at' => '2026-08-26 19:17:24', 'deleted_at' => '2026-08-26 19:17:24'],
            ['id' => 3, 'nom' => 'creme fraiche',     'duree_conservation' => 31,  'prix_vente' => null, 'created_at' => '2026-08-26 19:18:55', 'updated_at' => '2026-08-26 19:18:55', 'deleted_at' => null],
            ['id' => 4, 'nom' => 'fromage fondu',     'duree_conservation' => 31,  'prix_vente' => null, 'created_at' => '2026-08-26 19:31:31', 'updated_at' => '2026-08-26 19:31:31', 'deleted_at' => null],
            ['id' => 5, 'nom' => 'yaourt 1L',         'duree_conservation' => 31,  'prix_vente' => null, 'created_at' => '2026-08-26 19:37:41', 'updated_at' => '2026-08-26 19:37:41', 'deleted_at' => null],
            ['id' => 6, 'nom' => 'yaourt 1/2L',       'duree_conservation' => 31,  'prix_vente' => null, 'created_at' => '2026-08-26 19:38:08', 'updated_at' => '2026-08-26 19:38:08', 'deleted_at' => null],
            ['id' => 7, 'nom' => 'beurre',            'duree_conservation' => 60,  'prix_vente' => null, 'created_at' => '2026-08-26 19:38:29', 'updated_at' => '2026-08-26 19:38:29', 'deleted_at' => null],
            ['id' => 8, 'nom' => 'fromage sous-vide', 'duree_conservation' => 90,  'prix_vente' => null, 'created_at' => '2026-08-26 19:38:56', 'updated_at' => '2026-08-26 19:38:56', 'deleted_at' => null],
            ['id' => 9, 'nom' => 'fromage boule',     'duree_conservation' => 120, 'prix_vente' => null, 'created_at' => '2026-08-26 19:39:19', 'updated_at' => '2026-08-26 19:39:19', 'deleted_at' => null],
        ];

        $this->db->table('produits')->insertBatch($data);
    }
}