<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\RecapService;

class RecapController extends BaseController
{
    protected $recapService;

    public function __construct()
    {
        $this->recapService = new RecapService();
    }

    public function evolution()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin = $this->request->getGet('date_fin');
        $granularite = $this->request->getGet('granularite') ?? 'day';

        if (empty($dateDebut) || empty($dateFin)) {
            // Par défaut : dernière semaine
            $dateFin = date('Y-m-d');
            $dateDebut = date('Y-m-d', strtotime('-7 days'));
        }

        $data = $this->recapService->getVentesEvolution($dateDebut, $dateFin, $granularite);
        return $this->response->setJSON($data);
    }

    public function clients()
    {
        $dateDebut = $this->request->getGet('date_debut') ?? date('Y-m-d', strtotime('-7 days'));
        $dateFin = $this->request->getGet('date_fin') ?? date('Y-m-d');

        $data = $this->recapService->getVentesParClient($dateDebut, $dateFin);
        return $this->response->setJSON($data);
    }

    public function produits()
    {
        $dateDebut = $this->request->getGet('date_debut') ?? date('Y-m-d', strtotime('-7 days'));
        $dateFin = $this->request->getGet('date_fin') ?? date('Y-m-d');

        $data = $this->recapService->getVentesParProduit($dateDebut, $dateFin);
        return $this->response->setJSON($data);
    }

    public function exportPdf()
    {
        try {
            $data = $this->request->getJSON(true);
            $html = $data['html'] ?? '';
            $filename = $data['filename'] ?? 'recapitulation';

            if (empty($html)) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'HTML requis']);
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
            log_message('error', 'Erreur export PDF recap: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }
}