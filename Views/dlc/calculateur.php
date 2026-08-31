<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full">
    <!-- TopAppBar -->
    <header class="w-full h-20 sticky top-0 z-40 bg-surface dark:bg-surface-dim border-b border-outline-variant flex justify-between items-center px-4 md:px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Gestion des DLC</h2>
        </div>
        <div class="flex items-center gap-4">
            <button class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container-low transition-colors focus:ring-2 focus:ring-primary outline-none">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            </button>
            <button class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container-low transition-colors focus:ring-2 focus:ring-primary outline-none overflow-hidden">
                <span class="material-symbols-outlined text-[28px]" data-icon="account_circle">account_circle</span>
            </button>
        </div>
    </header>

    <!-- Content Canvas -->
    <div class="p-4 md:p-margin-desktop max-w-[1200px] mx-auto w-full flex-1">
        <div class="mb-8">
            <h2 class="font-headline-md text-headline-md text-on-background mb-2">Calculateur de Date Limite</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">Saisissez la date de fabrication de vos produits artisanaux pour obtenir instantanément la date limite de consommation (DLC).</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
            <!-- Formulaire -->
            <div class="lg:col-span-5 flex flex-col gap-md">
                <div class="bg-surface-container-lowest rounded-xl p-8 card-shadow flex flex-col gap-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-md text-label-md text-on-surface" for="product-select">Produit Laitier</label>
                        <div class="relative">
                            <select class="w-full h-12 bg-surface-container-low border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg px-4 font-body-md text-body-md text-on-background appearance-none transition-all shadow-inner outline-none cursor-pointer" id="product-select">
                                <option disabled selected value="">Choisir un produit artisanale...</option>
                            </select>
                            
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-md text-label-md text-on-surface" for="fabrication-date">Date de fabrication</label>
                        <div class="relative">
                            <input class="w-full h-12 bg-surface-container-low border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg px-4 font-body-md text-body-md text-on-background transition-all shadow-inner outline-none" 
                                   id="fabrication-date" type="date">
                            <p class="font-label-sm text-label-sm text-outline mt-1 ml-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                Date de fin de production du lot
                            </p>
                        </div>
                    </div>
                    <button id="calc-btn" class="mt-4 w-full bg-primary text-on-primary font-label-md text-label-md h-14 rounded-xl flex items-center justify-center gap-2 hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm active:scale-[0.98]">
                        <span class="material-symbols-outlined" data-icon="calculate" style="font-variation-settings: 'FILL' 1;">calculate</span>
                        Calculer la DLC
                    </button>
                </div>
            </div>
            <!-- Résultat -->
            <div class="lg:col-span-7 flex flex-col gap-md">
                <div class="bg-surface-container-lowest rounded-xl p-8 card-shadow flex items-start gap-6 relative overflow-hidden group py-12">
                    <div class="absolute top-0 left-0 w-1 h-full bg-secondary transition-all group-hover:w-2"></div>
                    <div class="w-16 h-16 rounded-full bg-secondary-container/30 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary text-[32px]">event_available</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-label-md text-label-md text-on-surface-variant mb-1 uppercase tracking-wider">Date de péremption</h3>
                        <div class="flex flex-col mb-4">
                            <div class="font-display-lg text-[40px] text-on-background leading-tight" id="dlc-standard">--/--/----</div>
                            <div class="font-headline-sm text-on-surface-variant opacity-70" id="dlc-standard-text">--</div>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm rounded-full">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            Qualité optimale garantie
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->endSection() ?>