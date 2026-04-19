(function () {
	if (window.ModeratorDashboardManager) {
		if (window.moderatorDashboardManager && typeof window.moderatorDashboardManager.destroy === 'function') {
			window.moderatorDashboardManager.destroy();
		}
		delete window.moderatorDashboardManager;
		delete window.ModeratorDashboardManager;
	}

	class ModeratorDashboardManager {
		constructor() {
			this.root = document.getElementById('moderatorDashboardPage');
			if (!this.root) {
				return;
			}

			this.urlRoot = this.root.dataset.urlRoot || 'http://localhost/test';
			this.refreshBtn = document.getElementById('modDashRefreshBtn');

			this.charts = {
				queue: null,
				revenue: null
			};

			this.state = {
				pendingAccounts: [],
				pendingLicenses: [],
				pendingVehicles: [],
				complaints: {
					pending: [],
					in_progress: [],
					completed: []
				},
				paymentStats: null,
				driverPayoutStats: null,
				guidePayoutStats: null,
				pendingDriverPayouts: [],
				pendingGuidePayouts: [],
				verificationStats: null,
				earningsMetrics: null,
				revenueTrend: null,
				unreadMessages: 0
			};

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
			this.destroyCharts();
		}

		destroyCharts() {
			if (this.charts.queue) {
				this.charts.queue.destroy();
				this.charts.queue = null;
			}
			if (this.charts.revenue) {
				this.charts.revenue.destroy();
				this.charts.revenue = null;
			}
		}

		async loadDashboard() {
			this.setLoading(true);

			try {
				const [
					accountsRes,
					licensesRes,
					vehiclesRes,
					complaintsRes,
					paymentStatsRes,
					driverPayoutStatsRes,
					guidePayoutStatsRes,
					pendingDriverRes,
					pendingGuideRes,
					verificationRes,
					earningsRes,
					revenueTrendRes,
					unreadRes
				] = await Promise.allSettled([
					this.fetchJson('/moderator/getAccounts/pending'),
					this.fetchJson('/moderator/getPendingLicenses'),
					this.fetchJson('/moderator/getPendingVehicles'),
					this.fetchJson('/moderator/getAllComplaints'),
					this.fetchJson('/moderator/getPaymentStats'),
					this.fetchJson('/moderator/getDriverPayoutStats'),
					this.fetchJson('/moderator/getGuidePayoutStats'),
					this.fetchJson('/moderator/getPendingDriverPayouts'),
					this.fetchJson('/moderator/getPendingGuidePayouts'),
					this.fetchJson('/moderator/getVerificationStats'),
					this.fetchJson('/moderator/getEarningsMetrics?timeRange=30days&viewType=daily'),
					this.fetchJson('/moderator/getRevenueTrend?timeRange=30days&viewType=daily'),
					this.fetchJson('/helpc/getUnreadMessageCount')
				]);

				this.state.pendingAccounts = this.readArrayPayload(accountsRes, 'accounts');
				this.state.pendingLicenses = this.readArrayPayload(licensesRes, 'licenses');
				this.state.pendingVehicles = this.readArrayPayload(vehiclesRes, 'vehicles');
				this.state.complaints = this.readComplaintsPayload(complaintsRes);
				this.state.paymentStats = this.readObjectPayload(paymentStatsRes, 'stats');
				this.state.driverPayoutStats = this.readObjectPayload(driverPayoutStatsRes, 'stats');
				this.state.guidePayoutStats = this.readObjectPayload(guidePayoutStatsRes, 'stats');
				this.state.pendingDriverPayouts = this.readArrayPayload(pendingDriverRes, 'payouts');
				this.state.pendingGuidePayouts = this.readArrayPayload(pendingGuideRes, 'payouts');
				this.state.verificationStats = this.readObjectPayload(verificationRes, 'stats');
				this.state.earningsMetrics = this.readObjectPayload(earningsRes, 'metrics');
				this.state.revenueTrend = this.readObjectPayload(revenueTrendRes, 'trend');
				this.state.unreadMessages = this.readUnreadCount(unreadRes);

				this.render();
			} catch (error) {
				console.error('Moderator dashboard load failed:', error);
				if (window.showNotification) {
					window.showNotification('Failed to load moderator dashboard data', 'error');
				}
			} finally {
				this.setLoading(false);
			}
		}

		render() {
			this.renderHeroSummary();
			this.renderTopStats();
			this.renderQueueBreakdown();
			this.renderFinanceSnapshot();
			this.renderComplaintList();
			this.renderVerificationList();
			this.renderPayoutList();
			this.renderQueueChart();
			this.renderRevenueTrendChart();
		}

		renderHeroSummary() {
			const today = new Date();
			this.setText(
				'modDashTodayDate',
				today.toLocaleDateString('en-GB', {
					weekday: 'short',
					day: 'numeric',
					month: 'short',
					year: 'numeric'
				})
			);

			this.setText('modDashHeroQueue', this.getTotalQueueCount());
			this.setText('modDashHeroUnread', this.state.unreadMessages);
		}

		renderTopStats() {
			const siteProfit = this.toNumber(this.state.earningsMetrics && this.state.earningsMetrics.siteProfit);

			this.setText('modDashPendingAccounts', this.getPendingAccountsCount());
			this.setText('modDashPendingLicenses', this.state.pendingLicenses.length);
			this.setText('modDashPendingVehicles', this.state.pendingVehicles.length);
			this.setText('modDashOpenComplaints', this.getOpenComplaintsCount());
			this.setText('modDashPendingPayouts', this.getPendingPayoutCount());
			this.setText('modDashSiteProfit', this.formatCurrency(siteProfit));
		}

		renderQueueBreakdown() {
			const breakdown = [
				{
					count: this.getPendingAccountsCount(),
					countId: 'modDashQueueAccounts',
					barId: 'modDashQueueAccountsBar'
				},
				{
					count: this.state.pendingLicenses.length,
					countId: 'modDashQueueLicenses',
					barId: 'modDashQueueLicensesBar'
				},
				{
					count: this.state.pendingVehicles.length,
					countId: 'modDashQueueVehicles',
					barId: 'modDashQueueVehiclesBar'
				},
				{
					count: this.getOpenComplaintsCount(),
					countId: 'modDashQueueComplaints',
					barId: 'modDashQueueComplaintsBar'
				},
				{
					count: this.getPendingPayoutCount(),
					countId: 'modDashQueuePayouts',
					barId: 'modDashQueuePayoutsBar'
				}
			];

			const total = Math.max(
				breakdown.reduce((sum, item) => sum + item.count, 0),
				1
			);

			breakdown.forEach((item) => {
				this.setText(item.countId, item.count);
				this.setBarWidth(item.barId, (item.count / total) * 100);
			});
		}

		renderFinanceSnapshot() {
			const paymentStats = this.state.paymentStats || {};

			const completedPayments = this.toNumber(paymentStats.completed_count);
			const cancelledPayments = this.toNumber(paymentStats.cancelled_count);
			const refundedPayments = this.toNumber(paymentStats.refunded_count);
			const totalRevenue = this.toNumber(paymentStats.total_revenue);

			const pendingPayoutCount = this.getPendingPayoutCount();
			const pendingPayoutAmount = this.getPendingPayoutAmount();

			this.setText('modDashCompletedPaymentsCount', completedPayments);
			this.setText('modDashCancelledPaymentsCount', cancelledPayments);
			this.setText('modDashRefundedPaymentsCount', refundedPayments);
			this.setText('modDashRevenueAmount', this.formatCurrency(totalRevenue));

			this.setText('modDashPendingPayoutCount', pendingPayoutCount);
			this.setText('modDashPendingPayoutAmount', this.formatCurrency(pendingPayoutAmount));

			this.setText('modDashPaidPayoutCount', this.getPaidPayoutCount());
			this.setText('modDashPaidPayoutAmount', this.formatCurrency(this.getPaidPayoutAmount()));
		}

		renderComplaintList() {
			const listElement = document.getElementById('modDashComplaintList');
			const emptyElement = document.getElementById('modDashComplaintEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const complaints = [
				...(this.state.complaints.pending || []),
				...(this.state.complaints.in_progress || [])
			]
				.sort((a, b) => this.readTimestamp(b.updatedAt || b.createdAt) - this.readTimestamp(a.updatedAt || a.createdAt))
				.slice(0, 6);

			if (complaints.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = complaints
				.map((complaint) => {
					const subject = this.mapComplaintSubject(complaint.subject);
					const userName = complaint.userName || 'User';
					const status = String(complaint.status || 'pending').toLowerCase();
					const statusLabel = this.toTitleCase(status);
					const statusClass = status.replace(/_/g, '-');

					return `
						<div class="moddash-list-item">
							<div class="moddash-list-main">
								<h4>${this.escapeHtml(subject)}</h4>
								<p>${this.escapeHtml(userName)}  |  ${this.escapeHtml(this.formatDate(complaint.createdAt))}</p>
							</div>
							<span class="moddash-status-badge ${this.escapeHtml(statusClass)}">${this.escapeHtml(statusLabel)}</span>
						</div>
					`;
				})
				.join('');
		}

		renderVerificationList() {
			const listElement = document.getElementById('modDashVerificationList');
			const emptyElement = document.getElementById('modDashVerificationEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const entries = [];

			(this.state.pendingAccounts || []).forEach((item) => {
				entries.push({
					type: 'account',
					title: item.name || 'Pending account verification',
					meta: `${item.email || 'Email unavailable'}  |  ${(item.account_type || 'user').toUpperCase()}`,
					timestamp: this.readTimestamp(item.verification_created_at || item.created_at)
				});
			});

			(this.state.pendingLicenses || []).forEach((item) => {
				entries.push({
					type: 'license',
					title: item.name || 'Pending tourist license verification',
					meta: `${item.email || 'Email unavailable'}  |  ${(item.account_type || 'user').toUpperCase()}`,
					timestamp: this.readTimestamp(item.created_at)
				});
			});

			(this.state.pendingVehicles || []).forEach((item) => {
				entries.push({
					type: 'vehicle',
					title: item.owner_name || 'Pending vehicle verification',
					meta: `${item.vehicle_type || 'Vehicle not specified'}  |  ${item.registration_number || 'Reg no unavailable'}`,
					timestamp: this.readTimestamp(item.submission_date || item.created_at)
				});
			});

			const sortedEntries = entries.sort((a, b) => b.timestamp - a.timestamp).slice(0, 7);

			if (sortedEntries.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = sortedEntries
				.map((entry) => {
					return `
						<div class="moddash-list-item">
							<div class="moddash-list-main">
								<h4>${this.escapeHtml(entry.title)}</h4>
								<p>${this.escapeHtml(entry.meta)}  |  ${this.escapeHtml(this.formatDate(entry.timestamp))}</p>
							</div>
							<span class="moddash-status-badge ${entry.type}">${this.escapeHtml(this.toTitleCase(entry.type))}</span>
						</div>
					`;
				})
				.join('');
		}

		renderPayoutList() {
			const listElement = document.getElementById('modDashPayoutList');
			const emptyElement = document.getElementById('modDashPayoutEmpty');
			if (!listElement || !emptyElement) {
				return;
			}

			const entries = [];

			(this.state.pendingDriverPayouts || []).forEach((item) => {
				entries.push({
					type: 'driver',
					title: item.driverName || 'Driver payout',
					tripId: item.tripId,
					amount: this.toNumber(item.amount),
					timestamp: this.readTimestamp(item.paymentDate || item.createdAt)
				});
			});

			(this.state.pendingGuidePayouts || []).forEach((item) => {
				entries.push({
					type: 'guide',
					title: item.guideName || 'Guide payout',
					tripId: item.tripId,
					amount: this.toNumber(item.amount),
					timestamp: this.readTimestamp(item.paymentDate || item.createdAt)
				});
			});

			const sortedEntries = entries.sort((a, b) => b.timestamp - a.timestamp).slice(0, 7);

			if (sortedEntries.length === 0) {
				listElement.innerHTML = '';
				emptyElement.style.display = 'block';
				return;
			}

			emptyElement.style.display = 'none';

			listElement.innerHTML = sortedEntries
				.map((entry) => {
					return `
						<div class="moddash-list-item">
							<div class="moddash-list-main">
								<h4>${this.escapeHtml(entry.title)}</h4>
								<p>${this.escapeHtml(`Trip #${entry.tripId || '-'}  |  ${this.formatCurrency(entry.amount)}  |  ${this.formatDate(entry.timestamp)}`)}</p>
							</div>
							<span class="moddash-status-badge ${entry.type}">${this.escapeHtml(this.toTitleCase(entry.type))}</span>
						</div>
					`;
				})
				.join('');
		}

		renderQueueChart() {
			const labels = ['Accounts', 'Licenses', 'Vehicles', 'Complaints', 'Payouts'];
			const data = [
				this.getPendingAccountsCount(),
				this.state.pendingLicenses.length,
				this.state.pendingVehicles.length,
				this.getOpenComplaintsCount(),
				this.getPendingPayoutCount()
			];

			const hasData = data.some((value) => value > 0);

			if (typeof Chart === 'undefined') {
				this.toggleChartEmpty('modDashQueueChartEmpty', true, 'Chart library unavailable.');
				return;
			}

			const canvas = document.getElementById('modDashQueueChart');
			if (!canvas) {
				return;
			}

			if (this.charts.queue) {
				this.charts.queue.destroy();
				this.charts.queue = null;
			}

			if (!hasData) {
				this.toggleChartEmpty('modDashQueueChartEmpty', true, 'Not enough queue data for chart.');
				return;
			}

			this.toggleChartEmpty('modDashQueueChartEmpty', false);

			this.charts.queue = new Chart(canvas, {
				type: 'bar',
				data: {
					labels,
					datasets: [
						{
							label: 'Queue items',
							data,
							borderWidth: 0,
							borderRadius: 8,
							backgroundColor: [
								'#4f86e8',
								'#f3ad45',
								'#41af75',
								'#df5f75',
								'#4e9ad7'
							]
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { display: false }
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								precision: 0
							}
						}
					}
				}
			});
		}

		renderRevenueTrendChart() {
			const trend = this.state.revenueTrend || {};
			const labels = Array.isArray(trend.labels) ? trend.labels : [];
			const totalRevenue = Array.isArray(trend.totalRevenue) ? trend.totalRevenue : [];
			const siteProfit = Array.isArray(trend.siteProfit) ? trend.siteProfit : [];

			if (typeof Chart === 'undefined') {
				this.toggleChartEmpty('modDashRevenueTrendEmpty', true, 'Chart library unavailable.');
				return;
			}

			const canvas = document.getElementById('modDashRevenueTrendChart');
			if (!canvas) {
				return;
			}

			if (this.charts.revenue) {
				this.charts.revenue.destroy();
				this.charts.revenue = null;
			}

			if (labels.length === 0) {
				this.toggleChartEmpty('modDashRevenueTrendEmpty', true, 'No revenue trend data available yet.');
				return;
			}

			this.toggleChartEmpty('modDashRevenueTrendEmpty', false);

			this.charts.revenue = new Chart(canvas, {
				type: 'line',
				data: {
					labels,
					datasets: [
						{
							label: 'Total Revenue',
							data: totalRevenue,
							borderColor: '#2f83e4',
							backgroundColor: 'rgba(47, 131, 228, 0.12)',
							fill: true,
							tension: 0.35,
							pointRadius: 3
						},
						{
							label: 'Site Profit',
							data: siteProfit,
							borderColor: '#1f9a57',
							backgroundColor: 'rgba(31, 154, 87, 0.12)',
							fill: true,
							tension: 0.35,
							pointRadius: 3
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					interaction: {
						mode: 'index',
						intersect: false
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								callback: (value) => `LKR ${Number(value).toLocaleString()}`
							}
						}
					}
				}
			});
		}

		getPendingAccountsCount() {
			if (this.state.pendingAccounts.length > 0) {
				return this.state.pendingAccounts.length;
			}

			const verification = this.state.verificationStats;
			if (verification && verification.overall) {
				return this.toNumber(verification.overall.pending);
			}

			return 0;
		}

		getOpenComplaintsCount() {
			return (this.state.complaints.pending || []).length + (this.state.complaints.in_progress || []).length;
		}

		getPendingPayoutCount() {
			const driverPending = this.toNumber(this.state.driverPayoutStats && this.state.driverPayoutStats.pending_count);
			const guidePending = this.toNumber(this.state.guidePayoutStats && this.state.guidePayoutStats.pending_count);
			const totalFromStats = driverPending + guidePending;

			if (totalFromStats > 0) {
				return totalFromStats;
			}

			return (this.state.pendingDriverPayouts || []).length + (this.state.pendingGuidePayouts || []).length;
		}

		getPendingPayoutAmount() {
			const driverAmount = this.sumAmounts(this.state.pendingDriverPayouts, 'amount');
			const guideAmount = this.sumAmounts(this.state.pendingGuidePayouts, 'amount');
			return driverAmount + guideAmount;
		}

		getPaidPayoutCount() {
			const driverCompleted = this.toNumber(this.state.driverPayoutStats && this.state.driverPayoutStats.completed_count);
			const guideCompleted = this.toNumber(this.state.guidePayoutStats && this.state.guidePayoutStats.completed_count);
			return driverCompleted + guideCompleted;
		}

		getPaidPayoutAmount() {
			const driverAmount = this.toNumber(this.state.driverPayoutStats && this.state.driverPayoutStats.total_payout_amount);
			const guideAmount = this.toNumber(this.state.guidePayoutStats && this.state.guidePayoutStats.total_payout_amount);
			return driverAmount + guideAmount;
		}

		getTotalQueueCount() {
			return (
				this.getPendingAccountsCount() +
				this.state.pendingLicenses.length +
				this.state.pendingVehicles.length +
				this.getOpenComplaintsCount() +
				this.getPendingPayoutCount()
			);
		}

		mapComplaintSubject(subject) {
			const labels = {
				booking: 'Booking issue',
				payment: 'Payment problem',
				trip: 'Trip experience issue',
				guide_driver: 'Guide or driver concern',
				account: 'Account support',
				feature: 'Feature request',
				other: 'General complaint'
			};

			const normalized = String(subject || '').toLowerCase();
			return labels[normalized] || this.toTitleCase(normalized || 'Complaint');
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

		readComplaintsPayload(result) {
			if (result.status !== 'fulfilled' || !result.value || result.value.success === false) {
				return {
					pending: [],
					in_progress: [],
					completed: []
				};
			}

			const payload = result.value.complaints;
			if (!payload || typeof payload !== 'object') {
				return {
					pending: [],
					in_progress: [],
					completed: []
				};
			}

			return {
				pending: Array.isArray(payload.pending) ? payload.pending : [],
				in_progress: Array.isArray(payload.in_progress) ? payload.in_progress : [],
				completed: Array.isArray(payload.completed) ? payload.completed : []
			};
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
				throw new Error(`Non-JSON response for ${path}: ${body.slice(0, 160)}`);
			}

			return response.json();
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
				element.textContent = String(value);
			}
		}

		setBarWidth(id, percentage) {
			const element = document.getElementById(id);
			if (!element) {
				return;
			}

			const safe = Math.max(0, Math.min(100, Number(percentage) || 0));
			element.style.width = `${safe}%`;
		}

		toggleChartEmpty(id, show, message) {
			const element = document.getElementById(id);
			if (!element) {
				return;
			}

			if (message) {
				element.textContent = message;
			}

			element.style.display = show ? 'flex' : 'none';
		}

		sumAmounts(items, key) {
			if (!Array.isArray(items)) {
				return 0;
			}

			return items.reduce((sum, item) => sum + this.toNumber(item && item[key]), 0);
		}

		readTimestamp(value) {
			if (!value) {
				return 0;
			}

			const date = new Date(value);
			const timestamp = date.getTime();
			return Number.isNaN(timestamp) ? 0 : timestamp;
		}

		formatDate(value) {
			if (!value) {
				return '-';
			}

			const date = new Date(value);
			if (Number.isNaN(date.getTime())) {
				return '-';
			}

			return date.toLocaleDateString('en-GB', {
				day: '2-digit',
				month: 'short',
				year: 'numeric'
			});
		}

		formatCurrency(value) {
			const amount = this.toNumber(value);
			return `LKR ${amount.toLocaleString('en-LK', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			})}`;
		}

		toNumber(value) {
			const num = Number(value);
			return Number.isFinite(num) ? num : 0;
		}

		toTitleCase(value) {
			if (!value) {
				return '';
			}

			return String(value)
				.replace(/[_-]+/g, ' ')
				.replace(/\b\w/g, (letter) => letter.toUpperCase());
		}

		escapeHtml(value) {
			return String(value || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;');
		}
	}

	window.ModeratorDashboardManager = ModeratorDashboardManager;
	window.moderatorDashboardManager = new ModeratorDashboardManager();
})();
