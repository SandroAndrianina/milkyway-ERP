<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProduitModel;
use App\Services\DlcService;  // ← importer le service

class DlcController extends BaseController
{
    protected $dlcService;

    public function __construct(DlcService $dlcService)
    {
        $this->dlcService = $dlcService;
    }

    public function calculer()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!isset($data['produit_id']) || !isset($data['date_creation'])) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'produit_id et date_creation requis']);
            }

            $produit_id = (int) $data['produit_id'];
            $date_creation = $data['date_creation'];

            $model = new ProduitModel();
            $produit = $model->find($produit_id);
            if (!$produit) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['error' => 'Produit introuvable']);
            }

            // ✅ Appel du service
            $date_peremption = $this->dlcService->calculerPeremption(
                (int) $produit['duree_conservation'],
                $date_creation
            );

            return $this->response->setJSON([
                'date_peremption' => $date_peremption,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'DLC calcul error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }
}