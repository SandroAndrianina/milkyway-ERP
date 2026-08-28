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
        $clients = $this->model->findAll();
        return $this->response->setJSON([
            'data' => $clients,
            'total' => count($clients),
            'stats' => [
                'total' => count($clients),
                'active' => count($clients), // si pas de logique, on met total
                'new_7d' => 0,
                'last_added' => null
            ]
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

            $filters = [
                'date_debut' => $this->request->getGet('date_debut'),
                'date_fin'   => $this->request->getGet('date_fin'),
            ];
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('perPage') ?? 10);

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
}