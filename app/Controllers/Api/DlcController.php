<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProduitModel;

class DlcController extends BaseController
{
    public function calculer()
    {
        try {
            // Récupérer les données JSON
            $data = $this->request->getJSON(true);
            
            // Validation
            if (!isset($data['produit_id']) || !isset($data['date_creation'])) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'produit_id et date_creation requis']);
            }

            $produit_id = (int) $data['produit_id'];
            $date_creation = $data['date_creation'];

            // Vérifier le produit
            $model = new ProduitModel();
            $produit = $model->find($produit_id);
            if (!$produit) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['error' => 'Produit introuvable']);
            }

            // Calculer la date de péremption
            $date = new \DateTime($date_creation);
            $date->modify('+' . (int) $produit['duree_conservation'] . ' days');
            $date_peremption = $date->format('Y-m-d');

            // Retourner le résultat
            return $this->response->setJSON([
                'date_peremption' => $date_peremption,
            ]);

        } catch (\Exception $e) {
            // Log et retourne l'erreur en JSON
            log_message('error', 'DLC calcul error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }
}