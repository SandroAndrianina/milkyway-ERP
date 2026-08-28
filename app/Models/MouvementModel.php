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
    protected $protectFields    = true;
    protected $allowedFields    = ['produit_id', 'client_id', 'type', 'cause', 'quantite', 'date_mouvement'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    //operation
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

        // Appliquer les filtres de dates
        if (!empty($filters['date_debut'])) {
            $builder->where('mouvements.date_mouvement >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $builder->where('mouvements.date_mouvement <=', $filters['date_fin']);
        }

        // Compter le total
        $total = $builder->countAllResults(false);

        // Récupérer la page
        $data = $builder->orderBy('mouvements.date_mouvement', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return [
            'data'  => $data,
            'total' => $total,
        ];
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

}
