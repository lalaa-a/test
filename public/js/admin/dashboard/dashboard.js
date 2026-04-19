(function () {
    if (window.AdminDashboardManager) {
        if (window.adminDashboardManager && typeof window.adminDashboardManager.destroy === 'function') {
            window.adminDashboardManager.destroy();
        }
        delete window.adminDashboardManager;
        delete window.AdminDashboardManager;
    }

    class AdminDashboardManager {
        constructor() {
            this.root = document.getElementById('adminDashboardPage');
            if (!this.root) {
                return;
            }

            this.urlRoot = this.root.dataset.urlRoot || 'http://localhost/test';
            this.refreshBtn = document.getElementById('admDashRefreshBtn');

            this.charts = {
                queue: null,
                revenue: null,
                registration: null
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
                earningsMetrics: null,
                revenueTrend: null,
                registrationTrend: null,
                tripLogs: [],
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
            Object.keys(this.charts).forEach((key) => {
                if (this.charts[key]) {
                    this.charts[key].destroy();
                    this.charts[key] = null;
                }
            });
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
                    earningsRes,
                    revenueTrendRes,
                    registrationTrendRes,
                    tripLogsRes,
                    unreadRes
                ] = await Promise.allSettled([
                    this.fetchJson('/Admin/getAccounts/pending'),
                    this.fetchJson('/Admin/getPendingLicenses'),
                    this.fetchJson('/Admin/getPendingVehicles'),
                    this.fetchJson('/Admin/getAllComplaints'),
                    this.fetchJson('/Admin/getPaymentStats'),
                    this.fetchJson('/Admin/getDriverPayoutStats'),
                    this.fetchJson('/Admin/getGuidePayoutStats'),
                    this.fetchJson('/Admin/getPendingDriverPayouts'),
                    this.fetchJson('/Admin/getPendingGuidePayouts'),
                    this.fetchJson('/Admin/getEarningsMetrics?timeRange=30days&viewType=daily'),
                    this.fetchJson('/Admin/getRevenueTrend?timeRange=30days&viewType=daily'),
                    this.fetchJson('/Admin/getRegistrationTrend?timeRange=30days&viewType=daily'),
                    this.fetchJson('/Admin/getTripLogs'),
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
                this.state.earningsMetrics = this.readObjectPayload(earningsRes, 'metrics');
                this.state.revenueTrend = this.readObjectPayload(revenueTrendRes, 'trend');
                this.state.registrationTrend = this.readObjectPayload(registrationTrendRes, 'trend');
                this.state.tripLogs = this.readArrayPayload(tripLogsRes, 'trips');
                this.state.unreadMessages = this.readUnreadCount(unreadRes);

                // Match analytics behavior: if 30-day registration trend is empty,
                // fallback to 90-day trend so the chart still provides useful history.
                if (!this.hasRegistrationTrendData(this.state.registrationTrend)) {
                    try {
                        const fallbackRegistration = await this.fetchJson('/Admin/getRegistrationTrend?timeRange=90days&viewType=daily');
                        if (fallbackRegistration && fallbackRegistration.success) {
                            this.state.registrationTrend = fallbackRegistration.trend || this.state.registrationTrend;
                        }
                    } catch (fallbackError) {
                        console.warn('Registration trend fallback request failed:', fallbackError);
                    }
                }

                this.render();
            } catch (error) {
                console.error('Admin dashboard load failed:', error);
                if (window.showNotification) {
                    window.showNotification('Failed to load admin dashboard data', 'error');
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
            this.renderTripWatchlist();
            this.renderQueueChart();
            this.renderRevenueTrendChart();
            this.renderRegistrationTrendChart();
        }

        renderHeroSummary() {
            const today = new Date();
            this.setText(
                'admDashTodayDate',
                today.toLocaleDateString('en-GB', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                })
            );

            this.setText('admDashHeroQueue', this.getWorkQueueCount());
            this.setText('admDashHeroUnread', this.state.unreadMessages);
        }

        renderTopStats() {
            this.setText('admDashPendingAccounts', this.state.pendingAccounts.length);
            this.setText('admDashPendingLicenses', this.state.pendingLicenses.length);
            this.setText('admDashPendingVehicles', this.state.pendingVehicles.length);
            this.setText('admDashOpenComplaints', this.getOpenComplaintsCount());
            this.setText('admDashPendingPayouts', this.getPendingPayoutCount());
            this.setText('admDashActiveTrips', this.getActiveTripsCount());
            this.setText('admDashSiteProfit', this.formatCurrency(this.toNumber(this.state.earningsMetrics && this.state.earningsMetrics.siteProfit)));
        }

        renderQueueBreakdown() {
            const rows = [
                {
                    count: this.state.pendingAccounts.length,
                    countId: 'admDashQueueAccounts',
                    barId: 'admDashQueueAccountsBar'
                },
                {
                    count: this.state.pendingLicenses.length,
                    countId: 'admDashQueueLicenses',
                    barId: 'admDashQueueLicensesBar'
                },
                {
                    count: this.state.pendingVehicles.length,
                    countId: 'admDashQueueVehicles',
                    barId: 'admDashQueueVehiclesBar'
                },
                {
                    count: this.getOpenComplaintsCount(),
                    countId: 'admDashQueueComplaints',
                    barId: 'admDashQueueComplaintsBar'
                },
                {
                    count: this.getPendingPayoutCount(),
                    countId: 'admDashQueuePayouts',
                    barId: 'admDashQueuePayoutsBar'
                }
            ];

            const total = Math.max(rows.reduce((sum, row) => sum + row.count, 0), 1);

            rows.forEach((row) => {
                this.setText(row.countId, row.count);
                this.setBarWidth(row.barId, (row.count / total) * 100);
            });
        }

        renderFinanceSnapshot() {
            const paymentStats = this.state.paymentStats || {};

            const completedPayments = this.toNumber(paymentStats.completed_count);
            const cancelledPayments = this.toNumber(paymentStats.cancelled_count);
            const refundedPayments = this.toNumber(paymentStats.refunded_count);
            const totalRevenue = this.toNumber(paymentStats.total_revenue);

            this.setText('admDashCompletedPaymentsCount', completedPayments);
            this.setText('admDashCancelledPaymentsCount', cancelledPayments);
            this.setText('admDashRefundedPaymentsCount', refundedPayments);
            this.setText('admDashRevenueAmount', this.formatCurrency(totalRevenue));

            this.setText('admDashPendingPayoutCount', this.getPendingPayoutCount());
            this.setText('admDashPendingPayoutAmount', this.formatCurrency(this.getPendingPayoutAmount()));

            this.setText('admDashPaidPayoutCount', this.getPaidPayoutCount());
            this.setText('admDashPaidPayoutAmount', this.formatCurrency(this.getPaidPayoutAmount()));

            this.setText('admDashDriverRevenueAmount', this.formatCurrency(this.toNumber(this.state.earningsMetrics && this.state.earningsMetrics.driverRevenue)));
            this.setText('admDashGuideRevenueAmount', this.formatCurrency(this.toNumber(this.state.earningsMetrics && this.state.earningsMetrics.guideRevenue)));
        }

        renderComplaintList() {
            const listElement = document.getElementById('admDashComplaintList');
            const emptyElement = document.getElementById('admDashComplaintEmpty');
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
                        <div class="admdash-list-item">
                            <div class="admdash-list-main">
                                <h4>${this.escapeHtml(subject)}</h4>
                                <p>${this.escapeHtml(userName)}  |  ${this.escapeHtml(this.formatDate(complaint.createdAt))}</p>
                            </div>
                            <span class="admdash-status-badge ${this.escapeHtml(statusClass)}">${this.escapeHtml(statusLabel)}</span>
                        </div>
                    `;
                })
                .join('');
        }

        renderVerificationList() {
            const listElement = document.getElementById('admDashVerificationList');
            const emptyElement = document.getElementById('admDashVerificationEmpty');
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
                        <div class="admdash-list-item">
                            <div class="admdash-list-main">
                                <h4>${this.escapeHtml(entry.title)}</h4>
                                <p>${this.escapeHtml(entry.meta)}  |  ${this.escapeHtml(this.formatDate(entry.timestamp))}</p>
                            </div>
                            <span class="admdash-status-badge ${this.escapeHtml(entry.type)}">${this.escapeHtml(this.toTitleCase(entry.type))}</span>
                        </div>
                    `;
                })
                .join('');
        }

        renderPayoutList() {
            const listElement = document.getElementById('admDashPayoutList');
            const emptyElement = document.getElementById('admDashPayoutEmpty');
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
                        <div class="admdash-list-item">
                            <div class="admdash-list-main">
                                <h4>${this.escapeHtml(entry.title)}</h4>
                                <p>${this.escapeHtml(`Trip #${entry.tripId || '-'}  |  ${this.formatCurrency(entry.amount)}  |  ${this.formatDate(entry.timestamp)}`)}</p>
                            </div>
                            <span class="admdash-status-badge ${this.escapeHtml(entry.type)}">${this.escapeHtml(this.toTitleCase(entry.type))}</span>
                        </div>
                    `;
                })
                .join('');
        }

        renderTripWatchlist() {
            const listElement = document.getElementById('admDashTripList');
            const emptyElement = document.getElementById('admDashTripEmpty');
            if (!listElement || !emptyElement) {
                return;
            }

            const activeTrips = (this.state.tripLogs || [])
                .filter((trip) => {
                    const status = String(trip.status || '').toLowerCase();
                    return status === 'scheduled' || status === 'ongoing';
                })
                .sort((a, b) => this.readTimestamp(a.startDate || a.createdAt) - this.readTimestamp(b.startDate || b.createdAt))
                .slice(0, 6);

            if (activeTrips.length === 0) {
                listElement.innerHTML = '';
                emptyElement.style.display = 'block';
                return;
            }

            emptyElement.style.display = 'none';

            listElement.innerHTML = activeTrips
                .map((trip) => {
                    const status = String(trip.status || 'scheduled').toLowerCase();
                    const title = trip.tripTitle || `Trip #${trip.tripId || '-'}`;
                    const travellerName = trip.travellerName || 'Traveller';

                    return `
                        <div class="admdash-list-item">
                            <div class="admdash-list-main">
                                <h4>${this.escapeHtml(title)}</h4>
                                <p>${this.escapeHtml(`Trip #${trip.tripId || '-'}  |  ${travellerName}  |  ${this.formatDate(trip.startDate)}`)}</p>
                            </div>
                            <span class="admdash-status-badge ${this.escapeHtml(status)}">${this.escapeHtml(this.toTitleCase(status))}</span>
                        </div>
                    `;
                })
                .join('');
        }

        renderQueueChart() {
            const labels = ['Accounts', 'Licenses', 'Vehicles', 'Complaints', 'Payouts'];
            const data = [
                this.state.pendingAccounts.length,
                this.state.pendingLicenses.length,
                this.state.pendingVehicles.length,
                this.getOpenComplaintsCount(),
                this.getPendingPayoutCount()
            ];

            const hasData = data.some((value) => value > 0);

            if (typeof Chart === 'undefined') {
                this.toggleChartEmpty('admDashQueueChartEmpty', true, 'Chart library unavailable.');
                return;
            }

            const canvas = document.getElementById('admDashQueueChart');
            if (!canvas) {
                return;
            }

            if (this.charts.queue) {
                this.charts.queue.destroy();
                this.charts.queue = null;
            }

            if (!hasData) {
                this.toggleChartEmpty('admDashQueueChartEmpty', true, 'Not enough queue data for chart.');
                return;
            }

            this.toggleChartEmpty('admDashQueueChartEmpty', false);

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
                            backgroundColor: ['#4f86e8', '#f3ad45', '#41af75', '#df5f75', '#4e9ad7']
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
                this.toggleChartEmpty('admDashRevenueTrendEmpty', true, 'Chart library unavailable.');
                return;
            }

            const canvas = document.getElementById('admDashRevenueTrendChart');
            if (!canvas) {
                return;
            }

            if (this.charts.revenue) {
                this.charts.revenue.destroy();
                this.charts.revenue = null;
            }

            if (labels.length === 0) {
                this.toggleChartEmpty('admDashRevenueTrendEmpty', true, 'No revenue trend data available yet.');
                return;
            }

            this.toggleChartEmpty('admDashRevenueTrendEmpty', false);

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

        renderRegistrationTrendChart() {
            const trend = this.state.registrationTrend || {};
            const labels = Array.isArray(trend.labels) ? trend.labels : [];
            const drivers = Array.isArray(trend.drivers) ? trend.drivers : [];
            const guides = Array.isArray(trend.guides) ? trend.guides : [];
            const tourists = Array.isArray(trend.tourists) ? trend.tourists : [];

            if (typeof Chart === 'undefined') {
                this.toggleChartEmpty('admDashRegistrationTrendEmpty', true, 'Chart library unavailable.');
                return;
            }

            const canvas = document.getElementById('admDashRegistrationTrendChart');
            if (!canvas) {
                return;
            }

            if (this.charts.registration) {
                this.charts.registration.destroy();
                this.charts.registration = null;
            }

            if (labels.length === 0) {
                this.toggleChartEmpty('admDashRegistrationTrendEmpty', true, 'No registration trend data available yet.');
                return;
            }

            this.toggleChartEmpty('admDashRegistrationTrendEmpty', false);

            this.charts.registration = new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Drivers',
                            data: drivers,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3
                        },
                        {
                            label: 'Guides',
                            data: guides,
                            borderColor: '#d97706',
                            backgroundColor: 'rgba(217, 119, 6, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3
                        },
                        {
                            label: 'Tourists',
                            data: tourists,
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.12)',
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
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        getWorkQueueCount() {
            const pendingVerification = this.state.pendingAccounts.length + this.state.pendingLicenses.length + this.state.pendingVehicles.length;
            return pendingVerification + this.getOpenComplaintsCount() + this.getPendingPayoutCount();
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
            return this.sumAmounts(this.state.pendingDriverPayouts, 'amount') + this.sumAmounts(this.state.pendingGuidePayouts, 'amount');
        }

        getPaidPayoutCount() {
            return this.toNumber(this.state.driverPayoutStats && this.state.driverPayoutStats.completed_count)
                + this.toNumber(this.state.guidePayoutStats && this.state.guidePayoutStats.completed_count);
        }

        getPaidPayoutAmount() {
            return this.toNumber(this.state.driverPayoutStats && this.state.driverPayoutStats.total_payout_amount)
                + this.toNumber(this.state.guidePayoutStats && this.state.guidePayoutStats.total_payout_amount);
        }

        getActiveTripsCount() {
            return (this.state.tripLogs || []).filter((trip) => {
                const status = String(trip.status || '').toLowerCase();
                return status === 'scheduled' || status === 'ongoing';
            }).length;
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

        hasRegistrationTrendData(trend) {
            if (!trend || typeof trend !== 'object') {
                return false;
            }

            return Array.isArray(trend.labels) && trend.labels.length > 0;
        }

        async fetchJson(path) {
            const response = await fetch(`${this.urlRoot}${path}`);
            const body = await response.text();

            let data;
            try {
                data = body ? JSON.parse(body) : {};
            } catch (error) {
                throw new Error(`Invalid JSON response from ${path}`);
            }

            if (!response.ok) {
                throw new Error((data && data.message) || `Request failed (${response.status})`);
            }

            return data;
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
            const time = date.getTime();
            return Number.isNaN(time) ? 0 : time;
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

    window.AdminDashboardManager = AdminDashboardManager;
    window.adminDashboardManager = new AdminDashboardManager();
})();