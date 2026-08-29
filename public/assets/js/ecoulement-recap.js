document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api';
    let currentPeriod = 'week';
    let dateDebut = '';
    let dateFin = '';
    let chartInstance = null;

    // Éléments
    const dateDebutInput = document.getElementById('recap-date-debut');
    const dateFinInput = document.getElementById('recap-date-fin');
    const filterBtn = document.getElementById('recap-filter-btn');
    const periodToggles = document.querySelectorAll('.period-toggle');
    const clientsBody = document.getElementById('recap-clients-body');
    const produitsBody = document.getElementById('recap-produits-body');

    // === GRANULARITÉ AUTOMATIQUE ===
    function getGranularite(dateDebut, dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        const diffTime = Math.abs(fin - debut);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays <= 31) return 'day';
        else if (diffDays <= 90) return 'week';
        else return 'month';
    }

    // === INITIALISATION DES DATES ===
    function initDates(period) {
        const today = new Date();
        dateFin = today.toISOString().split('T')[0];
        if (period === 'week') {
            const debut = new Date(today);
            debut.setDate(today.getDate() - 7);
            dateDebut = debut.toISOString().split('T')[0];
        } else if (period === 'month') {
            const debut = new Date(today.getFullYear(), today.getMonth(), 1);
            dateDebut = debut.toISOString().split('T')[0];
        }
        dateDebutInput.value = dateDebut;
        dateFinInput.value = dateFin;
    }

    // === CHARGEMENT GRAPHIQUE ===
    function loadEvolution() {
        const granularite = getGranularite(dateDebut, dateFin);
        const params = new URLSearchParams({
            date_debut: dateDebut,
            date_fin: dateFin,
            granularite: granularite
        });
        fetch(`${API_BASE}/recap/evolution?${params}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.labels && data.datasets && data.datasets.length > 0) {
                    renderChart(data.labels, data.datasets[0].data);
                } else {
                    renderChart([], []);
                }
            })
            .catch(err => console.error('Erreur graphique:', err));
    }

    function renderChart(labels, values) {
        const canvas = document.getElementById('salesChart');
        const ctx = canvas.getContext('2d');

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(8, 67, 101, 0.2)');
        gradient.addColorStop(1, 'rgba(8, 67, 101, 0)');

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventes (Ar)',
                    data: values,
                    borderColor: '#084365',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#084365',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Ar';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#e4e2e2', borderDash: [5,5] } }
                }
            }
        });
    }

    // === CHARGEMENT TABLEAU CLIENTS ===
    function loadClients() {
        const params = new URLSearchParams({ date_debut: dateDebut, date_fin: dateFin });
        fetch(`${API_BASE}/recap/clients?${params}`)
            .then(res => res.json())
            .then(data => {
                clientsBody.innerHTML = '';
                if (data.length === 0) {
                    clientsBody.innerHTML = '<tr><td colspan="3" class="text-center py-8 text-on-surface-variant">Aucune vente</td></tr>';
                    return;
                }
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-surface-variant hover:bg-surface-container-low/50 transition-colors';
                    tr.innerHTML = `
                        <td class="py-4 px-4 font-body-md font-medium">${row.client_nom}</td>
                        <td class="py-4 px-4 text-on-surface-variant">${row.produits_livres || '-'}</td>
                        <td class="py-4 px-4 text-right font-semibold text-primary">${parseFloat(row.montant_total).toLocaleString()} Ar</td>
                    `;
                    clientsBody.appendChild(tr);
                });
            })
            .catch(err => console.error('Erreur clients:', err));
    }

    // === CHARGEMENT TABLEAU PRODUITS ===
    function loadProduits() {
        const params = new URLSearchParams({ date_debut: dateDebut, date_fin: dateFin });
        fetch(`${API_BASE}/recap/produits?${params}`)
            .then(res => res.json())
            .then(data => {
                produitsBody.innerHTML = '';
                if (data.length === 0) {
                    produitsBody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-on-surface-variant">Aucun produit</td></tr>';
                    return;
                }
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-surface-variant hover:bg-surface-container-low/50 transition-colors';
                    tr.innerHTML = `
                        <td class="py-4 px-4 font-body-md font-medium">${row.produit_nom}</td>
                        <td class="py-4 px-4 text-right text-on-surface-variant">${row.quantite_vendue}</td>
                        <td class="py-4 px-4 text-right text-error">${row.quantite_perdue || 0}</td>
                        <td class="py-4 px-4 text-right font-semibold text-primary">${parseFloat(row.valeur_totale).toLocaleString()} Ar</td>
                    `;
                    produitsBody.appendChild(tr);
                });
            })
            .catch(err => console.error('Erreur produits:', err));
    }

    // === CHARGEMENT TOUTES LES DONNÉES ===
    function loadData() {
        loadEvolution();
        loadClients();
        loadProduits();
    }

    // === ÉVÉNEMENTS PÉRIODE ===
    periodToggles.forEach(btn => {
        btn.addEventListener('click', function() {
            periodToggles.forEach(b => {
                b.classList.remove('bg-surface', 'text-primary', 'shadow-sm');
                b.classList.add('text-on-surface-variant');
            });
            this.classList.add('bg-surface', 'text-primary', 'shadow-sm');
            this.classList.remove('text-on-surface-variant');

            currentPeriod = this.dataset.period;
            initDates(currentPeriod);
            loadData();
        });
    });

    filterBtn.addEventListener('click', function() {
        dateDebut = dateDebutInput.value;
        dateFin = dateFinInput.value;
        if (!dateDebut || !dateFin) {
            alert('Veuillez sélectionner une période.');
            return;
        }
        loadData();
    });

    // === EXPORT MODAL ===
    const exportModal = document.getElementById('export-modal');
    const exportTableSelect = document.getElementById('export-table-select');
    const previewBody = document.getElementById('export-preview-body');
    const previewHead = document.getElementById('export-preview-head');

    // Ouvrir le modal d'export avec la table sélectionnée
    document.querySelectorAll('.export-table-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher tout comportement par défaut
            const table = this.dataset.table;
            exportTableSelect.value = table;
            exportModal.classList.remove('hidden');
            loadExportPreview(table);
        });
    });

    // Charger l'aperçu
    function loadExportPreview(table) {
        previewBody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Chargement...</td></tr>';
        const params = new URLSearchParams({ date_debut: dateDebut, date_fin: dateFin });
        fetch(`${API_BASE}/recap/${table}?${params}`)
            .then(res => res.json())
            .then(data => {
                previewBody.innerHTML = '';
                let headers = [];
                let rows = [];

                if (table === 'clients') {
                    headers = ['Client', 'Produits livrés', 'Montant total (Ar)'];
                    rows = data.map(row => [row.client_nom, row.produits_livres || '-', parseFloat(row.montant_total).toLocaleString() + ' Ar']);
                } else if (table === 'produits') {
                    headers = ['Produit', 'Qté vendue', 'Qté perdue', 'Valeur (Ar)'];
                    rows = data.map(row => [row.produit_nom, row.quantite_vendue, row.quantite_perdue || 0, parseFloat(row.valeur_totale).toLocaleString() + ' Ar']);
                }

                previewHead.innerHTML = `<tr class="bg-surface-container-low border-b">${headers.map(h => `<th class="p-2 font-label-md">${h}</th>`).join('')}</tr>`;

                if (rows.length === 0) {
                    previewBody.innerHTML = `<tr><td colspan="${headers.length}" class="text-center py-4">Aucune donnée</td></tr>`;
                    return;
                }
                rows.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = row.map(cell => `<td class="p-2">${cell}</td>`).join('');
                    previewBody.appendChild(tr);
                });
            })
            .catch(err => {
                previewBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-error">Erreur de chargement</td></tr>';
                console.error(err);
            });
    }

    // Mettre à jour l'aperçu quand le select change
    exportTableSelect.addEventListener('change', function() {
        loadExportPreview(this.value);
    });

    // === EXPORT CSV ===
    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'recapitulation';
        const table = exportTableSelect.value;
        const rows = previewBody.querySelectorAll('tr');
        const headers = previewHead.querySelectorAll('th');

        let csv = '';
        if (table === 'clients') {
            csv = 'Client;Produits livrés;Montant total (Ar)\n';
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 3) {
                    csv += `"${cells[0].textContent.trim()}";"${cells[1].textContent.trim()}";"${cells[2].textContent.trim()}"\n`;
                }
            });
        } else if (table === 'produits') {
            csv = 'Produit;Qté vendue;Qté perdue;Valeur (Ar)\n';
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 4) {
                    csv += `"${cells[0].textContent.trim()}";"${cells[1].textContent.trim()}";"${cells[2].textContent.trim()}";"${cells[3].textContent.trim()}"\n`;
                }
            });
        }

        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${filename}_${table}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    });

    // === EXPORT PDF (via serveur) ===
    document.getElementById('export-pdf-btn').addEventListener('click', function() {
        const filename = document.getElementById('export-filename').value || 'recapitulation';
        const table = exportTableSelect.value;
        const rows = previewBody.querySelectorAll('tr');
        const headers = previewHead.querySelectorAll('th');

        let html = `<html><head><meta charset="utf-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #084365; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .text-right { text-align: right; }
        </style></head><body>`;
        html += `<h1>Récapitulation - ${table === 'clients' ? 'Par Client' : 'Par Produit'}</h1>`;
        html += `<p>Période : ${dateDebut} → ${dateFin}</p>`;
        html += `<p>Exporté le ${new Date().toLocaleString()}</p>`;
        html += '<table><thead><tr>';
        headers.forEach(th => {
            html += `<th>${th.textContent.trim()}</th>`;
        });
        html += '</tr></thead><tbody>';
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                html += '<tr>';
                cells.forEach(td => {
                    const align = td.classList.contains('text-right') ? ' style="text-align:right;"' : '';
                    html += `<td${align}>${td.textContent.trim()}</td>`;
                });
                html += '</tr>';
            }
        });
        html += '</tbody></table></body></html>';

        fetch(`${API_BASE}/recap/export-pdf`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ html: html, filename: `${filename}_${table}` })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.error || 'Erreur PDF'); });
            }
            return res.blob();
        })
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}_${table}.pdf`;
            a.click();
            URL.revokeObjectURL(url);
        })
        .catch(err => {
            alert('❌ ' + err.message);
            console.error(err);
        });
    });

    // === EXPORT GRAPHIQUE (PNG) ===
    document.getElementById('export-chart-btn').addEventListener('click', function() {
        const canvas = document.getElementById('salesChart');
        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = canvas.width;
        exportCanvas.height = canvas.height;
        const ctx = exportCanvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
        ctx.drawImage(canvas, 0, 0);
        const link = document.createElement('a');
        link.download = 'graphique_ventes.png';
        link.href = exportCanvas.toDataURL('image/png');
        link.click();
    });

    // === INIT ===
    initDates('week');
    loadData();
});