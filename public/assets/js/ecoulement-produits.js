document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('product-tbody');
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const productId = document.getElementById('product-id');
    const nom = document.getElementById('nom');
    const duree = document.getElementById('duree');
    const prix = document.getElementById('prix');
    const modalTitle = document.getElementById('modal-title');

    // Charger la liste
    function loadProducts() {
        fetch(`${API_BASE}/ecoulement/produits`)
            .then(res => res.json())
            .then(produits => {
                tbody.innerHTML = '';
                if (produits.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
                        <p class="font-body-md">Aucun produit.</p>
                    </td></tr>`;
                    return;
                }
                produits.forEach(prod => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-surface-container-low/50 transition-colors';
                    tr.innerHTML = `
                        <td class="py-3 px-6 font-body-md">${prod.nom}</td>
                        <td class="py-3 px-6 font-body-md">${prod.duree_conservation}</td>
                        <td class="py-3 px-6 font-body-md">${prod.prix_vente} Ar</td>
                        <td class="py-3 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
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
            })
            .catch(err => console.error('Erreur chargement produits:', err));
    }

    // Supprimer
    function deleteProduct(id) {
        if (!confirm('Supprimer ce produit ?')) return;
        fetch(`${API_BASE}/ecoulement/produits/${id}`, { method: 'DELETE' })
            .then(res => {
                if (res.ok) loadProducts();
                else alert('Erreur lors de la suppression');
            })
            .catch(err => console.error(err));
    }

    // Ouvrir modal pour modification
    function openEditModal(id) {
        fetch(`${API_BASE}/ecoulement/produits/${id}`)
            .then(res => res.json())
            .then(prod => {
                productId.value = prod.id;
                nom.value = prod.nom;
                duree.value = prod.duree_conservation;
                prix.value = prod.prix_vente;
                modalTitle.textContent = 'Modifier un produit';
                modal.classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }

    // Fonction globale pour le bouton "Ajouter"
    window.openAddModal = function() {
        productId.value = '';
        nom.value = '';
        duree.value = '';
        prix.value = '';
        modalTitle.textContent = 'Ajouter un produit';
        modal.classList.remove('hidden');
    };

    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            nom: nom.value,
            duree_conservation: parseInt(duree.value),
            prix_vente: parseInt(prix.value)
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

    // Chargement initial
    loadProducts();
});