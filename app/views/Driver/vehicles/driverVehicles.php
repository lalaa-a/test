<!-- Driver Vehicles Page -->

    <!-- Page Header with Title and Action Button -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">My Vehicles</h1>
                <p class="page-subtitle">Manage and organize your vehicle fleet</p>
            </div>
            <button class="btn-add-vehicle" id="addVehicleBtn">
                <i class="fas fa-plus"></i> Add Vehicle
            </button>
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
                <div class="stat-label">Total Vehicles</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="approvedVehicles">0</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pendingVehicles">0</div>
                <div class="stat-label">Pending Verification</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="activeVehicles">0</div>
                <div class="stat-label">Active</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon inactive">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="inactiveVehicles">0</div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>
    </div>

    <!-- Vehicles Sections -->
    <div class="vehicles-sections">
        <!-- Approved Vehicles -->
        <div class="vehicles-section">
            <div class="section-header">
                <h2><i class="fas fa-check-circle"></i> Approved Vehicles</h2>
            </div>
            <div class="vehicles-grid" id="approvedVehiclesGrid">
                <div class="no-vehicles">
                    <i class="fas fa-car"></i>
                    <p>No approved vehicles yet</p>
                </div>
            </div>
        </div>

        <!-- Pending Vehicles -->
        <div class="vehicles-section">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Pending Verification</h2>
            </div>
            <div class="vehicles-grid" id="pendingVehiclesGrid">
                <div class="no-vehicles">
                    <i class="fas fa-clock"></i>
                    <p>No vehicles pending verification</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div id="addVehicleModal" class="modal">
    <div class="modal-content vehicle-modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add New Vehicle</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addVehicleForm" class="vehicle-form" enctype="multipart/form-data">
                <!-- Vehicle Details -->
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Vehicle Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicleMake">Make *</label>
                            <input type="text" id="vehicleMake" name="vehicleMake" placeholder="e.g., Toyota" required>
                        </div>
                        <div class="form-group">
                            <label for="vehicleModel">Model *</label>
                            <input type="text" id="vehicleModel" name="vehicleModel" placeholder="e.g., Camry" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicleYear">Year *</label>
                            <input type="number" id="vehicleYear" name="vehicleYear" min="1900" max="2026" placeholder="e.g., 2020" required>
                        </div>
                        <div class="form-group">
                            <label for="vehicleColor">Color *</label>
                            <input type="text" id="vehicleColor" name="vehicleColor" placeholder="e.g., White" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="licensePlate">License Plate *</label>
                            <input type="text" id="licensePlate" name="licensePlate" placeholder="e.g., ABC-123" required>
                        </div>
                        <div class="form-group">
                            <label for="seatingCapacity">Seating Capacity *</label>
                            <input type="number" id="seatingCapacity" name="seatingCapacity" min="1" max="50" placeholder="e.g., 5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="childSeats">Child Seats Available</label>
                            <input type="number" id="childSeats" name="childSeats" min="0" max="10" placeholder="e.g., 2" value="0">
                        </div>
                        <div class="form-group">
                            <label for="fuelEfficiency">Fuel Efficiency (km/L)</label>
                            <input type="number" id="fuelEfficiency" name="fuelEfficiency" min="1" max="50" step="0.1" placeholder="e.g., 15.5">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="vehicleDescription">Description</label>
                        <textarea id="vehicleDescription" name="vehicleDescription" rows="3" placeholder="Describe your vehicle features, condition, amenities..."></textarea>
                    </div>
                </div>

                <!-- Vehicle Photos -->
                <div class="form-section">
                    <h4><i class="fas fa-camera"></i> Vehicle Photos</h4>
                    <p class="form-hint">Upload clear photos of your vehicle. All photos are required for verification.</p>

                    <div class="photo-upload-grid">
                        <!-- Front Photo -->
                        <div class="photo-upload-item">
                            <label>Front View *</label>
                            <div class="photo-upload-slot" data-slot="front">
                                <div class="upload-preview" id="frontPreview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Front Photo</p>
                                    </div>
                                </div>
                                <input type="file" id="frontPhoto" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="frontPhoto">
                                    <i class="fas fa-camera"></i> Upload Front
                                </button>
                            </div>
                        </div>

                        <!-- Back Photo -->
                        <div class="photo-upload-item">
                            <label>Back View *</label>
                            <div class="photo-upload-slot" data-slot="back">
                                <div class="upload-preview" id="backPreview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Back Photo</p>
                                    </div>
                                </div>
                                <input type="file" id="backPhoto" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="backPhoto">
                                    <i class="fas fa-camera"></i> Upload Back
                                </button>
                            </div>
                        </div>

                        <!-- Side Photo -->
                        <div class="photo-upload-item">
                            <label>Side View *</label>
                            <div class="photo-upload-slot" data-slot="side">
                                <div class="upload-preview" id="sidePreview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Side Photo</p>
                                    </div>
                                </div>
                                <input type="file" id="sidePhoto" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="sidePhoto">
                                    <i class="fas fa-camera"></i> Upload Side
                                </button>
                            </div>
                        </div>

                        <!-- Inside Photo 1 -->
                        <div class="photo-upload-item">
                            <label>Interior 1 *</label>
                            <div class="photo-upload-slot" data-slot="inside1">
                                <div class="upload-preview" id="inside1Preview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Interior Photo 1</p>
                                    </div>
                                </div>
                                <input type="file" id="inside1Photo" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="inside1Photo">
                                    <i class="fas fa-camera"></i> Upload Interior 1
                                </button>
                            </div>
                        </div>

                        <!-- Inside Photo 2 -->
                        <div class="photo-upload-item">
                            <label>Interior 2 *</label>
                            <div class="photo-upload-slot" data-slot="inside2">
                                <div class="upload-preview" id="inside2Preview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Interior Photo 2</p>
                                    </div>
                                </div>
                                <input type="file" id="inside2Photo" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="inside2Photo">
                                    <i class="fas fa-camera"></i> Upload Interior 2
                                </button>
                            </div>
                        </div>

                        <!-- Inside Photo 3 -->
                        <div class="photo-upload-item">
                            <label>Interior 3 *</label>
                            <div class="photo-upload-slot" data-slot="inside3">
                                <div class="upload-preview" id="inside3Preview">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Interior Photo 3</p>
                                    </div>
                                </div>
                                <input type="file" id="inside3Photo" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="inside3Photo">
                                    <i class="fas fa-camera"></i> Upload Interior 3
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Form Actions -->
            <div class="form-actions">
                <button class="btn-secondary" type="button" onclick="driverVehiclesManager.closeModal(document.getElementById('addVehicleModal'))">Cancel</button>
                <button type="submit" class="btn-primary" id="submitVehicleBtn" form="addVehicleForm">
                    <i class="fas fa-save"></i> Submit Vehicle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Details Modal -->
