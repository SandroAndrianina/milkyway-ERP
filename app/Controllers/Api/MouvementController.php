<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\MouvementService;
use App\Services\ExportService;

class MouvementController extends BaseController
{
    protected $mouvementService;
    protected $exportService;

    public function __construct()
    {
        $this->mouvementService = new MouvementService();
        $this->exportService    = new ExportService();
    }

    // ========== LISTE + CHART ==========
    public function index()
    {
        $filters = $this->getFiltersFromRequest();
        $page  = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        $result = $this->mouvementService->getMouvements($filters, $page, $perPage);
        return $this->response->setJSON([
            'data'  => $result['data'],
            'total' => $result['total'],
        ]);
    }

    public function chart()
    {
        $filters = $this->getFiltersFromRequest();
        $data = $this->mouvementService->getChartData($filters);
        return $this->response->setJSON($data);
    }

    // ========== CRUD ==========
    public function show($id)
    {
        $mouvement = $this->mouvementService->getMouvement($id);
        if (!$mouvement) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Mouvement introuvable']);
        }
        return $this->response->setJSON($mouvement);
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            // Validation basique
            if (empty($data['produit_id']) || empty($data['type']) || empty($data['quantite']) || empty($data['date_mouvement'])) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'Champs requis manquants']);
            }

            // Si type = sortie et cause = vente, client_id doit être présent
            if ($data['type'] === 'sortie' && $data['cause'] === 'vente' && empty($data['client_id'])) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'Client requis pour une vente']);
            }

            $id = $this->mouvementService->createMouvement($data);
            
            return $this->response->setStatusCode(201)
                ->setJSON(['status' => 'ok', 'id' => $id]);

        } catch (\Exception $e) {
            // Capturer l'erreur de stock insuffisant
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function createBatch()
    {
        try {
            $items = $this->request->getJSON(true);
            
            if (!is_array($items) || empty($items)) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'Tableau de mouvements requis']);
            }

            $success = $this->mouvementService->createBatchMouvements($items);
            
            if ($success) {
                return $this->response->setJSON(['status' => 'ok', 'count' => count($items)]);
            }
            
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Erreur lors de l\'insertion en lot']);

        } catch (\Exception $e) {
            // Capturer l'erreur de stock insuffisant
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    // ========== EXPORT ==========
    public function exportPreview()
    {
        $filters = $this->getFiltersFromRequest();
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        // On récupère les données (sans pagination)
        $result = $this->mouvementService->getMouvements($filters, 1, $limit);
        return $this->response->setJSON($result['data']);
    }

    public function exportCsv()
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'mouvements_export';
        $type = $this->request->getGet('type') ?? 'current';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;

        if ($type === 'all') {
            $result = $this->mouvementService->getMouvements($filters, 1, 10000);
        } else {
            $result = $this->mouvementService->getMouvements($filters, $page, $perPage);
        }
        $data = $result['data'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Type', 'Produit', 'Quantité', 'Cause', 'Client']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['date_mouvement'],
                $row['type'],
                $row['produit_nom'] ?? '',
                $row['quantite'],
                $row['cause'] ?? '',
                $row['client_nom'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    }

    public function exportPdf()
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'mouvements_export';
        $type = $this->request->getGet('type') ?? 'current';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;

        if ($type === 'all') {
            $result = $this->mouvementService->getMouvements($filters, 1, 10000);
        } else {
            $result = $this->mouvementService->getMouvements($filters, $page, $perPage);
        }
        $data = $result['data'];

        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #084365; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>Mouvements de stock</h1>';
        $html .= '<p>Exporté le ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table><thead><tr><th>Date</th><th>Type</th><th>Produit</th><th>Quantité</th><th>Cause</th><th>Client</th></tr></thead><tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['date_mouvement'] . '</td>';
            $html .= '<td>' . $row['type'] . '</td>';
            $html .= '<td>' . ($row['produit_nom'] ?? '') . '</td>';
            $html .= '<td>' . $row['quantite'] . '</td>';
            $html .= '<td>' . ($row['cause'] ?? '') . '</td>';
            $html .= '<td>' . ($row['client_nom'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
        exit;
    }

    // ========== MÉTHODES UTILITAIRES ==========
    private function getFiltersFromRequest(): array
    {
        $filters = [];
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');
        $type      = $this->request->getGet('type');
        $cause     = $this->request->getGet('cause');
        $produit   = $this->request->getGet('produit_id');
        $client    = $this->request->getGet('client_id');

        if ($dateDebut) $filters['date_debut'] = $dateDebut;
        if ($dateFin)   $filters['date_fin']   = $dateFin;
        if ($type && $type !== 'tous')     $filters['type'] = $type;
        if ($cause && $cause !== 'toutes') $filters['cause'] = $cause;
        if ($produit) $filters['produit_id'] = $produit;
        if ($client)  $filters['client_id']  = $client;

        return $filters;
    }
}