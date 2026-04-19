(function () {
	class OngoingTripManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';

			this.root = document.querySelector('.ongoing-trip-page');
			if (!this.root) {
				return;
			}

			this.tripId = Number(this.root.dataset.tripId || 0);
			this.selectedDate = this.getInitialSelectedDate();

			this.trip = null;
			this.featureFlags = {};
			this.allEvents = [];
			this.currentDateEvents = [];

			this.eventListElement = document.getElementById('ongoing-event-list');
			this.dateGrid = document.getElementById('ongoing-date-grid');
			this.selectedDateLabel = document.getElementById('selected-date-label');
			this.startPinValue = document.getElementById('start-pin-value');
			this.startPinHint = document.getElementById('start-pin-hint');
			this.startPinToggle = document.getElementById('start-pin-toggle');
			this.completionSummary = document.getElementById('completion-summary');
			this.mapElement = document.getElementById('ongoing-route-map');
			this.mapEmptyState = document.getElementById('map-empty-state');

			this.rawStartPin = '';
			this.startPinVisible = false;

			this.routeMap = null;
			this.routeMarkers = [];
			this.routePath = null;
			this.mapReady = false;

			this.bindDatePills();
			this.bindEventActions();
			this.bindStartPinToggle();

			this.loadSnapshot();
		}

		getInitialSelectedDate() {
			const activeButton = document.querySelector('.date-pill.active');
			return activeButton ? activeButton.dataset.date : null;
		}

		bindDatePills() {
			if (!this.dateGrid) {
				return;
			}

			this.dateGrid.addEventListener('click', (event) => {
				const button = event.target.closest('.date-pill');
				if (!button) {
					return;
				}

				const date = button.dataset.date;
				if (!date || date === this.selectedDate) {
					return;
				}

				this.dateGrid.querySelectorAll('.date-pill').forEach((item) => {
					item.classList.remove('active');
				});
				button.classList.add('active');

				this.selectedDate = date;
				this.loadDateEvents(date);
			});
		}

		bindEventActions() {
			if (!this.eventListElement) {
				return;
			}

			this.eventListElement.addEventListener('click', (event) => {
				const actionButton = event.target.closest('[data-action="mark-done"]');
				if (!actionButton) {
					return;
				}

				const eventId = Number(actionButton.dataset.eventId || 0);
				const target = String(actionButton.dataset.target || '').toLowerCase();

				if (!eventId || !target) {
					return;
				}

				this.markEventDone(eventId, target, actionButton);
			});
		}

		bindStartPinToggle() {
			if (!this.startPinToggle) {
				return;
			}

			this.startPinToggle.addEventListener('click', () => {
				if (!this.rawStartPin) {
					return;
				}

				this.startPinVisible = !this.startPinVisible;
				this.updateStartPinDisplay();
			});
		}

		async loadSnapshot() {
			if (!this.tripId) {
				this.renderState('Invalid trip ID.');
				return;
			}

			try {
				const response = await fetch(`${this.URL_ROOT}/tripMarker/tripSnapshot/${this.tripId}`);
				const data = await response.json();

				if (!data.success) {
					this.renderState(data.message || 'Unable to load trip data.');
					return;
				}

				this.trip = data.trip || null;
				this.featureFlags = data.featureFlags || {};
				this.allEvents = Array.isArray(data.events) ? data.events : [];

				this.renderStartPin();
				this.renderCompletionSummary();

				if (!this.selectedDate) {
					this.selectedDate = this.resolveFallbackDate();
				}

				if (this.selectedDate) {
					this.activateDatePill(this.selectedDate);
					this.loadDateEvents(this.selectedDate);
				} else {
					this.renderState('No timeline dates are available for this trip.');
				}
			} catch (error) {
				console.error('Failed to load trip snapshot:', error);
				this.renderState('Failed to load ongoing trip data.');
			}
		}

		resolveFallbackDate() {
			if (this.trip && this.trip.startDate) {
				return this.trip.startDate;
			}

			const firstButton = this.dateGrid ? this.dateGrid.querySelector('.date-pill') : null;
			return firstButton ? firstButton.dataset.date : null;
		}

		activateDatePill(date) {
			if (!this.dateGrid) {
				return;
			}

			this.dateGrid.querySelectorAll('.date-pill').forEach((button) => {
				button.classList.toggle('active', button.dataset.date === date);
			});
		}

		async loadDateEvents(date) {
			if (!date) {
				this.renderState('No date selected.');
				return;
			}

			this.renderLoadingState();
			this.updateSelectedDateLabel(date);

			try {
				const response = await fetch(`${this.URL_ROOT}/tripMarker/getEventCardsByDate/${this.tripId}/${date}`);
				const data = await response.json();

				if (!data.success) {
					this.renderState(data.message || 'Unable to load events for selected date.');
					this.renderMap([]);
					return;
				}

				this.currentDateEvents = Array.isArray(data.eventCards) ? data.eventCards : [];
				this.renderEvents(this.currentDateEvents);

				const coordinates = Array.isArray(data.coordinates) ? data.coordinates : [];
				this.renderMap(coordinates);
			} catch (error) {
				console.error('Failed to load date events:', error);
				this.renderState('Failed to load events for selected date.');
				this.renderMap([]);
			}
		}

		updateSelectedDateLabel(dateString) {
			if (!this.selectedDateLabel) {
				return;
			}

			const date = new Date(dateString);
			if (Number.isNaN(date.getTime())) {
				this.selectedDateLabel.textContent = dateString;
				return;
			}

			this.selectedDateLabel.textContent = date.toLocaleDateString(undefined, {
				weekday: 'short',
				year: 'numeric',
				month: 'short',
				day: 'numeric'
			});
		}

		renderLoadingState() {
			if (!this.eventListElement) {
				return;
			}

			this.eventListElement.innerHTML = '<div class="state-block loading-state">Loading event timeline...</div>';
		}

		renderState(message) {
			if (!this.eventListElement) {
				return;
			}

			this.eventListElement.innerHTML = `<div class="state-block">${this.escapeHtml(message)}</div>`;
		}

		renderEvents(events) {
			if (!this.eventListElement) {
				return;
			}

			if (!events || events.length === 0) {
				this.eventListElement.innerHTML = '<div class="state-block">No events added for this date.</div>';
				return;
			}

			this.eventListElement.innerHTML = events.map((event) => this.renderEventCard(event)).join('');
		}

		renderEventCard(event) {
			const title = this.escapeHtml(event.eventTitle || 'Trip Event');
			const description = this.escapeHtml(
				event.description || event.travelSpotOverview || 'No description available for this event.'
			);
			const location = this.escapeHtml(event.mapName || event.locationName || event.travelSpotName || 'Location unavailable');
			const eventType = this.escapeHtml(this.formatType(event.eventType));
			const eventStatus = this.escapeHtml(this.formatStatus(event.eventStatus));
			const timeRange = this.escapeHtml(this.formatTimeRange(event.startTime, event.endTime));

			return `
				<article class="event-item">
					<div class="event-top">
						<div>
							<h4 class="event-title">${title}</h4>
							<div class="event-time">${timeRange}</div>
						</div>
						<div class="event-tags">
							<span class="chip chip-type">${eventType}</span>
							<span class="chip chip-status">${eventStatus}</span>
						</div>
					</div>
					<p class="event-description">${description}</p>
					<div class="event-meta">
						<span><i class="fas fa-map-marker-alt"></i>${location}</span>
						<span><i class="fas fa-calendar-alt"></i>${this.escapeHtml(this.formatDate(event.eventDate))}</span>
					</div>
					<div class="done-grid">
						${this.renderDoneBlock(event, 'driver')}
						${this.renderDoneBlock(event, 'guide')}
					</div>
				</article>
			`;
		}

		renderDoneBlock(event, target) {
			const providerLabel = target === 'driver' ? 'Driver' : 'Guide';
			const hasAssignment = target === 'driver'
				? !!event.hasDriverAssignment
				: !!event.hasGuideAssignment;

			const providerDone = target === 'driver'
				? !!event.dDone
				: !!event.gDone;

			const travellerDone = target === 'driver'
				? !!event.tDoneDriver
				: !!event.tDoneGuide;

			const hasTravellerFlag = target === 'driver'
				? !!this.featureFlags.hasTravellerDriverFlag
				: !!this.featureFlags.hasTravellerGuideFlag;

			const hasProviderFlag = target === 'driver'
				? !!this.featureFlags.hasDriverDoneFlag
				: !!this.featureFlags.hasGuideDoneFlag;

			const pinMatched = !this.featureFlags.hasPinMatch || (this.trip && Number(this.trip.pinMatch) === 1);

			let statusLine = `<span class="status-chip-line status-muted"><i class="fas fa-info-circle"></i>${providerLabel} tracking unavailable</span>`;
			let buttonText = 'Not Available';
			let disabled = true;

			if (hasTravellerFlag) {
				if (!hasAssignment) {
					statusLine = `<span class="status-chip-line status-muted"><i class="fas fa-user-slash"></i>No accepted ${providerLabel.toLowerCase()} assigned</span>`;
					buttonText = 'No Assignment';
				} else if (travellerDone) {
					statusLine = '<span class="status-chip-line status-success"><i class="fas fa-check-circle"></i>Traveller confirmation completed</span>';
					buttonText = 'Confirmed';
				} else if (this.trip && this.trip.startPinRequired && !this.trip.startPin) {
					statusLine = '<span class="status-chip-line status-pending"><i class="fas fa-lock"></i>Start PIN required before confirmations</span>';
					buttonText = 'Blocked';
				} else if (!pinMatched) {
					statusLine = '<span class="status-chip-line status-pending"><i class="fas fa-key"></i>Waiting for driver PIN match</span>';
					buttonText = 'Waiting';
				} else if (hasProviderFlag && !providerDone) {
					statusLine = `<span class="status-chip-line status-pending"><i class="fas fa-hourglass-half"></i>Waiting for ${providerLabel.toLowerCase()} done mark</span>`;
					buttonText = 'Waiting';
				} else {
					statusLine = `<span class="status-chip-line status-success"><i class="fas fa-circle-check"></i>Ready for traveller confirmation</span>`;
					buttonText = 'Mark Done';
					disabled = false;
				}
			}

			const guideNameNote = (target === 'guide' && event.guideFullName)
				? `<div class="done-state">Assigned: ${this.escapeHtml(event.guideFullName)}</div>`
				: '';

			return `
				<div class="done-block">
					<span class="done-label">Traveller to ${providerLabel} completion</span>
					${guideNameNote}
					<div class="done-state">${statusLine}</div>
					<button
						type="button"
						class="mark-done-btn"
						data-action="mark-done"
						data-event-id="${Number(event.eventId)}"
						data-target="${target}"
						${disabled ? 'disabled' : ''}>
						${buttonText}
					</button>
				</div>
			`;
		}

		async markEventDone(eventId, target, button) {
			if (!button) {
				return;
			}

			button.disabled = true;
			const originalText = button.textContent;
			button.textContent = 'Saving...';

			try {
				const response = await fetch(`${this.URL_ROOT}/tripMarker/markEventDone`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						tripId: this.tripId,
						eventId,
						target
					})
				});

				const data = await response.json();
				if (!data.success) {
					this.notify(data.message || 'Failed to mark event done.', 'error');
					button.disabled = false;
					button.textContent = originalText;
					return;
				}

				this.notify(data.message || 'Event marked done successfully.', 'success');
				await this.loadSnapshot();
			} catch (error) {
				console.error('Failed to mark event done:', error);
				this.notify('Failed to mark event done.', 'error');
				button.disabled = false;
				button.textContent = originalText;
			}
		}

		renderStartPin() {
			if (!this.startPinValue || !this.startPinHint || !this.trip) {
				return;
			}

			this.rawStartPin = '';
			this.startPinVisible = false;

			if (!this.featureFlags.hasStartPin) {
				this.startPinValue.textContent = 'Not configured';
				this.startPinHint.textContent = 'Trip start PIN column is not available in this environment.';
				this.setStartPinToggleState(false, 'N/A');
				return;
			}

			if (this.trip.startPin) {
				this.rawStartPin = String(this.trip.startPin);
				this.updateStartPinDisplay();
				this.setStartPinToggleState(true, 'View PIN');

				if (this.featureFlags.hasPinMatch && Number(this.trip.pinMatch) === 1) {
					this.startPinHint.textContent = 'Driver has matched this start PIN. Confirmations can continue when provider done marks are ready.';
					return;
				}

				this.startPinHint.textContent = this.trip.startPinGenerated
					? 'A new start PIN was generated for this ongoing trip. Share it with the driver to start the trip.'
					: 'Share this start PIN with the driver and ask them to match it before traveller confirmations.';
				return;
			}

			if (this.trip.startPinRequired) {
				this.startPinValue.textContent = 'Pending';
				this.startPinHint.textContent = 'Start PIN is required before traveller confirmations can be marked.';
				this.setStartPinToggleState(false, 'Pending');
				return;
			}

			this.startPinValue.textContent = 'Not required';
			this.startPinHint.textContent = 'Start PIN is only required during the active trip window.';
			this.setStartPinToggleState(false, 'N/A');
		}

		setStartPinToggleState(isEnabled, label) {
			if (!this.startPinToggle) {
				return;
			}

			this.startPinToggle.disabled = !isEnabled;
			this.startPinToggle.textContent = label;
		}

		updateStartPinDisplay() {
			if (!this.startPinValue || !this.startPinToggle) {
				return;
			}

			if (!this.rawStartPin) {
				this.startPinValue.textContent = 'Pending';
				return;
			}

			this.startPinValue.textContent = this.startPinVisible ? this.rawStartPin : '******';
			this.startPinToggle.textContent = this.startPinVisible ? 'Hide PIN' : 'View PIN';
		}

		renderCompletionSummary() {
			if (!this.completionSummary) {
				return;
			}

			const allEvents = Array.isArray(this.allEvents) ? this.allEvents : [];

			const driverEvents = allEvents.filter((event) => !!event.hasDriverAssignment);
			const guideEvents = allEvents.filter((event) => !!event.hasGuideAssignment);

			const driverDoneCount = driverEvents.filter((event) => !!event.tDoneDriver).length;
			const guideDoneCount = guideEvents.filter((event) => !!event.tDoneGuide).length;

			const summaryParts = [];
			summaryParts.push(`Driver confirmations: ${driverDoneCount}/${driverEvents.length}`);
			summaryParts.push(`Guide confirmations: ${guideDoneCount}/${guideEvents.length}`);

			if (driverEvents.length === 0 && guideEvents.length === 0) {
				summaryParts.push('No accepted provider assignments found for this trip yet.');
			}

			this.completionSummary.textContent = summaryParts.join(' | ');
		}

		async ensureMapReady() {
			if (this.mapReady && this.routeMap) {
				return true;
			}

			if (!this.mapElement) {
				return false;
			}

			try {
				await this.waitForGoogleMaps();

				this.routeMap = new google.maps.Map(this.mapElement, {
					center: { lat: 7.8731, lng: 80.7718 },
					zoom: 8,
					mapTypeControl: false,
					streetViewControl: false
				});

				this.mapReady = true;
				return true;
			} catch (error) {
				console.error('Google Maps initialization failed:', error);
				this.mapReady = false;
				return false;
			}
		}

		async renderMap(coordinates) {
			const mapReady = await this.ensureMapReady();
			if (!mapReady) {
				if (this.mapEmptyState) {
					this.mapEmptyState.style.display = 'block';
					this.mapEmptyState.textContent = 'Google Maps is unavailable at the moment.';
				}
				return;
			}

			this.clearMapOverlays();

			if (!coordinates || coordinates.length === 0) {
				if (this.mapEmptyState) {
					this.mapEmptyState.style.display = 'block';
					this.mapEmptyState.textContent = 'Map points are not available for this date.';
				}
				return;
			}

			if (this.mapEmptyState) {
				this.mapEmptyState.style.display = 'none';
			}

			const bounds = new google.maps.LatLngBounds();
			const path = [];

			coordinates.forEach((point, index) => {
				const lat = Number(point.lat);
				const lng = Number(point.lng);
				if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
					return;
				}

				const position = { lat, lng };
				path.push(position);
				bounds.extend(position);

				const marker = new google.maps.Marker({
					map: this.routeMap,
					position,
					title: point.name || `Stop ${index + 1}`,
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
					content: `<div style="padding:4px 6px; font-size:12px;">${this.escapeHtml(point.name || `Stop ${index + 1}`)}</div>`
				});

				marker.addListener('click', () => {
					infoWindow.open(this.routeMap, marker);
				});

				this.routeMarkers.push(marker);
			});

			if (path.length >= 2) {
				this.routePath = new google.maps.Polyline({
					path,
					geodesic: true,
					strokeColor: '#006a71',
					strokeOpacity: 0.75,
					strokeWeight: 4,
					map: this.routeMap
				});
			}

			if (path.length === 1) {
				this.routeMap.setCenter(path[0]);
				this.routeMap.setZoom(12);
			} else if (!bounds.isEmpty()) {
				this.routeMap.fitBounds(bounds, 60);
			}
		}

		clearMapOverlays() {
			this.routeMarkers.forEach((marker) => marker.setMap(null));
			this.routeMarkers = [];

			if (this.routePath) {
				this.routePath.setMap(null);
				this.routePath = null;
			}
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

		formatTimeRange(startTime, endTime) {
			const start = this.formatTime(startTime);
			const end = this.formatTime(endTime);

			if (start && end) {
				return `${start} - ${end}`;
			}

			return start || end || 'Time not set';
		}

		formatTime(timeValue) {
			if (!timeValue) {
				return '';
			}

			const timeString = String(timeValue).slice(0, 5);
			const [hourPart, minutePart] = timeString.split(':');
			const hours = Number(hourPart);
			const minutes = Number(minutePart);

			if (!Number.isFinite(hours) || !Number.isFinite(minutes)) {
				return timeString;
			}

			const date = new Date();
			date.setHours(hours, minutes, 0, 0);

			return date.toLocaleTimeString(undefined, {
				hour: '2-digit',
				minute: '2-digit'
			});
		}

		formatDate(dateValue) {
			const date = new Date(dateValue);
			if (Number.isNaN(date.getTime())) {
				return String(dateValue || '-');
			}

			return date.toLocaleDateString(undefined, {
				weekday: 'short',
				year: 'numeric',
				month: 'short',
				day: 'numeric'
			});
		}

		formatType(type) {
			if (!type) {
				return 'Event';
			}

			if (String(type).toLowerCase() === 'travelspot') {
				return 'Travel Spot';
			}

			return 'Location';
		}

		formatStatus(status) {
			const normalized = String(status || '').toLowerCase();
			if (normalized === 'start') {
				return 'Start';
			}
			if (normalized === 'intermediate') {
				return 'Intermediate';
			}
			if (normalized === 'end') {
				return 'End';
			}
			return 'Event';
		}

		escapeHtml(value) {
			const div = document.createElement('div');
			div.textContent = String(value || '');
			return div.innerHTML;
		}

		notify(message, type = 'info') {
			if (typeof window.showNotification === 'function') {
				window.showNotification(message, type);
				return;
			}

			window.alert(message);
		}
	}

	window.ongoingTripManager = new OngoingTripManager();
})();

