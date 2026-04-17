<!-- Vehicle Verification Content -->

<!-- External CSS and JS includes -->
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">Vehicle Verification</h1>
                <p class="page-subtitle">Review and verify driver vehicles</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pendingVehiclesCount">0</div>
                <div class="stat-label">Pending Vehicles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="verifiedVehiclesCount">0</div>
                <div class="stat-label">Verified Vehicles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="rejectedVehiclesCount">0</div>
                <div class="stat-label">Rejected Vehicles</div>
            </div>
        </div>
    </div>

    <!-- Section Navigation -->
    <div class="section-nav">
        <a href="#pending-section" class="nav-link active" data-section="pending">
            <i class="fas fa-clock"></i>
            Pending
        </a>
        <a href="#verified-section" class="nav-link" data-section="verified">
            <i class="fas fa-check-circle"></i>
            Verified
        </a>
        <a href="#rejected-section" class="nav-link" data-section="rejected">
            <i class="fas fa-times-circle"></i>
            Rejected
        </a>
    </div>

    <!-- Verification Sections -->
    <div class="verification-sections">
        <!-- Pending Verification Section -->
        <div class="verification-section" id="pending-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-clock"></i>
                        Pending Verification
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="pendingSearchInput" placeholder="Search pending..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="pendingVehicleTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="car">Car</option>
                                    <option value="van">Van</option>
                                    <option value="bus">Bus</option>
                                    <option value="truck">Truck</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vehicles-table-container" id="pendingVehiclesContainer">
                <table class="vehicles-table" id="pendingVehiclesTable">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Owner</th>
                            <th>Email</th>
                            <th>Registration</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingVehiclesGrid">
                        <tr class="no-vehicles">
                            <td colspan="8">
                                <i class="fas fa-inbox"></i>
                                <p>No pending vehicles to verify</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verified Vehicles Section -->
        <div class="verification-section" id="verified-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Verified Vehicles
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="verifiedSearchInput" placeholder="Search verified..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="verifiedVehicleTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="car">Car</option>
                                    <option value="van">Van</option>
                                    <option value="bus">Bus</option>
                                    <option value="truck">Truck</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vehicles-table-container" id="verifiedVehiclesContainer">
                <table class="vehicles-table" id="verifiedVehiclesTable">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Owner</th>
                            <th>Email</th>
                            <th>Registration</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Verified Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="verifiedVehiclesGrid">
                        <tr class="no-vehicles">
                            <td colspan="8">
                                <i class="fas fa-check-circle"></i>
                                <p>No verified vehicles yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejected Vehicles Section -->
        <div class="verification-section" id="rejected-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-times-circle"></i>
                        Rejected Vehicles
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="rejectedSearchInput" placeholder="Search rejected..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="rejectedVehicleTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="car">Car</option>
                                    <option value="van">Van</option>
                                    <option value="bus">Bus</option>
                                    <option value="truck">Truck</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vehicles-table-container" id="rejectedVehiclesContainer">
                <table class="vehicles-table" id="rejectedVehiclesTable">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Owner</th>
                            <th>Email</th>
                            <th>Registration</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Rejected Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rejectedVehiclesGrid">
                        <tr class="no-vehicles">
                            <td colspan="8">
                                <i class="fas fa-car-crash"></i>
                                <p>No rejected vehicles yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Vehicle Details Modal -->
<div id="vehicleDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Vehicle Details</h3>
            <button class="modal-close" onclick="closeVehicleModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="vehicleDetailsContent">
            <!-- Vehicle details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeVehicleModal()">Close</button>
            <button class="btn btn-primary" id="verifyVehicleBtn" onclick="vehicleVerificationManager.verifyVehicle()">Verify Vehicle</button>
            <button class="btn btn-danger" id="rejectVehicleBtn" onclick="vehicleVerificationManager.rejectVehicle()">Reject Vehicle</button>
        </div>
    </div>
</div>

<!-- Verification Confirmation Modal -->
<div id="verifyVehicleConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Confirm Vehicle Verification</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-car"></i>
                <p>Are you sure you want to verify this vehicle?</p>
                <p class="confirm-warning">This will approve the vehicle for use in the platform.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelVerifyVehicleBtn">Cancel</button>
            <button class="btn btn-primary" id="confirmVerifyVehicleBtn">Verify Vehicle</button>
        </div>
    </div>
</div>

<!-- Rejection Confirmation Modal -->
<div id="rejectVehicleConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-car-crash"></i> Confirm Vehicle Rejection</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to reject this vehicle?</p>
                <p class="confirm-warning">This will deny the vehicle approval.</p>
            </div>
            <div class="form-group">
                <label for="vehicleRejectionReason">Rejection Reason <span class="required">*</span></label>
                <textarea id="vehicleRejectionReason" class="form-control" rows="4" placeholder="Please provide a detailed reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRejectVehicleBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectVehicleBtn">Reject Vehicle</button>
        </div>
    </div>
</div>

<!-- Revoke Verification Confirmation Modal -->
<div id="revokeVehicleVerificationModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Verification Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this vehicle verification?</p>
                <p class="confirm-warning">This will reset the vehicle to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeVehicleVerificationBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeVehicleVerificationBtn">Revoke Verification</button>
        </div>
    </div>
</div>

<!-- Revoke Rejection Confirmation Modal -->
<div id="revokeVehicleRejectionModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Rejection Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this vehicle rejection?</p>
                <p class="confirm-warning">This will reset the vehicle to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeVehicleRejectionBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeVehicleRejectionBtn">Revoke Rejection</button>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" class="photo-viewer-modal">
    <div class="photo-viewer-content">
        <button class="photo-viewer-close" onclick="closePhotoViewer()">
            <i class="fas fa-times"></i>
        </button>
        <div class="photo-viewer-label-container">
            <h3 id="photoViewerLabel" class="photo-viewer-label"></h3>
        </div>
        <img id="photoViewerImage" src="" alt="Vehicle Photo">
    </div>
</div>
