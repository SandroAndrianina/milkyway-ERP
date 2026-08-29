<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">État des stocks</h2>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                <button class="w-10 h-10 rounded-full hover:bg-surface-container-low transition-colors flex items-center justify-center text-on-surface-variant focus:ring-2 focus:ring-primary">
                    <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                </button>
                <button class="w-10 h-10 rounded-full hover:bg-surface-container-low transition-colors flex items-center justify-center text-on-surface-variant focus:ring-2 focus:ring-primary">
                    <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Contenu -->
    <div class="p-margin-desktop flex-1">
        <div class="max-w-7xl mx-auto">
            <!-- Page Title -->
            <div class="mb-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="font-display-lg text-display-lg text-on-background mb-1">Aperçu du Stock</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Vue en temps réel des inventaires de produits laitiers et alertes de seuil.</p>
                </div>
            </div>

            <!-- Carte Valeur Totale -->
            <div class="bg-primary text-on-primary rounded-xl p-md mb-6 border border-primary-container shadow-[0_4px_24px_rgba(8,67,101,0.15)] flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-on-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[32px]">payments</span>
                    </div>
                    <div>
                        <p class="font-label-md text-label-md text-primary-fixed-dim uppercase tracking-wider">Valeur Totale du Stock</p>
                        <h3 class="font-display-lg text-display-lg font-bold" id="valeur-totale">0 Ar</h3>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="font-label-sm text-label-sm">Mise à jour : <span id="date-maj">--</span></p>
                </div>
            </div>

            <!-- Graphique (Chart.js) -->
            <div class="bg-surface-container-lowest rounded-xl p-md border border-surface-container-highest shadow mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-on-background flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">bar_chart</span>
                        Répartition des Stocks
                    </h3>
                    <div class="flex items-center gap-3">
                        <!-- Toggle Financier / Quantité -->
                        <div class="flex p-1 bg-surface-container-low rounded-lg">
                            <button class="toggle-mode-btn px-3 py-1.5 rounded-md text-xs font-label-md bg-primary text-on-primary shadow-sm transition-all" data-mode="quantite">
                                Quantité
                            </button>
                            <button class="toggle-mode-btn px-3 py-1.5 rounded-md text-xs font-label-md text-on-surface-variant hover:bg-surface-container-low transition-all" data-mode="financier">
                                Financier (Ar)
                            </button>
                        </div>
                        <button class="text-primary border border-outline-variant hover:bg-primary-container/10 transition-colors rounded px-3 py-1 flex items-center gap-1 font-label-md text-label-md" id="export-chart-btn">
                            <span class="material-symbols-outlined text-[18px]">file_download</span>
                            Exporter
                        </button>
                    </div>
                </div>
                <canvas id="stockChart" height="250"></canvas>
            </div>

            <!-- Tableau -->
            <div class="bg-surface-container-lowest rounded-xl border border-surface-container-highest shadow overflow-hidden">
                    <div class="p-md border-b border-surface-variant flex items-center justify-between bg-surface-bright">
                        <h3 class="font-headline-sm text-headline-sm text-on-background flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">table_chart</span>
                            Inventaire Détaillé
                        </h3>
                        <button class="text-primary border border-outline-variant hover:bg-primary-container/10 transition-colors rounded px-3 py-1 flex items-center gap-1 font-label-md text-label-md" id="export-table-btn">
                            <span class="material-symbols-outlined text-[18px]">file_download</span>
                            Exporter
                        </button>
                    </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="stock-table">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant border-b border-outline-variant">
                                <th class="p-4 font-label-md text-label-md font-semibold whitespace-nowrap">Produit</th>
                                <th class="p-4 font-label-md text-label-md font-semibold text-right whitespace-nowrap">Quantité en stock</th>
                                <th class="p-4 font-label-md text-label-md font-semibold text-right whitespace-nowrap">Prix Total (Ar)</th>
                                <th class="p-4 font-label-md text-label-md font-semibold text-center whitespace-nowrap">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md divide-y divide-outline-variant/30" id="stock-tbody">
                            <tr><td colspan="4" class="text-center py-8 text-on-surface-variant">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- === MODAL EXPORT === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="export-modal">
    <div class="absolute inset-0 bg-on-background/30 backdrop-blur-sm" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Exporter l'état des stocks</h3>
            <button class="text-outline hover:text-on-surface p-1 rounded-full hover:bg-surface-container-low transition-colors" onclick="document.getElementById('export-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Nom du fichier</label>
                    <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface" id="export-filename" type="text" value="etat_stock">
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Exporter</label>
                    <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface" id="export-type">
                        <option value="current">Page actuelle</option>
                        <option value="all">Toutes les pages</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="export-preview-table">
                    <thead>
                        <tr class="bg-surface-container-low border-b">
                            <th class="p-2 font-label-md">Produit</th>
                            <th class="p-2 font-label-md text-right">Quantité</th>
                            <th class="p-2 font-label-md text-right">Prix Total (Ar)</th>
                            <th class="p-2 font-label-md text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="export-preview-body">
                        <tr><td colspan="4" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end gap-3">
                <button id="export-csv-btn" class="bg-primary text-on-primary px-6 py-2 rounded-lg hover:bg-primary-container transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span> CSV
                </button>
                <button id="export-pdf-btn" class="bg-error text-on-error px-6 py-2 rounded-lg hover:bg-error-container transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> PDF
                </button>
            </div>
        </div>
    </div>
</div>
</main>

<?= $this->endSection() ?>