// Navigation und Footer laden
class Components {
    static async loadNavigation() {
        try {
            const navHtml = `
                <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
                    <div class="container">
                        <a class="navbar-brand" href="index.html">
                            <img src="assets/images/logo.png" alt="Honig-Shop Logo" height="40">
                            Honig-Shop
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="produkte.html">Produkte</a></li>
                                <li class="nav-item"><a class="nav-link" href="ueber-uns.html">Über Uns</a></li>
                            </ul>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="warenkorb.html">
                                        <i class="bi bi-cart"></i> Warenkorb
                                        <span class="badge bg-danger cart-badge cart-count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person"></i> <span id="accountText">Account</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" id="accountDropdown" aria-labelledby="navbarDropdown">
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>`;

            document.body.insertAdjacentHTML('afterbegin', navHtml);
            console.log('Navigation geladen'); // Debug-Log
        } catch (error) {
            console.error('Fehler beim Laden der Navigation:', error);
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
                                    Honig-Shop GmbH<br>
                                    Bienenweg 123<br>
                                    12345 Honigstadt<br>
                                    Tel: +49 123 456789<br>
                                    E-Mail: info@honig-shop.de
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