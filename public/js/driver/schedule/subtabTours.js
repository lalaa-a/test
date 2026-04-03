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
            this.currentAcceptId = null;
            this.currentTripId = null;

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

            // Modal overlays
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal);
                    }
                });
            });

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal(document.getElementById('tourDetailsModal'));
                    this.closeModal(document.getElementById('startTripModal'));
                    this.closeModal(document.getElementById('eventCompletionModal'));
                }
            });
        }

        async loadTours() {
            try {
                console.log('Loading driver tours...');
                const response = await fetch(`${this.URL_ROOT}/Driver/getDriverTours`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.tours = data.tours;
                    this.updateStats();
                    this.renderTours();
                    this.switchToTab('ongoing-tours-section');
                } else {
                    console.error('API error:', data.message);
                    this.showNotification('Error loading tours', 'error');
                }
            } catch (error) {
                console.error('Error loading tours:', error);
                this.showNotification('Error loading tours', 'error');
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

        renderOngoingTours(customTours = null) {
            const tours = customTours || this.tours.ongoing;
            const tbody = document.getElementById('ongoingToursGrid');

            if (!tbody) return;

            if (tours.length === 0) {
                tbody.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-play-circle"></i>
                            <p>No ongoing tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = tours.map(tour => this.createOngoingTourRow(tour)).join('');
        }

        renderUpcomingTours(customTours = null) {
            const tours = customTours || this.tours.upcoming;
            const tbody = document.getElementById('upcomingToursGrid');

            if (!tbody) return;

            if (tours.length === 0) {
                tbody.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No upcoming tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = tours.map(tour => this.createUpcomingTourRow(tour)).join('');
        }

        renderCompletedTours(customTours = null) {
            const tours = customTours || this.tours.completed;
            const tbody = document.getElementById('completedToursGrid');

            if (!tbody) return;

            if (tours.length === 0) {
                tbody.innerHTML = `
                    <tr class="no-tours">
                        <td colspan="5">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed tours</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = tours.map(tour => this.createCompletedTourRow(tour)).join('');
        }

        createOngoingTourRow(tour) {
            const statusBadge = tour.doneStatus ?
                '<span class="status-badge completed">Completed</span>' :
                '<span class="status-badge ongoing">In Progress</span>';

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
                            <img src="${this.UP_ROOT}${tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
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
                            <img src="${this.UP_ROOT}${tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
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
                            <img src="${this.UP_ROOT}${tour.vehiclePhoto}" alt="Vehicle" class="vehicle-avatar">
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

        filterTours(section, searchTerm = '') {
            let filteredTours = [...this.tours[section]];

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredTours = filteredTours.filter(tour =>
                    tour.rqUserName.toLowerCase().includes(term) ||
                    tour.tripTitle.toLowerCase().includes(term) ||
                    tour.vehicleModel.toLowerCase().includes(term) ||
                    tour.totalAmount.toString().includes(term)
                );
            }

            if (section === 'ongoing') {
                this.renderOngoingTours(filteredTours);
            } else if (section === 'upcoming') {
                this.renderUpcomingTours(filteredTours);
            } else if (section === 'completed') {
                this.renderCompletedTours(filteredTours);
            }
        }

        switchToTab(targetId) {
            // Hide all sections
            document.querySelectorAll('.tours-section').forEach(section => {
                section.style.display = 'none';
            });

            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Show target section
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }

            // Add active class to corresponding nav link
            const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        async viewTourDetails(acceptId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${acceptId}`);
                const data = await response.json();

                if (data.success) {
                    this.showTourDetailsModal(data.tour);
                } else {
                    this.showNotification('Failed to load tour details: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error loading tour details:', error);
                this.showNotification('Error loading tour details', 'error');
            }
        }

        showTourDetailsModal(tour) {
            this.currentTour = tour;
            this.currentAcceptId = tour.acceptId;

            const modal = document.getElementById('tourDetailsModal');
            const modalTitle = document.getElementById('modalTitle');

            modalTitle.textContent = `Trip #${tour.tripId} Details`;

            // Show details tab by default
            this.showTourDetails(tour);
            this.switchModalTab('details');

            modal.style.display = 'block';
        }

        showTourDetails(tour) {
            const content = `
                <div class="request-details-grid">
                    <div class="detail-group">
                        <label class="detail-label">Traveller Name</label>
                        <div class="detail-value">${this.escapeHtml(tour.rqUserName)}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Traveller Rating</label>
                        <div class="detail-value">
                            <i class="fas fa-star" style="color: #fbbf24;"></i> ${parseFloat(tour.driverRating).toFixed(1)}
                        </div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Model</label>
                        <div class="detail-value">${tour.vehicleModel}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Type</label>
                        <div class="detail-value">${tour.vehicleType}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Capacity</label>
                        <div class="detail-value">${tour.vehicleCapacity} Seats</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Child Seats</label>
                        <div class="detail-value">${tour.childSeats || 0}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Duration</label>
                        <div class="detail-value">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Charge Type</label>
                        <div class="detail-value">${tour.chargeType === 'perDay' ? 'Per Day' : 'Per KM'}</div>
                    </div>
                    ${tour.totalKm ? `
                        <div class="detail-group">
                            <label class="detail-label">Total KM</label>
                            <div class="detail-value">${tour.totalKm} km</div>
                        </div>
                    ` : ''}
                    <div class="detail-group">
                        <label class="detail-label">Total Amount</label>
                        <div class="detail-value highlighted">${parseFloat(tour.totalAmount).toFixed(2)} LKR</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Status</label>
                        <div class="detail-value">${tour.doneStatus ? 'Completed' : (tour.tripStatus === 'ongoing' ? 'Ongoing' : 'Scheduled')}</div>
                    </div>
                </div>

                <div class="request-summary">
                    <div class="summary-row">
                        <span>Accepted Date:</span>
                        <span>${this.formatDate(tour.createdAt)}</span>
                    </div>
                    <div class="summary-row">
                        <span>Trip Status:</span>
                        <span style="text-transform: capitalize;">${tour.doneStatus ? 'Completed' : (tour.tripStatus === 'ongoing' ? 'Ongoing' : 'Scheduled')}</span>
                    </div>
                    ${tour.completedAt ? `
                        <div class="summary-row">
                            <span>Completed Date:</span>
                            <span>${this.formatDate(tour.completedAt)}</span>
                        </div>
                    ` : ''}
                    <div class="summary-row">
                        <span>Payment Status:</span>
                        <span style="text-transform: capitalize;">${tour.paymentStatus}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span>${parseFloat(tour.totalAmount).toFixed(2)} LKR</span>
                    </div>
                </div>

                ${tour.vehiclePhoto ? `
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Photo</label>
                        <img src="${this.UP_ROOT}${tour.vehiclePhoto}" alt="Vehicle" style="max-width: 100%; border-radius: 6px; cursor: pointer; max-height: 300px;" onclick="window.driverToursManager.viewPhoto('${this.UP_ROOT}${tour.vehiclePhoto}')">
                    </div>
                ` : ''}
            `;

            const detailsContent = document.getElementById('tourDetailsContent');
            if (detailsContent) {
                detailsContent.innerHTML = content;
            }
        }

        switchModalTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`[onclick="window.driverToursManager.switchModalTab('${tabName}')"]`);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }

            // Update tab content
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            const activeTab = document.getElementById(`${tabName}-tab`);
            if (activeTab) {
                activeTab.classList.add('active');
            }

            // Load itinerary if switching to itinerary tab
            if (tabName === 'itinerary') {
                const contentDiv = document.getElementById('itineraryContent');
                if (contentDiv) {
                    contentDiv.innerHTML = '<p>Loading itinerary...</p>';
                }
                this.loadItinerary();
            }
        }

        async loadItinerary() {
            if (!this.currentTour || !this.currentTour.tripId) {
                document.getElementById('itineraryContent').innerHTML = '<p class="no-data">No itinerary available</p>';
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getTripItinerary/${this.currentTour.tripId}`);
                const data = await response.json();

                if (data.success) {
                    this.renderItinerary(data.itinerary);
                } else {
                    document.getElementById('itineraryContent').innerHTML = '<p class="no-data">Failed to load itinerary</p>';
                }
            } catch (error) {
                console.error('Error loading itinerary:', error);
                document.getElementById('itineraryContent').innerHTML = '<p class="no-data">Error loading itinerary</p>';
            }
        }

        renderItinerary(itinerary) {
            const contentDiv = document.getElementById('itineraryContent');
            if (!itinerary || !itinerary.events) {
                if (contentDiv) {
                    contentDiv.innerHTML = '<p class="no-data">No itinerary available</p>';
                }
                return;
            }

            // Group events by date
            const eventsByDate = {};
            itinerary.events.forEach(event => {
                const date = event.eventDate;
                if (!eventsByDate[date]) {
                    eventsByDate[date] = [];
                }
                eventsByDate[date].push(event);
            });

            let html = '<div class="itinerary-summary">';

            // Sort dates
            const sortedDates = Object.keys(eventsByDate).sort();

            sortedDates.forEach((date, index) => {
                const events = eventsByDate[date];
                const dateObj = new Date(date);
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                html += `
                    <div class="summary-day-section">
                        <div class="summary-day-header" onclick="window.driverToursManager.toggleDaySummary(${index})">
                            <div class="summary-day-title">
                                <i class="fas fa-calendar-day"></i>
                                <span>Day ${index + 1}: ${dayName}, ${formattedDate}</span>
                            </div>
                            <div>
                                <span class="summary-day-count">${events.length} event${events.length !== 1 ? 's' : ''}</span>
                                <i class="fas fa-chevron-down summary-day-toggle" id="toggle-${index}"></i>
                            </div>
                        </div>
                        <div class="summary-day-events" id="day-events-${index}">
                            ${this.renderDayEvents(events)}
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            if (contentDiv) {
                contentDiv.innerHTML = html;
            } else {
                console.error('itineraryContent div not found');
            }
        }

        renderDayEvents(events) {
            return events.map(event => {
                const startTime = this.formatTimeToAMPM(event.startTime);
                const endTime = this.formatTimeToAMPM(event.endTime);

                const typeIcon = event.eventType === 'travelSpot' ? 'fa-map-marked-alt' : 'fa-map-marker-alt';
                const statusBadge = {
                    'start': 'Start Point',
                    'intermediate': 'Intermediate',
                    'end': 'End Point'
                }[event.eventStatus] || event.eventStatus;

                const name = event.eventType === 'travelSpot' ? 
                    (event.spotName || 'Travel Spot') : 
                    (event.locationName || 'Location');

                return `
                    <div class="summary-event-item">
                        <div class="summary-event-time">
                            ${startTime}<br>to<br>${endTime}
                        </div>
                        <div class="summary-event-details">
                            <div class="summary-event-title">${this.escapeHtml(name)}</div>
                            <div class="summary-event-meta">
                                <span><i class="fas ${typeIcon}"></i> ${event.eventType === 'travelSpot' ? 'Travel Spot' : 'Location'}</span>
                                <span><i class="fas fa-flag"></i> ${statusBadge}</span>
                            </div>
                            ${event.spotDescription ? `<p class="event-description">${this.escapeHtml(event.spotDescription)}</p>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        toggleDaySummary(dayIndex) {
            const eventsDiv = document.getElementById(`day-events-${dayIndex}`);
            const toggleIcon = document.getElementById(`toggle-${dayIndex}`);

            if (eventsDiv.classList.contains('collapsed')) {
                eventsDiv.classList.remove('collapsed');
                toggleIcon.classList.remove('collapsed');
            } else {
                eventsDiv.classList.add('collapsed');
                toggleIcon.classList.add('collapsed');
            }
        }

        formatTimeToAMPM(timeString) {
            if (!timeString) return 'N/A';
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        viewPhoto(photoUrl) {
            // Create photo viewer modal if it doesn't exist
            let photoViewerModal = document.getElementById('photoViewerModal');
            if (!photoViewerModal) {
                photoViewerModal = document.createElement('div');
                photoViewerModal.id = 'photoViewerModal';
                photoViewerModal.className = 'photo-viewer-modal';
                photoViewerModal.innerHTML = `
                    <div class="photo-viewer-content">
                        <button class="photo-viewer-close">
                            <i class="fas fa-times"></i>
                        </button>
                        <img id="photoViewerImage" src="" alt="Photo">
                    </div>
                `;
                document.body.appendChild(photoViewerModal);

                // Add event listeners
                photoViewerModal.addEventListener('click', (e) => {
                    if (e.target === photoViewerModal) {
                        this.closePhotoViewer();
                    }
                });

                const closeBtn = photoViewerModal.querySelector('.photo-viewer-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => this.closePhotoViewer());
                }

                // ESC key to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closePhotoViewer();
                    }
                });
            }

            const photoViewerImage = document.getElementById('photoViewerImage');
            if (photoViewerImage) {
                photoViewerImage.src = photoUrl;
            }

            photoViewerModal.style.display = 'block';
        }

        closePhotoViewer() {
            const photoViewerModal = document.getElementById('photoViewerModal');
            if (photoViewerModal) {
                photoViewerModal.style.display = 'none';
            }
        }

        startTripPrompt(acceptId) {
            this.currentAcceptId = acceptId;
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
                this.showNotification('Please enter a valid 6-digit PIN', 'error');
                return;
            }

            try {
                // First get tour details to get the tripId
                const tourResponse = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${this.currentAcceptId}`);
                const tourData = await tourResponse.json();

                if (!tourData.success) {
                    this.showNotification('Failed to load tour details: ' + tourData.message, 'error');
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
                    this.showNotification('Trip started successfully!', 'success');
                    this.closeModal(document.getElementById('startTripModal'));
                    this.loadTours(); // Refresh the tours list
                } else {
                    this.showNotification('Failed to start trip: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error starting trip:', error);
                this.showNotification('Error starting trip', 'error');
            }
        }

        async showEventCompletion(acceptId) {
            this.currentAcceptId = acceptId;

            try {
                // First get tour details to get the tripId
                const tourResponse = await fetch(`${this.URL_ROOT}/Driver/getTourDetails/${acceptId}`);
                const tourData = await tourResponse.json();

                if (!tourData.success) {
                    this.showNotification('Failed to load tour details: ' + tourData.message, 'error');
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
                    this.showNotification('Failed to load trip events: ' + eventsData.message, 'error');
                }
            } catch (error) {
                console.error('Error loading trip events:', error);
                this.showNotification('Error loading trip events', 'error');
            }
        }

        showEventCompletionModal(events, tour) {
            const modal = document.getElementById('eventCompletionModal');
            const eventsList = document.getElementById('eventsList');
            const completeTripBtn = document.getElementById('completeTripBtn');

            eventsList.innerHTML = events.map(event => `
                <div class="event-item">
                    <div class="event-details">
                        <div class="event-title">${this.escapeHtml(event.locationName || event.eventType)}</div>
                        <div class="event-time">${this.formatTime(event.startTime)} - ${this.formatTime(event.endTime)}</div>
                        <div class="event-date">${this.formatDate(event.eventDate)}</div>
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
                        eventId: eventId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Event marked as complete', 'success');
                    // Refresh the events list
                    this.showEventCompletion(this.currentAcceptId);
                } else {
                    this.showNotification('Failed to mark event complete: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error marking event complete:', error);
                this.showNotification('Error marking event complete', 'error');
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
                    this.showNotification('Trip completed successfully!', 'success');
                    this.closeModal(document.getElementById('eventCompletionModal'));
                    this.loadTours(); // Refresh the tours list
                } else {
                    this.showNotification('Failed to complete trip: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error completing trip:', error);
                this.showNotification('Error completing trip', 'error');
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
            }
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }

        formatTime(timeString) {
            if (!timeString) return 'N/A';
            const [hours, minutes] = timeString.split(':');
            const date = new Date();
            date.setHours(hours, minutes);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        showNotification(message, type = 'info') {
            // Use the global notification system if available
            if (window.showNotification) {
                window.showNotification(message, type);
            } else {
                // Fallback to alert
                alert(message);
            }
        }
    }

    // Initialize the manager
    window.DriverToursManager = DriverToursManager;
    window.driverToursManager = new DriverToursManager();

})();