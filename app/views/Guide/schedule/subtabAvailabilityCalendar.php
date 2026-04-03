<!-- Guide Availability Calendar -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Availability Calendar</h1>
            <p class="page-subtitle">Set your availability for the next 90 days</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon unavailable">
            <i class="fas fa-calendar-times"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="unavailableSlotsCount">0</div>
            <div class="stat-label">Unavailable Time Slots</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalHoursCount">0</div>
            <div class="stat-label">Total Hours Unavailable</div>
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
        <div class="legend-color partial"></div>
        <span>Partially Unavailable</span>
    </div>
    <div class="legend-item">
        <div class="legend-color unavailable"></div>
        <span>Fully Unavailable</span>
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
            <li><strong>Time Slots:</strong> Click on any day to set specific unavailable time slots within that day</li>
            <li><strong>Multiple Slots:</strong> You can set multiple time periods per day (e.g., 9:00-12:00 and 14:00-17:00)</li>
            <li><strong>Calendar Range:</strong> Shows the next 90 days from today</li>
            <li><strong>Reasons:</strong> Choose between personal reasons or booked for trips</li>
            <li><strong>Recurring:</strong> Set weekly recurring unavailability patterns</li>
        </ul>
    </div>
</div>

<!-- Time Slot Modal -->
<div id="timeSlotModal" class="modal">
    <div class="modal-content time-slot-modal">
        <div class="modal-header">
            <h3><i class="fas fa-clock"></i> Set Unavailable Time Slots</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="slot-form">
                <div class="form-group">
                    <label for="slotDate">Date:</label>
                    <input type="text" id="slotDate" readonly class="form-control">
                </div>

                <!-- Existing Slots Display -->
                <div class="existing-slots" id="existingSlots">
                    <h4>Current Unavailable Slots:</h4>
                    <div id="slotsList" class="slots-list">
                        <!-- Slots will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Add New Slot -->
                <div class="add-slot-section">
                    <h4>Add New Time Slot:</h4>
                    <div class="slot-inputs">
                        <div class="form-group">
                            <label for="startTime">Start Time <span style="color: #dc3545;">*</span>:</label>
                            <input type="time" id="startTime" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="endTime">End Time <span style="color: #dc3545;">*</span>:</label>
                            <input type="time" id="endTime" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slotReason">Reason <span style="color: #dc3545;">*</span>:</label>
                        <select id="slotReason" class="form-control" required>
                            <option value="personal">Personal</option>
                            <option value="booked">Booked for Trip</option>
                        </select>
                    </div>

                    <div class="form-group" id="personalReasonGroup">
                        <label for="slotPersonalReason">Personal Reason <span style="color: #dc3545;">*</span>:</label>
                        <textarea id="slotPersonalReason" class="form-control" rows="2" placeholder="Please provide a reason..." maxlength="500" required></textarea>
                        <small class="form-hint">Maximum 500 characters. Required for personal reasons.</small>
                    </div>

                    <div class="form-group" id="tripIdGroup" style="display: none;">
                        <label for="slotTripId">Trip ID:</label>
                        <input type="number" id="slotTripId" class="form-control" placeholder="Enter trip ID if booked">
                        <small class="form-hint">Optional: Enter the trip ID if this slot is booked for a specific trip.</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="isRecurring">
                            <span class="checkmark"></span>
                            Make this recurring weekly
                        </label>
                    </div>

                    <div class="form-group" id="recurringOptions" style="display: none;">
                        <label for="recurringEndDate">Recurring End Date:</label>
                        <input type="date" id="recurringEndDate" class="form-control">
                        <small class="form-hint">When should this recurring unavailability end?</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="cancelSlotBtn">Cancel</button>
            <button class="btn-primary" id="addSlotBtn">
                <i class="fas fa-plus"></i> Add Time Slot
            </button>
            <button class="btn-success" id="saveSlotsBtn">
                <i class="fas fa-save"></i> Save All Changes
            </button>
        </div>
    </div>
</div>
