let productsLoaded = false;
const cart = new ShoppingCart();

async function loadProducts() {
    if (productsLoaded) {
        console.log('Produkte wurden bereits geladen');
        return;
    }

    try {
        const container = document.getElementById('products-container');
        container.innerHTML = '';

        const response = await fetch('api/get_products.php');
        const products = await response.json();
        const template = document.getElementById('product-template');

        products.forEach(product => {
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.product-name').textContent = product.productname;
            clone.querySelector('.product-description').textContent = product.description;
            clone.querySelector('.product-price').textContent = parseFloat(product.price).toFixed(2);
            clone.querySelector('.product-stock').textContent = product.stock;
            clone.querySelector('.product-image').src = product.image_url || 'assets/images/placeholder.jpg';
            clone.querySelector('.product-image').alt = product.productname;

            const addToCartBtn = clone.querySelector('.add-to-cart');
            const quantityInput = clone.querySelector('.product-quantity');

            addToCartBtn.addEventListener('click', () => {
                const quantity = parseInt(quantityInput.value);
                if (quantity > 0) {
                    cart.addItem({
                        productName: product.productname,
                        price: parseFloat(product.price),
                        quantity: quantity
                    });

                    // Toast anzeigen
                    const toast = new bootstrap.Toast(document.getElementById('cartToast'));
                    toast.show();
                }
            });

            container.appendChild(clone);
        });

        productsLoaded = true;
        console.log('Produkte erfolgreich geladen');
    } catch (error) {
        console.error('Fehler beim Laden der Produkte:', error);
    }
}

window.loadProducts = loadProducts; 