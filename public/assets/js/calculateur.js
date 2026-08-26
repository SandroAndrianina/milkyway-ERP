document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('product-select');
    const dateInput = document.getElementById('fabrication-date');
    const calcBtn = document.getElementById('calc-btn');
    const dlcStandard = document.getElementById('dlc-standard');
    const dlcStandardText = document.getElementById('dlc-standard-text');

    function loadProductsForSelect() {
        fetch(`${API_BASE}/produits`)
            .then(res => res.json())
            .then(produits => {
                select.innerHTML = '<option disabled selected value="">Choisir un produit artisanale...</option>';
                produits.forEach(prod => {
                    const opt = document.createElement('option');
                    opt.value = prod.id;
                    opt.textContent = `${prod.nom} (${prod.duree_conservation} jours)`;
                    select.appendChild(opt);
                });
            })
            .catch(err => console.error('Erreur chargement produits pour select:', err));
    }

    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;

    calcBtn.addEventListener('click', function() {
        const produitId = select.value;
        const dateCreation = dateInput.value;
        if (!produitId || !dateCreation) {
            alert('Veuillez sélectionner un produit et une date de fabrication.');
            return;
        }

        fetch(`${API_BASE}/dlc/calculer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ produit_id: produitId, date_creation: dateCreation })
        })
        .then(res => res.json())
        .then(data => {
            if (data.date_peremption) {
                const datePer = new Date(data.date_peremption);
                const formatDate = (d) => {
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();
                    return `${day}/${month}/${year}`;
                };
                const formatText = (d) => {
                    return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
                };
                dlcStandard.textContent = formatDate(datePer);
                dlcStandardText.textContent = formatText(datePer);
            } else {
                alert('Erreur: réponse inattendue');
            }
        })
        .catch(err => console.error('Erreur calcul DLC:', err));
    });

    loadProductsForSelect();
});