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

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000, random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff));
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
        $produit = $this->produitModel->find($data['produit_id'] ?? 0);
        if (!$produit) {
            throw new \Exception('Produit introuvable');
        }

        // Take the product price unless a valid one is provided
        if (empty($data['prix_unitaire']) || !is_numeric($data['prix_unitaire'])) {
            $data['prix_unitaire'] = (float) ($produit['prix_vente'] ?? 0);
        }
        if ($data['prix_unitaire'] <= 0) {
            throw new \Exception("Le produit '{$produit['nom']}' n'a pas de prix de vente");
        }

        $data['uuid'] ??= $this->generateUuid();

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
        // Validation du prix pour chaque élément
        foreach ($items as $index => $item) {
            if (empty($item['prix_unitaire']) || !is_numeric($item['prix_unitaire']) || $item['prix_unitaire'] <= 0) {
                throw new \Exception("prix_unitaire manquant ou invalide à l'index $index (doit être > 0)");
            }
        }

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

        // Ajouter un uuid à chaque élément
        foreach ($items as &$item) {
            $item['uuid'] ??= $this->generateUuid();
        }
        unset($item);

        // Si tout est validé, insérer en lot (remplit created_at / updated_at)
        return $this->mouvementModel->insertBatch($items) !== false;
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