<div id="vehicleDetailsModal" class="modal">
    <div class="modal-content vehicle-details-modal">
        <div class="modal-header">
            <h3 id="vehicleDetailsTitle"><i class="fas fa-car"></i> Vehicle Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="vehicle-details-content">
                <!-- Vehicle Status -->
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Vehicle Status</h4>
                    <div class="vehicle-status-section">
                        <div class="status-badge" id="vehicleStatusBadge">
                            <i class="fas fa-clock"></i> Pending Verification
                        </div>
                        <div class="vehicle-usage-status" id="vehicleUsageStatus" style="display: none;">
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="form-section">
                    <h4><i class="fas fa-car"></i> Vehicle Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Make & Model</label>
                            <span id="detailVehicleMakeModel">-</span>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <span id="detailVehicleYear">-</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Color</label>
                            <span id="detailVehicleColor">-</span>
                        </div>
                        <div class="form-group">
                            <label>License Plate</label>
                            <span id="detailLicensePlate">-</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Seating Capacity</label>
                            <span id="detailSeatingCapacity">-</span>
                        </div>
                        <div class="form-group">
                            <label>Child Seats</label>
                            <span id="detailChildSeats">-</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Fuel Efficiency</label>
                            <span id="detailFuelEfficiency">-</span>
                        </div>
                        <div class="form-group">
                            <label>Submitted Date</label>
                            <span id="detailSubmittedDate">-</span>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Description</label>
                        <p id="detailVehicleDescription">-</p>
                    </div>
                </div>

                <!-- Vehicle Photos -->
                <div class="form-section">
                    <h4><i class="fas fa-camera"></i> Vehicle Photos</h4>
                    <div class="photo-upload-grid">
                        <div class="photo-upload-item">
                            <label>Front View</label>
                            <div class="vehicle-photo" id="detailFrontPhoto">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                        <div class="photo-upload-item">
                            <label>Back View</label>
                            <div class="vehicle-photo" id="detailBackPhoto">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                        <div class="photo-upload-item">
                            <label>Side View</label>
                            <div class="vehicle-photo" id="detailSidePhoto">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                        <div class="photo-upload-item">
                            <label>Interior 1</label>
                            <div class="vehicle-photo" id="detailInside1Photo">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                        <div class="photo-upload-item">
                            <label>Interior 2</label>
                            <div class="vehicle-photo" id="detailInside2Photo">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                        <div class="photo-upload-item">
                            <label>Interior 3</label>
                            <div class="vehicle-photo" id="detailInside3Photo">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Actions -->
        <div class="form-actions">
            <button class="btn-delete" id="deleteVehicleBtn" style="display: none;">
                <i class="fas fa-trash"></i> Delete Vehicle
            </button>
            <button class="btn-cancel">Close</button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-trash-alt"></i>
                <p>Are you sure you want to delete this vehicle?</p>
                <p class="confirm-warning">This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="cancelDeleteBtn">Cancel</button>
            <button class="btn-delete" id="confirmDeleteBtn">Delete Vehicle</button>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" class="photo-viewer-modal">
    <div class="photo-viewer-content">
        <button class="photo-viewer-close">&times;</button>
        <img id="photoViewerImage" class="photo-viewer-image" src="" alt="Vehicle Photo">
    </div>
</div>
