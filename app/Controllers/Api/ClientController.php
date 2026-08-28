<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ClientModel;

class ClientController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ClientModel();
    }

    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $search = $this->request->getGet('search') ?? '';

        $builder = $this->model->builder();
        if ($search) {
            $builder->like('nom', $search);
        }
        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('id', 'DESC')
                        ->limit($perPage, ($page - 1) * $perPage)
                        ->get()
                        ->getResultArray();

        return $this->response->setJSON([
            'data' => $data,
            'total' => $total,
            'stats' => [
                'total' => $total,
                'active' => $total,
                'new_7d' => 0,
                'last_added' => null,
            ],
        ]);
    }

    // Les autres méthodes (show, create, update, delete) restent inchangées
    public function show($id)
    {
        $client = $this->model->find($id);
        if (!$client) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Client introuvable']);
        }
        return $this->response->setJSON($client);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (empty($data['nom']) || empty($data['contact'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'nom et contact requis']);
        }
        $this->model->insert($data);
        return $this->response->setStatusCode(201)
            ->setJSON(['status' => 'ok', 'id' => $this->model->getInsertID()]);
    }

    public function update($id)
    {
        $client = $this->model->find($id);
        if (!$client) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Client introuvable']);
        }
        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function delete($id)
    {
        $client = $this->model->find($id);
        if (!$client) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Client introuvable']);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function achats($clientId)
    {
        try {
            $service = new \App\Services\ClientService();

            $dateDebut = $this->request->getGet('date_debut');
            $dateFin   = $this->request->getGet('date_fin');
            $period    = $this->request->getGet('period') ?? 'month';
            $page      = (int) ($this->request->getGet('page') ?? 1);
            $perPage   = (int) ($this->request->getGet('perPage') ?? 10);

            // ⚠️ Si pas de dates personnalisées, on applique le period
            if (empty($dateDebut) && empty($dateFin)) {
                if ($period === 'week') {
                    $dateDebut = date('Y-m-d', strtotime('-7 days'));
                } elseif ($period === 'month') {
                    $dateDebut = date('Y-m-d', strtotime('-30 days'));
                }
                // 'all' → pas de filtre
            }

            $filters = [];
            if ($dateDebut) $filters['date_debut'] = $dateDebut;
            if ($dateFin)   $filters['date_fin']   = $dateFin;

            $result = $service->getClientWithAchats($clientId, $filters, $page, $perPage);

            return $this->response->setJSON([
                'data'          => $result['achats'],
                'total'         => $result['total_achats'],
                'total_achete'  => $result['total_achete'],
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function exportPreview()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $offset = ($page - 1) * $limit;
        $clients = $this->model->orderBy('id', 'DESC')->limit($limit, $offset)->findAll();
        return $this->response->setJSON($clients);
    }

    public function exportCsv()
    {
        $filename = $this->request->getGet('filename') ?? 'clients_export';
        $type = $this->request->getGet('type') ?? 'current';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10; // ou une constante

        if ($type === 'all') {
            $clients = $this->model->orderBy('id', 'DESC')->findAll();
        } else {
            $offset = ($page - 1) * $perPage;
            $clients = $this->model->orderBy('id', 'DESC')->limit($perPage, $offset)->findAll();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nom', 'Contact', 'Adresse', 'Créé le']);
        foreach ($clients as $row) {
            fputcsv($output, [$row['id'], $row['nom'], $row['contact'], $row['adresse'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function exportPdf()
    {
        $filename = $this->request->getGet('filename') ?? 'clients_export';
        $type = $this->request->getGet('type') ?? 'current';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;

        if ($type === 'all') {
            $clients = $this->model->orderBy('id', 'DESC')->findAll();
        } else {
            $offset = ($page - 1) * $perPage;
            $clients = $this->model->orderBy('id', 'DESC')->limit($perPage, $offset)->findAll();
        }

        // Générer HTML pour le PDF
        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>Liste des clients</h1>';
        $html .= '<table><thead><tr><th>ID</th><th>Nom</th><th>Contact</th><th>Adresse</th><th>Créé le</th></tr></thead><tbody>';
        foreach ($clients as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['id'] . '</td>';
            $html .= '<td>' . $row['nom'] . '</td>';
            $html .= '<td>' . ($row['contact'] ?? '-') . '</td>';
            $html .= '<td>' . ($row['adresse'] ?? '-') . '</td>';
            $html .= '<td>' . $row['created_at'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        // Utiliser Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
        exit;
    }

    public function exportAchatsPreview($clientId)
{
    $type = $this->request->getGet('type') ?? 'current';
    $page = (int) ($this->request->getGet('page') ?? 1);
    $perPage = 10;

    $filters = [];
    $dateDebut = $this->request->getGet('date_debut');
    $dateFin = $this->request->getGet('date_fin');
    if ($dateDebut) $filters['date_debut'] = $dateDebut;
    if ($dateFin) $filters['date_fin'] = $dateFin;

    $service = new \App\Services\ClientService();
    $result = $service->getClientWithAchats($clientId, $filters, $page, $perPage);
    $achats = $result['achats'];

    if ($type === 'all') {
        // On récupère toutes les pages (sans limite)
        $resultAll = $service->getClientWithAchats($clientId, $filters, 1, 10000);
        $achats = $resultAll['achats'];
    }

    return $this->response->setJSON($achats);
}

public function exportAchatsCsv($clientId)
{
    $filename = $this->request->getGet('filename') ?? 'achats_client';
    $type = $this->request->getGet('type') ?? 'current';
    $page = (int) ($this->request->getGet('page') ?? 1);
    $perPage = 10;

    $filters = [];
    $dateDebut = $this->request->getGet('date_debut');
    $dateFin = $this->request->getGet('date_fin');
    if ($dateDebut) $filters['date_debut'] = $dateDebut;
    if ($dateFin) $filters['date_fin'] = $dateFin;

    $service = new \App\Services\ClientService();
    if ($type === 'all') {
        $result = $service->getClientWithAchats($clientId, $filters, 1, 10000);
    } else {
        $result = $service->getClientWithAchats($clientId, $filters, $page, $perPage);
    }
    $achats = $result['achats'];

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Produit', 'Quantité', 'Prix unitaire', 'Total']);
    foreach ($achats as $row) {
        fputcsv($output, [
            $row['date'],
            $row['produit_nom'],
            $row['quantite'],
            $row['prix_unitaire'] . ' Ar',
            $row['total'] . ' Ar'
        ]);
    }
    fclose($output);
    exit;
}

public function exportAchatsPdf($clientId)
{
    $filename = $this->request->getGet('filename') ?? 'achats_client';
    $type = $this->request->getGet('type') ?? 'current';
    $page = (int) ($this->request->getGet('page') ?? 1);
    $perPage = 10;

    $filters = [];
    $dateDebut = $this->request->getGet('date_debut');
    $dateFin = $this->request->getGet('date_fin');
    if ($dateDebut) $filters['date_debut'] = $dateDebut;
    if ($dateFin) $filters['date_fin'] = $dateFin;

    $service = new \App\Services\ClientService();
    if ($type === 'all') {
        $result = $service->getClientWithAchats($clientId, $filters, 1, 10000);
    } else {
        $result = $service->getClientWithAchats($clientId, $filters, $page, $perPage);
    }
    $achats = $result['achats'];

    // Récupérer le client
    $client = (new \App\Models\ClientModel())->find($clientId);

    // Générer HTML
    $html = '<html><head><meta charset="utf-8"><style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #084365; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total { font-weight: bold; }
    </style></head><body>';
    $html .= '<h1>Historique des achats - ' . ($client['nom'] ?? 'Client') . '</h1>';
    $html .= '<p>Exporté le ' . date('d/m/Y H:i') . '</p>';
    $html .= '<table><thead><tr><th>Date</th><th>Produit</th><th class="text-right">Qté</th><th class="text-right">Prix unit.</th><th class="text-right">Total</th></tr></thead><tbody>';
    foreach ($achats as $row) {
        $html .= '<tr>';
        $html .= '<td>' . $row['date'] . '</td>';
        $html .= '<td>' . $row['produit_nom'] . '</td>';
        $html .= '<td class="text-right">' . $row['quantite'] . '</td>';
        $html .= '<td class="text-right">' . $row['prix_unitaire'] . ' Ar</td>';
        $html .= '<td class="text-right">' . $row['total'] . ' Ar</td>';
        $html .= '</tr>';
    }
    // Ligne de total
    $totalAchete = array_sum(array_column($achats, 'total'));
    $html .= '<tr><td colspan="4" class="text-right total">Total général</td><td class="text-right total">' . $totalAchete . ' Ar</td></tr>';
    $html .= '</tbody></table></body></html>';

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
    exit;
}
}