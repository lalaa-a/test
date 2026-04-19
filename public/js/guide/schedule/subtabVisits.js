(function() {
    // Clean up any existing instance
    if (window.GuideVisitsManager) {
        console.log('GuideVisitsManager already exists, cleaning up...');
        if (window.guideVisitsManager) {
            delete window.guideVisitsManager;
        }
        delete window.GuideVisitsManager;
    }

    // Driver Tours Manager
    class GuideVisitsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.currentUser = null;
            this.currentAcceptId = null;
            this.currentTripId = null;
            this.currentTripMeta = null;
            this.tripFeatureFlags = {};

            this.eventRouteMap = null;
            this.eventMapReady = false;
            this.eventRouteMarkers = [];
            this.eventRoutePath = null;
            this.eventRouteDirectionsService = null;

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
                    this.setPinStatus('', 'neutral');
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
                console.log('Loading guide visits...');
                const response = await fetch(`${this.URL_ROOT}/Guide/getGuideVisits`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.tours = data.visits;
                    this.updateStats();
                    this.renderTours();
                    this.switchToTab('ongoing-tours-section');
                } else {
                    console.error('API error:', data.message);
                    this.showNotification('Error loading visits', 'error');
                }
            } catch (error) {
                console.error('Error loading visits:', error);
                this.showNotification('Error loading visits', 'error');
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

        getTravellerPhotoUrl(tour) {
            const raw = String(tour.rqUserProfilePhoto || '').trim();
            if (!raw) {
                return `${this.URL_ROOT}/public/img/signup/profile.png`;
            }

            if (/^https?:\/\//i.test(raw)) {
                return raw;
            }

            if (raw.startsWith('/public/')) {
                return `${this.URL_ROOT}${raw}`;
            }

            return `${this.UP_ROOT}${raw}`;
        }

        formatCurrency(value) {
            const amount = Number(value);
            return Number.isFinite(amount) ? amount.toFixed(2) : '0.00';
        }

        createOngoingTourRow(tour) {
            const isCompleted = Number(tour.gDone) === 1 || Number(tour.doneStatus) === 1;
            const statusBadge = isCompleted
                ? '<span class="status-badge completed">Completed</span>'
                : '<span class="status-badge ongoing">In Progress</span>';

            const tripTitle = this.escapeHtml(tour.tripTitle || `Trip #${tour.tripId}`);
            const spotName = this.escapeHtml(tour.travelSpotName || tour.locationName || 'Assigned Spot');
            const canMark = !isCompleted && String(tour.tripStatus || '').toLowerCase() === 'ongoing';

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${this.getTravellerPhotoUrl(tour)}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName || 'Traveller')}</div>
                                <div class="traveller-rating">Guide visit assignment</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${tripTitle}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${this.formatCurrency(tour.totalCharge)} LKR (${tour.chargeType || 'Charge pending'})</div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${spotName}</div>
                            <div class="trip-dates">${tour.eventDate ? this.formatDate(tour.eventDate) : 'Date pending'}</div>
                            <div class="trip-route">${this.formatTime(tour.startTime)} - ${this.formatTime(tour.endTime)}</div>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="window.guideVisitsManager.viewTourDetails(${tour.acceptId})">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                            ${canMark ? `
                                <button class="btn btn-warning" onclick="window.guideVisitsManager.showEventCompletion(${tour.acceptId})">
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
            const tripTitle = this.escapeHtml(tour.tripTitle || `Trip #${tour.tripId}`);
            const spotName = this.escapeHtml(tour.travelSpotName || tour.locationName || 'Assigned Spot');

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${this.getTravellerPhotoUrl(tour)}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName || 'Traveller')}</div>
                                <div class="traveller-rating">Guide visit assignment</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${tripTitle}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${this.formatCurrency(tour.totalCharge)} LKR (${tour.chargeType || 'Charge pending'})</div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${spotName}</div>
                            <div class="trip-dates">${tour.eventDate ? this.formatDate(tour.eventDate) : 'Date pending'}</div>
                            <div class="trip-route">${this.formatTime(tour.startTime)} - ${this.formatTime(tour.endTime)}</div>
                        </div>
                    </td>
                    <td>${this.formatDate(tour.startDate)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="window.guideVisitsManager.viewTourDetails(${tour.acceptId})">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        createCompletedTourRow(tour) {
            const paymentStatus = {
                pending: '<span class="status-badge" style="background: #fef3c7; color: #92400e;">Pending</span>',
                paid: '<span class="status-badge" style="background: #dcfce7; color: #166534;">Paid</span>',
                failed: '<span class="status-badge" style="background: #fee2e2; color: #dc2626;">Failed</span>',
                refunded: '<span class="status-badge" style="background: #e0e7ff; color: #3730a3;">Refunded</span>'
            }[String(tour.paymentStatus || '').toLowerCase()] || '<span class="status-badge">Unknown</span>';

            const tripTitle = this.escapeHtml(tour.tripTitle || `Trip #${tour.tripId}`);
            const spotName = this.escapeHtml(tour.travelSpotName || tour.locationName || 'Assigned Spot');

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${this.getTravellerPhotoUrl(tour)}" alt="Traveller" class="traveller-avatar">
                            <div class="traveller-details">
                                <div class="traveller-name">${this.escapeHtml(tour.rqUserName || 'Traveller')}</div>
                                <div class="traveller-rating">Guide visit assignment</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${tripTitle}</div>
                            <div class="trip-dates">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                            <div class="trip-route">${this.formatCurrency(tour.totalCharge)} LKR</div>
                        </div>
                    </td>
                    <td>
                        <div class="trip-details">
                            <div class="trip-title">${spotName}</div>
                            <div class="trip-dates">${tour.eventDate ? this.formatDate(tour.eventDate) : 'Date pending'}</div>
                            <div class="trip-route">${this.formatTime(tour.startTime)} - ${this.formatTime(tour.endTime)}</div>
                        </div>
                    </td>
                    <td>${tour.completedAt ? this.formatDate(String(tour.completedAt).split(' ')[0]) : 'N/A'}</td>
                    <td>${paymentStatus}</td>
                </tr>
            `;
        }

        filterTours(section, searchTerm = '') {
            let filteredTours = [...this.tours[section]];

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredTours = filteredTours.filter(tour =>
                    String(tour.rqUserName || '').toLowerCase().includes(term) ||
                    String(tour.tripTitle || '').toLowerCase().includes(term) ||
                    String(tour.travelSpotName || '').toLowerCase().includes(term) ||
                    String(tour.totalCharge || '').toLowerCase().includes(term)
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
                const response = await fetch(`${this.URL_ROOT}/Guide/getVisitDetails/${acceptId}`);
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

            this.showTourDetails(tour);

            modal.style.display = 'block';
        }

        showTourDetails(tour) {
            const tripStatusText = Number(tour.doneStatus) === 1 || Number(tour.gDone) === 1
                ? 'Completed'
                : (String(tour.tripStatus || '').toLowerCase() === 'ongoing' ? 'Ongoing' : 'Scheduled');

            const content = `
                <div class="request-details-grid">
                    <div class="detail-group">
                        <label class="detail-label">Traveller Name</label>
                        <div class="detail-value">${this.escapeHtml(tour.rqUserName || 'Traveller')}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Title</label>
                        <div class="detail-value">${this.escapeHtml(tour.tripTitle || `Trip #${tour.tripId}`)}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Assigned Spot</label>
                        <div class="detail-value">${this.escapeHtml(tour.travelSpotName || tour.locationName || 'N/A')}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Event Date</label>
                        <div class="detail-value">${tour.eventDate ? this.formatDate(tour.eventDate) : 'N/A'}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Event Time</label>
                        <div class="detail-value">${this.formatTime(tour.startTime)} - ${this.formatTime(tour.endTime)}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Duration</label>
                        <div class="detail-value">${this.formatDate(tour.startDate)} - ${this.formatDate(tour.endDate)}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Charge Type</label>
                        <div class="detail-value">${tour.chargeType === 'perDay' ? 'Per Day' : (tour.chargeType === 'perPerson' ? 'Per Person' : 'Per Trip')}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Group Size</label>
                        <div class="detail-value">${tour.numberOfPeople || 'N/A'}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Guide Charge</label>
                        <div class="detail-value highlighted">${this.formatCurrency(tour.totalCharge)} LKR</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Status</label>
                        <div class="detail-value">${tripStatusText}</div>
                    </div>
                </div>

                <div class="request-summary">
                    <div class="summary-row">
                        <span>Accepted Date:</span>
                        <span>${this.formatDate(String(tour.createdAt || '').split(' ')[0])}</span>
                    </div>
                    <div class="summary-row">
                        <span>Trip Status:</span>
                        <span style="text-transform: capitalize;">${tripStatusText}</span>
                    </div>
                    ${tour.completedAt ? `
                        <div class="summary-row">
                            <span>Completed Date:</span>
                            <span>${this.formatDate(String(tour.completedAt).split(' ')[0])}</span>
                        </div>
                    ` : ''}
                    <div class="summary-row">
                        <span>Payment Status:</span>
                        <span style="text-transform: capitalize;">${tour.paymentStatus || 'pending'}</span>
                    </div>
                    <div class="summary-row">
                        <span>Guide Charge:</span>
                        <span>${this.formatCurrency(tour.totalCharge)} LKR</span>
                    </div>
                </div>
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
            const activeBtn = document.querySelector(`[onclick="window.guideVisitsManager.switchModalTab('${tabName}')"]`);
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
                const response = await fetch(`${this.URL_ROOT}/Guide/getTripItinerary/${this.currentTour.tripId}`);
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
                        <div class="summary-day-header" onclick="window.guideVisitsManager.toggleDaySummary(${index})">
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

            if (!modal || !pinInput) {
                this.showNotification('Trip start is handled by the assigned driver.', 'info');
                return;
            }

            pinInput.value = '';
            this.setPinStatus('', 'neutral');
            modal.style.display = 'block';
            pinInput.focus();
        }

        async startTrip() {
            this.showNotification('Trip start is handled by the assigned driver.', 'info');
            this.closeModal(document.getElementById('startTripModal'));
        }

        async showEventCompletion(acceptId) {
            this.currentAcceptId = acceptId;

            try {
                // First get tour details to get the tripId
                const tourResponse = await fetch(`${this.URL_ROOT}/Guide/getVisitDetails/${acceptId}`);
                const tourData = await tourResponse.json();

                if (!tourData.success) {
                    this.showNotification('Failed to load tour details: ' + tourData.message, 'error');
                    return;
                }

                const tripId = tourData.tour.tripId;
                this.currentTripId = tripId;

                // Now get trip events
                const eventsResponse = await fetch(`${this.URL_ROOT}/Guide/getVisitEvents?tripId=${tripId}`);
                const eventsData = await eventsResponse.json();

                if (eventsData.success) {
                    this.showEventCompletionModal(
                        eventsData.events,
                        tourData.tour,
                        eventsData.trip || null,
                        eventsData.featureFlags || {}
                    );
                } else {
                    this.showNotification('Failed to load trip events: ' + eventsData.message, 'error');
                }
            } catch (error) {
                console.error('Error loading trip events:', error);
                this.showNotification('Error loading trip events', 'error');
            }
        }

        showEventCompletionModal(events, tour, tripMeta = null, featureFlags = {}) {
            const modal = document.getElementById('eventCompletionModal');
            const eventsList = document.getElementById('eventsList');
            const completeTripBtn = document.getElementById('completeTripBtn');
            const pinGateNotice = document.getElementById('eventPinGateNotice');

            if (!modal || !eventsList) {
                return;
            }

            this.currentTripMeta = tripMeta;
            this.tripFeatureFlags = featureFlags || {};

            const hasPinMatch = !!this.tripFeatureFlags.hasPinMatch;
            const pinMatched = hasPinMatch ? !!(tripMeta && tripMeta.pinMatched) : true;
            const tripStatus = String((tripMeta && tripMeta.status) || tour.tripStatus || '').toLowerCase();
            const isOngoing = tripStatus === 'ongoing';

            const canMarkEvents = pinMatched && isOngoing;

            this.renderEventPinGateNotice(pinGateNotice, {
                canMarkEvents,
                hasPinMatch,
                pinMatched,
                isOngoing
            });

            if (!Array.isArray(events) || events.length === 0) {
                eventsList.innerHTML = `
                    <div class="event-item">
                        <div class="event-details">
                            <div class="event-title">No assigned events found for this visit.</div>
                        </div>
                    </div>
                `;
            } else {
                eventsList.innerHTML = events.map(event => `
                    <div class="event-item">
                        <div class="event-details">
                            <div class="event-title">${this.escapeHtml(event.spotName || event.locationName || event.eventType || 'Trip Event')}</div>
                            <div class="event-time">${this.formatTime(event.startTime)} - ${this.formatTime(event.endTime)}</div>
                            <div class="event-date">${this.formatDate(event.eventDate)}</div>
                        </div>
                        <div class="event-status">
                            <input type="checkbox"
                                   class="event-checkbox"
                                   data-event-id="${event.eventId}"
                                   ${event.gDone ? 'checked disabled' : ''}
                                   ${!event.gDone && !canMarkEvents ? 'disabled' : ''}
                                   onchange="window.guideVisitsManager.markEventComplete(${event.eventId}, this.checked)">
                            <span class="${event.gDone ? 'event-completed' : ''} ${!event.gDone && !canMarkEvents ? 'event-locked' : ''}">
                                ${event.gDone ? 'Completed' : (canMarkEvents ? 'Mark Complete' : 'Trip Must Be Ongoing')}
                            </span>
                        </div>
                    </div>
                `).join('');
            }

            // Show complete trip button if all events are completed
            const allCompleted = events.length > 0 && events.every(event => event.gDone);
            if (completeTripBtn) {
                completeTripBtn.style.display = allCompleted ? 'block' : 'none';
            }

            modal.style.display = 'block';
            this.renderEventRouteMap(events);
        }

        async markEventComplete(eventId, completed) {
            if (!completed) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Guide/markVisitEventComplete`, {
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
                    this.showEventCompletion(this.currentAcceptId);
                }
            } catch (error) {
                console.error('Error marking event complete:', error);
                this.showNotification('Error marking event complete', 'error');
                this.showEventCompletion(this.currentAcceptId);
            }
        }

        async completeTrip() {
            this.showNotification('Trip completion is handled by the assigned driver.', 'info');
        }

        closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
            }

            if (modal && modal.id === 'eventCompletionModal') {
                this.currentTripMeta = null;
                this.tripFeatureFlags = {};
                this.clearEventMapOverlays();
                this.setEventMapEmptyState('Map points are not available for this trip.');
            }

            if (modal && modal.id === 'startTripModal') {
                this.setPinStatus('', 'neutral');
            }
        }

        setPinStatus(message, tone = 'neutral') {
            const statusElement = document.getElementById('tripPinStatus');
            if (!statusElement) {
                return;
            }

            const text = String(message || '').trim();
            statusElement.textContent = text;
            statusElement.style.display = text ? 'block' : 'none';
            statusElement.className = 'pin-status';

            if (!text) {
                return;
            }

            if (tone === 'success') {
                statusElement.classList.add('success');
            } else if (tone === 'error') {
                statusElement.classList.add('error');
            } else if (tone === 'pending') {
                statusElement.classList.add('pending');
            }
        }

        renderEventPinGateNotice(container, state) {
            if (!container) {
                return;
            }

            let message = '';
            let className = 'event-pin-gate-notice';

            if (!state.isOngoing) {
                message = 'Trip is not ongoing yet. Guide events can be marked only after the trip starts.';
                className += ' locked';
            } else if (state.hasPinMatch && !state.pinMatched) {
                message = 'Traveller PIN is not matched yet. Wait until the driver starts the trip with a valid PIN.';
                className += ' locked';
            } else {
                message = 'Trip is ongoing and PIN is matched. You can mark your assigned events as completed.';
                className += ' ready';
            }

            container.textContent = message;
            container.className = className;
            container.style.display = 'block';
        }

        getEventMapElements() {
            return {
                mapElement: document.getElementById('driver-events-route-map'),
                emptyStateElement: document.getElementById('driver-events-map-empty-state')
            };
        }

        async ensureEventMapReady() {
            if (this.eventMapReady && this.eventRouteMap) {
                return true;
            }

            const { mapElement } = this.getEventMapElements();
            if (!mapElement) {
                return false;
            }

            try {
                await this.waitForGoogleMaps();

                this.eventRouteMap = new google.maps.Map(mapElement, {
                    center: { lat: 7.8731, lng: 80.7718 },
                    zoom: 8,
                    mapTypeControl: false,
                    streetViewControl: false
                });

                if (!this.eventRouteDirectionsService) {
                    this.eventRouteDirectionsService = new google.maps.DirectionsService();
                }

                this.eventMapReady = true;
                return true;
            } catch (error) {
                console.error('Failed to initialize event route map:', error);
                this.eventMapReady = false;
                return false;
            }
        }

        async renderEventRouteMap(events) {
            const ready = await this.ensureEventMapReady();
            if (!ready) {
                this.setEventMapEmptyState('Google Maps is not available at the moment.');
                return;
            }

            this.clearEventMapOverlays();

            const points = Array.isArray(events)
                ? events.map((event) => {
                    const lat = Number(event.latitude);
                    const lng = Number(event.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                        return null;
                    }

                    return {
                        lat,
                        lng,
                        name: event.locationName || event.spotName || event.eventType || 'Trip Event'
                    };
                }).filter(Boolean)
                : [];

            if (points.length === 0) {
                this.setEventMapEmptyState('Map points are not available for these events.');
                return;
            }

            this.setEventMapEmptyState('', true);

            const routePath = [];

            points.forEach((point, index) => {
                const position = { lat: point.lat, lng: point.lng };
                routePath.push(position);

                const marker = new google.maps.Marker({
                    map: this.eventRouteMap,
                    position,
                    title: point.name,
                    label: {
                        text: String(index + 1),
                        color: '#ffffff'
                    },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 11,
                        fillColor: '#006a71',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `<div style="padding:4px 6px; font-size:12px;">${this.escapeHtml(point.name)}</div>`
                });

                marker.addListener('click', () => {
                    infoWindow.open(this.eventRouteMap, marker);
                });

                this.eventRouteMarkers.push(marker);
            });

            let displayPath = routePath;

            if (routePath.length >= 2) {
                const roadPath = await this.buildRoadPath(routePath);
                const pathToRender = roadPath.length >= 2 ? roadPath : routePath;

                displayPath = pathToRender;

                this.eventRoutePath = new google.maps.Polyline({
                    path: pathToRender,
                    geodesic: true,
                    strokeColor: '#006a71',
                    strokeOpacity: 0.82,
                    strokeWeight: 4,
                    map: this.eventRouteMap
                });
            }

            if (routePath.length === 1) {
                this.eventRouteMap.setCenter(routePath[0]);
                this.eventRouteMap.setZoom(12);
            } else if (displayPath.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                displayPath.forEach((point) => bounds.extend(point));
                if (!bounds.isEmpty()) {
                    this.eventRouteMap.fitBounds(bounds, 60);
                }
            }

            window.setTimeout(() => {
                if (this.eventRouteMap && window.google && window.google.maps) {
                    google.maps.event.trigger(this.eventRouteMap, 'resize');
                }
            }, 50);
        }

        async buildRoadPath(points) {
            if (!Array.isArray(points) || points.length < 2 || !this.eventRouteDirectionsService) {
                return [];
            }

            const maxPointsPerRequest = 25; // origin + destination + 23 waypoints
            const fullRoadPath = [];
            let cursor = 0;

            while (cursor < points.length - 1) {
                const segment = points.slice(cursor, Math.min(cursor + maxPointsPerRequest, points.length));
                if (segment.length < 2) {
                    break;
                }

                try {
                    const route = await this.requestRoadRoute(segment);
                    const segmentPath = this.extractRoadPolyline(route);

                    if (segmentPath.length === 0) {
                        return [];
                    }

                    if (
                        fullRoadPath.length > 0 &&
                        segmentPath.length > 0 &&
                        this.sameCoordinate(fullRoadPath[fullRoadPath.length - 1], segmentPath[0])
                    ) {
                        segmentPath.shift();
                    }

                    fullRoadPath.push(...segmentPath);
                } catch (error) {
                    console.warn('Road path request failed for segment:', error);
                    return [];
                }

                cursor += segment.length - 1; // overlap at segment edge for continuity
            }

            return fullRoadPath;
        }

        requestRoadRoute(segmentPoints) {
            return new Promise((resolve, reject) => {
                if (!this.eventRouteDirectionsService || segmentPoints.length < 2) {
                    reject(new Error('Directions service is not ready.'));
                    return;
                }

                const origin = segmentPoints[0];
                const destination = segmentPoints[segmentPoints.length - 1];
                const waypoints = segmentPoints.slice(1, -1).map((point) => ({
                    location: point,
                    stopover: true
                }));

                this.eventRouteDirectionsService.route(
                    {
                        origin,
                        destination,
                        waypoints,
                        optimizeWaypoints: false,
                        travelMode: google.maps.TravelMode.DRIVING
                    },
                    (result, status) => {
                        if (status === 'OK' && result) {
                            resolve(result);
                            return;
                        }

                        reject(new Error(`Directions request failed: ${status}`));
                    }
                );
            });
        }

        extractRoadPolyline(routeResult) {
            const path = [];
            const route = routeResult && routeResult.routes && routeResult.routes[0]
                ? routeResult.routes[0]
                : null;

            if (!route) {
                return path;
            }

            const legs = Array.isArray(route.legs) ? route.legs : [];
            legs.forEach((leg) => {
                const steps = Array.isArray(leg.steps) ? leg.steps : [];
                steps.forEach((step) => {
                    const stepPath = Array.isArray(step.path) ? step.path : [];
                    stepPath.forEach((latLng) => {
                        if (latLng && typeof latLng.lat === 'function' && typeof latLng.lng === 'function') {
                            path.push({ lat: latLng.lat(), lng: latLng.lng() });
                        }
                    });
                });
            });

            if (path.length === 0 && Array.isArray(route.overview_path)) {
                route.overview_path.forEach((latLng) => {
                    if (latLng && typeof latLng.lat === 'function' && typeof latLng.lng === 'function') {
                        path.push({ lat: latLng.lat(), lng: latLng.lng() });
                    }
                });
            }

            return path;
        }

        sameCoordinate(a, b) {
            if (!a || !b) {
                return false;
            }

            const deltaLat = Math.abs(Number(a.lat) - Number(b.lat));
            const deltaLng = Math.abs(Number(a.lng) - Number(b.lng));

            return deltaLat < 0.000001 && deltaLng < 0.000001;
        }

        clearEventMapOverlays() {
            this.eventRouteMarkers.forEach((marker) => marker.setMap(null));
            this.eventRouteMarkers = [];

            if (this.eventRoutePath) {
                this.eventRoutePath.setMap(null);
                this.eventRoutePath = null;
            }
        }

        setEventMapEmptyState(message, hide = false) {
            const { emptyStateElement } = this.getEventMapElements();
            if (!emptyStateElement) {
                return;
            }

            if (hide) {
                emptyStateElement.style.display = 'none';
                emptyStateElement.textContent = '';
                return;
            }

            emptyStateElement.style.display = 'block';
            emptyStateElement.textContent = message || 'Map points are not available for this trip.';
        }

        waitForGoogleMaps(timeoutMs = 12000) {
            return new Promise((resolve, reject) => {
                const startedAt = Date.now();

                const check = () => {
                    if (window.google && window.google.maps) {
                        resolve();
                        return;
                    }

                    if (Date.now() - startedAt > timeoutMs) {
                        reject(new Error('Google Maps API did not load in time.'));
                        return;
                    }

                    window.setTimeout(check, 120);
                };

                check();
            });
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
    window.GuideVisitsManager = GuideVisitsManager;
    window.guideVisitsManager = new GuideVisitsManager();

})();

