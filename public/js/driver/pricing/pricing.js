(function(){

    if (window.PricingManager) {
        console.log('PricingManager already exists, cleaning up...');
        if (window.pricingManager) {
            delete window.pricingManager;
        }
        delete window.PricingManager;
    }

    class PricingManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.vehicles = {
                priced: [],
                unpriced: []
            };
            this.stats = {
                total: 0,
                priced: 0,
                unpriced: 0,
                active: 0
            };
            this.currentVehicleId = null;
            this.currentPricing = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadVehicles();
        }

        bindEvents() {
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

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                }
            });

            // Set pricing form
            const setPricingForm = document.getElementById('setPricingForm');
            if (setPricingForm) {
                setPricingForm.addEventListener('submit', (e) => this.savePricing(e));
            }

            // Edit pricing button
            const editPricingBtn = document.getElementById('editPricingBtn');
            if (editPricingBtn) {
                editPricingBtn.addEventListener('click', () => this.editPricing());
            }

            // Delete pricing button
            const deletePricingBtn = document.getElementById('deletePricingBtn');
            if (deletePricingBtn) {
                deletePricingBtn.addEventListener('click', () => this.showDeleteConfirmation());
            }

            // Delete confirmation modal events
            const cancelDeletePricingBtn = document.getElementById('cancelDeletePricingBtn');
            const confirmDeletePricingBtn = document.getElementById('confirmDeletePricingBtn');

            if (cancelDeletePricingBtn) {
                cancelDeletePricingBtn.addEventListener('click', () => this.closeDeleteConfirmModal());
            }

            if (confirmDeletePricingBtn) {
                confirmDeletePricingBtn.addEventListener('click', () => this.executeDeletePricing());
            }

            // Pricing input listeners for real-time calculation
            this.bindPricingCalculations();
        }

        bindPricingCalculations() {
            const inputs = [
                'vehicleChargePerKm', 'driverChargePerKm',
                'vehicleChargePerDay', 'driverChargePerDay'
            ];

            inputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('input', () => this.updatePricingSummary());
                }
            });
        }

        async loadVehicles() {
            try {
                this.showLoading();

                // Load verified vehicles that can have pricing
                const response = await fetch(`${this.URL_ROOT}/driver/getVerifiedVehicles`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.processVehicles(data.vehicles);
                    this.updateStats();
                    this.renderVehicles();
                } else {
                    this.showError(data.message || 'Failed to load vehicles');
                }
            } catch (error) {
                console.error('Error loading vehicles:', error);
                this.showError('Failed to load vehicles. Please try again.');
            } finally {
                this.hideLoading();
            }
        }

        processVehicles(vehicles) {
            this.vehicles.priced = [];
            this.vehicles.unpriced = [];

            vehicles.forEach(vehicle => {
                if (vehicle.pricing && vehicle.pricing.vehicleChargePerKm !== null) {
                    this.vehicles.priced.push(vehicle);
                } else {
                    this.vehicles.unpriced.push(vehicle);
                }
            });

            this.stats.total = vehicles.length;
            this.stats.priced = this.vehicles.priced.length;
            this.stats.unpriced = this.vehicles.unpriced.length;
            this.stats.active = vehicles.filter(v => v.availability === 1).length;
        }

        updateStats() {
            document.getElementById('totalVehicles').textContent = this.stats.total;
            document.getElementById('pricedVehicles').textContent = this.stats.priced;
            document.getElementById('unpricedVehicles').textContent = this.stats.unpriced;
            document.getElementById('activeVehicles').textContent = this.stats.active;
        }

        renderVehicles() {
            this.renderPricedVehicles();
            this.renderUnpricedVehicles();
        }

        renderPricedVehicles() {
            const container = document.getElementById('pricedVehiclesGrid');

            if (this.vehicles.priced.length === 0) {
                container.innerHTML = `
                    <div class="no-vehicles">
                        <i class="fas fa-dollar-sign"></i>
                        <p>No vehicles with pricing yet</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.vehicles.priced.map(vehicle => this.createVehicleCard(vehicle, 'priced')).join('');
        }

        renderUnpricedVehicles() {
            const container = document.getElementById('unpricedVehiclesGrid');

            if (this.vehicles.unpriced.length === 0) {
                container.innerHTML = `
                    <div class="no-vehicles">
                        <i class="fas fa-clock"></i>
                        <p>No vehicles awaiting pricing</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.vehicles.unpriced.map(vehicle => this.createVehicleCard(vehicle, 'unpriced')).join('');
        }

        createVehicleCard(vehicle, type) {
            const isPriced = type === 'priced';
            const pricing = vehicle.pricing || {};

            return `
                <div class="vehicle-card" data-vehicle-id="${vehicle.vehicleId}">
                    ${isPriced ? `
                        <div class="vehicle-card-top">
                            <button class="btn-delete-card" onclick="pricingManager.confirmDeletePricingFromCard(${vehicle.vehicleId})" title="Delete Pricing">
                                <i class="fas fa-trash"></i>
                            </button>
                            <span class="vehicle-status ${type}">${isPriced ? 'Priced' : 'Awaiting Pricing'}</span>
                        </div>
                    ` : `
                        <div class="vehicle-card-top">
                            <span class="vehicle-status ${type}">${isPriced ? 'Priced' : 'Awaiting Pricing'}</span>
                        </div>
                    `}
                    <div class="vehicle-header">
                        <div>
                            <h3 class="vehicle-title">${vehicle.make} ${vehicle.model} ${vehicle.year}</h3>
                            <p class="vehicle-subtitle">${vehicle.licensePlate}</p>
                        </div>
                    </div>

                    <div class="vehicle-details">
                        <div class="vehicle-detail">
                            <span class="vehicle-detail-label">Seating</span>
                            <span class="vehicle-detail-value">${vehicle.seatingCapacity} seats</span>
                        </div>
                        <div class="vehicle-detail">
                            <span class="vehicle-detail-label">Status</span>
                            <span class="vehicle-detail-value">${vehicle.availability === 1 ? 'Available' : 'Inuse'}</span>
                        </div>
                        ${isPriced ? `
                            <div class="vehicle-detail">
                                <span class="vehicle-detail-label">Per Km</span>
                                <span class="vehicle-detail-value">LKR ${(parseFloat(pricing.vehicleChargePerKm || 0) + parseFloat(pricing.driverChargePerKm || 0)).toFixed(0)}</span>
                            </div>
                            <div class="vehicle-detail">
                                <span class="vehicle-detail-label">Per Day</span>
                                <span class="vehicle-detail-value">LKR ${(parseFloat(pricing.vehicleChargePerDay || 0) + parseFloat(pricing.driverChargePerDay || 0)).toFixed(0)}</span>
                            </div>
                        ` : ''}
                    </div>

                    <div class="vehicle-actions">
                        ${isPriced ? `
                            <button class="btn-view-pricing" onclick="pricingManager.viewPricing(${vehicle.vehicleId})">
                                <i class="fas fa-eye"></i> View Pricing
                            </button>
                            <button class="btn-edit-pricing" onclick="pricingManager.openSetPricingModal(${vehicle.vehicleId})">
                                <i class="fas fa-edit"></i> Edit Pricing
                            </button>
                        ` : `
                            <button class="btn-set-pricing" onclick="pricingManager.openSetPricingModal(${vehicle.vehicleId})">
                                <i class="fa-solid fa-money-check-dollar"></i> Set Pricing
                            </button>
                        `}
                    </div>
                </div>
            `;
        }

        openSetPricingModal(vehicleId) {
            this.currentVehicleId = vehicleId;
            const vehicle = [...this.vehicles.priced, ...this.vehicles.unpriced].find(v => v.vehicleId === vehicleId);

            if (!vehicle) {
                this.showError('Vehicle not found');
                return;
            }

            // Populate vehicle info
            document.getElementById('pricingVehicleName').textContent = `${vehicle.make} ${vehicle.model} ${vehicle.year}`;
            document.getElementById('pricingLicensePlate').textContent = vehicle.licensePlate;
            document.getElementById('pricingSeatingCapacity').textContent = `${vehicle.seatingCapacity} seats`;

            // Load existing pricing if available
            if (vehicle.pricing) {
                this.loadPricingIntoForm(vehicle.pricing);
            } else {
                this.resetPricingForm();
            }

            this.openModal(document.getElementById('setPricingModal'));
        }

        loadPricingIntoForm(pricing) {
            document.getElementById('vehicleChargePerKm').value = parseInt(pricing.vehicleChargePerKm) || '';
            document.getElementById('driverChargePerKm').value = parseInt(pricing.driverChargePerKm) || '';
            document.getElementById('vehicleChargePerDay').value = parseInt(pricing.vehicleChargePerDay) || '';
            document.getElementById('driverChargePerDay').value = parseInt(pricing.driverChargePerDay) || '';
            document.getElementById('minimumKm').value = parseInt(pricing.minimumKm) || 0;
            document.getElementById('minimumDays').value = parseInt(pricing.minimumDays) || 1;

            this.updatePricingSummary();
        }

        resetPricingForm() {
            document.getElementById('setPricingForm').reset();
            document.getElementById('minimumKm').value = 0;
            document.getElementById('minimumDays').value = 1;
            this.updatePricingSummary();
        }

        updatePricingSummary() {
            const vehiclePerKm = parseFloat(document.getElementById('vehicleChargePerKm').value) || 0;
            const driverPerKm = parseFloat(document.getElementById('driverChargePerKm').value) || 0;
            const totalPerKm = vehiclePerKm + driverPerKm;

            const vehiclePerDay = parseFloat(document.getElementById('vehicleChargePerDay').value) || 0;
            const driverPerDay = parseFloat(document.getElementById('driverChargePerDay').value) || 0;
            const totalPerDay = vehiclePerDay + driverPerDay;

            document.getElementById('totalPerKm').textContent = `LKR ${totalPerKm.toFixed(0)}`;
            document.getElementById('totalPerDay').textContent = `LKR ${totalPerDay.toFixed(0)}`;
        }

        async savePricing(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const pricingData = {
                vehicleId: this.currentVehicleId,
                vehicleChargePerKm: parseFloat(formData.get('vehicleChargePerKm')) || 0,
                driverChargePerKm: parseFloat(formData.get('driverChargePerKm')) || 0,
                vehicleChargePerDay: parseFloat(formData.get('vehicleChargePerDay')) || 0,
                driverChargePerDay: parseFloat(formData.get('driverChargePerDay')) || 0,
                minimumKm: parseFloat(formData.get('minimumKm')) || 0,
                minimumDays: parseFloat(formData.get('minimumDays')) || 1
            };

            // Validation
            if (pricingData.vehicleChargePerKm <= 0 || pricingData.driverChargePerKm <= 0 ||
                pricingData.vehicleChargePerDay <= 0 || pricingData.driverChargePerDay <= 0) {
                this.showError('All pricing fields must be greater than 0');
                return;
            }

            try {
                this.showLoading();

                const response = await fetch(`${this.URL_ROOT}/driver/saveVehiclePricing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(pricingData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showSuccess('Pricing saved successfully');
                    this.closeModal(document.getElementById('setPricingModal'));
                    this.loadVehicles(); // Refresh the list
                } else {
                    this.showError(data.message || 'Failed to save pricing');
                }
            } catch (error) {
                console.error('Error saving pricing:', error);
                this.showError('Failed to save pricing. Please try again.');
            } finally {
                this.hideLoading();
            }
        }

        viewPricing(vehicleId) {
            this.currentVehicleId = vehicleId;
            const vehicle = this.vehicles.priced.find(v => v.vehicleId === vehicleId);

            if (!vehicle || !vehicle.pricing) {
                this.showError('Pricing information not found');
                return;
            }

            const pricing = vehicle.pricing;

            // Populate vehicle info
            document.getElementById('viewVehicleName').textContent = `${vehicle.make} ${vehicle.model} ${vehicle.year}`;
            document.getElementById('viewLicensePlate').textContent = vehicle.licensePlate;
            document.getElementById('viewSeatingCapacity').textContent = `${vehicle.seatingCapacity} seats`;

            // Populate pricing details
            document.getElementById('viewVehicleChargePerKm').textContent = `LKR ${parseFloat(pricing.vehicleChargePerKm || 0).toFixed(0)}`;
            document.getElementById('viewDriverChargePerKm').textContent = `LKR ${parseFloat(pricing.driverChargePerKm || 0).toFixed(0)}`;
            document.getElementById('viewTotalPerKm').textContent = `LKR ${(parseFloat(pricing.vehicleChargePerKm || 0) + parseFloat(pricing.driverChargePerKm || 0)).toFixed(0)}`;

            document.getElementById('viewVehicleChargePerDay').textContent = `LKR ${parseFloat(pricing.vehicleChargePerDay || 0).toFixed(0)}`;
            document.getElementById('viewDriverChargePerDay').textContent = `LKR ${parseFloat(pricing.driverChargePerDay || 0).toFixed(0)}`;
            document.getElementById('viewTotalPerDay').textContent = `LKR ${(parseFloat(pricing.vehicleChargePerDay || 0) + parseFloat(pricing.driverChargePerDay || 0)).toFixed(0)}`;

            document.getElementById('viewMinimumKm').textContent = pricing.minimumKm || '0';
            document.getElementById('viewMinimumDays').textContent = pricing.minimumDays || '1';

            this.openModal(document.getElementById('viewPricingModal'));
        }

        editPricing() {
            this.closeModal(document.getElementById('viewPricingModal'));
            this.openSetPricingModal(this.currentVehicleId);
        }

        confirmDeletePricingFromCard(vehicleId) {
            this.currentVehicleId = vehicleId;
            this.showDeleteConfirmation();
        }

        showDeleteConfirmation() {
            if (!this.currentVehicleId) {
                this.showError('No vehicle selected for pricing deletion');
                return;
            }

            // Check if vehicle has pricing
            const vehicle = [...this.vehicles.priced, ...this.vehicles.unpriced].find(v => v.vehicleId === this.currentVehicleId);
            if (!vehicle || !vehicle.pricing) {
                this.showError('This vehicle does not have pricing to delete');
                return;
            }

            this.openModal(document.getElementById('deletePricingConfirmModal'));
        }

        async executeDeletePricing() {
            if (!this.currentVehicleId) {
                this.showError('No vehicle selected for deletion');
                return;
            }

            this.closeDeleteConfirmModal();

            try {
                this.showLoading();

                const response = await fetch(`${this.URL_ROOT}/driver/deleteVehiclePricing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ vehicleId: this.currentVehicleId })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showSuccess('Pricing deleted successfully');
                    this.closeModal(document.getElementById('viewPricingModal'));
                    this.loadVehicles(); // Refresh the list
                } else {
                    this.showError(data.message || 'Failed to delete pricing');
                }
            } catch (error) {
                console.error('Error deleting pricing:', error);
                this.showError('Failed to delete pricing. Please try again.');
            } finally {
                this.hideLoading();
            }
        }

        openModal(modal) {
            if (modal) {
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                document.body.style.overflow = 'hidden';
            }
        }

        closeDeleteConfirmModal() {
            const modal = document.getElementById('deletePricingConfirmModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        closeAllModals() {
            document.querySelectorAll('.modal').forEach(modal => {
                this.closeModal(modal);
            });
        }

        showLoading() {
            // You can implement a loading overlay here
            console.log('Loading...');
        }

        hideLoading() {
            // Hide loading overlay
            console.log('Loading complete');
        }

        showSuccess(message) {
            // You can implement a toast notification here
            window.showNotification(message, 'success');
        }

        showError(message) {
            // You can implement a toast notification here
            window.showNotification(message, 'error');
        }
    }

    // Initialize the manager
    window.PricingManager = PricingManager;
    window.pricingManager = new PricingManager();

})();
