class PageInitializer {
    static async init() {
        try {
            // Lade Navigation und Footer
            await Components.init();
            
            // Prüfe Login-Status
            await AuthService.checkLoginStatus();
            
            // Lade Produkte nur auf der Produktseite
            if (window.location.pathname.includes('produkte.html')) {
                await loadProducts();
            }
            
            // Initialisiere Warenkorb wenn die Funktion existiert
            if (typeof initializeCart === 'function') {
                await initializeCart();
            }
            
            // Zeige den Content
            document.body.style.visibility = 'visible';
        } catch (error) {
            console.error('Fehler bei der Initialisierung:', error);
        }
    }
}

// Warte bis das Dokument geladen ist
document.addEventListener('DOMContentLoaded', () => {
    PageInitializer.init();
}); 