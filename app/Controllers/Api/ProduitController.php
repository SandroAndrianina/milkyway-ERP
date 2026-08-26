<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProduitModel;

class ProduitController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProduitModel();
    }

    public function index()
    {
        return $this->response->setJSON($this->model->findAll());
    }

    public function show($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        return $this->response->setJSON($produit);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['nom']) || empty($data['duree_conservation'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'nom et duree_conservation requis']);
        }

        $this->model->insert($data);

        return $this->response->setStatusCode(201)
            ->setJSON(['status' => 'ok', 'id' => $this->model->getInsertID()]);
    }

    public function update($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function delete($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $this->model->delete($id); // soft delete grâce à $useSoftDeletes

        return $this->response->setJSON(['status' => 'ok']);
    }
}