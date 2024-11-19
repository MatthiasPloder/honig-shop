class ShoppingCart {
    constructor() {
        this.items = [];
    }

    async init() {
        try {
            const response = await fetch('api/get_cart.php');
            const data = await response.json();
            
            if (data.status === 'success') {
                this.items = data.items;
                this.updateCartCount();
            }
            return this;
        } catch (error) {
            console.error('Fehler beim Laden des Warenkorbs:', error);
            throw error;
        }
    }

    async updateQuantity(index, newQuantity) {
        if (newQuantity < 1) return;
        
        try {
            const item = this.items[index];
            const response = await fetch('api/sync_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: this.items.map((cartItem, idx) => {
                        if (idx === index) {
                            return {
                                productId: item.product_id,
                                quantity: newQuantity
                            };
                        }
                        return {
                            productId: cartItem.product_id,
                            quantity: cartItem.quantity
                        };
                    })
                })
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                await this.init();
                await renderCart(); // Warenkorb neu rendern
            }
        } catch (error) {
            console.error('Fehler beim Aktualisieren der Menge:', error);
            throw error;
        }
    }
    async removeItem(index) {
        try {
            const filteredItems = this.items.filter((_, idx) => idx !== index);
            const response = await fetch('api/sync_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: filteredItems.map(item => ({
                        productId: item.product_id,
                        quantity: item.quantity
                    }))
                })
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                await this.init();
                await renderCart(); // Warenkorb neu rendern
            }
        } catch (error) {
            console.error('Fehler beim Entfernen aus dem Warenkorb:', error);
            throw error;
        }
    }

    async clearCart() {
        try {
            const response = await fetch('api/sync_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ items: [] })
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                await this.init();
                await renderCart(); // Warenkorb neu rendern
            }
        } catch (error) {
            console.error('Fehler beim Leeren des Warenkorbs:', error);
            throw error;
        }
    }

    getTotal() {
        return this.items.reduce((sum, item) => {
            return sum + (parseFloat(item.price) * parseInt(item.quantity));
        }, 0);
    }

    updateCartCount() {
        const totalItems = this.items.reduce((sum, item) => {
            return sum + parseInt(item.quantity);
        }, 0);
        
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = totalItems;
            element.style.display = totalItems > 0 ? 'inline' : 'none';
        });
    }
}

// Globale Instanz erstellen
window.cart = new ShoppingCart();

