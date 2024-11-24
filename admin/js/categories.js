async function initializeCategories() {
    await loadCategoriesTable();
}

async function loadCategoriesTable() {
    try {
        const response = await fetch('/honig-shop/admin/api/get_categories.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            renderCategoriesTable(data.categories);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Kategorien konnten nicht geladen werden'
        });
    }
}

function renderCategoriesTable(categories) {
    const tbody = document.getElementById('categories-table-body');
    tbody.innerHTML = categories.map(category => `
        <tr>
            <td>${category.category_id}</td>
            <td>${category.category_name}</td>
            <td>
                <button class="btn btn-sm btn-warning me-2" 
                        onclick="editCategory(${category.category_id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" 
                        onclick="deleteCategory(${category.category_id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function openAddCategoryModal() {
    document.getElementById('category-form').reset();
    document.getElementById('category-id').value = '';
    document.querySelector('.modal-title').textContent = 'Neue Kategorie';
    
    const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    categoryModal.show();
}

async function editCategory(categoryId) {
    try {
        const response = await fetch(`/honig-shop/admin/api/get_category.php?id=${categoryId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            document.getElementById('category-id').value = data.category.category_id;
            document.getElementById('category-name').value = data.category.category_name;
            
            document.querySelector('.modal-title').textContent = 'Kategorie bearbeiten';
            
            const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
            categoryModal.show();
        }
    } catch (error) {
        console.error('Error loading category:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Kategorie konnte nicht geladen werden'
        });
    }
}

async function saveCategory() {
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('category-id').value);
        formData.append('name', document.getElementById('category-name').value);

        const response = await fetch('/honig-shop/admin/api/save_category.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            const modalElement = document.getElementById('categoryModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            
            await loadCategoriesTable();
            
            Swal.fire({
                icon: 'success',
                title: 'Erfolg',
                text: 'Kategorie wurde erfolgreich gespeichert'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error saving category:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: error.message || 'Kategorie konnte nicht gespeichert werden'
        });
    }
}

async function deleteCategory(categoryId) {
    try {
        const result = await Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        });

        if (result.isConfirmed) {
            const response = await fetch('/honig-shop/admin/api/delete_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ category_id: categoryId })
            });

            const data = await response.json();

            if (data.status === 'success') {
                await loadCategoriesTable();
                Swal.fire(
                    'Gelöscht!',
                    'Die Kategorie wurde gelöscht.',
                    'success'
                );
            } else {
                throw new Error(data.message);
            }
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: error.message || 'Kategorie konnte nicht gelöscht werden'
        });
    }
}

async function loadCategories() {
    try {
        const response = await fetch('/honig-shop/admin/api/get_categories.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            const categorySelect = document.getElementById('product-category');
            if (categorySelect) {
                categorySelect.innerHTML = data.categories.map(category => 
                    `<option value="${category.category_id}">${category.category_name}</option>`
                ).join('');
            }
            return data.categories;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            text: 'Kategorien konnten nicht geladen werden'
        });
    }
} 