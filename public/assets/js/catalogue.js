console.log('catalogue.js chargé');

// === Récupération des éléments (existent au moment du chargement) ===
const tbody = document.getElementById('product-tbody');
const modal = document.getElementById('product-modal');
const form = document.getElementById('product-form');
const productId = document.getElementById('product-id');
const productName = document.getElementById('product-name');
const shelfLife = document.getElementById('shelf-life');
const modalTitle = document.getElementById('modal-title');

// === Fonctions CRUD ===
function loadProducts() {
    fetch(`${API_BASE}/produits`)
        .then(res => res.json())
        .then(produits => {
            tbody.innerHTML = '';
            if (produits.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="py-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
                    <p class="font-body-md">Aucun produit dans le catalogue.</p>
                </td></tr>`;
                return;
            }
            produits.forEach(prod => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-surface-container-lowest transition-colors h-[56px] group';
                tr.innerHTML = `
                    <td class="py-3 px-6 font-body-md text-on-surface flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[18px]">egg_alt</span>
                        </div>
                        ${prod.nom}
                    </td>
                    <td class="py-3 px-6 font-body-md text-on-surface-variant">
                        <span class="bg-surface-container py-1 px-3 rounded-full text-sm font-medium border border-outline-variant/30">${prod.duree_conservation} jours</span>
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex items-center justify-end gap-2 transition-opacity">
                            <button class="edit-btn w-8 h-8 rounded-full hover:bg-primary/10 text-primary transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <button class="delete-btn w-8 h-8 rounded-full hover:bg-error-container text-error transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            // Attacher les événements aux boutons
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    deleteProduct(this.dataset.id);
                });
            });
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    openEditModal(this.dataset.id);
                });
            });
        })
        .catch(err => console.error('Erreur chargement produits:', err));
}

function deleteProduct(id) {
    if (!confirm('Supprimer ce produit ?')) return;
    fetch(`${API_BASE}/produits/${id}`, { method: 'DELETE' })
        .then(res => {
            if (res.ok) loadProducts();
            else alert('Erreur lors de la suppression');
        })
        .catch(err => console.error(err));
}

function openEditModal(id) {
    console.log('🔍 Récupération du produit ID:', id);
    fetch(`${API_BASE}/produits/${id}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(prod => {
            console.log('📦 Produit récupéré :', prod);
            productId.value = prod.id;
            productName.value = prod.nom;
            shelfLife.value = prod.duree_conservation;
            modalTitle.textContent = 'Modifier un produit';
            modal.classList.remove('hidden');
        })
        .catch(err => {
            console.error('❌ Erreur chargement produit :', err);
            alert('Impossible de charger les données du produit.');
        });
}

// === Fonction globale pour le bouton "Ajouter" ===
function openAddModal() {
    productId.value = '';
    productName.value = '';
    shelfLife.value = '';
    modalTitle.textContent = 'Ajouter un produit';
    modal.classList.remove('hidden');
}

// === Gestion du formulaire ===
form.addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('📤 Formulaire soumis');

    const data = {
        nom: productName.value,
        duree_conservation: parseInt(shelfLife.value)
    };
    console.log('📦 Données :', data);

    const id = productId.value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${API_BASE}/produits/${id}` : `${API_BASE}/produits`;
    console.log(`🔗 ${method} ${url}`);

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('📡 Status :', response.status);
        return response.json().then(json => ({ status: response.status, json }));
    })
    .then(({ status, json }) => {
        console.log('📨 Réponse :', json);
        if (status === 200 || status === 201) {
            modal.classList.add('hidden');
            loadProducts();
        } else {
            alert('❌ Erreur : ' + (json.message || 'Inconnue'));
        }
    })
    .catch(err => console.error('❌ Erreur fetch :', err));
});

// === Recherche ===
document.getElementById('search-input').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
        const nom = row.querySelector('td:first-child')?.textContent?.toLowerCase() || '';
        row.style.display = nom.includes(term) ? '' : 'none';
    });
});

// === Chargement initial ===
loadProducts();