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
    protected $allowedFields    = ['nom', 'duree_conservation', 'prix_vente', 'seuil_critique', 'image']; // ← ajout
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    public function creerProduit(array $data): int
    {
        $this->insert([
            'nom'                => $data['nom'],
            'duree_conservation' => $data['duree_conservation'],
            'prix_vente'         => $data['prix_vente'],
            'seuil_critique'     => $data['seuil_critique'] ?? 50,
            'image'              => $data['image'] ?? null,
        ]);
        return $this->getInsertID();
    }

    public function modifierProduitEcoulement(int $id, array $data): bool
    {
        $updateData = [
            'nom'                => $data['nom'],
            'duree_conservation' => $data['duree_conservation'],
            'prix_vente'         => $data['prix_vente'],
            'seuil_critique'     => $data['seuil_critique'] ?? 50,
        ];
        if (isset($data['image'])) {
            $updateData['image'] = $data['image'];
        }
        return $this->update($id, $updateData);
    }
}