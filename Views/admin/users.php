<?= $this->extend('partials/header') ?>

<?= $this->section('content') ?>
<main class="flex-1 flex flex-col relative w-full overflow-x-hidden">
    <!-- TopAppBar -->
    <header class="bg-surface dark:bg-surface-dim w-full h-20 sticky top-0 z-40 border-b border-outline-variant flex justify-between items-center px-4 md:px-margin-desktop ml-auto">
        <div class="flex items-center gap-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Gestion des utilisateurs</h2>
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Gestion des utilisateurs</h2>
                <p class="text-on-surface-variant font-body-md">Validez, désactivez ou modifiez les rôles des comptes.</p>
            </div>
            <button class="bg-primary text-on-primary font-label-md text-label-md py-2 px-6 rounded-full shadow-sm hover:bg-primary-container transition-colors min-h-[48px] flex items-center gap-2 shrink-0" 
                    onclick="openCreateAdminModal()">
                <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                Créer un admin
            </button>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow border border-outline-variant/30 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left border-collapse" id="user-table">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container-low/50">
                            <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">ID</th>
                            <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">Nom</th>
                            <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">Rôle</th>
                            <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant">Statut</th>
                            <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50" id="user-tbody">
                        <!-- Rempli par JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- === MODAL CHANGER LE RÔLE === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="role-modal">
    <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm" onclick="document.getElementById('role-modal').classList.add('hidden')"></div>
    <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Changer le rôle</h3>
            <button class="text-on-surface-variant hover:bg-surface-container-high rounded-full w-8 h-8 flex items-center justify-center" onclick="document.getElementById('role-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="role-user-id">
            <div>
                <label class="block font-label-md text-label-md text-on-surface mb-2">Nouveau rôle</label>
                <select id="role-select" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="1">vente</option>
                    <option value="2">stocks</option>
                    <option value="3">admin</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-outline-variant">
                <button class="px-5 py-2.5 rounded-lg font-label-md text-label-md text-primary hover:bg-primary/5 transition-colors" onclick="document.getElementById('role-modal').classList.add('hidden')">Annuler</button>
                <button id="btn-change-role" class="px-6 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm hover:bg-primary-container transition-colors">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL CRÉER ADMIN === -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden" id="create-admin-modal">
    <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm" onclick="document.getElementById('create-admin-modal').classList.add('hidden')"></div>
    <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Créer un administrateur</h3>
            <button class="text-on-surface-variant hover:bg-surface-container-high rounded-full w-8 h-8 flex items-center justify-center" onclick="document.getElementById('create-admin-modal').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <form id="create-admin-form">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="block font-label-md text-label-md text-on-surface mb-2">Nom d'utilisateur</label>
                    <input type="text" id="admin-nom" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Ex: nouvel_admin" required>
                </div>
                <div class="mb-4">
                    <label class="block font-label-md text-label-md text-on-surface mb-2">Mot de passe</label>
                    <input type="password" id="admin-password" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Mot de passe" required>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                    <button type="button" class="px-5 py-2.5 rounded-lg font-label-md text-label-md text-primary hover:bg-primary/5 transition-colors" onclick="document.getElementById('create-admin-modal').classList.add('hidden')">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm hover:bg-primary-container transition-colors">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Variables JS et script -->
<script>
    const currentUserId = <?= session('user_id') ?>;
</script>
<script src="/assets/js/admin-users.js"></script>
<?= $this->endSection() ?>