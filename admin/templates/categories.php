<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kategorien verwalten</h2>
        <button class="btn btn-warning" onclick="openAddCategoryModal()">
            <i class="bi bi-plus-lg"></i> Neue Kategorie
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategoriename</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="categories-table-body">
                <!-- Wird dynamisch gefüllt -->
            </tbody>
        </table>
    </div>
</div>

<!-- Kategorie Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategorie bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <input type="hidden" id="category-id">
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Kategoriename</label>
                        <input type="text" class="form-control" id="category-name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-warning" onclick="saveCategory()">Speichern</button>
            </div>
        </div>
    </div>
</div> 