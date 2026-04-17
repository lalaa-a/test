<!-- Driver Pricing Page -->
    <!-- Page Header with Title and Action Button -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">Vehicle Pricing</h1>
                <p class="page-subtitle">Set pricing for your verified vehicles</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-car"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="totalVehicles">0</div>
                <div class="stat-label">Verified Vehicles</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon priced">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pricedVehicles">0</div>
                <div class="stat-label">Vehicles with Pricing</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon unpriced">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="unpricedVehicles">0</div>
                <div class="stat-label">Awaiting Pricing</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="activeVehicles">0</div>
                <div class="stat-label">Active Vehicles</div>
            </div>
        </div>
    </div>

    <!-- Vehicles Sections -->
    <div class="vehicles-sections">
        <!-- Vehicles with Pricing -->
        <div class="vehicles-section">
            <div class="section-header">
                <h2><i class="fas fa-dollar-sign"></i> Vehicles with Pricing</h2>
            </div>
            <div class="vehicles-grid" id="pricedVehiclesGrid">
                <div class="no-vehicles">
                    <i class="fas fa-dollar-sign"></i>
                    <p>No vehicles with pricing yet</p>
                </div>
            </div>
        </div>

        <!-- Vehicles Awaiting Pricing -->
        <div class="vehicles-section">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Awaiting Pricing Setup</h2>
            </div>
            <div class="vehicles-grid" id="unpricedVehiclesGrid">
                <div class="no-vehicles">
                    <i class="fas fa-clock"></i>
                    <p>No vehicles awaiting pricing</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Set Pricing Modal -->
<div id="setPricingModal" class="modal">
    <div class="modal-content pricing-modal">
        <div class="modal-header">
            <h3><i class="fas fa-dollar-sign"></i> Set Vehicle Pricing</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="vehicle-info-section">
                <h4><i class="fas fa-car"></i> Vehicle Information</h4>
                <div class="vehicle-info-display">
                    <div class="vehicle-info-item">
                        <span class="label">Vehicle:</span>
                        <span class="value" id="pricingVehicleName">-</span>
                    </div>
                    <div class="vehicle-info-item">
                        <span class="label">License Plate:</span>
                        <span class="value" id="pricingLicensePlate">-</span>
                    </div>
                    <div class="vehicle-info-item">
                        <span class="label">Seating Capacity:</span>
                        <span class="value" id="pricingSeatingCapacity">-</span>
                    </div>
                </div>
            </div>

            <form id="setPricingForm" class="pricing-form">
                <!-- Per Kilometer Pricing -->
                <div class="form-section">
                    <h4><i class="fas fa-route"></i> Short Trip Pricing (Less than 1 day)</h4>
                    <p class="form-hint">Set charges for trips that are less than 24 hours</p>

                    <div class="pricing-inputs">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicleChargePerKm">Vehicle Charge per Kilometer (LKR) *</label>
                                <input type="number" id="vehicleChargePerKm" name="vehicleChargePerKm" min="0" step="1" placeholder="e.g., 50" required>
                            </div>
                            <div class="form-group">
                                <label for="driverChargePerKm">Driver Charge per Kilometer (LKR) *</label>
                                <input type="number" id="driverChargePerKm" name="driverChargePerKm" min="0" step="1" placeholder="e.g., 25" required>
                            </div>
                        </div>
                        <div class="pricing-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total per km:</span>
                                <span class="summary-value" id="totalPerKm">LKR 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Per Day Pricing -->
                <div class="form-section">
                    <h4><i class="fas fa-calendar-day"></i> Long Trip Pricing (1 day or more)</h4>
                    <p class="form-hint">Set charges for trips that are 24 hours or longer</p>

                    <div class="pricing-inputs">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicleChargePerDay">Vehicle Charge per Day (LKR) *</label>
                                <input type="number" id="vehicleChargePerDay" name="vehicleChargePerDay" min="0" step="1" placeholder="e.g., 5000" required>
                            </div>
                            <div class="form-group">
                                <label for="driverChargePerDay">Driver Charge per Day (LKR) *</label>
                                <input type="number" id="driverChargePerDay" name="driverChargePerDay" min="0" step="1" placeholder="e.g., 2500" required>
                            </div>
                        </div>
                        <div class="pricing-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total per day:</span>
                                <span class="summary-value" id="totalPerDay">LKR 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="form-section">
                    <h4><i class="fas fa-cogs"></i> Additional Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="minimumKm">Minimum Kilometers</label>
                            <input type="number" id="minimumKm" name="minimumKm" min="0" step="0.1" placeholder="e.g., 10" value="0">
                            <small class="form-hint">Minimum distance for short trips (optional)</small>
                        </div>
                        <div class="form-group">
                            <label for="minimumDays">Minimum Days</label>
                            <input type="number" id="minimumDays" name="minimumDays" min="1" step="0.1" placeholder="e.g., 1" value="1">
                            <small class="form-hint">Minimum days for long trips</small>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Form Actions -->
            <div class="form-actions">
                <button class="btn-secondary" type="button" onclick="pricingManager.closeModal(document.getElementById('setPricingModal'))">Cancel</button>
                <button type="submit" class="btn-primary" id="submitPricingBtn" form="setPricingForm">
                    <i class="fas fa-save"></i> Save Pricing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Pricing Modal -->
