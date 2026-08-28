let currentPage = 1;
const perPage = 10;
let totalPages = 0;
let chartInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('mouvement-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const pageIndic = document.getElementById('page-indic');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const filterBtn = document.getElementById('btnFilter');

    // Éléments filtres
    const filterDateDebut = document.getElementById('filter-date-debut');
    const filterDateFin = document.getElementById('filter-date-fin');
    const filterType = document.getElementById('filter-type');
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
            });

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

    // === GESTION DES FILTRES (synchronisation Type / Cause / Client) ===
    function updateFilterFields() {
        const type = filterType.value;
        if (type === 'entree') {
            // Cause fixée à 'production' et désactivée
            filterCause.disabled = true;
            filterCause.value = 'production';
            filterCause.classList.add('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterCause.classList.remove('bg-surface', 'cursor-pointer');
            // Client désactivé
            filterClient.disabled = true;
            filterClient.value = '';
            filterClient.classList.add('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterClient.classList.remove('bg-surface', 'cursor-pointer');
        } else if (type === 'sortie') {
            // Cause activée avec options Vente / Non conforme
            filterCause.disabled = false;
            filterCause.innerHTML = '<option value="toutes">Toutes</option><option value="vente">Vente</option><option value="non_conforme">Non conforme</option>';
            filterCause.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterCause.classList.add('bg-surface', 'cursor-pointer');
            // Déclencher l'événement pour mettre à jour le client
            filterCause.dispatchEvent(new Event('change'));
        } else { // 'tous'
            filterCause.disabled = false;
            filterCause.innerHTML = '<option value="toutes">Toutes</option><option value="vente">Vente</option><option value="non_conforme">Non conforme</option>';
            filterCause.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterCause.classList.add('bg-surface', 'cursor-pointer');
            // Client activé
            filterClient.disabled = false;
            filterClient.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterClient.classList.add('bg-surface', 'cursor-pointer');
        }
    }

    filterType.addEventListener('change', updateFilterFields);

    // Quand la cause change, on gère le client
    filterCause.addEventListener('change', function() {
        if (this.value === 'non_conforme') {
            filterClient.disabled = true;
            filterClient.value = '';
            filterClient.classList.add('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterClient.classList.remove('bg-surface', 'cursor-pointer');
        } else if (this.value === 'vente') {
            filterClient.disabled = false;
            filterClient.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterClient.classList.add('bg-surface', 'cursor-pointer');
        } else {
            // 'toutes' ou autre
            filterClient.disabled = false;
            filterClient.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
            filterClient.classList.add('bg-surface', 'cursor-pointer');
        }
    });

    // Initialisation
    updateFilterFields();

    // === FORMULAIRE UNIQUE (modal) ===
    const modal = document.getElementById('modalMvmt');
    const btnNew = document.getElementById('btnNewMvmt');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const backdrop = document.getElementById('modalBackdrop');
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const sortieFields = document.getElementById('sortieFields');
    const modalCause = document.getElementById('modal-cause');
    const modalClient = document.getElementById('modal-client');
    const modalClientGroup = document.getElementById('modal-client-group');

    function toggleModal() { modal.classList.toggle('hidden'); }
    btnNew.addEventListener('click', toggleModal);
    closeModal.addEventListener('click', toggleModal);
    cancelModal.addEventListener('click', toggleModal);
    backdrop.addEventListener('click', toggleModal);

    // Fonction pour mettre à jour l'affichage du type
    function updateUniqueFields() {
        const selected = document.querySelector('input[name="type"]:checked');
        if (selected && selected.value === 'sortie') {
            sortieFields.classList.remove('hidden');
            // Mettre à jour les options de cause (vente, non_conforme)
            modalCause.innerHTML = '<option value="">Sélectionner...</option><option value="vente">Vente</option><option value="non_conforme">Non conforme</option>';
            modalCause.dispatchEvent(new Event('change'));
        } else {
            sortieFields.classList.add('hidden');
            // Réinitialiser cause et client
            modalCause.value = '';
            modalClient.value = '';
            modalClient.disabled = true;
            modalClientGroup.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateUniqueFields);
    });

    // Gestion cause -> client
    modalCause.addEventListener('change', function() {
        if (this.value === 'vente') {
            modalClient.disabled = false;
            modalClientGroup.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            modalClient.disabled = true;
            modalClient.value = '';
            modalClientGroup.classList.add('opacity-50', 'pointer-events-none');
        }
    });

    // Initialisation
    updateUniqueFields();

    // Soumission formulaire unique
    document.getElementById('formMvmt').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            produit_id: document.getElementById('modal-produit').value,
            quantite: document.getElementById('modal-quantite').value,
            date_mouvement: document.getElementById('modal-date').value,
            type: document.querySelector('input[name="type"]:checked').value,
            cause: document.getElementById('modal-cause').value,
            client_id: document.getElementById('modal-client').value || null
        };
        fetch(`${API_BASE}/ecoulement/mouvements`, {
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
            loadChart();
        })
        .catch(err => {
            alert('❌ ' + err.message);
            console.error(err);
        });
    });

    // === BATCH (multiple) ===
    const multipleModal = document.getElementById('modalMultipleMvmts');
    const btnMultiple = document.getElementById('btnMultipleMvmts');
    const closeMultiple = document.getElementById('closeMultipleModal');
    const cancelMultiple = document.getElementById('cancelMultipleModal');
    const multipleBackdrop = document.getElementById('modalMultipleBackdrop');
    const tbodyMultiple = document.getElementById('multipleMvmtsBody');
    const batchType = document.getElementById('batch-type');
    const batchCause = document.getElementById('batch-cause');
    const batchClient = document.getElementById('batch-client');
    const batchCauseGroup = document.getElementById('batch-cause-group');
    const batchClientGroup = document.getElementById('batch-client-group');

    function toggleMultiple() { multipleModal.classList.toggle('hidden'); }
    btnMultiple.addEventListener('click', toggleMultiple);
    closeMultiple.addEventListener('click', toggleMultiple);
    cancelMultiple.addEventListener('click', toggleMultiple);
    multipleBackdrop.addEventListener('click', toggleMultiple);

    // Synchronisation batch type -> cause/client
    function updateBatchFields() {
        if (batchType.value === 'entree') {
            batchCauseGroup.style.display = 'none';
            batchClientGroup.style.display = 'none';
            batchCause.value = '';
            batchClient.value = '';
        } else { // sortie
            batchCauseGroup.style.display = 'block';
            // Mettre à jour les options de cause
            batchCause.innerHTML = '<option value="">Sélectionner...</option><option value="vente">Vente</option><option value="non_conforme">Non conforme</option>';
            // Déclencher changement pour le client
            batchCause.dispatchEvent(new Event('change'));
        }
    }

    batchType.addEventListener('change', updateBatchFields);

    batchCause.addEventListener('change', function() {
        if (this.value === 'vente') {
            batchClientGroup.style.display = 'block';
            batchClient.disabled = false;
            batchClient.classList.remove('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
        } else {
            batchClientGroup.style.display = 'block';
            batchClient.disabled = true;
            batchClient.value = '';
            batchClient.classList.add('bg-surface-container-low', 'cursor-not-allowed', 'opacity-70');
        }
    });

    // Initialisation batch
    updateBatchFields();

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

    // Soumission batch
    document.getElementById('submitBatch').addEventListener('click', function() {
        const rows = tbodyMultiple.querySelectorAll('tr');
        const items = [];
        let valid = true;
        rows.forEach(row => {
            const produit = row.querySelector('.row-produit').value;
            const quantite = row.querySelector('.row-quantite').value;
            if (!produit || !quantite) { valid = false; return; }
            items.push({
                produit_id: produit,
                quantite: quantite,
                date_mouvement: document.getElementById('batch-date').value,
                type: batchType.value,
                cause: batchCause.value || null,
                client_id: batchClient.value || null
            });
        });
        if (!valid) { alert('Remplissez tous les champs de chaque ligne.'); return; }
        if (items.length === 0) { alert('Ajoutez au moins un mouvement.'); return; }

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
            loadChart();
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
            type: filterType.value === 'tous' ? '' : filterType.value,
            cause: filterCause.value === 'toutes' ? '' : filterCause.value,
            produit_id: filterProduit.value,
            client_id: filterClient.value,
        };
    }

    function loadMouvements(page = 1) {
        const filters = getFilters();
        const params = new URLSearchParams({ page, perPage, ...filters });
        fetch(`${API_BASE}/ecoulement/mouvements?${params}`)
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
            tbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-on-surface-variant">Aucun mouvement trouvé</td></tr>`;
            return;
        }
        mouvements.forEach(m => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-surface-variant hover:bg-surface-container-low/50 transition-colors';
            tr.innerHTML = `
                <td class="py-3 px-4">${m.date_mouvement}</td>
                <td class="py-3 px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${m.type === 'entree' ? 'bg-secondary-container text-on-secondary-container' : 'bg-primary-fixed text-on-primary-fixed-variant'}">
                        ${m.type === 'entree' ? 'Entrée' : 'Sortie'}
                    </span>
                </td>
                <td class="py-3 px-4 font-medium">${m.produit_nom || ''}</td>
                <td class="py-3 px-4 text-right ${m.type === 'entree' ? 'text-secondary' : 'text-primary'}">${m.quantite}</td>
                <td class="py-3 px-4">${m.cause || '-'}</td>
                <td class="py-3 px-4">${m.client_nom || '-'}</td>
                <td class="py-3 px-4 text-right">
                    <button class="text-outline hover:text-primary p-1"><span class="material-symbols-outlined text-[20px]">more_vert</span></button>
                </td>
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

    // === GRAPHIQUE (Chart.js) ===
    function loadChart() {
        const filters = getFilters();
        const params = new URLSearchParams(filters);
        fetch(`${API_BASE}/ecoulement/mouvements/chart?${params}`)
            .then(res => res.json())
            .then(data => {
                renderChart(data.labels, data.entree, data.sortie);
            })
            .catch(err => console.error('Erreur chargement graphique:', err));
    }

    function renderChart(labels, entrees, sorties) {
        const ctx = document.getElementById('mouvementChart').getContext('2d');
        if (chartInstance) chartInstance.destroy();
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Entrées', data: entrees, backgroundColor: '#246a51', borderRadius: 4 },
                    { label: 'Sorties', data: sorties, backgroundColor: '#084365', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Entrées / Sorties (période)' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // === FILTRE ===
    filterBtn.addEventListener('click', function() {
        loadMouvements(1);
        loadChart();
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
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Chargement...</td></tr>';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 1;
        const limit = type === 'current' ? perPage : 1000;
        const filters = getFilters();
        const params = new URLSearchParams({ page, limit, ...filters });
        fetch(`${API_BASE}/ecoulement/mouvements/export-preview?${params}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Aucun mouvement</td></tr>';
                    return;
                }
                data.forEach(m => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="p-2">${m.date_mouvement}</td><td class="p-2">${m.type}</td><td class="p-2">${m.produit_nom || ''}</td><td class="p-2 text-right">${m.quantite}</td><td class="p-2">${m.cause || '-'}</td><td class="p-2">${m.client_nom || '-'}</td>`;
                    tbody.appendChild(tr);
                });
            });
    }

    document.getElementById('export-type').addEventListener('change', loadExportPreview);

    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'mouvements_export';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 0;
        const filters = getFilters();
        const params = new URLSearchParams({ filename, type, page, ...filters });
        window.location.href = `${API_BASE}/ecoulement/mouvements/export/csv?${params}`;
    });

    document.getElementById('export-pdf-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'mouvements_export';
        const type = document.getElementById('export-type').value;
        const page = type === 'current' ? currentPage : 0;
        const filters = getFilters();
        const params = new URLSearchParams({ filename, type, page, ...filters });
        window.location.href = `${API_BASE}/ecoulement/mouvements/export/pdf?${params}`;
    });

    // ===== INIT =====
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    document.getElementById('filter-date-debut').value = firstDay;
    document.getElementById('filter-date-fin').value = today;
    document.getElementById('modal-date').value = today;
    document.getElementById('batch-date').value = today;

    // Activer le bouton "Ce mois" par défaut
    document.querySelector('.period-btn[data-period="month"]')?.classList.add('bg-primary', 'text-on-primary');

    loadMouvements(1);
    loadChart();

        // === BOUTONS PÉRIODE RAPIDE ===
    const periodBtns = document.querySelectorAll('.period-btn');
    periodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Désactiver tous les boutons
            periodBtns.forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary');
                b.classList.add('border', 'border-outline-variant', 'text-on-surface');
            });
            // Activer celui-ci
            this.classList.remove('border', 'border-outline-variant', 'text-on-surface');
            this.classList.add('bg-primary', 'text-on-primary');

            const period = this.dataset.period;
            const today = new Date();
            let dateDebut = '';
            let dateFin = today.toISOString().split('T')[0];

            if (period === 'week') {
                const debut = new Date(today);
                debut.setDate(today.getDate() - 7);
                dateDebut = debut.toISOString().split('T')[0];
            } else if (period === 'month') {
                const debut = new Date(today.getFullYear(), today.getMonth(), 1);
                dateDebut = debut.toISOString().split('T')[0];
            } else if (period === 'all') {
                dateDebut = '';
                dateFin = '';
            }

            document.getElementById('filter-date-debut').value = dateDebut;
            document.getElementById('filter-date-fin').value = dateFin;

            // Recharger instantanément (ne touche pas aux autres filtres)
            loadMouvements(1);
            loadChart();
        });
    });
});