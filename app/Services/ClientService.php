<?php

namespace App\Services;

use App\Models\ClientModel;
use App\Models\MouvementModel;

class ClientService
{
    protected $clientModel;
    protected $mouvementModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->mouvementModel = new MouvementModel();
    }

    public function getClientWithAchats(int $clientId, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            throw new \Exception('Client introuvable');
        }

        $achatsData = $this->mouvementModel->getAchatsClient($clientId, $filters, $page, $perPage);
        $totalAchete = $this->mouvementModel->getTotalAchatsClient($clientId, $filters);

        $formatted = array_map(function ($mvt) {
            $prixUnitaire = $mvt['prix_vente'] ?? 0;
            return [
                'date'          => date('d M Y', strtotime($mvt['date_mouvement'])),
                'produit_nom'   => $mvt['produit_nom'] ?? 'Produit inconnu',
                'quantite'      => $mvt['quantite'],
                'prix_unitaire' => $prixUnitaire,
                'total'         => $mvt['quantite'] * $prixUnitaire,
            ];
        }, $achatsData['data']);

        return [
            'client' => $client,
            'achats' => $formatted,
            'total_achats' => $achatsData['total'],
            'total_achete' => $totalAchete,
        ];
    }

    /**
     * Retourne les statistiques globales des clients
     */
    public function getStats(): array
    {
        return [
            'total'      => $this->clientModel->countAllResults(),
            'active'     => $this->clientModel->getActiveClientsCount(30),
            'new_7d'     => $this->clientModel->getNewClientsCount(7),
            'last_added' => $this->clientModel->getLastAddedClient(),
        ];
    }

}