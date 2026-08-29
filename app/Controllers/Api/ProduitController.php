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

    // create() supprimé — la création se fait uniquement côté Écoulement

    public function update($id)
    {
        $produit = $this->model->find($id);

        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['duree_conservation'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'duree_conservation requis']);
        }

        $this->model->modifierDureeDlc($id, (int) $data['duree_conservation']);

        return $this->response->setJSON(['status' => 'ok']);
    }

    // delete() supprimé — DLC ne supprime pas de produit
}