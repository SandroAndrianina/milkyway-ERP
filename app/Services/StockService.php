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
     * Récupère le stock global
     * @param bool $includeFinance Si false, exclut prix_vente et total
     */
    public function getStockGlobal(bool $includeFinance = true): array
    {
        $produits = $this->produitModel->where('deleted_at', null)->findAll();
        $result = [];
        $valeurTotale = 0;

        foreach ($produits as $prod) {
            $quantite = $this->mouvementModel->getStockActuel($prod['id']);
            $prix = $prod['prix_vente'] ?? 0;
            $total = $quantite * $prix;
            $valeurTotale += $total;

            $item = [
                'id'       => $prod['id'],
                'nom'      => $prod['nom'],
                'quantite' => $quantite,
                'statut'   => $this->determinerStatut($quantite, $prod['seuil_critique'] ?? 50),
            ];

            // ✅ Inclusion conditionnelle des données financières
            if ($includeFinance) {
                $item['prix_vente'] = $prix;
                $item['total'] = $total;
            }

            $result[] = $item;
        }

        return [
            'produits' => $result,
            'valeur_totale' => $includeFinance ? $valeurTotale : 0,
        ];
    }

    private function determinerStatut(float $quantite, float $seuil): string
    {
        if ($quantite <= 0) return 'rupture';
        if ($quantite < $seuil) return 'critique';
        return 'optimal';
    }
}