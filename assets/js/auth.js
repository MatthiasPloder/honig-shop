class AuthService {
    static async checkLoginStatus() {
        try {
            const response = await fetch('/honig-shop/login_user/api/check_auth.php', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'include'
            });

            if (!response.ok) {
                throw new Error('Netzwerk-Antwort war nicht ok');
            }

            const data = await response.json();
            
            if (data.status === 'success') {
                updateNavigation(data.isLoggedIn, data.user?.email);
                return data.isLoggedIn;
            } else {
                console.error('Auth-Status-Fehler:', data.message);
                updateNavigation(false);
                return false;
            }
        } catch (error) {
            console.error('Fehler beim Prüfen des Login-Status:', error);
            updateNavigation(false);
            return false;
        }
    }

    static async login(email, password) {
        try {
            const response = await fetch('/honig-shop/login_user/api/process_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: 'Login erfolgreich',
                    timer: 1500,
                    showConfirmButton: false
                });
                window.location.href = '/honig-shop/login_user/dashboard/dashboard.html';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Login fehlgeschlagen',
                text: error.message || 'Bitte überprüfen Sie Ihre Eingaben'
            });
        }
    }

    static async logout() {
        try {
            const response = await fetch('/honig-shop/login_user/api/process_logout.php');
            const data = await response.json();
            
            if (data.status === 'success') {
                sessionStorage.clear();
                localStorage.clear();
                
                document.cookie.split(";").forEach(function(c) { 
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
                });

                await Swal.fire({
                    icon: 'success',
                    title: 'Erfolgreich ausgeloggt',
                    text: 'Sie werden zur Startseite weitergeleitet...',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                window.location.href = '/honig-shop/index.html';
            }
        } catch (error) {
            console.error('Logout error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Fehler beim Ausloggen',
                text: 'Bitte versuchen Sie es erneut.'
            });
        }
    }

    static async register(formData) {
        try {
            const response = await fetch('/login_user/api/process_register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: 'Registrierung erfolgreich',
                    text: 'Sie können sich jetzt einloggen',
                    timer: 1500,
                    showConfirmButton: false
                });
                window.location.href = '/login_user/login.html';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Registrierung fehlgeschlagen',
                text: error.message || 'Bitte versuchen Sie es später erneut'
            });
        }
    }
}

function updateNavigation(isLoggedIn, email = null) {
    const accountDropdown = document.getElementById('accountDropdown');
    const accountText = document.getElementById('accountText');
    
    if (!accountDropdown || !accountText) return;

    if (isLoggedIn && email) {
        accountText.innerHTML = `<i class="bi bi-person-circle"></i> ${email}`;
        accountDropdown.innerHTML = `
            <li><a class="dropdown-item" href="/honig-shop/login_user/dashboard/profile.html">
                <i class="bi bi-person"></i> Mein Profil
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="AuthService.logout(); return false;">
                <i class="bi bi-box-arrow-right"></i> Ausloggen
            </a></li>
        `;
    } else {
        accountText.innerHTML = `<i class="bi bi-person-circle"></i> Account`;
        accountDropdown.innerHTML = `
            <li><a class="dropdown-item" href="/honig-shop/login_user/login.html">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a></li>
            <li><a class="dropdown-item" href="/honig-shop/login_user/register.html">
                <i class="bi bi-person-plus"></i> Registrieren
            </a></li>
        `;
    }
}

// Event Listener für Login/Register Forms
document.addEventListener('DOMContentLoaded', () => {
    // Prüfe Login-Status beim Laden der Seite
    AuthService.checkLoginStatus();
    
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            await AuthService.login(email, password);
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value
            };
            await AuthService.register(formData);
        });
    }
});