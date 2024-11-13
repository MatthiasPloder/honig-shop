// app.js

// Funktion zur Navigation und zum dynamischen Laden der Inhalte
function loadContent(route) {
    const content = document.getElementById('content');

    // Inhalt für jede Seite definieren
    let html = '';
    switch (route) {
        case '#home':
            html = `
                <div class="hero-section text-center py-5">
                    <h1>Willkommen im Honig-Shop</h1>
                    <p class="lead">Entdecken Sie unsere Vielfalt an natürlichen Honigsorten!</p>
                    <a href="#produkte" class="btn btn-primary btn-lg">Jetzt einkaufen</a>
                </div>`;
            break;

        case '#produkte':
            html = `
                <section class="product-gallery py-5">
                    <div class="container">
                        <div class="row">
                            <!-- Blütenhonig -->
                            <div class="col-md-4">
                                <div class="card">
                                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Blütenhonig">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Blütenhonig</h5>
                                        <p class="price">Ab €5.99</p>
                                        <select class="form-select size-select mb-3">
                                            <option selected>Größe wählen</option>
                                            <option>250g - €5.99</option>
                                            <option>500g - €9.99</option>
                                            <option>1kg - €17.99</option>
                                        </select>
                                        <a href="#warenkorb" class="btn btn-primary">In den Warenkorb</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Weitere Honigsorten könnten hierhin kommen -->
                        </div>
                    </div>
                </section>`;
            break;

        case '#ueber-uns':
            html = `
                <section class="about-section py-5">
                    <h2>Über Uns</h2>
                    <p>Wir sind ein familiengeführter Betrieb, der sich auf die Herstellung von hochwertigem, natürlichem Honig spezialisiert hat. Unsere Bienenvölker arbeiten in den besten Regionen, um Ihnen ein unvergleichliches Geschmackserlebnis zu bieten.</p>
                </section>`;
            break;

        case '#warenkorb':
            html = `
                <section class="cart-section py-5">
                    <h2>Warenkorb</h2>
                    <p>Ihr Warenkorb ist derzeit leer. Fügen Sie Produkte hinzu, um mit dem Einkauf fortzufahren.</p>
                </section>`;
            break;

        default:
            html = `<p>Seite nicht gefunden!</p>`;
            break;
    }

    // HTML in den Hauptcontainer einfügen
    content.innerHTML = html;
}

// Route initial laden
window.addEventListener('load', () => {
    loadContent(location.hash || '#home');
});

// Event-Listener für Hash-Änderungen (Navigation)
window.addEventListener('hashchange', () => {
    loadContent(location.hash);
});
