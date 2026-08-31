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

    // === FORMATAGE PRIX ===
    function formatPrice(value) {
        return Number(value).toLocaleString('fr-FR') + ' Ar';
    }

    // === FILTRAGE INSTANTANÉ ===
    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const cards = grid.querySelectorAll('.card-item');
        cards.forEach(card => {
            const nom = card.querySelector('h3')?.textContent?.toLowerCase() || '';
            card.style.display = nom.includes(query) ? '' : 'none';
        });
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
                    card.className = 'card-item bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-surface-variant/30 flex flex-col justify-between transition-all hover:shadow-md hover:-translate-y-0.5 duration-200';
                    
                    // === GÉNÉRATION DE L'IMAGE (ou placeholder) ===
                    let imageHtml = '';
                    if (prod.image) {
                        imageHtml = `<img src="/${prod.image}" alt="${prod.nom}" class="w-full h-32 object-cover rounded-t-lg mb-3">`;
                    } else {
                        imageHtml = `<div class="w-full h-32 bg-surface-container-low rounded-t-lg flex items-center justify-center text-on-surface-variant mb-3"><span class="material-symbols-outlined text-4xl">image</span></div>`;
                    }
                    
                    card.innerHTML = `
                        <div>
                            ${imageHtml}
                            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1.5">${prod.nom}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-on-surface-variant mb-4">
                                <span class="font-label-md text-label-md text-primary font-semibold">${formatPrice(prod.prix_vente)}</span>
                                <span class="w-px h-4 bg-outline-variant hidden sm:block"></span>
                                <span class="font-label-sm text-label-sm">${prod.duree_conservation} jours</span>
                                <span class="w-px h-4 bg-outline-variant hidden sm:block"></span>
                                <span class="font-label-sm text-label-sm">Seuil : ${prod.seuil_critique || 50}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-1 pt-4 border-t border-surface-variant/30">
                            <button class="edit-btn w-9 h-9 rounded-full hover:bg-primary/10 text-primary transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <button class="delete-btn w-9 h-9 rounded-full hover:bg-error-container text-error transition-colors flex items-center justify-center" data-id="${prod.id}">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    `;
                    grid.appendChild(card);
                });

                // Attacher événements
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

                filterProducts();
            })
            .catch(err => console.error('Erreur chargement produits:', err));
    }

    // === SUPPRIMER ===
    function deleteProduct(id) {
        if (!confirm('Supprimer ce produit ?')) return;
        fetch(`${API_BASE}/ecoulement/produits/${id}`, { method: 'DELETE' })
            .then(res => {
                if (res.ok) loadProducts();
                else alert('Erreur lors de la suppression');
            })
            .catch(err => console.error(err));
    }

    // === MODIFICATION (charger les données dans le modal) ===
    function openEditModal(id) {
        fetch(`${API_BASE}/ecoulement/produits/${id}`)
            .then(res => res.json())
            .then(prod => {
                productId.value = prod.id;
                nom.value = prod.nom;
                duree.value = prod.duree_conservation;
                prix.value = prod.prix_vente;
                seuil.value = prod.seuil_critique || 50;

                // Afficher l'image existante
                const preview = document.getElementById('current-image-preview');
                const img = document.getElementById('current-image');
                if (prod.image) {
                    img.src = '/' + prod.image;
                    preview.classList.remove('hidden');
                } else {
                    preview.classList.add('hidden');
                }
                document.getElementById('image').value = ''; // réinitialiser le champ file

                modalTitle.textContent = 'Modifier un produit';
                modal.classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }

    // === AJOUT (réinitialiser le formulaire) ===
    window.openAddModal = function() {
        productId.value = '';
        nom.value = '';
        duree.value = '';
        prix.value = '';
        seuil.value = '';
        document.getElementById('image').value = '';
        document.getElementById('current-image-preview').classList.add('hidden');
        modalTitle.textContent = 'Ajouter un produit';
        modal.classList.remove('hidden');
    };

    // === SOUMISSION (avec FormData) ===
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('nom', nom.value);
        formData.append('duree_conservation', parseInt(duree.value));
        formData.append('prix_vente', parseInt(prix.value));
        formData.append('seuil_critique', parseInt(seuil.value) || 50);

        const fileInput = document.getElementById('image');
        if (fileInput.files.length > 0) {
            formData.append('image', fileInput.files[0]);
        }

        const id = productId.value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_BASE}/ecoulement/produits/${id}` : `${API_BASE}/ecoulement/produits`;

        fetch(url, {
            method: method,
            body: formData, // pas de Content-Type car FormData le gère
        })
        .then(res => {
            if (res.ok) {
                modal.classList.add('hidden');
                loadProducts();
            } else {
                return res.json().then(err => { throw new Error(err.message || 'Erreur inconnue'); });
            }
        })
        .catch(err => alert('❌ ' + err.message));
    });

    // === CHARGEMENT INITIAL ===
    loadProducts();
});