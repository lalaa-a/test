<style>
    /* ============================== */
    /*         CSS VARIABLES         */
    /* ============================== */
    :root {
        /* Card Sizing - Easy to adjust */
        --card-width: 270px;
        --card-min-height: 300px;
        --card-image-height: 150px;
        --card-padding: 18px;
        --card-gap: 20px;
        --card-border-radius: 8px;
        
        /* Colors */
        --primary-color: #006a71;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-light: #4b5563;
        --background-gray: #f9fafb;
        --card-background: white;
        --border-color: #e5e7eb;
        --shadow-color: rgba(0, 0, 0, 0.13);
        
        /* Typography */
        --font-primary: 'Geologica', sans-serif;
        --font-secondary: 'Roboto', sans-serif;
        
        /* Spacing */
        --section-spacing: 40px;
        --title-spacing: 30px;
    }

    /* ============================== */
    /*           CSS RESET           */
    /* ============================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        font-family: var(--font-primary);
        background-color: var(--background-gray);
        max-width: 100vw;
        box-sizing: border-box;
    }

    /* ============================== */
    /*        LAYOUT STRUCTURE       */
    /* ============================== */
    .content-wrapper {
        width: 100%;
        padding: 20px;
        position: relative;
        overflow: hidden;
        max-width: 100vw;
        box-sizing: border-box;
        margin: 0 auto;
    }

    /* Create Trip Button Styles */
    .create-trip-section {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: var(--section-spacing) 0;
        margin-bottom: var(--title-spacing);
    }

    .create-trip-btn {
        padding: 12px 50px;
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        border-radius: var(--card-border-radius);
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px var(--shadow-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .create-trip-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px var(--shadow-color);
    }

    .create-trip-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px var(--shadow-color);
    }

    .create-trip-btn i {
        font-size: 20px;
    }


    /* Popup Styles */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    /* Popup Form Styles */
    .popup-content {
        background: var(--card-background);
        padding: 30px;
        border-radius: var(--card-border-radius);
        box-shadow: 0 4px 6px var(--shadow-color);
        text-align: left;
        width: 450px;
        max-width: 90vw;
    }

    .popup-content h2 {
        margin-bottom: 20px;
        color: var(--text-primary);
        text-align: center;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 106, 113, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .date-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .popup-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .btn-cancel {
        padding: 10px 20px;
        background: var(--background-gray);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
        color: var(--text-primary);
    }

    .btn-create {
        padding: 10px 24px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-create:hover {
        background: #005a5f;
        transform: translateY(-1px);
    }

    .btn-create:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    /* ============================== */
    /*     TRIP MANAGEMENT STYLES    */
    /* ============================== */

    .trip-management {
        margin-top: var(--section-spacing);
    }

    .trip-section {
        margin-bottom: 40px;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border-color);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-primary);
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .section-count {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
    }

    /* Trip Cards Grid */
    .trip-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    /* Individual Trip Card */
    .trip-card {
        background: var(--card-background);
        border-radius: var(--card-border-radius);
        box-shadow: 0 4px 20px var(--shadow-color);
        padding: 24px;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
    }

    .trip-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px var(--shadow-color);
    }

    .trip-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .trip-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.3;
        flex: 1;
        margin-right: 12px;
    }

    .trip-menu-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        color: var(--text-secondary);
        transition: all 0.2s ease;
        position: relative;
    }

    .trip-menu-btn:hover {
        background: var(--background-gray);
        color: var(--text-primary);
    }

    .trip-menu-btn i {
        font-size: 1rem;
    }

    .trip-menu-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--card-background);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 120px;
        z-index: 100;
        display: none;
        border: 1px solid var(--border-color);
    }

    .trip-menu-dropdown.show {
        display: block;
    }

    .trip-menu-item {
        display: block;
        width: 100%;
        padding: 10px 16px;
        text-decoration: none;
        color: var(--text-primary);
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        transition: background 0.2s ease;
        font-size: 0.9rem;
    }

    .trip-menu-item:hover {
        background: var(--background-gray);
    }

    .trip-menu-item.edit {
        color: var(--primary-color);
    }

    .trip-menu-item.delete {
        color: #dc3545;
    }

    .trip-description {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .trip-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .trip-dates {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .trip-dates i {
        margin-right: 6px;
        color: var(--primary-color);
    }

    /* Status Badge */
    .trip-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Trip Card Body */
    .trip-card-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .trip-dates-section {
        flex: 1;
    }

    .trip-status-section {
        display: flex;
        justify-content: flex-end;
    }

    /* Status Colors with Enhanced Styling */
    .status-planning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border: 1px solid #ffeaa7;
        box-shadow: 0 2px 4px rgba(255, 234, 167, 0.3);
    }

    .status-pending {
        background: linear-gradient(135deg, #cce5ff 0%, #99d6ff 100%);
        color: #004085;
        border: 1px solid #99d6ff;
        box-shadow: 0 2px 4px rgba(153, 214, 255, 0.3);
    }

    .status-scheduled {
        background: linear-gradient(135deg, #d1ecf1 0%, #9eeaf9 100%);
        color: #0c5460;
        border: 1px solid #9eeaf9;
        box-shadow: 0 2px 4px rgba(158, 234, 249, 0.3);
    }

    .status-ongoing {
        background: linear-gradient(135deg, #d4edda 0%, #a8e6b1 100%);
        color: #155724;
        border: 1px solid #a8e6b1;
        box-shadow: 0 2px 4px rgba(168, 230, 177, 0.3);
        animation: pulse 2s infinite;
    }

    .status-completed {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #6c757d;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(222, 226, 230, 0.3);
    }

    /* Pulse animation for ongoing trips */
    @keyframes pulse {
        0% {
            box-shadow: 0 2px 4px rgba(168, 230, 177, 0.3);
        }
        50% {
            box-shadow: 0 2px 8px rgba(168, 230, 177, 0.6);
        }
        100% {
            box-shadow: 0 2px 4px rgba(168, 230, 177, 0.3);
        }
    }

    /* Empty State */
    .empty-section {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }

    .empty-section i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-section h4 {
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .empty-section p {
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .trip-cards-container {
            grid-template-columns: 1fr;
        }
        
        .trip-card {
            padding: 20px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }

</style>

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

<script>

    // Trip Management JavaScript
    let tripsData = [];
    let currentEditingTrip = null;

    loadUserTrips();

    // Load user's trips from backend
    async function loadUserTrips() {
        console.log('loadUserTrips function called');

        try {

            const response = await fetch('<?= URL_ROOT ?>/RegUser/getUserTrips');
            
            const data = await response.json();
            
            if (data.success) {
                
                tripsData = data.trips;
                renderTrips();

            } else {
                console.error('Failed to load trips:', data.message);
                alert('Failed to load trips: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading trips:', error);
            alert('Error loading trips: ' + error.message);
        }

    }

    // Render trips into sections
    function renderTrips() {

        // Clear existing content
        document.getElementById('ongoing-trips').innerHTML = '';
        document.getElementById('scheduled-trips').innerHTML = '';
        document.getElementById('completed-trips').innerHTML = '';
        
        // Group trips by status
        const groupedTrips = {
            ongoing: tripsData.filter(trip => {
                const status = trip.trip_status || trip.status;
                return status === 'ongoing';
            }),
            scheduled: tripsData.filter(trip => {
                const status = trip.trip_status || trip.status;
                return status === 'scheduled' || status === 'planning' || status === 'pending';
            }),
            completed: tripsData.filter(trip => {
                const status = trip.trip_status || trip.status;
                return status === 'completed';
            })
        };
        
        console.log('Grouped trips:', groupedTrips);
        
        // Render each section
        Object.keys(groupedTrips).forEach(status => {

            const container = document.getElementById(`${status}-trips`);
            const countElement = document.getElementById(`${status}-count`);
            
            // Update count
            countElement.textContent = groupedTrips[status].length;
            
            if (groupedTrips[status].length === 0) {
                // Show empty state
                container.innerHTML = `
                    <div class="empty-section">
                        <i class="fas fa-${getEmptyIcon(status)}"></i>
                        <h4>No ${status} trips</h4>
                        <p>${getEmptyMessage(status)}</p>
                    </div>
                `;
            } else {
                console.log(`Rendering ${groupedTrips[status].length} trips for ${status} section`);
                
                // Render trip cards
                groupedTrips[status].forEach((trip, index) => {
                    console.log(`Creating card ${index + 1} for trip:`, trip);
                    const cardElement = createTripCard(trip);
                    container.appendChild(cardElement);
                });
                
                // Final check of container contents
                console.log(`Final ${status} container HTML:`, container.innerHTML);
            }
        });
    }

    // Helper functions
    function getEmptyIcon(status) {
        const icons = {
            ongoing: 'plane',
            scheduled: 'calendar-plus',
            completed: 'check-circle'
        };
        return icons[status] || 'calendar';
    }

    function getEmptyMessage(status) {
        const messages = {
            ongoing: 'You don\'t have any trips currently in progress.',
            scheduled: 'No upcoming trips scheduled yet.',
            completed: 'No completed trips to show.'
        };
        return messages[status] || 'No trips found.';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Get status CSS class based on trip status
    function getStatusClass(status) {
        if (!status) return 'status-pending';
        const statusLower = status.toLowerCase();
        switch (statusLower) {
            case 'planning':
                return 'status-planning';
            case 'pending':
                return 'status-pending';
            case 'scheduled':
                return 'status-scheduled';
            case 'ongoing':
                return 'status-ongoing';
            case 'completed':
                return 'status-completed';
            default:
                return 'status-pending';
        }
    }

    // Toggle trip menu dropdown
    function toggleTripMenu(tripId, event) {
        event.stopPropagation();
        
        // Close all other menus
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            if (menu.id !== `menu-${tripId}`) {
                menu.classList.remove('show');
            }
        });
        
        // Toggle current menu
        const menu = document.getElementById(`menu-${tripId}`);
        menu.classList.toggle('show');
    }

    // Close menus when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    });

    // Create trip card using DOM methods (primary method)
    function createTripCard(trip) {
        console.log('Creating trip card DOM with data:', trip);
        
        const tripId = trip.trip_id || trip.id;
        const tripName = trip.trip_name || trip.trip_title;
        const tripStatus = trip.trip_status || trip.status;
        const startDate = trip.start_date;
        const endDate = trip.end_date;
        
        // Format dates
        const formatDate = (dateString) => {
            if (!dateString) return 'Date not set';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid date';
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        };
        
        // Create main card
        const card = document.createElement('div');
        card.className = 'trip-card';
        card.dataset.tripId = tripId;
        
        // Create header
        const header = document.createElement('div');
        header.className = 'trip-card-header';
        
        const title = document.createElement('h3');
        title.className = 'trip-title';
        title.textContent = tripName || 'Untitled Trip';
        
        const menu = document.createElement('div');
        menu.className = 'trip-menu';
        
        const menuBtn = document.createElement('button');
        menuBtn.className = 'trip-menu-btn';
        menuBtn.onclick = (e) => toggleTripMenu(tripId, e);
        
        const menuIcon = document.createElement('i');
        menuIcon.className = 'fas fa-ellipsis-v';
        menuBtn.appendChild(menuIcon);
        
        // Create dropdown menu
        const dropdown = document.createElement('div');
        dropdown.className = 'trip-menu-dropdown';
        dropdown.id = `menu-${tripId}`;
        
        const editBtn = document.createElement('button');
        editBtn.className = 'trip-menu-item edit';
        editBtn.onclick = () => editTrip(tripId);
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
        
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'trip-menu-item delete';
        deleteBtn.onclick = () => deleteTrip(tripId);
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
        
        dropdown.appendChild(editBtn);
        dropdown.appendChild(deleteBtn);
        
        menu.appendChild(menuBtn);
        menu.appendChild(dropdown);
        
        header.appendChild(title);
        header.appendChild(menu);
        
        // Create body
        const body = document.createElement('div');
        body.className = 'trip-card-body';
        
        const datesSection = document.createElement('div');
        datesSection.className = 'trip-dates-section';
        
        const dates = document.createElement('div');
        dates.className = 'trip-dates';
        
        const calendarIcon = document.createElement('i');
        calendarIcon.className = 'fas fa-calendar-alt';
        
        dates.appendChild(calendarIcon);
        dates.appendChild(document.createTextNode(` ${formatDate(startDate)} - ${formatDate(endDate)}`));
        
        datesSection.appendChild(dates);
        
        const statusSection = document.createElement('div');
        statusSection.className = 'trip-status-section';
        
        const status = document.createElement('span');
        status.className = `trip-status ${getStatusClass(tripStatus)}`;
        status.textContent = tripStatus || 'Unknown';
        
        statusSection.appendChild(status);
        
        body.appendChild(datesSection);
        body.appendChild(statusSection);
        
        card.appendChild(header);
        card.appendChild(body);
        
        console.log('Successfully created trip card');
        return card;
    }


    // JavaScript for Popup Functionality
    const createTripBtn = document.getElementById('create-trip-btn');
    const popup = document.getElementById('popup');
    const cancelBtn = document.getElementById('cancel-popup');
    const createTripForm = document.getElementById('create-trip-form');
    const submitBtn = document.getElementById('submit-trip');

    // Open popup
    createTripBtn.addEventListener('click', () => {
        popup.style.display = 'flex';
        
        // Set minimum date to today for both date inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start-date').setAttribute('min', today);
        document.getElementById('end-date').setAttribute('min', today);
        
        // Focus on first input when popup opens
        setTimeout(() => {
            document.getElementById('trip-title').focus();
        }, 100);

    });

    // Update end date minimum when start date changes
    document.getElementById('start-date').addEventListener('change', (e) => {
        const startDate = e.target.value;
        document.getElementById('end-date').setAttribute('min', startDate);
        
        // If end date is now before start date, clear it
        const endDate = document.getElementById('end-date').value;
        if (endDate && endDate < startDate) {
            document.getElementById('end-date').value = '';
        }
    });

    // Close popup (Cancel button)
    cancelBtn.addEventListener('click', () => {
        closePopup();
    });

    // Close popup (Click on overlay)
    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            closePopup();
        }
    });

// Update form submission to handle both create and edit
createTripForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(createTripForm);
    const tripData = {
        tripTitle: formData.get('trip_title'),
        description: formData.get('trip_description'),
        startDate: formData.get('start_date'),
        endDate: formData.get('end_date')
    };

    // Basic validation (keep existing validation)
    if (!tripData.tripTitle.trim()) {
        alert('Please enter a trip name');
        return;
    }

    if (!tripData.startDate) {
        alert('Please select a start date');
        return;
    }

    if (!tripData.endDate) {
        alert('Please select an end date');
        return;
    }

    if (new Date(tripData.startDate) > new Date(tripData.endDate)) {
        alert('End date cannot be before start date');
        return;
    }

    // Disable submit button during processing
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = currentEditingTrip ? 'Updating...' : 'Creating...';

    try {
        let url, method;
        
        if (currentEditingTrip) {
            // Update existing trip
            url = '<?= URL_ROOT ?>/RegUser/updateTrip';
            method = 'PUT';
            tripData.trip_id = currentEditingTrip.trip_id || currentEditingTrip.id;
        } else {
            // Create new trip
            url = '<?= URL_ROOT ?>/RegUser/createTrip';
            method = 'POST';
        }

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(tripData)
        });

        const result = await response.json();

        if (result.success) {
            alert(currentEditingTrip ? 'Trip updated successfully!' : 'Trip created successfully!');
            closePopup();
            
            // Reload trips data
            await loadUserTrips();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        currentEditingTrip = null;
    }
});


    // Close popup function
    function closePopup() {
        popup.style.display = 'none';
        createTripForm.reset();
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Trip';
    }

    // Close popup on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && popup.style.display === 'flex') {
            closePopup();
        }
    });

    // Edit trip function
    function editTrip(tripId) {
        const trip = tripsData.find(t => (t.trip_id || t.id) === tripId);
        if (!trip) {
            alert('Trip not found');
            return;
        }
        
        currentEditingTrip = trip;
        
        // Populate form with existing data using correct property names
        document.getElementById('trip-title').value = trip.trip_name || trip.trip_title || '';
        document.getElementById('trip-description').value = trip.trip_description || trip.description || '';
        document.getElementById('start-date').value = trip.start_date || '';
        document.getElementById('end-date').value = trip.end_date || '';
        
        // Update popup title and button text
        document.querySelector('.popup-content h2').textContent = 'Edit Trip';
        document.getElementById('submit-trip').textContent = 'Update Trip';
        
        // Show popup
        popup.style.display = 'flex';
        
        // Close any open menus
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }

    // Delete trip function
    async function deleteTrip(tripId) {
        if (!confirm('Are you sure you want to delete this trip?')) {
            return;
        }
        
        try {
            const response = await fetch(`<?= URL_ROOT ?>/RegUser/deleteTrip`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ trip_id: tripId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Trip deleted successfully!');
                await loadUserTrips(); // Reload trips
            } else {
                alert('Error deleting trip: ' + result.message);
            }
        } catch (error) {
            console.error('Error deleting trip:', error);
            alert('An error occurred while deleting the trip.');
        }
        
        // Close any open menus
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }

</script>