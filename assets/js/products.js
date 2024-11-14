const cart = new ShoppingCart();

async function loadProducts() {
    try {
        const response = await fetch('index.php?action=get_products');
        const products = await response.json();
        const container = document.getElementById('products-container');
        
        products.forEach(product => {
            const template = document.getElementById('product-template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.product-image').src = `img/${product.image_url}`;
            clone.querySelector('.product-image').alt = product.productname;
            
            clone.querySelector('.product-name').textContent = product.productname;
            clone.querySelector('.product-description').textContent = product.description;
            clone.querySelector('.product-price').textContent = `€ ${parseFloat(product.price).toFixed(2)}`;
            clone.querySelector('.product-stock').textContent = product.stock_quantity;
            
            const addButton = clone.querySelector('.add-to-cart');
            const quantityInput = clone.querySelector('.product-quantity');
            
            addButton.onclick = async () => {
                const quantity = parseInt(quantityInput.value);
                
                const stockCheck = await fetch(`index.php?action=check_stock&product_id=${product.product_id}&quantity=${quantity}`);
                const stockResult = await stockCheck.json();
                
                if (stockResult.available) {
                    cart.addItem(
                        product.product_id,
                        product.productname,
                        parseFloat(product.price),
                        quantity
                    );
                    const toast = new bootstrap.Toast(document.createElement('div'));
                    toast.show();
                    alert('Produkt wurde zum Warenkorb hinzugefügt!');
                } else {
                    alert('Leider nicht genügend Produkte auf Lager!');
                }
            };
            
            container.appendChild(clone);
        });
    } catch (error) {
        console.error('Fehler beim Laden der Produkte:', error);
        document.getElementById('products-container').innerHTML = 
            '<div class="col-12"><p class="text-danger">Fehler beim Laden der Produkte. Bitte versuchen Sie es später erneut.</p></div>';
    }
}

// Lade Produkte beim Seitenaufruf
document.addEventListener('DOMContentLoaded', loadProducts); 