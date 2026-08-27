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
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $period = $this->request->getGet('period') ?? 'month';
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin = $this->request->getGet('date_fin');

        $dateFilter = '';
        if ($period === 'week') {
            $dateFilter = date('Y-m-d', strtotime('-7 days'));
        } elseif ($period === 'month') {
            $dateFilter = date('Y-m-d', strtotime('-30 days'));
        }

        $db = \Config\Database::connect();
        $builder = $db->table('mouvements')
                    ->select('mouvements.*, produits.nom as produit_nom, produits.prix_vente')
                    ->join('produits', 'produits.id = mouvements.produit_id')
                    ->where('mouvements.client_id', $clientId)
                    ->where('mouvements.type', 'sortie')
                    ->where('mouvements.cause', 'vente')
                    ->where('mouvements.deleted_at', null);

        // Priorité : si des dates personnalisées sont fournies, on ignore 'period'
        if ($dateDebut) {
            $builder->where('mouvements.date_mouvement >=', $dateDebut);
        } elseif ($dateFin) {
            $builder->where('mouvements.date_mouvement <=', $dateFin);
        } elseif ($dateFilter && !$dateDebut && !$dateFin) {
            // Sinon on applique le filtre de période
            $builder->where('mouvements.date_mouvement >=', $dateFilter);
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('mouvements.date_mouvement', 'DESC')
                        ->limit($perPage, ($page - 1) * $perPage)
                        ->get()
                        ->getResultArray();

        // Calculer le total acheté
        $totalAchete = 0;
        $result = [];
        foreach ($data as $mvt) {
            $prixUnitaire = $mvt['prix_vente'] ?? 0;
            $totalLigne = $mvt['quantite'] * $prixUnitaire;
            $totalAchete += $totalLigne;

            $result[] = [
                'date'          => date('d M Y', strtotime($mvt['date_mouvement'])),
                'produit_nom'   => $mvt['produit_nom'] ?? 'Produit inconnu',
                'quantite'      => $mvt['quantite'],
                'prix_unitaire' => $prixUnitaire,
                'total'         => $totalLigne,
            ];
        }

        return $this->response->setJSON([
            'data'  => $result,
            'total' => $total,
            'total_achete' => $totalAchete,
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Erreur achats client: ' . $e->getMessage());
        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'Erreur serveur: ' . $e->getMessage()]);
    }
}
}