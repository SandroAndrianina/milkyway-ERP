<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class MouvementSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('fr_FR');

        // Récupérer tous les produits existants
        $produits = $this->db->table('produits')->where('deleted_at', null)->get()->getResultArray();
        $produitIds = array_column($produits, 'id');

        // Récupérer tous les clients (non supprimés)
        $clients = $this->db->table('clients')->where('deleted_at', null)->get()->getResultArray();
        $clientIds = array_column($clients, 'id');

        // S'il n'y a pas de clients, on en crée quelques-uns via le seeder client
        if (empty($clientIds)) {
            // On appelle le seeder client pour en générer
            $this->call('ClientSeeder');
            // Recharger
            $clients = $this->db->table('clients')->where('deleted_at', null)->get()->getResultArray();
            $clientIds = array_column($clients, 'id');
        }

        // Générer des mouvements pour les 6 derniers mois
        $startDate = strtotime('-6 months');
        $endDate = time();

        // Pour chaque produit, créer 3-8 mouvements (mix entrées/sorties)
        foreach ($produitIds as $prodId) {
            $nbMouvements = rand(3, 8);
            $stock = 0; // suivi du stock pour éviter négatif

            for ($i = 0; $i < $nbMouvements; $i++) {
                // Date aléatoire entre startDate et endDate, triée croissante
                $date = date('Y-m-d', rand($startDate, $endDate));

                // Type : entrée ou sortie ? On favorise les sorties (ventes)
                $type = (rand(1, 10) <= 7) ? 'sortie' : 'entree';

                // Pour les sorties, on s'assure qu'il y a assez de stock
                if ($type === 'sortie' && $stock <= 0) {
                    // si stock vide, on force une entrée
                    $type = 'entree';
                }

                $quantite = 0;
                if ($type === 'entree') {
                    $quantite = rand(5, 50);
                    $stock += $quantite;
                } else { // sortie
                    $maxQuantite = min($stock, rand(1, 20));
                    if ($maxQuantite > 0) {
                        $quantite = $maxQuantite;
                        $stock -= $quantite;
                    } else {
                        // pas assez de stock, on saute ce mouvement
                        continue;
                    }
                }

                $cause = null;
                $clientId = null;
                if ($type === 'sortie') {
                    // 80% de chance d'être une vente, 20% non conforme
                    if (rand(1, 10) <= 8) {
                        $cause = 'vente';
                        $clientId = $faker->randomElement($clientIds);
                    } else {
                        $cause = 'non_conforme';
                        $clientId = null;
                    }
                }

                // Insérer le mouvement
                $this->db->table('mouvements')->insert([
                    'produit_id'     => $prodId,
                    'client_id'      => $clientId,
                    'type'           => $type,
                    'cause'          => $cause,
                    'quantite'       => $quantite,
                    'date_mouvement' => $date,
                    'created_at'     => $date . ' ' . $faker->time('H:i:s'),
                    'updated_at'     => $date . ' ' . $faker->time('H:i:s'),
                ]);
            }
        }
    }
}