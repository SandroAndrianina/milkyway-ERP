<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nom', 'contact', 'adresse'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Nombre de clients ayant effectué au moins un achat dans les X derniers jours
     */
    public function getActiveClientsCount(int $days = 30): int
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        $builder = $this->db->table('clients')
            ->select('clients.id')
            ->distinct()
            ->join('mouvements', 'mouvements.client_id = clients.id')
            ->where('mouvements.type', 'sortie')
            ->where('mouvements.cause', 'vente')
            ->where('mouvements.date_mouvement >=', $date)
            ->where('clients.deleted_at', null);
        return $builder->countAllResults();
    }

    /**
     * Nombre de clients créés dans les X derniers jours
     */
    public function getNewClientsCount(int $days = 7): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-$days days"));
        return $this->where('created_at >=', $date)
                    ->where('deleted_at', null)
                    ->countAllResults();
    }

    /**
     * Date du dernier client ajouté
     */
    public function getLastAddedClient(): ?string
    {
        $client = $this->orderBy('created_at', 'DESC')
                       ->where('deleted_at', null)
                       ->first();
        return $client ? $client['created_at'] : null;
    }
}