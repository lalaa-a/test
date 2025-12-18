// Category Management JavaScript - Safe re-initialization
(function() {

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
            this.URL_ROOT = 'http://localhost/test';

            this.allFilters = [];
            this.groupedFilters = [];
            this.currentEditingMainFilter = null;
            this.currentEditingSubFilter = null;


            this.currentEditId = null;
            this.currentDeleteId = null;
            this.currentDeleteType = null; // 'category' or 'subcategory'
            this.currentParentId = null; // for subcategory editing

            this.initializeElements();
            this.attachEventListeners();
            this.loadAllFilters();
            this.closetoggles();
        }

        initializeElements() {
        
            // Main buttons and forms
            this.addMainFilterBtn = document.getElementById('add-main-filter-btn');
            this.addMainFilterPopup = document.getElementById('popup');
            this.mainFilterFormElement = document.getElementById('main-filter-form-element');
            this.cancelMainFilterPopup = document.getElementById('cancel-popup');
            this.submitMainFilterPopup = document.getElementById('submit-main-filter');


        // Containers
        this.categoriesContainer = document.getElementById('categories-container');

        // Modals
        this.editModal = document.getElementById('edit-modal');
        this.deleteModal = document.getElementById('delete-modal');

        this.editForm = document.getElementById('edit-form');
        this.editButton = document.getElementById('edit-save-button');
        this.editModalClose = document.getElementById('edit-modal-close');
        this.deleteModalClose = document.getElementById('delete-modal-close');
        this.confirmDelete = document.getElementById('confirm-delete');
        this.cancelDelete = document.getElementById('cancel-delete');
        this.editCancel = document.getElementById('edit-cancel');
    }

    attachEventListeners() {

        //popup events
        this.addMainFilterBtn.addEventListener('click',() => this.openAddMainFilterPopup());
        this.mainFilterFormElement.addEventListener('submit', (e) => this.handleMainFilterSubmit(e));
        
        this.editForm.addEventListener('submit',(e) => this.handleFilterEdit(e));
        this.cancelMainFilterPopup.addEventListener('click',()=> this.closeAddMainFilterPopup());

        // Modal events
        this.editModalClose.addEventListener('click', () => this.closeEditModal());
        this.deleteModalClose.addEventListener('click', () => this.closeDeleteModal());
        this.editCancel.addEventListener('click', () => this.closeEditModal());
        this.cancelDelete.addEventListener('click', () => this.closeDeleteModal());
        this.confirmDelete.addEventListener('click', () => this.handleDeleteConfirm());


        // Close modals when clicking outside
        this.editModal.addEventListener('click', (e) => {
            if (e.target === this.editModal) this.closeEditModal();
        });
        this.deleteModal.addEventListener('click', (e) => {
            if (e.target === this.deleteModal) this.closeDeleteModal();
        });
    }

    openAddMainFilterPopup(){
        this.addMainFilterPopup.style.display = 'flex'; 
    }

    closeAddMainFilterPopup(){
        this.addMainFilterPopup.style.display = 'none';
        this.mainFilterFormElement.reset();
        this.submitMainFilterPopup.disabled = false;
        this.submitMainFilterPopup.textContent = 'Add Main Filter';
    }

    //handle submition
    async handleMainFilterSubmit(e) {

        e.preventDefault();

        const formData = new FormData(this.mainFilterFormElement);
        const originalText = this.submitMainFilterPopup.textContent;

        const addedFilter = {
            mainFilterName: formData.get('mainFilterName')
        };

        if (!addedFilter.mainFilterName.trim()) {
            alert('Please enter a Filter Name');
            return;
        }

        // Disable submit button during processing
        this.submitMainFilterPopup.disabled = true;
        this.submitMainFilterPopup.textContent =  'Creating...'

        try {

            let url, method;
            url = this.URL_ROOT+'/Moderator/mainFilterNameSubmit';
            method = 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(addedFilter)
            });

            const result = await response.json();

            console.log("result of "+result);

            if (result.success) {
                alert('Main Filter created successfully!');
                this.loadAllFilters();
                this.closeAddMainFilterPopup();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            // Re-enable submit button
            this.submitMainFilterPopup.disabled = false;
            this.submitMainFilterPopup.textContent = originalText;
        }
    }

    async handleFilterEdit(e){

        e.preventDefault();
        const formData = new FormData(this.editForm);
        let editedFilter;
        let url;
        const method = 'PUT';

        if(this.currentEditingMainFilter){
            
            url = this.URL_ROOT+'/Moderator/mainFilterEdit';

            editedFilter = {
                mainFilterName: formData.get('filterName'),
                mainFilterId: this.currentEditingMainFilter.mainFilterId
            };

            if (!editedFilter.mainFilterName.trim()) {
                alert('Please enter a MainFilter Name');
                return;
            } 

        } else{

            url = this.URL_ROOT+'/Moderator/subFilterEdit';
            
            editedFilter = {
                subFilterId:this.currentEditingSubFilter.subFilterId,
                subFilterName: formData.get('filterName')
            };
            
            if (!editedFilter.subFilterName.trim()) {
                alert('Please enter a SubFilter Name');
                return;
            }
        }

        this.editButton.disabled = true;

        try{

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(editedFilter)
            });

            const result = await response.json();
            console.log(result);

            if (result.success) {
                
                alert('Filter updated successfully!');
                this.loadAllFilters();
                this.closeAddMainFilterPopup();
            } else {
                console.log('this is the message'+result.message);
                alert('Error: ' + result.message);
            }

        }catch(error){
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
        finally {
            // Re-enable submit button
            this.editButton.disabled = false;
            this.currentEditingMainFilter = null; //<-- whatever the filter kind we keep the current editing ones null because the editing happen from any kinf one at a time.
            this.currentEditingSubFilter = null;
            this.closeEditModal();
        }
    }

    async handleFilterDelete(filterId,event){
        let url = null;
        let deletingFilter = null;
        let altertMsg = null;
        
        if (!confirm('Are you sure you want to delete this trip? This action cannot be undone.')) {
            return;
        }

        console.log(event.target.name , ' target name');

        if(event.target.name == 'mainFilterDelete'){
            url = this.URL_ROOT + '/Moderator/deleteMainFilter';
            deletingFilter = {
                mainFilterId : filterId
            };
            altertMsg = 'MainFilter Deleted Succesfully..';
        }

        if(event.target.name == 'subFilterDelete'){
            url = this.URL_ROOT + '/Moderator/deleteSubFilter';
            deletingFilter = {
                subFilterId : filterId
            };
            altertMsg = 'SubFilter Deleted Successfully..'
            console.log(deletingFilter);
        }
        
        // Make delete request
        fetch( url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(deletingFilter)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(altertMsg);
                this.loadAllFilters();
            } else {
                alert('Error deleting Filters: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the filters.');
        });

    }

    renderFilters() {

        if (this.allFilters.length === 0) {
            this.categoriesContainer.innerHTML = `
                <div class="no-categories">
                    <i class="fas fa-folder-open"></i>
                    <p>No categories added yet. Click "Add Main Category" to get started.</p>
                </div>
            `;
            return;
        }

        this.categoriesContainer.innerHTML = this.allFilters.map(mainFilter => `
            
            <div class="category-card" data-id="${mainFilter.mainFilterId}">
                <div class="category-header-card">
                    <div class="category-title">
                        <i class="fas fa-folder"></i>
                        <div>
                            <h3>${this.escapeHtml(mainFilter.mainFilterName)}</h3>
                        </div>
                    </div>

                    <div class="dot-menu-container">
                        <button class="dot-menu-btn" onclick="categoryManager.toggleMainFilterDots(${mainFilter.mainFilterId},event)">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dot-menu-dropdown" id="mainFilterMenu-${mainFilter.mainFilterId}">
                            <button class="dot-menu-item edit" onclick="categoryManager.editMainFilter(${mainFilter.mainFilterId},event)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="dot-menu-item delete" name='mainFilterDelete' onclick="categoryManager.handleFilterDelete(${mainFilter.mainFilterId},event)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>

                </div>
                <div class="subcategories">
                    ${mainFilter.subFilters.length === 0 ?
                        '<div class="add-subcategory"><p style="color: var(--text-muted); margin: 0; font-style: italic;">No subFilters yet</p></div>'+this.getAddSubcategoryForm(mainFilter.mainFilterId) :
                        mainFilter.subFilters.map(subFilter => `
                            <div class="subcategory-item">

                                <div class="subcategory-name">
                                    <i class="fas fa-tag"></i>
                                    <span>${this.escapeHtml(subFilter.subFilterName)}</span>
                                </div>

                                <div class="dot-menu-container">
                                    <button class="dot-menu-btn" onclick="categoryManager.toggleSubFilterDots(${subFilter.subFilterId},event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dot-menu-dropdown" id="subFilterMenu-${subFilter.subFilterId}">
                                        <button class="dot-menu-item edit" onclick="categoryManager.editSubFilter(${mainFilter.mainFilterId},${subFilter.subFilterId},event)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="dot-menu-item delete" name='subFilterDelete' onclick="categoryManager.handleFilterDelete(${subFilter.subFilterId},event)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                        `).join('') + this.getAddSubcategoryForm(mainFilter.mainFilterId)
                    }
                </div>
            </div>
        `).join('');
    }


    // Toggle trip menu dropdown
    toggleMainFilterDots(mainFilterId,event) {
        event.stopPropagation();
        console.log("toggle main filter menu is working");
        
        //when opening this toggle,remove all other these type of toggles
        document.querySelectorAll('.dot-menu-dropdown.show').forEach(menu => {   
                menu.classList.remove('show');
        });
     
        // Toggle current menu
        const menu = document.getElementById(`mainFilterMenu-${mainFilterId}`);
        menu.classList.toggle('show');
    }


    //function to close toggles when clicking on outside 
    closetoggles(){
        // Close menus when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dot-menu-dropdown.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });
    }

    toggleSubFilterDots(subFilterId,event){
        event.stopPropagation();
        console.log("toggle sub filter menu is working");
        
        //when opening this toggle remove all other these type of toggles
        document.querySelectorAll('.dot-menu-dropdown.show').forEach(menu => { 
                menu.classList.remove('show');  
        });
    
        // Toggle current subFilters 
        const menu = document.getElementById(`subFilterMenu-${subFilterId}`);
        menu.classList.toggle('show');
    }

    //function to get sub category
    getAddSubcategoryForm(categoryId) {
        return `
            <div class="add-subcategory">
                <form class="add-subcategory-form" onsubmit="categoryManager.handleSubFilterSubmit(event, '${categoryId}')">
                    <div class="form-group">
                        <input type="text" placeholder="Add subcategory..." required>
                    </div>
                    <button type="submit" class="btn-create" id="add-sub-category">Add</button>
                </form>
            </div>
        `;
    }

    async handleSubFilterSubmit(e, mainFilterId) {

        e.preventDefault();

        const currentEditingSubFilter = false;

        const input = e.target.querySelector('input');
        const addBtn = e.target.querySelector('button[type="submit"]');
        const name = input.value.trim();
        const originalText = addBtn.textContent;

        if (!name) {
            alert('Please enter a SubFilter Name');
            return;
        }

        const addedFilter = {
            mainFilterId: mainFilterId,
            subFilterName : name
        };

        // Disable submit button during processing
        addBtn.disabled = true;
        addBtn.textContent = 'Adding...';

        try {
            
            let url, method;
            
            if (currentEditingSubFilter) {
                // Update existing trip
                url = this.URL_ROOT+'/Moderator/subFilterNameUpdate';
                method = 'PUT';
                tripData.tripId = currentEditingTrip.tripId;
            } else {
                // Create new trip
                url = this.URL_ROOT+'/Moderator/subFilterNameSubmit';
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(addedFilter)
            });

            const result = await response.json();

            console.log("result of "+result);

            if (result.success) {
                if(currentEditingSubFilter){
                    alert('Sub Filter Updated Successfully...');
                }else{
                    alert('Sub Filter Added Successfully...');
                    this.closeEditModal();
                }
                
                // Reload trips data
                await this.loadAllFilters();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            // Re-enable submit button
            addBtn.disabled = false;
            addBtn.textContent = originalText;
            //currentEditingTrip = null;
        }
    }

    async handleSubFilterEdit(e){

        e.preventDefault();
        const formData = new FormData(this.editForm);

        const editedFilter = {
            subFilterId:this.currentEditingSubFilter.subFilterData.subFilterId,
            subFilterName: formData.get('filterName'),
            mainFilterId: this.currentEditingSubFilter.mainFilterId
        };

        if (!editedFilter.subFilterName.trim()) {
            alert('Please enter a Sub Filter Name');
            return;
        }

        console.log(editedFilter);

        this.editButton.disabled = true;

        /*
        try{
            const url = this.URL_ROOT+'/Moderator/subFilterEdit';
            const method = 'PUT';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(editedFilter)
            });
            const result = await response.json();

            if (result.success) {
                
                alert('Trip updated successfully!');
                this.loadAllFilters();
                this.closeAddMainFilterPopup();
            } else {
                alert('Error: ' + result.message);
            }

        }catch(error){
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
        finally {
            // Re-enable submit button
            this.editButton.disabled = false;
            this.currentEditingMainFilter = null;
            this.closeEditModal();
        }
        */
    }

    //To display the main filter edit popup
    editMainFilter(mainFilterId,event) {

        event.preventDefault();

        const mainFilterData = this.allFilters.find(filter => filter.mainFilterId === mainFilterId);
        
        if (!mainFilterData) {
            alert("Main Filter Not found");
            return;
        }

        this.currentEditingMainFilter = mainFilterData;

        document.getElementById('edit-modal-title').textContent = 'Edit MainFilter';
        document.getElementById('edit-name').value = mainFilterData.mainFilterName;

        this.editModal.style.display = 'flex';
        document.getElementById('edit-name').focus();
    }

    //To display the editsubfilter popup
    editSubFilter(mainFilterId,subFilterId,e) {
        e.preventDefault();
        const mainFilterData = this.allFilters.find(filter => filter.mainFilterId === mainFilterId);
        const subFilterData = mainFilterData.subFilters.find(subfilter=>subfilter.subFilterId===subFilterId)

        if (!subFilterData) {
            alert("sub Filter Not found");
            return;
        }

        this.currentEditingSubFilter = subFilterData;

        document.getElementById('edit-modal-title').textContent = 'Edit SubFilter of ' + mainFilterData.mainFilterName;
        document.getElementById('edit-name').value = subFilterData.subFilterName;

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

    async loadAllFilters() {

        try {

            let loadedAllFilters = [];
            const response = await fetch(this.URL_ROOT + '/Moderator/getAllFilters');
            const data = await response.json();
            
            if (data.success) {
                
                loadedAllFilters = data.allFilters;
                console.log("all filters");

                // Clear the array before re-populating to avoid duplicates
                this.allFilters = [];

                //Grouping elemets 
                loadedAllFilters.forEach(item => {
                    // check if the main filter already exists
                    let existing = this.allFilters.find(g => g.mainFilterId === item.mainFilterId);

                    // if not, create it
                    if (!existing) {
                        existing = {
                            mainFilterId: item.mainFilterId,
                            mainFilterName: item.mainFilterName,
                            subFilters: []
                        };
                        this.allFilters.push(existing);
                    }

                    // push subfilter ONLY if it exists
                    if (item.subFilterId !== null) {
                        existing.subFilters.push({
                            subFilterId: item.subFilterId,
                            subFilterName: item.subFilterName
                        });
                    }
                });

                this.renderFilters();

            console.log(this.allFilters);

            } else {
                console.error('Failed to load AllFilters :', data.message);
                alert('Failed to load allFilters: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading allFilters:', error);
            alert('Error loading allFilters ' + error.message);
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
