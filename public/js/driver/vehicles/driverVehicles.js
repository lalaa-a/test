
(function(){

    if (window.DriverVehiclesManager) {
        console.log('DriverVehiclesManager already exists, cleaning up...');
        if (window.driverVehiclesManager) {
            delete window.driverVehiclesManager;
        }
        delete window.DriverVehiclesManager;
    }

    class DriverVehiclesManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.vehicles = {
                approved: [],
                pending: []
            };
            this.stats = {
                total: 0,
                approved: 0,
                pending: 0
            };
            this.currentVehicleId = null;
            this.photoFiles = {};
            this.pendingDeleteVehicleId = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadVehicles();
        }

        bindEvents() {
            // Add vehicle button
            const addVehicleBtn = document.getElementById('addVehicleBtn');
            if (addVehicleBtn) {
                addVehicleBtn.addEventListener('click', () => this.openAddVehicleModal());
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

            // Photo viewer modal events
            const photoViewerModal = document.getElementById('photoViewerModal');
            const photoViewerClose = document.querySelector('.photo-viewer-close');
            
            if (photoViewerModal) {
                photoViewerModal.addEventListener('click', (e) => {
                    if (e.target === photoViewerModal) {
                        this.closePhotoViewer();
                    }
                });
            }
            
            if (photoViewerClose) {
                photoViewerClose.addEventListener('click', () => this.closePhotoViewer());
            }

            // ESC key to close photo viewer
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closePhotoViewer();
                }
            });

            // Delete confirmation modal events
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            
            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', () => this.closeDeleteConfirmModal());
            }
            
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
            }

            // Submit button click
            const submitVehicleBtn = document.getElementById('submitVehicleBtn');
            if (submitVehicleBtn) {
                submitVehicleBtn.addEventListener('click', (e) => this.addVehicle(e));
            }

            // Photo upload buttons
            document.querySelectorAll('.btn-upload-photo').forEach(btn => {
                btn.addEventListener('click', (e) => this.handlePhotoUpload(e));
            });

            // Vehicle card clicks
            document.addEventListener('click', (e) => {
                // Don't open details if clicking on interactive elements
                if (e.target.closest('.vehicle-card') &&
                    !e.target.closest('.btn-toggle-active') &&
                    !e.target.closest('.btn-delete-vehicle') &&
                    !e.target.closest('.vehicle-card-image')) {
                    const card = e.target.closest('.vehicle-card');
                    const vehicleId = card.dataset.vehicleId;
                    const status = card.dataset.status;
                    this.openVehicleDetails(vehicleId, status);
                }
            });

            // Delete vehicle buttons
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-delete-vehicle')) {
                    e.stopPropagation();
                    const card = e.target.closest('.vehicle-card');
                    const vehicleId = card.dataset.vehicleId;
                    this.confirmDeleteVehicle(vehicleId);
                }
            });

            // Toggle active/inactive buttons
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-toggle-active')) {
                    e.stopPropagation();
                    const button = e.target.closest('.btn-toggle-active');
                    const vehicleId = button.dataset.vehicleId;
                    this.toggleVehicleActiveStatus(vehicleId);
                }
            });

            // Delete button in details modal
            const deleteVehicleBtn = document.getElementById('deleteVehicleBtn');
            if (deleteVehicleBtn) {
                deleteVehicleBtn.addEventListener('click', () => {
                    if (this.currentVehicleId) {
                        this.confirmDeleteVehicle(this.currentVehicleId);
                    }
                });
            }
        }

        openAddVehicleModal() {
            const modal = document.getElementById('addVehicleModal');
            if (modal) {
                modal.classList.add('show');
                this.resetAddVehicleForm();
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
                this.currentVehicleId = null;
            }
        }

        resetAddVehicleForm() {
            const form = document.getElementById('addVehicleForm');
            if (form) {
                form.reset();
                // Reset photo previews
                document.querySelectorAll('.upload-preview').forEach(preview => {
                    const placeholder = preview.querySelector('.upload-placeholder');
                    const img = preview.querySelector('img');
                    if (img) img.remove();
                    if (placeholder) placeholder.style.display = 'block';
                });
                this.photoFiles = {};
            }
        }

        handlePhotoUpload(e) {
            e.preventDefault();
            const targetId = e.target.getAttribute('data-target');
            if (!targetId) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            // Map input ID to photo type
            const photoTypeMap = {
                'frontPhoto': 'front',
                'backPhoto': 'back',
                'sidePhoto': 'side',
                'inside1Photo': 'interior1',
                'inside2Photo': 'interior2',
                'inside3Photo': 'interior3'
            };

            const photoType = photoTypeMap[targetId];
            if (!photoType) return;

            // Remove any existing change listener to avoid duplicates
            const existingListener = input._photoChangeListener;
            if (existingListener) {
                input.removeEventListener('change', existingListener);
            }

            // Create new change listener
            const changeListener = (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.photoFiles[photoType] = file;
                    this.updatePhotoPreview(targetId, file);
                }
            };

            // Store reference to listener
            input._photoChangeListener = changeListener;
            input.addEventListener('change', changeListener);

            input.click();
        }

        updatePhotoPreview(targetId, file) {
            // Map targetId to slot value
            const slotMap = {
                'frontPhoto': 'front',
                'backPhoto': 'back',
                'sidePhoto': 'side',
                'inside1Photo': 'inside1',
                'inside2Photo': 'inside2',
                'inside3Photo': 'inside3'
            };

            const slot = slotMap[targetId];
            if (!slot) return;

            const preview = document.querySelector(`[data-slot="${slot}"] .upload-preview`);
            if (!preview) return;

            const placeholder = preview.querySelector('.upload-placeholder');
            const existingImg = preview.querySelector('img');

            if (existingImg) {
                existingImg.remove();
            }

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => {
                if (placeholder) placeholder.style.display = 'none';
                preview.appendChild(img);
            };
        }

        async addVehicle(e) {
            e.preventDefault();

            // Get form data
            const form = document.getElementById('addVehicleForm');
            const formData = new FormData(form);
            const vehicleData = {
                make: formData.get('vehicleMake'),
                model: formData.get('vehicleModel'),
                year: formData.get('vehicleYear'),
                license_plate: formData.get('licensePlate'),
                color: formData.get('vehicleColor'),
                seating_capacity: formData.get('seatingCapacity'),
                child_seats: formData.get('childSeats') || 0,
                fuel_efficiency: formData.get('fuelEfficiency'),
                fuel_type: formData.get('fuelType'),
                transmission: formData.get('transmissionType'),
                description: formData.get('vehicleDescription'),
                photos: this.photoFiles
            };

            // Validate required fields
            if (!this.validateVehicleData(vehicleData)) {
                window.showNotification('Please fill in all required fields and upload all 6 vehicle photos', 'error');
                return;
            }

            // Get button reference and store original text
            const submitBtn = document.getElementById('submitVehicleBtn');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            try {
                // Show loading state
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Vehicle...';
                    submitBtn.disabled = true;
                }

                // Create FormData for file uploads
                const apiFormData = new FormData();

                // Add basic vehicle data matching backend expectations
                apiFormData.append('vehicleMake', vehicleData.make);
                apiFormData.append('vehicleModel', vehicleData.model);
                apiFormData.append('vehicleYear', vehicleData.year);
                apiFormData.append('licensePlate', vehicleData.license_plate);
                apiFormData.append('vehicleColor', vehicleData.color);
                apiFormData.append('seatingCapacity', vehicleData.seating_capacity);
                apiFormData.append('childSeats', vehicleData.child_seats);
                apiFormData.append('fuelEfficiency', vehicleData.fuel_efficiency || '');
                apiFormData.append('description', vehicleData.description || '');

                // Add photos with field names expected by backend
                if (vehicleData.photos) {
                    if (vehicleData.photos.front) apiFormData.append('front', vehicleData.photos.front);
                    if (vehicleData.photos.back) apiFormData.append('back', vehicleData.photos.back);
                    if (vehicleData.photos.side) apiFormData.append('side', vehicleData.photos.side);
                    if (vehicleData.photos.interior1) apiFormData.append('interior1', vehicleData.photos.interior1);
                    if (vehicleData.photos.interior2) apiFormData.append('interior2', vehicleData.photos.interior2);
                    if (vehicleData.photos.interior3) apiFormData.append('interior3', vehicleData.photos.interior3);
                }

                console.log('Sending FormData to backend...');

                // Make API call
                const response = await fetch(`${this.URL_ROOT}/Driver/addVehicle`, {
                    method: 'POST',
                    body: apiFormData
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Vehicle added successfully! It will be reviewed by our team.', 'success');
                    this.closeModal(document.getElementById('addVehicleModal'));
                    this.loadVehicles();
                    this.updateStats();
                } else {
                    throw new Error(data.message || 'Failed to add vehicle');
                }

            } catch (error) {
                console.error('Error adding vehicle:', error);
                window.showNotification(error.message || 'Failed to add vehicle. Please try again.', 'error');
            } finally {
                // Reset button state
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        }

        validateVehicleData(data) {
            const required = ['make', 'model', 'year', 'license_plate', 'color', 'seating_capacity'];
            const requiredPhotos = ['front', 'back', 'side', 'interior1', 'interior2', 'interior3'];

            // Check required text fields
            const textFieldsValid = required.every(field => data[field] && data[field].toString().trim() !== '');

            // Check required photos
            const photosValid = requiredPhotos.every(photoType => data.photos && data.photos[photoType]);

            return textFieldsValid && photosValid;
        }

        async loadVehicles() {
            try {
                console.log('Loading vehicles...');
                // Simulate API call - replace with actual implementation
                const response = await this.fetchVehicles();
                console.log('Vehicles response:', response);

                if (response.success) {
                    this.vehicles = {
                        approved: response.vehicles.filter(v => v.status === 'approved'),
                        pending: response.vehicles.filter(v => v.status === 'pending')
                    };

                    console.log('Filtered vehicles:', this.vehicles);
                    this.renderVehicles();
                    this.updateStats();
                }
            } catch (error) {
                console.error('Error loading vehicles:', error);
                window.showNotification('Failed to load vehicles', 'error');
            }
        }

        async fetchVehicles() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getVehicles`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    return {
                        success: true,
                        vehicles: data.vehicles
                    };
                } else {
                    throw new Error(data.message || 'Failed to fetch vehicles');
                }

            } catch (error) {
                console.error('Error fetching vehicles:', error);
                throw error;
            }
        }

        renderVehicles() {
            this.renderVehicleSection('approved');
            this.renderVehicleSection('pending');
        }

        renderVehicleSection(status) {
            const container = document.getElementById(`${status}VehiclesGrid`);
            if (!container) return;

            const vehicles = this.vehicles[status] || [];

            if (vehicles.length === 0) {
                container.innerHTML = `
                    <div class="no-vehicles">
                        <i class="fas fa-car"></i>
                        <p>No ${status} vehicles</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = vehicles.map(vehicle => this.createVehicleCard(vehicle, status)).join('');
        }

        createVehicleCard(vehicle, status) {
            const primaryPhoto = vehicle.photos && vehicle.photos.length > 0 ? vehicle.photos[0] : null;

            return `
                <div class="vehicle-card" data-vehicle-id="${vehicle.id}" data-status="${status}">
                    <div class="vehicle-card-header">
                        ${primaryPhoto ?
                            `<img src="${this.UP_ROOT}${primaryPhoto.url}" alt="${vehicle.make} ${vehicle.model}" class="vehicle-card-image" onclick="event.stopPropagation(); driverVehiclesManager.openPhotoViewer('${this.UP_ROOT}${primaryPhoto.url}')">` :
                            `<div class="vehicle-card-placeholder">
                                <i class="fas fa-car"></i>
                                <p>No Photo</p>
                            </div>`
                        }
                        <div class="vehicle-card-status ${status}">
                            ${status.toUpperCase()}
                        </div>
                        <div class="vehicle-card-actions">
                            <button class="btn-delete-vehicle" title="Delete Vehicle" data-vehicle-id="${vehicle.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        ${status === 'approved' ? `
                        <div class="vehicle-card-toggle">
                            <button class="btn-toggle-active ${vehicle.is_active ? 'active' : 'inactive'}" title="${vehicle.is_active ? 'Mark as Inactive' : 'Mark as Active'}" data-vehicle-id="${vehicle.id}">
                                <i class="fas ${vehicle.is_active ? 'fa-check-circle' : 'fa-pause-circle'}"></i>
                            </button>
                        </div>
                        ` : ''}
                    </div>
                    <div class="vehicle-card-content">
                        <div class="vehicle-card-title">${vehicle.make} ${vehicle.model} ${vehicle.year}</div>
                        <div class="vehicle-card-details">
                            <div class="vehicle-card-detail">
                                <label>License:</label>
                                <span>${vehicle.license_plate}</span>
                            </div>
                            <div class="vehicle-card-detail">
                                <label>Color:</label>
                                <span>${vehicle.color}</span>
                            </div>
                            <div class="vehicle-card-detail">
                                <label>Capacity:</label>
                                <span>${vehicle.seating_capacity} seats</span>
                            </div>
                            <div class="vehicle-card-detail">
                                <label>Child Seats:</label>
                                <span>${vehicle.child_seats || 0}</span>
                            </div>
                            ${status === 'approved' ? `
                                <div class="vehicle-card-detail">
                                    <label>Status:</label>
                                    <div class="vehicle-status-indicators">
                                        <span class="status-indicator ${vehicle.is_active ? 'active' : 'inactive'}">
                                            <i class="fas ${vehicle.is_active ? 'fa-check-circle' : 'fa-pause-circle'}"></i>
                                            ${vehicle.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                        <span class="status-indicator ${vehicle.in_use ? 'in-use' : 'available'}">
                                            <i class="fas ${vehicle.in_use ? 'fa-car' : 'fa-car-side'}"></i>
                                            ${vehicle.in_use ? 'In Use' : 'Available'}
                                        </span>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        openVehicleDetails(vehicleId, status) {
            const vehicles = this.vehicles[status] || [];
            const vehicle = vehicles.find(v => v.id == vehicleId);

            if (!vehicle) {
                window.showNotification('Vehicle not found', 'error');
                return;
            }

            this.currentVehicleId = vehicleId;
            this.renderVehicleDetailsModal(vehicle);
            const modal = document.getElementById('vehicleDetailsModal');
            modal.classList.add('show');
        }

        renderVehicleDetailsModal(vehicle) {
            const modal = document.getElementById('vehicleDetailsModal');
            if (!modal) return;

            // Update modal title
            const titleEl = document.getElementById('vehicleDetailsTitle');
            if (titleEl) {
                titleEl.innerHTML = `<i class="fas fa-car"></i> ${vehicle.make} ${vehicle.model}`;
            }

            // Update status badge
            const statusBadge = document.getElementById('vehicleStatusBadge');
            if (statusBadge) {
                statusBadge.className = `status-badge ${vehicle.status}`;
                statusBadge.innerHTML = `<i class="fas fa-${vehicle.status === 'approved' ? 'check-circle' : 'clock'}"></i> ${vehicle.status === 'approved' ? 'Approved' : 'Pending Verification'}`;
            }

            // Update usage status (now showing active/inactive and in-use status for approved vehicles)
            const usageStatus = document.getElementById('vehicleUsageStatus');
            if (usageStatus) {
                if (vehicle.status === 'approved') {
                    usageStatus.style.display = 'block';
                    const usageContainer = usageStatus.querySelector('.usage-badges') || usageStatus;
                    
                    // Create or update the badges container
                    let badgesContainer = usageStatus.querySelector('.usage-badges');
                    if (!badgesContainer) {
                        badgesContainer = document.createElement('div');
                        badgesContainer.className = 'usage-badges';
                        usageStatus.appendChild(badgesContainer);
                    }
                    
                    badgesContainer.innerHTML = `
                        <span class="usage-badge ${vehicle.is_active ? 'active' : 'inactive'}">
                            <i class="fas ${vehicle.is_active ? 'fa-check-circle' : 'fa-pause-circle'}"></i> ${vehicle.is_active ? 'Active' : 'Inactive'}
                        </span>
                        <span class="usage-badge ${vehicle.in_use ? 'in-use' : 'available'}">
                            <i class="fas ${vehicle.in_use ? 'fa-car' : 'fa-car-side'}"></i> ${vehicle.in_use ? 'In Use' : 'Available'}
                        </span>
                    `;
                } else {
                    usageStatus.style.display = 'none';
                }
            }

            // Update vehicle info
            const infoMap = {
                detailVehicleMakeModel: `${vehicle.make} ${vehicle.model}`,
                detailVehicleYear: vehicle.year,
                detailVehicleColor: vehicle.color,
                detailLicensePlate: vehicle.license_plate,
                detailSeatingCapacity: `${vehicle.seating_capacity} passengers`,
                detailChildSeats: `${vehicle.child_seats || 0} child seats available`,
                detailFuelEfficiency: vehicle.fuel_efficiency ? `${vehicle.fuel_efficiency} km/L` : 'Not specified',
                detailSubmittedDate: new Date(vehicle.created_at).toLocaleDateString()
            };

            Object.keys(infoMap).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = infoMap[id];
            });

            // Update description
            const descEl = document.getElementById('detailVehicleDescription');
            if (descEl) {
                descEl.textContent = vehicle.description || 'No description provided';
            }

            // Update photos
            const photoMap = {
                detailFrontPhoto: vehicle.photos?.find(p => p.type === 'front'),
                detailBackPhoto: vehicle.photos?.find(p => p.type === 'back'),
                detailSidePhoto: vehicle.photos?.find(p => p.type === 'side'),
                detailInside1Photo: vehicle.photos?.find(p => p.type === 'interior1'),
                detailInside2Photo: vehicle.photos?.find(p => p.type === 'interior2'),
                detailInside3Photo: vehicle.photos?.find(p => p.type === 'interior3')
            };

            Object.keys(photoMap).forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const photo = photoMap[id];
                    if (photo) {
                        el.innerHTML = `<img src="${this.UP_ROOT}${photo.url}" alt="Vehicle photo">`;
                        // Add click event to view photo
                        el.style.cursor = 'pointer';
                        el.addEventListener('click', () => this.openPhotoViewer(`${this.UP_ROOT}${photo.url}`));
                    } else {
                        el.innerHTML = `
                            <div class="photo-placeholder">
                                <i class="fas fa-image"></i>
                                <p>No photo</p>
                            </div>
                        `;
                    }
                }
            });

            // Show/hide delete button
            const deleteBtn = document.getElementById('deleteVehicleBtn');
            if (deleteBtn) {
                deleteBtn.style.display = 'inline-flex';
            }
        }

        confirmDeleteVehicle(vehicleId) {
            if (!vehicleId) {
                window.showNotification('Invalid vehicle ID', 'error');
                return;
            }

            // Find the vehicle to check its availability
            const vehicle = [...this.vehicles.approved, ...this.vehicles.pending].find(v => v.id == vehicleId);
            
            if (!vehicle) {
                window.showNotification('Vehicle not found', 'error');
                return;
            }

            // Check if vehicle is in use (in_use = true means currently being used)
            if (vehicle.in_use) {
                window.showNotification('Cannot delete vehicle that is currently in use', 'error');
                return;
            }

            // Store the vehicle ID for deletion and show confirmation modal
            this.pendingDeleteVehicleId = vehicleId;
            this.showDeleteConfirmModal();
        }

        async deleteVehicle(vehicleId) {
            try {
                // Simulate API call - replace with actual implementation
                const response = await this.deleteVehicleAPI(vehicleId);

                if (response.success) {
                    window.showNotification('Vehicle deleted successfully', 'success');
                    this.closeModal(document.getElementById('vehicleDetailsModal'));
                    this.loadVehicles();
                    this.updateStats();
                } else {
                    throw new Error(response.message || 'Failed to delete vehicle');
                }
            } catch (error) {
                console.error('Error deleting vehicle:', error);
                window.showNotification(error.message || 'Failed to delete vehicle', 'error');
            }
        }

        async deleteVehicleAPI(vehicleId) {
            if (!vehicleId) {
                throw new Error('Vehicle ID is required');
            }

            try {
                const formData = new FormData();
                formData.append('vehicleId', vehicleId);

                const response = await fetch(`${this.URL_ROOT}/Driver/deleteVehicle`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    return { success: true, message: data.message };
                } else {
                    throw new Error(data.message || 'Failed to delete vehicle');
                }

            } catch (error) {
                console.error('Error deleting vehicle:', error);
                throw error;
            }
        }

        async toggleVehicleActiveStatus(vehicleId) {
            try {
                // Find the vehicle in approved vehicles
                const vehicle = this.vehicles.approved.find(v => v.id == vehicleId);
                if (!vehicle) {
                    window.showNotification('Vehicle not found or not approved for activation', 'error');
                    return;
                }

                // Additional check to ensure vehicle is approved
                if (vehicle.status !== 'approved') {
                    window.showNotification('Only approved vehicles can be activated/deactivated', 'error');
                    return;
                }

                const newActiveStatus = !vehicle.is_active;

                // Simulate API call - replace with actual implementation
                const response = await this.toggleVehicleActiveAPI(vehicleId, newActiveStatus);

                if (response.success) {
                    // Update local vehicle data
                    vehicle.is_active = newActiveStatus;
                    
                    // Re-render the vehicles
                    this.renderVehicles();
                    
                    // Update stats
                    this.updateStats();
                    
                    window.showNotification(`Vehicle marked as ${newActiveStatus ? 'Active' : 'Inactive'}`, 'success');
                } else {
                    throw new Error(response.message || 'Failed to update vehicle status');
                }
            } catch (error) {
                console.error('Error toggling vehicle active status:', error);
                window.showNotification(error.message || 'Failed to update vehicle status', 'error');
            }
        }

        async toggleVehicleActiveAPI(vehicleId, isActive) {
            try {
                const formData = new FormData();
                formData.append('vehicleId', vehicleId);
                formData.append('isActive', isActive);

                const response = await fetch(`${this.URL_ROOT}/Driver/toggleVehicle`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                return result;
            } catch (error) {
                console.error('Error toggling vehicle status:', error);
                return { success: false, message: 'Network error occurred' };
            }
        }

        updateStats() {
            console.log('Current vehicles:', this.vehicles);
            // Count active and inactive vehicles from approved vehicles
            const activeCount = this.vehicles.approved.filter(v => v.is_active).length;
            const inactiveCount = this.vehicles.approved.filter(v => !v.is_active).length;

            console.log('Active vehicles:', this.vehicles.approved.filter(v => v.is_active));
            console.log('Inactive vehicles:', this.vehicles.approved.filter(v => !v.is_active));

            this.stats = {
                total: this.vehicles.approved.length + this.vehicles.pending.length,
                approved: this.vehicles.approved.length,
                pending: this.vehicles.pending.length,
                active: activeCount,
                inactive: inactiveCount
            };

            console.log('Updated stats:', this.stats);
            this.renderStats();
        }

        renderStats() {
            const totalEl = document.getElementById('totalVehicles');
            const approvedEl = document.getElementById('approvedVehicles');
            const pendingEl = document.getElementById('pendingVehicles');
            const activeEl = document.getElementById('activeVehicles');
            const inactiveEl = document.getElementById('inactiveVehicles');

            if (totalEl) totalEl.textContent = this.stats.total;
            if (approvedEl) approvedEl.textContent = this.stats.approved;
            if (pendingEl) pendingEl.textContent = this.stats.pending;
            if (activeEl) activeEl.textContent = this.stats.active;
            if (inactiveEl) inactiveEl.textContent = this.stats.inactive;
        }

        // Photo viewer methods
        openPhotoViewer(imageUrl) {
            const modal = document.getElementById('photoViewerModal');
            const image = document.getElementById('photoViewerImage');
            if (modal && image) {
                // Reset image styles before loading
                image.style.maxWidth = '';
                image.style.maxHeight = '';
                image.style.width = '';
                image.style.height = '';

                image.src = imageUrl;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling

                // Ensure image fits properly when loaded
                image.onload = () => {
                    this.adjustImageSize(image);
                };

                // Handle case where image is already cached
                if (image.complete) {
                    this.adjustImageSize(image);
                }

                // Add resize handler for responsive behavior
                this.handleResize = () => {
                    if (modal.classList.contains('active')) {
                        this.adjustImageSize(image);
                    }
                };
                window.addEventListener('resize', this.handleResize);
            }
        }

        adjustImageSize(image) {
            const modal = document.getElementById('photoViewerModal');
            const viewportWidth = window.innerWidth * 0.9; // 90% of viewport width
            const viewportHeight = window.innerHeight * 0.9; // 90% of viewport height

            const imgAspectRatio = image.naturalWidth / image.naturalHeight;
            const viewportAspectRatio = viewportWidth / viewportHeight;

            if (imgAspectRatio > viewportAspectRatio) {
                // Image is wider relative to viewport
                image.style.width = Math.min(viewportWidth, image.naturalWidth) + 'px';
                image.style.height = 'auto';
                image.style.maxWidth = '90vw';
                image.style.maxHeight = '90vh';
            } else {
                // Image is taller relative to viewport
                image.style.height = Math.min(viewportHeight, image.naturalHeight) + 'px';
                image.style.width = 'auto';
                image.style.maxWidth = '90vw';
                image.style.maxHeight = '90vh';
            }
        }

        closePhotoViewer() {
            const modal = document.getElementById('photoViewerModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling

                // Remove resize listener
                window.removeEventListener('resize', this.handleResize);
            }
        }

        showDeleteConfirmModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeDeleteConfirmModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingDeleteVehicleId = null;
            }
        }

        confirmDelete() {
            if (this.pendingDeleteVehicleId) {
                const vehicleId = this.pendingDeleteVehicleId;
                this.closeDeleteConfirmModal();
                this.pendingDeleteVehicleId = null;
                this.deleteVehicle(vehicleId);
            } else {
                window.showNotification('No vehicle selected for deletion', 'error');
            }
        }
    }

    window.DriverVehiclesManager = DriverVehiclesManager;
    window.driverVehiclesManager = new DriverVehiclesManager();
})();