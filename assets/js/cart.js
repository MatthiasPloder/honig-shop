class ShoppingCart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
        this.updateCartBadge();
    }

    addItem(id, productName, price, quantity) {
        const existingItem = this.items.find(item => item.id === id);

        if (existingItem) {
            existingItem.quantity += parseInt(quantity);
        } else {
            this.items.push({
                id,
                productName,
                price,
                quantity: parseInt(quantity)
            });
        }

        this.saveCart();
        this.updateCartBadge();
    }

    removeItem(index) {
        this.items.splice(index, 1);
        this.saveCart();
        this.updateCartBadge();
    }

    updateQuantity(index, newQuantity) {
        if (newQuantity < 1) {
            this.removeItem(index);
            return;
        }
        this.items[index].quantity = parseInt(newQuantity);
        this.saveCart();
        this.updateCartBadge();
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getTotalItems() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    updateCartBadge() {
        const badges = document.querySelectorAll('.cart-count');
        const totalItems = this.getTotalItems();
        badges.forEach(badge => {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? 'inline' : 'none';
        });
    }

    clearCart() {
        this.items = [];
        this.saveCart();
        this.updateCartBadge();
    }
} 