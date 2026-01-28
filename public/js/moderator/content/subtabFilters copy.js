// Category Management JavaScript - Safe re-initialization
(function() {
    'use strict';
    
    
    // Check if CategoryManager already exists and clean up
    if (window.CategoryManager) {
        console.log('CategoryManager already exists, cleaning up...');
        // Clean up any existing instance
        if (window.categoryManager) {
            // Clean up event listeners if needed
            delete window.categoryManager;
        }
        delete window.CategoryManager;
    }
    
    
    class CategoryManager {
        constructor() {
            this.categories = [];
            this.currentEditId = null;
            this.currentDeleteId = null;
            this.currentDeleteType = null; // 'category' or 'subcategory'
            this.currentParentId = null; // for subcategory editing

            this.initializeElements();
            this.attachEventListeners();
            this.loadCategories();
        }

        initializeElements() {
            // Main buttons and forms
            this.addMainCategoryBtn = document.getElementById('add-main-category-btn');
            
            this.mainCategoryForm = document.getElementById('main-category-form');
            this.mainCategoryFormElement = document.getElementById('main-category-form-element');
            this.cancelMainCategory = document.getElementById('cancel-main-category');

        // Containers
        this.categoriesContainer = document.getElementById('categories-container');

        // Modals
        this.editModal = document.getElementById('edit-modal');
        this.deleteModal = document.getElementById('delete-modal');
        this.editForm = document.getElementById('edit-form');
        this.editModalClose = document.getElementById('edit-modal-close');
        this.deleteModalClose = document.getElementById('delete-modal-close');
        this.confirmDelete = document.getElementById('confirm-delete');
        this.cancelDelete = document.getElementById('cancel-delete');
        this.editCancel = document.getElementById('edit-cancel');
    }

    attachEventListeners() {
        // Main category form
        this.addMainCategoryBtn.addEventListener('click', () => this.showMainCategoryForm());
        this.cancelMainCategory.addEventListener('click', () => this.hideMainCategoryForm());
        this.mainCategoryFormElement.addEventListener('submit', (e) => this.handleMainCategorySubmit(e));

        // Modal events
        this.editModalClose.addEventListener('click', () => this.closeEditModal());
        this.deleteModalClose.addEventListener('click', () => this.closeDeleteModal());
        this.editCancel.addEventListener('click', () => this.closeEditModal());
        this.cancelDelete.addEventListener('click', () => this.closeDeleteModal());
        this.confirmDelete.addEventListener('click', () => this.handleDeleteConfirm());
        this.editForm.addEventListener('submit', (e) => this.handleEditSubmit(e));

        // Close modals when clicking outside
        this.editModal.addEventListener('click', (e) => {
            if (e.target === this.editModal) this.closeEditModal();
        });
        this.deleteModal.addEventListener('click', (e) => {
            if (e.target === this.deleteModal) this.closeDeleteModal();
        });
    }

    showMainCategoryForm() {
        this.mainCategoryForm.style.display = 'block';
        this.addMainCategoryBtn.style.display = 'none';
        document.getElementById('main-category-name').focus();
    }

    hideMainCategoryForm() {
        this.mainCategoryForm.style.display = 'none';
        this.addMainCategoryBtn.style.display = 'inline-flex';
        this.mainCategoryFormElement.reset();
    }

    handleMainCategorySubmit(e) {
        e.preventDefault();

        const name = document.getElementById('main-category-name').value.trim();
        const description = document.getElementById('main-category-description').value.trim();

        if (!name) return;

        const category = {
            id: Date.now().toString(),
            name: name,
            description: description,
            subcategories: []
        };

        this.categories.push(category);
        this.saveCategories();
        this.renderCategories();
        this.hideMainCategoryForm();

        this.showNotification('Category added successfully!', 'success');
    }

    renderCategories() {
        if (this.categories.length === 0) {
            this.categoriesContainer.innerHTML = `
                <div class="no-categories">
                    <i class="fas fa-folder-open"></i>
                    <p>No categories added yet. Click "Add Main Category" to get started.</p>
                </div>
            `;
            return;
        }

        this.categoriesContainer.innerHTML = this.categories.map(category => `
            <div class="category-card" data-id="${category.id}">
                <div class="category-header-card">
                    <div class="category-title">
                        <i class="fas fa-folder"></i>
                        <div>
                            <h3>${this.escapeHtml(category.name)}</h3>
                            ${category.description ? `<p class="category-description">${this.escapeHtml(category.description)}</p>` : ''}
                        </div>
                    </div>
                    <div class="category-actions">
                        <button class="btn btn-secondary btn-small" onclick="categoryManager.addSubcategory('${category.id}')">
                            <i class="fas fa-plus"></i> Add Sub
                        </button>
                        <button class="btn btn-secondary btn-small" onclick="categoryManager.editCategory('${category.id}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-small" onclick="categoryManager.deleteCategory('${category.id}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="subcategories">
                    ${category.subcategories.length === 0 ?
                        '<div class="add-subcategory"><p style="color: var(--text-muted); margin: 0; font-style: italic;">No subcategories yet</p></div>' :
                        category.subcategories.map(sub => `
                            <div class="subcategory-item">
                                <div class="subcategory-name">
                                    <i class="fas fa-tag"></i>
                                    <span>${this.escapeHtml(sub.name)}</span>
                                </div>
                                <div class="subcategory-actions">
                                    <button class="btn btn-secondary btn-small" onclick="categoryManager.editSubcategory('${category.id}', '${sub.id}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-small" onclick="categoryManager.deleteSubcategory('${category.id}', '${sub.id}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('') + this.getAddSubcategoryForm(category.id)
                    }
                </div>
            </div>
        `).join('');
    }

    getAddSubcategoryForm(categoryId) {
        return `
            <div class="add-subcategory">
                <form class="add-subcategory-form" onsubmit="categoryManager.handleSubcategorySubmit(event, '${categoryId}')">
                    <div class="form-group">
                        <input type="text" placeholder="Add subcategory..." required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-small">Add</button>
                </form>
            </div>
        `;
    }

    addSubcategory(categoryId) {
        const categoryCard = document.querySelector(`[data-id="${categoryId}"]`);
        const addSubcategory = categoryCard.querySelector('.add-subcategory');
        const input = addSubcategory.querySelector('input');
        input.focus();
    }

    handleSubcategorySubmit(e, categoryId) {
        e.preventDefault();

        const input = e.target.querySelector('input');
        const name = input.value.trim();

        if (!name) return;

        const category = this.categories.find(cat => cat.id === categoryId);
        if (!category) return;

        const subcategory = {
            id: Date.now().toString(),
            name: name
        };

        category.subcategories.push(subcategory);
        this.saveCategories();
        this.renderCategories();

        input.value = '';
        this.showNotification('Subcategory added successfully!', 'success');
    }

    editCategory(categoryId) {
        const category = this.categories.find(cat => cat.id === categoryId);
        if (!category) return;

        this.currentEditId = categoryId;
        this.currentParentId = null;

        document.getElementById('edit-modal-title').textContent = 'Edit Category';
        document.getElementById('edit-name').value = category.name;
        document.getElementById('edit-description').value = category.description || '';

        this.editModal.style.display = 'flex';
        document.getElementById('edit-name').focus();
    }

    editSubcategory(categoryId, subcategoryId) {
        const category = this.categories.find(cat => cat.id === categoryId);
        if (!category) return;

        const subcategory = category.subcategories.find(sub => sub.id === subcategoryId);
        if (!subcategory) return;

        this.currentEditId = subcategoryId;
        this.currentParentId = categoryId;

        document.getElementById('edit-modal-title').textContent = 'Edit Subcategory';
        document.getElementById('edit-name').value = subcategory.name;
        document.getElementById('edit-description').value = '';

        // Hide description field for subcategories
        document.querySelector('label[for="edit-description"]').style.display = 'none';
        document.getElementById('edit-description').style.display = 'none';

        this.editModal.style.display = 'flex';
        document.getElementById('edit-name').focus();
    }

    handleEditSubmit(e) {
        e.preventDefault();

        const name = document.getElementById('edit-name').value.trim();
        const description = document.getElementById('edit-description').value.trim();

        if (!name) return;

        if (this.currentParentId) {
            // Editing subcategory
            const category = this.categories.find(cat => cat.id === this.currentParentId);
            if (category) {
                const subcategory = category.subcategories.find(sub => sub.id === this.currentEditId);
                if (subcategory) {
                    subcategory.name = name;
                }
            }
        } else {
            // Editing category
            const category = this.categories.find(cat => cat.id === this.currentEditId);
            if (category) {
                category.name = name;
                category.description = description;
            }
        }

        this.saveCategories();
        this.renderCategories();
        this.closeEditModal();

        this.showNotification('Item updated successfully!', 'success');
    }

    deleteCategory(categoryId) {
        const category = this.categories.find(cat => cat.id === categoryId);
        if (!category) return;

        this.currentDeleteId = categoryId;
        this.currentDeleteType = 'category';
        this.currentParentId = null;

        document.getElementById('delete-message').textContent =
            `Are you sure you want to delete the category "${category.name}" and all its subcategories? This action cannot be undone.`;

        this.deleteModal.style.display = 'flex';
    }

    deleteSubcategory(categoryId, subcategoryId) {
        const category = this.categories.find(cat => cat.id === categoryId);
        if (!category) return;

        const subcategory = category.subcategories.find(sub => sub.id === subcategoryId);
        if (!subcategory) return;

        this.currentDeleteId = subcategoryId;
        this.currentDeleteType = 'subcategory';
        this.currentParentId = categoryId;

        document.getElementById('delete-message').textContent =
            `Are you sure you want to delete the subcategory "${subcategory.name}"? This action cannot be undone.`;

        this.deleteModal.style.display = 'flex';
    }

    handleDeleteConfirm() {
        if (this.currentDeleteType === 'category') {
            this.categories = this.categories.filter(cat => cat.id !== this.currentDeleteId);
        } else if (this.currentDeleteType === 'subcategory') {
            const category = this.categories.find(cat => cat.id === this.currentParentId);
            if (category) {
                category.subcategories = category.subcategories.filter(sub => sub.id !== this.currentDeleteId);
            }
        }

        this.saveCategories();
        this.renderCategories();
        this.closeDeleteModal();

        this.showNotification('Item deleted successfully!', 'success');
    }

    closeEditModal() {
        this.editModal.style.display = 'none';
        this.editForm.reset();
        this.currentEditId = null;
        this.currentParentId = null;

        // Show description field again
        document.querySelector('label[for="edit-description"]').style.display = 'block';
        document.getElementById('edit-description').style.display = 'block';
    }

    closeDeleteModal() {
        this.deleteModal.style.display = 'none';
        this.currentDeleteId = null;
        this.currentDeleteType = null;
        this.currentParentId = null;
    }

    saveCategories() {
        // In a real implementation, this would save to backend
        localStorage.setItem('categories', JSON.stringify(this.categories));
    }

    loadCategories() {
        // In a real implementation, this would load from backend
        const saved = localStorage.getItem('categories');
        if (saved) {
            this.categories = JSON.parse(saved);
            this.renderCategories();
        }
    }

    showNotification(message, type = 'info') {
        // Simple notification - in a real app, you'd use a proper notification system
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#007bff'};
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1001;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        `;
        notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i> ${message}`;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
    document.head.appendChild(style);

    // Make CategoryManager available globally for this tab
    window.CategoryManager = CategoryManager;
    
    // Initialize the category manager when DOM is loaded
    console.log('Initializing new CategoryManager instance...');
    window.categoryManager = new CategoryManager();

})(); // End of IIFE
