<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\MouvementService;
use App\Models\ProduitModel;
use App\Models\ClientModel;

class VenteApiController extends BaseController
{
    protected $mouvementService;
    protected $produitModel;
    protected $clientModel;

    public function __construct()
    {
        $this->mouvementService = new MouvementService();
        $this->produitModel = new ProduitModel();
        $this->clientModel = new ClientModel();
    }

    /**
     * Récupère l'historique des sorties (ventes + pertes)
     */
    public function historique()
    {
        $filters = [
            'type'  => 'sortie',
            'cause' => $this->request->getGet('cause') ?? '', // 'vente' ou 'non_conforme' ou vide = toutes
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
            'client_id'  => $this->request->getGet('client_id'),
        ];

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        // On force le type = sortie et la cause dans (vente, non_conforme)
        $filters['type'] = 'sortie';
        // Si cause est vide, on ne filtre pas par cause (toutes les sorties)
        // Si cause est spécifiée, on la garde

        $result = $this->mouvementService->getMouvements($filters, $page, $perPage);

        // Formater les données pour l'affichage
        foreach ($result['data'] as &$row) {
            $row['cause_label'] = $row['cause'] === 'vente' ? 'Vente' : 'Non conforme / Perte';
        }

        return $this->response->setJSON($result);
    }

    /**
     * Crée un nouveau mouvement (sortie)
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validation des champs obligatoires
        if (empty($data['produit_id']) || empty($data['quantite']) || empty($data['date_mouvement']) || empty($data['cause'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'produit_id, quantite, date_mouvement et cause sont requis']);
        }

        // Vérifier que le produit existe
        $produit = $this->produitModel->find($data['produit_id']);
        if (!$produit) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Produit introuvable']);
        }

        // Vérifier la quantité > 0
        if ((float) $data['quantite'] <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'La quantité doit être supérieure à 0']);
        }

        // Vérifier la cause
        if (!in_array($data['cause'], ['vente', 'non_conforme'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Cause invalide. Causes autorisées : vente, non_conforme']);
        }

        // Si cause = vente, client_id obligatoire
        if ($data['cause'] === 'vente' && empty($data['client_id'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Le client est obligatoire pour une vente']);
        }

        // Si cause = non_conforme, client_id forcé à null
        if ($data['cause'] === 'non_conforme') {
            $data['client_id'] = null;
        }

        // Vérifier que le client existe (si fourni)
        if (!empty($data['client_id'])) {
            $client = $this->clientModel->find($data['client_id']);
            if (!$client) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'Client introuvable']);
            }
        }

        // Construire le tableau de données pour le service
        $mouvementData = [
            'produit_id'     => (int) $data['produit_id'],
            'quantite'       => (float) $data['quantite'],
            'date_mouvement' => $data['date_mouvement'],
            'type'           => 'sortie',
            'cause'          => $data['cause'],
            'client_id'      => $data['client_id'] ?? null,
        ];

        try {
            $id = $this->mouvementService->createMouvement($mouvementData);
            return $this->response->setStatusCode(201)
                ->setJSON(['status' => 'ok', 'id' => $id, 'message' => 'Mouvement enregistré avec succès']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * Récupère les données pour le graphique (optionnel)
     */
    public function chart()
    {
        $filters = [
            'type' => 'sortie',
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
        ];
        $data = $this->mouvementService->getChartData($filters);
        return $this->response->setJSON($data);
    }

    /**
     * Export CSV de l'historique
     */
    public function exportCsv()
    {
        $filters = [
            'type'  => 'sortie',
            'cause' => $this->request->getGet('cause') ?? '',
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
            'client_id'  => $this->request->getGet('client_id'),
        ];
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $type = $this->request->getGet('type') ?? 'current';

        if ($type === 'all') {
            $result = $this->mouvementService->getMouvements($filters, 1, 10000);
        } else {
            $result = $this->mouvementService->getMouvements($filters, $page, $perPage);
        }
        $data = $result['data'];

        $filename = $this->request->getGet('filename') ?? 'sorties_export';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Type', 'Cause', 'Produit', 'Quantité', 'Client']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['date_mouvement'],
                $row['type'] === 'sortie' ? 'Sortie' : 'Entrée',
                $row['cause'] ?? '',
                $row['produit_nom'] ?? '',
                $row['quantite'],
                $row['client_nom'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    }
}