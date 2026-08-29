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
            $data = $this->stockService->getStockGlobal();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

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

            // Générer le PDF avec Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Retourner le PDF en téléchargement
            $output = $dompdf->output();
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"')
                ->setBody($output);

        } catch (\Exception $e) {
            log_message('error', 'Erreur export PDF stock: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()]);
        }
    }
}