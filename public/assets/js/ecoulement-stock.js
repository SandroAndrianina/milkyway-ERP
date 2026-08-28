document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('stock-tbody');
    const valeurTotaleEl = document.getElementById('valeur-totale');
    const dateMajEl = document.getElementById('date-maj');
    let chartInstance = null;
    let currentMode = 'quantite'; // 'quantite' ou 'financier'
    let stockData = [];

    // --- Gestion du toggle ---
    const toggleBtns = document.querySelectorAll('.toggle-mode-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Désactiver tous
            toggleBtns.forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                b.classList.add('text-on-surface-variant');
            });
            // Activer celui-ci
            this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            this.classList.remove('text-on-surface-variant');

            currentMode = this.dataset.mode;
            renderChart(stockData); // Recharger le graphique avec le nouveau mode
        });
    });

    // --- Chargement des données ---
    function loadStock() {
        fetch(`${API_BASE}/ecoulement/stock`)
            .then(res => res.json())
            .then(data => {
                stockData = data.produits || [];
                const valeurTotale = data.valeur_totale || 0;
                valeurTotaleEl.textContent = valeurTotale.toLocaleString() + ' Ar';
                dateMajEl.textContent = new Date().toLocaleString();

                renderTable(stockData);
                renderChart(stockData);
            })
            .catch(err => {
                console.error('Erreur chargement stock:', err);
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-error">Erreur de chargement</td></tr>`;
            });
    }

    // --- Rendu du tableau ---
    function renderTable(produits) {
        tbody.innerHTML = '';
        if (produits.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-on-surface-variant">Aucun produit trouvé.</td></tr>`;
            return;
        }

        produits.forEach(p => {
            const tr = document.createElement('tr');
            const statut = p.statut;
            let badgeClass, badgeText, bgClass = '';
            if (statut === 'rupture') {
                badgeClass = 'bg-error text-on-error';
                badgeText = 'Rupture';
                bgClass = 'bg-error-container/5 border-l-4 border-l-error';
            } else if (statut === 'critique') {
                badgeClass = 'bg-error text-on-error';
                badgeText = 'Seuil critique';
                bgClass = 'bg-error-container/5 border-l-4 border-l-error';
            } else {
                badgeClass = 'bg-secondary-container text-on-secondary-container';
                badgeText = 'Optimal';
                bgClass = '';
            }

            tr.className = `hover:bg-surface-container-low/50 transition-colors h-[64px] ${bgClass}`;
            tr.innerHTML = `
                <td class="p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-primary-container/20 flex items-center justify-center flex-shrink-0 text-primary">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <span class="font-medium text-on-background">${p.nom}</span>
                    </div>
                </td>
                <td class="p-4 text-right font-medium ${statut !== 'optimal' ? 'text-error' : ''}">${p.quantite}</td>
                <td class="p-4 text-right font-medium">${p.total.toLocaleString()} Ar</td>
                <td class="p-4 text-center">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full ${badgeClass} font-label-sm text-label-sm gap-1">
                        ${statut !== 'optimal' ? '<span class="material-symbols-outlined text-[14px]">warning</span>' : '<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>'}
                        ${badgeText}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // --- Rendu du graphique ---
    function renderChart(produits) {
        const canvas = document.getElementById('stockChart');
        const ctx = canvas.getContext('2d');

        // ✅ Destruction propre du graphique existant
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        // ✅ Nettoyer le canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const labels = produits.map(p => p.nom);
        const colors = ['#084365', '#246a51', '#2a5b7e', '#ba1a1a', '#585753', '#8fd5b6', '#a4d2fb'];

        // Sélectionner les données selon le mode
        let dataValues, label, unit;
        if (currentMode === 'financier') {
            dataValues = produits.map(p => p.total || 0);
            label = 'Valeur (Ar)';
            unit = ' Ar';
        } else {
            dataValues = produits.map(p => p.quantite || 0);
            label = 'Quantité';
            unit = ' unités';
        }

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: dataValues,
                    backgroundColor: colors.slice(0, dataValues.length),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + unit;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: label }
                    }
                }
            }
        });
    }

// === EXPORT STOCK ===
document.getElementById('export-table-btn').addEventListener('click', function() {
    document.getElementById('export-modal').classList.remove('hidden');
    loadExportPreview();
});

function loadExportPreview() {
    const tbody = document.getElementById('export-preview-body');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Chargement...</td></tr>';
    
    const type = document.getElementById('export-type').value;
    // Pour l'état des stocks, on prend toujours toutes les données
    fetch(`${API_BASE}/ecoulement/stock`)
        .then(res => res.json())
        .then(data => {
            const produits = data.produits || [];
            tbody.innerHTML = '';
            if (produits.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Aucun produit</td></tr>';
                return;
            }
            produits.forEach(p => {
                const tr = document.createElement('tr');
                const statut = p.statut;
                let badgeText = '';
                if (statut === 'rupture') badgeText = 'Rupture';
                else if (statut === 'critique') badgeText = 'Seuil critique';
                else badgeText = 'Optimal';
                
                tr.innerHTML = `
                    <td class="p-2">${p.nom}</td>
                    <td class="p-2 text-right">${p.quantite}</td>
                    <td class="p-2 text-right">${p.total.toLocaleString()} Ar</td>
                    <td class="p-2 text-center">${badgeText}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error('Erreur chargement aperçu:', err));
}

document.getElementById('export-type').addEventListener('change', loadExportPreview);

// Export CSV
document.getElementById('export-csv-btn').addEventListener('click', function() {
    const filename = document.getElementById('export-filename').value || 'etat_stock';
    const rows = document.querySelectorAll('#export-preview-body tr');
    
    // En-têtes
    let csv = 'Produit;Quantité;Prix Total (Ar);Statut\n';
    
    // Données
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 4) {
            const nom = cells[0].textContent.trim();
            const qte = cells[1].textContent.trim();
            const total = cells[2].textContent.trim();
            const statut = cells[3].textContent.trim();
            csv += `"${nom}";"${qte}";"${total}";"${statut}"\n`;
        }
    });

    // Téléchargement
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' }); // BOM pour UTF-8
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});

