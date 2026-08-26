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
        <li>
            <a class="flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
                <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                <span class="font-body-md text-body-md">Tableau de bord</span>
            </a>
        </li>
        <li>
            <?php 
                $currentUri = service('uri')->getSegment(1); // 'dlc' ou autre
                $isActive = ($currentUri === 'dlc'); 
            ?>
            <a class="flex items-center gap-4 <?= $isActive ? 'bg-surface-container-lowest text-primary font-bold rounded-l-full ml-4 pl-4 py-3 shadow-sm' : 'text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 hover:bg-primary-container/20' ?> transition-all cursor-pointer" 
               href="/dlc/catalogue">
                <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
                <span class="font-body-md text-body-md">Gestion DLC</span>
            </a>
        </li>
        <li>
            <a class="flex items-center gap-4 text-primary-fixed-dim opacity-70 hover:opacity-100 px-8 py-3 transition-all hover:bg-primary-container/20 cursor-pointer" href="#">
                <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
                <span class="font-body-md text-body-md">Inventaire</span>
            </a>
        </li>
        <li>
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