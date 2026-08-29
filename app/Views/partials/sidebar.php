<!-- SideNavBar (Shared) -->
<nav class="bg-primary dark:bg-surface-container-highest shadow-sm w-[280px] h-screen sticky left-0 top-0 flex flex-col py-lg z-50 transition-all duration-300 ease-in-out" id="sidebar">
    
<!-- Bouton toggle (visible sur tous les écrans) -->
<button id="sidebar-toggle" class="absolute -right-3 top-6 w-7 h-7 rounded-full bg-primary text-on-primary border-2 border-surface-container-lowest flex items-center justify-center shadow-md hover:bg-primary-container transition-colors z-50">
    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
</button>

<!-- Brand Header -->
<div class="px-6 mb-8 flex items-center gap-4">
    <!-- ✅ Logo plus grand (w-16 = 64px) -->
    <img class="w-16 h-16 object-contain flex-shrink-0" 
         src="/assets/images/logo-remove.png"
         alt="Milky Way">
    <div class="transition-opacity duration-200 whitespace-nowrap" id="brand-text">
        <h1 class="font-headline-md text-headline-md font-bold text-on-primary leading-tight">Milky Way</h1>
        <p class="font-label-sm text-label-sm text-primary-fixed-dim">Laiterie d'Antsirabe</p>
    </div>
</div>

    <!-- Navigation Links -->
    <ul class="flex flex-col flex-1 gap-1 overflow-y-auto">

<!-- Section DLC -->
<li class="px-6 pt-2 pb-1">
    <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-60 font-bold section-label">DLC</span>
</li>
        <li>
            <?php 
                $currentUri = service('uri')->getSegment(1);
                $isActive = ($currentUri === 'dlc'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/dlc/catalogue">
                <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
                <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Gestion DLC</span>
            </a>
        </li>

<!-- Section Écoulement -->
<li class="px-6 pt-4 pb-1">
    <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-60 font-bold section-label">Écoulement</span>
</li>

        <li>
            <?php 
                $currentUri2 = service('uri')->getSegment(1);
                $isProdActive = ($currentUri2 === 'produits-ecoulement'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isProdActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/produits-ecoulement">
                <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
                <span class="font-body-md text-body-md nav-text <?= $isProdActive ? 'font-bold' : '' ?>">Produits</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri3 = service('uri')->getSegment(1);
                $isClientActive = ($currentUri3 === 'clients'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isClientActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/clients">
                <span class="material-symbols-outlined" data-icon="group">group</span>
                <span class="font-body-md text-body-md nav-text <?= $isClientActive ? 'font-bold' : '' ?>">Clients</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri4 = service('uri')->getSegment(1);
                $isMvtActive = ($currentUri4 === 'mouvements'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isMvtActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/mouvements">
                <span class="material-symbols-outlined" data-icon="swap_horiz">swap_horiz</span>
                <span class="font-body-md text-body-md nav-text <?= $isMvtActive ? 'font-bold' : '' ?>">Mouvements de Stock</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri5 = service('uri')->getSegment(1);
                $isStockActive = ($currentUri5 === 'etat-stock'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isStockActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/etat-stock">
                <span class="material-symbols-outlined" data-icon="assessment">assessment</span>
                <span class="font-body-md text-body-md nav-text <?= $isStockActive ? 'font-bold' : '' ?>">État de Stock Actuel</span>
            </a>
        </li>

        <li>
            <?php 
                $currentUri6 = service('uri')->getSegment(1);
                $isRecapActive = ($currentUri6 === 'recapitulation'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isRecapActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/recapitulation">
                <span class="material-symbols-outlined" data-icon="summarize">summarize</span>
                <span class="font-body-md text-body-md nav-text <?= $isRecapActive ? 'font-bold' : '' ?>">Récapitulation</span>
            </a>
        </li>

        <!-- Espacement -->
        <li class="flex-1"></li>

<!-- Déconnexion -->
<li>
    <a class="nav-link flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
        <span class="material-symbols-outlined" data-icon="logout">logout</span>
        <span class="font-body-md text-body-md nav-text">Déconnexion</span>
    </a>
</li>
    </ul>
</nav>