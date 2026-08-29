document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('product-grid');
    const searchInput = document.getElementById('search-product');
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const productId = document.getElementById('product-id');
    const nom = document.getElementById('nom');
    const duree = document.getElementById('duree');
    const prix = document.getElementById('prix');
    const seuil = document.getElementById('seuil');
    const modalTitle = document.getElementById('modal-title');

    // === FILTRAGE INSTANTANÉ ===
    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const cards = grid.querySelectorAll('.card-item');
        cards.forEach(card => {
            const nom = card.querySelector('h3')?.textContent?.toLowerCase() || '';
            card.style.display = nom.includes(query) ? '' : 'none';
        });
    }

    function formatPrice(value) {
        return Number(value).toLocaleString('fr-FR') + ' Ar';
    }

    searchInput.addEventListener('input', filterProducts);

    // === CHARGEMENT DE LA LISTE ===
    function loadProducts() {
        fetch(`${API_BASE}/ecoulement/produits`)
            .then(res => res.json())
            .then(produits => {
                grid.innerHTML = '';
                if (produits.length === 0) {
                    grid.innerHTML = `<div class="col-span-full text-center py-12 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
                        <p class="font-body-md">Aucun produit.</p>
                    </div>`;
                    return;
                }
                produits.forEach(prod => {
                    const card = document.createElement('div');
                    // ✅ Ajout de la classe 'card-item' pour le filtrage
                    card.className = 'card-item bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(8,67,101,0.05)] border border-outline-variant/30 flex flex-col justify-between transition-all hover:shadow-md';
                    card.innerHTML = `
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-headline-sm text-headline-sm text-on-surface">${prod.nom}</h3>
                                <span class="text-xs font-label-sm text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded-full">${prod.duree_conservation} j</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant mb-3">
                                <span class="font-label-md text-label-md">${formatPrice(prod.prix_vente)}</span>
                                <span class="w-px h-4 bg-outline-variant"></span>
                                <span class="font-label-md text-label-md">Seuil : ${prod.seuil_critique || 50}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/30">
                            <button class="edit-btn w-8 h-8 rounded-full hover:bg-primary/10 text-primary transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <button class="delete-btn w-8 h-8 rounded-full hover:bg-error-container text-error transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    `;
                    grid.appendChild(card);
                });

                // Attacher les événements
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

                // Réappliquer le filtre
                filterProducts();
            })
            .catch(err => console.error('Erreur chargement produits:', err));
    }

    // === CRUD ===
    function deleteProduct(id) {
        if (!confirm('Supprimer ce produit ?')) return;
        fetch(`${API_BASE}/ecoulement/produits/${id}`, { method: 'DELETE' })
            .then(res => {
                if (res.ok) loadProducts();
                else alert('Erreur lors de la suppression');
            })
            .catch(err => console.error(err));
    }

    function openEditModal(id) {
        fetch(`${API_BASE}/ecoulement/produits/${id}`)
            .then(res => res.json())
            .then(prod => {
                productId.value = prod.id;
                nom.value = prod.nom;
                duree.value = prod.duree_conservation;
                prix.value = prod.prix_vente;
                seuil.value = prod.seuil_critique || 50;
                modalTitle.textContent = 'Modifier un produit';
                modal.classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }

    window.openAddModal = function() {
        productId.value = '';
        nom.value = '';
        duree.value = '';
        prix.value = '';
        seuil.value = '';
        modalTitle.textContent = 'Ajouter un produit';
        modal.classList.remove('hidden');
    };

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            nom: nom.value,
            duree_conservation: parseInt(duree.value),
            prix_vente: parseInt(prix.value),
            seuil_critique: parseInt(seuil.value) || 50
        };
        const id = productId.value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_BASE}/ecoulement/produits/${id}` : `${API_BASE}/ecoulement/produits`;

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (res.ok) {
                modal.classList.add('hidden');
                loadProducts();
            } else {
                alert('Erreur lors de l\'enregistrement');
            }
        })
        .catch(err => console.error(err));
    });

    // === INIT ===
    loadProducts();
});