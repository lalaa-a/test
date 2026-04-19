<!-- Account Verification Content -->

<!-- External CSS and JS includes -->
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">Account Verification</h1>
                <p class="page-subtitle">Review and verify guide and driver accounts</p>
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
                <div class="stat-label">Pending Guides</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pendingDriversCount">0</div>
                <div class="stat-label">Pending Drivers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="verifiedGuidesCount">0</div>
                <div class="stat-label">Verified Guides</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="verifiedDriversCount">0</div>
                <div class="stat-label">Verified Drivers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="rejectedAccountsCount">0</div>
                <div class="stat-label">Rejected Accounts</div>
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
                                <select id="pendingAccountTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="pendingAccountsContainer">
                <table class="accounts-table" id="pendingAccountsTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>NIC/ID</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingAccountsGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-inbox"></i>
                                <p>No pending accounts to verify</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verified Accounts Section -->
        <div class="verification-section" id="verified-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Verified Accounts
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="verifiedSearchInput" placeholder="Search verified..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="verifiedAccountTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="verifiedAccountsContainer">
                <table class="accounts-table" id="verifiedAccountsTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>NIC/ID</th>
                            <th>Verified Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="verifiedAccountsGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-check-circle"></i>
                                <p>No verified accounts yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejected Accounts Section -->
        <div class="verification-section" id="rejected-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-times-circle"></i>
                        Rejected Accounts
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="rejectedSearchInput" placeholder="Search rejected..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="rejectedAccountTypeFilter" class="filter-select">
                                    <option value="all">All Types</option>
                                    <option value="guide">Guides</option>
                                    <option value="driver">Drivers</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="rejectedAccountsContainer">
                <table class="accounts-table" id="rejectedAccountsTable">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>NIC/ID</th>
                            <th>Rejected Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rejectedAccountsGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-user-times"></i>
                                <p>No rejected accounts yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<!-- User Details Modal -->
<div id="userDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>User Details</h3>
            <button class="modal-close" onclick="closeUserModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="userDetailsContent">
            <!-- User details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeUserModal()">Close</button>
            <button class="btn btn-primary" id="verifyBtn" onclick="accountVerificationManager.verifyAccount()">Verify Account</button>
            <button class="btn btn-danger" id="rejectBtn" onclick="accountVerificationManager.rejectAccount()">Reject</button>
        </div>
    </div>
</div>

<!-- Verification Confirmation Modal -->
<div id="verifyConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Confirm Account Verification</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-user-check"></i>
                <p>Are you sure you want to verify this account?</p>
                <p class="confirm-warning">This will grant the user full access to the platform.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelVerifyBtn">Cancel</button>
            <button class="btn btn-primary" id="confirmVerifyBtn">Verify Account</button>
        </div>
    </div>
</div>

<!-- Rejection Confirmation Modal -->
<div id="rejectConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-times"></i> Confirm Account Rejection</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to reject this account?</p>
                <p class="confirm-warning">This will deny the user access to the platform.</p>
            </div>
            <div class="form-group">
                <label for="rejectionReason">Rejection Reason <span class="required">*</span></label>
                <textarea id="rejectionReason" class="form-control" rows="4" placeholder="Please provide a detailed reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRejectBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectBtn">Reject Account</button>
        </div>
    </div>
</div>

<!-- Revoke Verification Confirmation Modal -->
<div id="revokeVerificationModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Verification Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this account verification?</p>
                <p class="confirm-warning">This will reset the account to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeVerificationBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeVerificationBtn">Revoke Verification</button>
        </div>
    </div>
</div>

<!-- Revoke Rejection Confirmation Modal -->
<div id="revokeRejectionModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Confirm Rejection Revoke</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to revoke this account rejection?</p>
                <p class="confirm-warning">This will reset the account to pending status for re-review.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRevokeRejectionBtn">Cancel</button>
            <button class="btn btn-warning" id="confirmRevokeRejectionBtn">Revoke Rejection</button>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" class="photo-viewer-modal">
    <div class="photo-viewer-content">
        <button class="photo-viewer-close" onclick="closePhotoViewer()">
            <i class="fas fa-times"></i>
        </button>
        <img id="photoViewerImage" src="" alt="User Photo">
    </div>
</div>

