    <!-- Page Header with Title and Action Button -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">My Guide Spots</h1>
                <p class="page-subtitle">Manage the travel spots you guide and set your charges</p>
            </div>
            <button class="btn-add-spot" id="addSpotBtn">
                <i class="fas fa-plus"></i> Add Guide Spot
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="totalSpots">0</div>
                <div class="stat-label">Total Spots</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="activeSpots">0</div>
                <div class="stat-label">Active</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon inactive">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="inactiveSpots">0</div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>
    </div>

    <!-- Guide Spots Grid -->
    <div class="guide-spots-grid" id="guideSpotsGrid">
        <div class="no-spots">
            <i class="fas fa-map-marker-alt"></i>
            <p>No guide spots added yet</p>
            <p class="no-spots-subtitle">Click "Add Guide Spot" to start guiding travelers</p>
        </div>
    </div>
</div>

<!-- Add/Edit Spot Modal -->
<div id="spotModal" class="modal">
    <div class="modal-content spot-modal">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-plus"></i> Add Guide Spot</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="spotForm" class="spot-form">
                <!-- Spot Selection -->
                <div class="form-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Travel Spot</h4>
                    <div class="spot-selection">
                        <div id="selectedSpotDisplay" class="selected-spot-display">
                            <div class="no-spot-selected">
                                <i class="fas fa-map-marker-alt"></i>
                                <p>No spot selected</p>
                                <button type="button" class="btn-select-spot" id="selectSpotBtn">
                                    <i class="fas fa-search"></i> Select Spot
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guiding Charges -->
                <div class="form-section">
                    <h4><i class="fas fa-dollar-sign"></i> Guiding Charges</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="chargeType">Charge Type *</label>
                            <select id="chargeType" name="chargeType" required>
                                <option value="per_person">Per Person</option>
                                <option value="whole_trip">Whole Trip</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="baseCharge">Base Charge (LKR) *</label>
                            <div class="input-with-icon">
                                <span class="currency-symbol">Rs</span>
                                <input type="number" id="baseCharge" name="baseCharge" placeholder="5000" min="0" step="100" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="minGroupSize">Minimum Group Size</label>
                            <input type="number" id="minGroupSize" name="minGroupSize" placeholder="1" min="1" max="50" value="1">
                        </div>
                        <div class="form-group">
                            <label for="maxGroupSize">Maximum Group Size</label>
                            <input type="number" id="maxGroupSize" name="maxGroupSize" placeholder="20" min="1" max="100" value="20">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe what you'll cover in this guided tour..." rows="4"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
            <button type="button" class="btn-save" id="saveBtn">
                <i class="fas fa-save"></i> Save Spot
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to remove this guide spot?</p>
                <p class="confirm-subtitle">This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelDeleteBtn">Cancel</button>
            <button type="button" class="btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
