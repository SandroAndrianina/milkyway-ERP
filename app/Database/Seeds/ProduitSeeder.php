<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ProduitSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('fr_FR');

        $produits = [
            ['nom' => 'yaourt milky', 'duree_conservation' => 45, 'seuil' => 30],
            ['nom' => 'creme fraiche', 'duree_conservation' => 31, 'seuil' => 20],
            ['nom' => 'fromage fondu', 'duree_conservation' => 31, 'seuil' => 15],
            ['nom' => 'yaourt 1L', 'duree_conservation' => 31, 'seuil' => 40],
            ['nom' => 'yaourt 1/2L', 'duree_conservation' => 31, 'seuil' => 40],
            ['nom' => 'beurre', 'duree_conservation' => 60, 'seuil' => 25],
            ['nom' => 'fromage sous-vide', 'duree_conservation' => 90, 'seuil' => 10],
            ['nom' => 'fromage boule', 'duree_conservation' => 120, 'seuil' => 8],
            ['nom' => 'lait pasteurisé 1L', 'duree_conservation' => 10, 'seuil' => 50],
            ['nom' => 'yaourt aux fruits', 'duree_conservation' => 35, 'seuil' => 30],
            ['nom' => 'crème dessert', 'duree_conservation' => 21, 'seuil' => 20],
        ];

        foreach ($produits as $p) {
            $prix = $faker->numberBetween(1000, 15000);
            $prix = round($prix / 100) * 100;

            $this->db->table('produits')->insert([
                'nom'                => $p['nom'],
                'duree_conservation' => $p['duree_conservation'],
                'prix_vente'         => $prix,
                'seuil_critique'     => $p['seuil'], // ← nouveau champ
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
        }
    }
}