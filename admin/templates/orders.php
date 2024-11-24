<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Bestellungen verwalten</h2>
        <div class="d-flex gap-2">
            <select class="form-select" id="order-status-filter">
                <option value="">Alle Bestellstatus</option>
                <option value="pending">Ausstehend</option>
                <option value="processing">In Bearbeitung</option>
                <option value="shipped">Versendet</option>
                <option value="completed">Abgeschlossen</option>
                <option value="cancelled">Storniert</option>
            </select>
            <select class="form-select" id="payment-status-filter">
                <option value="">Alle Zahlungsstatus</option>
                <option value="unpaid">Unbezahlt</option>
                <option value="paid">Bezahlt</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Bestell-ID</th>
                    <th>Datum</th>
                    <th>Kunde</th>
                    <th>Gesamtpreis</th>
                    <th>Bestellstatus</th>
                    <th>Zahlungsstatus</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="orders-table-body">
                <!-- Wird dynamisch gefüllt -->
            </tbody>
        </table>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bestelldetails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Bestellinformationen</h6>
                        <div id="order-info">
                            <!-- Wird dynamisch gefüllt -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Kundendaten</h6>
                        <div id="customer-info">
                            <!-- Wird dynamisch gefüllt -->
                        </div>
                    </div>
                </div>
                <h6>Bestellte Produkte</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produkt</th>
                                <th>Menge</th>
                                <th>Preis</th>
                                <th>Gesamt</th>
                            </tr>
                        </thead>
                        <tbody id="order-items">
                            <!-- Wird dynamisch gefüllt -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Bestellstatus ändern</h6>
                            <select class="form-select" id="order-status">
                                <option value="pending">Ausstehend</option>
                                <option value="processing">In Bearbeitung</option>
                                <option value="shipped">Versendet</option>
                                <option value="completed">Abgeschlossen</option>
                                <option value="cancelled">Storniert</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h6>Zahlungsstatus ändern</h6>
                            <select class="form-select" id="payment-status">
                                <option value="unpaid">Unbezahlt</option>
                                <option value="paid">Bezahlt</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-warning" onclick="updateOrderStatus()">Status aktualisieren</button>
            </div>
        </div>
    </div>
</div> 