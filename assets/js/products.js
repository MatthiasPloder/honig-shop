console.log('products.js wird geladen');

// Globale Variable für den Ladestatus
window.productsLoaded = false;

// Globale Variablen
let allProducts = []; // Speichert alle Produkte
let filteredProducts = []; // Speichert gefilterte Produkte
let categories = []; // Neu

async function loadCategories() {
    try {
        const response = await fetch('/honig-shop/api/get_categories.php');
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status === 'success' && Array.isArray(data.categories)) {
            categories = data.categories;
            renderCategoryButtons();
        } else {
            throw new Error('Ungültiges Datenformat von der API');
        }
    } catch (error) {
        console.error('Detaillierter Fehler beim Laden der Kategorien:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler beim Laden der Kategorien',
            text: error.message
        });
    }
}

function renderCategoryButtons() {
    const buttonContainer = document.querySelector('.category-buttons');
    if (!buttonContainer) {
        console.error('Category-Buttons Container nicht gefunden');
        return;
    }

    const categoryButtons = categories.map(category => `
        <button class="btn btn-outline-warning me-2 mb-2 category-btn" data-category="${category.id}">
            ${category.name}
        </button>
    `).join('');
    
    buttonContainer.insertAdjacentHTML('beforeend', categoryButtons);

    document.querySelectorAll('.category-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Aktiven Button markieren
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-outline-warning');
            });
            button.classList.add('active');
            button.classList.remove('btn-outline-warning');
            button.classList.add('btn-warning');
            
            const selectedCategory = button.dataset.category;
            filterProductsByCategory(selectedCategory);
        });
    });
}

function filterProductsByCategory(categoryId) {
    // Zuerst nach Kategorie filtern
    if (categoryId === 'all') {
        filteredProducts = [...allProducts];
    } else {
        filteredProducts = allProducts.filter(product => 
            product.category_id.toString() === categoryId.toString()
        );
    }
    
    // Dann die bestehende Filter- und Sortierfunktion aufrufen
    filterAndSortProducts(true); // Neuer Parameter, um anzuzeigen, dass bereits gefiltert wurde
}

function filterAndSortProducts(categoryAlreadyFiltered = false) {
    const searchTerm = document.getElementById('search-input')?.value.toLowerCase() || '';
    const sortValue = document.getElementById('sort-select')?.value || 'name-asc';
    
    // Nur nach Kategorie filtern, wenn nicht bereits geschehen
    if (!categoryAlreadyFiltered) {
        const activeCategory = document.querySelector('.category-btn.active')?.dataset.category;
        if (activeCategory && activeCategory !== 'all') {
            filteredProducts = allProducts.filter(product =>
                product.category_id.toString() === activeCategory.toString()
            );
        } else {
            filteredProducts = [...allProducts];
        }
    }
    
    // Nach Suchbegriff filtern
    if (searchTerm) {
        filteredProducts = filteredProducts.filter(product =>
            product.productname.toLowerCase().includes(searchTerm) ||
            product.description.toLowerCase().includes(searchTerm)
        );
    }
    
    // Sortierung korrigiert
    filteredProducts.sort((a, b) => {
        switch(sortValue) {
            case 'name-asc':
                return a.productname.localeCompare(b.productname);
            case 'name-desc':
                return b.productname.localeCompare(a.productname);
            case 'price-asc':
                return parseFloat(a.price) - parseFloat(b.price);
            case 'price-desc':
                return parseFloat(b.price) - parseFloat(a.price);
            default:
                return 0;
        }
    });
    
    renderProducts(filteredProducts);
}

async function loadProducts() {
    if (window.productsLoaded) return; // Verhindere mehrfaches Laden
    
    try {
        // Zuerst Kategorien laden
        await loadCategories();
        
        const response = await fetch('/honig-shop/api/get_products.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status !== 'success') {
            throw new Error(data.message || 'Fehler beim Laden der Produkte');
        }

        allProducts = data.products; // Alle Produkte speichern
        filteredProducts = [...allProducts]; // Kopie für Filter
        
        renderProducts(filteredProducts); // Initial alle Produkte anzeigen
        
        // Event Listener für Filter hinzufügen
        setupFilterListeners();
    } catch (error) {
        console.error('Fehler beim Laden der Produkte:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Produkte konnten nicht geladen werden: ' + error.message
        });
    }
}

// Filter-Listener einrichten
function setupFilterListeners() {
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            filterAndSortProducts(); // Statt applyCurrentFilters()
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            filterAndSortProducts(); // Statt applyCurrentFilters()
        });
    }
}

// Produkte rendern
function renderProducts(products) {
    const container = document.getElementById('products-container');
    if (!container) return;
    
    if (products.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-center">Keine Produkte gefunden.</p></div>';
        return;
    }
    
    container.innerHTML = products.map(product => createProductCard(product)).join('');
}

function createProductCard(product) {
    const price = parseFloat(product.price);
    const formattedPrice = isNaN(price) ? '0.00' : price.toFixed(2);
    
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="img/${product.image_url || '/honig-shop/img/platzhalter.jpg'}" 
                     class="card-img-top" 
                     alt="${product.productname}"
                     onerror="this.src='/honig-shop/assets/img/platzhalter.jpg';"
                     style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${product.productname}</h5>
                    <p class="card-text">${product.description || 'Keine Beschreibung verfügbar'}</p>
                    <p class="card-text">
                        <strong>${formattedPrice} €</strong><br>
                        <small class="text-muted">${product.weight}g</small>
                    </p>
                    ${product.stock_quantity > 0 ? `
                        <button class="btn btn-primary add-to-cart-btn" 
                                onclick="addToCart(${product.product_id})"
                                data-product-id="${product.product_id}">
                            In den Warenkorb
                        </button>
                    ` : `
                        <button class="btn btn-secondary" disabled>
                            Nicht verfügbar
                        </button>
                    `}
                </div>
            </div>
        </div>
    `;
}

async function addToCart(productId) {
    try {
        // Prüfe Login-Status vor dem Hinzufügen zum Warenkorb
        const isLoggedIn = await AuthService.checkLoginStatus();
        if (!isLoggedIn) {
            Swal.fire({
                icon: 'info',
                title: 'Anmeldung erforderlich',
                text: 'Bitte melden Sie sich an oder registrieren Sie sich, um Produkte in den Warenkorb zu legen.',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Login',
                denyButtonText: 'Registrieren',
                cancelButtonText: 'Zurück'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Zum Login
                    window.location.href = '/honig-shop/login_user/login.html?redirect=' + encodeURIComponent(window.location.pathname);
                } else if (result.isDenied) {
                    // Zur Registrierung
                    window.location.href = '/honig-shop/login_user/register.html?redirect=' + encodeURIComponent(window.location.pathname);
                }
            });
            return;
        }

        const response = await fetch('/honig-shop/api/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.status === 'success') {
            await window.cart.init();
            
            const toast = new bootstrap.Toast(document.getElementById('cartToast'));
            toast.show();
            
            Swal.fire({
                icon: 'success',
                title: 'Erfolg!',
                text: 'Produkt wurde zum Warenkorb hinzugefügt',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            throw new Error(data.message || 'Fehler beim Hinzufügen zum Warenkorb');
        }
    } catch (error) {
        console.error('Fehler:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: error.message || 'Produkt konnte nicht zum Warenkorb hinzugefügt werden'
        });
    }
}

window.loadProducts = loadProducts; 