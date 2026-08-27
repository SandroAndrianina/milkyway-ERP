<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Gestion des DLC</h2>
        </div>
        <div class="flex items-center gap-6">
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">search</span>
                <input class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-body-md font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all w-64 group-hover:bg-surface-container transition-colors" 
                       placeholder="Rechercher un produit..." type="text" id="search-input">
            </div>
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

    <!-- Page Content -->
    <div class="p-margin-desktop flex-1">
        <!-- Page Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-2">Catalogue des Produits</h2>
                <p class="text-on-surface-variant font-body-md">Gérez les paramètres de conservation de vos produits laitiers artisanaux.</p>
            </div>
            <div class="flex gap-3">
                <button class="border border-outline-variant text-primary font-label-md text-label-md px-6 py-3 rounded-lg hover:bg-primary/5 transition-all active:scale-95 duration-150 flex items-center gap-2 min-h-[48px]" 
                        onclick="window.location.href='/dlc/calculateur'">
                    <span class="material-symbols-outlined text-[20px]">calculate</span>
                    Calculer DLC
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_30px_rgba(8,67,101,0.05)] border border-outline-variant/30 overflow-hidden">
            <table class="w-full text-left border-collapse" id="product-table">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low/50">
                        <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">Nom du produit</th>
                        <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">Durée de conservation (jours)</th>
                        <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50" id="product-tbody">
                    <!-- Chargé par JS -->
                </tbody>
            </table>
        </div>

        <!-- Alert Sécurité (exemple) -->
        <div class="mt-8 bg-surface-container-lowest border-l-4 border-error shadow-[0_2px_8px_rgba(0,0,0,0.05)] rounded-r-lg p-4 flex gap-4 max-w-2xl">
            <span class="material-symbols-outlined text-error">warning</span>
            <div>
                <h4 class="font-label-md text-label-md text-on-surface font-bold">Rappel de Sécurité</h4>
                <p class="text-sm text-on-surface-variant mt-1">Soyez sur de la duree de conservation des produits.</p>
            </div>
        </div>
    </div>
</main>

<!-- Modal Ajouter/Modifier -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="product-modal">
    <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('product-modal').classList.add('hidden')"></div>
    <div class="bg-surface-container-lowest rounded-xl shadow-[0_16px_40px_rgba(0,0,0,0.12)] w-full max-w-md relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface" id="modal-title">Ajouter un produit</h3>
            <button class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-full w-8 h-8 flex items-center justify-center transition-colors" 
                    onclick="document.getElementById('product-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <!-- Form Body -->
        <div class="p-6 overflow-y-auto">
            <form id="product-form" class="space-y-6">
                <input type="hidden" id="product-id" value="">
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-2" for="shelf-life">Durée de conservation (jours)</label>
                    <div class="relative">
                        <input class="w-full pl-4 pr-12 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-all font-body-md text-right" 
                               id="shelf-life" min="1" placeholder="45" type="number" required>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-body-md pointer-events-none">j.</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1">Temps total avant péremption.</p>
                </div>
                <!-- Footer Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                    <button class="px-5 py-2.5 rounded-lg font-label-md text-label-md text-primary hover:bg-primary/5 transition-colors min-h-[48px]" 
                            onclick="document.getElementById('product-modal').classList.add('hidden')" type="button">Annuler</button>
                    <button class="px-6 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm hover:bg-primary-container hover:text-on-primary-container active:scale-95 transition-all min-h-[48px]" 
                            type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>