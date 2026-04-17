<!-- Trip Control Content -->

<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Trip Control</h1>
            <p class="page-subtitle">Search by trip ID and reassign drivers or event guides with availability checks</p>
        </div>
    </div>
</div>

<div class="trip-control-search-card">
    <div class="search-filter-section">
        <div class="search-box trip-id-search-box">
            <i class="fas fa-search"></i>
            <input type="number" id="tripControlTripIdInput" placeholder="Enter Trip ID" class="search-input" min="1">
        </div>
        <button type="button" class="btn btn-primary" id="tripControlSearchBtn">
            <i class="fas fa-search"></i>
            Search Trip
        </button>
        <button type="button" class="btn btn-secondary" id="tripControlClearBtn">
            <i class="fas fa-undo"></i>
            Clear
        </button>
    </div>
</div>

<div id="tripControlMessage"></div>

<div id="tripControlResultWrapper" class="trip-control-result-wrapper" style="display:none;">
    <div class="trip-control-grid">
        <div class="trip-summary-panel">
            <h3><i class="fas fa-route"></i> Trip Summary</h3>
            <div id="tripControlSummaryContent"></div>
        </div>

        <div class="trip-driver-panel">
            <h3><i class="fas fa-car"></i> Driver Assignment</h3>
            <div id="tripControlDriverContent"></div>
        </div>
    </div>

    <div class="verification-section" id="tripControlEventsSection" style="display:block;">
        <div class="section-header">
            <div class="section-header-content">
                <h2><i class="fas fa-map-marked-alt"></i> Event Guide Assignments</h2>
                <div class="section-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="tripControlEventSearchInput" class="search-input" placeholder="Search events by event ID, spot, status...">
                    </div>
                </div>
            </div>
        </div>

        <div class="accounts-table-container">
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Event Type</th>
                        <th>Travel Spot / Location</th>
                        <th>Current Guide</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tripControlEventsGrid">
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            <p>Search a trip to view guide assignments</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="tripControlDriverModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-car"></i> Replace Driver</h3>
            <button type="button" class="modal-close" onclick="closeTripControlDriverModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-helper-text">
                Available drivers are listed first. Unavailable entries can still be assigned using Manual Assign.
            </div>
            <div class="accounts-table-container">
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Capacity</th>
                            <th>Availability</th>
                            <th>Rate / Day</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tripControlDriverCandidatesGrid"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeTripControlDriverModal()">Close</button>
        </div>
    </div>
</div>

<div id="tripControlGuideModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-friends"></i> Replace Event Guide</h3>
            <button type="button" class="modal-close" onclick="closeTripControlGuideModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-helper-text">
                Search and select a guide to replace the current assignment.
            </div>
            <div class="search-box" style="margin-bottom: 15px;">
                <i class="fas fa-search"></i>
                <input type="text" id="tripControlGuideSearchInput" class="search-input" placeholder="Search guides by ID or name...">
            </div>
            <div class="accounts-table-container">
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th>Guide ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Experience</th>
                            <th>Languages</th>
                            <th>Hourly Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tripControlGuideCandidatesGrid"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeTripControlGuideModal()">Close</button>
        </div>
    </div>
</div>