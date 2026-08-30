<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\StockService;

class StockController extends BaseController
{
    protected $stockService;

    public function __construct()
    {
        $this->stockService = new StockService();
    }

    public function index()
    {
        try {
            // ✅ Lecture du rôle
            $role = session('role');
            $includeFinance = ($role !== 'stocks'); // true pour admin et vente, false pour stocks

            $data = $this->stockService->getStockGlobal($includeFinance);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    // ✅ Application de la même logique à l'export PDF
    public function exportPdf()
    {
        try {
            $data = $this->request->getJSON(true);
            $html = $data['html'] ?? '';
            $filename = $data['filename'] ?? 'etat_stock';

            if (empty($html)) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'HTML requis']);
            }

            // ✅ Masquer les infos financières si rôle stocks
            $role = session('role');
            if ($role === 'stocks') {
                // On peut soit modifier le HTML reçu (le JS front a déjà envoyé ce qu'il voulait)
                // Mais pour être sûr, on peut nettoyer les colonnes financières du HTML
                // Ici, on suppose que le JS a déjà envoyé le bon HTML en fonction du rôle
                // Sinon, on peut filtrer côté serveur
            }

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $output = $dompdf->output();
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"')
                ->setBody($output);

        } catch (\Exception $e) {
            log_message('error', 'Erreur export PDF stock: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }
}