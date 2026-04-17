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
                                <input type="number" id="vehicleChargePerKm" name="vehicleChargePerKm" min="0" step="0.01" placeholder="e.g., 50.00" required>
                            </div>
                            <div class="form-group">
                                <label for="driverChargePerKm">Driver Charge per Kilometer (LKR) *</label>
                                <input type="number" id="driverChargePerKm" name="driverChargePerKm" min="0" step="0.01" placeholder="e.g., 25.00" required>
                            </div>
                        </div>
                        <div class="pricing-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total per km:</span>
                                <span class="summary-value" id="totalPerKm">LKR 0.00</span>
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
                                <input type="number" id="vehicleChargePerDay" name="vehicleChargePerDay" min="0" step="0.01" placeholder="e.g., 5000.00" required>
                            </div>
                            <div class="form-group">
                                <label for="driverChargePerDay">Driver Charge per Day (LKR) *</label>
                                <input type="number" id="driverChargePerDay" name="driverChargePerDay" min="0" step="0.01" placeholder="e.g., 2500.00" required>
                            </div>
                        </div>
                        <div class="pricing-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total per day:</span>
                                <span class="summary-value" id="totalPerDay">LKR 0.00</span>
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



 
 
 < ! - -   D e l e t e   C o n f i r m a t i o n   M o d a l   - - > 
 < d i v   i d = " d e l e t e P r i c i n g C o n f i r m M o d a l "   c l a s s = " m o d a l " > 
         < d i v   c l a s s = " m o d a l - c o n t e n t   c o n f i r m - m o d a l " > 
                 < d i v   c l a s s = " m o d a l - h e a d e r " > 
                         < h 3 > < i   c l a s s = " f a s   f a - e x c l a m a t i o n - t r i a n g l e " > < / i >   C o n f i r m   D e l e t i o n < / h 3 > 
                 < / d i v > 
                 < d i v   c l a s s = " m o d a l - b o d y " > 
                         < d i v   c l a s s = " c o n f i r m - m e s s a g e " > 
                                 < i   c l a s s = " f a s   f a - t r a s h - a l t " > < / i > 
                                 < p > A r e   y o u   s u r e   y o u   w a n t   t o   d e l e t e   t h i s   v e h i c l e 
 
 ' 
 
 s   p r i c i n g ? < / p > 
                                 < p   c l a s s = " c o n f i r m - w a r n i n g " > T h i s   a c t i o n   c a n n o t   b e   u n d o n e .   T h e   v e h i c l e   w i l l   n o   l o n g e r   b e   a v a i l a b l e   f o r   b o o k i n g . < / p > 
                         < / d i v > 
                 < / d i v > 
                 < d i v   c l a s s = " m o d a l - f o o t e r " > 
                         < b u t t o n   c l a s s = " b t n - s e c o n d a r y "   i d = " c a n c e l D e l e t e P r i c i n g B t n " > C a n c e l < / b u t t o n > 
                         < b u t t o n   c l a s s = " b t n - d e l e t e "   i d = " c o n f i r m D e l e t e P r i c i n g B t n " > D e l e t e   P r i c i n g < / b u t t o n > 
                 < / d i v > 
         < / d i v > 
 < / d i v > 
 
 