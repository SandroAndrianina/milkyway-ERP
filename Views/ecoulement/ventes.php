<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-4 md:px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Ventes et pertes</h2>
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

    <div class="p-4 md:p-margin-desktop flex-1">
        <!-- Deux boutons -->
        <div class="flex justify-end items-center gap-4 mb-6">
            <button class="bg-surface hover:bg-surface-container-low text-primary border border-primary font-label-md px-4 py-2 rounded-lg shadow-sm transition-all flex items-center gap-2" id="btnMultipleMvmts">
                <span class="material-symbols-outlined text-[20px]">library_add</span>
                Ajouter plusieurs sorties
            </button>
            <button class="bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-label-md px-4 py-2 rounded-lg shadow-sm transition-all flex items-center gap-2" id="btnNewMvmt">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Ajouter une sortie
            </button>
        </div>

        <!-- Filtres (inchangés) -->
        <div class="bg-surface rounded-xl shadow-[0_4px_20px_rgba(8,67,101,0.05)] p-4 mb-6 border border-surface-container">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Du</label>
                    <input class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm text-on-surface" type="date" id="filter-date-debut">
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Au</label>
                    <input class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm text-on-surface" type="date" id="filter-date-fin">
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Cause</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm text-on-surface" id="filter-cause">
                        <option value="toutes">Toutes</option>
                        <option value="vente">Vente</option>
                        <option value="non_conforme">Non conforme</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Produit</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm text-on-surface" id="filter-produit">
                        <option value="">Tous</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Client</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm text-on-surface" id="filter-client">
                        <option value="">Tous</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button class="bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-label-md px-6 py-2 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2" id="btnFilter">
                    <span class="material-symbols-outlined text-[20px]">filter_list</span>
                    Filtrer
                </button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-surface rounded-xl shadow-[0_4px_20px_rgba(8,67,101,0.05)] border border-surface-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="vente-table">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-surface-variant">
                            <th class="py-3 px-4 font-label-md text-on-surface-variant">Date</th>
                            <th class="py-3 px-4 font-label-md text-on-surface-variant">Produit</th>
                            <th class="py-3 px-4 font-label-md text-on-surface-variant text-right">Quantité</th>
                            <th class="py-3 px-4 font-label-md text-on-surface-variant">Cause</th>
                            <th class="py-3 px-4 font-label-md text-on-surface-variant">Client</th>
                        </tr>
                    </thead>
                    <tbody id="vente-tbody">
                        <tr><td colspan="5" class="text-center py-8 text-on-surface-variant">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-surface-variant flex items-center justify-between bg-surface-container-lowest">
                <span class="font-body-md text-sm text-on-surface-variant" id="pagination-info">Affichage 0-0 sur 0</span>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low disabled:opacity-50" id="prev-page" disabled>
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <span class="font-label-md" id="page-indic">1</span>
                    <button class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low" id="next-page">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                    <button class="ml-4 flex items-center gap-2 px-4 py-1.5 border border-outline text-primary hover:bg-primary-container/10 rounded-lg font-label-md transition-colors" id="export-btn">
                        <span class="material-symbols-outlined text-[20px]">file_download</span> Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- === MODAL AJOUT UNIQUE === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="modalMvmt">
    <div class="absolute inset-0 bg-on-background/30 backdrop-blur-sm" id="modalBackdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-lg mx-4 flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Enregistrer une sortie</h3>
            <button class="text-outline hover:text-on-surface p-1 rounded-full hover:bg-surface-container-low transition-colors" id="closeModal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form id="formVente" class="flex flex-col gap-4">
                <?= csrf_field() ?>
                <!-- Produit -->
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Produit</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-on-surface" id="modal-produit" required>
                        <option value="">Sélectionner un produit...</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-label-sm text-on-surface-variant mb-1">Quantité</label>
                        <input class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-on-surface" type="number" step="1" placeholder="0" id="modal-quantite" min="1" required>
                    </div>
                    <div>
                        <label class="block font-label-sm text-on-surface-variant mb-1">Date</label>
                        <input class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-on-surface" type="date" id="modal-date" required>
                    </div>
                </div>
                <!-- Cause -->
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Cause</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-on-surface" id="modal-cause" required>
                        <option value="">Sélectionner...</option>
                        <option value="vente">Vente</option>
                        <option value="non_conforme">Non conforme</option>
                    </select>
                </div>
                <!-- Client (conditionnel) -->
                <div id="modal-client-group">
                    <label class="block font-label-sm text-on-surface-variant mb-1">Client (obligatoire pour une vente)</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-on-surface" id="modal-client">
                        <option value="">Sélectionner un client...</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-surface-variant">
                    <button type="button" class="px-4 py-2 font-label-md text-primary hover:bg-primary-container/10 rounded-full transition-colors" id="cancelModal">Annuler</button>
                    <button type="submit" class="bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-label-md px-6 py-2 rounded-full shadow-sm transition-all">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- === MODAL AJOUT MULTIPLE === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="modalMultipleMvmts">
    <div class="absolute inset-0 bg-on-background/30 backdrop-blur-sm" id="modalMultipleBackdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-4xl mx-4 flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Ajouter plusieurs sorties</h3>
            <button class="text-outline hover:text-on-surface p-1 rounded-full hover:bg-surface-container-low transition-colors" id="closeMultipleModal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Champs communs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-surface-container-low/50 rounded-xl border border-surface-container">
