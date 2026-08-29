<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementModel extends Model
{
    protected $table            = 'mouvements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['produit_id', 'client_id', 'type', 'cause', 'quantite', 'date_mouvement'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Récupère la liste des mouvements avec filtres et pagination
     */
    public function getMouvements(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $builder = $this->db->table('mouvements')
            ->select('mouvements.*, produits.nom as produit_nom, clients.nom as client_nom')
            ->join('produits', 'produits.id = mouvements.produit_id', 'left')
            ->join('clients', 'clients.id = mouvements.client_id', 'left')
            ->where('mouvements.deleted_at', null);

        $this->applyMouvementFilters($builder, $filters);

        $total = $builder->countAllResults(false);

        $data = $builder->orderBy('mouvements.date_mouvement', 'DESC')
                        ->orderBy('mouvements.id', 'DESC')
                        ->limit($perPage, ($page - 1) * $perPage)
                        ->get()
                        ->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Récupère les données pour le graphique (agrégé par jour)
     */
    public function getChartData(array $filters = []): array
    {
        $builder = $this->db->table('mouvements')
            ->select("
                DATE(date_mouvement) as date,
                SUM(CASE WHEN type = 'entree' THEN quantite ELSE 0 END) as entree,
                SUM(CASE WHEN type = 'sortie' THEN quantite ELSE 0 END) as sortie
            ")
            ->where('mouvements.deleted_at', null);

        $this->applyMouvementFilters($builder, $filters);

        $builder->groupBy('DATE(date_mouvement)')
                ->orderBy('date', 'ASC');

        $results = $builder->get()->getResultArray();
        // Formater pour Chart.js (labels + datasets)
        $labels = array_column($results, 'date');
        $entrees = array_column($results, 'entree');
        $sorties = array_column($results, 'sortie');

        return [
            'labels' => $labels,
            'entree' => $entrees,
            'sortie' => $sorties,
        ];
    }

    /**
     * Applique les filtres au builder
     */
    private function applyMouvementFilters(&$builder, array $filters): void
    {
        if (!empty($filters['date_debut'])) {
            $builder->where('mouvements.date_mouvement >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $builder->where('mouvements.date_mouvement <=', $filters['date_fin']);
        }
        if (!empty($filters['type']) && $filters['type'] !== 'tous') {
            $builder->where('mouvements.type', $filters['type']);
        }
        if (!empty($filters['cause']) && $filters['cause'] !== 'toutes') {
            $builder->where('mouvements.cause', $filters['cause']);
        }
        if (!empty($filters['produit_id'])) {
            $builder->where('mouvements.produit_id', $filters['produit_id']);
        }
        if (!empty($filters['client_id'])) {
            $builder->where('mouvements.client_id', $filters['client_id']);
        }
    }

    /**
     * Récupère les achats d'un client avec pagination et filtres
     */
    public function getAchatsClient(int $clientId, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $builder = $this->db->table('mouvements')
            ->select('mouvements.*, produits.nom as produit_nom, produits.prix_vente')
            ->join('produits', 'produits.id = mouvements.produit_id')
            ->where('mouvements.client_id', $clientId)
            ->where('mouvements.type', 'sortie')
            ->where('mouvements.cause', 'vente')
            ->where('mouvements.deleted_at', null);

        if (!empty($filters['date_debut'])) {
            $builder->where('mouvements.date_mouvement >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $builder->where('mouvements.date_mouvement <=', $filters['date_fin']);
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('mouvements.date_mouvement', 'DESC')
                        ->limit($perPage, ($page - 1) * $perPage)
                        ->get()
                        ->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Calcule le total des achats d'un client sur une période
     */
    public function getTotalAchatsClient(int $clientId, array $filters = []): int
    {
        $builder = $this->db->table('mouvements')
            ->select('SUM(mouvements.quantite * produits.prix_vente) as total')
            ->join('produits', 'produits.id = mouvements.produit_id')
            ->where('mouvements.client_id', $clientId)
            ->where('mouvements.type', 'sortie')
            ->where('mouvements.cause', 'vente')
            ->where('mouvements.deleted_at', null);

        if (!empty($filters['date_debut'])) {
            $builder->where('mouvements.date_mouvement >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $builder->where('mouvements.date_mouvement <=', $filters['date_fin']);
        }

        $result = $builder->get()->getRowArray();
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Calcule le stock actuel d'un produit à une date donnée (ou aujourd'hui)
     */
    public function getStockActuel(int $produitId, ?string $date = null): float
    {
        $date = $date ?? date('Y-m-d');
        
        // On prend tous les mouvements jusqu'à cette date (inclus)
        $builder = $this->db->table('mouvements')
            ->select("
                SUM(CASE WHEN type = 'entree' THEN quantite ELSE 0 END) as total_entree,
                SUM(CASE WHEN type = 'sortie' THEN quantite ELSE 0 END) as total_sortie
            ")
            ->where('produit_id', $produitId)
            ->where('deleted_at', null)
            ->where('date_mouvement <=', $date);

        $result = $builder->get()->getRowArray();
        $entree = (float) ($result['total_entree'] ?? 0);
        $sortie = (float) ($result['total_sortie'] ?? 0);
        return $entree - $sortie;
    }

    /**
     * Récupère les ventes regroupées par client sur une période
     */
    public function getVentesParClient(string $dateDebut, string $dateFin): array
    {
        $builder = $this->db->table('mouvements')
            ->select("
                clients.id as client_id,
                clients.nom as client_nom,
                GROUP_CONCAT(DISTINCT produits.nom SEPARATOR ', ') as produits_livres,
                SUM(mouvements.quantite * produits.prix_vente) as montant_total
            ")
            ->join('clients', 'clients.id = mouvements.client_id')
            ->join('produits', 'produits.id = mouvements.produit_id')
            ->where('mouvements.type', 'sortie')
            ->where('mouvements.cause', 'vente')
            ->where('mouvements.deleted_at', null)
            ->where('mouvements.date_mouvement >=', $dateDebut)
            ->where('mouvements.date_mouvement <=', $dateFin)
            ->groupBy('clients.id')
            ->orderBy('montant_total', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Récupère les ventes regroupées par produit sur une période
     */
    public function getVentesParProduit(string $dateDebut, string $dateFin): array
    {
        $builder = $this->db->table('mouvements')
            ->select("
                produits.id as produit_id,
                produits.nom as produit_nom,
                SUM(CASE WHEN mouvements.cause = 'vente' THEN mouvements.quantite ELSE 0 END) as quantite_vendue,
                SUM(CASE WHEN mouvements.cause = 'non_conforme' THEN mouvements.quantite ELSE 0 END) as quantite_perdue,
                SUM(mouvements.quantite * produits.prix_vente) as valeur_totale
            ")
            ->join('produits', 'produits.id = mouvements.produit_id')
            ->where('mouvements.type', 'sortie')
            ->whereIn('mouvements.cause', ['vente', 'non_conforme'])
            ->where('mouvements.deleted_at', null)
            ->where('mouvements.date_mouvement >=', $dateDebut)
            ->where('mouvements.date_mouvement <=', $dateFin)
            ->groupBy('produits.id')
            ->orderBy('valeur_totale', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Récupère l'évolution des ventes (montant total par jour/semaine/mois)
     */
public function getVentesEvolution(string $dateDebut, string $dateFin, string $granularite = 'day'): array
{
    $format = '%Y-%m-%d';
    if ($granularite === 'week') {
        $format = '%Y-%u'; // année-semaine (ISO)
    } elseif ($granularite === 'month') {
        $format = '%Y-%m';
    }

    $builder = $this->db->table('mouvements')
        ->select("
            DATE_FORMAT(mouvements.date_mouvement, '{$format}') as periode,
            SUM(mouvements.quantite * produits.prix_vente) as montant_total
        ")
        ->join('produits', 'produits.id = mouvements.produit_id')
        ->where('mouvements.type', 'sortie')
        ->where('mouvements.cause', 'vente')
        ->where('mouvements.deleted_at', null)
        ->where('mouvements.date_mouvement >=', $dateDebut)
        ->where('mouvements.date_mouvement <=', $dateFin)
        ->groupBy('periode')
        ->orderBy('periode', 'ASC');

    return $builder->get()->getResultArray();
}

}