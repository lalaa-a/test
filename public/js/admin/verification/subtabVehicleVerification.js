(function(){
// Vehicle Verification JavaScript
    if (window.VehicleVerificationManager) {
        console.log('VehicleVerificationManager already exists, cleaning up...');
        if (window.vehicleVerificationManager) {
            delete window.vehicleVerificationManager;
        }
        delete window.VehicleVerificationManager;
    }

    // Vehicle Verification Manager
    class VehicleVerificationManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.currentVehicle = null;
            this.pendingVerifyVehicleId = null;
            this.pendingRejectVehicleId = null;
            this.pendingRevokeVehicleVerificationId = null;
            this.pendingRevokeVehicleRejectionId = null;
            this.vehicles = {
                pending: [],
                verified: [],
                rejected: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadVehicles();
        }

        bindEvents() {
            // Pending section search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterVehicles('pending', e.target.value);
                });
            }

            const pendingVehicleTypeFilter = document.getElementById('pendingVehicleTypeFilter');
            if (pendingVehicleTypeFilter) {
                pendingVehicleTypeFilter.addEventListener('change', () => {
                    this.filterVehicles('pending');
                });
            }

            // Verified section search and filter
            const verifiedSearchInput = document.getElementById('verifiedSearchInput');
            if (verifiedSearchInput) {
                verifiedSearchInput.addEventListener('input', (e) => {
                    this.filterVehicles('verified', e.target.value);
                });
            }

            const verifiedVehicleTypeFilter = document.getElementById('verifiedVehicleTypeFilter');
            if (verifiedVehicleTypeFilter) {
                verifiedVehicleTypeFilter.addEventListener('change', () => {
                    this.filterVehicles('verified');
                });
            }

            // Rejected section search and filter
            const rejectedSearchInput = document.getElementById('rejectedSearchInput');
            if (rejectedSearchInput) {
                rejectedSearchInput.addEventListener('input', (e) => {
                    this.filterVehicles('rejected', e.target.value);
                });
            }

            const rejectedVehicleTypeFilter = document.getElementById('rejectedVehicleTypeFilter');
            if (rejectedVehicleTypeFilter) {
                rejectedVehicleTypeFilter.addEventListener('change', () => {
                    this.filterVehicles('rejected');
                });
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

            // Modal footer close button
            const closeBtn = document.querySelector('#vehicleDetailsModal .modal-footer .btn-secondary');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(document.getElementById('vehicleDetailsModal')));
            }

            // Vehicle row clicks (View button)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const vehicleId = button.dataset.vehicleId;
                    if (vehicleId) {
                        this.showVehicleDetails(vehicleId);
                    }
                }
            });

            // Action buttons (Verify, Reject, Revoke)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-verify-small')) {
                    const button = e.target.closest('.btn-verify-small');
                    const vehicleId = button.dataset.vehicleId;
                    if (vehicleId) {
                        this.verifyVehicle(vehicleId);
                    }
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-reject-small')) {
                    const button = e.target.closest('.btn-reject-small');
                    const vehicleId = button.dataset.vehicleId;
                    if (vehicleId) {
                        this.rejectVehicle(vehicleId);
                    }
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-revoke-small')) {
                    const button = e.target.closest('.btn-revoke-small');
                    const revokeType = button.dataset.revokeType;
                    const vehicleId = button.dataset.vehicleId;
                    if (vehicleId && revokeType) {
                        this.revokeVehicle(vehicleId, revokeType);
                    }
                }
            });

            // ESC key handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                    this.closePhotoViewer();
                }
            });

            // Photo viewer modal
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

            // Verification confirmation modal events
            const cancelVerifyVehicleBtn = document.getElementById('cancelVerifyVehicleBtn');
            const confirmVerifyVehicleBtn = document.getElementById('confirmVerifyVehicleBtn');

            if (cancelVerifyVehicleBtn) {
                cancelVerifyVehicleBtn.addEventListener('click', () => this.closeVerifyVehicleConfirmModal());
            }

            if (confirmVerifyVehicleBtn) {
                confirmVerifyVehicleBtn.addEventListener('click', () => this.confirmVerifyVehicle());
            }

            // Rejection confirmation modal events
            const cancelRejectVehicleBtn = document.getElementById('cancelRejectVehicleBtn');
            const confirmRejectVehicleBtn = document.getElementById('confirmRejectVehicleBtn');

            if (cancelRejectVehicleBtn) {
                cancelRejectVehicleBtn.addEventListener('click', () => this.closeRejectVehicleConfirmModal());
            }

            if (confirmRejectVehicleBtn) {
                confirmRejectVehicleBtn.addEventListener('click', () => this.confirmRejectVehicle());
            }

            // Revoke verification modal events
            const cancelRevokeVehicleVerificationBtn = document.getElementById('cancelRevokeVehicleVerificationBtn');
            const confirmRevokeVehicleVerificationBtn = document.getElementById('confirmRevokeVehicleVerificationBtn');

            if (cancelRevokeVehicleVerificationBtn) {
                cancelRevokeVehicleVerificationBtn.addEventListener('click', () => this.closeRevokeVehicleVerificationModal());
            }

            if (confirmRevokeVehicleVerificationBtn) {
                confirmRevokeVehicleVerificationBtn.addEventListener('click', () => this.confirmRevokeVehicleVerification());
            }

            // Revoke rejection modal events
            const cancelRevokeVehicleRejectionBtn = document.getElementById('cancelRevokeVehicleRejectionBtn');
            const confirmRevokeVehicleRejectionBtn = document.getElementById('confirmRevokeVehicleRejectionBtn');

            if (cancelRevokeVehicleRejectionBtn) {
                cancelRevokeVehicleRejectionBtn.addEventListener('click', () => this.closeRevokeVehicleRejectionModal());
            }

            if (confirmRevokeVehicleRejectionBtn) {
                confirmRevokeVehicleRejectionBtn.addEventListener('click', () => this.confirmRevokeVehicleRejection());
            }

            // Vehicle row clicks (View button)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const vehicleId = button.dataset.vehicleId;
                    if (vehicleId) {
                        this.showVehicleDetails(vehicleId);
                    }
                }
            });

            // Revoke buttons
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-revoke')) {
                    const button = e.target.closest('.btn-revoke');
                    const vehicleId = button.dataset.vehicleId;
                    const action = button.dataset.action;
                    if (vehicleId && action) {
                        if (action === 'revoke-verification') {
                            this.showRevokeVehicleVerificationModal(vehicleId);
                        } else if (action === 'revoke-rejection') {
                            this.showRevokeVehicleRejectionModal(vehicleId);
                        }
                    }
                }
            });

            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToTab(targetId);
                });
            });

            // Remove scroll event listener since sections are toggled
        }

        async loadVehicles() {
            try {
                const [pendingResponse, verifiedResponse, rejectedResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getPendingVehicles`),
                    fetch(`${this.URL_ROOT}/moderator/getVerifiedVehicles`),
                    fetch(`${this.URL_ROOT}/moderator/getRejectedVehicles`)
                ]);

                const [pendingData, verifiedData, rejectedData] = await Promise.all([
                    pendingResponse.json(),
                    verifiedResponse.json(),
                    rejectedResponse.json()
                ]);

                this.vehicles.pending = pendingData.success ? pendingData.vehicles : [];
                this.vehicles.verified = verifiedData.success ? verifiedData.vehicles : [];
                this.vehicles.rejected = rejectedData.success ? rejectedData.vehicles : [];

                this.updateStats();
                this.renderVehicles('pending');
                this.renderVehicles('verified');
                this.renderVehicles('rejected');
                this.switchToTab('pending-section');

            } catch (error) {
                console.error('Error loading vehicles:', error);
                window.showNotification('Error loading vehicles', 'error');
            }
        }

        updateStats() {
            const pendingCount = this.vehicles.pending.length;
            const verifiedCount = this.vehicles.verified.length;
            const rejectedCount = this.vehicles.rejected.length;

            document.getElementById('pendingVehiclesCount').textContent = pendingCount;
            document.getElementById('verifiedVehiclesCount').textContent = verifiedCount;
            document.getElementById('rejectedVehiclesCount').textContent = rejectedCount;
        }

        renderVehicles(status) {
            const container = document.getElementById(`${status}VehiclesGrid`);
            if (!container) return;

            const vehicles = this.vehicles[status];

            if (vehicles.length === 0) {
                const sectionName = status === 'pending' ? 'pending' : 
                                   status === 'verified' ? 'verified' : 'rejected';
                const icon = status === 'pending' ? 'inbox' : 
                            status === 'verified' ? 'check-circle' : 'car-crash';
                const message = status === 'pending' ? 'to verify' : 'yet';
                container.innerHTML = `
                    <tr class="no-vehicles">
                        <td colspan="7">
                            <i class="fas fa-${icon}"></i>
                            <p>No ${sectionName} vehicles ${message}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = vehicles.map(vehicle => this.createVehicleRow(vehicle, status)).join('');
        }

        createVehicleRow(vehicle, status) {
            const vehiclePhoto = vehicle.vehicle_photo ? `${this.UP_ROOT}${vehicle.vehicle_photo}` : `${this.URL_ROOT}/public/img/default-vehicle.jpg`;
            // Always show the applied date (created_at) for consistency
            const dateField = vehicle.created_at;
            const dateLabel = 'Applied';

            return `
                <tr class="vehicle-row" data-vehicle-id="${vehicle.id}">
                    <td class="vehicle-cell">
                        <img src="${vehiclePhoto}" alt="Vehicle" class="vehicle-avatar-small" onerror="this.src='${this.URL_ROOT}/public/img/default-vehicle.jpg'">
                    </td>
                    <td class="owner-cell">${vehicle.owner_name}</td>
                    <td class="email-cell">${vehicle.driver_email}</td>
                    <td class="registration-cell">${vehicle.registration_number}</td>
                    <td class="type-cell">
                        <span class="vehicle-type-badge">${vehicle.vehicle_type}</span>
                    </td>
                    <td class="model-cell">${vehicle.model || 'N/A'}</td>
                    <td class="date-cell">${this.formatDate(dateField)}</td>
                    <td class="actions-cell">
                        <button class="btn btn-view" data-vehicle-id="${vehicle.id}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                        ${status === 'pending' ? `
                            <button class="btn-verify-small" data-action="verify" data-vehicle-id="${vehicle.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-reject-small" data-action="reject" data-vehicle-id="${vehicle.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : `
                            <button class="btn-revoke-small" data-action="revoke" data-revoke-type="${status}" data-vehicle-id="${vehicle.id}">
                                <i class="fas fa-undo"></i>
                            </button>
                        `}
                    </td>
                </tr>
            `;
        }

        filterVehicles(status, searchTerm = '') {
            const filterSelect = document.getElementById(`${status}VehicleTypeFilter`);
            const typeFilter = filterSelect ? filterSelect.value : 'all';

            const container = document.getElementById(`${status}VehiclesGrid`);
            if (!container) return;

            const rows = container.querySelectorAll('tr:not(.no-vehicles)');

            rows.forEach(row => {
                const vehicleReg = row.cells[2]?.textContent?.toLowerCase() || '';
                const ownerName = row.cells[1]?.textContent?.toLowerCase() || '';
                const vehicleType = row.cells[3]?.querySelector('.vehicle-type-badge')?.textContent?.toLowerCase() || '';
                const model = row.cells[4]?.textContent?.toLowerCase() || '';

                const matchesSearch = !searchTerm ||
                    vehicleReg.includes(searchTerm.toLowerCase()) ||
                    ownerName.includes(searchTerm.toLowerCase()) ||
                    model.includes(searchTerm.toLowerCase());

                const matchesType = typeFilter === 'all' || vehicleType === typeFilter;

                row.style.display = matchesSearch && matchesType ? '' : 'none';
            });
        }

        async showVehicleDetails(vehicleId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getVehicleDetails/${vehicleId}`);
                const data = await response.json();

                if (data.success) {
                    this.currentVehicle = data.vehicle;
                    this.renderVehicleDetails(data.vehicle);
                    this.showModal(document.getElementById('vehicleDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to load vehicle details');
                }
            } catch (error) {
                console.error('Error loading vehicle details:', error);
                window.showNotification(error.message || 'Error loading vehicle details', 'error');
            }
        }

        renderVehicleDetails(vehicle) {
            const content = document.getElementById('vehicleDetailsContent');
            if (!content) return;

            content.innerHTML = `
                <div class="vehicle-details-grid">
                    <div class="vehicle-profile-section">
                        <img src="${this.UP_ROOT}${vehicle.vehicle_photo || '/img/default-vehicle.jpg'}" alt="Vehicle" class="vehicle-profile-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.vehicle_photo || '/img/default-vehicle.jpg'}', 'Vehicle Photo')">
                        <h3>${vehicle.registration_number}</h3>
                        <p class="vehicle-type">${vehicle.vehicle_type} - ${vehicle.model || 'N/A'}</p>
                    </div>
                    <div class="vehicle-info-section">
                        ${vehicle.rejectionReason ? `
                        <div class="info-group rejection-reason-group">
                            <h4><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h4>
                            <div class="rejection-reason-content">
                                ${vehicle.rejectionReason}
                            </div>
                        </div>
                        ` : ''}
                        <div class="info-group">
                            <h4>Vehicle Information</h4>
                            <div class="info-item">
                                <label>Registration Number:</label>
                                <span>${vehicle.registration_number}</span>
                            </div>
                            <div class="info-item">
                                <label>Vehicle Type:</label>
                                <span>${vehicle.vehicle_type}</span>
                            </div>
                            <div class="info-item">
                                <label>Model:</label>
                                <span>${vehicle.model || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Year:</label>
                                <span>${vehicle.year || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Color:</label>
                                <span>${vehicle.color || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Capacity:</label>
                                <span>${vehicle.seatingCapacity || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Child Seats:</label>
                                <span>${vehicle.childSeats || '0'}</span>
                            </div>
                            <div class="info-item">
                                <label>Fuel Efficiency:</label>
                                <span>${vehicle.fuelEfficiency ? vehicle.fuelEfficiency + ' L/100km' : 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Description:</label>
                                <span>${vehicle.vehicleDescription || 'N/A'}</span>
                            </div>
                            ${vehicle.frontViewPhoto || vehicle.backViewPhoto || vehicle.sideViewPhoto || vehicle.interiorPhoto1 || vehicle.interiorPhoto2 || vehicle.interiorPhoto3 ? `
                            <div class="documents-section">
                                <label>Vehicle Photos:</label>
                                <div class="documents-grid">
                                    ${vehicle.frontViewPhoto ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.frontViewPhoto}" alt="Front View" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.frontViewPhoto}', 'Front View')">
                                            <span class="photo-label">Front View</span>
                                        </div>
                                    ` : ''}
                                    ${vehicle.backViewPhoto ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.backViewPhoto}" alt="Back View" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.backViewPhoto}', 'Back View')">
                                            <span class="photo-label">Back View</span>
                                        </div>
                                    ` : ''}
                                    ${vehicle.sideViewPhoto ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.sideViewPhoto}" alt="Side View" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.sideViewPhoto}', 'Side View')">
                                            <span class="photo-label">Side View</span>
                                        </div>
                                    ` : ''}
                                    ${vehicle.interiorPhoto1 ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.interiorPhoto1}" alt="Interior 1" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.interiorPhoto1}', 'Interior 1')">
                                            <span class="photo-label">Interior 1</span>
                                        </div>
                                    ` : ''}
                                    ${vehicle.interiorPhoto2 ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.interiorPhoto2}" alt="Interior 2" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.interiorPhoto2}', 'Interior 2')">
                                            <span class="photo-label">Interior 2</span>
                                        </div>
                                    ` : ''}
                                    ${vehicle.interiorPhoto3 ? `
                                        <div class="document-item">
                                            <img src="${this.UP_ROOT}${vehicle.interiorPhoto3}" alt="Interior 3" class="document-photo" onclick="vehicleVerificationManager.viewPhoto('${this.UP_ROOT}${vehicle.interiorPhoto3}', 'Interior 3')">
                                            <span class="photo-label">Interior 3</span>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            ` : ''}
                        </div>

                        <div class="info-group">
                            <h4>Owner Information</h4>
                            <div class="info-item">
                                <label>Owner Name:</label>
                                <span>${vehicle.owner_name || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Owner Contact:</label>
                                <span>${vehicle.driverPhone || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Owner Email:</label>
                                <span>${vehicle.driverEmail || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Owner Address:</label>
                                <span>${vehicle.driverAddress || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Applied Date:</label>
                                <span>${this.formatDate(vehicle.created_at)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        viewPhoto(src, label = '') {
            const modal = document.getElementById('photoViewerModal');
            const image = document.getElementById('photoViewerImage');
            const labelElement = document.getElementById('photoViewerLabel');

            if (modal && image) {
                image.src = src;
                if (labelElement && label) {
                    labelElement.textContent = label;
                    labelElement.style.display = 'block';
                } else if (labelElement) {
                    labelElement.style.display = 'none';
                }
                modal.style.display = 'block';

                // Adjust image size for large images
                image.onload = () => {
                    this.adjustImageSize(image);
                };
            }
        }

        adjustImageSize(img) {
            const maxWidth = window.innerWidth * 0.8;
            const maxHeight = window.innerHeight * 0.8;

            let { naturalWidth: width, naturalHeight: height } = img;

            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width *= ratio;
                height *= ratio;
            }

            img.style.width = `${width}px`;
            img.style.height = `${height}px`;
        }

        closePhotoViewer() {
            const modal = document.getElementById('photoViewerModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const d = new Date(dateString);
            if (isNaN(d)) return 'N/A';
            const month = d.getMonth() + 1;
            const day = d.getDate();
            const year = d.getFullYear();
            return `${month}/${day}/${year}`;
        }

        showModal(modal) {
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
                this.currentVehicle = null;
            }
        }

        closeAllModals() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
            });
            this.currentVehicle = null;
        }

        closeVehicleModal() {
            this.closeModal(document.getElementById('vehicleDetailsModal'));
        }

        // Verification modal methods
        async verifyVehicle(vehicleId = null) {
            const id = vehicleId || this.currentVehicle?.id;
            if (!id) return;

            this.pendingVerifyVehicleId = id;
            this.showVerifyVehicleConfirmModal();
        }

        showVerifyVehicleConfirmModal() {
            const modal = document.getElementById('verifyVehicleConfirmModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeVerifyVehicleConfirmModal() {
            const modal = document.getElementById('verifyVehicleConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingVerifyVehicleId = null;
            }
        }

        async confirmVerifyVehicle() {
            if (this.pendingVerifyVehicleId) {
                const vehicleId = this.pendingVerifyVehicleId;
                this.closeVerifyVehicleConfirmModal();
                this.pendingVerifyVehicleId = null;
                await this.performVerifyVehicle(vehicleId);
            } else {
                window.showNotification('No vehicle selected for verification', 'error');
            }
        }

        async performVerifyVehicle(vehicleId) {
            try {
                console.log('Verifying vehicle:', vehicleId);
                const response = await fetch(`${this.URL_ROOT}/moderator/verifyVehicle/${vehicleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Vehicle verified successfully', 'success');
                    this.loadVehicles();
                    this.closeModal(document.getElementById('vehicleDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to verify vehicle');
                }
            } catch (error) {
                console.error('Error verifying vehicle:', error);
                window.showNotification(error.message || 'Error verifying vehicle', 'error');
            }
        }

        async rejectVehicle(vehicleId = null) {
            const id = vehicleId || this.currentVehicle?.id;
            if (!id) return;

            this.pendingRejectVehicleId = id;
            this.showRejectVehicleConfirmModal();
        }

        showRejectVehicleConfirmModal() {
            const modal = document.getElementById('rejectVehicleConfirmModal');
            if (modal) {
                // Clear previous reason
                const reasonTextarea = document.getElementById('vehicleRejectionReason');
                if (reasonTextarea) {
                    reasonTextarea.value = '';
                }
                modal.classList.add('show');
            }
        }

        closeRejectVehicleConfirmModal() {
            const modal = document.getElementById('rejectVehicleConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRejectVehicleId = null;
            }
        }

        async confirmRejectVehicle() {
            const reasonTextarea = document.getElementById('vehicleRejectionReason');
            const reason = reasonTextarea ? reasonTextarea.value.trim() : '';

            if (!reason) {
                window.showNotification('Please provide a rejection reason', 'error');
                reasonTextarea?.focus();
                return;
            }

            if (this.pendingRejectVehicleId) {
                const vehicleId = this.pendingRejectVehicleId;
                this.closeRejectVehicleConfirmModal();
                this.pendingRejectVehicleId = null;
                await this.performRejectVehicle(vehicleId, reason);
            } else {
                window.showNotification('No vehicle selected for rejection', 'error');
            }
        }

        async performRejectVehicle(vehicleId, reason) {
            try {
                console.log('Rejecting vehicle:', vehicleId, 'Reason:', reason);
                const response = await fetch(`${this.URL_ROOT}/moderator/rejectVehicle/${vehicleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Vehicle rejected', 'success');
                    this.loadVehicles();
                    this.closeModal(document.getElementById('vehicleDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to reject vehicle');
                }
            } catch (error) {
                console.error('Error rejecting vehicle:', error);
                window.showNotification(error.message || 'Error rejecting vehicle', 'error');
            }
        }

        async performRevokeVehicleVerification(vehicleId) {
            try {
                console.log('Revoking vehicle verification:', vehicleId);
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeVehicleVerification/${vehicleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Vehicle verification revoked successfully', 'success');
                    this.loadVehicles();
                    this.closeModal(document.getElementById('vehicleDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to revoke vehicle verification');
                }
            } catch (error) {
                console.error('Error revoking vehicle verification:', error);
                window.showNotification(error.message || 'Error revoking vehicle verification', 'error');
            }
        }

        async performRevokeVehicleRejection(vehicleId) {
            try {
                console.log('Revoking vehicle rejection:', vehicleId);
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeVehicleRejection/${vehicleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Vehicle rejection revoked successfully', 'success');
                    this.loadVehicles();
                    this.closeModal(document.getElementById('vehicleDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to revoke vehicle rejection');
                }
            } catch (error) {
                console.error('Error revoking vehicle rejection:', error);
                window.showNotification(error.message || 'Error revoking vehicle rejection', 'error');
            }
        }

        // Revoke vehicle method
        async revokeVehicle(vehicleId, revokeType) {
            if (revokeType === 'verified') {
                this.showRevokeVehicleVerificationModal(vehicleId);
            } else if (revokeType === 'rejected') {
                this.showRevokeVehicleRejectionModal(vehicleId);
            }
        }

        // Revoke modal methods
        showRevokeVehicleVerificationModal(vehicleId) {
            this.pendingRevokeVehicleVerificationId = vehicleId;
            const modal = document.getElementById('revokeVehicleVerificationModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeVehicleVerificationModal() {
            const modal = document.getElementById('revokeVehicleVerificationModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeVehicleVerificationId = null;
            }
        }

        async confirmRevokeVehicleVerification() {
            if (this.pendingRevokeVehicleVerificationId) {
                const vehicleId = this.pendingRevokeVehicleVerificationId;
                this.closeRevokeVehicleVerificationModal();
                this.pendingRevokeVehicleVerificationId = null;
                await this.performRevokeVehicleVerification(vehicleId);
            } else {
                window.showNotification('No vehicle selected for verification revoke', 'error');
            }
        }

        showRevokeVehicleRejectionModal(vehicleId) {
            this.pendingRevokeVehicleRejectionId = vehicleId;
            const modal = document.getElementById('revokeVehicleRejectionModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeVehicleRejectionModal() {
            const modal = document.getElementById('revokeVehicleRejectionModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeVehicleRejectionId = null;
            }
        }

        async confirmRevokeVehicleRejection() {
            if (this.pendingRevokeVehicleRejectionId) {
                const vehicleId = this.pendingRevokeVehicleRejectionId;
                this.closeRevokeVehicleRejectionModal();
                this.pendingRevokeVehicleRejectionId = null;
                await this.performRevokeVehicleRejection(vehicleId);
            } else {
                window.showNotification('No vehicle selected for rejection revoke', 'error');
            }
        }

        updateActiveNavLink(targetId) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        switchToTab(targetId) {
            // Hide all sections
            document.querySelectorAll('.verification-section').forEach(sec => sec.style.display = 'none');
            // Show target section
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            // Update active nav link
            this.updateActiveNavLink(targetId);
        }
    }

    // Initialize the manager
    window.VehicleVerificationManager = VehicleVerificationManager;
    window.vehicleVerificationManager = new VehicleVerificationManager();

})();

// Global functions for onclick handlers
function closeVehicleModal() {
    if (window.vehicleVerificationManager) {
        window.vehicleVerificationManager.closeVehicleModal();
    }
}

function closePhotoViewer() {
    if (window.vehicleVerificationManager) {
        window.vehicleVerificationManager.closePhotoViewer();
    }
}
