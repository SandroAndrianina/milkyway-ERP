<!-- SideNavBar (Shared) -->
<nav class="bg-primary dark:bg-surface-container-highest shadow-sm w-[280px] h-screen fixed lg:sticky left-0 top-0 flex flex-col py-lg z-50 transition-transform lg:transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0" id="sidebar">

    <!-- Bouton fermeture (mobile/tablette uniquement) -->
    <button id="sidebar-mobile-close" class="lg:hidden absolute right-4 top-6 w-9 h-9 rounded-full bg-surface-container-lowest text-primary flex items-center justify-center shadow-md z-50">
        <span class="material-symbols-outlined text-[20px]">close</span>
    </button>

    <!-- Bouton toggle (desktop uniquement — collapse en icônes) -->
    <button id="sidebar-toggle" class="hidden lg:flex absolute -right-3 top-6 w-7 h-7 rounded-full bg-primary text-on-primary border-2 border-surface-container-lowest items-center justify-center shadow-md hover:bg-primary-container transition-colors z-50">
        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
    </button>

    <!-- Brand Header -->
    <div class="px-6 mb-8 flex items-center gap-4">
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

        <?php $role = session('role'); ?>

        <!-- ========================= -->
        <!-- SECTION DLC (admin only) -->
        <!-- ========================= -->
        <?php if ($role === 'admin'): ?>
            <li class="px-6 pt-2 pb-1">
                <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-60 font-bold section-label">Gestion DLC</span>
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
        <?php endif; ?>

        <!-- ========================= -->
        <!-- SECTION ÉCOULEMENT       -->
        <!-- ========================= -->
        <li class="px-6 pt-4 pb-1">
            <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-60 font-bold section-label">Écoulement</span>
        </li>

        <!-- Ventes (vente + admin) -->
        <?php if (in_array($role, ['vente', 'admin'])): ?>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'ventes'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/ventes">
                    <span class="material-symbols-outlined" data-icon="sell">sell</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Ventes</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Clients (vente + admin) -->
        <?php if (in_array($role, ['vente', 'admin'])): ?>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'clients'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/clients">
                    <span class="material-symbols-outlined" data-icon="group">group</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Clients</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Produits (stocks + admin) -->
        <?php if (in_array($role, ['stocks', 'admin'])): ?>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'produits-ecoulement'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/produits-ecoulement">
                    <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Produits</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- ❌ Lien "Mouvements de Stock" supprimé ici -->

        <!-- État de Stock Actuel (toujours visible) -->
        <li>
            <?php 
                $currentUri = service('uri')->getSegment(1);
                $isActive = ($currentUri === 'etat-stock'); 
            ?>
            <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/etat-stock">
                <span class="material-symbols-outlined" data-icon="assessment">assessment</span>
                <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">État de Stock Actuel</span>
            </a>
        </li>

        <!-- Récapitulation (vente + admin) -->
        <?php if (in_array($role, ['vente', 'admin'])): ?>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'recapitulation'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/recapitulation">
                    <span class="material-symbols-outlined" data-icon="summarize">summarize</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Récapitulation</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Gestion de stock (stocks + admin) -->
        <?php if (in_array($role, ['stocks', 'admin'])): ?>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'stock-gestion'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/stock-gestion">
                    <span class="material-symbols-outlined" data-icon="inventory">inventory</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Gestion de stock</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- ========================= -->
        <!-- SECTION ADMIN             -->
        <!-- ========================= -->
        <?php if ($role === 'admin'): ?>
            <li class="px-6 pt-4 pb-1">
                <span class="text-primary-fixed-dim text-xs uppercase tracking-wider opacity-60 font-bold section-label">Administration</span>
            </li>
            <li>
                <?php 
                    $currentUri = service('uri')->getSegment(1);
                    $isActive = ($currentUri === 'admin'); 
                ?>
                <a class="nav-link flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
                   href="/admin/users">
                    <span class="material-symbols-outlined" data-icon="admin_panel_settings">admin_panel_settings</span>
                    <span class="font-body-md text-body-md nav-text <?= $isActive ? 'font-bold' : '' ?>">Gestion utilisateurs</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Espacement -->
        <li class="flex-1"></li>

        <!-- Déconnexion -->
        <li>
            <a class="nav-link flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-6 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" 
               href="/logout">
                <span class="material-symbols-outlined" data-icon="logout">logout</span>
                <span class="font-body-md text-body-md nav-text">Déconnexion</span>
            </a>
        </li>
    </ul>
</nav>