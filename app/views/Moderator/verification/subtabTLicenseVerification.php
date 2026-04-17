<!-- Tourist License Verification Content -->

<!-- External CSS and JS includes -->
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">Tourist License Verification</h1>
                <p class="page-subtitle">Review and verify tourist licenses for guides and drivers</p>
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
                <div class="stat-number" id="pendingGuidesCount">0</div>
                <div class="stat-label">Pending Guide Licenses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pendingDriversCount">0</div>
                <div class="stat-label">Pending Driver Licenses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="verifiedGuidesCount">0</div>
                <div class="stat-label">Verified Guide Licenses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="verifiedDriversCount">0</div>
                <div class="stat-label">Verified Driver Licenses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="rejectedLicensesCount">0</div>
                <div class="stat-label">Rejected Licenses</div>
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
        <!-- Pending License Verification Section -->
        <div class="verification-section" id="pending-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-clock"></i>
                        Pending License Verification
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="pendingSearchInput" placeholder="Search pending..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="pendingLicenseTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="licenses-table-container" id="pendingLicensesContainer">
                <table class="licenses-table" id="pendingLicensesTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>License Number</th>
                            <th>Expiry Date</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingLicensesGrid">
                        <tr class="no-licenses">
                            <td colspan="8">
                                <i class="fas fa-inbox"></i>
                                <p>No pending licenses to verify</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verified Licenses Section -->
        <div class="verification-section" id="verified-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Verified Licenses
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="verifiedSearchInput" placeholder="Search verified..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="verifiedLicenseTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="licenses-table-container" id="verifiedLicensesContainer">
                <table class="licenses-table" id="verifiedLicensesTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>License Number</th>
                            <th>Expiry Date</th>
                            <th>Verified Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="verifiedLicensesGrid">
                        <tr class="no-licenses">
                            <td colspan="8">
                                <i class="fas fa-check-circle"></i>
                                <p>No verified licenses yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejected Licenses Section -->
        <div class="verification-section" id="rejected-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-times-circle"></i>
                        Rejected Licenses
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="rejectedSearchInput" placeholder="Search rejected..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="rejectedLicenseTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="licenses-table-container" id="rejectedLicensesContainer">
                <table class="licenses-table" id="rejectedLicensesTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>License Number</th>
                            <th>Expiry Date</th>
                            <th>Rejected Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rejectedLicensesGrid">
                        <tr class="no-licenses">
                            <td colspan="8">
                                <i class="fas fa-user-times"></i>
                                <p>No rejected licenses yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- License Details Modal -->
<div id="licenseDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>License Details</h3>
            <button class="modal-close" onclick="closeLicenseModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="licenseDetailsContent">
            <!-- License details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeLicenseModal()">Close</button>
            <button class="btn btn-primary" id="verifyLicenseBtn" onclick="licenseVerificationManager.verifyLicense()">Verify License</button>
            <button class="btn btn-danger" id="rejectLicenseBtn" onclick="licenseVerificationManager.rejectLicense()">Reject License</button>
        </div>
    </div>
</div>

<!-- License Verification Confirmation Modal -->
<div id="verifyLicenseConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Confirm License Verification</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-user-check"></i>
                <p>Are you sure you want to verify this license?</p>
                <p class="confirm-warning">This will grant the license approval for use in the platform.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelVerifyLicenseBtn">Cancel</button>
            <button class="btn btn-primary" id="confirmVerifyLicenseBtn">Verify License</button>
        </div>
    </div>
</div>

<!-- License Rejection Confirmation Modal -->
<div id="rejectLicenseConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-times"></i> Confirm License Rejection</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to reject this license?</p>
                <p class="confirm-warning">This will deny the license approval.</p>
            </div>
            <div class="form-group">
                <label for="licenseRejectionReason">Rejection Reason <span class="required">*</span></label>
                <textarea id="licenseRejectionReason" class="form-control" rows="4" placeholder="Please provide a detailed reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRejectLicenseBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectLicenseBtn">Reject License</button>
        </div>
    </div>
</div>

<!-- Revoke License Verification Confirmation Modal -->
<div id="revokeLicenseVerificationModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Verification Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this license verification?</p>
                <p class="confirm-warning">This will reset the license to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeLicenseVerificationBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeLicenseVerificationBtn">Revoke Verification</button>
        </div>
    </div>
</div>

<!-- Revoke License Rejection Confirmation Modal -->
<div id="revokeLicenseRejectionModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Rejection Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this license rejection?</p>
                <p class="confirm-warning">This will reset the license to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeLicenseRejectionBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeLicenseRejectionBtn">Revoke Rejection</button>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" class="photo-viewer-modal">
    <div class="photo-viewer-content">
        <button class="photo-viewer-close" onclick="closePhotoViewer()">
            <i class="fas fa-times"></i>
        </button>
        <img id="photoViewerImage" src="" alt="License Photo">
    </div>
</div>
