document.addEventListener('DOMContentLoaded', async () => {
    // Prüfe Admin-Authentifizierung
    try {
        const response = await fetch('/honig-shop/admin/api/check_auth.php');
        const data = await response.json();
        
        if (data.status !== 'success') {
            window.location.href = '/honig-shop/admin/login.html';
            return;
        }
    } catch (error) {
        console.error('Auth check error:', error);
        window.location.href = '/honig-shop/admin/login.html';
        return;
    }

    // Event Listener für Navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            // Prüfe, ob das geklickte Element oder sein übergeordnetes Element data-page hat
            const target = e.target.closest('[data-page]');
            if (target) {
                const page = target.dataset.page;
                loadContent(page);
            }
        });
    });

    // Logout-Handler
    document.getElementById('logout-btn').addEventListener('click', async (e) => {
        e.preventDefault();
        try {
            await fetch('/honig-shop/admin/api/logout.php');
            window.location.href = '/honig-shop/admin/login.html';
        } catch (error) {
            console.error('Logout error:', error);
        }
    });

    // Lade Standardseite (Produkte)
    loadContent('products');
});

async function loadContent(page) {
    try {
        const response = await fetch(`templates/${page}.php`);
        const content = await response.text();
        document.getElementById('main-content').innerHTML = content;
        
        // Initialisiere die entsprechende Funktionalität
        switch(page) {
            case 'products':
                await initializeProducts();
                break;
            case 'orders':
                await initializeOrders();
                break;
            case 'users':
                await initializeUsers();
                break;
            case 'categories':
                await initializeCategories();
                break;
        }
    } catch (error) {
        console.error(`Error loading ${page}:`, error);
    }
} 