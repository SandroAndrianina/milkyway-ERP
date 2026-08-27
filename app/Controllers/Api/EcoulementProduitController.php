<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProduitModel;

class EcoulementProduitController extends BaseController
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

        if (empty($data['nom']) || empty($data['duree_conservation']) || !isset($data['prix_vente'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'nom, duree_conservation et prix_vente requis']);
        }

        $id = $this->model->creerProduit($data);

        return $this->response->setStatusCode(201)
            ->setJSON(['status' => 'ok', 'id' => $id]);
    }

    public function update($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['nom']) || empty($data['duree_conservation']) || !isset($data['prix_vente'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'nom, duree_conservation et prix_vente requis']);
        }

        $this->model->modifierProduitEcoulement($id, $data);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function delete($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $this->model->delete($id); // soft delete

        return $this->response->setJSON(['status' => 'ok']);
    }
}