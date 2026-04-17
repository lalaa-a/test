(function () {
    if (window.TripControlManager) {
        if (window.tripControlManager) {
            delete window.tripControlManager;
        }
        delete window.TripControlManager;
    }

    class TripControlManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentTrip = null;
            this.currentGuideEventId = null;
            this.driverCandidates = [];
            this.guideCandidates = [];
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            const searchBtn = document.getElementById('tripControlSearchBtn');
            const clearBtn = document.getElementById('tripControlClearBtn');
            const tripInput = document.getElementById('tripControlTripIdInput');
            const eventSearchInput = document.getElementById('tripControlEventSearchInput');

            if (searchBtn) {
                searchBtn.addEventListener('click', () => this.searchTrip());
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => this.resetView());
            }

            if (tripInput) {
                tripInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        this.searchTrip();
                    }
                });
            }

            if (eventSearchInput) {
                eventSearchInput.addEventListener('input', (e) => this.filterEvents(e.target.value));
            }

            const guideSearchInput = document.getElementById('tripControlGuideSearchInput');
            if (guideSearchInput) {
                guideSearchInput.addEventListener('input', (e) => this.filterGuides(e.target.value));
            }

            document.querySelectorAll('.modal').forEach((modal) => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal);
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal(document.getElementById('tripControlDriverModal'));
                    this.closeModal(document.getElementById('tripControlGuideModal'));
                }
            });

            document.addEventListener('click', (e) => {
                const driverBtn = e.target.closest('.btn-open-driver-modal');
                if (driverBtn) {
                    this.openDriverCandidatesModal();
                    return;
                }

                const guideBtn = e.target.closest('.btn-open-guide-modal');
                if (guideBtn) {
                    const eventId = parseInt(guideBtn.dataset.eventId || '0', 10);
                    if (eventId > 0) {
                        this.openGuideCandidatesModal(eventId);
                    }
                    return;
                }

                const removeGuideEventBtn = e.target.closest('.btn-remove-guide-from-event');
                if (removeGuideEventBtn && !removeGuideEventBtn.disabled) {
                    const eventId = parseInt(removeGuideEventBtn.dataset.eventId || '0', 10);
                    if (eventId > 0) {
                        this.removeGuideFromEvent(eventId);
                    }
                    return;
                }

                const assignDriverBtn = e.target.closest('.btn-assign-driver');
                if (assignDriverBtn) {
                    const driverId = parseInt(assignDriverBtn.dataset.driverId || '0', 10);
                    const forceAssign = assignDriverBtn.dataset.force === '1';
                    if (driverId > 0) {
                        this.assignDriver(driverId, forceAssign);
                    }
                    return;
                }

                const assignGuideBtn = e.target.closest('.btn-assign-guide');
                if (assignGuideBtn) {
                    const guideId = parseInt(assignGuideBtn.dataset.guideId || '0', 10);
                    const eventId = parseInt(assignGuideBtn.dataset.eventId || '0', 10);
                    const forceAssign = assignGuideBtn.dataset.force === '1';
                    if (guideId > 0 && eventId > 0) {
                        this.assignGuide(eventId, guideId, forceAssign);
                    }
                }
            });
        }

        async searchTrip() {
            const input = document.getElementById('tripControlTripIdInput');
            const tripId = parseInt((input?.value || '').trim(), 10);

            if (!tripId || tripId <= 0) {
                this.showMessage('Please enter a valid trip ID', 'error');
                return;
            }

            this.showMessage('Searching trip...', 'info');

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getTripControlTrip/${tripId}`);
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Trip not found');
                }

                this.currentTrip = data.trip;
                this.renderTrip();
                this.showMessage(`Trip #${tripId} loaded successfully`, 'success');
            } catch (error) {
                this.currentTrip = null;
                this.renderEmptyResults();
                this.showMessage(error.message || 'Failed to load trip details', 'error');
            }
        }

        async refreshCurrentTrip() {
            if (!this.currentTrip?.tripId) {
                return;
            }

            const tripId = parseInt(this.currentTrip.tripId, 10);
            if (!tripId || tripId <= 0) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getTripControlTrip/${tripId}`);
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Trip not found');
                }

                this.currentTrip = data.trip;
                this.renderTrip();
            } catch (error) {
                this.showMessage(error.message || 'Failed to refresh trip details', 'error');
            }
        }

        renderTrip() {
            const wrapper = document.getElementById('tripControlResultWrapper');
            if (wrapper) {
                wrapper.style.display = 'flex';
            }

            this.renderSummary();
            this.renderDriverCard();
            this.renderEvents();
        }

        renderSummary() {
            const container = document.getElementById('tripControlSummaryContent');
            if (!container || !this.currentTrip) {
                return;
            }

            const trip = this.currentTrip;
            container.innerHTML = `
                <div class="summary-list">
                    <div class="summary-row"><span class="summary-label">Trip ID</span><span class="summary-value">${this.escapeHtml(trip.tripId)}</span></div>
                    <div class="summary-row"><span class="summary-label">Title</span><span class="summary-value">${this.escapeHtml(trip.tripTitle || '-')}</span></div>
                    <div class="summary-row"><span class="summary-label">Traveller</span><span class="summary-value">${this.escapeHtml(trip.travellerName || '-')}</span></div>
                    <div class="summary-row"><span class="summary-label">People</span><span class="summary-value">${this.escapeHtml(trip.numberOfPeople || '-')}</span></div>
                    <div class="summary-row"><span class="summary-label">Start Date</span><span class="summary-value">${this.formatDate(trip.startDate)}</span></div>
                    <div class="summary-row"><span class="summary-label">End Date</span><span class="summary-value">${this.formatDate(trip.endDate)}</span></div>
                    <div class="summary-row"><span class="summary-label">Status</span><span class="summary-value">${this.escapeHtml(this.humanizeStatus(trip.status))}</span></div>
                </div>
            `;
        }

        renderDriverCard() {
            const container = document.getElementById('tripControlDriverContent');
            if (!container || !this.currentTrip) {
                return;
            }

            const currentDriver = this.currentTrip.currentDriver;

            container.innerHTML = `
                <div class="assignment-list">
                    <div class="assignment-row"><span class="assignment-label">Current Driver</span><span class="assignment-value">${this.escapeHtml(currentDriver?.driverName || 'Not Assigned')}</span></div>
                    <div class="assignment-row"><span class="assignment-label">Vehicle</span><span class="assignment-value">${this.escapeHtml(currentDriver?.vehicleModel || '-')}</span></div>
                    <div class="assignment-row"><span class="assignment-label">Vehicle Type</span><span class="assignment-value">${this.escapeHtml(currentDriver?.vehicleType || '-')}</span></div>
                    <div class="assignment-row"><span class="assignment-label">Capacity</span><span class="assignment-value">${this.escapeHtml(currentDriver?.vehicleCapacity || '-')}</span></div>
                    <div class="assignment-row"><span class="assignment-label">Charge</span><span class="assignment-value">${currentDriver?.totalAmount ? this.formatCurrency(currentDriver.totalAmount) : '-'}</span></div>
                </div>
                <div style="margin-top: 14px;">
                    <button class="btn btn-primary btn-open-driver-modal">
                        <i class="fas fa-random"></i>
                        Replace
                    </button>
                </div>
            `;
        }

        renderEvents() {
            const tbody = document.getElementById('tripControlEventsGrid');
            if (!tbody || !this.currentTrip) {
                return;
            }

            const events = Array.isArray(this.currentTrip.events) ? this.currentTrip.events : [];

            if (!events.length) {
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-calendar-times"></i>
                            <p>No events available for this trip</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = events.map((event) => {
                const timeSlot = `${this.formatTime(event.startTime)} - ${this.formatTime(event.endTime)}`;
                const spotOrLocation = event.spotName || event.locationName || '-';
                const guideName = event.currentGuide?.guideFullName || 'Not Assigned';

                return `
                    <tr class="trip-event-row" data-event-search="${this.escapeHtml(`${event.eventId} ${event.eventType || ''} ${spotOrLocation} ${event.eventStatus || ''}`.toLowerCase())}">
                        <td>${this.escapeHtml(event.eventId)}</td>
                        <td>${this.formatDate(event.eventDate)}</td>
                        <td>${this.escapeHtml(timeSlot)}</td>
                        <td>${this.escapeHtml(event.eventType || '-')}<span class="inline-meta">${this.escapeHtml(event.eventStatus || '-')}</span></td>
                        <td>${this.escapeHtml(spotOrLocation)}</td>
                        <td>${this.escapeHtml(guideName)}</td>
                        <td class="actions-cell">
                            <button type="button" class="btn btn-primary btn-open-guide-modal" data-event-id="${this.escapeHtml(event.eventId)}">
                                <i class="fas fa-random"></i>
                                Replace
                            </button>
                            <button type="button" class="btn btn-danger btn-remove-guide-from-event" data-event-id="${this.escapeHtml(event.eventId)}" style="margin-left: 5px;" ${!event.currentGuide?.guideFullName ? 'disabled' : ''}>
                                <i class="fas fa-times"></i>
                                Remove
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        filterEvents(searchTerm) {
            const normalized = (searchTerm || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#tripControlEventsGrid .trip-event-row');
            rows.forEach((row) => {
                const haystack = row.dataset.eventSearch || '';
                row.style.display = !normalized || haystack.includes(normalized) ? '' : 'none';
            });
        }

        filterGuides(searchTerm) {
            const normalized = (searchTerm || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#tripControlGuideCandidatesGrid tr');
            rows.forEach((row) => {
                const haystack = row.dataset.guideSearch || '';
                row.style.display = !normalized || haystack.includes(normalized) ? '' : 'none';
            });
        }

        async openDriverCandidatesModal() {
            if (!this.currentTrip?.tripId) {
                this.showMessage('Search and load a trip first', 'error');
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getTripControlDriverCandidates/${this.currentTrip.tripId}`);
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load drivers');
                }

                this.driverCandidates = data.candidates || [];
                this.renderDriverCandidates();
                this.openModal(document.getElementById('tripControlDriverModal'));
            } catch (error) {
                this.showMessage(error.message || 'Failed to load driver candidates', 'error');
            }
        }

        renderDriverCandidates() {
            const tbody = document.getElementById('tripControlDriverCandidatesGrid');
            if (!tbody) {
                return;
            }

            if (!this.driverCandidates.length) {
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="6">
                            <i class="fas fa-user-times"></i>
                            <p>No driver candidates found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = this.driverCandidates.map((driver) => {
                const isAvailable = !!driver.isAvailable;
                const capacityOk = !!driver.capacityOk;
                const canAssign = !!driver.canAssignWithoutForce;
                const isCurrent = !!driver.isCurrentAssigned;

                const availabilityLabel = isCurrent
                    ? `<span class="status-pill current">Current</span>`
                    : (canAssign
                        ? `<span class="status-pill available">Available</span>`
                        : `<span class="status-pill unavailable">Unavailable</span>`);

                const meta = [];
                if (!capacityOk) {
                    meta.push('capacity mismatch');
                }
                if (driver.firstConflictDate) {
                    meta.push(`conflict: ${this.formatDate(driver.firstConflictDate)}`);
                }

                return `
                    <tr>
                        <td>
                            ${this.escapeHtml(driver.driverName || '-')}
                            <span class="inline-meta">Rating: ${this.escapeHtml(this.roundNumber(driver.averageRating, 1))}</span>
                        </td>
                        <td>
                            ${this.escapeHtml(driver.vehicleModel || '-')}
                            <span class="inline-meta">${this.escapeHtml(driver.vehicleType || '-')}</span>
                        </td>
                        <td>${this.escapeHtml(driver.vehicleCapacity || '-')}</td>
                        <td>
                            ${availabilityLabel}
                            ${meta.length ? `<span class="inline-meta">${this.escapeHtml(meta.join(', '))}</span>` : ''}
                        </td>
                        <td>${this.formatCurrency(driver.totalChargePerDay)}</td>
                        <td class="actions-cell">
                            <button type="button" class="btn btn-primary btn-assign-driver" data-driver-id="${this.escapeHtml(driver.driverId)}" data-force="0" ${isCurrent ? 'disabled' : ''}>
                                Assign
                            </button>
                            <button type="button" class="btn btn-warning btn-assign-driver" data-driver-id="${this.escapeHtml(driver.driverId)}" data-force="1" ${isCurrent ? 'disabled' : ''}>
                                Manual Assign
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async assignDriver(driverId, forceAssign) {
            if (!this.currentTrip?.tripId) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/replaceTripDriver`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tripId: this.currentTrip.tripId,
                        driverId,
                        forceAssign
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to replace driver');
                }

                this.showMessage(data.message || 'Driver reassigned', 'success');
                this.closeModal(document.getElementById('tripControlDriverModal'));
                await this.refreshCurrentTrip();
            } catch (error) {
                this.showMessage(error.message || 'Failed to replace driver', 'error');
            }
        }

        async openGuideCandidatesModal(eventId) {
            if (!this.currentTrip?.tripId) {
                this.showMessage('Search and load a trip first', 'error');
                return;
            }

            this.currentGuideEventId = eventId;

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getAllGuidesForModerator`);
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load guides');
                }

                this.guideCandidates = data.guides || [];
                this.renderGuideCandidates();
                // Clear search input
                const searchInput = document.getElementById('tripControlGuideSearchInput');
                if (searchInput) {
                    searchInput.value = '';
                }
                this.openModal(document.getElementById('tripControlGuideModal'));
            } catch (error) {
                this.showMessage(error.message || 'Failed to load guides', 'error');
            }
        }

        renderGuideCandidates() {
            const tbody = document.getElementById('tripControlGuideCandidatesGrid');
            if (!tbody) {
                return;
            }

            if (!this.guideCandidates.length) {
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-user-times"></i>
                            <p>No guides found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = this.guideCandidates.map((guide) => {
                const fullName = `${this.escapeHtml(guide.firstName || '')} ${this.escapeHtml(guide.lastName || '')}`.trim();
                const searchData = `${guide.guideId} ${fullName} ${this.escapeHtml(guide.email || '')}`.toLowerCase();

                return `
                    <tr data-guide-search="${this.escapeHtml(searchData)}">
                        <td>${this.escapeHtml(guide.guideId)}</td>
                        <td>${this.escapeHtml(fullName)}</td>
                        <td>${this.escapeHtml(guide.email || '-')}</td>
                        <td>${this.escapeHtml(guide.experience || '-')}</td>
                        <td>${this.escapeHtml(guide.languages || '-')}</td>
                        <td>${guide.hourlyRate !== null ? this.formatCurrency(guide.hourlyRate) : '-'}</td>
                        <td class="actions-cell">
                            <button type="button" class="btn btn-primary btn-assign-guide" data-event-id="${this.escapeHtml(this.currentGuideEventId)}" data-guide-id="${this.escapeHtml(guide.guideId)}" data-force="1">
                                Replace
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async assignGuide(eventId, guideId, forceAssign) {
            if (!this.currentTrip?.tripId) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/replaceTripGuide`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tripId: this.currentTrip.tripId,
                        eventId,
                        guideId,
                        forceAssign
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to replace guide');
                }

                this.showMessage(data.message || 'Guide reassigned', 'success');
                this.closeModal(document.getElementById('tripControlGuideModal'));
                await this.refreshCurrentTrip();
            } catch (error) {
                this.showMessage(error.message || 'Failed to replace guide', 'error');
            }
        }

        async removeGuide(eventId) {
            if (!this.currentTrip?.tripId) {
                return;
            }

            if (!confirm('Are you sure you want to remove the guide from this event?')) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/removeTripGuide`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tripId: this.currentTrip.tripId,
                        eventId
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to remove guide');
                }

                this.showMessage(data.message || 'Guide removed from event', 'success');
                this.closeModal(document.getElementById('tripControlGuideModal'));
                await this.refreshCurrentTrip();
            } catch (error) {
                this.showMessage(error.message || 'Failed to remove guide', 'error');
            }
        }

        async removeGuideFromEvent(eventId) {
            if (!this.currentTrip?.tripId) {
                return;
            }

            if (!confirm('Are you sure you want to remove the guide from this event?')) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/removeTripGuide`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tripId: this.currentTrip.tripId,
                        eventId
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to remove guide');
                }

                this.showMessage(data.message || 'Guide removed from event', 'success');
                await this.refreshCurrentTrip();
            } catch (error) {
                this.showMessage(error.message || 'Failed to remove guide', 'error');
            }
        }

        resetView() {
            const input = document.getElementById('tripControlTripIdInput');
            const wrapper = document.getElementById('tripControlResultWrapper');
            const eventsGrid = document.getElementById('tripControlEventsGrid');
            const eventSearchInput = document.getElementById('tripControlEventSearchInput');

            if (input) {
                input.value = '';
            }
            if (eventSearchInput) {
                eventSearchInput.value = '';
            }
            if (wrapper) {
                wrapper.style.display = 'none';
            }
            if (eventsGrid) {
                eventsGrid.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            <p>Search a trip to view guide assignments</p>
                        </td>
                    </tr>
                `;
            }

            this.currentTrip = null;
            this.currentGuideEventId = null;
            this.driverCandidates = [];
            this.guideCandidates = [];
            this.clearMessage();
        }

        renderEmptyResults() {
            const wrapper = document.getElementById('tripControlResultWrapper');
            if (wrapper) {
                wrapper.style.display = 'none';
            }
        }

        openModal(modal) {
            if (!modal) {
                return;
            }
            modal.classList.add('show');
            modal.style.display = 'flex';
        }

        closeModal(modal) {
            if (!modal) {
                return;
            }
            modal.classList.remove('show');
            modal.style.display = 'none';
        }

        showMessage(message, type = 'info') {
            const container = document.getElementById('tripControlMessage');
            if (!container) {
                return;
            }
            container.innerHTML = `<div class="message ${this.escapeHtml(type)}">${this.escapeHtml(message)}</div>`;
        }

        clearMessage() {
            const container = document.getElementById('tripControlMessage');
            if (container) {
                container.innerHTML = '';
            }
        }

        formatDate(value) {
            if (!value) {
                return '-';
            }
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return this.escapeHtml(String(value));
            }
            return date.toLocaleDateString();
        }

        formatTime(value) {
            if (!value) {
                return '-';
            }
            const str = String(value);
            if (/^\d{2}:\d{2}(:\d{2})?$/.test(str)) {
                return str.slice(0, 5);
            }
            return this.escapeHtml(str);
        }

        formatCurrency(amount) {
            const num = Number(amount || 0);
            if (Number.isNaN(num)) {
                return '-';
            }
            return `LKR ${num.toFixed(2)}`;
        }

        roundNumber(value, digits) {
            const num = Number(value || 0);
            if (Number.isNaN(num)) {
                return '0.0';
            }
            return num.toFixed(digits);
        }

        humanizeStatus(status) {
            const map = {
                awPayment: 'Awaiting Payment',
                scheduled: 'Scheduled',
                ongoing: 'Ongoing',
                completed: 'Completed',
                cancelled: 'Cancelled',
                pending: 'Pending'
            };
            return map[status] || String(status || '-');
        }

        escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }

    window.closeTripControlDriverModal = function () {
        if (window.tripControlManager) {
            window.tripControlManager.closeModal(document.getElementById('tripControlDriverModal'));
        }
    };

    window.closeTripControlGuideModal = function () {
        if (window.tripControlManager) {
            window.tripControlManager.closeModal(document.getElementById('tripControlGuideModal'));
        }
    };


        window.TripControlManager = TripControlManager;
        window.tripControlManager = new TripControlManager();
  
})();
