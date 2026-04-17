(function() {
    // Clean up any existing instance
    if (window.DriverRequestsManager) {
        console.log('DriverRequestsManager already exists, cleaning up...');
        if (window.driverRequestsManager) {
            delete window.driverRequestsManager;
        }
        delete window.DriverRequestsManager;
    }

    // Driver Requests Manager
    class DriverRequestsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads'
            this.currentUser = null;
            this.pendingAcceptRequestId = null;
            this.pendingRejectRequestId = null;
            
            this.requests = {
                pending: [],
                accepted: [],
                rejected: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadRequests();
        }

        bindEvents() {
            // Pending section search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterRequests('pending', e.target.value);
                });
            }

            const pendingChargeTypeFilter = document.getElementById('pendingChargeTypeFilter');
            if (pendingChargeTypeFilter) {
                pendingChargeTypeFilter.addEventListener('change', () => {
                    this.filterRequests('pending');
                });
            }

            // Accepted section search and filter
            const acceptedSearchInput = document.getElementById('acceptedSearchInput');
            if (acceptedSearchInput) {
                acceptedSearchInput.addEventListener('input', (e) => {
                    this.filterRequests('accepted', e.target.value);
                });
            }

            const acceptedChargeTypeFilter = document.getElementById('acceptedChargeTypeFilter');
            if (acceptedChargeTypeFilter) {
                acceptedChargeTypeFilter.addEventListener('change', () => {
                    this.filterRequests('accepted');
                });
            }

            // Rejected section search and filter
            const rejectedSearchInput = document.getElementById('rejectedSearchInput');
            if (rejectedSearchInput) {
                rejectedSearchInput.addEventListener('input', (e) => {
                    this.filterRequests('rejected', e.target.value);
                });
            }

            const rejectedChargeTypeFilter = document.getElementById('rejectedChargeTypeFilter');
            if (rejectedChargeTypeFilter) {
                rejectedChargeTypeFilter.addEventListener('change', () => {
                    this.filterRequests('rejected');
                });
            }

            // Completed section removed

            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToTab(targetId);
                });
            });

            // Modal close buttons
            const modalCloseBtn = document.querySelector('#requestDetailsModal .modal-close');
            if (modalCloseBtn) {
                modalCloseBtn.addEventListener('click', () => this.closeModal(document.getElementById('requestDetailsModal')));
            }

            const modalFooterCloseBtn = document.getElementById('modalCloseBtn');
            if (modalFooterCloseBtn) {
                modalFooterCloseBtn.addEventListener('click', () => this.closeModal(document.getElementById('requestDetailsModal')));
            }

            // Modal overlays
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal);
                    }
                });
            });

            // Accept confirmation modal events
            const cancelAcceptBtn = document.getElementById('cancelAcceptBtn');
            const confirmAcceptBtn = document.getElementById('confirmAcceptBtn');

            if (cancelAcceptBtn) {
                cancelAcceptBtn.addEventListener('click', () => this.closeAcceptConfirmModal());
            }

            if (confirmAcceptBtn) {
                confirmAcceptBtn.addEventListener('click', () => this.confirmAcceptRequest());
            }

            // Reject confirmation modal events
            const cancelRejectBtn = document.getElementById('cancelRejectBtn');
            const confirmRejectBtn = document.getElementById('confirmRejectBtn');

            if (cancelRejectBtn) {
                cancelRejectBtn.addEventListener('click', () => this.closeRejectConfirmModal());
            }

            if (confirmRejectBtn) {
                confirmRejectBtn.addEventListener('click', () => this.confirmRejectRequest());
            }

            // Request row clicks (View button)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const requestId = button.dataset.requestId;
                    if (requestId) {
                        this.showRequestDetails(requestId);
                    }
                }
            });

            // Action buttons (Accept, Reject)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-accept-small')) {
                    const button = e.target.closest('.btn-accept-small');
                    const requestId = button.dataset.requestId;
                    if (requestId) {
                        this.acceptRequest(requestId);
                    }
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-reject-small')) {
                    const button = e.target.closest('.btn-reject-small');
                    const requestId = button.dataset.requestId;
                    if (requestId) {
                        this.rejectRequest(requestId);
                    }
                }
            });

            // Accept button in details modal
            const acceptBtn = document.getElementById('acceptBtn');
            if (acceptBtn) {
                acceptBtn.addEventListener('click', () => this.acceptRequest(this.currentRequestId));
            }

            // Reject button in details modal
            const rejectBtn = document.getElementById('rejectBtn');
            if (rejectBtn) {
                rejectBtn.addEventListener('click', () => this.rejectRequest(this.currentRequestId));
            }

            // Photo viewer modal
            const photoViewerModal = document.getElementById('photoViewerModal');
            const photoViewerClose = document.querySelector('.photo-viewer-close');

            if (photoViewerModal) {
                photoViewerModal.addEventListener('click', (e) => {
                    if (e.target === photoViewerModal) {
                        this.closePhotoViewer();
                    }
                });
            }

            if (photoViewerClose) {
                photoViewerClose.addEventListener('click', () => this.closePhotoViewer());
            }

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closePhotoViewer();
                }
            });
        }

        async loadRequests() {
            try {
                console.log('Loading trip requests...');
                const response = await fetch(`${this.URL_ROOT}/Driver/getMyRequests`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Organize requests by status
                    data.requests.forEach(request => {
                        const status = request.requestStatus;
                        if (status === 'pending' || status === 'requested') {
                            this.requests.pending.push(request);
                        } else if (status === 'accepted') {
                            this.requests.accepted.push(request);
                        } else if (status === 'rejected' || status === 'cancelled') {
                            this.requests.rejected.push(request);
                        }
                    });

                    this.updateStats();
                    this.renderRequests('pending');
                    this.renderRequests('accepted');
                    this.renderRequests('rejected');
                    this.switchToTab('pending-requests-section');
                } else {
                    console.error('API error:', data.message);
                    window.showNotification('Error loading requests', 'error');
                }
            } catch (error) {
                console.error('Error loading requests:', error);
                window.showNotification('Error loading requests', 'error');
            }
        }

        updateStats() {
            document.getElementById('pendingRequestsCount').textContent = this.requests.pending.length;
            document.getElementById('acceptedRequestsCount').textContent = this.requests.accepted.length;
            document.getElementById('rejectedRequestsCount').textContent = this.requests.rejected.length;
        }

        filterRequests(section, searchTerm = '') {
            const chargeTypeFilter = document.getElementById(`${section}ChargeTypeFilter`).value;

            let filteredRequests = [...this.requests[section]];

            if (chargeTypeFilter !== 'all') {
                filteredRequests = filteredRequests.filter(req => req.chargeType === chargeTypeFilter);
            }

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredRequests = filteredRequests.filter(req =>
                    req.driverName.toLowerCase().includes(term) ||
                    req.vehicleModel.toLowerCase().includes(term) ||
                    req.totalAmount.toString().includes(term)
                );
            }

            this.renderRequests(section, filteredRequests);
        }

        renderRequests(section, customRequests = null) {
            const requests = customRequests || this.requests[section];
            const tbody = document.getElementById(`${section}RequestsGrid`);

            if (!tbody) return;

            if (requests.length === 0) {
                tbody.innerHTML = `
                    <tr class="no-requests">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            <p>No ${section} requests</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = requests.map(req => this.createRequestRow(req, section)).join('');
        }

        createRequestRow(request, section) {
            const tripDate = new Date(request.tripDate || request.requestedAt).toLocaleDateString();
            const responseDate = request.respondedAt ? new Date(request.respondedAt).toLocaleDateString() : 
                                 request.requestedAt ? new Date(request.requestedAt).toLocaleDateString() : 'N/A';
            let dateValue = responseDate;
            if (section === 'pending') {
                dateValue = new Date(request.requestedAt).toLocaleDateString();
            }

            let actionButtons = '';
            if (section === 'pending') {
                actionButtons = `
                    <div class="request-actions">
                        <button class="btn btn-view" data-request-id="${request.requestId}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                `;
            } else if (section === 'accepted') {
                actionButtons = `
                    <div class="request-actions">
                        <button class="btn btn-view" data-request-id="${request.requestId}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                `;
            } else {
                actionButtons = `
                    <div class="request-actions">
                        <button class="btn btn-view" data-request-id="${request.requestId}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                `;
            }

            return `
                <tr>
                    <td>
                        <div class="traveller-info">
                            <img src="${this.UP_ROOT}${request.driverProfilePhoto}" alt="Traveller" class="traveller-avatar">
                            <div>
                                <div class="traveller-name">${request.driverName}</div>
                                <div class="traveller-rating">
                                    <i class="fas fa-star"></i> ${request.driverRating || 'N/A'}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>${tripDate}</td>
                    <td>
                        <span class="charge-badge">${request.chargeType === 'perDay' ? 'Per Day' : 'Per KM'}</span>
                    </td>
                    <td>
                        <span class="amount-display">${request.totalAmount} LKR</span>
                    </td>
                    <td>
                        <span class="vehicle-badge">${request.vehicleModel}</span>
                    </td>
                    <td>${dateValue}</td>
                    <td>${actionButtons}</td>
                </tr>
            `;
        }

        showRequestDetails(requestId) {
            // Find the request in all sections
            let request = null;
            for (let section of Object.keys(this.requests)) {
                request = this.requests[section].find(r => r.requestId == requestId);
                if (request) break;
            }

            if (!request) {
                console.error('Request not found:', requestId);
                return;
            }

            this.currentRequestId = requestId;
            this.currentRequestStatus = request.requestStatus;

            const tripDate = new Date(request.tripDate || request.requestedAt).toLocaleDateString();
            const requestedDate = new Date(request.requestedAt).toLocaleDateString();
            const acceptedDate = request.respondedAt ? new Date(request.respondedAt).toLocaleDateString() : 'N/A';

            const content = `
                <div class="request-details-grid">
                    <div class="detail-group">
                        <label class="detail-label">Traveller Name</label>
                        <div class="detail-value">${request.driverName}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Traveller Rating</label>
                        <div class="detail-value">
                            <i class="fas fa-star" style="color: #fbbf24;"></i> ${request.driverRating || 'No rating'}
                        </div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Model</label>
                        <div class="detail-value">${request.vehicleModel}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Type</label>
                        <div class="detail-value">${request.vehicleType}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Capacity</label>
                        <div class="detail-value">${request.vehicleCapacity} Seats</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Child Seats</label>
                        <div class="detail-value">${request.childSeats || 0}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Trip Date</label>
                        <div class="detail-value">${tripDate}</div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Charge Type</label>
                        <div class="detail-value">${request.chargeType === 'perDay' ? 'Per Day' : 'Per KM'}</div>
                    </div>
                    ${request.totalKm ? `
                        <div class="detail-group">
                            <label class="detail-label">Total KM</label>
                            <div class="detail-value">${request.totalKm} km</div>
                        </div>
                    ` : ''}
                    <div class="detail-group">
                        <label class="detail-label">Total Amount</label>
                        <div class="detail-value highlighted">${request.totalAmount} LKR</div>
                    </div>
                </div>

                <div class="request-summary">
                    <div class="summary-row">
                        <span>Requested Date:</span>
                        <span>${requestedDate}</span>
                    </div>
                    <div class="summary-row">
                        <span>Request Status:</span>
                        <span style="text-transform: capitalize;">${request.requestStatus}</span>
                    </div>
                    ${request.respondedAt ? `
                        <div class="summary-row">
                            <span>Response Date:</span>
                            <span>${acceptedDate}</span>
                        </div>
                    ` : ''}
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span>${request.totalAmount} LKR</span>
                    </div>
                </div>

                ${request.vehiclePhoto ? `
                    <div class="detail-group">
                        <label class="detail-label">Vehicle Photo</label>
                        <img src="${this.UP_ROOT}${request.vehiclePhoto}" alt="Vehicle" style="max-width: 100%; border-radius: 6px; cursor: pointer; max-height: 300px;" onclick="driverRequestsManager.viewPhoto('${this.UP_ROOT}${request.vehiclePhoto}')">
                    </div>
                ` : ''}
            `;

            const detailsContent = document.getElementById('requestDetailsContent');
            if (detailsContent) {
                detailsContent.innerHTML = content;
            }

            const acceptBtn = document.getElementById('acceptBtn');
            const rejectBtn = document.getElementById('rejectBtn');

            if (acceptBtn && rejectBtn) {
                if (request.requestStatus === 'pending' || request.requestStatus === 'requested') {
                    acceptBtn.style.display = 'inline-flex';
                    rejectBtn.style.display = 'inline-flex';
                } else {
                    acceptBtn.style.display = 'none';
                    rejectBtn.style.display = 'none';
                }
            }

            this.openModal(document.getElementById('requestDetailsModal'));
            
            // Reset to details tab
            this.switchModalTab('details');
        }

        acceptRequest(requestId = null) {
            const id = requestId || this.currentRequestId;
            if (!id) {
                console.error('No request ID provided');
                return;
            }

            this.pendingAcceptRequestId = id;
            this.openModal(document.getElementById('acceptConfirmModal'));
        }

        rejectRequest(requestId = null) {
            const id = requestId || this.currentRequestId;
            if (!id) {
                console.error('No request ID provided');
                return;
            }

            this.pendingRejectRequestId = id;
            document.getElementById('rejectionReason').value = '';
            this.openModal(document.getElementById('rejectConfirmModal'));
        }

        async confirmAcceptRequest() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/acceptRequest/${this.pendingAcceptRequestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Request accepted successfully', 'success');
                    this.closeAcceptConfirmModal();
                    this.closeModal(document.getElementById('requestDetailsModal'));
                    
                    // Reload requests
                    this.requests = {
                        pending: [],
                        accepted: [],
                        rejected: []
                    };
                    this.loadRequests();
                } else {
                    window.showNotification(data.message || 'Error accepting request', 'error');
                }
            } catch (error) {
                console.error('Error accepting request:', error);
                window.showNotification('Error accepting request', 'error');
            }
        }

        async confirmRejectRequest() {
            const reason = document.getElementById('rejectionReason').value.trim();

            if (!reason) {
                window.showNotification('Please provide a rejection reason', 'warning');
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/rejectRequest/${this.pendingRejectRequestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Request rejected successfully', 'success');
                    this.closeRejectConfirmModal();
                    this.closeModal(document.getElementById('requestDetailsModal'));
                    
                    // Reload requests
                    this.requests = {
                        pending: [],
                        accepted: [],
                        rejected: []
                    };
                    this.loadRequests();
                } else {
                    window.showNotification(data.message || 'Error rejecting request', 'error');
                }
            } catch (error) {
                console.error('Error rejecting request:', error);
                window.showNotification('Error rejecting request', 'error');
            }
        }

        switchToTab(tabId) {
            // Hide all sections
            document.querySelectorAll('.requests-section').forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            const selectedSection = document.getElementById(tabId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }

            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${tabId}`) {
                    link.classList.add('active');
                }
            });

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        openModal(modal) {
            if (modal) {
                modal.classList.add('show');
            }
        }

        switchModalTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`[onclick="driverRequestsManager.switchModalTab('${tabName}')"]`);
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
            if (!this.currentRequestId) return;

            // Find the request to get tripId
            let request = null;
            for (let section of Object.keys(this.requests)) {
                request = this.requests[section].find(r => r.requestId == this.currentRequestId);
                if (request) break;
            }

            if (!request || !request.tripId) {
                document.getElementById('itineraryContent').innerHTML = '<p class="no-data">No itinerary available</p>';
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getTripItinerary/${request.tripId}`);
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
                        <div class="summary-day-header" onclick="driverRequestsManager.toggleDaySummary(${index})">
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

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
            }
        }

        closeAcceptConfirmModal() {
            this.closeModal(document.getElementById('acceptConfirmModal'));
            this.pendingAcceptRequestId = null;
        }

        closeRejectConfirmModal() {
            this.closeModal(document.getElementById('rejectConfirmModal'));
            this.pendingRejectRequestId = null;
            document.getElementById('rejectionReason').value = '';
        }

        viewPhoto(photoPath) {
            const modal = document.getElementById('photoViewerModal');
            const img = document.getElementById('photoViewerImage');

            if (img) {
                img.src = photoPath;
            }

            if (modal) {
                modal.classList.add('show');
            }
        }

        closePhotoViewer() {
            const modal = document.getElementById('photoViewerModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }
    }


    
    window.DriverRequestsManager = DriverRequestsManager;
    window.driverRequestsManager = new DriverRequestsManager();

})();
