<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;

class SyncController extends BaseController
{
    public function push()
    {
        $data = $this->request->getJSON(true);
        $mouvements = $data['mouvements'] ?? [];
        $mouvementModel = new \App\Models\MouvementModel();
        $produitModel = new \App\Models\ProduitModel();

        $resultats = [];

        foreach ($mouvements as $m) {
            // 1. Vérifier l'unicité de l'UUID
            if ($mouvementModel->where('uuid', $m['uuid'])->first()) {
                $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'deja_synchronise'];
                continue;
            }

            // 2. Valider que le produit existe et n'est pas supprimé
            $produit = $produitModel
                ->where('id', $m['produit_id'])
                ->where('deleted_at', null)  // ou is_deleted = 0 selon ta table
                ->first();

            if (!$produit) {
                log_message('error', "Produit invalide pour mouvement {$m['uuid']}: produit_id {$m['produit_id']}");
                $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'rejete', 'raison' => 'produit_invalide'];
                continue;
            }

            // 3. Insérer le mouvement
            $insertData = [
                'uuid'           => $m['uuid'],
                'produit_id'     => $m['produit_id'],
                'client_id'      => $m['client_id'] ?? null,
                'type'           => $m['type'],           // 'entree' ou 'sortie'
                'cause'          => $m['cause'] ?? null,  // 'vente' ou 'non_conforme'
                'quantite'       => $m['quantite'],
                'date_mouvement' => $m['date_mouvement'] ?? date('Y-m-d'),
                'user_id'        => $this->request->user_id, // injecté par le filtre JWT
            ];

            $mouvementModel->insert($insertData);
            $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'ok'];
        }

        return $this->response->setJSON(['resultats' => $resultats]);
    }

    public function pull()
    {
        $since = $this->request->getGet('since') ?? '1970-01-01 00:00:00';

        $produitModel = new \App\Models\ProduitModel();
        $clientModel  = new \App\Models\ClientModel();

        // On suppose que ces tables ont une colonne `updated_at`
        return $this->response->setJSON([
            'produits'    => $produitModel->where('updated_at >=', $since)->findAll(),
            'clients'     => $clientModel->where('updated_at >=', $since)->findAll(),
            'server_time' => date('Y-m-d H:i:s'),
        ]);
    }
}