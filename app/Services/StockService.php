<?php

namespace App\Services;

use App\Models\ProduitModel;
use App\Models\MouvementModel;

class StockService
{
    protected $produitModel;
    protected $mouvementModel;

    public function __construct()
    {
        $this->produitModel = new ProduitModel();
        $this->mouvementModel = new MouvementModel();
    }

    /**
     * Récupère le stock actuel pour tous les produits (non supprimés)
     * Retourne un tableau avec : id, nom, prix_vente, quantite, total, statut
     */
    public function getStockGlobal(): array
    {
        $produits = $this->produitModel->where('deleted_at', null)->findAll();
        $result = [];
        $valeurTotale = 0;

        foreach ($produits as $prod) {
            $quantite = $this->mouvementModel->getStockActuel($prod['id']);
            $total = $quantite * ($prod['prix_vente'] ?? 0);
            $valeurTotale += $total;

            $result[] = [
                'id'          => $prod['id'],
                'nom'         => $prod['nom'],
                'prix_vente'  => $prod['prix_vente'] ?? 0,
                'quantite'    => $quantite,
                'total'       => $total,
                'statut'      => $this->determinerStatut($quantite, $prod['seuil_critique'] ?? 50)
            ];
        }

        return [
            'produits' => $result,
            'valeur_totale' => $valeurTotale
        ];
    }

    /**
     * Détermine le statut en fonction d'un seuil (ex: 50)
     */
    private function determinerStatut(float $quantite, float $seuil): string
    {
        if ($quantite <= 0) return 'rupture';
        if ($quantite < $seuil) return 'critique';
        return 'optimal';
    }
}