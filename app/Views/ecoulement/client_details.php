<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar (déjà dans le layout, on le surcharge) -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Détails client</h2>
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

    <div class="p-margin-desktop flex-1">
        <div class="max-w-6xl mx-auto space-y-md md:space-y-lg">
            <!-- Back Button & Header -->
            <div>
                <a href="/clients" class="flex items-center gap-xs text-primary font-label-md text-label-md hover:text-primary-container transition-colors mb-sm">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Retour aux clients
                </a>
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-md">
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-xs" id="client-nom">Chargement...</h2>
                        <div class="flex flex-wrap items-center gap-x-md gap-y-xs text-on-surface-variant font-body-md text-body-md" id="client-infos">
                            <span class="flex items-center gap-xs">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                                <span id="client-contact">-</span>
                            </span>
                            <span class="flex items-center gap-xs">
                                <span class="material-symbols-outlined text-[18px]">location_on</span>
                                <span id="client-adresse">-</span>
                            </span>
                        </div>
                    </div>
                    <!-- Total acheté -->
                    <div class="glass-card rounded-xl p-md flex items-center gap-sm min-w-[200px] border-l-4 border-primary">
                        <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center">
                            <span class="material-symbols-outlined" data-icon="shopping_cart" style="font-variation-settings: 'FILL' 1;">shopping_cart</span>
                        </div>
                        <div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total acheté</p>
                            <p class="font-headline-sm text-headline-sm text-primary font-bold" id="total-achete">0 Ar</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres & Contrôles -->
            <div class="glass-card rounded-xl p-sm flex flex-col xl:flex-row items-center justify-between gap-sm">
                <div class="flex flex-wrap items-center gap-sm w-full xl:w-auto">
                    <div class="flex items-center gap-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant" for="date-debut">Du</label>
                        <input class="form-input rounded-lg border-outline-variant bg-surface focus:ring-primary focus:border-primary font-body-md text-body-md py-1.5 px-3" id="date-debut" type="date">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant" for="date-fin">Au</label>
                        <input class="form-input rounded-lg border-outline-variant bg-surface focus:ring-primary focus:border-primary font-body-md text-body-md py-1.5 px-3" id="date-fin" type="date">
                    </div>
                    <button id="filter-date-btn" class="bg-primary text-on-primary hover:bg-on-primary-fixed-variant transition-colors rounded-lg px-4 py-1.5 flex items-center gap-2 font-label-md text-label-md shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrer
                    </button>
                    <button id="clear-date-btn" class="border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors rounded-lg px-4 py-1.5 flex items-center gap-2 font-label-md text-label-md">
                        <span class="material-symbols-outlined text-[18px]">clear</span>
                        Effacer
                    </button>
                </div>
                <div class="flex items-center gap-2 w-full xl:w-auto overflow-x-auto pb-1 xl:pb-0">
                    <button class="period-btn px-sm py-1.5 rounded-full border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-surface-container-low transition-colors whitespace-nowrap" data-period="week">Cette semaine</button>
                    <button class="period-btn px-sm py-1.5 rounded-full bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-colors whitespace-nowrap" data-period="month">Ce mois</button>
                    <button class="period-btn px-sm py-1.5 rounded-full border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-surface-container-low transition-colors whitespace-nowrap" data-period="all">Tout</button>
                </div>
            </div>

            <!-- Tableau historique -->
            <div class="glass-card rounded-xl overflow-hidden">
                <div class="p-md border-b border-outline-variant bg-surface-container-lowest flex items-center justify-between">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Historique des achats</h3>
                    <button class="bg-primary text-on-primary hover:bg-on-primary-fixed-variant transition-colors rounded-lg px-4 h-10 flex items-center gap-2 font-label-md text-label-md shadow-sm ml-auto">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        Exporter
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant">
                                <th class="py-sm px-md font-label-md text-label-md text-on-surface-variant cursor-pointer hover:text-primary transition-colors whitespace-nowrap">Date</th>
                                <th class="py-sm px-md font-label-md text-label-md text-on-surface-variant cursor-pointer hover:text-primary transition-colors whitespace-nowrap">Produit</th>
                                <th class="py-sm px-md font-label-md text-label-md text-on-surface-variant cursor-pointer hover:text-primary transition-colors whitespace-nowrap text-right">Quantité</th>
                                <th class="py-sm px-md font-label-md text-label-md text-on-surface-variant cursor-pointer hover:text-primary transition-colors whitespace-nowrap text-right">Prix unitaire</th>
                                <th class="py-sm px-md font-label-md text-label-md text-on-surface-variant cursor-pointer hover:text-primary transition-colors whitespace-nowrap text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md text-on-surface" id="historique-tbody">
                            <tr><td colspan="5" class="text-center py-8 text-on-surface-variant">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination simplifiée -->
                <div class="p-sm flex justify-between items-center border-t border-outline-variant bg-surface-container-lowest">
                    <span class="font-label-sm text-label-sm text-on-surface-variant" id="pagination-info">Affichage 0-0 sur 0</span>
                    <div class="flex gap-xs">
                        <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant text-outline hover:bg-surface-container-low disabled:opacity-50" id="prev-hist" disabled>
                            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                        </button>
                        <span class="w-8 h-8 flex items-center justify-center font-label-md" id="page-indic">1</span>
                        <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant text-outline hover:bg-surface-container-low" id="next-hist">
                            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JS spécifique -->
<script src="/assets/js/ecoulement-client-details.js"></script>
<?= $this->endSection() ?>