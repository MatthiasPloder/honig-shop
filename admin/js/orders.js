// Globale Variable für die aktive Bestell-ID
let activeOrderId = null;

async function initializeOrders() {
    try {
        await loadOrders();
        setupFilters();
    } catch (error) {
        console.error('Error initializing orders:', error);
    }
}

async function loadOrders() {
    try {
        const response = await fetch('/honig-shop/admin/api/get_orders.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            renderOrdersTable(data.orders);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Bestellungen konnten nicht geladen werden'
        });
    }
}

function renderOrdersTable(orders) {
    const tbody = document.getElementById('orders-table-body');
    tbody.innerHTML = orders.map(order => `
        <tr>
            <td>${order.order_id}</td>
            <td>${new Date(order.order_date).toLocaleString('de-DE')}</td>
            <td>${order.user_email}</td>
            <td>${order.total_price} €</td>
            <td>
                <span class="badge bg-${getOrderStatusBadgeColor(order.order_status)}">
                    ${getOrderStatusText(order.order_status)}
                </span>
            </td>
            <td>
                <span class="badge bg-${getPaymentStatusBadgeColor(order.payment_status)}">
                    ${getPaymentStatusText(order.payment_status)}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-warning" 
                        onclick="viewOrderDetails(${order.order_id})">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getOrderStatusBadgeColor(status) {
    const colors = {
        'pending': 'warning',
        'processing': 'info',
        'shipped': 'primary',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function getPaymentStatusBadgeColor(status) {
    const colors = {
        'unpaid': 'danger',
        'paid': 'success'
    };
    return colors[status] || 'secondary';
}

function getOrderStatusText(status) {
    const texts = {
        'pending': 'Ausstehend',
        'processing': 'In Bearbeitung',
        'shipped': 'Versendet',
        'completed': 'Abgeschlossen',
        'cancelled': 'Storniert'
    };
    return texts[status] || status;
}

function getPaymentStatusText(status) {
    const texts = {
        'unpaid': 'Unbezahlt',
        'paid': 'Bezahlt'
    };
    return texts[status] || status;
}

async function viewOrderDetails(orderId) {
    try {
        const response = await fetch(`/honig-shop/admin/api/get_order_details.php?id=${orderId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.status === 'success') {
            const { order, items } = data;
            
            // Speichere die aktive Bestell-ID
            activeOrderId = order.order_id;
            
            // Bestellinfos
            document.getElementById('order-info').innerHTML = `
                <p><strong>Bestell-ID:</strong> ${order.order_id}</p>
                <p><strong>Datum:</strong> ${new Date(order.order_date).toLocaleString('de-DE')}</p>
                <p><strong>Status:</strong> ${getOrderStatusText(order.order_status)}</p>
                <p><strong>Zahlungsstatus:</strong> ${getPaymentStatusText(order.payment_status)}</p>
                <p><strong>Gesamtpreis:</strong> ${order.total_price} €</p>
            `;
            
            // Kundeninfos
            document.getElementById('customer-info').innerHTML = `
                <p><strong>E-Mail:</strong> ${order.user_email || 'Nicht verfügbar'}</p>
                <p><strong>Lieferadresse:</strong><br>${order.shipping_address || 'Nicht verfügbar'}</p>
                <p><strong>Rechnungsadresse:</strong><br>${order.billing_address || 'Nicht verfügbar'}</p>
            `;
            
            // Bestellte Produkte
            document.getElementById('order-items').innerHTML = items.map(item => `
                <tr>
                    <td>${item.productname}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price} €</td>
                    <td>${item.total_price} €</td>
                </tr>
            `).join('');
            
            // Status-Dropdowns aktualisieren
            document.getElementById('order-status').value = order.order_status;
            document.getElementById('payment-status').value = order.payment_status;
            
            // Modal öffnen
            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            orderModal.show();
        } else {
            throw new Error(data.message || 'Fehler beim Laden der Bestelldetails');
        }
    } catch (error) {
        console.error('Error loading order details:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Bestelldetails konnten nicht geladen werden: ' + error.message
        });
    }
}

async function updateOrderStatus() {
    if (!activeOrderId) {
        console.error('Keine aktive Bestell-ID gefunden');
        return;
    }

    const newOrderStatus = document.getElementById('order-status').value;
    const newPaymentStatus = document.getElementById('payment-status').value;
    
    try {
        const response = await fetch('/honig-shop/admin/api/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: activeOrderId,
                order_status: newOrderStatus,
                payment_status: newPaymentStatus
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            await loadOrders();
            bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Erfolg',
                text: 'Bestellstatus wurde aktualisiert'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error updating order status:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Status konnte nicht aktualisiert werden'
        });
    }
}

function setupFilters() {
    const orderStatusFilter = document.getElementById('order-status-filter');
    const paymentStatusFilter = document.getElementById('payment-status-filter');
    
    orderStatusFilter.addEventListener('change', filterOrders);
    paymentStatusFilter.addEventListener('change', filterOrders);
}

async function filterOrders() {
    const orderStatus = document.getElementById('order-status-filter').value;
    const paymentStatus = document.getElementById('payment-status-filter').value;
    
    try {
        let url = '/honig-shop/admin/api/get_orders.php?';
        const params = [];
        
        if (orderStatus) {
            params.push(`order_status=${orderStatus}`);
        }
        if (paymentStatus) {
            params.push(`payment_status=${paymentStatus}`);
        }
        
        url += params.join('&');
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.status === 'success') {
            renderOrdersTable(data.orders);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error filtering orders:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Bestellungen konnten nicht gefiltert werden'
        });
    }
} 