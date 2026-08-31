<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-4 md:px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Récapitulation des ventes</h2>
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

    <div class="p-4 md:p-margin-desktop max-w-[1440px] mx-auto w-full flex flex-col gap-md">
        <!-- Header avec toggle Semaine / Mois -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
            <div>
                <h2 class="font-headline-md text-headline-md text-primary font-bold">Récapitulation des Ventes</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Vue d'ensemble de l'écoulement des produits laitiers.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex bg-surface-container-low rounded-lg p-1 border border-outline-variant">
                    <button class="period-toggle px-6 py-2 rounded-md font-label-md text-label-md bg-surface text-primary shadow-sm transition-all" data-period="week">Semaine</button>
                    <button class="period-toggle px-6 py-2 rounded-md font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" data-period="month">Mois</button>
                </div>
                <div class="flex items-center gap-2 bg-surface-container-low border border-outline-variant rounded-lg px-3 py-1.5">
                    <input type="date" id="recap-date-debut" class="bg-transparent border-none focus:ring-0 text-label-md font-label-md text-on-surface p-0 w-32" aria-label="Date de début">
                    <span class="text-outline-variant">—</span>
                    <input type="date" id="recap-date-fin" class="bg-transparent border-none focus:ring-0 text-label-md font-label-md text-on-surface p-0 w-32" aria-label="Date de fin">
<button id="recap-filter-btn" class="border border-primary text-primary hover:bg-primary hover:text-on-primary px-4 py-2 rounded-lg font-label-md text-label-md transition-all duration-200 flex items-center gap-2">
    <span class="material-symbols-outlined text-[20px]">filter_list</span>
    Filtrer
</button>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <div class="bg-surface rounded-xl p-md card-shadow border border-surface-container">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-sm text-headline-sm text-primary">Évolution des Ventes (Montant Total)</h3>
                <button id="export-chart-btn" class="text-primary hover:bg-primary-container/10 px-3 py-1 rounded-full transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px">download</span>
                    <span class="font-label-sm text-label-sm">Exporter le graphique</span>
                </button>
            </div>
            <div class="w-full h-[300px] relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Tableaux en colonne unique -->
        <div class="grid grid-cols-1 gap-md mt-4">
            <!-- Table 1: Par Client -->
            <div class="bg-surface rounded-xl p-md card-shadow border border-surface-container flex flex-col h-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-primary">Par Client</h3>
                    <button type="button" class="export-table-btn text-primary hover:bg-primary-container/10 px-3 py-1 rounded-full transition-colors flex items-center gap-1" data-table="clients">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span class="font-label-sm text-label-sm">Exporter</span>
                    </button>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-left border-collapse" id="recap-clients-table">
                        <thead>
                            <tr class="border-b-2 border-primary-fixed">
                                <th class="py-3 px-4 font-label-md text-label-md text-primary">Client</th>
                                <th class="py-3 px-4 font-label-md text-label-md text-primary">Produits livrés</th>
                                <th class="py-3 px-4 font-label-md text-label-md text-primary text-right">Montant total</th>
                            </tr>
                        </thead>
                        <tbody id="recap-clients-body">
                            <tr><td colspan="3" class="text-center py-8 text-on-surface-variant">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Par Produit -->
            <div class="bg-surface rounded-xl p-md card-shadow border border-surface-container flex flex-col h-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-primary">Par Produit</h3>
                    <button type="button" class="export-table-btn text-primary hover:bg-primary-container/10 px-3 py-1 rounded-full transition-colors flex items-center gap-1" data-table="clients">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span class="font-label-sm text-label-sm">Exporter</span>
                    </button>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-left border-collapse" id="recap-produits-table">
                        <thead>
                            <tr class="border-b-2 border-primary-fixed">
                                <th class="py-3 px-4 font-label-md text-label-md text-primary">Produit</th>
                                <th class="py-3 px-4 font-label-md text-label-md text-primary text-right">Qté vendue</th>
                                <th class="py-3 px-4 font-label-md text-label-md text-primary text-right">Qté perdue</th>
                                <th class="py-3 px-4 font-label-md text-label-md text-primary text-right">Valeur</th>
                            </tr>
                        </thead>
                        <tbody id="recap-produits-body">
                            <tr><td colspan="4" class="text-center py-8 text-on-surface-variant">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

    <!-- Fin des tableaux -->
        </div>
    </div>
</main>

<!-- === MODAL EXPORT === (déplacé en dehors des tableaux) -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="export-modal">
    <div class="absolute inset-0 bg-on-background/30 backdrop-blur-sm" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Exporter la récapitulation</h3>
            <button class="text-outline hover:text-on-surface p-1 rounded-full hover:bg-surface-container-low transition-colors" onclick="document.getElementById('export-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Nom du fichier</label>
                    <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface" id="export-filename" type="text" value="recapitulation">
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Table à exporter</label>
                    <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface" id="export-table-select">
                        <option value="clients">Par Client</option>
                        <option value="produits">Par Produit</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="export-preview-table">
                    <thead id="export-preview-head">
                        <tr class="bg-surface-container-low border-b"><th class="p-2 font-label-md">Chargement...</th></tr>
                    </thead>
                    <tbody id="export-preview-body">
                        <tr><td colspan="3" class="text-center py-4">Chargement...</td></tr>
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

<?= $this->endSection() ?>