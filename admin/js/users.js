let allUsers = []; // Globale Variable für alle Benutzer

async function initializeUsers() {
    try {
        await loadUsers();
        setupUserSearch();
    } catch (error) {
        console.error('Error initializing users:', error);
    }
}

async function loadUsers() {
    try {
        const response = await fetch('/honig-shop/admin/api/get_users.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            allUsers = data.users; // Speichere alle Benutzer
            renderUsersTable(data.users);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading users:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Benutzer konnten nicht geladen werden'
        });
    }
}

function setupUserSearch() {
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredUsers = allUsers.filter(user => 
                user.email.toLowerCase().includes(searchTerm) ||
                (user.first_name && user.first_name.toLowerCase().includes(searchTerm)) ||
                (user.last_name && user.last_name.toLowerCase().includes(searchTerm))
            );
            renderUsersTable(filteredUsers);
        });
    }
}

function renderUsersTable(users) {
    const tbody = document.getElementById('users-table-body');
    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Keine Benutzer gefunden</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.user_id}</td>
            <td>${user.email || '-'}</td>
            <td>${user.first_name || '-'}</td>
            <td>${user.last_name || '-'}</td>
            <td>${user.phone_number || '-'}</td>
            <td>
                <span class="badge bg-${getStatusBadgeColor(user.account_status)}">
                    ${getStatusText(user.account_status)}
                </span>
            </td>
            <td>${formatDate(user.date_created)}</td>
            <td>${formatDate(user.last_login)}</td>
            <td>
                <button class="btn btn-sm btn-warning me-2" onclick="editUser(${user.user_id})">
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getStatusBadgeColor(status) {
    switch(status) {
        case 'active': return 'success';
        case 'locked': return 'danger';
        default: return 'secondary';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'active': return 'Aktiv';
        case 'locked': return 'Gesperrt';
        default: return status || 'Unbekannt';
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';
        return date.toLocaleString('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return '-';
    }
}

async function editUser(userId) {
    const user = allUsers.find(u => u.user_id === userId);
    if (!user) return;

    // Modal mit Benutzerdaten füllen
    document.getElementById('user-id').value = user.user_id;
    document.getElementById('user-email').value = user.email || '';
    document.getElementById('user-firstname').value = user.first_name || '';
    document.getElementById('user-lastname').value = user.last_name || '';
    document.getElementById('user-phone').value = user.phone_number || '';
    document.getElementById('user-status').value = user.account_status || 'active';

    // Modal öffnen
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    userModal.show();
}

async function updateUserStatus() {
    const userId = document.getElementById('user-id').value;
    const newStatus = document.getElementById('user-status').value;
    const userData = {
        user_id: userId,
        email: document.getElementById('user-email').value,
        first_name: document.getElementById('user-firstname').value,
        last_name: document.getElementById('user-lastname').value,
        phone_number: document.getElementById('user-phone').value,
        account_status: newStatus
    };

    try {
        const response = await fetch('/honig-shop/admin/api/update_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Modal schließen
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            // Benutzerliste neu laden
            await loadUsers();
            
            Swal.fire({
                icon: 'success',
                title: 'Erfolg',
                text: 'Benutzer wurde erfolgreich aktualisiert'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error updating user:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Der Benutzer konnte nicht aktualisiert werden'
        });
    }
} 