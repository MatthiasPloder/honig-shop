document.getElementById('admin-login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch('/honig-shop/admin/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            window.location.href = '/honig-shop/admin/dashboard.html';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: data.message || 'Login fehlgeschlagen'
            });
        }
    } catch (error) {
        console.error('Login error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Ein Fehler ist aufgetreten'
        });
    }
}); 