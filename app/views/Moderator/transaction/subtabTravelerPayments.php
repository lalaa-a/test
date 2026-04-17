<!-- Traveler Payments Content -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Traveler Payments</h1>
            <p class="page-subtitle">Manage completed, cancelled, and refunded payments</p>
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
            <div class="stat-number" id="completedPaymentsCount">0</div>
            <div class="stat-label">Completed Payments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cancelled">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="cancelledPaymentsCount">0</div>
            <div class="stat-label">Cancelled Payments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon refunded">
            <i class="fas fa-undo-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="refundedPaymentsCount">0</div>
            <div class="stat-label">Refunded Payments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalRevenue">$0.00</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#completed-section" class="nav-link active" data-section="completed">
        <i class="fas fa-check-circle"></i>
        Completed
    </a>
    <a href="#cancelled-section" class="nav-link" data-section="cancelled">
        <i class="fas fa-times-circle"></i>
        Cancelled
    </a>
    <a href="#refunded-section" class="nav-link" data-section="refunded">
        <i class="fas fa-undo-alt"></i>
        Refunded
    </a>
</div>

<!-- Payment Sections -->
<div class="verification-sections">
    <!-- Completed Payments Section -->
    <div class="verification-section" id="completed-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Payments
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search completed..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="completedPaymentFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payments</option>
                                <option value="guide">Guide Payments</option>
                                <option value="site">Site Service Charges</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="completedPaymentsContainer">
            <table class="accounts-table" id="completedPaymentsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Traveler</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="completedPaymentsGrid">
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed payments yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cancelled Payments Section -->
    <div class="verification-section" id="cancelled-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Cancelled Payments
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="cancelledSearchInput" placeholder="Search cancelled..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="cancelledPaymentFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payments</option>
                                <option value="guide">Guide Payments</option>
                                <option value="site">Site Service Charges</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="cancelledPaymentsContainer">
            <table class="accounts-table" id="cancelledPaymentsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Traveler</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cancelledPaymentsGrid">
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-times-circle"></i>
                            <p>No cancelled payments yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Refunded Payments Section -->
    <div class="verification-section" id="refunded-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-undo-alt"></i>
                    Refunded Payments
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="refundedSearchInput" placeholder="Search refunded..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="refundedPaymentFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Payments</option>
                                <option value="guide">Guide Payments</option>
                                <option value="site">Site Service Charges</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accounts-table-container" id="refundedPaymentsContainer">
            <table class="accounts-table" id="refundedPaymentsTable">
                <thead>
                    <tr>
                        <th>Trip ID</th>
                        <th>Traveler</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="refundedPaymentsGrid">
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-undo-alt"></i>
                            <p>No refunded payments yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div id="paymentDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Payment Details</h3>
            <button class="modal-close" onclick="travelerPaymentsManager.closeModal('paymentDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="paymentDetailsContent">
            <!-- Payment details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="travelerPaymentsManager.closeModal('paymentDetailsModal')">Close</button>
        </div>
    </div>
</div>
