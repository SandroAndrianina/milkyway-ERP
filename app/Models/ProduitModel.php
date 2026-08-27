<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduitModel extends Model
{
    protected $table            = 'produits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'duree_conservation',  'prix_vente'];

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

        // Utilisé par Écoulement : création complète
    public function creerProduit(array $data): int
    {
        $this->insert([
            'nom' => $data['nom'],
            'duree_conservation' => $data['duree_conservation'],
            'prix_vente' => $data['prix_vente'],
        ]);
        return $this->getInsertID();
    }

    // Utilisé par Écoulement : modif complète
    public function modifierProduitEcoulement(int $id, array $data): bool
    {
        return $this->update($id, [
            'nom' => $data['nom'],
            'duree_conservation' => $data['duree_conservation'],
            'prix_vente' => $data['prix_vente'],
        ]);
    }

    // Utilisé par DLC : modif restreinte, durée uniquement
    public function modifierDureeDlc(int $id, int $dureeConservation): bool
    {
        return $this->update($id, ['duree_conservation' => $dureeConservation]);
    }
}
