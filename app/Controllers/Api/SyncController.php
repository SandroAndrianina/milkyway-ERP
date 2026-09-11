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
            ->where('deleted_at', null)
            ->first();

        if (!$produit) {
            log_message('error', "Produit invalide pour mouvement {$m['uuid']}: produit_id {$m['produit_id']}");
            $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'rejete', 'raison' => 'produit_invalide'];
            continue;
        }

        // 3. Valider le prix
        if (empty($m['prix_unitaire']) || !is_numeric($m['prix_unitaire']) || $m['prix_unitaire'] <= 0) {
            log_message('error', "Prix invalide pour mouvement {$m['uuid']}: " . ($m['prix_unitaire'] ?? 'null'));
            $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'rejete', 'raison' => 'prix_invalide'];
            continue;
        }

        // 4. Insérer le mouvement
        $insertData = [
            'uuid'           => $m['uuid'],
            'produit_id'     => $m['produit_id'],
            'client_id'      => $m['client_id'] ?? null,
            'type'           => $m['type'],
            'cause'          => $m['cause'] ?? null,
            'quantite'       => $m['quantite'],
            'prix_unitaire'  => $m['prix_unitaire'],
            'date_mouvement' => $m['date_mouvement'] ?? date('Y-m-d'),
            'user_id'        => $this->request->user_id,
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