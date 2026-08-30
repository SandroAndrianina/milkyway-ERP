let currentPage = 1;
const perPage = 10;
let totalPages = 0;

document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // ÉLÉMENTS DU DOM
    // ==========================================
    const tbody = document.getElementById('stock-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const pageIndic = document.getElementById('page-indic');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const filterBtn = document.getElementById('btnFilter');

    // Filtres
    const filterDateDebut = document.getElementById('filter-date-debut');
    const filterDateFin = document.getElementById('filter-date-fin');
    const filterType = document.getElementById('filter-type');
    const filterProduit = document.getElementById('filter-produit');

    // ==========================================
    // CHARGEMENT DES PRODUITS
    // ==========================================
    function loadProducts() {
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
    }
    loadProducts();

    // ==========================================
    // MODAL UNIQUE
    // ==========================================
    const modal = document.getElementById('modalStock');
    const btnNew = document.getElementById('btnNewStock');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const backdrop = document.getElementById('modalBackdrop');

    function toggleModal() { modal.classList.toggle('hidden'); }
    btnNew.addEventListener('click', toggleModal);
    closeModal.addEventListener('click', toggleModal);
    cancelModal.addEventListener('click', toggleModal);
    backdrop.addEventListener('click', toggleModal);

    // Gestion type → cause affichée
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const causeLabel = document.getElementById('cause-label');

    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            causeLabel.textContent = this.value === 'entree' ? 'Production' : 'Non conforme';
        });
    });
    causeLabel.textContent = 'Production';

    // Soumission unique
    document.getElementById('formStock').addEventListener('submit', function(e) {
        e.preventDefault();

        const quantite = parseFloat(document.getElementById('modal-quantite').value);
        if (isNaN(quantite) || quantite <= 0) {
            alert('⚠️ La quantité doit être un nombre supérieur à 0.');
            return;
        }

        const type = document.querySelector('input[name="type"]:checked').value;
        const data = {
            produit_id: document.getElementById('modal-produit').value,
            quantite: quantite,
            date_mouvement: document.getElementById('modal-date').value,
            type: type
        };

        fetch(`${API_BASE}/stock-gestion`, {
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

    // ==========================================
    // MODAL MULTIPLE
    // ==========================================
    const multipleModal = document.getElementById('modalStockMultiple');
    const btnMultiple = document.getElementById('btnMultipleStock');
    const closeMultiple = document.getElementById('closeMultipleModal');
    const cancelMultiple = document.getElementById('cancelMultipleModal');
    const multipleBackdrop = document.getElementById('modalMultipleBackdrop');
    const tbodyMultiple = document.getElementById('multipleStockBody');

    function toggleMultiple() { multipleModal.classList.toggle('hidden'); }
    btnMultiple.addEventListener('click', toggleMultiple);
    closeMultiple.addEventListener('click', toggleMultiple);
    cancelMultiple.addEventListener('click', toggleMultiple);
    multipleBackdrop.addEventListener('click', toggleMultiple);

    // Gestion type → cause pour le batch
    const batchType = document.getElementById('batch-type');
    const batchCauseLabel = document.getElementById('batch-cause-label');

    batchType.addEventListener('change', function() {
        batchCauseLabel.textContent = this.value === 'entree' ? 'Production' : 'Non conforme';
    });
    batchType.dispatchEvent(new Event('change'));

    // Ajouter une ligne
    document.getElementById('addRowStockBtn').addEventListener('click', function() {
        const lastRow = tbodyMultiple.lastElementChild;
        const newRow = lastRow.cloneNode(true);
        newRow.querySelector('.row-produit').value = '';
        newRow.querySelector('.row-quantite').value = '';
        // Recharger les produits dans le nouveau select
        fetch(`${API_BASE}/ecoulement/produits`)
            .then(res => res.json())
            .then(produits => {
                const select = newRow.querySelector('.row-produit');
                select.innerHTML = '<option value="">Choisir...</option>';
                produits.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nom;
                    select.appendChild(opt);
                });
            });
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

    // Soumission batch
    document.getElementById('submitBatchStock').addEventListener('click', function() {
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

            const type = batchType.value;
            const cause = type === 'entree' ? 'production' : 'non_conforme';
            items.push({
                produit_id: produit,
                quantite: quantite,
                date_mouvement: document.getElementById('batch-date').value,
                type: type,
                cause: cause,
                client_id: null
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

    // ==========================================
    // FONCTIONS PRINCIPALES
    // ==========================================
    function getFilters() {
        return {
            date_debut: filterDateDebut.value,
            date_fin: filterDateFin.value,
            type: filterType.value,
            produit_id: filterProduit.value,
        };
    }

    function loadMouvements(page = 1) {
        const filters = getFilters();
        const params = new URLSearchParams({ page, perPage, ...filters });
        fetch(`${API_BASE}/stock-gestion/historique?${params}`)
            .then(res => res.json())
            .then(data => {
                renderTable(data.data);
                const total = data.total || 0;
                totalPages = Math.ceil(total / perPage);
                updatePagination(page, total);
            })
            .catch(err => console.error('Erreur chargement mouvements:', err));
    }

    function renderTable(mouvements) {
        tbody.innerHTML = '';
        if (mouvements.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-on-surface-variant">Aucun mouvement trouvé</td></tr>`;
            return;
        }
        mouvements.forEach(m => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-surface-variant hover:bg-surface-container-low/50 transition-colors';
            const typeLabel = m.type === 'entree' ? 'Entrée' : 'Sortie';
            const causeLabel = m.cause === 'production' ? 'Production' : 'Non conforme';
            tr.innerHTML = `
                <td class="py-3 px-4">${m.date_mouvement}</td>
                <td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${m.type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${typeLabel}</span></td>
                <td class="py-3 px-4 font-medium">${m.produit_nom || ''}</td>
                <td class="py-3 px-4 text-right">${m.quantite}</td>
                <td class="py-3 px-4">${causeLabel}</td>
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

    // ==========================================
    // FILTRES
    // ==========================================
    filterBtn.addEventListener('click', function() {
        loadMouvements(1);
    });

    // ==========================================
    // PAGINATION
    // ==========================================
    prevBtn.addEventListener('click', () => { if (currentPage > 1) loadMouvements(currentPage - 1); });
    nextBtn.addEventListener('click', () => { if (currentPage < totalPages) loadMouvements(currentPage + 1); });

    // ==========================================
    // EXPORT
    // ==========================================
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
        fetch(`${API_BASE}/stock-gestion/export-preview?${params}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Aucun mouvement</td></tr>';
                    return;
                }
                data.forEach(m => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="p-2">${m.date_mouvement}</td><td class="p-2">${m.type}</td><td class="p-2">${m.produit_nom || ''}</td><td class="p-2 text-right">${m.quantite}</td><td class="p-2">${m.cause || '-'}</td>`;
                    tbody.appendChild(tr);
                });
            });
    }

    document.getElementById('export-type').addEventListener('change', loadExportPreview);

    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'stock_export';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 0;
        const filters = getFilters();
        const params = new URLSearchParams({ filename, type, page, ...filters });
        window.location.href = `${API_BASE}/stock-gestion/export/csv?${params}`;
    });

    // ==========================================
    // INITIALISATION
    // ==========================================
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    filterDateDebut.value = firstDay;
    filterDateFin.value = today;
    document.getElementById('modal-date').value = today;
    document.getElementById('batch-date').value = today;

    loadMouvements(1);
});