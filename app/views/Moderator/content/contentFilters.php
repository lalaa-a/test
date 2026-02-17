<!-- Category Management Interface -->
    <div class="filter-header">
        <button id="add-main-filter-btn" class="add-main-filter-btn">
            <i class="fas fa-plus"></i>
            Add Main Filter
        </button>
    </div>

    <div class="popup-overlay" id="popup">
        <div class="main-filter-popup">
            <h2>Add Main Filter</h2>
            <form id="main-filter-form-element">
                <div class="form-group">
                    <label for="main-filter-name">Filter Name</label>
                    <input type="text" id="main-filter-name" name= "mainFilterName" placeholder="e.g., Adventure & Outdoor Activities" required>
                </div>
                
                <div class="popup-buttons">
                    <button type="button" class="btn-cancel" id="cancel-popup">Cancel</button>
                    <button type="submit" class="btn-create" id="submit-main-filter">Add Filter</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Categories Display -->
    <div class="categories-container" id="categories-container">
        <!-- Categories will be dynamically added here -->
        <div class="no-categories">
            <i class="fas fa-folder-open"></i>
            <p>No categories added yet. Click "Add Main Category" to get started.</p>
        </div>
    </div>

</div>

<!-- Edit Category Modal -->
<div class="modal" id="edit-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="edit-modal-title">Edit Category</h3>
            <button class="modal-close" id="edit-modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="edit-form">
                <div class="form-group">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="filterName" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id='edit-save-button' >Save Changes</button>
                    <button type="button" class="btn btn-secondary" id="edit-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-content small-modal">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" id="delete-modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="delete-message">Are you sure you want to delete this item?</p>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>

