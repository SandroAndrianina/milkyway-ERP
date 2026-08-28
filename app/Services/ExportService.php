<?php

namespace App\Services;

use App\Models\ClientModel;
use App\Models\MouvementModel;
use App\Models\ProduitModel;
use Dompdf\Dompdf;

class ExportService
{
    protected $clientModel;
    protected $mouvementModel;
    protected $produitModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->mouvementModel = new MouvementModel();
        $this->produitModel = new ProduitModel();
    }

    // ========================================
    // CLIENTS
    // ========================================

    public function exportClientsCsv(array $filters, string $filename): void
    {
        $clients = $this->getClientsData($filters);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nom', 'Contact', 'Adresse', 'Créé le']);
        foreach ($clients as $row) {
            fputcsv($output, [$row['id'], $row['nom'], $row['contact'], $row['adresse'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function exportClientsPdf(array $filters, string $filename): void
    {
        $clients = $this->getClientsData($filters);
        $html = $this->generateClientsHtml($clients);
        $this->generatePdf($html, $filename);
    }

    public function getClientsPreview(array $filters, int $limit = 10): array
    {
        $clients = $this->getClientsData($filters, $limit);
        return $clients;
    }

    // ========================================
    // ACHATS CLIENT
    // ========================================

    public function exportAchatsCsv(int $clientId, array $filters, string $filename): void
    {
        $achats = $this->getAchatsData($clientId, $filters);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Produit', 'Quantité', 'Prix unitaire', 'Total']);
        foreach ($achats as $row) {
            fputcsv($output, [
                $row['date'],
                $row['produit_nom'],
                $row['quantite'],
                $row['prix_unitaire'] . ' Ar',
                $row['total'] . ' Ar'
            ]);
        }
        fclose($output);
        exit;
    }

    public function exportAchatsPdf(int $clientId, array $filters, string $filename): void
    {
        $achats = $this->getAchatsData($clientId, $filters);
        $client = $this->clientModel->find($clientId);
        $html = $this->generateAchatsHtml($client, $achats);
        $this->generatePdf($html, $filename);
    }

    public function getAchatsPreview(int $clientId, array $filters, int $limit = 10): array
    {
        $achats = $this->getAchatsData($clientId, $filters, $limit);
        return $achats;
    }

    // ========================================
    // MÉTHODES PRIVÉES (helpers)
    // ========================================

    private function getClientsData(array $filters, ?int $limit = null): array
    {
        $builder = $this->clientModel->builder();
        if (!empty($filters['search'])) {
            $builder->like('nom', $filters['search']);
        }
        $builder->orderBy('id', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    private function getAchatsData(int $clientId, array $filters, ?int $limit = null): array
    {
        $builder = $this->mouvementModel->builder()
            ->select('mouvements.*, produits.nom as produit_nom, produits.prix_vente')
            ->join('produits', 'produits.id = mouvements.produit_id')
            ->where('mouvements.client_id', $clientId)
            ->where('mouvements.type', 'sortie')
            ->where('mouvements.cause', 'vente')
            ->where('mouvements.deleted_at', null)
            ->orderBy('mouvements.date_mouvement', 'DESC');

        if (!empty($filters['date_debut'])) {
            $builder->where('mouvements.date_mouvement >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $builder->where('mouvements.date_mouvement <=', $filters['date_fin']);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        $data = $builder->get()->getResultArray();
        $result = [];
        foreach ($data as $mvt) {
            $prixUnitaire = $mvt['prix_vente'] ?? 0;
            $result[] = [
                'date'          => date('d M Y', strtotime($mvt['date_mouvement'])),
                'produit_nom'   => $mvt['produit_nom'] ?? 'Produit inconnu',
                'quantite'      => $mvt['quantite'],
                'prix_unitaire' => $prixUnitaire,
                'total'         => $mvt['quantite'] * $prixUnitaire,
            ];
        }
        return $result;
    }

    private function generateClientsHtml(array $clients): string
    {
        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #084365; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';
        $html .= '<h1>Liste des clients</h1>';
        $html .= '<p>Exporté le ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table><thead><tr><th>ID</th><th>Nom</th><th>Contact</th><th>Adresse</th><th>Créé le</th></tr></thead><tbody>';
        foreach ($clients as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['id'] . '</td>';
            $html .= '<td>' . $row['nom'] . '</td>';
            $html .= '<td>' . ($row['contact'] ?? '-') . '</td>';
            $html .= '<td>' . ($row['adresse'] ?? '-') . '</td>';
            $html .= '<td>' . $row['created_at'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';
        return $html;
    }

    private function generateAchatsHtml(?array $client, array $achats): string
    {
        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #084365; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .text-right { text-align: right; }
            .total { font-weight: bold; }
        </style></head><body>';
        $html .= '<h1>Historique des achats - ' . ($client['nom'] ?? 'Client') . '</h1>';
        $html .= '<p>Exporté le ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table><thead><tr><th>Date</th><th>Produit</th><th class="text-right">Qté</th><th class="text-right">Prix unit.</th><th class="text-right">Total</th></tr></thead><tbody>';
        foreach ($achats as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['date'] . '</td>';
            $html .= '<td>' . $row['produit_nom'] . '</td>';
            $html .= '<td class="text-right">' . $row['quantite'] . '</td>';
            $html .= '<td class="text-right">' . $row['prix_unitaire'] . ' Ar</td>';
            $html .= '<td class="text-right">' . $row['total'] . ' Ar</td>';
            $html .= '</tr>';
        }
        $totalAchete = array_sum(array_column($achats, 'total'));
        $html .= '<tr><td colspan="4" class="text-right total">Total général</td><td class="text-right total">' . $totalAchete . ' Ar</td></tr>';
        $html .= '</tbody></table></body></html>';
        return $html;
    }

    private function generatePdf(string $html, string $filename): void
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
        exit;
    }
}