class PageInitializer {
    static async waitForElement(selector) {
        while (!document.querySelector(selector)) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        return document.querySelector(selector);
    }

    static async init() {
        try {
            // Komponenten laden
            await Components.init();
            
            // Warte auf die Navigation-Elemente
            await this.waitForElement('#accountText');
            await this.waitForElement('#accountDropdown');
            
            // Login-Status prüfen
            await checkLoginStatus();
            
            // Warenkorb initialisieren
            const cart = new ShoppingCart();
            cart.updateCartCount();

            // Wenn wir auf der Produkteseite sind, lade die Produkte
            if (window.location.pathname.includes('produkte.html')) {
                await loadProducts();
            }
            
            console.log('Initialisierung abgeschlossen');
        } catch (error) {
            console.error('Fehler bei der Initialisierung:', error);
        }
    }
}

// Globale Initialisierung für alle Seiten
document.addEventListener('DOMContentLoaded', async () => {
    await PageInitializer.init();
}); 