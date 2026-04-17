<!-- Driver Availability Calendar -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Availability Calendar</h1>
            <p class="page-subtitle">Set your availability for the next 60 days</p>
        </div>
        <div class="header-actions">
            <button class="btn-secondary" id="resetAvailabilityBtn">
                <i class="fas fa-undo"></i> Reset All
            </button>
            <button class="btn-primary" id="saveAvailabilityBtn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon available">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="availableDaysCount">0</div>
            <div class="stat-label">Available Days</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon unavailable">
            <i class="fas fa-calendar-times"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="unavailableDaysCount">0</div>
            <div class="stat-label">Unavailable Days</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalDaysCount">60</div>
            <div class="stat-label">Total Days</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon percentage">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="availabilityPercentage">0%</div>
            <div class="stat-label">Availability Rate</div>
        </div>
    </div>
</div>

<!-- Calendar Legend -->
<div class="calendar-legend">
    <div class="legend-item">
        <div class="legend-color available"></div>
        <span>Available</span>
    </div>
    <div class="legend-item">
        <div class="legend-color unavailable"></div>
        <span>Unavailable</span>
    </div>
    <div class="legend-item">
        <div class="legend-color today"></div>
        <span>Today</span>
    </div>
    <div class="legend-item">
        <div class="legend-color past"></div>
        <span>Past Days</span>
    </div>
</div>

<!-- Calendar Container -->
<div class="calendar-container">
    <div class="calendar-header">
        <button class="calendar-nav-btn" id="prevMonthBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h2 class="calendar-title" id="calendarTitle">Loading...</h2>
        <button class="calendar-nav-btn" id="nextMonthBtn">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid" id="calendarGrid">
        <!-- Days will be populated by JavaScript -->
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <div class="action-group">
        <h3>Quick Selection</h3>
        <div class="action-buttons">
            <button class="btn-outline" id="selectWeekendsBtn">
                <i class="fas fa-calendar-week"></i> Select All Weekends
            </button>
            <button class="btn-outline" id="selectWeekdaysBtn">
                <i class="fas fa-briefcase"></i> Select All Weekdays
            </button>
            <button class="btn-outline" id="clearSelectionBtn">
                <i class="fas fa-eraser"></i> Clear Selection
            </button>
        </div>
    </div>
</div>

<!-- Instructions -->
<div class="instructions-card">
    <div class="instructions-header">
        <i class="fas fa-info-circle"></i>
        <h3>How to Set Your Availability</h3>
    </div>
    <div class="instructions-content">
        <ul>
            <li><strong>Available Days:</strong> Click on any day to mark it as unavailable (red)</li>
            <li><strong>Unavailable Days:</strong> Click again to make it available (green)</li>
            <li><strong>Calendar Range:</strong> Shows the next 60 days from today</li>
            <li><strong>Save Changes:</strong> Don't forget to save your availability settings</li>
            <li><strong>Auto-update:</strong> Your availability will be visible to customers booking tours</li>
        </ul>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="saveConfirmationModal" class="modal">
    <div class="modal-content confirmation-modal">
        <div class="modal-header">
            <h3><i class="fas fa-save"></i> Save Availability</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="confirmation-message">
                <i class="fas fa-question-circle confirmation-icon"></i>
                <p>Are you sure you want to save these availability changes?</p>
                <div class="availability-summary">
                    <div class="summary-item">
                        <span class="summary-label">Available Days:</span>
                        <span class="summary-value" id="confirmAvailableDays">0</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Unavailable Days:</span>
                        <span class="summary-value" id="confirmUnavailableDays">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="cancelSaveBtn">Cancel</button>
            <button class="btn-primary" id="confirmSaveBtn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>