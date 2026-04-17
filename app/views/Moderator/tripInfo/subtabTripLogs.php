<!-- Trip Logs Content -->

<!-- External CSS and JS includes -->
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">Trip Logs</h1>
                <p class="page-subtitle">Site-wide planned trips and assigned drivers/guides by trip status</p>
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
                <div class="stat-number" id="awPaymentCount">0</div>
                <div class="stat-label">Awaiting Payment</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon scheduled">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="scheduledCount">0</div>
                <div class="stat-label">Scheduled</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ongoing">
                <i class="fas fa-play"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="ongoingCount">0</div>
                <div class="stat-label">Ongoing</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="completedCount">0</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cancelled">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="cancelledCount">0</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- Section Navigation -->
    <div class="section-nav">
        <a href="#awPayment-section" class="nav-link active" data-section="awPayment">
            <i class="fas fa-clock"></i>
            Awaiting Payment
        </a>
        <a href="#scheduled-section" class="nav-link" data-section="scheduled">
            <i class="fas fa-calendar"></i>
            Scheduled
        </a>
        <a href="#ongoing-section" class="nav-link" data-section="ongoing">
            <i class="fas fa-play"></i>
            Ongoing
        </a>
        <a href="#completed-section" class="nav-link" data-section="completed">
            <i class="fas fa-check-circle"></i>
            Completed
        </a>
        <a href="#cancelled-section" class="nav-link" data-section="cancelled">
            <i class="fas fa-times-circle"></i>
            Cancelled
        </a>
    </div>

    <!-- Trip Sections -->
    <div class="verification-sections">
        <!-- Awaiting Payment Section -->
        <div class="verification-section" id="awPayment-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-clock"></i>
                        Awaiting Payment
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="awPaymentSearchInput" placeholder="Search awaiting payment..." class="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="awPaymentAccountsContainer">
                <table class="accounts-table" id="awPaymentAccountsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Trip Title</th>
                            <th>Traveller</th>
                            <th>People</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="awPaymentTripGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-clock"></i>
                                <p>No trips awaiting payment</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scheduled Section -->
        <div class="verification-section" id="scheduled-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-calendar"></i>
                        Scheduled
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="scheduledSearchInput" placeholder="Search scheduled..." class="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="scheduledAccountsContainer">
                <table class="accounts-table" id="scheduledAccountsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Trip Title</th>
                            <th>Traveller</th>
                            <th>People</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="scheduledTripGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-calendar"></i>
                                <p>No scheduled trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ongoing Section -->
        <div class="verification-section" id="ongoing-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-play"></i>
                        Ongoing
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="ongoingSearchInput" placeholder="Search ongoing..." class="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="ongoingAccountsContainer">
                <table class="accounts-table" id="ongoingAccountsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Trip Title</th>
                            <th>Traveller</th>
                            <th>People</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ongoingTripGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-play"></i>
                                <p>No ongoing trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Completed Section -->
        <div class="verification-section" id="completed-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Completed
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="completedSearchInput" placeholder="Search completed..." class="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="completedAccountsContainer">
                <table class="accounts-table" id="completedAccountsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Trip Title</th>
                            <th>Traveller</th>
                            <th>People</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="completedTripGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-check-circle"></i>
                                <p>No completed trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cancelled Section -->
        <div class="verification-section" id="cancelled-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-times-circle"></i>
                        Cancelled
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="cancelledSearchInput" placeholder="Search cancelled..." class="search-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accounts-table-container" id="cancelledAccountsContainer">
                <table class="accounts-table" id="cancelledAccountsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Trip Title</th>
                            <th>Traveller</th>
                            <th>People</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cancelledTripGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-times-circle"></i>
                                <p>No cancelled trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Trip Details Modal -->
<div id="tripDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Trip Details</h3>
            <button class="modal-close" onclick="closeTripModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="tripDetailsContent">
            <!-- Trip details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeTripModal()">Close</button>
        </div>
    </div>
</div>