<!-- Type (toujours sortie) -->
<input type="hidden" name="type" value="sortie" id="modal-type">
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Date</label>
                    <input class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm" type="date" id="batch-date">
                </div>
                <div id="batch-cause-group">
                    <label class="block font-label-sm text-on-surface-variant mb-1">Cause</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm" id="batch-cause">
                        <option value="">Sélectionner...</option>
                        <option value="vente">Vente</option>
                        <option value="non_conforme">Non conforme</option>
                    </select>
                </div>
                <div id="batch-client-group" class="col-span-1">
                    <label class="block font-label-sm text-on-surface-variant mb-1">Client (pour vente)</label>
                    <select class="w-full bg-surface border-2 border-surface-variant focus:border-primary rounded-lg px-3 py-2 text-sm" id="batch-client">
                        <option value="">Sélectionner un client...</option>
                    </select>
                </div>
            </div>
            <!-- Tableau des lignes -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="multipleMvmtsTable">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-surface-variant text-sm font-label-md text-on-surface-variant">
                            <th class="py-2 px-3">Produit</th>
                            <th class="py-2 px-3 w-40">Quantité</th>
                            <th class="py-2 px-3 text-center w-16">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="multipleMvmtsBody">
                        <tr class="border-b border-surface-variant">
                            <td class="py-2 px-2">
                                <select class="row-produit w-full bg-surface border border-surface-variant focus:border-primary rounded-md px-2 py-1.5 text-sm">
                                    <option value="">Choisir...</option>
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input class="row-quantite w-full bg-surface border border-surface-variant focus:border-primary rounded-md px-2 py-1.5 text-sm" type="number" step="1" placeholder="0" min="1" required>
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button class="text-outline hover:text-error p-1 rounded-full transition-colors remove-row-btn">
                                    <span class="material-symbols-outlined text-[20px]">close</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="mt-4 flex items-center gap-2 text-primary font-label-md hover:bg-primary-container/10 px-4 py-2 rounded-lg transition-colors" id="addRowBtn">
                <span class="material-symbols-outlined">add</span> Ajouter une ligne
            </button>
        </div>
        <div class="px-6 py-4 border-t border-surface-variant bg-surface-container-lowest flex justify-end gap-3 rounded-b-2xl">
            <button class="px-4 py-2 font-label-md text-primary hover:bg-primary-container/10 rounded-full transition-colors" id="cancelMultipleModal">Annuler</button>
            <button class="bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-label-md px-6 py-2 rounded-full shadow-sm transition-all" id="submitBatch">Enregistrer tout</button>
        </div>
    </div>
</div>

<!-- === MODAL EXPORT === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="export-modal">
    <div class="absolute inset-0 bg-on-background/30 backdrop-blur-sm" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Exporter les sorties</h3>
            <button class="text-outline hover:text-on-surface p-1 rounded-full hover:bg-surface-container-low transition-colors" onclick="document.getElementById('export-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-label-sm text-on-surface-variant mb-1">Nom du fichier</label>
                    <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-surface" id="export-filename" type="text" value="sorties_export">
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
                    <thead><tr class="bg-surface-container-low border-b"><th class="p-2 font-label-md">Date</th><th class="p-2 font-label-md">Produit</th><th class="p-2 font-label-md text-right">Qté</th><th class="p-2 font-label-md">Cause</th><th class="p-2 font-label-md">Client</th></tr></thead>
                    <tbody id="export-preview-body"><tr><td colspan="5" class="text-center py-4">Chargement...</td></tr></tbody>
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