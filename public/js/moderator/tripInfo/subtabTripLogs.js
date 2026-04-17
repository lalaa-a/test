(function(){
	if (window.TripLogsManager) {
		if (window.tripLogsManager) {
			delete window.tripLogsManager;
		}
		delete window.TripLogsManager;
	}

	class TripLogsManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.trips = [];
			this.statuses = ['awPayment', 'scheduled', 'ongoing', 'completed', 'cancelled'];
			this.init();
		}

		init() {
			this.bindEvents();
			this.loadTripLogs();
		}

		bindEvents() {
			// Section navigation
			document.querySelectorAll('.nav-link').forEach((link) => {
				link.addEventListener('click', (e) => {
					e.preventDefault();
					this.switchSection(link.dataset.section);
				});
			});

			// Search inputs
			const searchInputs = ['awPaymentSearchInput', 'scheduledSearchInput', 'ongoingSearchInput', 'completedSearchInput', 'cancelledSearchInput'];
			searchInputs.forEach(inputId => {
				const input = document.getElementById(inputId);
				if (input) {
					input.addEventListener('input', (e) => {
						const section = inputId.replace('SearchInput', '');
						this.filterTrips(section, e.target.value);
					});
				}
			});

			// Modal close
			document.querySelectorAll('.modal-close').forEach(btn => {
				btn.addEventListener('click', () => this.closeModal(btn.closest('.modal')));
			});

			// Modal overlays
			document.querySelectorAll('.modal').forEach(modal => {
				modal.addEventListener('click', (e) => {
					if (e.target === modal) {
						this.closeModal(modal);
					}
				});
			});

			// Trip row clicks
			document.addEventListener('click', (e) => {
				if (e.target.closest('.btn-view')) {
					const button = e.target.closest('.btn-view');
					const tripId = button.dataset.tripId;
					if (tripId) {
						this.showTripDetails(tripId);
					}
				}
			});

			// ESC key to close modal
			document.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					this.closeModal(document.getElementById('tripDetailsModal'));
				}
			});
		}

		async loadTripLogs() {
			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getTripLogs`);
				const data = await response.json();

				if (!response.ok || !data.success) {
					this.showAlert(data.message || 'Failed to load trip logs', 'error');
					return;
				}

				this.trips = data.trips || [];
				this.updateStats();
				this.renderAllSections();
				this.showAlert(`Loaded ${this.trips.length} trip log(s)`, 'success');

			} catch (error) {
				this.showAlert('Failed to fetch trip logs', 'error');
			}
		}

		switchSection(sectionName) {
			document.querySelectorAll('.nav-link').forEach((link) => {
				link.classList.toggle('active', link.dataset.section === sectionName);
			});

			document.querySelectorAll('.verification-section').forEach((section) => {
				section.style.display = 'none';
			});

			const target = document.getElementById(`${sectionName}-section`);
			if (target) {
				target.style.display = 'block';
			}
		}

		filterTrips(section, searchTerm) {
			const targetGrid = section === 'awPayment' ? 'awPaymentTripGrid' :
							 section === 'scheduled' ? 'scheduledTripGrid' :
							 section === 'ongoing' ? 'ongoingTripGrid' :
							 section === 'completed' ? 'completedTripGrid' : 'cancelledTripGrid';

			const tbody = document.getElementById(targetGrid);
			const rows = tbody.querySelectorAll('tr:not(.no-accounts)');

			if (!searchTerm.trim()) {
				rows.forEach(row => row.style.display = '');
				return;
			}

			const term = searchTerm.toLowerCase();
			rows.forEach(row => {
				const text = row.textContent.toLowerCase();
				row.style.display = text.includes(term) ? '' : 'none';
			});
		}

		updateStats() {
			const counts = {
				awPayment: 0,
				scheduled: 0,
				ongoing: 0,
				completed: 0,
				cancelled: 0
			};

			this.trips.forEach((trip) => {
				if (Object.prototype.hasOwnProperty.call(counts, trip.status)) {
					counts[trip.status] += 1;
				}
			});

			this.setText('awPaymentCount', counts.awPayment);
			this.setText('scheduledCount', counts.scheduled);
			this.setText('ongoingCount', counts.ongoing);
			this.setText('completedCount', counts.completed);
			this.setText('cancelledCount', counts.cancelled);
		}

		renderAllSections() {
			this.statuses.forEach((status) => {
				this.renderStatusSection(status);
			});
		}

		renderStatusSection(status) {
			const targetGrid = status === 'awPayment' ? 'awPaymentTripGrid' :
							 status === 'scheduled' ? 'scheduledTripGrid' :
							 status === 'ongoing' ? 'ongoingTripGrid' :
							 status === 'completed' ? 'completedTripGrid' : 'cancelledTripGrid';

			const tbody = document.getElementById(targetGrid);

			if (!tbody) {
				return;
			}

			const statusTrips = this.trips.filter((trip) => trip.status === status);

			if (statusTrips.length === 0) {
				const icon = status === 'awPayment' ? 'clock' :
							status === 'scheduled' ? 'calendar' :
							status === 'ongoing' ? 'play' :
							status === 'completed' ? 'check-circle' : 'times-circle';
				const message = status === 'awPayment' ? 'awaiting payment' :
							   status === 'scheduled' ? 'scheduled' :
							   status === 'ongoing' ? 'ongoing' :
							   status === 'completed' ? 'completed' : 'cancelled';
				tbody.innerHTML = `
					<tr class="no-accounts">
						<td colspan="7">
							<i class="fas fa-${icon}"></i>
							<p>No ${message} trips</p>
						</td>
					</tr>
				`;
				return;
			}

			tbody.innerHTML = statusTrips.map((trip) => this.createTripRow(trip)).join('');
		}

		createTripRow(trip) {
			const startDate = this.formatDate(trip.startDate);
			const endDate = this.formatDate(trip.endDate);

			return `
				<tr class="trip-row" data-trip-id="${this.escapeHtml(trip.tripId || '-')}">
					<td class="trip-id-cell">${this.escapeHtml(trip.tripId || '-')}</td>
					<td class="trip-title-cell">${this.escapeHtml(trip.tripTitle || 'Untitled Trip')}</td>
					<td class="traveller-cell">${this.escapeHtml(trip.travellerName || '-')}</td>
					<td class="people-cell">${this.escapeHtml(trip.numberOfPeople || '-')}</td>
					<td class="start-date-cell">${startDate}</td>
					<td class="end-date-cell">${endDate}</td>
					<td class="actions-cell">
						<button class="btn btn-view" data-trip-id="${this.escapeHtml(trip.tripId || '-')}">
							<i class="fas fa-eye"></i>
							View
						</button>
					</td>
				</tr>
			`;
		}

		async showTripDetails(tripId) {
			try {
				const trip = this.trips.find(t => t.tripId == tripId);
				if (!trip) {
					this.showAlert('Trip not found', 'error');
					return;
				}

				this.renderTripDetails(trip);
				this.openModal(document.getElementById('tripDetailsModal'));
			} catch (error) {
				this.showAlert('Failed to load trip details', 'error');
			}
		}

		renderTripDetails(trip) {
			const drivers = Array.isArray(trip.drivers) ? trip.drivers : [];
			const guides = Array.isArray(trip.guides) ? trip.guides : [];

			document.getElementById('tripDetailsContent').innerHTML = `
				<div class="trip-details-grid">
					<div class="trip-profile-section">
						<div class="trip-status-icon">
							<i class="fas fa-route"></i>
						</div>
						<h3>${this.escapeHtml(trip.tripTitle || 'Untitled Trip')}</h3>
						<p class="trip-status-badge">
							<span class="status-badge ${trip.status}">${this.humanizeStatus(trip.status)}</span>
						</p>
					</div>
					<div class="trip-info-section">
						<div class="info-group">
							<h4>Basic Information</h4>
							<div class="info-item">
								<label>Trip ID:</label>
								<span>${this.escapeHtml(trip.tripId || '-')}</span>
							</div>
							<div class="info-item">
								<label>Title:</label>
								<span>${this.escapeHtml(trip.tripTitle || 'Untitled Trip')}</span>
							</div>
							<div class="info-item">
								<label>Traveller:</label>
								<span>${this.escapeHtml(trip.travellerName || '-')}</span>
							</div>
							<div class="info-item">
								<label>Number of People:</label>
								<span>${this.escapeHtml(trip.numberOfPeople || '-')}</span>
							</div>
							<div class="info-item">
								<label>Start Date:</label>
								<span>${this.formatDate(trip.startDate)}</span>
							</div>
							<div class="info-item">
								<label>End Date:</label>
								<span>${this.formatDate(trip.endDate)}</span>
							</div>
						</div>

						<div class="info-group">
							<h4>Assigned Drivers</h4>
							${drivers.length ? drivers.map(driver => `
								<div class="assignment-item">
									<div class="assignment-header">
										<i class="fas fa-car"></i>
										<strong>${this.escapeHtml(driver.driverName || '-')}</strong>
										<span class="assignment-id">(ID: ${this.escapeHtml(driver.driverId || '-')})</span>
									</div>
									<div class="assignment-details">
										<div class="detail-row">
											<label>Vehicle:</label>
											<span>${this.escapeHtml(driver.vehicleModel || '-')} (${this.escapeHtml(driver.vehicleType || '-')})</span>
										</div>
										<div class="detail-row">
											<label>Vehicle ID:</label>
											<span>${this.escapeHtml(driver.vehicleId || '-')}</span>
										</div>
									</div>
								</div>
							`).join('') : '<p class="no-data">No assigned drivers</p>'}
						</div>

						<div class="info-group">
							<h4>Assigned Guides</h4>
							${guides.length ? guides.map(guide => `
								<div class="assignment-item">
									<div class="assignment-header">
										<i class="fas fa-map-marked-alt"></i>
										<strong>${this.escapeHtml(guide.guideFullName || '-')}</strong>
										<span class="assignment-id">(ID: ${this.escapeHtml(guide.guideId || '-')})</span>
									</div>
									<div class="assignment-details">
										<div class="detail-row">
											<label>Event ID:</label>
											<span>${this.escapeHtml(guide.eventId || '-')}</span>
										</div>
										<div class="detail-row">
											<label>Travel Spot:</label>
											<span>${this.escapeHtml(guide.spotName || '-')} (ID: ${this.escapeHtml(guide.travelSpotId || '-')})</span>
										</div>
									</div>
								</div>
							`).join('') : '<p class="no-data">No assigned guides</p>'}
						</div>
					</div>
				</div>
			`;
		}

		openModal(modal) {
			if (modal) {
				modal.style.display = 'flex';
				modal.classList.add('show');
			}
		}

		closeModal(modal) {
			if (modal) {
				modal.style.display = 'none';
				modal.classList.remove('show');
			}
		}

		humanizeStatus(status) {
			const map = {
				awPayment: 'Awaiting Payment',
				scheduled: 'Scheduled',
				ongoing: 'Ongoing',
				completed: 'Completed',
				cancelled: 'Cancelled'
			};
			return map[status] || String(status || '-');
		}

		setText(id, value) {
			const el = document.getElementById(id);
			if (el) {
				el.textContent = String(value);
			}
		}

		showAlert(message, type) {
			// For now, just log to console since we removed the alert div
			console.log(`${type.toUpperCase()}: ${message}`);
		}

		formatDate(value) {
			if (!value) {
				return '-';
			}
			const date = new Date(value);
			if (Number.isNaN(date.getTime())) {
				return String(value);
			}
			return date.toLocaleDateString();
		}

		escapeHtml(str) {
			return String(str)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}
	}

	// Global functions for modal
	window.closeTripModal = function() {
		if (window.tripLogsManager) {
			window.tripLogsManager.closeModal(document.getElementById('tripDetailsModal'));
		}
	};

	const initializeTripLogsManager = function() {
		window.TripLogsManager = TripLogsManager;
		window.tripLogsManager = new TripLogsManager();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeTripLogsManager);
	} else {
		initializeTripLogsManager();
	}
})();
