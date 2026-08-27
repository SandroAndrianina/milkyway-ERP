document.addEventListener('DOMContentLoaded', function() {
    const clientId = window.location.pathname.split('/').pop();
    const nomEl = document.getElementById('client-nom');
    const contactEl = document.getElementById('client-contact');
    const adresseEl = document.getElementById('client-adresse');
    const totalAcheteEl = document.getElementById('total-achete');
    const tbody = document.getElementById('historique-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const pageIndic = document.getElementById('page-indic');
    const prevBtn = document.getElementById('prev-hist');
    const nextBtn = document.getElementById('next-hist');

    const dateDebut = document.getElementById('date-debut');
    const dateFin = document.getElementById('date-fin');
    const filterBtn = document.getElementById('filter-date-btn');
    const clearBtn = document.getElementById('clear-date-btn');

    let currentPage = 1;
    let perPage = 10;
    let total = 0;
    let totalPages = 0;
    let currentPeriod = 'month';
    let customDateDebut = '';
    let customDateFin = '';

    // Charger les infos du client
    function loadClientInfo() {
        fetch(`${API_BASE}/ecoulement/clients/${clientId}`)
            .then(res => res.json())
            .then(client => {
                nomEl.textContent = client.nom || 'Client inconnu';
                contactEl.textContent = client.contact || '-';
                adresseEl.textContent = client.adresse || '-';
                document.title = `Détails - ${client.nom}`;
            })
            .catch(err => console.error('Erreur chargement client:', err));
    }

    // Charger l'historique des achats
    function loadHistorique(page = 1, period = currentPeriod, dateDebut = '', dateFin = '') {
        let url = `${API_BASE}/ecoulement/clients/${clientId}/achats?page=${page}&perPage=${perPage}`;
        
        if (period) {
            url += `&period=${period}`;
        }
        if (dateDebut) {
            url += `&date_debut=${dateDebut}`;
        }
        if (dateFin) {
            url += `&date_fin=${dateFin}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const achats = data.data || [];
                total = data.total || 0;
                const totalAchete = data.total_achete || 0;
                totalPages = Math.ceil(total / perPage);
                renderHistorique(achats);
                updatePagination(page);
                updateTotalAchete(totalAchete);
            })
            .catch(err => {
                console.error('Erreur chargement historique:', err);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-error">Erreur de chargement</td></tr>`;
            });
    }

    function renderHistorique(achats) {
        tbody.innerHTML = '';
        if (achats.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-on-surface-variant">Aucun achat trouvé pour cette période.</td></tr>`;
            return;
        }
        achats.forEach(a => {
            const tr = document.createElement('tr');
            tr.className = 'table-row-hover border-b border-outline-variant/50 h-[56px] transition-colors bg-surface-container-lowest';
            tr.innerHTML = `
                <td class="py-sm px-md whitespace-nowrap">${a.date || '-'}</td>
                <td class="py-sm px-md font-medium text-primary">${a.produit_nom || '-'}</td>
                <td class="py-sm px-md text-right">${a.quantite || 0}</td>
                <td class="py-sm px-md text-right text-on-surface-variant">${a.prix_unitaire ? a.prix_unitaire.toLocaleString() + ' Ar' : '-'}</td>
                <td class="py-sm px-md text-right font-semibold">${a.total ? a.total.toLocaleString() + ' Ar' : '-'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updatePagination(page) {
        currentPage = page;
        pageIndic.textContent = page;
        const start = (page - 1) * perPage + 1;
        const end = Math.min(page * perPage, total);
        paginationInfo.textContent = `Affichage ${start}-${end} sur ${total}`;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages || totalPages === 0;
    }

    function updateTotalAchete(total) {
        document.getElementById('total-achete').textContent = total.toLocaleString() + ' Ar';
    }

    // Gestion du filtre par dates personnalisé
    filterBtn.addEventListener('click', function() {
        const debut = dateDebut.value;
        const fin = dateFin.value;
        if (debut && fin && debut > fin) {
            alert('La date de début doit être antérieure à la date de fin.');
            return;
        }
        currentPeriod = '';
        currentPage = 1;
        loadHistorique(1, '', debut, fin);
    });

    clearBtn.addEventListener('click', function() {
        dateDebut.value = '';
        dateFin.value = '';
        currentPeriod = 'month';
        currentPage = 1;
        // Désactiver tous les boutons période et réactiver "Ce mois"
        document.querySelectorAll('.period-btn').forEach(b => {
            b.classList.remove('bg-primary', 'text-on-primary');
            b.classList.add('border', 'border-outline-variant', 'text-on-surface');
        });
        document.querySelectorAll('.period-btn').forEach(b => {
            if (b.dataset.period === 'month') {
                b.classList.remove('border', 'border-outline-variant', 'text-on-surface');
                b.classList.add('bg-primary', 'text-on-primary');
            }
        });
        loadHistorique(1, 'month');
    });

    // Événements des filtres de période
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Désactiver tous les boutons
            document.querySelectorAll('.period-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary');
                b.classList.add('border', 'border-outline-variant', 'text-on-surface');
            });
            // Activer celui-ci
            this.classList.remove('border', 'border-outline-variant', 'text-on-surface');
            this.classList.add('bg-primary', 'text-on-primary');

            // Vider les champs de date
            dateDebut.value = '';
            dateFin.value = '';

            currentPeriod = this.dataset.period;
            currentPage = 1;
            loadHistorique(1, currentPeriod);
        });
    });

    // Pagination
    prevBtn.addEventListener('click', function() {
        if (currentPage > 1) {
            loadHistorique(currentPage - 1, currentPeriod, dateDebut.value, dateFin.value);
        }
    });
    nextBtn.addEventListener('click', function() {
        if (currentPage < totalPages) {
            loadHistorique(currentPage + 1, currentPeriod, dateDebut.value, dateFin.value);
        }
    });

    // Chargement initial
    loadClientInfo();
    loadHistorique(1, 'month');
});