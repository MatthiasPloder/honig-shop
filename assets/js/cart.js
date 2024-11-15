class ShoppingCart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
    }

    addItem(id, name, price, quantity) {
        const existingItem = this.items.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({ id, name, price, quantity });
        }
        
        this.saveCart();
        this.updateCartCount();
    }

    removeItem(index) {
        this.items.splice(index, 1);
        this.saveCart();
        this.updateCartCount();
    }

    updateQuantity(index, quantity) {
        if (quantity > 0) {
            this.items[index].quantity = quantity;
        } else {
            this.items.splice(index, 1);
        }
        this.saveCart();
        this.updateCartCount();
    }

    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    clearCart() {
        this.items = [];
        this.saveCart();
        this.updateCartCount();
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    }

    updateCartCount() {
        const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = totalItems;
        });
    }
}

function updateCartCount() {
    const cart = new ShoppingCart();
    cart.updateCartCount();
}

document.addEventListener('DOMContentLoaded', updateCartCount);