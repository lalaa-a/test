<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Guide Requests</h1>
            <p class="page-subtitle">Manage incoming requests from travellers</p>
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
            <div class="stat-number" id="pendingRequestsCount">0</div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon approved">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="acceptedRequestsCount">0</div>
            <div class="stat-label">Accepted Requests</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rejected">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="rejectedRequestsCount">0</div>
            <div class="stat-label">Rejected Requests</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#pending-requests-section" class="nav-link active" data-section="pending">
        <i class="fas fa-clock"></i>
        Pending
    </a>
    <a href="#accepted-requests-section" class="nav-link" data-section="accepted">
        <i class="fas fa-check-circle"></i>
        Accepted
    </a>
    <a href="#rejected-requests-section" class="nav-link" data-section="rejected">
        <i class="fas fa-times-circle"></i>
        Rejected
    </a>
</div>

<!-- Requests Sections -->
<div class="requests-sections">
    <!-- Pending Requests Section -->
    <div class="requests-section" id="pending-requests-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Requests
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="pendingSearchInput" placeholder="Search requests..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="pendingChargeTypeFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="per_person">Per Person</option>
                                <option value="whole_trip">Whole Trip</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="requests-table-container" id="pendingRequestsContainer">
            <table class="requests-table" id="pendingRequestsTable">
                <thead>
                    <tr>
                        <th>Tourist</th>
                        <th>Trip Date</th>
                        <th>Charge Type</th>
                        <th>Amount</th>
                        <th>People</th>
                        <th>Requested Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pendingRequestsGrid">
                    <tr class="no-requests">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            <p>No pending requests</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accepted Requests Section -->
    <div class="requests-section" id="accepted-requests-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Accepted Requests
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="acceptedSearchInput" placeholder="Search requests..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="acceptedChargeTypeFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="per_person">Per Person</option>
                                <option value="whole_trip">Whole Trip</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="requests-table-container" id="acceptedRequestsContainer">
            <table class="requests-table" id="acceptedRequestsTable">
                <thead>
                    <tr>
                        <th>Tourist</th>
                        <th>Trip Date</th>
                        <th>Charge Type</th>
                        <th>Amount</th>
                        <th>People</th>
                        <th>Accepted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="acceptedRequestsGrid">
                    <tr class="no-requests">
                        <td colspan="7">
                            <i class="fas fa-check-circle"></i>
                            <p>No accepted requests</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rejected Requests Section -->
    <div class="requests-section" id="rejected-requests-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Rejected Requests
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="rejectedSearchInput" placeholder="Search requests..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="rejectedChargeTypeFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="per_person">Per Person</option>
                                <option value="whole_trip">Whole Trip</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="requests-table-container" id="rejectedRequestsContainer">
            <table class="requests-table" id="rejectedRequestsTable">
                <thead>
                    <tr>
                        <th>Tourist</th>
                        <th>Trip Date</th>
                        <th>Charge Type</th>
                        <th>Amount</th>
                        <th>People</th>
                        <th>Rejected Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rejectedRequestsGrid">
                    <tr class="no-requests">
                        <td colspan="7">
                            <i class="fas fa-user-times"></i>
                            <p>No rejected requests</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Requests Section -->
        <!-- Completed section removed as not needed -->
</div>

<!-- Request Details Modal -->
<div id="requestDetailsModal" class="modal">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3>Request Details</h3>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="request-details-tabs">
                <button class="tab-btn active" onclick="guideRequestsManager.switchModalTab('details')">Details</button>
                <button class="tab-btn" onclick="guideRequestsManager.switchModalTab('itinerary')">Itinerary</button>
            </div>
            <div class="tab-content">
                <div id="details-tab" class="tab-pane active">
                    <div id="requestDetailsContent">
                        <!-- Request details will be loaded here -->
                    </div>
                </div>
                <div id="itinerary-tab" class="tab-pane">
                    <div id="itineraryContent">
                        <!-- Itinerary will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="modalCloseBtn">Close</button>
            <button class="btn btn-primary" id="acceptBtn" style="display:none;">Accept Request</button>
            <button class="btn btn-danger" id="rejectBtn" style="display:none;">Reject Request</button>
        </div>
    </div>
</div>

<!-- Accept Confirmation Modal -->
<div id="acceptConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Confirm Request Acceptance</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-handshake"></i>
                <p>Are you sure you want to accept this request?</p>
                <p class="confirm-warning">Once accepted, you'll be committed to this trip.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelAcceptBtn">Cancel</button>
            <button class="btn btn-primary" id="confirmAcceptBtn">Accept Request</button>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-times-circle"></i> Confirm Request Rejection</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to reject this request?</p>
                <p class="confirm-warning">The traveller will be notified of your rejection.</p>
            </div>
            <div class="form-group">
                <label for="rejectionReason">Rejection Reason <span class="required">*</span></label>
                <textarea id="rejectionReason" class="form-control" rows="4" placeholder="Please provide a reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelRejectBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectBtn">Reject Request</button>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" class="photo-viewer-modal">
    <div class="photo-viewer-content">
        <button class="photo-viewer-close">
            <i class="fas fa-times"></i>
        </button>
        <img id="photoViewerImage" src="" alt="User Photo">
    </div>
</div>
