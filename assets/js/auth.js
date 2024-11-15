async function checkLoginStatus() {
    try {
        const response = await fetch('api/check_login_status.php');
        const data = await response.json();
        
        const accountText = document.getElementById('accountText');
        const accountDropdown = document.getElementById('accountDropdown');
        
        if (data.isLoggedIn) {
            accountText.textContent = data.userEmail;
            accountDropdown.innerHTML = `
                <li><a class="dropdown-item" href="login_user/dashboard/dashboard.html">Mein Account</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="confirmAndLogout(); return false;">Ausloggen</a></li>
            `;
        } else {
            accountText.textContent = 'Account';
            accountDropdown.innerHTML = `
                <li><a class="dropdown-item" href="login_user/login.html">Login</a></li>
                <li><a class="dropdown-item" href="login_user/register.html">Registrieren</a></li>
            `;
        }
    } catch (error) {
        console.error('Fehler beim Prüfen des Login-Status:', error);
    }
}

async function handleLogout() {
    try {
        const response = await fetch('/login_user/api/process_logout.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            const toast = new bootstrap.Toast(document.getElementById('logoutToast'));
            toast.show();
            
            setTimeout(() => {
                window.location.href = '/index.html';
            }, 1500);
        } else {
            alert('Fehler beim Ausloggen. Bitte versuchen Sie es erneut.');
        }
    } catch (error) {
        console.error('Logout-Fehler:', error);
        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
    }
}

async function confirmAndLogout() {
    const result = await Swal.fire({
        title: 'Ausloggen?',
        text: "Möchten Sie sich wirklich ausloggen?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ja, ausloggen',
        cancelButtonText: 'Abbrechen'
    });

    if (result.isConfirmed) {
        handleLogout();
    }
}

// Mache die Funktionen global verfügbar
window.checkLoginStatus = checkLoginStatus;
window.handleLogout = handleLogout;
window.confirmAndLogout = confirmAndLogout; 