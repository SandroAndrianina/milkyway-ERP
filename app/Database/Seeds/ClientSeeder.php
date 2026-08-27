<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('fr_FR');

        // Liste de types de clients
        $types = ['Professionnel', 'Particulier', 'GMS', 'Artisan'];

        for ($i = 0; $i < 25; $i++) {
            $nom = $faker->company; // ou $faker->name pour particulier
            // Mélanger pour avoir des noms variés
            if ($i % 3 === 0) {
                $nom = $faker->name;
            }

            $this->db->table('clients')->insert([
                'nom'       => $nom,
                'contact'   => $faker->phoneNumber, // génère format 06 XX XX XX XX
                'adresse'   => $faker->address,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}