<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">My Tours</h1>
            <p class="page-subtitle">Manage your accepted trips and track progress</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon ongoing">
            <i class="fas fa-play-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="ongoingToursCount">0</div>
            <div class="stat-label">Ongoing Tours</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon upcoming">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="upcomingToursCount">0</div>
            <div class="stat-label">Upcoming Tours</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="completedToursCount">0</div>
            <div class="stat-label">Completed Tours</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#ongoing-tours-section" class="nav-link active" data-section="ongoing">
        <i class="fas fa-play-circle"></i>
        Ongoing
    </a>
    <a href="#upcoming-tours-section" class="nav-link" data-section="upcoming">
        <i class="fas fa-calendar-alt"></i>
        Upcoming
    </a>
    <a href="#completed-tours-section" class="nav-link" data-section="completed">
        <i class="fas fa-check-circle"></i>
        Completed
    </a>
</div>

<!-- Tours Sections -->
<div class="tours-sections">
    <!-- Ongoing Tours Section -->
    <div class="tours-section" id="ongoing-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-play-circle"></i>
                    Ongoing Tours
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="ongoingSearchInput" placeholder="Search tours..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tours-table-container" id="ongoingToursContainer">
            <table class="tours-table" id="ongoingToursTable">
                <thead>
                    <tr>
                        <th>Traveller</th>
                        <th>Trip Details</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ongoingToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-play-circle"></i>
                            <p>No ongoing tours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Tours Section -->
    <div class="tours-section" id="upcoming-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Tours
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="upcomingSearchInput" placeholder="Search tours..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tours-table-container" id="upcomingToursContainer">
            <table class="tours-table" id="upcomingToursTable">
                <thead>
                    <tr>
                        <th>Traveller</th>
                        <th>Trip Details</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="upcomingToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No upcoming tours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Tours Section -->
    <div class="tours-section" id="completed-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Tours
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search tours..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tours-table-container" id="completedToursContainer">
            <table class="tours-table" id="completedToursTable">
                <thead>
                    <tr>
                        <th>Traveller</th>
                        <th>Trip Details</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="completedToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed tours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Upcoming Tours Section -->
    <div class="tours-section" id="upcoming-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Tours
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="upcomingSearchInput" placeholder="Search tours..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tours-table-container" id="upcomingToursContainer">
            <table class="tours-table" id="upcomingToursTable">
                <thead>
                    <tr>
                        <th>Traveller</th>
                        <th>Trip Details</th>
                        <th>Vehicle</th>
                        <th>Start Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="upcomingToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No upcoming tours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Tours Section -->
    <div class="tours-section" id="completed-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Tours
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search tours..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tours-table-container" id="completedToursContainer">
            <table class="tours-table" id="completedToursTable">
                <thead>
                    <tr>
                        <th>Traveller</th>
                        <th>Trip Details</th>
                        <th>Vehicle</th>
                        <th>Completed Date</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody id="completedToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed tours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tour Details Modal -->
<div id="tourDetailsModal" class="modal">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="modalTitle">Tour Details</h3>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="request-details-tabs">
                <button class="tab-btn active" onclick="window.driverToursManager.switchModalTab('details')">Details</button>
                <button class="tab-btn" onclick="window.driverToursManager.switchModalTab('itinerary')">Itinerary</button>
            </div>
            <div class="tab-content">
                <div id="details-tab" class="tab-pane active">
                    <div id="tourDetailsContent">
                        <!-- Tour details will be loaded here -->
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
            <button id="modalCloseBtn" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<!-- Start Trip Modal -->
<div id="startTripModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Start Trip</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="pin-section">
                <label for="tripPin">Enter Trip PIN from Traveller:</label>
                <input type="text" id="tripPin" class="pin-input" placeholder="Enter 6-digit PIN" maxlength="6">
                <p class="pin-help">Get the PIN from the traveller to start this trip</p>
                <p class="pin-status" id="tripPinStatus" aria-live="polite"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button id="startTripCancelBtn" class="btn-secondary">Cancel</button>
            <button id="startTripConfirmBtn" class="btn-primary">Start Trip</button>
        </div>
    </div>
</div>

<!-- Event Completion Modal -->
<div id="eventCompletionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Trip Events</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="event-completion-layout">
                <div class="event-list-panel">
                    <div id="eventPinGateNotice" class="event-pin-gate-notice" style="display: none;"></div>
                    <div id="eventsList">
                        <!-- Events will be loaded here -->
                    </div>
                </div>

                <aside class="event-map-panel">
                    <div class="event-map-head">
                        <h4>Places and Route</h4>
                        <p>Use this map while marking event progress.</p>
                    </div>
                    <div id="driver-events-route-map" class="event-route-map"></div>
                    <p id="driver-events-map-empty-state" class="event-map-empty-state">Map points are not available for this trip.</p>
                </aside>
            </div>
        </div>
        <div class="modal-footer">
            <button id="eventCompletionCloseBtn" class="btn-secondary">Close</button>
            <button id="completeTripBtn" class="btn-primary" style="display: none;">Complete Trip</button>
        </div>
    </div>
</div>