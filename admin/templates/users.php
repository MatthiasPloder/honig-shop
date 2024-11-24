<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Benutzer verwalten</h2>
        <div class="d-flex gap-2">
            <input type="text" 
                   id="user-search" 
                   class="form-control" 
                   placeholder="Suche nach Name oder E-Mail...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>E-Mail</th>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Telefon</th>
                    <th>Status</th>
                    <th>Erstellt am</th>
                    <th>Letzter Login</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                <!-- Wird dynamisch gefüllt -->
            </tbody>
        </table>
    </div>
</div>

<!-- Benutzer Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Benutzer bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" id="user-id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user-email" class="form-label">E-Mail</label>
                            <input type="email" class="form-control" id="user-email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="user-phone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="user-phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user-firstname" class="form-label">Vorname</label>
                            <input type="text" class="form-control" id="user-firstname">
                        </div>
                        <div class="col-md-6">
                            <label for="user-lastname" class="form-label">Nachname</label>
                            <input type="text" class="form-control" id="user-lastname">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="user-shipping-address" class="form-label">Lieferadresse</label>
                            <textarea class="form-control" id="user-shipping-address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="user-billing-address" class="form-label">Rechnungsadresse</label>
                            <textarea class="form-control" id="user-billing-address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user-status" class="form-label">Status</label>
                            <select class="form-select" id="user-status">
                                <option value="active">Aktiv</option>
                                <option value="locked">Gesperrt</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="user-failed-attempts" class="form-label">Fehlgeschlagene Anmeldeversuche</label>
                            <input type="number" class="form-control" id="user-failed-attempts" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="user-last-login" class="form-label">Letzter Login</label>
                            <input type="text" class="form-control" id="user-last-login" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="user-date-created" class="form-label">Erstellt am</label>
                            <input type="text" class="form-control" id="user-date-created" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-warning" onclick="updateUserStatus()">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap und andere Scripts werden im dashboard.html eingebunden --> 