<div class="user-trip-page">
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">My Trips</h1>
                <p class="page-subtitle">Create, organize, and track every journey from one dashboard.</p>
            </div>
            <div class="header-actions">
                <button id="create-trip-btn" class="create-trip-btn" type="button">
                    <i class="fas fa-plus"></i>
                    Create New Trip
                </button>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-route"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="totalTripsCount">0</div>
                <div class="stat-label">Total Trips</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="pendingStatsCount">0</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon waiting">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="wconfirmationStatsCount">0</div>
                <div class="stat-label">Waiting Confirmation</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon payment">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="awpaymentStatsCount">0</div>
                <div class="stat-label">Awaiting Payment</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon scheduled">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="scheduledStatsCount">0</div>
                <div class="stat-label">Scheduled</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon ongoing">
                <i class="fas fa-plane"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="ongoingStatsCount">0</div>
                <div class="stat-label">Ongoing</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="completedStatsCount">0</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <div class="section-nav">
        <a href="#pending-section" class="nav-link active" data-section="pending">
            <i class="fas fa-clock"></i>
            Pending
        </a>
        <a href="#wconfirmation-section" class="nav-link" data-section="wconfirmation">
            <i class="fas fa-hourglass-half"></i>
            Waiting Confirmation
        </a>
        <a href="#awpayment-section" class="nav-link" data-section="awpayment">
            <i class="fas fa-credit-card"></i>
            Awaiting Payment
        </a>
        <a href="#scheduled-section" class="nav-link" data-section="scheduled">
            <i class="fas fa-calendar-check"></i>
            Scheduled
        </a>
        <a href="#ongoing-section" class="nav-link" data-section="ongoing">
            <i class="fas fa-plane"></i>
            Ongoing
        </a>
        <a href="#completed-section" class="nav-link" data-section="completed">
            <i class="fas fa-check-circle"></i>
            Completed
        </a>
    </div>

    <div class="trip-sections">
        <div class="trip-section" id="pending-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-clock"></i>
                        Pending Trips
                        <span class="section-count" id="pending-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="pendingSearchInput" placeholder="Search pending trips..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="pendingSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-inbox"></i>
                                <p>No pending trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="trip-section" id="wconfirmation-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-hourglass-half"></i>
                        Waiting Confirmation Trips
                        <span class="section-count" id="wconfirmation-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="wconfirmationSearchInput" placeholder="Search waiting confirmation..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="wconfirmationSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="wconfirmationTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-hourglass-half"></i>
                                <p>No waiting confirmation trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="trip-section" id="awpayment-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-credit-card"></i>
                        Awaiting Payment Trips
                        <span class="section-count" id="awpayment-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="awpaymentSearchInput" placeholder="Search awaiting payment..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="awpaymentSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="awpaymentTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-credit-card"></i>
                                <p>No awaiting payment trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="trip-section" id="scheduled-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-calendar-check"></i>
                        Scheduled Trips
                        <span class="section-count" id="scheduled-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="scheduledSearchInput" placeholder="Search scheduled trips..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="scheduledSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="scheduledTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-calendar-check"></i>
                                <p>No scheduled trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="trip-section" id="ongoing-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-plane"></i>
                        Ongoing Trips
                        <span class="section-count" id="ongoing-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="ongoingSearchInput" placeholder="Search ongoing trips..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="ongoingSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ongoingTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-plane"></i>
                                <p>No ongoing trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="trip-section" id="completed-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-check-circle"></i>
                        Completed Trips
                        <span class="section-count" id="completed-count">0</span>
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="completedSearchInput" placeholder="Search completed trips..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="completedSortFilter" class="filter-select">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trips-table-container">
                <table class="trips-table">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Dates</th>
                            <th>People</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="completedTripsGrid">
                        <tr class="no-trips-row">
                            <td colspan="6">
                                <i class="fas fa-check-circle"></i>
                                <p>No completed trips</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="trip-modal" id="popup" aria-hidden="true">
        <div class="trip-modal-content">
            <div class="trip-modal-header">
                <h2 id="popup-title">Create New Trip</h2>
                <button type="button" class="modal-close-btn" id="close-popup-btn" aria-label="Close trip form">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="create-trip-form" class="trip-form">
                <div class="form-group">
                    <label for="trip-title">Trip Title</label>
                    <input type="text" id="trip-title" name="trip_title" placeholder="Enter trip title" required>
                </div>

                <div class="form-group">
                    <label for="trip-description">Description</label>
                    <textarea id="trip-description" name="trip_description" rows="3" placeholder="Describe your trip route, goals, or notes..."></textarea>
                </div>

                <div class="form-group">
                    <label for="people-count">Number of People</label>
                    <input type="number" id="people-count" name="people_count" min="1" max="50" placeholder="How many people are joining?" required>
                </div>

                <div class="form-group">
                    <label>Trip Dates</label>
                    <div class="date-inputs">
                        <div>
                            <label for="start-date" class="sub-label">Start Date</label>
                            <input type="date" id="start-date" name="start_date" required>
                        </div>
                        <div>
                            <label for="end-date" class="sub-label">End Date</label>
                            <input type="date" id="end-date" name="end_date" required>
                        </div>
                    </div>
                </div>

                <div class="trip-modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancel-popup">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-trip">Create Trip</button>
                </div>
            </form>
        </div>
    </div>
</div>
