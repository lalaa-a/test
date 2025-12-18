// Trip Management JavaScript
    let tripsData = [];
    let currentEditingTrip = null;
    const URL_ROOT = 'http://localhost/test'

    loadUserTrips();

    // Load user's trips from backend
    async function loadUserTrips() {
        console.log('loadUserTrips function called');

        try {

            const response = await fetch(URL_ROOT+'/RegUser/getUserTrips');
            
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
        document.getElementById('pending-trips').innerHTML = '';
        document.getElementById('scheduled-trips').innerHTML = '';
        document.getElementById('completed-trips').innerHTML = '';
        
        // Group trips by status
        const groupedTrips = {
            ongoing: tripsData.filter(trip => trip.status === 'ongoing'),
            pending: tripsData.filter(trip => trip.status === 'pending'),
            scheduled: tripsData.filter(trip => trip.status === 'scheduled'),
            completed: tripsData.filter(trip => trip.status === 'completed')
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
                // Render trip cards
                groupedTrips[status].forEach(trip => {
                    container.appendChild(createTripCard(trip));
                });
            }
        });
    }

    // Helper functions
    function getEmptyIcon(status) {
        const icons = {
            ongoing: 'plane',
            pending: 'clock',
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
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

    // Make functions globally accessible
    window.toggleTripMenu = toggleTripMenu;

    // Edit trip function
    function editTrip(tripId) {
        console.log('Editing trip:', tripId);
        
        // Find the trip data
        const trip = tripsData.find(t => t.tripId == tripId);
        
        if (!trip) {
            alert('Trip not found');
            return;
        }
        
        // Set current editing trip
        currentEditingTrip = trip;
        
        // Populate form with existing data
        document.getElementById('trip-title').value = trip.tripTitle || '';
        document.getElementById('trip-description').value = trip.description || '';
        document.getElementById('start-date').value = trip.startDate || '';
        document.getElementById('end-date').value = trip.endDate || '';
        
        // Update submit button text
        const submitBtn = document.getElementById('submit-trip');
        submitBtn.textContent = 'Update Trip';
        
        // Open popup
        popup.style.display = 'flex';
        
        // Focus on first input
        setTimeout(() => {
            document.getElementById('trip-title').focus();
        }, 100);
        
        // Close the dropdown menu
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }

    // Delete trip function
    function deleteTrip(tripId) {

        console.log('Deleting trip:', tripId);
        
        if (!confirm('Are you sure you want to delete this trip? This action cannot be undone.')) {
            return;
        }
        
        // Make delete request
        fetch(URL_ROOT + '/RegUser/deleteTrip', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ tripId: tripId })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Trip deleted successfully!');
                loadUserTrips(); // Reload trips
            } else {
                alert('Error deleting trip: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the trip.');
        });
        
        // Close the dropdown menu
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }

    // Make all functions globally accessible
    window.editTrip = editTrip;
    window.deleteTrip = deleteTrip;

    // Close menus when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.trip-menu-dropdown.show').forEach(menu => {
            menu.classList.remove('show');
        });
    });

    // Create trip card element
    function createTripCard(trip) {
        const card = document.createElement('div');
        card.className = 'trip-card';
        card.dataset.tripId = trip.tripId;
        
        const startDate = new Date(trip.startDate).toLocaleDateString();
        const endDate = new Date(trip.endDate).toLocaleDateString();

        card.innerHTML = `
            <div class="trip-card-header">
                <h4 class="trip-title">${escapeHtml(trip.tripTitle)}</h4>
                <div class="trip-menu-container">
                    <button class="trip-menu-btn" onclick="toggleTripMenu(${trip.tripId}, event)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="trip-menu-dropdown" id="menu-${trip.tripId}">
                        <button class="trip-menu-item edit" onclick="editTrip(${trip.tripId})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="trip-menu-item delete" onclick="deleteTrip(${trip.tripId})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            
            ${trip.description ? `<p class="trip-description">${escapeHtml(trip.description)}</p>` : ''}
            
            <div class="trip-details">
                <div class="trip-dates">
                    <i class="fas fa-calendar-alt"></i>
                    ${startDate} - ${endDate}
                </div>
                <span class="trip-status status-${trip.status.toLowerCase()}">
                    ${trip.status.charAt(0).toUpperCase() + trip.status.slice(1)}
                </span>
            </div>
        `;

        card.addEventListener('click',(e)=>{
            //Dont naviagate if clcking on menu buttons(3 dots)
            if(e.target.closest('.trip-menu-container')){
                return;
            }
            naviagateToTripEventList(trip.tripId); //<--navigate to a specific 
        })
        
        return card;
    }

    //Navigate to trip event list of a particular created trip
    function naviagateToTripEventList(tripId){
        window.location.href = `${URL_ROOT}/RegUser/tripEventList`; ///${tripId}
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
        tripTitle: formData.get('trip_title').trim(),
        description: formData.get('trip_description').trim(),
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

    if(currentEditingTrip){
        console.log(tripData);
    }

    // Disable submit button during processing
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = currentEditingTrip ? 'Updating...' : 'Creating...';

    try {
        let url, method;
        
        if (currentEditingTrip) {
            // Update existing trip
            url = URL_ROOT+'/RegUser/updatetrip';
            method = 'PUT';
            tripData.tripId = currentEditingTrip.tripId;
        } else {
            // Create new trip
            url = URL_ROOT+'/RegUser/createTrip';
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

        console.log("result of "+result);

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
