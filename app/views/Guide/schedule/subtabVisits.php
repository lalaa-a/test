<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">My Visits</h1>
            <p class="page-subtitle">Manage your accepted visits and event progress</p>
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
            <div class="stat-label">Ongoing Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon upcoming">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="upcomingToursCount">0</div>
            <div class="stat-label">Upcoming Visits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="completedToursCount">0</div>
            <div class="stat-label">Completed Visits</div>
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

<!-- Visits Sections -->
<div class="tours-sections">
    <div class="tours-section" id="ongoing-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-play-circle"></i>
                    Ongoing Visits
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="ongoingSearchInput" placeholder="Search visits..." class="search-input">
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
                        <th>Assigned Spots</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ongoingToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-play-circle"></i>
                            <p>No ongoing visits</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tours-section" id="upcoming-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Visits
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="upcomingSearchInput" placeholder="Search visits..." class="search-input">
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
                        <th>Assigned Spots</th>
                        <th>Start Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="upcomingToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No upcoming visits</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tours-section" id="completed-tours-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Visits
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search visits..." class="search-input">
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
                        <th>Assigned Spots</th>
                        <th>Completed Date</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody id="completedToursGrid">
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed visits</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Visit Details Modal -->
<div id="tourDetailsModal" class="modal">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="modalTitle">Visit Details</h3>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="tourDetailsContent"></div>
        </div>
        <div class="modal-footer">
            <button id="modalCloseBtn" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<!-- Event Completion Modal -->
<div id="eventCompletionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Visit Events</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="event-completion-layout">
                <div class="event-list-panel">
                    <div id="eventPinGateNotice" class="event-pin-gate-notice" style="display: none;"></div>
                    <div id="eventsList"></div>
                </div>

                <aside class="event-map-panel">
                    <div class="event-map-head">
                        <h4>Places and Route</h4>
                        <p>Use this map while marking event progress.</p>
                    </div>
                    <div id="driver-events-route-map" class="event-route-map"></div>
                    <p id="driver-events-map-empty-state" class="event-map-empty-state">Map points are not available for this visit.</p>
                </aside>
            </div>
        </div>
        <div class="modal-footer">
            <button id="eventCompletionCloseBtn" class="btn-secondary">Close</button>
        </div>
    </div>
</div>
