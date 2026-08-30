<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Écoulement - Produits</h2>
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
    <div class="p-margin-desktop flex-1">
                <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Gestion des Produits</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Gérez le catalogue des produits, leurs durées de conservation et leurs seuils de stock.</p>
            </div>
            <button class="bg-primary text-on-primary font-label-md text-label-md py-2 px-6 rounded-full shadow-sm hover:bg-primary-container transition-colors min-h-[48px] flex items-center gap-2 shrink-0" 
                    onclick="openAddModal()">
                <span class="material-symbols-outlined text-[20px]" data-icon="add">add</span>
                Ajouter un produit
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">search</span>
                <input type="text" id="search-product" placeholder="Rechercher un produit..." 
                       class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md">
            </div>
        </div>

        <!-- Grille de cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="product-grid">
            <!-- Rempli par JS -->
        </div>
    </div>
</main>

<!-- Modal Ajouter/Modifier (inchangé) -->
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
        <!-- Formulaire -->
        <div class="p-6 overflow-y-auto">
            <form id="product-form" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" id="product-id" value="">
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-2" for="nom">Nom du produit</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-all font-body-md" 
                           id="nom" placeholder="Ex: Yaourt nature" type="text" required>
                </div>
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-2" for="duree">Durée de conservation (jours)</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-all font-body-md" 
                           id="duree" placeholder="45" type="number" min="1" required>
                </div>
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-2" for="prix">Prix de vente (Ar)</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-all font-body-md" 
                           id="prix" placeholder="5000" type="number" min="0" step="100" required>
                </div>
                <!-- Seuil critique -->
                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-2" for="seuil">Seuil critique (stock minimum)</label>
                    <input class="w-full px-4 py-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface focus:ring-2 focus:ring-primary focus:border-primary shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-all font-body-md" 
                           id="seuil" placeholder="50" type="number" min="1" required>
                    <p class="text-[11px] text-on-surface-variant mt-1">En dessous de ce seuil, le produit sera marqué comme "critique".</p>
                </div>
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