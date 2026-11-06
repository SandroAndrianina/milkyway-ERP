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
        if ((int) $data['quantite'] <= 0) {
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
            'quantite'       => (int) $data['quantite'],
            'date_mouvement' => $data['date_mouvement'],
            'type'           => $data['type'],
            'cause'          => $cause,
            'client_id'      => null, // Pas de client en stock
            'prix_unitaire'  => (float) ($produit['prix_vente'] ?? 0),
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

    public function exportPdf()
    {
        $filters = [
            'type'       => $this->request->getGet('type') ?? '',
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
        ];
        $filename   = $this->request->getGet('filename') ?? 'stock_export';
        $typeExport = $this->request->getGet('typeExport') ?? 'current';
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = (int) ($this->request->getGet('perPage') ?? 10);

        $result = $typeExport === 'all'
            ? $this->mouvementService->getMouvements($filters, 1, 10000)
            : $this->mouvementService->getMouvements($filters, $page, $perPage);

        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #084365; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background: #f2f2f2; }
        </style></head><body><h1>Mouvements de stock</h1>
        <p>Exporté le ' . date('d/m/Y H:i') . '</p>
        <table><thead><tr><th>Date</th><th>Type</th><th>Produit</th><th>Quantité</th><th>Cause</th></tr></thead><tbody>';

        foreach ($result['data'] as $row) {
            $html .= '<tr><td>' . esc($row['date_mouvement']) . '</td>'
                . '<td>' . ($row['type'] === 'entree' ? 'Entrée' : 'Sortie') . '</td>'
                . '<td>' . esc($row['produit_nom'] ?? '') . '</td>'
                . '<td>' . esc($row['quantite']) . '</td>'
                . '<td>' . esc($row['cause'] ?? '') . '</td></tr>';
        }
        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"')
            ->setBody($dompdf->output());
    }

    public function exportPreview()
    {
        $filters = [
            'type'       => $this->request->getGet('type') ?? '',
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin'   => $this->request->getGet('date_fin'),
            'produit_id' => $this->request->getGet('produit_id'),
        ];
        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = (int) ($this->request->getGet('limit') ?? 10);

        $result = $this->mouvementService->getMouvements($filters, $page, $limit);
        return $this->response->setJSON($result['data']);
    }
}