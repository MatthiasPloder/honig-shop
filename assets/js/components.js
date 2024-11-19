// Navigation und Footer laden
class Components {
    static async loadNavigation() {
        const navHtml = `
            <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
                <div class="container">
                    <a class="navbar-brand" href="/honig-shop/index.html">
                        <img src="/honig-shop/img/platzhalter.jpg" alt="Platzhalter" height="40">
                        Imkerei Ploder
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/honig-shop/index.html">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/honig-shop/produkte.html">Produkte</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/honig-shop/ueber-uns.html">Über uns</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/honig-shop/warenkorb.html">
                                    <i class="bi bi-cart3"></i>
                                    <span class="cart-count badge bg-danger">0</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="accountDropdownLink" 
                                   role="button" data-bs-toggle="dropdown">
                                    <i class="bi"></i>
                                    <span id="accountText">Account</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" id="accountDropdown">
                                    <li><a class="dropdown-item" href="/honig-shop/login_user/login.html">Login</a></li>
                                    <li><a class="dropdown-item" href="/honig-shop/login_user/register.html">Registrieren</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        `;
        document.body.insertAdjacentHTML('afterbegin', navHtml);
        
        // Nach dem Einfügen der Navigation den Cart-Count aktualisieren
        if (window.cart) {
            window.cart.updateCartCount();
        }
    }

    static async loadFooter() {
        try {
            const footerHtml = `
                <footer class="bg-dark text-light py-4 mt-auto">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Kontakt</h5>
                                <p>
                                    Imkerei Ploder<br>
                                    Glauning 118<br>
                                    8093 Wittmannsdorf<br>
                                    Tel: +43 664 123456<br>
                                    E-Mail: haplo522@hotmail.com
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>Links</h5>
                                <ul class="list-unstyled">
                                    <li><a href="impressum.html" class="text-light">Impressum</a></li>
                                    <li><a href="datenschutz.html" class="text-light">Datenschutz</a></li>
                                    <li><a href="agb.html" class="text-light">AGB</a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>Newsletter</h5>
                                <form class="mb-3">
                                    <div class="input-group">
                                        <input type="email" class="form-control" placeholder="Ihre E-Mail-Adresse">
                                        <button class="btn btn-warning" type="submit">Anmelden</button>
                                    </div>
                                </form>
                                <div class="social-icons">
                                    <a href="#" class="text-light me-2"><i class="bi bi-facebook"></i></a>
                                    <a href="#" class="text-light me-2"><i class="bi bi-instagram"></i></a>
                                    <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-4">
                        <div class="text-center">
                            <p class="mb-0">&copy; 2024 Honig-Shop. Alle Rechte vorbehalten.</p>
                        </div>
                    </div>
                </footer>`;

            document.body.insertAdjacentHTML('beforeend', footerHtml);
            console.log('Footer geladen'); // Debug-Log
        } catch (error) {
            console.error('Fehler beim Laden des Footers:', error);
        }
    }

    // Initialisierung der Komponenten
    static async init() {
        await this.loadNavigation();
        await this.loadFooter();
    }
} 