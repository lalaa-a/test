<!-- Driver Payouts Content -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Driver Payouts</h1>
            <p class="page-subtitle">Manage completed, pending, and cancelled driver payouts</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="completedPayoutsCount">0</div>
            <div class="stat-label">Completed Payouts</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="pendingPayoutsCount">0</div>
            <div class="stat-label">Pending Payouts</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cancelled">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="cancelledPayoutsCount">0</div>
            <div class="stat-label">Cancelled Payouts</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalPayoutAmount">$0.00</div>
            <div class="stat-label">Total Payout Amount</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#completed-section" class="nav-link active" data-section="completed">
        <i class="fas fa-check-circle"></i>
        Completed
    </a>
    <a href="#pending-section" class="nav-link" data-section="pending">
        <i class="fas fa-clock"></i>
        Pending
    </a>
    <a href="#cancelled-section" class="nav-link" data-section="cancelled">
        <i class="fas fa-times-circle"></i>
        Cancelled
    </a>
</div>

<!-- Payout Sections -->
<div class="verification-sections">
    <!-- Completed Payouts Section -->
    <div class="verification-section" id="completed-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Payouts
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search completed..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="completedPayoutFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payouts</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="completedPayoutsContainer">
            <table class="accounts-table" id="completedPayoutsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Driver</th>
                        <th>Amount</th>
                        <th>Payout Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="completedPayoutsGrid">
                    <tr class="no-accounts">
                        <td colspan="6">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed payouts yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pending Payouts Section -->
    <div class="verification-section" id="pending-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Payouts
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="pendingSearchInput" placeholder="Search pending..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="pendingPayoutFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payouts</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="pendingPayoutsContainer">
            <table class="accounts-table" id="pendingPayoutsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Driver</th>
                        <th>Amount</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pendingPayoutsGrid">
                    <tr class="no-accounts">
                        <td colspan="6">
                            <i class="fas fa-clock"></i>
                            <p>No pending payouts yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cancelled Payouts Section -->
    <div class="verification-section" id="cancelled-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Cancelled Payouts
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="cancelledSearchInput" placeholder="Search cancelled..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="cancelledPayoutFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payouts</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="cancelledPayoutsContainer">
            <table class="accounts-table" id="cancelledPayoutsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Driver</th>
                        <th>Amount</th>
                        <th>Cancellation Date</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cancelledPayoutsGrid">
                    <tr class="no-accounts">
                        <td colspan="6">
                            <i class="fas fa-times-circle"></i>
                            <p>No cancelled payouts yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payout Details Modal -->
<div id="payoutDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Payout Details</h3>
            <button class="modal-close" onclick="driverPayoutsManager.closeModal('payoutDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="payoutDetailsContent">
            <!-- Payout details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="driverPayoutsManager.closeModal('payoutDetailsModal')">Close</button>
        </div>
    </div>
</div>
