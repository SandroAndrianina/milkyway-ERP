document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('client-tbody');
    const modal = document.getElementById('client-modal');
    const form = document.getElementById('client-form');
    const clientId = document.getElementById('client-id');
    const nom = document.getElementById('client-nom');
    const contact = document.getElementById('client-contact');
    const adresse = document.getElementById('client-adresse');
    const modalTitle = document.getElementById('modal-title');
    const searchInput = document.getElementById('search-client');
    const totalClients = document.getElementById('total-clients');
    const activeClients = document.getElementById('active-clients');
    const activePercent = document.getElementById('active-percent');
    const newClients7d = document.getElementById('new-clients-7d');
    const lastAdded = document.getElementById('last-added');
    const paginationInfo = document.getElementById('pagination-info');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const paginationNumbers = document.getElementById('pagination-numbers');

    let currentPage = 1;
    let perPage = 10;
    let totalPages = 0;
    let allClients = [];

    // Charger les clients
    function loadClients(page = 1, search = '') {
        const url = `${API_BASE}/ecoulement/clients?page=${page}&perPage=${perPage}&search=${encodeURIComponent(search)}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                allClients = data.data || [];
                const total = data.total || 0;
                totalPages = Math.ceil(total / perPage);
                renderTable(allClients);
                updatePagination(page, total);
                updateStats(data.stats || {});
            })
            .catch(err => console.error('Erreur chargement clients:', err));
    }

    function formatPhone(phone) {
        if (!phone) return '-';
        let cleaned = phone.replace(/\D/g, ''); // garde uniquement les chiffres
        if (cleaned.length === 10) {
            return cleaned.replace(/(\d{3})(\d{2})(\d{3})(\d{2})/, '$1 $2 $3 $4');
        }
        return phone; // format non reconnu → affiché tel quel
    }

function renderTable(clients) {
    tbody.innerHTML = '';
    if (clients.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="py-12 text-center text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">group_off</span>
            <p class="font-body-md">Aucun client trouvé.</p>
        </td></tr>`;
        return;
    }
    clients.forEach(client => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-container-low transition-colors group';

        // Nom
        const tdNom = document.createElement('td');
        tdNom.className = 'p-4';
        const initial = client.nom ? client.nom.charAt(0).toUpperCase() : '?';
        tdNom.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-md font-bold">
                    ${initial}
                </div>
                <div>
                    <div class="font-label-md text-label-md text-on-surface">${client.nom || ''}</div>
                    <div class="font-label-sm text-label-sm text-on-surface-variant">Client</div>
                </div>
            </div>
        `;

        // Contact (formaté)
        const tdContact = document.createElement('td');
        tdContact.className = 'p-4 whitespace-nowrap';
        tdContact.textContent = formatPhone(client.contact);

        // Adresse
        const tdAdresse = document.createElement('td');
        tdAdresse.className = 'p-4';
        tdAdresse.textContent = client.adresse || '-';

        // Actions
        const tdActions = document.createElement('td');
        tdActions.className = 'p-4 text-center whitespace-nowrap';
        tdActions.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <button class="details-btn p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-colors" data-id="${client.id}" title="Détails">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
                <button class="edit-btn p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-colors" data-id="${client.id}" title="Modifier">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </button>
                <button class="delete-btn p-2 text-on-surface-variant hover:text-error hover:bg-error-container rounded-lg transition-colors" data-id="${client.id}" title="Supprimer">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
            </div>
        `;

        tr.appendChild(tdNom);
        tr.appendChild(tdContact);
        tr.appendChild(tdAdresse);
        tr.appendChild(tdActions);
        tbody.appendChild(tr);
    });

    // Attacher événements
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteClient(this.dataset.id);
        });
    });

    document.querySelectorAll('.details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.location.href = `/clients/details/${this.dataset.id}`;
        });
    });
    
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openEditModal(this.dataset.id);
        });
    });
}

    function updatePagination(page, total) {
        currentPage = page;
        const start = (page - 1) * perPage + 1;
        const end = Math.min(page * perPage, total);
        paginationInfo.textContent = `Affichage de ${start} à ${end} sur ${total} clients`;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages;
        // Générer les numéros
        paginationNumbers.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `w-10 h-10 border rounded-md font-label-md flex items-center justify-center ${i === page ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant hover:bg-surface-container-low'}`;
            btn.textContent = i;
            btn.addEventListener('click', () => {
                loadClients(i, searchInput.value);
            });
            paginationNumbers.appendChild(btn);
        }
    }

    function updateStats(stats) {
        totalClients.textContent = stats.total || 0;
        activeClients.textContent = stats.active || 0;
        const percent = stats.total > 0 ? Math.round((stats.active / stats.total) * 100) : 0;
        activePercent.textContent = `${percent}% de la base`;
        newClients7d.textContent = stats.new_7d || 0;
        lastAdded.textContent = stats.last_added ? `Dernier ajout: ${stats.last_added}` : 'Dernier ajout: -';
    }

    // Supprimer
    function deleteClient(id) {
        if (!confirm('Supprimer ce client ?')) return;
        fetch(`${API_BASE}/ecoulement/clients/${id}`, { method: 'DELETE' })
            .then(res => {
                if (res.ok) {
                    loadClients(currentPage, searchInput.value);
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
            .catch(err => console.error(err));
    }

    // Ouvrir modal pour modification
    function openEditModal(id) {
        fetch(`${API_BASE}/ecoulement/clients/${id}`)
            .then(res => res.json())
            .then(client => {
                clientId.value = client.id;
                nom.value = client.nom;
                contact.value = client.contact;
                adresse.value = client.adresse || '';
                modalTitle.textContent = 'Modifier un client';
                modal.classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }

    // Fonction globale pour le bouton "Ajouter"
    window.openAddModal = function() {
        clientId.value = '';
        nom.value = '';
        contact.value = '';
        adresse.value = '';
        modalTitle.textContent = 'Ajouter un client';
        modal.classList.remove('hidden');
    };

    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            nom: nom.value,
            contact: contact.value,
            adresse: adresse.value
        };
        const id = clientId.value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_BASE}/ecoulement/clients/${id}` : `${API_BASE}/ecoulement/clients`;

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (res.ok) {
                modal.classList.add('hidden');
                loadClients(currentPage, searchInput.value);
            } else {
                alert('Erreur lors de l\'enregistrement');
            }
        })
        .catch(err => console.error(err));
    });

    // Recherche
    searchInput.addEventListener('input', function() {
        loadClients(1, this.value);
    });

    // Pagination précédente/suivante
    prevBtn.addEventListener('click', function() {
        if (currentPage > 1) loadClients(currentPage - 1, searchInput.value);
    });
    nextBtn.addEventListener('click', function() {
        if (currentPage < totalPages) loadClients(currentPage + 1, searchInput.value);
    });

    // Chargement initial
    loadClients(1);
});