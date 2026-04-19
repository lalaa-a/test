(function () {
	if (window.GuideDashboardManager) {
		if (window.guideDashboardManager && typeof window.guideDashboardManager.destroy === 'function') {
			window.guideDashboardManager.destroy();
		}
		delete window.guideDashboardManager;
		delete window.GuideDashboardManager;
	}

	class GuideDashboardManager {
		constructor() {
			this.root = document.getElementById('guideDashboardPage');
			if (!this.root) {
				return;
			}

			this.urlRoot = this.root.dataset.urlRoot || 'http://localhost/test';
			this.requests = [];
			this.earnings = null;
			this.visits = { ongoing: [], upcoming: [], completed: [] };
			this.spots = [];
			this.problems = [];
			this.unreadMessages = 0;

			this.refreshBtn = document.getElementById('guideDashboardRefreshBtn');
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
				const [requestsRes, earningsRes, visitsRes, spotsRes, supportRes, unreadRes] = await Promise.allSettled([
					this.fetchJson('/Guide/getMyRequests'),
					this.fetchJson('/Guide/getEarningsSummary'),
					this.fetchJson('/Guide/getGuideVisits'),
					this.fetchJson('/Guide/getGuideSpots'),
					this.fetchJson('/Guide/getUserProblemsByUserId'),
					this.fetchJson('/helpc/getUnreadMessageCount')
				]);

				this.requests = this.readArrayPayload(requestsRes, 'requests');
				this.earnings = this.readObjectPayload(earningsRes, 'summary');
				this.visits = this.readVisitsPayload(visitsRes);
				this.spots = this.readArrayPayload(spotsRes, 'spots');
				this.problems = this.readArrayPayload(supportRes, 'problems');
				this.unreadMessages = this.readUnreadCount(unreadRes);

				this.render();
			} catch (error) {
				console.error('Guide dashboard load failed:', error);
				if (window.showNotification) {
					window.showNotification('Failed to load dashboard data', 'error');
				}
			} finally {
				this.setLoading(false);
			}
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

		readVisitsPayload(result) {
			if (result.status !== 'fulfilled' || !result.value || result.value.success === false) {
				return { ongoing: [], upcoming: [], completed: [] };
			}

			const visits = result.value.visits;
			if (!visits || typeof visits !== 'object') {
				return { ongoing: [], upcoming: [], completed: [] };
			}

			return {
				ongoing: Array.isArray(visits.ongoing) ? visits.ongoing : [],
				upcoming: Array.isArray(visits.upcoming) ? visits.upcoming : [],
				completed: Array.isArray(visits.completed) ? visits.completed : []
			};
		}

		readUnreadCount(result) {
			if (result.status !== 'fulfilled' || !result.value) {
				return 0;
			}

			const count = Number(result.value.unreadCount);
			return Number.isFinite(count) && count > 0 ? count : 0;
		}

		render() {
			const requestGroups = this.groupRequests(this.requests);

			this.renderHeroSummary();
			this.renderTopStats(requestGroups);
			this.renderRequestPipeline(requestGroups);
			this.renderEarnings();
			this.renderUpcomingVisits();
			this.renderRecentActivity(this.requests);
			this.renderSpots();
			this.renderSupport();
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
			return String(request.status || request.requestStatus || 'requested').trim().toLowerCase();
		}

		renderHeroSummary() {
			const today = new Date();
			const upcomingVisits = this.visits.upcoming || [];

			this.setText('guideDashTodayDate', today.toLocaleDateString('en-GB', {
				weekday: 'short',
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			}));
			this.setText('guideDashHeroUpcoming', upcomingVisits.length);
			this.setText('guideDashHeroUnread', this.unreadMessages);
		}

		renderTopStats(groups) {
			const totalRequests = groups.pending.length + groups.accepted.length + groups.rejected.length;
			const ongoingVisits = (this.visits.ongoing || []).length;
			const activeSpots = this.spots.filter((spot) => this.toBoolean(spot.isActive)).length;

			this.setText('guideDashTotalRequests', totalRequests);
			this.setText('guideDashPendingRequests', groups.pending.length);
			this.setText('guideDashAcceptedRequests', groups.accepted.length);
			this.setText('guideDashOngoingVisits', ongoingVisits);
			this.setText('guideDashActiveSpots', activeSpots);
			this.setText('guideDashUnreadMessages', this.unreadMessages);
		}

		renderRequestPipeline(groups) {
			const pending = groups.pending.length;
			const accepted = groups.accepted.length;
			const rejected = groups.rejected.length;
			const total = Math.max(pending + accepted + rejected, 1);

			this.setText('guideDashPendingCountRow', pending);
			this.setText('guideDashAcceptedCountRow', accepted);
			this.setText('guideDashRejectedCountRow', rejected);

			this.setBarWidth('guideDashPendingBar', (pending / total) * 100);
			this.setBarWidth('guideDashAcceptedBar', (accepted / total) * 100);
			this.setBarWidth('guideDashRejectedBar', (rejected / total) * 100);
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

			this.setText('guideDashPendingAmount', this.formatCurrency(summary.pending_amount));
			this.setText('guideDashPendingTrips', `${Number(summary.pending_count || 0)} trips`);

			this.setText('guideDashPaidAmount', this.formatCurrency(summary.paid_amount));
			this.setText('guideDashPaidTrips', `${Number(summary.paid_count || 0)} trips`);

			this.setText('guideDashRefundedAmount', this.formatCurrency(summary.refunded_amount));
			this.setText('guideDashRefundedTrips', `${Number(summary.refunded_count || 0)} trips`);
		}

		renderUpcomingVisits() {
			const listElement = document.getElementById('guideDashUpcomingList');
			const emptyElement = document.getElementById('guideDashUpcomingEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const ongoing = (this.visits.ongoing || []).map((visit) => ({
				...visit,
				_kind: 'ongoing',
				_date: this.readVisitDate(visit)
			}));

			const upcoming = (this.visits.upcoming || []).map((visit) => ({
				...visit,
				_kind: 'upcoming',
				_date: this.readVisitDate(visit)
			}));

			const merged = [...ongoing, ...upcoming]
				.sort((a, b) => a._date - b._date)
				.slice(0, 6);

			if (merged.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';
			listElement.innerHTML = merged.map((visit) => {
				const statusLabel = visit._kind === 'ongoing' ? 'Ongoing' : 'Upcoming';
				const statusClass = visit._kind === 'ongoing' ? 'ongoing' : 'upcoming';

				const tripTitle = this.escapeHtml(visit.tripTitle || `Trip #${visit.tripId || '-'}`);
				const spotName = this.escapeHtml(visit.travelSpotName || 'Spot not set');
				const dateText = this.formatDate(visit._date);
				const peopleText = Number(visit.numberOfPeople || 0) > 0
					? `${Number(visit.numberOfPeople)} people`
					: 'Group size not set';

				return `
					<div class="guide-list-item">
						<div class="guide-list-main">
							<h4>${tripTitle}</h4>
							<p>${spotName}  |  ${dateText}  |  ${peopleText}</p>
						</div>
						<span class="guide-status-badge ${statusClass}">${statusLabel}</span>
					</div>
				`;
			}).join('');
		}

		renderRecentActivity(requests) {
			const listElement = document.getElementById('guideDashRecentList');
			const emptyElement = document.getElementById('guideDashRecentEmpty');
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
				const statusClass = this.statusClass(status);
				const statusLabel = this.toTitleCase(status);

				const traveller = this.escapeHtml(request.userFullName || 'Traveller');
				const spot = this.escapeHtml(request.spotName || 'Spot not set');
				const amount = Number(request.totalCharge || 0);
				const amountText = amount > 0 ? this.formatCurrency(amount) : 'Amount not set';

				return `
					<div class="guide-list-item">
						<div class="guide-list-main">
							<h4>${traveller}</h4>
							<p>${spot}  |  ${this.formatDate(entry.activityDate)}  |  ${amountText}</p>
						</div>
						<span class="guide-status-badge ${statusClass}">${this.escapeHtml(statusLabel)}</span>
					</div>
				`;
			}).join('');
		}

		renderSpots() {
			const listElement = document.getElementById('guideDashSpotsList');
			const emptyElement = document.getElementById('guideDashSpotsEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const total = this.spots.length;
			const active = this.spots.filter((spot) => this.toBoolean(spot.isActive)).length;
			const inactive = total - active;

			this.setText('guideDashTotalSpots', total);
			this.setText('guideDashActiveSpotsChip', active);
			this.setText('guideDashInactiveSpots', inactive);

			if (total === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';
			listElement.innerHTML = this.spots.slice(0, 5).map((spot) => {
				const activeState = this.toBoolean(spot.isActive);
				const statusClass = activeState ? 'active' : 'inactive';
				const statusLabel = activeState ? 'Active' : 'Inactive';

				const spotName = this.escapeHtml(spot.spotName || `Spot #${spot.spotId || '-'}`);
				const chargeType = String(spot.chargeType || '').toLowerCase() === 'per_person' ? 'Per Person' : 'Whole Trip';
				const amount = Number(spot.baseCharge || 0);
				const rangeText = `Group ${Number(spot.minGroupSize || 1)}-${Number(spot.maxGroupSize || 0)}`;

				return `
					<div class="guide-list-item">
						<div class="guide-list-main">
							<h4>${spotName}</h4>
							<p>${this.escapeHtml(chargeType)}  |  ${this.formatCurrency(amount)}  |  ${this.escapeHtml(rangeText)}</p>
						</div>
						<span class="guide-status-badge ${statusClass}">${statusLabel}</span>
					</div>
				`;
			}).join('');
		}

		renderSupport() {
			const listElement = document.getElementById('guideDashSupportList');
			const emptyElement = document.getElementById('guideDashSupportEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const recentProblems = [...(this.problems || [])]
				.sort((a, b) => this.readActivityDate(b) - this.readActivityDate(a))
				.slice(0, 5);

			if (recentProblems.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';
			listElement.innerHTML = recentProblems.map((problem) => {
				const status = String(problem.status || 'pending').toLowerCase();
				const statusClass = status === 'in_progress' ? 'in_progress' : this.statusClass(status);
				const statusLabel = this.toTitleCase(status);

				const subject = this.escapeHtml(problem.subject || 'Support issue');
				const created = this.formatDate(problem.createdAt || problem.created_at);

				return `
					<div class="guide-list-item">
						<div class="guide-list-main">
							<h4>${subject}</h4>
							<p>${created}</p>
						</div>
						<span class="guide-status-badge ${statusClass}">${this.escapeHtml(statusLabel)}</span>
					</div>
				`;
			}).join('');
		}

		readVisitDate(visit) {
			const candidates = [visit.eventDate, visit.startDate, visit.createdAt, visit.created_at];
			for (let i = 0; i < candidates.length; i += 1) {
				if (!candidates[i]) {
					continue;
				}
				const date = new Date(candidates[i]);
				if (!Number.isNaN(date.getTime())) {
					return date;
				}
			}
			return new Date(0);
		}

		readActivityDate(record) {
			const candidates = [record.respondedAt, record.updatedAt, record.createdAt, record.created_at, record.requestedAt];
			for (let i = 0; i < candidates.length; i += 1) {
				if (!candidates[i]) {
					continue;
				}
				const date = new Date(candidates[i]);
				if (!Number.isNaN(date.getTime())) {
					return date;
				}
			}
			return new Date(0);
		}

		statusClass(status) {
			if (status === 'accepted' || status === 'active' || status === 'ongoing') {
				return 'accepted';
			}

			if (status === 'rejected' || status === 'cancelled' || status === 'inactive') {
				return 'rejected';
			}

			if (status === 'upcoming') {
				return 'upcoming';
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

	window.GuideDashboardManager = GuideDashboardManager;
	window.guideDashboardManager = new GuideDashboardManager();
})();
