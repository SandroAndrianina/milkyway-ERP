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
        ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
        $data = $this->request->getPost();
        $file = $this->request->getFile('image');

        // === LOGS DE DÉBOGAGE ===
        log_message('debug', '=== UPLOAD IMAGE ===');
        log_message('debug', 'POST data: ' . json_encode($data));
        log_message('debug', 'Fichier reçu ? ' . ($file ? 'OUI' : 'NON'));
        if ($file) {
            log_message('debug', 'Nom original: ' . $file->getName());
            log_message('debug', 'Taille: ' . $file->getSize());
            log_message('debug', 'MIME: ' . $file->getClientMimeType());
            log_message('debug', 'Erreur: ' . $file->getError());
            log_message('debug', 'isValid: ' . ($file->isValid() ? 'true' : 'false'));
        }

        // Validation
        if (empty($data['nom']) || empty($data['duree_conservation']) || !isset($data['prix_vente']) || !isset($data['seuil_critique'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'nom, duree_conservation, prix_vente et seuil_critique requis']);
        }

        // Gestion de l'image
        $imagePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Vérifier le type MIME
            $mimeType = $file->getClientMimeType();
            if (in_array($mimeType, ['image/jpeg', 'image/png'])) {
                // Créer le dossier si inexistant
                $uploadPath = FCPATH . 'uploads/produits';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $imagePath = 'uploads/produits/' . $newName;
                log_message('debug', 'Image uploadée avec succès: ' . $imagePath);
            } else {
                log_message('debug', 'Type MIME non autorisé: ' . $mimeType);
                return $this->response->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Format d\'image non supporté (JPEG ou PNG seulement)']);
            }
        } else {
            log_message('debug', 'Aucune image valide reçue (file null ou invalide)');
        }

        $data['image'] = $imagePath;
        $id = $this->model->creerProduit($data);

        return $this->response->setStatusCode(201)
            ->setJSON(['status' => 'ok', 'id' => $id, 'image' => $imagePath]);
    }

    public function update($id)
    {
        ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
        $produit = $this->model->find($id);
        if (!$produit) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Produit introuvable']);
        }

        $data = $this->request->getPost();
        $file = $this->request->getFile('image');

        // Validation
        if (empty($data['nom']) || empty($data['duree_conservation']) || !isset($data['prix_vente']) || !isset($data['seuil_critique'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'nom, duree_conservation, prix_vente et seuil_critique requis']);
        }

        // Gestion de l'image
        $imagePath = $produit['image'];
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Supprimer l'ancienne image si elle existe
            if ($imagePath && file_exists(FCPATH . $imagePath)) {
                unlink(FCPATH . $imagePath);
            }
            // Vérifier le type MIME
            $mimeType = $file->getClientMimeType();
            if (in_array($mimeType, ['image/jpeg', 'image/png'])) {
                $uploadPath = FCPATH . 'uploads/produits';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $imagePath = 'uploads/produits/' . $newName;
            } else {
                return $this->response->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Format d\'image non supporté (JPEG ou PNG seulement)']);
            }
        }

        $data['image'] = $imagePath;
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

        // Supprimer l'image associée
        if ($produit['image'] && file_exists(FCPATH . $produit['image'])) {
            unlink(FCPATH . $produit['image']);
        }

        $this->model->delete($id);
        return $this->response->setJSON(['status' => 'ok']);
    }
}