<div id="viewPricingModal" class="modal">
    <div class="modal-content pricing-modal">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Vehicle Pricing Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="vehicle-info-section">
                <h4><i class="fas fa-car"></i> Vehicle Information</h4>
                <div class="vehicle-info-display">
                    <div class="vehicle-info-item">
                        <span class="label">Vehicle:</span>
                        <span class="value" id="viewVehicleName">-</span>
                    </div>
                    <div class="vehicle-info-item">
                        <span class="label">License Plate:</span>
                        <span class="value" id="viewLicensePlate">-</span>
                    </div>
                    <div class="vehicle-info-item">
                        <span class="label">Seating Capacity:</span>
                        <span class="value" id="viewSeatingCapacity">-</span>
                    </div>
                </div>
            </div>

            <div class="pricing-details-section">
                <!-- Short Trip Pricing -->
                <div class="pricing-display-section">
                    <h4><i class="fas fa-route"></i> Short Trip Pricing (Less than 1 day)</h4>
                    <div class="pricing-display-grid">
                        <div class="pricing-display-item">
                            <span class="pricing-label">Vehicle Charge per km:</span>
                            <span class="pricing-value" id="viewVehicleChargePerKm">-</span>
                        </div>
                        <div class="pricing-display-item">
                            <span class="pricing-label">Driver Charge per km:</span>
                            <span class="pricing-value" id="viewDriverChargePerKm">-</span>
                        </div>
                        <div class="pricing-display-item total">
                            <span class="pricing-label">Total per km:</span>
                            <span class="pricing-value" id="viewTotalPerKm">-</span>
                        </div>
                    </div>
                </div>

                <!-- Long Trip Pricing -->
                <div class="pricing-display-section">
                    <h4><i class="fas fa-calendar-day"></i> Long Trip Pricing (1 day or more)</h4>
                    <div class="pricing-display-grid">
                        <div class="pricing-display-item">
                            <span class="pricing-label">Vehicle Charge per day:</span>
                            <span class="pricing-value" id="viewVehicleChargePerDay">-</span>
                        </div>
                        <div class="pricing-display-item">
                            <span class="pricing-label">Driver Charge per day:</span>
                            <span class="pricing-value" id="viewDriverChargePerDay">-</span>
                        </div>
                        <div class="pricing-display-item total">
                            <span class="pricing-label">Total per day:</span>
                            <span class="pricing-value" id="viewTotalPerDay">-</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="pricing-display-section">
                    <h4><i class="fas fa-cogs"></i> Additional Settings</h4>
                    <div class="pricing-display-grid">
                        <div class="pricing-display-item">
                            <span class="pricing-label">Minimum Kilometers:</span>
                            <span class="pricing-value" id="viewMinimumKm">-</span>
                        </div>
                        <div class="pricing-display-item">
                            <span class="pricing-label">Minimum Days:</span>
                            <span class="pricing-value" id="viewMinimumDays">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button class="btn-delete" id="deletePricingBtn">
                    <i class="fas fa-trash"></i> Delete Pricing
                </button>
                <button class="btn-secondary" onclick="pricingManager.closeModal(document.getElementById('viewPricingModal'))">Close</button>
                <button class="btn-primary" id="editPricingBtn">
                    <i class="fas fa-edit"></i> Edit Pricing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deletePricingConfirmModal" class="modal deletePricingConfirmation-modal">
    <div class="modal-content deletePricingConfirmation-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h3>
        </div>
        <div class="modal-body">
            <div class="deletePricingConfirmation-message">
                <i class="fas fa-trash-alt deletePricingConfirmation-icon"></i>
                <p class="deletePricingConfirmation-text">Are you sure you want to delete this vehicle's pricing?</p>
                <p class="deletePricingConfirmation-warning">This action cannot be undone. The vehicle will no longer be available for booking.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="cancelDeletePricingBtn">Cancel</button>
            <button class="btn-delete" id="confirmDeletePricingBtn">Delete Pricing</button>
        </div>
    </div>
</div>
