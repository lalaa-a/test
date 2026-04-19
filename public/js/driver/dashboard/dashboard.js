(function () {
	if (window.DriverDashboardManager) {
		if (window.driverDashboardManager && typeof window.driverDashboardManager.destroy === 'function') {
			window.driverDashboardManager.destroy();
		}
		delete window.driverDashboardManager;
		delete window.DriverDashboardManager;
	}

	class DriverDashboardManager {
		constructor() {
			this.root = document.getElementById('driverDashboardPage');
			if (!this.root) {
				return;
			}

			this.urlRoot = this.root.dataset.urlRoot || 'http://localhost/test';
			this.requests = [];
			this.earnings = null;
			this.vehicles = [];
			this.unreadMessages = 0;

			this.refreshBtn = document.getElementById('driverDashboardRefreshBtn');
			this.handleRefresh = this.loadDashboard.bind(this);

			this.bindEvents();
			this.loadDashboard();
		}

		bindEvents() {
			if (this.refreshBtn) {
				this.refreshBtn.addEventListener('click', this.handleRefresh);
			}
		}

		destroy() {
			if (this.refreshBtn) {
				this.refreshBtn.removeEventListener('click', this.handleRefresh);
			}
		}

		async loadDashboard() {
			this.setLoading(true);

			try {
				const [requestsRes, earningsRes, vehiclesRes, unreadRes] = await Promise.allSettled([
					this.fetchJson('/Driver/getMyRequests'),
					this.fetchJson('/Driver/getEarningsSummary'),
					this.fetchJson('/Driver/getVehicles'),
					this.fetchJson('/helpc/getUnreadMessageCount')
				]);

				this.requests = this.readArrayPayload(requestsRes, 'requests');
				this.earnings = this.readObjectPayload(earningsRes, 'summary');
				this.vehicles = this.readArrayPayload(vehiclesRes, 'vehicles');
				this.unreadMessages = this.readUnreadCount(unreadRes);

				this.render();
			} catch (error) {
				console.error('Driver dashboard load failed:', error);
				if (window.showNotification) {
					window.showNotification('Failed to load dashboard data', 'error');
				}
			} finally {
				this.setLoading(false);
			}
		}

		readArrayPayload(result, key) {
			if (result.status !== 'fulfilled' || !result.value || result.value.success === false) {
				return [];
			}

			const payload = result.value[key];
			return Array.isArray(payload) ? payload : [];
		}

		readObjectPayload(result, key) {
			if (result.status !== 'fulfilled' || !result.value || result.value.success === false) {
				return null;
			}

			const payload = result.value[key];
			return payload && typeof payload === 'object' ? payload : null;
		}

		readUnreadCount(result) {
			if (result.status !== 'fulfilled' || !result.value) {
				return 0;
			}

			const count = Number(result.value.unreadCount);
			return Number.isFinite(count) && count > 0 ? count : 0;
		}

		async fetchJson(path) {
			const response = await fetch(`${this.urlRoot}${path}`);
			const contentType = response.headers.get('content-type') || '';

			if (!response.ok) {
				throw new Error(`Request failed (${response.status}) for ${path}`);
			}

			if (!contentType.includes('application/json')) {
				const body = await response.text();
				throw new Error(`Non-JSON response for ${path}: ${body.slice(0, 180)}`);
			}

			return response.json();
		}

		render() {
			const requestGroups = this.groupRequests(this.requests);

			this.renderHeroSummary(requestGroups);
			this.renderTopStats(requestGroups);
			this.renderRequestPipeline(requestGroups);
			this.renderEarnings();
			this.renderUpcomingTrips(requestGroups.accepted);
			this.renderRecentActivity(this.requests);
			this.renderFleet(this.vehicles);
		}

		renderHeroSummary(groups) {
			const today = new Date();
			this.setText('dashTodayDate', today.toLocaleDateString('en-GB', {
				weekday: 'short',
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			}));

			this.setText('dashHeroPendingCount', groups.pending.length);
			this.setText('dashHeroUnreadCount', this.unreadMessages);
		}

		groupRequests(requests) {
			const groups = {
				pending: [],
				accepted: [],
				rejected: []
			};

			(requests || []).forEach((request) => {
				const status = this.readRequestStatus(request);
				if (status === 'accepted') {
					groups.accepted.push(request);
					return;
				}

				if (status === 'rejected' || status === 'cancelled') {
					groups.rejected.push(request);
					return;
				}

				groups.pending.push(request);
			});

			return groups;
		}

		readRequestStatus(request) {
			return String(request.requestStatus || request.status || 'requested').trim().toLowerCase();
		}

		renderTopStats(groups) {
			const totalRequests = groups.pending.length + groups.accepted.length + groups.rejected.length;
			const activeVehicles = this.vehicles.filter((vehicle) => this.toBoolean(vehicle.is_active)).length;

			this.setText('dashTotalRequests', totalRequests);
			this.setText('dashPendingRequests', groups.pending.length);
			this.setText('dashAcceptedRequests', groups.accepted.length);
			this.setText('dashUnreadMessages', this.unreadMessages);
			this.setText('dashActiveVehicles', activeVehicles);
		}

		renderRequestPipeline(groups) {
			const pending = groups.pending.length;
			const accepted = groups.accepted.length;
			const rejected = groups.rejected.length;
			const total = Math.max(pending + accepted + rejected, 1);

			this.setText('dashPendingCountRow', pending);
			this.setText('dashAcceptedCountRow', accepted);
			this.setText('dashRejectedCountRow', rejected);

			this.setBarWidth('dashPendingBar', (pending / total) * 100);
			this.setBarWidth('dashAcceptedBar', (accepted / total) * 100);
			this.setBarWidth('dashRejectedBar', (rejected / total) * 100);
		}

		renderEarnings() {
			const summary = this.earnings || {
				pending_amount: 0,
				pending_count: 0,
				paid_amount: 0,
				paid_count: 0,
				refunded_amount: 0,
				refunded_count: 0
			};

			this.setText('dashPendingAmount', this.formatCurrency(summary.pending_amount));
			this.setText('dashPendingTrips', `${Number(summary.pending_count || 0)} trips`);

			this.setText('dashPaidAmount', this.formatCurrency(summary.paid_amount));
			this.setText('dashPaidTrips', `${Number(summary.paid_count || 0)} trips`);

			this.setText('dashRefundedAmount', this.formatCurrency(summary.refunded_amount));
			this.setText('dashRefundedTrips', `${Number(summary.refunded_count || 0)} trips`);
		}

		renderUpcomingTrips(acceptedRequests) {
			const listElement = document.getElementById('dashUpcomingList');
			const emptyElement = document.getElementById('dashUpcomingEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const today = new Date();
			today.setHours(0, 0, 0, 0);

			const upcoming = (acceptedRequests || [])
				.map((request) => ({ request, date: this.readRequestDate(request) }))
				.filter((entry) => entry.date && entry.date >= today)
				.sort((a, b) => a.date - b.date)
				.slice(0, 5);

			if (upcoming.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = upcoming.map((entry) => {
				const request = entry.request;
				const amount = this.readAmount(request);
				const requestTitle = this.escapeHtml(this.readTripTitle(request));
				const meta = [
					this.readTripId(request),
					this.formatDate(entry.date),
					amount > 0 ? this.formatCurrency(amount) : 'Amount not set'
				];

				return `
					<div class="list-item">
						<div class="list-main">
							<h4>${requestTitle}</h4>
							<p>${this.escapeHtml(meta.join('  |  '))}</p>
						</div>
						<span class="status-badge accepted">Accepted</span>
					</div>
				`;
			}).join('');
		}

		renderRecentActivity(requests) {
			const listElement = document.getElementById('dashRecentList');
			const emptyElement = document.getElementById('dashRecentEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const recent = (requests || [])
				.map((request) => ({ request, activityDate: this.readActivityDate(request) }))
				.sort((a, b) => b.activityDate - a.activityDate)
				.slice(0, 6);

			if (recent.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = recent.map((entry) => {
				const request = entry.request;
				const status = this.readRequestStatus(request);
				const statusLabel = this.toTitleCase(status);
				const statusClass = this.statusClass(status);
				const amount = this.readAmount(request);

				const metaParts = [
					this.readTripId(request),
					this.formatDate(entry.activityDate)
				];

				if (amount > 0) {
					metaParts.push(this.formatCurrency(amount));
				}

				return `
					<div class="list-item">
						<div class="list-main">
							<h4>${this.escapeHtml(this.readTripTitle(request))}</h4>
							<p>${this.escapeHtml(metaParts.join('  |  '))}</p>
						</div>
						<span class="status-badge ${statusClass}">${this.escapeHtml(statusLabel)}</span>
					</div>
				`;
			}).join('');
		}

		renderFleet(vehicles) {
			const listElement = document.getElementById('dashFleetList');
			const emptyElement = document.getElementById('dashFleetEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const total = vehicles.length;
			const inUse = vehicles.filter((vehicle) => this.toBoolean(vehicle.in_use)).length;
			const pendingApproval = vehicles.filter((vehicle) => String(vehicle.status || '').toLowerCase() !== 'approved').length;

			this.setText('dashTotalVehicles', total);
			this.setText('dashInUseVehicles', inUse);
			this.setText('dashPendingVehicles', pendingApproval);

			if (total === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = vehicles.slice(0, 4).map((vehicle) => {
				const name = [vehicle.make, vehicle.model].filter(Boolean).join(' ');
				const label = name || 'Unnamed vehicle';
				const year = vehicle.year ? ` (${vehicle.year})` : '';
				const plate = vehicle.license_plate || 'Plate not set';

				const approvalStatus = String(vehicle.status || '').toLowerCase() === 'approved'
					? '<span class="status-badge accepted">Approved</span>'
					: '<span class="status-badge pending">Pending</span>';

				const usageStatus = this.toBoolean(vehicle.in_use)
					? '<span class="status-badge in-use">In Use</span>'
					: '<span class="status-badge idle">Idle</span>';

				return `
					<div class="list-item fleet-item">
						<div class="list-main">
							<h4>${this.escapeHtml(label + year)}</h4>
							<p>${this.escapeHtml(plate)}</p>
						</div>
						<div class="fleet-status-wrap">
							${approvalStatus}
							${usageStatus}
						</div>
					</div>
				`;
			}).join('');
		}

		readTripTitle(request) {
			if (request.tripTitle && String(request.tripTitle).trim() !== '') {
				return request.tripTitle;
			}

			const tripId = request.tripId || request.requestId;
			return tripId ? `Trip #${tripId}` : 'Trip Request';
		}

		readTripId(request) {
			return request.tripId ? `Trip #${request.tripId}` : 'Trip ID unavailable';
		}

		readAmount(request) {
			const candidates = [request.totalAmount, request.driverCharge, request.budget];
			for (let i = 0; i < candidates.length; i += 1) {
				const value = Number(candidates[i]);
				if (Number.isFinite(value) && value > 0) {
					return value;
				}
			}
			return 0;
		}

		readRequestDate(request) {
			const candidates = [
				request.startDate,
				request.tripStartDate,
				request.date,
				request.createdAt
			];

			return this.readFirstValidDate(candidates);
		}

		readActivityDate(request) {
			const candidates = [
				request.respondedAt,
				request.updatedAt,
				request.createdAt,
				request.startDate
			];

			return this.readFirstValidDate(candidates) || new Date(0);
		}

		readFirstValidDate(candidates) {
			for (let i = 0; i < candidates.length; i += 1) {
				if (!candidates[i]) {
					continue;
				}
				const date = new Date(candidates[i]);
				if (!Number.isNaN(date.getTime())) {
					return date;
				}
			}
			return null;
		}

		statusClass(status) {
			if (status === 'accepted') {
				return 'accepted';
			}

			if (status === 'rejected' || status === 'cancelled') {
				return 'rejected';
			}

			return 'pending';
		}

		toTitleCase(value) {
			if (!value) {
				return '';
			}
			return String(value)
				.replace(/[_-]+/g, ' ')
				.replace(/\b\w/g, (letter) => letter.toUpperCase());
		}

		toBoolean(value) {
			if (typeof value === 'boolean') {
				return value;
			}
			if (typeof value === 'number') {
				return value === 1;
			}
			const normalized = String(value || '').trim().toLowerCase();
			return normalized === '1' || normalized === 'true' || normalized === 'yes';
		}

		setLoading(isLoading) {
			if (!this.refreshBtn) {
				return;
			}

			this.refreshBtn.disabled = isLoading;
			this.refreshBtn.classList.toggle('is-loading', isLoading);
		}

		setText(id, value) {
			const element = document.getElementById(id);
			if (element) {
				element.textContent = value;
			}
		}

		setBarWidth(id, percentage) {
			const element = document.getElementById(id);
			if (!element) {
				return;
			}
			const safePercentage = Math.max(0, Math.min(100, percentage));
			element.style.width = `${safePercentage}%`;
		}

		formatCurrency(value) {
			const amount = Number(value);
			const safeAmount = Number.isFinite(amount) ? amount : 0;
			return `LKR ${safeAmount.toLocaleString('en-LK', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			})}`;
		}

		formatDate(value) {
			if (!value) {
				return 'Date not available';
			}

			const date = value instanceof Date ? value : new Date(value);
			if (Number.isNaN(date.getTime())) {
				return 'Date not available';
			}

			return date.toLocaleDateString('en-GB', {
				day: '2-digit',
				month: 'short',
				year: 'numeric'
			});
		}

		escapeHtml(value) {
			return String(value || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}
	}

	window.DriverDashboardManager = DriverDashboardManager;
	window.driverDashboardManager = new DriverDashboardManager();
})();
