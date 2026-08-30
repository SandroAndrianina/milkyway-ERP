<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\MouvementService;
use App\Models\ProduitModel;

class StockGestionController extends BaseController
{
    protected $mouvementService;
    protected $produitModel;

    public function __construct()
    {
        $this->mouvementService = new MouvementService();
        $this->produitModel = new ProduitModel();
    }

    /**
     * Récupère l'historique des mouvements de stock (entrées + sorties)
     * avec filtres : date, type, produit
     */
    public function historique()
    {
        $filters = [
            'type' => $this->request->getGet('type') ?? '', // 'entree' ou 'sortie' ou vide = tous
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
        ];

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        $result = $this->mouvementService->getMouvements($filters, $page, $perPage);

        return $this->response->setJSON($result);
    }

    /**
     * Crée un mouvement de stock (entrée ou sortie)
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validation des champs obligatoires
        if (empty($data['produit_id']) || empty($data['quantite']) || empty($data['date_mouvement']) || empty($data['type'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'produit_id, quantite, date_mouvement et type sont requis']);
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

        // Vérifier le type
        if (!in_array($data['type'], ['entree', 'sortie'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Type invalide. Types autorisés : entree, sortie']);
        }

        // Déterminer la cause selon le type
        $cause = ($data['type'] === 'entree') ? 'production' : 'non_conforme';

        // Construire les données pour le service
        $mouvementData = [
            'produit_id'     => (int) $data['produit_id'],
            'quantite'       => (float) $data['quantite'],
            'date_mouvement' => $data['date_mouvement'],
            'type'           => $data['type'],
            'cause'          => $cause,
            'client_id'      => null, // Pas de client en stock
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
     * Export CSV de l'historique
     */
    public function exportCsv()
    {
        $filters = [
            'type' => $this->request->getGet('type') ?? '',
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
        ];
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $type = $this->request->getGet('typeExport') ?? 'current';

        if ($type === 'all') {
            $result = $this->mouvementService->getMouvements($filters, 1, 10000);
        } else {
            $result = $this->mouvementService->getMouvements($filters, $page, $perPage);
        }
        $data = $result['data'];

        $filename = $this->request->getGet('filename') ?? 'stock_export';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Type', 'Produit', 'Quantité', 'Cause']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['date_mouvement'],
                $row['type'] === 'entree' ? 'Entrée' : 'Sortie',
                $row['produit_nom'] ?? '',
                $row['quantite'],
                $row['cause'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    }
}