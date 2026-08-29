<?php

namespace App\Services;

use App\Models\MouvementModel;
use App\Models\ProduitModel;
use App\Models\ClientModel;

class MouvementService
{
    protected $mouvementModel;
    protected $produitModel;
    protected $clientModel;

    public function __construct()
    {
        $this->mouvementModel = new MouvementModel();
        $this->produitModel   = new ProduitModel();
        $this->clientModel    = new ClientModel();
    }

    /**
     * Récupère la liste paginée avec filtres
     */
    public function getMouvements(array $filters, int $page, int $perPage): array
    {
        return $this->mouvementModel->getMouvements($filters, $page, $perPage);
    }

    /**
     * Récupère les données du graphique
     */
    public function getChartData(array $filters): array
    {
        return $this->mouvementModel->getChartData($filters);
    }

    /**
     * Crée un mouvement unique
     */
    public function createMouvement(array $data): int
    {
        // Si c'est une sortie, vérifier le stock
        if ($data['type'] === 'sortie') {
            $stock = $this->mouvementModel->getStockActuel((int) $data['produit_id']);
            if ($stock < $data['quantite']) {
                throw new \Exception('Stock insuffisant. Stock actuel : ' . $stock . ', demandé : ' . $data['quantite']);
            }
        }
        return $this->mouvementModel->insert($data);
    }

    /**
     * Crée plusieurs mouvements en lot
     */
    public function createBatchMouvements(array $items): bool
    {
        // Simuler le stock pour chaque produit
        $stocks = [];
        
        foreach ($items as $item) {
            $produitId = (int) $item['produit_id'];
            
            // Initialiser le stock du produit s'il n'est pas encore en mémoire
            if (!isset($stocks[$produitId])) {
                $stocks[$produitId] = $this->mouvementModel->getStockActuel($produitId);
            }
            
            if ($item['type'] === 'sortie') {
                if ($stocks[$produitId] < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour le produit ID $produitId. Stock actuel : {$stocks[$produitId]}, demandé : {$item['quantite']}");
                }
                // Décrémenter le stock simulé
                $stocks[$produitId] -= $item['quantite'];
            } else {
                // Entrée : incrémenter le stock simulé
                $stocks[$produitId] += $item['quantite'];
            }
        }
        
        // Si tout est validé, insérer en lot
        $db = \Config\Database::connect();
        return $db->table('mouvements')->insertBatch($items);
    }

    /**
     * Récupère un mouvement par ID (pour édition, si besoin)
     */
    public function getMouvement(int $id): ?array
    {
        return $this->mouvementModel->find($id);
    }

    /**
     * Récupère la liste des produits pour les filtres
     */
    public function getProduitsOptions(): array
    {
        return $this->produitModel->where('deleted_at', null)->findAll();
    }

    /**
     * Récupère la liste des clients pour les filtres
     */
    public function getClientsOptions(): array
    {
        return $this->clientModel->where('deleted_at', null)->findAll();
    }

}