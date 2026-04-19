(function() {
	if (window.regUserHomeDashboard && typeof window.regUserHomeDashboard.destroy === 'function') {
		window.regUserHomeDashboard.destroy();
		delete window.regUserHomeDashboard;
	}

	class RegUserHomeDashboard {
		constructor() {
			this.root = document.querySelector('.traveller-home-page');
			if (!this.root) {
				return;
			}

			this.urlRoot = this.root.dataset.urlRoot || `${window.location.origin}/test`;
			this.tripList = [];
			this.problemList = [];
			this.packageList = [];
			this.unreadMessageCount = 0;

			this.initializeElements();
			this.bindEvents();
			this.renderToday();
			this.loadDashboard();
		}

		initializeElements() {
			this.refreshButton = document.getElementById('homeRefreshBtn');

			this.totalTrips = document.getElementById('homeTotalTrips');
			this.activeTrips = document.getElementById('homeActiveTrips');
			this.pendingActions = document.getElementById('homePendingActions');
			this.unreadMessages = document.getElementById('homeUnreadMessages');
			this.openProblems = document.getElementById('homeOpenProblems');
			this.packageCount = document.getElementById('homePackageCount');

			this.upcomingCount = document.getElementById('homeHeroUpcomingCount');
			this.pendingPaymentCount = document.getElementById('homeHeroPaymentCount');

			this.tripListElement = document.getElementById('homeTripsList');
			this.tripEmptyElement = document.getElementById('homeTripsEmpty');

			this.problemListElement = document.getElementById('homeProblemsList');
			this.problemEmptyElement = document.getElementById('homeProblemsEmpty');

			this.packageHighlightsElement = document.getElementById('homePackageHighlights');
			this.todayDateElement = document.getElementById('homeTodayDate');
		}

		bindEvents() {
			this.handleRefreshClick = () => this.loadDashboard(true);

			if (this.refreshButton) {
				this.refreshButton.addEventListener('click', this.handleRefreshClick);
			}
		}

		destroy() {
			if (this.refreshButton && this.handleRefreshClick) {
				this.refreshButton.removeEventListener('click', this.handleRefreshClick);
			}
		}

		renderToday() {
			if (!this.todayDateElement) {
				return;
			}

			const today = new Date();
			this.todayDateElement.textContent = today.toLocaleDateString('en-GB', {
				weekday: 'short',
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			});
		}

		async loadDashboard(isManualRefresh = false) {
			if (isManualRefresh && this.refreshButton) {
				this.refreshButton.disabled = true;
				this.refreshButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
			}

			try {
				const [tripsResult, problemsResult, messagesResult, packagesResult] = await Promise.allSettled([
					this.fetchJson(`${this.urlRoot}/RegUser/getUserTrips`),
					this.fetchJson(`${this.urlRoot}/RegUser/getUserProblemsByUserId`),
					this.fetchJson(`${this.urlRoot}/helpc/getUnreadMessageCount`),
					this.fetchJson(`${this.urlRoot}/RegUser/getPackageCatalog`)
				]);

				this.tripList = this.getSafeArray(tripsResult, 'trips');
				this.problemList = this.getSafeArray(problemsResult, 'problems');
				this.packageList = this.getSafeArray(packagesResult, 'packages');
				this.unreadMessageCount = this.getSafeUnreadCount(messagesResult);

				this.renderSummary();
				this.renderTripBreakdown();
				this.renderTripsList();
				this.renderProblemsList();
				this.renderPackageHighlights();

				if (isManualRefresh && typeof window.showNotification === 'function') {
					window.showNotification('Home dashboard updated.', 'success');
				}
			} catch (error) {
				if (typeof window.showNotification === 'function') {
					window.showNotification('Failed to load home dashboard data.', 'error');
				}
			} finally {
				if (isManualRefresh && this.refreshButton) {
					this.refreshButton.disabled = false;
					this.refreshButton.innerHTML = '<i class="fa-solid fa-rotate"></i>';
				}
			}
		}

		async fetchJson(url) {
			const response = await fetch(url, {
				method: 'GET',
				headers: {
					'Accept': 'application/json'
				}
			});

			const data = await response.json();

			if (!response.ok) {
				throw new Error((data && data.message) || 'Request failed');
			}

			return data;
		}

		getSafeArray(result, key) {
			if (!result || result.status !== 'fulfilled') {
				return [];
			}

			const payload = result.value || {};
			const candidate = payload[key];
			return Array.isArray(candidate) ? candidate : [];
		}

		getSafeUnreadCount(result) {
			if (!result || result.status !== 'fulfilled') {
				return 0;
			}

			const payload = result.value || {};
			const count = Number(payload.unreadCount);
			return Number.isFinite(count) && count > 0 ? Math.floor(count) : 0;
		}

		renderSummary() {
			const grouped = this.groupTripsByStatus(this.tripList);
			const totalTrips = this.tripList.length;
			const activeTrips = grouped.ongoing + grouped.scheduled;
			const pendingActions = grouped.pending + grouped.wconfirmation + grouped.awpayment;
			const openProblems = this.problemList.filter((item) => {
				const status = String(item.status || '').toLowerCase();
				return status === 'pending' || status === 'in_progress';
			}).length;

			const upcomingTrips = this.getUpcomingTrips(this.tripList);

			this.setText(this.totalTrips, totalTrips);
			this.setText(this.activeTrips, activeTrips);
			this.setText(this.pendingActions, pendingActions);
			this.setText(this.unreadMessages, this.unreadMessageCount);
			this.setText(this.openProblems, openProblems);
			this.setText(this.packageCount, this.packageList.length);
			this.setText(this.upcomingCount, upcomingTrips.length);
			this.setText(this.pendingPaymentCount, grouped.awpayment);
		}

		renderTripBreakdown() {
			const grouped = this.groupTripsByStatus(this.tripList);
			const maxValue = Math.max(
				grouped.pending,
				grouped.wconfirmation,
				grouped.awpayment,
				grouped.scheduled,
				grouped.ongoing,
				grouped.completed,
				1
			);

			this.renderBreakdownRow('Pending', grouped.pending, maxValue);
			this.renderBreakdownRow('Wconfirmation', grouped.wconfirmation, maxValue);
			this.renderBreakdownRow('Awpayment', grouped.awpayment, maxValue);
			this.renderBreakdownRow('Scheduled', grouped.scheduled, maxValue);
			this.renderBreakdownRow('Ongoing', grouped.ongoing, maxValue);
			this.renderBreakdownRow('Completed', grouped.completed, maxValue);
		}

		renderBreakdownRow(labelKey, count, maxValue) {
			const countElement = document.getElementById(`homeStatus${labelKey}`);
			const barElement = document.getElementById(`homeStatus${labelKey}Bar`);

			if (countElement) {
				countElement.textContent = String(count);
			}

			if (barElement) {
				const width = Math.max(0, Math.round((count / maxValue) * 100));
				barElement.style.width = `${width}%`;
			}
		}

		renderTripsList() {
			if (!this.tripListElement || !this.tripEmptyElement) {
				return;
			}

			const upcoming = this.getUpcomingTrips(this.tripList);
			const fallbackRecent = this.getRecentTrips(this.tripList);
			const displayTrips = upcoming.length > 0 ? upcoming.slice(0, 5) : fallbackRecent.slice(0, 5);

			this.tripListElement.innerHTML = '';

			if (displayTrips.length === 0) {
				this.tripEmptyElement.style.display = 'block';
				return;
			}

			this.tripEmptyElement.style.display = 'none';

			displayTrips.forEach((trip) => {
				const tripStatus = this.normalizeTripStatus(trip.status);
				const statusLabel = this.getStatusLabel(tripStatus);

				const item = document.createElement('div');
				item.className = 'home-list-item';
				item.innerHTML = `
					<div>
						<p class="title">${this.escapeHtml(trip.tripTitle || 'Untitled Trip')}</p>
						<p class="meta">${this.escapeHtml(this.formatDateRange(trip.startDate, trip.endDate))}</p>
					</div>
					<span class="home-status-pill ${tripStatus}">${this.escapeHtml(statusLabel)}</span>
				`;

				this.tripListElement.appendChild(item);
			});
		}

		renderProblemsList() {
			if (!this.problemListElement || !this.problemEmptyElement) {
				return;
			}

			const sortedProblems = [...this.problemList].sort((left, right) => {
				const leftTime = Date.parse(left.createdAt || '');
				const rightTime = Date.parse(right.createdAt || '');

				if (Number.isNaN(leftTime) && Number.isNaN(rightTime)) {
					return 0;
				}

				if (Number.isNaN(leftTime)) {
					return 1;
				}

				if (Number.isNaN(rightTime)) {
					return -1;
				}

				return rightTime - leftTime;
			});

			const displayProblems = sortedProblems.slice(0, 4);
			this.problemListElement.innerHTML = '';

			if (displayProblems.length === 0) {
				this.problemEmptyElement.style.display = 'block';
				return;
			}

			this.problemEmptyElement.style.display = 'none';

			displayProblems.forEach((problem) => {
				const status = String(problem.status || '').toLowerCase();
				const statusClass = status === 'in_progress' ? 'in_progress' : status;
				const statusLabel = status === 'in_progress' ? 'In Progress' : this.capitalize(status || 'pending');

				const item = document.createElement('div');
				item.className = 'home-list-item';
				item.innerHTML = `
					<div>
						<p class="title">${this.escapeHtml(problem.subject || 'Support item')}</p>
						<p class="meta">${this.escapeHtml(this.formatDate(problem.createdAt))}</p>
					</div>
					<span class="home-status-pill ${this.escapeHtml(statusClass)}">${this.escapeHtml(statusLabel)}</span>
				`;

				this.problemListElement.appendChild(item);
			});
		}

		renderPackageHighlights() {
			if (!this.packageHighlightsElement) {
				return;
			}

			this.packageHighlightsElement.innerHTML = '';

			if (this.packageList.length === 0) {
				this.packageHighlightsElement.innerHTML = '<p class="home-empty">No package highlights available right now.</p>';
				return;
			}

			const topPackages = this.packageList.slice(0, 4);
			topPackages.forEach((pkg) => {
				const highlight = document.createElement('div');
				highlight.className = 'home-highlight-pill';
				highlight.textContent = pkg.packageName || 'Unnamed package';
				this.packageHighlightsElement.appendChild(highlight);
			});
		}

		groupTripsByStatus(trips) {
			const bucket = {
				pending: 0,
				wconfirmation: 0,
				awpayment: 0,
				scheduled: 0,
				ongoing: 0,
				completed: 0
			};

			trips.forEach((trip) => {
				const status = this.normalizeTripStatus(trip.status);
				if (Object.prototype.hasOwnProperty.call(bucket, status)) {
					bucket[status] += 1;
				}
			});

			return bucket;
		}

		getUpcomingTrips(trips) {
			const todayStart = new Date();
			todayStart.setHours(0, 0, 0, 0);
			const todayEpoch = todayStart.getTime();

			return trips
				.filter((trip) => {
					const status = this.normalizeTripStatus(trip.status);
					if (status === 'completed' || status === 'cancelled' || status === 'canceled') {
						return false;
					}

					const startTime = Date.parse(trip.startDate || '');
					if (Number.isNaN(startTime)) {
						return true;
					}

					return startTime >= todayEpoch;
				})
				.sort((left, right) => this.getTripSortTime(left, 'start') - this.getTripSortTime(right, 'start'));
		}

		getRecentTrips(trips) {
			return [...trips].sort((left, right) => this.getTripSortTime(right, 'updated') - this.getTripSortTime(left, 'updated'));
		}

		getTripSortTime(trip, field) {
			const source = field === 'start'
				? (trip.startDate || trip.createdAt || trip.updatedAt)
				: (trip.updatedAt || trip.createdAt || trip.startDate);

			const value = Date.parse(source || '');
			return Number.isNaN(value) ? 0 : value;
		}

		normalizeTripStatus(status) {
			const safeStatus = String(status || '').trim().toLowerCase();

			if (safeStatus === 'wconfirmation') {
				return 'wconfirmation';
			}

			if (safeStatus === 'awpayment') {
				return 'awpayment';
			}

			return safeStatus;
		}

		getStatusLabel(statusKey) {
			const map = {
				pending: 'Pending',
				wconfirmation: 'Waiting Confirmation',
				awpayment: 'Awaiting Payment',
				scheduled: 'Scheduled',
				ongoing: 'Ongoing',
				completed: 'Completed'
			};

			return map[statusKey] || this.capitalize(statusKey);
		}

		formatDateRange(startDate, endDate) {
			const start = this.formatDate(startDate);
			const end = this.formatDate(endDate);

			if (!start && !end) {
				return 'Date not set';
			}

			if (start && end) {
				return `${start} - ${end}`;
			}

			return start || end;
		}

		formatDate(value) {
			const time = Date.parse(value || '');
			if (Number.isNaN(time)) {
				return '';
			}

			const date = new Date(time);
			return date.toLocaleDateString('en-GB', {
				day: '2-digit',
				month: 'short',
				year: 'numeric'
			});
		}

		setText(element, value) {
			if (!element) {
				return;
			}

			element.textContent = String(value);
		}

		capitalize(value) {
			const text = String(value || '').trim();
			if (!text) {
				return '';
			}

			return text.charAt(0).toUpperCase() + text.slice(1).replace('_', ' ');
		}

		escapeHtml(value) {
			const text = String(value || '');
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	}

	window.regUserHomeDashboard = new RegUserHomeDashboard();
})();