// Export PDF
document.getElementById('export-pdf-btn').addEventListener('click', function() {
    const filename = document.getElementById('export-filename').value || 'etat_stock';
    const rows = document.querySelectorAll('#export-preview-body tr');
    
    // Générer le même HTML que pour l'aperçu
    let html = `<html><head><meta charset="utf-8"><style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #084365; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style></head><body>`;
    html += '<h1>État des stocks</h1>';
    html += '<p>Exporté le ' + new Date().toLocaleString() + '</p>';
    html += '<table><thead><tr><th>Produit</th><th class="text-right">Quantité</th><th class="text-right">Prix Total</th><th class="text-center">Statut</th></tr></thead><tbody>';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 4) {
            html += `<tr><td>${cells[0].textContent.trim()}</td><td class="text-right">${cells[1].textContent.trim()}</td><td class="text-right">${cells[2].textContent.trim()}</td><td class="text-center">${cells[3].textContent.trim()}</td></tr>`;
        }
    });
    html += '</tbody></table></body></html>';

    // Envoyer au serveur pour génération PDF
    fetch(`${API_BASE}/ecoulement/stock/export-pdf`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ html: html, filename: filename })
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
        a.download = filename + '.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    })
    .catch(err => {
        alert('❌ ' + err.message);
        console.error(err);
    });
});

// --- Export graphique PNG avec fond blanc ---
document.getElementById('export-chart-btn')?.addEventListener('click', function() {
    const canvas = document.getElementById('stockChart');
    const exportCanvas = document.createElement('canvas');
    exportCanvas.width = canvas.width;
    exportCanvas.height = canvas.height;
    const ctx = exportCanvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
    ctx.drawImage(canvas, 0, 0);
    const link = document.createElement('a');
    link.download = 'graphique_stock.png';
    link.href = exportCanvas.toDataURL('image/png');
    link.click();
});

    // --- Chargement initial ---
    loadStock();
});