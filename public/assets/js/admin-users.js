document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('user-tbody');

    function loadUsers() {
        fetch('/api/users')
            .then(res => res.json())
            .then(users => {
                tbody.innerHTML = '';
                if (users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-on-surface-variant">Aucun utilisateur.</td></tr>';
                    return;
                }
                users.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-surface-container-low/50 transition-colors';
                    
                    // Badge de statut
                    let statusBadge = '';
                    if (user.status === 'active') {
                        statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>`;
                    } else if (user.status === 'pending') {
                        statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En attente</span>`;
                    } else {
                        statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Désactivé</span>`;
                    }

                    // Vérifier si c'est l'admin lui-même
                    const isSelf = (user.id == currentUserId);

                    tr.innerHTML = `
                        <td class="py-3 px-6">${user.id}</td>
                        <td class="py-3 px-6 font-medium">${user.nom}</td>
                        <td class="py-3 px-6">${user.role_nom}</td>
                        <td class="py-3 px-6">${statusBadge}</td>
                        <td class="py-3 px-6 text-right whitespace-nowrap">
                            ${user.status === 'pending' ? 
                                `<button class="validate-btn bg-green-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-700 transition-colors" data-id="${user.id}">Valider</button>` : ''}
                            ${user.status === 'active' && !isSelf ? 
                                `<button class="disable-btn bg-red-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-700 transition-colors" data-id="${user.id}">Désactiver</button>` : ''}
                            ${user.status === 'disabled' ? 
                                `<button class="reactivate-btn bg-blue-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-blue-700 transition-colors" data-id="${user.id}">Réactiver</button>` : ''}
                            <button class="role-btn bg-purple-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-purple-700 transition-colors" data-id="${user.id}" data-role-id="${user.role_id}">Rôle</button>
                            <button class="reset-btn bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-yellow-700 transition-colors" data-id="${user.id}">Reset MDP</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // === Événements ===

                // 1. Valider un compte
                document.querySelectorAll('.validate-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        if (!confirm('Valider ce compte ?')) return;
                        fetch(`/api/users/validate/${id}`, { method: 'POST' })
                            .then(res => res.json())
                            .then(() => loadUsers())
                            .catch(err => alert('Erreur: ' + err.message));
                    });
                });

                // 2. Désactiver un compte
                document.querySelectorAll('.disable-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        if (!confirm('Désactiver ce compte ?')) return;
                        fetch(`/api/users/disable/${id}`, { method: 'POST' })
                            .then(res => res.json())
                            .then(data => {
                                if (data.error) alert('❌ ' + data.error);
                                else loadUsers();
                            })
                            .catch(err => alert('Erreur: ' + err.message));
                    });
                });

                // 3. Réactiver un compte
                document.querySelectorAll('.reactivate-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        if (!confirm('Réactiver ce compte ?')) return;
                        fetch(`/api/users/reactivate/${id}`, { method: 'POST' })
                            .then(res => res.json())
                            .then(() => loadUsers())
                            .catch(err => alert('Erreur: ' + err.message));
                    });
                });

                // 4. Changer le rôle (ouvre le modal)
                document.querySelectorAll('.role-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('role-user-id').value = this.dataset.id;
                        document.getElementById('role-select').value = this.dataset.roleId;
                        document.getElementById('role-modal').classList.remove('hidden');
                    });
                });

                // 5. Réinitialiser le mot de passe
                document.querySelectorAll('.reset-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        if (!confirm('Réinitialiser le mot de passe ? Un nouveau mot de passe sera généré.')) return;
                        fetch(`/api/users/reset-password/${id}`, { method: 'POST' })
                            .then(res => res.json())
                            .then(data => {
                                if (data.new_password) {
                                    alert(`✅ Nouveau mot de passe : ${data.new_password}\n⚠️ L'utilisateur devra le changer à la prochaine connexion.`);
                                }
                                loadUsers();
                            })
                            .catch(err => alert('Erreur: ' + err.message));
                    });
                });
            })
            .catch(err => {
                console.error('Erreur chargement utilisateurs:', err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-error">Erreur de chargement</td></tr>';
            });
    }

    // === MODAL CHANGER LE RÔLE ===
    document.getElementById('btn-change-role').addEventListener('click', function() {
        const id = document.getElementById('role-user-id').value;
        const roleId = document.getElementById('role-select').value;
        
        if (!id || !roleId) {
            alert('Veuillez sélectionner un rôle.');
            return;
        }

        fetch(`/api/users/change-role/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ role_id: roleId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('❌ ' + data.error);
            } else {
                document.getElementById('role-modal').classList.add('hidden');
                loadUsers();
            }
        })
        .catch(err => alert('Erreur: ' + err.message));
    });

    // Fermer le modal en cliquant sur l'arrière-plan
    document.getElementById('role-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // === MODAL CRÉER ADMIN ===
    window.openCreateAdminModal = function() {
        document.getElementById('create-admin-modal').classList.remove('hidden');
        document.getElementById('admin-nom').value = '';
        document.getElementById('admin-password').value = '';
    };

    document.getElementById('create-admin-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const nom = document.getElementById('admin-nom').value;
        const password = document.getElementById('admin-password').value;

        if (!nom || !password) {
            alert('Veuillez remplir tous les champs.');
            return;
        }

        fetch('/api/users/create-admin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom, password })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('❌ ' + data.error);
            } else {
                document.getElementById('create-admin-modal').classList.add('hidden');
                alert('✅ Administrateur créé avec succès !');
                loadUsers();
            }
        })
        .catch(err => alert('Erreur: ' + err.message));
    });

    // Fermer le modal en cliquant sur l'arrière-plan
    document.getElementById('create-admin-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // === CHARGEMENT INITIAL ===
    loadUsers();
});