console.log('products.js wird geladen');

// Globale Variable für den Ladestatus
window.productsLoaded = false;

// Globale Variablen
let allProducts = []; // Speichert alle Produkte
let filteredProducts = []; // Speichert gefilterte Produkte

async function loadProducts() {
    if (window.productsLoaded) return; // Verhindere mehrfaches Laden
    
    try {
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
            filterAndSortProducts();
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            filterAndSortProducts();
        });
    }
}

// Filter und Sortierung kombiniert
function filterAndSortProducts() {
    const searchTerm = document.getElementById('search-input')?.value.toLowerCase() || '';
    const sortValue = document.getElementById('sort-select')?.value || 'name-asc';
    
    // Erst filtern
    filteredProducts = allProducts.filter(product => {
        return product.productname.toLowerCase().includes(searchTerm) ||
               product.description.toLowerCase().includes(searchTerm);
    });
    
    // Dann sortieren
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
    
    // Gefilterte und sortierte Produkte anzeigen
    renderProducts(filteredProducts);
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