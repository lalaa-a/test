(function() {
	if (window.PayoutAnalysisManager) {
		if (window.payoutAnalysisManager) {
			delete window.payoutAnalysisManager;
		}
		delete window.PayoutAnalysisManager;
	}

	class PayoutAnalysisManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.UPLOAD_ROOT = 'http://localhost/test/public/uploads';
			this.currentTimeRange = '30days';
			this.currentViewType = 'daily';

			this.payoutTrendChart = null;
			this.payoutDistributionChart = null;
			this.userPayoutTrendChart = null;
			this.topEarnersChart = null;

			this.init();
		}

		init() {
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', () => {
					this.bindEvents();
					this.loadDashboardData();
				});
			} else {
				this.bindEvents();
				this.loadDashboardData();
			}
		}

		bindEvents() {
			const timeRange = document.getElementById('payoutTimeRange');
			if (timeRange) {
				timeRange.addEventListener('change', (e) => {
					this.currentTimeRange = e.target.value;
					this.loadDashboardData();
					// Also reload top earners if on user explorer tab
					if (document.getElementById('user-explorer-section').style.display !== 'none') {
						this.loadTopEarnersData();
					}
				});
			}

			const viewType = document.getElementById('payoutViewType');
			if (viewType) {
				viewType.addEventListener('change', (e) => {
					this.currentViewType = e.target.value;
					this.loadDashboardData();
				});
			}

			// Section navigation
			document.querySelectorAll('.nav-link').forEach(link => {
				link.addEventListener('click', (e) => {
					e.preventDefault();
					const targetId = link.getAttribute('href').substring(1);
					this.switchToTab(targetId);
				});
			});

			const searchBtn = document.getElementById('loadUserPayoutBtn');
			const userIdInput = document.getElementById('payoutUserIdInput');

			if (searchBtn) {
				searchBtn.addEventListener('click', () => this.loadUserPayoutAnalysis());
			}

			if (userIdInput) {
				userIdInput.addEventListener('keydown', (e) => {
					if (e.key === 'Enter') {
						this.loadUserPayoutAnalysis();
					}
				});
			}
		}

		async loadDashboardData() {
			try {
				const [metricsRes, trendRes, breakdownRes] = await Promise.all([
					fetch(`${this.URL_ROOT}/moderator/getPayoutAnalysisMetrics?timeRange=${this.currentTimeRange}`),
					fetch(`${this.URL_ROOT}/moderator/getPayoutTrendData?timeRange=${this.currentTimeRange}&viewType=${this.currentViewType}`),
					fetch(`${this.URL_ROOT}/moderator/getPayoutTypeBreakdown?timeRange=${this.currentTimeRange}`)
				]);

				const [metricsData, trendData, breakdownData] = await Promise.all([
					metricsRes.json(),
					trendRes.json(),
					breakdownRes.json()
				]);

				if (!metricsData.success) {
					throw new Error(metricsData.message || 'Failed to load payout metrics');
				}

				if (!trendData.success) {
					throw new Error(trendData.message || 'Failed to load payout trend');
				}

				if (!breakdownData.success) {
					throw new Error(breakdownData.message || 'Failed to load payout breakdown');
				}

				this.updateMetrics(metricsData.metrics);
				this.updatePayoutTrendChart(trendData.trend);
				this.updatePayoutDistributionChart(breakdownData.breakdown);
			} catch (error) {
				console.error('Error loading payout analysis dashboard:', error);
				window.showNotification?.('Failed to load payout analysis', 'error');
			}
		}

		updateMetrics(metrics) {
			this.setText('totalPaidValue', `LKR ${this.formatCurrency(metrics.totalPaid)}`);
			this.setText('driverPaidValue', `LKR ${this.formatCurrency(metrics.driverPaid)}`);
			this.setText('guidePaidValue', `LKR ${this.formatCurrency(metrics.guidePaid)}`);
			this.setText('pendingPayoutCount', `${metrics.pendingCount ?? 0}`);
		}

		updatePayoutTrendChart(trend) {
			const ctx = document.getElementById('payoutTrendChart');
			if (!ctx) return;

			if (!this.payoutTrendChart) {
				this.payoutTrendChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: [],
						datasets: [
							{
								label: 'Driver Payouts',
								data: [],
								borderColor: '#2563eb',
								backgroundColor: 'rgba(37, 99, 235, 0.10)',
								fill: true,
								tension: 0.35
							},
							{
								label: 'Guide Payouts',
								data: [],
								borderColor: '#d97706',
								backgroundColor: 'rgba(217, 119, 6, 0.10)',
								fill: true,
								tension: 0.35
							},
							{
								label: 'Total Payouts',
								data: [],
								borderColor: '#0d9488',
								backgroundColor: 'rgba(13, 148, 136, 0.08)',
								fill: false,
								tension: 0.35,
								borderDash: [6, 6]
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						interaction: { mode: 'index', intersect: false },
						plugins: {
							legend: { position: 'top' },
							tooltip: {
								callbacks: {
									label: (context) => `${context.dataset.label}: LKR ${this.formatCurrency(context.parsed.y)}`
								}
							}
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

			this.payoutTrendChart.data.labels = trend.labels || [];
			this.payoutTrendChart.data.datasets[0].data = trend.driverPaid || [];
			this.payoutTrendChart.data.datasets[1].data = trend.guidePaid || [];
			this.payoutTrendChart.data.datasets[2].data = trend.totalPaid || [];
			this.payoutTrendChart.update();
		}

		updatePayoutDistributionChart(breakdown) {
			const ctx = document.getElementById('payoutDistributionChart');
			if (!ctx) return;

			if (!this.payoutDistributionChart) {
				this.payoutDistributionChart = new Chart(ctx, {
					type: 'doughnut',
					data: {
						labels: ['Driver Paid', 'Guide Paid', 'Refunded'],
						datasets: [{
							data: [0, 0, 0],
							backgroundColor: ['#2563eb', '#d97706', '#dc2626'],
							borderWidth: 2,
							borderColor: '#ffffff'
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { position: 'bottom' },
							tooltip: {
								callbacks: {
									label: (context) => `${context.label}: LKR ${this.formatCurrency(context.parsed)}`
								}
							}
						}
					}
				});
			}

			this.payoutDistributionChart.data.datasets[0].data = [
				breakdown.driverPaid || 0,
				breakdown.guidePaid || 0,
				breakdown.refundedAmount || 0
			];
			this.payoutDistributionChart.update();
		}

		async loadUserPayoutAnalysis() {
			const userIdInput = document.getElementById('payoutUserIdInput');
			if (!userIdInput) return;

			const userId = parseInt(userIdInput.value, 10);
			if (!userId || userId <= 0) {
				window.showNotification?.('Enter a valid user ID', 'warning');
				return;
			}

			try {
				const response = await fetch(
					`${this.URL_ROOT}/moderator/getUserPayoutAnalysis?userId=${userId}&timeRange=${this.currentTimeRange}&viewType=${this.currentViewType}`
				);
				const data = await response.json();

				if (!data.success) {
					throw new Error(data.message || 'Failed to load user payout analysis');
				}

				this.renderUserProfile(data.profile);
				this.renderUserSummary(data.summary);
				this.updateUserTrendChart(data.trend, data.profile);
				this.renderRecentPayouts(data.recentPayouts || []);

				const emptyState = document.getElementById('userPayoutEmptyState');
				const content = document.getElementById('userPayoutContent');
				if (emptyState) emptyState.style.display = 'none';
				if (content) content.style.display = 'flex';
			} catch (error) {
				console.error('Error loading user payout analysis:', error);
				window.showNotification?.(error.message, 'error');
			}
		}

		renderUserProfile(profile) {
			const container = document.getElementById('userProfileCard');
			if (!container) return;

			const avatarUrl = profile.profile_photo
				? `${this.UPLOAD_ROOT}/${profile.profile_photo}`
				: `${this.URL_ROOT}/public/img/default-avatar.png`;

			container.innerHTML = `
				<img class="user-avatar" src="${avatarUrl}" alt="${profile.fullname}" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
				<div class="user-info">
					<h4>${profile.fullname}</h4>
					<div class="user-meta">ID: ${profile.id} | ${profile.email}</div>
					<div class="user-meta">${profile.phone || 'No phone available'}</div>
				</div>
				<span class="user-type-badge ${profile.account_type}">${profile.account_type}</span>
			`;
		}

		renderUserSummary(summary) {
			this.setText('userTotalPaid', `LKR ${this.formatCurrency(summary.totalPaid || 0)}`);
			this.setText('userCompletedCount', `${summary.completedCount || 0}`);
			this.setText('userPendingCount', `${summary.pendingCount || 0}`);
			this.setText('userRefundedAmount', `LKR ${this.formatCurrency(summary.refundedAmount || 0)}`);
		}

		updateUserTrendChart(trend, profile) {
			const ctx = document.getElementById('userPayoutTrendChart');
			if (!ctx) return;

			this.setText('userTrendLabel', `${profile.account_type} payout earnings in selected period`);

			if (!this.userPayoutTrendChart) {
				this.userPayoutTrendChart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: [],
						datasets: [{
							label: 'Payout Earnings',
							data: [],
							backgroundColor: profile.account_type === 'driver' ? '#2563eb' : '#d97706',
							borderRadius: 6,
							maxBarThickness: 34
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false },
							tooltip: {
								callbacks: {
									label: (context) => `LKR ${this.formatCurrency(context.parsed.y)}`
								}
							}
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

			this.userPayoutTrendChart.data.labels = trend.labels || [];
			this.userPayoutTrendChart.data.datasets[0].data = trend.paidAmounts || [];
			this.userPayoutTrendChart.data.datasets[0].backgroundColor = profile.account_type === 'driver' ? '#2563eb' : '#d97706';
			this.userPayoutTrendChart.update();
		}

		renderRecentPayouts(records) {
			const tbody = document.getElementById('userPayoutTableBody');
			if (!tbody) return;

			if (!records.length) {
				tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No payout records found</td></tr>';
				return;
			}

			tbody.innerHTML = records.map((row) => `
				<tr>
					<td>${row.payoutId}</td>
					<td>${row.tripId || 'N/A'}</td>
					<td><span class="status-badge ${row.payoutStatus}">${row.payoutStatus}</span></td>
					<td>LKR ${this.formatCurrency(row.amount || 0)}</td>
					<td>${this.formatDate(row.payoutDate)}</td>
					<td>${row.transactionId || 'N/A'}</td>
				</tr>
			`).join('');
		}

		setText(elementId, text) {
			const element = document.getElementById(elementId);
			if (element) {
				element.textContent = text;
			}
		}

		formatCurrency(value) {
			return Number(value || 0).toLocaleString('en-LK', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		async loadTopEarnersData() {
			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getTopEarners?timeRange=${this.currentTimeRange}&limit=5`);
				const data = await response.json();

				if (!data.success) {
					throw new Error(data.message || 'Failed to load top earners data');
				}

				this.updateTopEarnersChart(data.topEarners || []);

			} catch (error) {
				console.error('Error loading top earners data:', error);
				// Show empty chart on error
				this.updateTopEarnersChart([]);
			}
		}

		updateTopEarnersChart(earners) {
			const ctx = document.getElementById('topEarnersChart');
			if (!ctx) return;

			// Destroy existing chart
			if (this.topEarnersChart) {
				this.topEarnersChart.destroy();
			}

			// Use shorter labels for x-axis
			const labels = earners.map((earner, index) => `Earner ${index + 1}`);
			const earnings = earners.map(earner => earner.total_earned);

			const colors = [
				'#2563eb', // Blue
				'#d97706', // Orange
				'#dc2626', // Red
				'#16a34a', // Green
				'#9333ea'  // Purple
			];

			this.topEarnersChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: [{
						label: 'Total Earnings',
						data: earnings,
						backgroundColor: colors.slice(0, earners.length),
						borderWidth: 1,
						borderRadius: 4,
						borderSkipped: false
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { display: false },
						tooltip: {
							callbacks: {
								label: (context) => {
									const earner = earners[context.dataIndex];
									return [
										`Earnings: LKR ${this.formatCurrency(context.parsed.y)}`,
										`Payments: ${earner.total_payments}`,
										`Type: ${earner.account_type.charAt(0).toUpperCase() + earner.account_type.slice(1)}`
									];
								}
							}
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								callback: (value) => `LKR ${Number(value).toLocaleString()}`
							}
						},
						x: {
							ticks: {
								maxRotation: 0,
								minRotation: 0
							}
						}
					}
				}
			});

			// Create custom legend
			this.createTopEarnersLegend(earners, colors);
		}

		createTopEarnersLegend(earners, colors) {
			const legendContainer = document.getElementById('topEarnersLegend');
			if (!legendContainer) return;

			if (!earners.length) {
				legendContainer.innerHTML = '<p class="legend-empty">No earners data available</p>';
				return;
			}

			const legendItems = earners.map((earner, index) => `
				<div class="legend-item">
					<div class="legend-color" style="background-color: ${colors[index]}"></div>
					<div class="legend-text">
						<span class="legend-name">${earner.name}</span>
						<span class="legend-type">(${earner.account_type})</span>
					</div>
				</div>
			`).join('');

			legendContainer.innerHTML = `<div class="legend-grid">${legendItems}</div>`;
		}

		switchToTab(targetId) {
			// Update navigation
			document.querySelectorAll('.nav-link').forEach(link => {
				link.classList.remove('active');
			});
			const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
			if (activeLink) {
				activeLink.classList.add('active');
			}

			// Update sections
			document.querySelectorAll('.payout-section').forEach(sec => {
				sec.style.display = 'none';
			});

			const sectionElement = document.getElementById(targetId);
			if (sectionElement) {
				sectionElement.style.display = 'block';
				
				// Load top earners data when switching to user explorer
				if (targetId === 'user-explorer-section') {
					this.loadTopEarnersData();
				}
			}
		}

		formatDate(value) {
			if (!value) return 'N/A';
			const date = new Date(value);
			if (Number.isNaN(date.getTime())) return 'N/A';

			return date.toLocaleDateString('en-LK', {
				year: 'numeric',
				month: 'short',
				day: 'numeric'
			});
		}
	}

	window.PayoutAnalysisManager = PayoutAnalysisManager;
	window.payoutAnalysisManager = new PayoutAnalysisManager();
})();
