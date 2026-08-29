<?php

namespace App\Services;

use App\Models\MouvementModel;

class RecapService
{
    protected $mouvementModel;

    public function __construct()
    {
        $this->mouvementModel = new MouvementModel();
    }

    public function getVentesParClient(string $dateDebut, string $dateFin): array
    {
        return $this->mouvementModel->getVentesParClient($dateDebut, $dateFin);
    }

    public function getVentesParProduit(string $dateDebut, string $dateFin): array
    {
        return $this->mouvementModel->getVentesParProduit($dateDebut, $dateFin);
    }

    public function getVentesEvolution(string $dateDebut, string $dateFin, string $granularite = 'day'): array
    {
        // Récupérer les données réelles
        $data = $this->mouvementModel->getVentesEvolution($dateDebut, $dateFin, $granularite);
        $dataMap = [];
        foreach ($data as $row) {
            $dataMap[$row['periode']] = (float) $row['montant_total'];
        }

        // Générer toutes les périodes
        $labels = [];
        $values = [];
        $current = new \DateTime($dateDebut);
        $end = new \DateTime($dateFin);
        $interval = new \DateInterval('P1D');

        if ($granularite === 'week') {
            // Aller au lundi de la semaine de début
            $current->modify('monday this week');
            $end->modify('sunday this week');
            $interval = new \DateInterval('P1W');
        } elseif ($granularite === 'month') {
            // Aller au premier jour du mois
            $current->modify('first day of this month');
            $end->modify('last day of this month');
            $interval = new \DateInterval('P1M');
        }

        while ($current <= $end) {
            $periodKey = $current->format('Y-m-d');
            if ($granularite === 'week') {
                $periodKey = $current->format('Y-W');
            } elseif ($granularite === 'month') {
                $periodKey = $current->format('Y-m');
            }
            $labels[] = $periodKey;
            $values[] = $dataMap[$periodKey] ?? 0;
            $current->add($interval);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventes (Ar)',
                    'data' => $values,
                ]
            ]
        ];
    }
}