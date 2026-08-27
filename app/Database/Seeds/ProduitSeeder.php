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
            ['nom' => 'yaourt milky', 'duree_conservation' => 45],
            ['nom' => 'creme fraiche', 'duree_conservation' => 31],
            ['nom' => 'fromage fondu', 'duree_conservation' => 31],
            ['nom' => 'yaourt 1L', 'duree_conservation' => 31],
            ['nom' => 'yaourt 1/2L', 'duree_conservation' => 31],
            ['nom' => 'beurre', 'duree_conservation' => 60],
            ['nom' => 'fromage sous-vide', 'duree_conservation' => 90],
            ['nom' => 'fromage boule', 'duree_conservation' => 120],
            ['nom' => 'lait pasteurisé 1L', 'duree_conservation' => 10],
            ['nom' => 'yaourt aux fruits', 'duree_conservation' => 35],
            ['nom' => 'crème dessert', 'duree_conservation' => 21],
        ];

        foreach ($produits as $p) {
            $prix = $faker->numberBetween(1000, 15000);
            $prix = round($prix / 100) * 100;

            $this->db->table('produits')->insert([
                'nom'                => $p['nom'],
                'duree_conservation' => $p['duree_conservation'],
                'prix_vente'         => $prix,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
        }
    }
}