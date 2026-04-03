(function() {
    // Clean up any existing instance
    if (window.DriverToursManager) {
        console.log('DriverToursManager already exists, cleaning up...');
        if (window.driverToursManager) {
            delete window.driverToursManager;
        }
        delete window.DriverToursManager;
    }

    // Driver Tours Manager
    class DriverToursManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.currentUser = null;
            this.currentTripId = null;
            this.currentTripPin = null;

            this.tours = {
                ongoing: [],
                upcoming: [],
                completed: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadTours();
        }

        bindEvents() {
            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToTab(targetId);
                });
            });

            // Search inputs
            const ongoingSearchInput = document.getElementById('ongoingSearchInput');
            if (ongoingSearchInput) {
                ongoingSearchInput.addEventListener('input', (e) => {
                    this.filterTours('ongoing', e.target.value);
                });
            }

            const upcomingSearchInput = document.getElementById('upcomingSearchInput');
            if (upcomingSearchInput) {
                upcomingSearchInput.addEventListener('input', (e) => {
                    this.filterTours('upcoming', e.target.value);
                });
            }

            const completedSearchInput = document.getElementById('completedSearchInput');
            if (completedSearchInput) {
                completedSearchInput.addEventListener('input', (e) => {
                    this.filterTours('completed', e.target.value);
                });
            }

            // Modal close buttons
            const tourDetailsModalClose = document.querySelector('#tourDetailsModal .modal-close');
            if (tourDetailsModalClose) {
                tourDetailsModalClose.addEventListener('click', () => this.closeModal(document.getElementById('tourDetailsModal')));
            }

            const tourDetailsModalFooterClose = document.getElementById('modalCloseBtn');
            if (tourDetailsModalFooterClose) {
                tourDetailsModalFooterClose.addEventListener('click', () => this.closeModal(document.getElementById('tourDetailsModal')));
            }

            // Start trip modal
            const startTripModalClose = document.querySelector('#startTripModal .modal-close');
            if (startTripModalClose) {
                startTripModalClose.addEventListener('click', () => this.closeModal(document.getElementById('startTripModal')));
            }

            const startTripCancelBtn = document.getElementById('startTripCancelBtn');
            if (startTripCancelBtn) {
                startTripCancelBtn.addEventListener('click', () => this.closeModal(document.getElementById('startTripModal')));
            }

            const startTripConfirmBtn = document.getElementById('startTripConfirmBtn');
            if (startTripConfirmBtn) {
                startTripConfirmBtn.addEventListener('click', () => this.startTrip());
            }

            // Event completion modal
            const eventCompletionModalClose = document.querySelector('#eventCompletionModal .modal-close');
            if (eventCompletionModalClose) {
                eventCompletionModalClose.addEventListener('click', () => this.closeModal(document.getElementById('eventCompletionModal')));
            }

            const eventCompletionCloseBtn = document.getElementById('eventCompletionCloseBtn');
            if (eventCompletionCloseBtn) {
                eventCompletionCloseBtn.addEventListener('click', () => this.closeModal(document.getElementById('eventCompletionModal')));
            }

            const completeTripBtn = document.getElementById('completeTripBtn');
            if (completeTripBtn) {
                completeTripBtn.addEventListener('click', () => this.completeTrip());
            }

            // PIN input validation
            const tripPinInput = document.getElementById('tripPin');
            if (tripPinInput) {
                tripPinInput.addEventListener('input', (e) => {
                    // Only allow numbers
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                });
            }
        }

        async loadTours() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getDriverTours`);
                const data = await response.json();

                if (data.success) {
                    this.tours = data.tours;
                    this.updateStats();
                    this.renderTours();
                } else {
                    console.error('Failed to load tours:', data.message);
                }
            } catch (error) {
                console.error('Error loading tours:', error);
            }
        }

        updateStats() {
            const ongoingCount = this.tours.ongoing.length;
            const upcomingCount = this.tours.upcoming.length;
            const completedCount = this.tours.completed.length;

            document.getElementById('ongoingToursCount').textContent = ongoingCount;
            document.getElementById('upcomingToursCount').textContent = upcomingCount;
            document.getElementById('completedToursCount').textContent = completedCount;
        }

        renderTours() {
            this.renderOngoingTours();
            this.renderUpcomingTours();
            this.renderCompletedTours();
        }

        renderOngoingTours() {
            const container = document.getElementById('ongoingToursGrid');
            const tours = this.tours.ongoing;

            if (tours.length === 0) {
                container.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-play-circle"></i>
                            <p>No ongoing tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = tours.map(tour => this.createOngoingTourRow(tour)).join('');
        }

        renderUpcomingTours() {
            const container = document.getElementById('upcomingToursGrid');
            const tours = this.tours.upcoming;

            if (tours.length === 0) {
                container.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No upcoming tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = tours.map(tour => this.createUpcomingTourRow(tour)).join('');
        }

        renderCompletedTours() {
            const container = document.getElementById('completedToursGrid');
            const tours = this.tours.completed;

            if (tours.length === 0) {
                container.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = tours.map(tour => this.createCompletedTourRow(tour)).join('');
        }

        createOngoingTourRow(tour) {
            const statusBadge = tour.doneStatus ?
                '<span class="status-badge ongoing">Completed</span>' :
                '<span class="status-badge not-started">In Progress</span>';

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${tour.rqUserProfilePhoto || '/public/img/signup/profile.png'}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName)}</div>
                                <div class="traveller-rating">
                                    <i class="fas fa-star"></i>
                                    <span>${parseFloat(tour.driverRating).toFixed(1)}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">Trip #${tour.tripId}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${tour.totalKm ? `${tour.totalKm} km` : 'Distance TBD'}</div>
                        </div>
                    </td>
                    <td>
                        <div class="vehicle-info">
                            <img src="${this.UP_ROOT + tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
                            <div class="vehicle-details">
                                <div class="vehicle-model">${tour.vehicleModel}</div>
                                <div class="vehicle-capacity">${tour.vehicleCapacity} seats</div>
                            </div>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="window.driverToursManager.viewTourDetails(${tour.acceptId})">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                            ${!tour.doneStatus ? `
                                <button class="btn btn-warning" onclick="window.driverToursManager.showEventCompletion(${tour.acceptId})">
                                    <i class="fas fa-tasks"></i>
                                    Mark Events
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }

        createUpcomingTourRow(tour) {
            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${tour.rqUserProfilePhoto || '/public/img/signup/profile.png'}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName)}</div>
                                <div class="traveller-rating">
                                    <i class="fas fa-star"></i>
                                    <span>${parseFloat(tour.driverRating).toFixed(1)}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">Trip #${tour.tripId}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${tour.totalKm ? `${tour.totalKm} km` : 'Distance TBD'}</div>
                        </div>
                    </td>
                    <td>
                        <div class="vehicle-info">
                            <img src="${this.UP_ROOT + tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
                            <div class="vehicle-details">
                                <div class="vehicle-model">${tour.vehicleModel}</div>
                                <div class="vehicle-capacity">${tour.vehicleCapacity} seats</div>
                            </div>
                        </div>
                    </td>
                    <td>${this.formatDate(tour.startDate)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="window.driverToursManager.viewTourDetails(${tour.acceptId})">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                            <button class="btn btn-success" onclick="window.driverToursManager.startTripPrompt(${tour.acceptId})">
                                <i class="fas fa-play"></i>
                                Start Trip
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        createCompletedTourRow(tour) {
            const paymentStatus = {
                'pending': '<span class="status-badge" style="background: #fef3c7; color: #92400e;">Pending</span>',
                'paid': '<span class="status-badge" style="background: #dcfce7; color: #166534;">Paid</span>',
                'failed': '<span class="status-badge" style="background: #fee2e2; color: #dc2626;">Failed</span>',
                'refunded': '<span class="status-badge" style="background: #e0e7ff; color: #3730a3;">Refunded</span>'
            }[tour.paymentStatus] || '<span class="status-badge">Unknown</span>';

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${tour.rqUserProfilePhoto || '/public/img/signup/profile.png'}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName)}</div>
                                <div class="traveller-rating">
                                    <i class="fas fa-star"></i>
                                    <span>${parseFloat(tour.driverRating).toFixed(1)}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">Trip #${tour.tripId}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${tour.totalKm ? `${tour.totalKm} km` : 'Distance TBD'}</div>
                        </div>
                    </td>
                    <td>
                        <div class="vehicle-info">
                            <img src="${this.UP_ROOT + tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
                            <div class="vehicle-details">
                                <div class="vehicle-model">${tour.vehicleModel}</div>
                                <div class="vehicle-capacity">${tour.vehicleCapacity} seats</div>
                            </div>
                        </div>
                    </td>
                    <td>${tour.completedAt ? this.formatDate(tour.completedAt.split(' ')[0]) : 'N/A'}</td>
                    <td>${paymentStatus}</td>
                </tr>
            `;
        }

        switchToTab(targetId) {
            // Hide all sections
            document.querySelectorAll('.tours-section').forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            const selectedSection = document.getElementById(targetId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }

            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${targetId}`) {
                    link.classList.add('active');
                }
            });

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        filterTours(type, searchTerm) {
            const container = document.getElementById(`${type}ToursGrid`);
            const rows = container.querySelectorAll('tr:not(.no-tours)');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matches = text.includes(searchTerm.toLowerCase());
                row.style.display = matches ? '' : 'none';
            });
        }

        async viewTourDetails(acceptId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${acceptId}`);
                const data = await response.json();

                if (data.success) {
                    this.showTourDetailsModal(data.tour);
                } else {
                    alert('Failed to load tour details: ' + data.message);
                }
            } catch (error) {
                console.error('Error loading tour details:', error);
                alert('Error loading tour details');
            }
        }

        showTourDetailsModal(tour) {
            const modal = document.getElementById('tourDetailsModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');

            modalTitle.textContent = `Trip #${tour.tripId} Details`;

            modalBody.innerHTML = `
                <div class="tour-details">
                    <div class="detail-section">
                        <h4>Traveller Information</h4>
                        <div class="detail-row">
                            <span class="label">Name:</span>
                            <span class="value">${this.escapeHtml(tour.rqUserName)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Rating:</span>
                            <span class="value">
                                <i class="fas fa-star"></i> ${parseFloat(tour.driverRating).toFixed(1)}
                            </span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4>Trip Information</h4>
                        <div class="detail-row">
                            <span class="label">Duration:</span>
                            <span class="value">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Distance:</span>
                            <span class="value">${tour.totalKm ? `${tour.totalKm} km` : 'TBD'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Amount:</span>
                            <span class="value">$${parseFloat(tour.totalAmount).toFixed(2)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Status:</span>
                            <span class="value">${tour.doneStatus ? 'Completed' : 'In Progress'}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4>Vehicle Information</h4>
                        <div class="detail-row">
                            <span class="label">Model:</span>
                            <span class="value">${tour.vehicleModel}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Capacity:</span>
                            <span class="value">${tour.vehicleCapacity} passengers</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Child Seats:</span>
                            <span class="value">${tour.childSeats}</span>
                        </div>
                    </div>
                </div>
            `;

            modal.style.display = 'block';
        }

        startTripPrompt(acceptId) {
            this.currentTripId = acceptId;
            const modal = document.getElementById('startTripModal');
            const pinInput = document.getElementById('tripPin');

            pinInput.value = '';
            modal.style.display = 'block';
            pinInput.focus();
        }

        async startTrip() {
            const pinInput = document.getElementById('tripPin');
            const pin = pinInput.value.trim();

            if (!pin || pin.length !== 6) {
                alert('Please enter a valid 6-digit PIN');
                return;
            }

            try {
                // First get tour details to get the tripId
                const tourResponse = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${this.currentTripId}`);
                const tourData = await tourResponse.json();

                if (!tourData.success) {
                    alert('Failed to load tour details: ' + tourData.message);
                    return;
                }

                const tripId = tourData.tour.tripId;

                const response = await fetch(`${this.URL_ROOT}/Driver/startTrip`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tripId: tripId,
                        pin: pin
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Trip started successfully!');
                    this.closeModal(document.getElementById('startTripModal'));
                    this.loadTours(); // Refresh the tours list
                } else {
                    alert('Failed to start trip: ' + data.message);
                }
            } catch (error) {
                console.error('Error starting trip:', error);
                alert('Error starting trip');
            }
        }

        async showEventCompletion(acceptId) {
            this.currentAcceptId = acceptId;

            try {
                // First get tour details to get the tripId
                const tourResponse = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${acceptId}`);
                const tourData = await tourResponse.json();

                if (!tourData.success) {
                    alert('Failed to load tour details: ' + tourData.message);
                    return;
                }

                const tripId = tourData.tour.tripId;
                this.currentTripId = tripId;

                // Now get trip events
                const eventsResponse = await fetch(`${this.URL_ROOT}/Driver/getTripEvents?tripId=${tripId}`);
                const eventsData = await eventsResponse.json();

                if (eventsData.success) {
                    this.showEventCompletionModal(eventsData.events, tourData.tour);
                } else {
                    alert('Failed to load trip events: ' + eventsData.message);
                }
            } catch (error) {
                console.error('Error loading trip events:', error);
                alert('Error loading trip events');
            }
        }

        showEventCompletionModal(events) {
            const modal = document.getElementById('eventCompletionModal');
            const eventsList = document.getElementById('eventsList');
            const completeTripBtn = document.getElementById('completeTripBtn');

            eventsList.innerHTML = events.map(event => `
                <div class="event-item">
                    <div class="event-details">
                        <div class="event-title">${this.escapeHtml(event.locationName || event.eventType)}</div>
                        <div class="event-time">${this.formatTime(event.startTime)} - ${this.formatTime(event.endTime)}</div>
                    </div>
                    <div class="event-status">
                        <input type="checkbox"
                               class="event-checkbox"
                               data-event-id="${event.eventId}"
                               ${event.dDone ? 'checked disabled' : ''}
                               onchange="window.driverToursManager.markEventComplete(${event.eventId}, this.checked)">
                        <span class="${event.dDone ? 'event-completed' : ''}">
                            ${event.dDone ? 'Completed' : 'Mark Complete'}
                        </span>
                    </div>
                </div>
            `).join('');

            // Show complete trip button if all events are completed
            const allCompleted = events.every(event => event.dDone);
            completeTripBtn.style.display = allCompleted ? 'block' : 'none';

            modal.style.display = 'block';
        }

        async markEventComplete(eventId, completed) {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/markEventComplete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        eventId: eventId,
                        completed: completed
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Refresh the events list
                    this.showEventCompletion(this.currentTripId);
                } else {
                    alert('Failed to update event: ' + data.message);
                }
            } catch (error) {
                console.error('Error updating event:', error);
                alert('Error updating event');
            }
        }

        async completeTrip() {
            if (!confirm('Are you sure you want to complete this trip? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/completeTrip`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tripId: this.currentTripId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Trip completed successfully!');
                    this.closeModal(document.getElementById('eventCompletionModal'));
                    this.loadTours(); // Refresh the tours list
                } else {
                    alert('Failed to complete trip: ' + data.message);
                }
            } catch (error) {
                console.error('Error completing trip:', error);
                alert('Error completing trip');
            }
        }

        closeModal(modal) {
            modal.style.display = 'none';
            this.currentTripId = null;
            this.currentTripPin = null;
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        formatTime(timeString) {
            if (!timeString) return 'N/A';
            const [hours, minutes] = timeString.split(':');
            const date = new Date();
            date.setHours(hours, minutes);
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialize the manager
    window.DriverToursManager = DriverToursManager;
    window.driverToursManager = new DriverToursManager();

})();