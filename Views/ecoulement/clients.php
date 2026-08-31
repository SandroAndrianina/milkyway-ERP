<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar (déjà dans le layout, on le surcharge si besoin) -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-4 md:px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Écoulement - Clients</h2>
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

    <!-- Contenu principal -->
    <div class="p-4 md:p-margin-desktop flex-1 max-w-[1200px] mx-auto w-full">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="font-display-lg text-display-lg text-on-surface">Gestion des Clients</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Gérez votre base de clients et leurs informations de contact.</p>
            </div>
            <button class="bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:bg-primary-container hover:text-on-primary-container shadow-[0_4px_12px_rgba(8,67,101,0.15)] transition-all min-h-[48px] active:scale-95 duration-150" 
                    onclick="openAddModal()">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Ajouter un client
            </button>
        </div>

        <!-- Cartes stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-6">
            <div class="bg-surface-container-lowest rounded-xl p-md border border-outline-variant shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant">Total Clients</span>
                    <span class="material-symbols-outlined text-primary bg-primary-fixed p-2 rounded-lg text-[20px]">group</span>
                </div>
                <div class="font-display-lg text-display-lg text-on-surface" id="total-clients">0</div>
                <div class="font-label-sm text-label-sm text-secondary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span>
                    <span id="new-clients-month">0</span> ce mois
                </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-md border border-outline-variant shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant">Clients Actifs (30j)</span>
                    <span class="material-symbols-outlined text-secondary bg-secondary-fixed p-2 rounded-lg text-[20px]">how_to_reg</span>
                </div>
                <div class="font-display-lg text-display-lg text-on-surface" id="active-clients">0</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant" id="active-percent">0% de la base</div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-md border border-outline-variant shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant">Nouveaux (7j)</span>
                    <span class="material-symbols-outlined text-surface-tint bg-surface-container-high p-2 rounded-lg text-[20px]">new_releases</span>
                </div>
                <div class="font-display-lg text-display-lg text-on-surface" id="new-clients-7d">0</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant" id="last-added">Dernier ajout: -</div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0_4px_16px_rgba(0,0,0,0.04)] overflow-hidden">
            <!-- Barre d'outils -->
            <div class="p-4 border-b border-outline-variant flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-container-low">
                <div class="relative w-full sm:w-auto">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input class="w-full sm:w-80 pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary transition-all" 
                           placeholder="Rechercher un client..." type="text" id="search-client">
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-outline-variant rounded-lg text-on-surface-variant font-label-md text-label-md hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        Filtrer
                    </button>
                    <button class="export-btn flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-outline-variant rounded-lg text-on-surface-variant font-label-md text-label-md hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Exporter
                    </button>
                </div>
            </div>

                            <!-- Table -->
                <table class="w-full min-w-[900px] text-left border-collapse" id="client-table">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant">
                            <th class="p-4 font-label-md text-label-md text-on-surface-variant">Nom</th>
                            <th class="p-4 font-label-md text-label-md text-on-surface-variant whitespace-nowrap">Contact</th>
                            <th class="p-4 font-label-md text-label-md text-on-surface-variant">Adresse</th>
                            <th class="p-4 font-label-md text-label-md text-on-surface-variant text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-body-md text-on-surface divide-y divide-outline-variant" id="client-tbody">
                        <!-- Rempli par JS -->
                    </tbody>
                </table>
<!-- Modal Export -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="export-modal">
    <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
    <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col relative z-10">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Exporter les clients</h3>
            <button class="text-on-surface-variant hover:bg-surface-container-high rounded-full w-8 h-8 flex items-center justify-center" onclick="document.getElementById('export-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Options d'export -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1" for="export-filename">Nom du fichier</label>
                    <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface-container-lowest" id="export-filename" type="text" value="clients_export">
                </div>
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1" for="export-type">Exporter</label>
                    <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface-container-lowest" id="export-type">
                        <option value="current">Page actuelle ({{ currentPage }})</option>
                        <option value="all">Toutes les pages</option>
                    </select>
                </div>
            </div>
            <!-- Aperçu -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="export-preview-table">
                    <thead>
                        <tr class="bg-surface-container-low border-b">
                            <th class="p-2 font-label-md">Nom</th>
                            <th class="p-2 font-label-md">Contact</th>
                            <th class="p-2 font-label-md">Adresse</th>
                        </tr>
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
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-outline-variant flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-container-low">
                <span class="font-body-md text-body-md text-on-surface-variant" id="pagination-info">Affichage de 1 à 0 sur 0 clients</span>
                <div class="flex gap-1">
                    <button class="p-2 border border-outline-variant rounded-md text-on-surface-variant hover:bg-surface-container-low disabled:opacity-50 disabled:cursor-not-allowed" id="prev-page" disabled>
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </button>
                    <div class="flex gap-1" id="pagination-numbers">
                        <!-- généré par JS -->
                    </div>
                    <button class="p-2 border border-outline-variant rounded-md text-on-surface-variant hover:bg-surface-container-low" id="next-page">
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Ajouter/Modifier -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="client-modal">
    <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('client-modal').classList.add('hidden')"></div>
    <div class="bg-surface-container-lowest rounded-xl shadow-[0_16px_40px_rgba(0,0,0,0.12)] w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface" id="modal-title">Ajouter un client</h3>
            <button class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-full w-8 h-8 flex items-center justify-center transition-colors" 
                    onclick="document.getElementById('client-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form id="client-form" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" id="client-id" value="">
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1" for="client-nom">Nom du client / Raison sociale</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md" 
                           id="client-nom" placeholder="Ex: Fromagerie Durand" type="text" required>
                </div>
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1" for="client-contact">Téléphone</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md" 
                           id="client-contact" placeholder="06 00 00 00 00" type="text" required>
                </div>
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1" for="client-adresse">Adresse postale</label>
                    <textarea class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md resize-none" 
                              id="client-adresse" placeholder="Adresse complète..." rows="3"></textarea>
                </div>
                <div class="bg-surface-container-low border-l-4 border-secondary p-4 rounded-r-lg flex gap-3">
                    <span class="material-symbols-outlined text-secondary mt-0.5">info</span>
                    <p class="font-body-md text-[14px] text-on-surface-variant">Ces informations doivent etre precis</p>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                    <button class="px-5 py-2.5 rounded-lg font-label-md text-label-md text-primary hover:bg-primary/5 transition-colors min-h-[48px]" 
                            onclick="document.getElementById('client-modal').classList.add('hidden')" type="button">Annuler</button>
                    <button class="px-6 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm hover:bg-primary-container hover:text-on-primary-container active:scale-95 transition-all min-h-[48px]" 
                            type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>