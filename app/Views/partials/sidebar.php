<!-- SideNavBar (Shared) -->
<nav class="bg-primary dark:bg-surface-container-highest shadow-sm w-[280px] h-screen sticky left-0 top-0 flex flex-col py-lg z-50">
    <!-- Brand Header -->
    <div class="px-8 mb-8 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-surface-container-lowest flex items-center justify-center overflow-hidden flex-shrink-0">
            <img class="object-cover w-full h-full" 
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuBv5-GEJ0ctW-qwH23TAYXbsSKgXVmLW8uxLaJStQvKJO9SE3bvvwIx7Vm897S0rVEsv5LhIz9mhL-b_yLlncBuE82XJzMITJXBVrgQGhiRHn74_2ZlzJ9WJMEoHOE6xoSn1iQrlW58SNBMIJSdyGHZSi3LaQC_BXP12n9cLyNyoGvBDVD2_dUrGr6cItA2R3_IFpiFwIIniZ8-8EN4XXyL2YUG0Qnu3DfeyGzd_NXAcTjgtg3D3M9mDw"
                 alt="Milky Way">
        </div>
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-primary leading-tight">Milky Way</h1>
            <p class="font-label-sm text-label-sm text-primary-fixed-dim">Gestion Qualité</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <ul class="flex flex-col flex-1 gap-2">
        <!-- Tableau de bord -->
        <li>
            <a class="flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
                <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                <span class="font-body-md text-body-md">Tableau de bord</span>
            </a>
        </li>

        <!-- Gestion DLC -->
        <li>
            <?php 
                $currentUri = service('uri')->getSegment(1);
                $isActive = ($currentUri === 'dlc'); 
            ?>
            <a class="flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/dlc/catalogue">
                <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
                <span class="font-body-md text-body-md">Gestion DLC</span>
            </a>
        </li>

        <!-- Section Écoulement -->
        <li class="px-8 pt-4 pb-2">
            <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-50 font-bold">Écoulement</span>
        </li>

        <li>
            <?php 
                $currentUri2 = service('uri')->getSegment(1);
                $isProdActive = ($currentUri2 === 'produits-ecoulement'); 
            ?>
            <a class="flex items-center gap-4 <?= $isProdActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer pl-8" 
               href="/produits-ecoulement">
                <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
                <span class="font-body-md text-body-md">Produits</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri3 = service('uri')->getSegment(1);
                $isClientActive = ($currentUri3 === 'clients'); 
            ?>
            <a class="flex items-center gap-4 <?= $isClientActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer pl-8" 
               href="/clients">
                <span class="material-symbols-outlined" data-icon="group">group</span>
                <span class="font-body-md text-body-md">Clients</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri4 = service('uri')->getSegment(1);
                $isMvtActive = ($currentUri4 === 'mouvements'); 
            ?>
            <a class="flex items-center gap-4 <?= $isMvtActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer pl-8" 
               href="/mouvements">
                <span class="material-symbols-outlined" data-icon="swap_horiz">swap_horiz</span>
                <span class="font-body-md text-body-md">Mouvements de Stock</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri5 = service('uri')->getSegment(1);
                $isStockActive = ($currentUri5 === 'etat-stock'); 
            ?>
            <a class="flex items-center gap-4 <?= $isStockActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer pl-8" 
               href="/etat-stock">
                <span class="material-symbols-outlined" data-icon="assessment">assessment</span>
                <span class="font-body-md text-body-md">État de Stock Actuel</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri6 = service('uri')->getSegment(1);
                $isRecapActive = ($currentUri6 === 'recapitulation'); 
            ?>
            <a class="flex items-center gap-4 <?= $isRecapActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer pl-8" 
               href="/recapitulation">
                <span class="material-symbols-outlined" data-icon="summarize">summarize</span>
                <span class="font-body-md text-body-md">Récapitulation</span>
            </a>
        </li>

        <!-- Paramètres (gardé) -->
        <li class="mt-4">
            <a class="flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
                <span class="material-symbols-outlined" data-icon="settings">settings</span>
                <span class="font-body-md text-body-md">Paramètres</span>
            </a>
        </li>
    </ul>

    <!-- Footer Tab -->
    <div class="mt-auto">
        <a class="flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
            <span class="material-symbols-outlined" data-icon="logout">logout</span>
            <span class="font-body-md text-body-md">Déconnexion</span>
        </a>
    </div>
</nav>