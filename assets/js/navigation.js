document.addEventListener('DOMContentLoaded', function() {
    // Aktuelle Seite in der Navigation hervorheben
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Warenkorb-Badge aktualisieren
    const cart = new ShoppingCart();
    cart.updateCartDisplay();
}); 