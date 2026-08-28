<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Services\ClientService;
use App\Services\ExportService;

class ClientController extends BaseController
{
    protected $model;
    protected $clientService;
    protected $exportService;

    public function __construct()
    {
        $this->model = new ClientModel();
        $this->clientService = new ClientService();
        $this->exportService = new ExportService();
    }

    public function index()
    {
        $all = $this->request->getGet('all');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $search = $this->request->getGet('search') ?? '';

        $builder = $this->model->builder();
        if ($search) {
            $builder->like('nom', $search);
        }
        $total = $builder->countAllResults(false);
        
        if ($all) {
            $data = $builder->get()->getResultArray();
        } else {
            $data = $builder->orderBy('id', 'DESC')
                            ->limit($perPage, ($page - 1) * $perPage)
                            ->get()
                            ->getResultArray();
        }

        return $this->response->setJSON([
            'data'  => $data,
            'total' => $total,
            'stats' => [
                'total'     => $total,
                'active'    => $total,
                'new_7d'    => 0,
                'last_added' => null,
            ],
        ]);
    }

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

    // ========================================
    // ACHATS
    // ========================================

    public function achats($clientId)
    {
        try {
            $filters = [];
            $dateDebut = $this->request->getGet('date_debut');
            $dateFin = $this->request->getGet('date_fin');
            $period = $this->request->getGet('period') ?? 'month';

            if (empty($dateDebut) && empty($dateFin)) {
                if ($period === 'week') {
                    $filters['date_debut'] = date('Y-m-d', strtotime('-7 days'));
                } elseif ($period === 'month') {
                    $filters['date_debut'] = date('Y-m-d', strtotime('-30 days'));
                }
            } else {
                if ($dateDebut) $filters['date_debut'] = $dateDebut;
                if ($dateFin)   $filters['date_fin']   = $dateFin;
            }

            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('perPage') ?? 10);

            // ✅ Utiliser $this->clientService
            $result = $this->clientService->getClientWithAchats($clientId, $filters, $page, $perPage);

            return $this->response->setJSON([
                'data'          => $result['achats'],
                'total'         => $result['total_achats'],
                'total_achete'  => $result['total_achete'],
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur achats client: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    // ========================================
    // EXPORT CLIENTS
    // ========================================

    public function exportPreview()
    {
        $filters = $this->getFiltersFromRequest();
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $data = $this->exportService->getClientsPreview($filters, $limit);
        return $this->response->setJSON($data);
    }

    public function exportCsv()
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'clients_export';
        $this->exportService->exportClientsCsv($filters, $filename);
    }

    public function exportPdf()
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'clients_export';
        $this->exportService->exportClientsPdf($filters, $filename);
    }

    // ========================================
    // EXPORT ACHATS CLIENT
    // ========================================

    public function exportAchatsPreview($clientId)
    {
        $filters = $this->getFiltersFromRequest();
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $data = $this->exportService->getAchatsPreview($clientId, $filters, $limit);
        return $this->response->setJSON($data);
    }

    public function exportAchatsCsv($clientId)
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'achats_client';
        $this->exportService->exportAchatsCsv($clientId, $filters, $filename);
    }

    public function exportAchatsPdf($clientId)
    {
        $filters = $this->getFiltersFromRequest();
        $filename = $this->request->getGet('filename') ?? 'achats_client';
        $this->exportService->exportAchatsPdf($clientId, $filters, $filename);
    }

    // ========================================
    // PRIVÉ
    // ========================================

    private function getFiltersFromRequest(): array
    {
        $filters = [];
        $search = $this->request->getGet('search');
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin = $this->request->getGet('date_fin');
        $period = $this->request->getGet('period');

        if ($search) {
            $filters['search'] = $search;
        }

        // Gestion du period
        if (empty($dateDebut) && empty($dateFin)) {
            if ($period === 'week') {
                $filters['date_debut'] = date('Y-m-d', strtotime('-7 days'));
            } elseif ($period === 'month') {
                $filters['date_debut'] = date('Y-m-d', strtotime('-30 days'));
            }
        }

        if ($dateDebut) {
            $filters['date_debut'] = $dateDebut;
        }
        if ($dateFin) {
            $filters['date_fin'] = $dateFin;
        }

        return $filters;
    }
}