async function initializeProducts() {
    try {
        await loadCategories();
        await loadProducts();
    } catch (error) {
        console.error('Error initializing products:', error);
    }
}

async function loadProducts() {
    try {
        const response = await fetch('/honig-shop/admin/api/get_products.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            renderProductsTable(data.products);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading products:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Produkte konnten nicht geladen werden'
        });
    }
}

function renderProductsTable(products) {
    const tbody = document.getElementById('products-table-body');
    if (!products || products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Keine Produkte vorhanden</td></tr>';
        return;
    }
    
    tbody.innerHTML = products.map(product => `
        <tr>
            <td>${product.product_id}</td>
            <td>
                <img src="/honig-shop/img/${product.image_url || 'placeholder.jpg'}" 
                     alt="${product.productname}" 
                     style="height: 50px; width: auto;">
            </td>
            <td>${product.productname}</td>
            <td>${product.price} €</td>
            <td>${product.category_name}</td>
            <td>
                <button class="btn btn-sm btn-warning me-2" 
                        onclick="editProduct(${product.product_id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" 
                        onclick="deleteProduct(${product.product_id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Weitere Funktionen für das Produkt-Management
async function editProduct(productId) {
    // Implementation folgt
}

async function deleteProduct(productId) {
    // Implementation folgt
} 