<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Produkte verwalten</h2>
        <button class="btn btn-warning" onclick="openAddProductModal()">
            <i class="bi bi-plus-lg"></i> Neues Produkt
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bild</th>
                    <th>Name</th>
                    <th>Preis</th>
                    <th>Kategorie</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="products-table-body">
                <!-- Wird dynamisch gefüllt -->
            </tbody>
        </table>
    </div>
</div>

<!-- Produkt Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Produkt bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <input type="hidden" id="product-id">
                    <div class="mb-3">
                        <label for="product-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="product-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-price" class="form-label">Preis</label>
                        <input type="number" step="0.01" class="form-control" id="product-price" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-category" class="form-label">Kategorie</label>
                        <select class="form-select" id="product-category" required>
                            <!-- Wird dynamisch gefüllt -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product-description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="product-description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product-weight" class="form-label">Gewicht (in g)</label>
                        <input type="number" class="form-control" id="product-weight" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-stock" class="form-label">Lagerbestand</label>
                        <input type="number" class="form-control" id="product-stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-image-url" class="form-label">Bild-Dateiname</label>
                        <input type="text" class="form-control" id="product-image-url" 
                               placeholder="z.B. Honig.jpeg" required>
                        <small class="form-text text-muted">Geben Sie den Dateinamen des Bildes ein (z.B. Honig.jpeg)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-warning" onclick="saveProduct()">Speichern</button>
            </div>
        </div>
    </div>
</div> 