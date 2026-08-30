let currentPage = 1;
const perPage = 10;
let totalPages = 0;

document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('vente-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const pageIndic = document.getElementById('page-indic');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const filterBtn = document.getElementById('btnFilter');

    // Éléments filtres
    const filterDateDebut = document.getElementById('filter-date-debut');
    const filterDateFin = document.getElementById('filter-date-fin');
    const filterCause = document.getElementById('filter-cause');
    const filterProduit = document.getElementById('filter-produit');
    const filterClient = document.getElementById('filter-client');

    // === CHARGEMENT DES OPTIONS (produits + clients) ===
    function loadSelectOptions() {
        // Produits
        fetch(`${API_BASE}/ecoulement/produits`)
            .then(res => res.json())
            .then(produits => {
                const selects = document.querySelectorAll('#filter-produit, #modal-produit, .row-produit');
                selects.forEach(sel => {
                    const currentVal = sel.value;
                    sel.innerHTML = '<option value="">Choisir...</option>';
                    produits.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nom;
                        sel.appendChild(opt);
                    });
                    sel.value = currentVal;
                });
            })
            .catch(err => console.error('Erreur chargement produits:', err));

        // Clients (avec all=1 pour obtenir tous)
        fetch(`${API_BASE}/ecoulement/clients?all=1`)
            .then(res => res.json())
            .then(data => {
                const clients = data.data || [];
                const selects = document.querySelectorAll('#filter-client, #modal-client, #batch-client');
                selects.forEach(sel => {
                    const currentVal = sel.value;
                    sel.innerHTML = '<option value="">Sélectionner...</option>';
                    clients.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.nom;
                        sel.appendChild(opt);
                    });
                    sel.value = currentVal;
                });
            })
            .catch(err => console.error('Erreur chargement clients:', err));
    }
    loadSelectOptions();

    // === GESTION CAUSE → CLIENT (modal unique) ===
    const modalCause = document.getElementById('modal-cause');
    const modalClient = document.getElementById('modal-client');
    const modalClientGroup = document.getElementById('modal-client-group');

    modalCause.addEventListener('change', function() {
        if (this.value === 'vente') {
            modalClientGroup.style.display = 'block';
            modalClient.required = true;
        } else {
            modalClientGroup.style.display = 'none';
            modalClient.required = false;
            modalClient.value = '';
        }
    });
    modalCause.dispatchEvent(new Event('change'));

    // === GESTION CAUSE → CLIENT (batch) ===
    const batchCause = document.getElementById('batch-cause');
    const batchClient = document.getElementById('batch-client');
    const batchClientGroup = document.getElementById('batch-client-group');

    batchCause.addEventListener('change', function() {
        if (this.value === 'vente') {
            batchClientGroup.style.display = 'block';
            batchClient.required = true;
        } else {
            batchClientGroup.style.display = 'none';
            batchClient.required = false;
            batchClient.value = '';
        }
    });
    batchCause.dispatchEvent(new Event('change'));

    // === FORMULAIRE UNIQUE ===
    const modal = document.getElementById('modalMvmt');
    const btnNew = document.getElementById('btnNewMvmt');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const backdrop = document.getElementById('modalBackdrop');

    function toggleModal() { modal.classList.toggle('hidden'); }
    btnNew.addEventListener('click', toggleModal);
    closeModal.addEventListener('click', toggleModal);
    cancelModal.addEventListener('click', toggleModal);
    backdrop.addEventListener('click', toggleModal);

    document.getElementById('formVente').addEventListener('submit', function(e) {
        e.preventDefault();

        const quantite = parseFloat(document.getElementById('modal-quantite').value);
        if (isNaN(quantite) || quantite <= 0) {
            alert('⚠️ La quantité doit être un nombre entier supérieur à 0.');
            return;
        }

        const data = {
            produit_id: document.getElementById('modal-produit').value,
            quantite: quantite,
            date_mouvement: document.getElementById('modal-date').value,
            cause: document.getElementById('modal-cause').value,
            client_id: document.getElementById('modal-client').value || null,
            type: 'sortie' // ✅ toujours sortie
        };

        fetch(`${API_BASE}/ventes`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.error || 'Erreur inconnue'); });
            }
            return res.json();
        })
        .then(() => {
            toggleModal();
            loadMouvements(currentPage);
        })
        .catch(err => {
            alert('❌ ' + err.message);
            console.error(err);
        });
    });

    // === FORMULAIRE MULTIPLE ===
    const multipleModal = document.getElementById('modalMultipleMvmts');
    const btnMultiple = document.getElementById('btnMultipleMvmts');
    const closeMultiple = document.getElementById('closeMultipleModal');
    const cancelMultiple = document.getElementById('cancelMultipleModal');
    const multipleBackdrop = document.getElementById('modalMultipleBackdrop');
    const tbodyMultiple = document.getElementById('multipleMvmtsBody');

    function toggleMultiple() { multipleModal.classList.toggle('hidden'); }
    btnMultiple.addEventListener('click', toggleMultiple);
    closeMultiple.addEventListener('click', toggleMultiple);
    cancelMultiple.addEventListener('click', toggleMultiple);
    multipleBackdrop.addEventListener('click', toggleMultiple);

    // Ajouter une ligne
    document.getElementById('addRowBtn').addEventListener('click', function() {
        const lastRow = tbodyMultiple.lastElementChild;
        const newRow = lastRow.cloneNode(true);
        newRow.querySelector('.row-produit').value = '';
        newRow.querySelector('.row-quantite').value = '';
        tbodyMultiple.appendChild(newRow);
    });

    // Supprimer une ligne
    tbodyMultiple.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            if (tbodyMultiple.children.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });

    document.getElementById('submitBatch').addEventListener('click', function() {
        const rows = tbodyMultiple.querySelectorAll('tr');
        const items = [];
        let valid = true;

        rows.forEach(row => {
            const produit = row.querySelector('.row-produit').value;
            const quantite = parseFloat(row.querySelector('.row-quantite').value);

            if (!produit) {
                alert('Veuillez sélectionner un produit pour chaque ligne.');
                valid = false;
                return;
            }
            if (isNaN(quantite) || quantite <= 0) {
                alert('La quantité doit être un nombre supérieur à 0.');
                valid = false;
                return;
            }

            items.push({
                produit_id: produit,
                quantite: quantite,
                date_mouvement: document.getElementById('batch-date').value,
                type: 'sortie',
                cause: document.getElementById('batch-cause').value || null,
                client_id: document.getElementById('batch-client').value || null
            });
        });

        if (!valid) return;
        if (items.length === 0) {
            alert('Ajoutez au moins un mouvement.');
            return;
        }

        fetch(`${API_BASE}/ecoulement/mouvements/batch`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(items)
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.error || 'Erreur inconnue'); });
            }
            return res.json();
        })
        .then(() => {
            toggleMultiple();
            loadMouvements(currentPage);
        })
        .catch(err => {
            alert('❌ ' + err.message);
            console.error(err);
        });
    });

    // === FONCTIONS PRINCIPALES ===
    function getFilters() {
        return {
            date_debut: filterDateDebut.value,
            date_fin: filterDateFin.value,
            cause: filterCause.value === 'toutes' ? '' : filterCause.value,
            produit_id: filterProduit.value,
            client_id: filterClient.value,
        };
    }

    function loadMouvements(page = 1) {
        const filters = getFilters();
        const params = new URLSearchParams({ page, perPage, ...filters });
        fetch(`${API_BASE}/ventes/historique?${params}`)
            .then(res => res.json())
            .then(data => {
                renderTable(data.data);
                const total = data.total || 0;
                totalPages = Math.ceil(total / perPage);
                updatePagination(page, total);
            })
            .catch(err => console.error('Erreur chargement sorties:', err));
    }

    function renderTable(mouvements) {
        tbody.innerHTML = '';
        if (mouvements.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-on-surface-variant">Aucune sortie trouvée</td></tr>`;
            return;
        }
        mouvements.forEach(m => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-surface-variant hover:bg-surface-container-low/50 transition-colors';
            const causeLabel = m.cause === 'vente' ? 'Vente' : 'Non conforme / Perte';
            tr.innerHTML = `
                <td class="py-3 px-4">${m.date_mouvement}</td>
                <td class="py-3 px-4 font-medium">${m.produit_nom || ''}</td>
                <td class="py-3 px-4 text-right">${m.quantite}</td>
                <td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${m.cause === 'vente' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${causeLabel}</span></td>
                <td class="py-3 px-4">${m.client_nom || '-'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updatePagination(page, total) {
        currentPage = page;
        const start = (page - 1) * perPage + 1;
        const end = Math.min(page * perPage, total);
        paginationInfo.textContent = `Affichage ${start}-${end} sur ${total}`;
        pageIndic.textContent = page;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages || totalPages === 0;
    }

    // === FILTRE ===
    filterBtn.addEventListener('click', function() {
        loadMouvements(1);
    });

    // === PAGINATION ===
    prevBtn.addEventListener('click', () => { if (currentPage > 1) loadMouvements(currentPage - 1); });
    nextBtn.addEventListener('click', () => { if (currentPage < totalPages) loadMouvements(currentPage + 1); });

    // === EXPORT ===
    document.getElementById('export-btn').addEventListener('click', function() {
        document.getElementById('export-modal').classList.remove('hidden');
        loadExportPreview();
    });

    function loadExportPreview() {
        const tbody = document.getElementById('export-preview-body');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Chargement...</td></tr>';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 1;
        const limit = type === 'current' ? perPage : 1000;
        const filters = getFilters();
        const params = new URLSearchParams({ page, limit, ...filters });
        fetch(`${API_BASE}/ventes/export-preview?${params}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Aucune sortie</td></tr>';
                    return;
                }
                data.forEach(m => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="p-2">${m.date_mouvement}</td><td class="p-2">${m.produit_nom || ''}</td><td class="p-2 text-right">${m.quantite}</td><td class="p-2">${m.cause || '-'}</td><td class="p-2">${m.client_nom || '-'}</td>`;
                    tbody.appendChild(tr);
                });
            });
    }

    document.getElementById('export-type').addEventListener('change', loadExportPreview);

    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'sorties_export';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 0;
        const filters = getFilters();
        const params = new URLSearchParams({ filename, type, page, ...filters });
        window.location.href = `${API_BASE}/ventes/export/csv?${params}`;
    });

    document.getElementById('export-pdf-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'sorties_export';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 0;
        const filters = getFilters();
        const params = new URLSearchParams({ filename, type, page, ...filters });
        window.location.href = `${API_BASE}/ventes/export/pdf?${params}`;
    });

    // ===== INIT =====
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    filterDateDebut.value = firstDay;
    filterDateFin.value = today;
    document.getElementById('modal-date').value = today;
    document.getElementById('batch-date').value = today;

    loadMouvements(1);
});