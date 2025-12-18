<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Create Trip Section -->
    <div class="create-trip-section">
        <button id="create-trip-btn" class="create-trip-btn">
            <i class="fas fa-plus"></i>
            Create New Trip
        </button>
    </div>

    <!-- Trip Management Sections -->
    <div class="trip-management">
        
        <!-- Ongoing Trips Section -->
        <div class="trip-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-plane"></i>
                    Ongoing Trips
                </h3>
                <span class="section-count" id="ongoing-count">0</span>
            </div>
            <div class="trip-cards-container" id="ongoing-trips">
                <!-- Ongoing trip cards will be inserted here -->
            </div>
        </div>

        <!-- Pending Trips Section -->
        <div class="trip-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fa-solid fa-clock"></i>
                    Pending Trips
                </h3>
                <span class="section-count" id="pending-count">0</span>
            </div>
            <div class="trip-cards-container" id="pending-trips">
                <!-- Scheduled trip cards will be inserted here -->
            </div>
        </div>

        <!-- Scheduled Trips Section -->
        <div class="trip-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    Scheduled Trips
                </h3>
                <span class="section-count" id="scheduled-count">0</span>
            </div>
            <div class="trip-cards-container" id="scheduled-trips">
                <!-- Scheduled trip cards will be inserted here -->
            </div>
        </div>

        <!-- Completed Trips Section -->
        <div class="trip-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-check-circle"></i>
                    Completed Trips
                </h3>
                <span class="section-count" id="completed-count">0</span>
            </div>
            <div class="trip-cards-container" id="completed-trips">
                <!-- Completed trip cards will be inserted here -->
            </div>
        </div>

    </div>

    <!-- Popup -->
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <h2>Create New Trip</h2>
            
            <form id="create-trip-form">
                <div class="form-group">
                    <label for="trip-title">Trip Title</label>
                    <input type="text" id="trip-title" name="trip_title" placeholder="Enter trip title" required>
                </div>
                
                <div class="form-group">
                    <label for="trip-description">Description</label>
                    <textarea id="trip-description" name="trip_description" placeholder="Describe your trip..." rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Trip Dates</label>
                    <div class="date-inputs">
                        <div>
                            <label for="start-date" style="font-size: 0.8rem; color: var(--text-secondary);">Start Date</label>
                            <input type="date" id="start-date" name="start_date" required>
                        </div>
                        <div>
                            <label for="end-date" style="font-size: 0.8rem; color: var(--text-secondary);">End Date</label>
                            <input type="date" id="end-date" name="end_date" required>
                        </div>
                    </div>
                </div>
                
                <div class="popup-buttons">
                    <button type="button" class="btn-cancel" id="cancel-popup">Cancel</button>
                    <button type="submit" class="btn-create" id="submit-trip">Create Trip</button>
                </div>
            </form>

        </div>
    </div>
</div>
