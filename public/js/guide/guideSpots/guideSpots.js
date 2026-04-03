(function(){

    if (window.GuideSpotsManager) {
        console.log('GuideSpotsManager already exists, cleaning up...');
        if (window.guideSpotsManager) {
            delete window.guideSpotsManager;
        }
        delete window.GuideSpotsManager;
    }

    class GuideSpotsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = `${this.URL_ROOT}/public/uploads`;
            this.spots = [];
            this.currentSpotId = null;
            this.selectedSpotData = null;
            this.pendingDeleteSpotId = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadSpots();
        }

        loadSpots() {
            console.log('Loading spots...');
            fetch(`${this.URL_ROOT}/Guide/getGuideSpots`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        this.spots = data.spots.map(spot => ({
                            id: spot.id,
                            spotId: spot.spotId,
                            spotName: spot.spotName,
                            baseCharge: parseFloat(spot.baseCharge),
                            chargeType: spot.chargeType,
                            minGroupSize: parseInt(spot.minGroupSize),
                            maxGroupSize: parseInt(spot.maxGroupSize),
                            description: spot.description || '',
                            photoPath: spot.photoPath || '',
                            isActive: spot.isActive == 1
                        }));
                        console.log('Mapped spots:', this.spots);
                        this.updateStats();
                        this.renderSpots();
                    } else {
                        window.showNotification('Failed to load guide spots', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading spots:', error);
                    window.showNotification('Error loading guide spots', 'error');
                });
        }

        bindEvents() {
            // Add spot button
            const addSpotBtn = document.getElementById('addSpotBtn');
            if (addSpotBtn) {
                addSpotBtn.addEventListener('click', () => this.openAddSpotModal());
            }

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal(btn.closest('.modal')));
            });

            // Modal overlays
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal);
                    }
                });
            });

            // Select spot button
            const selectSpotBtn = document.getElementById('selectSpotBtn');
            if (selectSpotBtn) {
                selectSpotBtn.addEventListener('click', () => this.openSpotSelection());
            }

            // Save button
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', (e) => this.saveSpot(e));
            }

            // Cancel button
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => this.closeModal(document.getElementById('spotModal')));
            }

            // Delete confirmation modal events
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', () => this.closeDeleteConfirmModal());
            }

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
            }

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                }
            });
        }

        loadSpots() {
            console.log('Loading spots...');
            fetch(`${this.URL_ROOT}/Guide/getGuideSpots`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        this.spots = data.spots.map(spot => ({
                            id: spot.id,
                            spotId: spot.spotId,
                            spotName: spot.spotName,
                            baseCharge: parseFloat(spot.baseCharge),
                            chargeType: spot.chargeType,
                            minGroupSize: parseInt(spot.minGroupSize),
                            maxGroupSize: parseInt(spot.maxGroupSize),
                            description: spot.description || '',
                            photoPath: spot.photoPath || '',
                            isActive: spot.isActive == 1
                        }));
                        console.log('Mapped spots:', this.spots);
                        this.updateStats();
                        this.renderSpots();
                    } else {
                        window.showNotification('Failed to load guide spots', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading spots:', error);
                    window.showNotification('Error loading guide spots', 'error');
                });
        }

        updateStats() {
            const total = this.spots.length;
            const active = this.spots.filter(spot => spot.isActive).length;
            const inactive = total - active;

            document.getElementById('totalSpots').textContent = total;
            document.getElementById('activeSpots').textContent = active;
            document.getElementById('inactiveSpots').textContent = inactive;
        }

        renderSpots() {
            console.log('Rendering spots:', this.spots);
            const container = document.getElementById('guideSpotsGrid');

            if (this.spots.length === 0) {
                container.innerHTML = `
                    <div class="no-spots">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>No guide spots added yet</p>
                        <p class="no-spots-subtitle">Click "Add Guide Spot" to start guiding travelers</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.spots.map(spot => this.createSpotCard(spot)).join('');
        }

        createSpotCard(spot) {
            const imgSrc = spot.photoPath ? `${this.URL_ROOT}/public/uploads/${spot.photoPath}` : '';
            const statusClass = spot.isActive ? 'active' : 'inactive';
            const statusText = spot.isActive ? 'Active' : 'Inactive';
            return `
                <div class="guide-spot-card" data-spot-id="${spot.id}">
                    <div class="guide-spot-card-header">
                        ${imgSrc ? `<img src="${imgSrc}" alt="${spot.spotName}" class="guide-spot-card-image">` : `<i class="fas fa-map-marker-alt guide-spot-card-icon"></i>`}
                        <div class="guide-spot-card-status ${statusClass}">
                            ${statusText}
                        </div>
                        <div class="guide-spot-card-actions">
                            <button class="btn-edit-spot" title="Edit Spot" data-spot-id="${spot.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete-spot" title="Delete Spot" data-spot-id="${spot.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="guide-spot-card-toggle">
                            <button class="btn-toggle-active ${spot.isActive ? 'active' : 'inactive'}" title="${spot.isActive ? 'Mark as Inactive' : 'Mark as Active'}" data-spot-id="${spot.id}">
                                <i class="fas ${spot.isActive ? 'fa-check-circle' : 'fa-pause-circle'}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="guide-spot-card-content">
                        <div class="guide-spot-card-title">
                            <i class="fas fa-map-marker-alt"></i>
                            ${spot.spotName}
                        </div>
                        <div class="guide-spot-card-details">
                            <div class="guide-spot-card-detail">
                                <label>Base Charge:</label>
                                <span class="spot-charge">Rs ${spot.baseCharge} ${spot.chargeType === 'per_person' ? '(per person)' : '(whole trip)'}</span>
                            </div>
                            <div class="guide-spot-card-detail">
                                <label>Group Size:</label>
                                <span>${spot.minGroupSize} - ${spot.maxGroupSize} people</span>
                            </div>
                        </div>
                        ${spot.description ? `
                            <div class="spot-description">
                                ${spot.description}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        openAddSpotModal() {
            this.currentSpotId = null;
            this.selectedSpotData = null;
            this.resetForm();
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Add Guide Spot';
            document.getElementById('spotModal').classList.add('show');
        }

        openEditSpotModal(spotId) {
            const spot = this.spots.find(s => s.id === spotId);
            if (!spot) {
                window.showNotification('Guide spot not found', 'error');
                return;
            }

            this.currentSpotId = spotId;
            
            // Fetch full travel spot data like in add functionality
            fetch(`${this.URL_ROOT}/Guide/getTravelSpotCardDataBySpotId/${spot.spotId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.success && data.travelSpotCardData) {
                        this.selectedSpotData = data.travelSpotCardData;
                    } else {
                        // Fallback to basic info from local spot data
                        this.selectedSpotData = {
                            spotId: spot.spotId,
                            spotName: spot.spotName
                        };
                    }
                    
                    this.populateForm(spot);
                    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Guide Spot';
                    document.getElementById('spotModal').classList.add('show');
                })
                .catch(error => {
                    console.error('Error fetching spot data:', error);
                    // Fallback to basic info
                    this.selectedSpotData = {
                        spotId: spot.spotId,
                        spotName: spot.spotName
                    };
                    this.populateForm(spot);
                    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Guide Spot';
                    document.getElementById('spotModal').classList.add('show');
                    window.showNotification('Failed to fetch spot details, using basic info', 'error');
                });
        }

        resetForm() {
            const form = document.getElementById('spotForm');
            form.reset();

            // Reset spot selection display
            this.updateSpotSelectionDisplay();

            // Set default values
            document.getElementById('minGroupSize').value = '1';
            document.getElementById('maxGroupSize').value = '20';
            document.getElementById('chargeType').value = 'per_person';
        }

        populateForm(spot) {
            document.getElementById('baseCharge').value = spot.baseCharge;
            document.getElementById('chargeType').value = spot.chargeType || 'per_person';
            document.getElementById('minGroupSize').value = spot.minGroupSize;
            document.getElementById('maxGroupSize').value = spot.maxGroupSize;
            document.getElementById('description').value = spot.description || '';

            this.updateSpotSelectionDisplay();
        }

        updateSpotSelectionDisplay() {
            const display = document.getElementById('selectedSpotDisplay');
            if (this.selectedSpotData) {
                display.classList.add('has-spot');

                // Determine if detailed card data is available (returned from server)
                const hasDetails = !!(this.selectedSpotData.spotName || this.selectedSpotData.overview || this.selectedSpotData.photoPath);

                if (hasDetails) {
                    // Build image src - assume server returns a relative photoPath like 'travelSpots/21/..'
                    let imgSrc = '';
                    if (this.selectedSpotData.photoPath) {
                        if (/^https?:\/\//i.test(this.selectedSpotData.photoPath)) {
                            imgSrc = this.selectedSpotData.photoPath;
                        } else {
                            imgSrc = `${this.URL_ROOT}/public/uploads/${this.selectedSpotData.photoPath}`;
                        }
                    }

                    display.innerHTML = `
                        <div class="selected-spot-card">
                            <div class="selected-spot-image">
                                ${imgSrc ? `<img src="${imgSrc}" alt="${this.selectedSpotData.spotName || this.selectedSpotData.name}">` : `<div class="selected-spot-image-placeholder"><i class="fas fa-map-marker-alt"></i></div>`}
                            </div>
                            <div class="selected-spot-content">
                                <div>
                                    <h4>${this.selectedSpotData.spotName || this.selectedSpotData.name}</h4>
                                    <p class="selected-spot-overview">${this.selectedSpotData.overview || ''}</p>
                                </div>
                                <div class="selected-spot-meta">
                                    Rating: ${this.selectedSpotData.averageRating || 'N/A'} (${this.selectedSpotData.totalReviews || 0})
                                </div>
                            </div>
                            <div class="selected-spot-actions">
                                <button type="button" class="btn-change-spot" onclick="guideSpotsManager.openSpotSelection()">
                                    <i class="fas fa-exchange-alt"></i> Change
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    // Minimal display when only name/id available
                    display.innerHTML = `
                        <div class="selected-spot-info">
                            <i class="fas fa-map-marker-alt" style="font-size: 2rem; color: var(--primary);"></i>
                            <div class="selected-spot-details">
                                <h4>${this.selectedSpotData.spotName}</h4>
                                <p>Spot ID: ${this.selectedSpotData.spotId}</p>
                            </div>
                            <button type="button" class="btn-change-spot" onclick="guideSpotsManager.openSpotSelection()">
                                <i class="fas fa-exchange-alt"></i> Change
                            </button>
                        </div>
                    `;
                }
            } else {
                display.classList.remove('has-spot');
                display.innerHTML = `
                    <div class="no-spot-selected">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>No spot selected</p>
                        <button type="button" class="btn-select-spot" id="selectSpotBtn">
                            <i class="fas fa-search"></i> Select Spot
                        </button>
                    </div>
                `;

                // Re-bind the select spot button
                const selectSpotBtn = document.getElementById('selectSpotBtn');
                if (selectSpotBtn) {
                    selectSpotBtn.addEventListener('click', () => this.openSpotSelection());
                }
            }
        }

        openSpotSelection() {
            // Open spot selection in new window
            const spotSelectUrl = `${this.URL_ROOT}/Guide/selectGuideSpot`;
            window.open(spotSelectUrl, 'spotSelection', 'width=1000,height=700,scrollbars=yes,resizable=yes');
        }

        handleSpotSelection(spotData) {
            // Called from the spot selection window. Fetch full spot card data and render.
            const spotId = spotData.id || spotData.spotId;

            if (!spotId) {
                this.selectedSpotData = spotData;
                this.updateSpotSelectionDisplay();
                window.showNotification('Spot selected', 'success');
                return;
            }

            const url = `${this.URL_ROOT}/Guide/getTravelSpotCardDataBySpotId/${spotId}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data && data.success && data.travelSpotCardData) {
                        this.selectedSpotData = data.travelSpotCardData;
                    } else {
                        // Fallback to minimal info
                        this.selectedSpotData = { spotId: spotId, spotName: spotData.name || spotData.spotName };
                    }
                    this.updateSpotSelectionDisplay();
                    window.showNotification('Spot selected successfully!', 'success');
                })
                .catch(err => {
                    console.error('Error fetching spot data:', err);
                    this.selectedSpotData = { id: spotId, name: spotData.name || spotData.spotName };
                    this.updateSpotSelectionDisplay();
                    window.showNotification('Failed to fetch spot details, using basic info', 'error');
                });
        }

        saveSpot(e) {
            e.preventDefault();

            if (!this.selectedSpotData) {
                window.showNotification('Please select a travel spot first.', 'error');
                return;
            }

            const formData = {
                spotId: this.selectedSpotData.spotId,
                baseCharge: parseFloat(document.getElementById('baseCharge').value),
                chargeType: document.getElementById('chargeType').value,
                minGroupSize: parseInt(document.getElementById('minGroupSize').value),
                maxGroupSize: parseInt(document.getElementById('maxGroupSize').value),
                description: document.getElementById('description').value.trim(),
                photoPath: this.selectedSpotData.photoPath || ''
            };

            // Validation
            if (!formData.baseCharge || formData.baseCharge <= 0) {
                window.showNotification('Please enter a valid base charge.', 'error');
                return;
            }

            if (formData.minGroupSize > formData.maxGroupSize) {
                window.showNotification('Minimum group size cannot be greater than maximum group size.', 'error');
                return;
            }

            // Show loading state
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Determine if this is an add or edit operation
            const isEdit = !!this.currentSpotId;
            const endpoint = isEdit ? `${this.URL_ROOT}/Guide/updateGuideSpot` : `${this.URL_ROOT}/Guide/addGuideSpot`;

            // For edit, include the spot ID in the formData
            if (isEdit) {
                formData.id = this.currentSpotId;
            }

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const successMessage = isEdit ? 'Guide spot updated successfully!' : 'Guide spot added successfully!';
                    window.showNotification(successMessage, 'success');
                    
                    if (isEdit) {
                        // Update the existing spot in the local array

                        const spotIndex = this.spots.findIndex(s => s.id === this.currentSpotId);
                        if (spotIndex !== -1) {
                            this.spots[spotIndex] = {
                                ...this.spots[spotIndex],
                                ...formData,
                                spotName: this.selectedSpotData.spotName
                            };
                        }
                    } else {
                        // For add, refresh spots from server to get the real database id
                        this.loadSpots();
                    }
                    
                    this.updateStats();
                    this.renderSpots();
                    this.closeModal(document.getElementById('spotModal'));
                } else {
                    const errorMessage = isEdit ? 'Failed to update guide spot' : 'Failed to add guide spot';
                    window.showNotification(data.message || errorMessage, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('An error occurred while saving the spot', 'error');
            })
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        }

        openDeleteConfirmModal(spotId) {
            this.pendingDeleteSpotId = spotId;
            document.getElementById('deleteConfirmModal').classList.add('show');
        }

        closeDeleteConfirmModal() {
            this.pendingDeleteSpotId = null;
            document.getElementById('deleteConfirmModal').classList.remove('show');
        }

        confirmDelete() {
            if (!this.pendingDeleteSpotId) return;

            // Show loading state
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            confirmBtn.disabled = true;

            fetch(`${this.URL_ROOT}/Guide/deleteGuideSpot`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: this.pendingDeleteSpotId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh spots from server to ensure consistency
                    this.loadSpots();
                    this.closeDeleteConfirmModal();
                    window.showNotification('Guide spot deleted successfully!', 'success');
                } else {
                    window.showNotification(data.message || 'Failed to delete guide spot', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting spot:', error);
                window.showNotification('An error occurred while deleting the spot', 'error');
            })
            .finally(() => {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            });
        }

        toggleGuideSpotActiveStatus(spotId) {
            const spot = this.spots.find(s => s.id === spotId);
            if (!spot) {
                window.showNotification('Guide spot not found', 'error');
                return;
            }

            const newActiveStatus = !spot.isActive;

            // Call API using FormData like driver vehicles
            const formData = new FormData();
            formData.append('spotId', spotId);
            formData.append('isActive', newActiveStatus);

            fetch(`${this.URL_ROOT}/Guide/toggleGuideSpot`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    spot.isActive = newActiveStatus;
                    this.updateStats();
                    this.renderSpots();
                    window.showNotification(`Guide spot marked as ${newActiveStatus ? 'Active' : 'Inactive'}`, 'success');
                } else {
                    window.showNotification(data.message || 'Failed to update spot status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('An error occurred while updating spot status', 'error');
            });
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
            }
            this.currentSpotId = null;
            this.selectedSpotData = null;
        }

        closeAllModals() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
            });
            this.currentSpotId = null;
            this.selectedSpotData = null;
            this.pendingDeleteSpotId = null;
        }

        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this
            console.log(`${type.toUpperCase()}: ${message}`);

            // For now, just use alert. In production, implement a proper notification system
            if (type === 'error') {
                alert(`Error: ${message}`);
            } else {
                alert(message);
            }
        }
    }

    // Event delegation for dynamic elements
    document.addEventListener('click', (e) => {
        // Edit spot button
        if (e.target.closest('.btn-edit-spot')) {
            const spotId = parseInt(e.target.closest('.btn-edit-spot').dataset.spotId);
            if (window.guideSpotsManager && spotId) {
                window.guideSpotsManager.openEditSpotModal(spotId);
            }
        }

        // Delete spot button
        if (e.target.closest('.btn-delete-spot')) {
            const spotId = parseInt(e.target.closest('.btn-delete-spot').dataset.spotId);
            if (window.guideSpotsManager && spotId) {
                window.guideSpotsManager.openDeleteConfirmModal(spotId);
            }
        }

        // Toggle active button
        if (e.target.closest('.btn-toggle-active')) {
            const button = e.target.closest('.btn-toggle-active');
            const spotId = parseInt(button.dataset.spotId);
            if (window.guideSpotsManager && spotId) {
                window.guideSpotsManager.toggleGuideSpotActiveStatus(spotId);
            }
        }
    });

    window.GuideSpotsManager = GuideSpotsManager;
    window.guideSpotsManager = new GuideSpotsManager();

})();