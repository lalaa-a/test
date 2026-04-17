(function () {
    if (window.AdminDashboardHome) {
        if (window.adminDashboardHome && typeof window.adminDashboardHome.destroy === 'function') {
            window.adminDashboardHome.destroy();
        }
        delete window.AdminDashboardHome;
        delete window.adminDashboardHome;
    }

    class AdminDashboardHome {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.refreshButton = document.getElementById('refreshDashboardSummaryBtn');
            this.timeRangeSelect = document.getElementById('dashboardOverviewTimeRange');
            this.summaryCards = Array.from(document.querySelectorAll('.summary-card'));
            this.overviewMetricCards = Array.from(document.querySelectorAll('.overview-metric-card'));
            this.overviewChartCards = Array.from(document.querySelectorAll('.overview-chart-card'));
            this.summaryElements = {
                pendingVerifications: document.getElementById('summaryPendingVerifications'),
                openSupportChats: document.getElementById('summaryOpenSupportChats'),
                pendingPayouts: document.getElementById('summaryPendingPayouts'),
                ongoingTrips: document.getElementById('summaryOngoingTrips')
            };
            this.summaryMetaElements = {
                pendingVerifications: document.getElementById('summaryPendingVerificationsMeta'),
                openSupportChats: document.getElementById('summaryOpenSupportChatsMeta'),
                pendingPayouts: document.getElementById('summaryPendingPayoutsMeta'),
                ongoingTrips: document.getElementById('summaryOngoingTripsMeta')
            };
            this.overviewMetricElements = {
                totalUsers: document.getElementById('overviewTotalUsers'),
                verifiedAccounts: document.getElementById('overviewVerifiedAccounts'),
                totalVehicles: document.getElementById('overviewTotalVehicles'),
                siteProfit: document.getElementById('overviewSiteProfit')
            };
            this.overviewMetricMetaElements = {
                totalUsers: document.getElementById('overviewTotalUsersMeta'),
                verifiedAccounts: document.getElementById('overviewVerifiedAccountsMeta'),
                totalVehicles: document.getElementById('overviewTotalVehiclesMeta'),
                siteProfit: document.getElementById('overviewSiteProfitMeta')
            };
            this.charts = {};
            this.handleRefreshClick = this.loadDashboardData.bind(this);
            this.handleTimeRangeChange = this.loadPlatformOverview.bind(this);

            this.init();
        }

        init() {
            if (this.refreshButton) {
                this.refreshButton.addEventListener('click', this.handleRefreshClick);
            }

            if (this.timeRangeSelect) {
                this.timeRangeSelect.addEventListener('change', this.handleTimeRangeChange);
            }

            this.loadDashboardData();
        }

        async loadDashboardData() {
            this.setRefreshButtonLoading(true);

            try {
                await Promise.allSettled([
                    this.loadSummaryStats(),
                    this.loadPlatformOverview()
                ]);
            } finally {
                this.setRefreshButtonLoading(false);
            }
        }

        async loadSummaryStats() {
            this.setSummaryLoadingState(true);

            try {
                const [
                    accountsResponse,
                    licensesResponse,
                    vehiclesResponse,
                    supportChatsResponse,
                    driverPayoutsResponse,
                    guidePayoutsResponse,
                    tripLogsResponse
                ] = await Promise.all([
                    fetch(`${this.URL_ROOT}/Admin/getAccounts/pending`),
                    fetch(`${this.URL_ROOT}/Admin/getPendingLicenses`),
                    fetch(`${this.URL_ROOT}/Admin/getPendingVehicles`),
                    fetch(`${this.URL_ROOT}/helpc/getChatsForModerator`),
                    fetch(`${this.URL_ROOT}/Admin/getPendingDriverPayouts`),
                    fetch(`${this.URL_ROOT}/Admin/getPendingGuidePayouts`),
                    fetch(`${this.URL_ROOT}/Admin/getTripLogs`)
                ]);

                const [
                    accountsData,
                    licensesData,
                    vehiclesData,
                    supportChatsData,
                    driverPayoutsData,
                    guidePayoutsData,
                    tripLogsData
                ] = await Promise.all([
                    accountsResponse.json(),
                    licensesResponse.json(),
                    vehiclesResponse.json(),
                    supportChatsResponse.json(),
                    driverPayoutsResponse.json(),
                    guidePayoutsResponse.json(),
                    tripLogsResponse.json()
                ]);

                if (!accountsResponse.ok || !accountsData.success) {
                    throw new Error(accountsData.message || 'Failed to load pending accounts');
                }

                if (!licensesResponse.ok || !licensesData.success) {
                    throw new Error(licensesData.message || 'Failed to load pending licenses');
                }

                if (!vehiclesResponse.ok || !vehiclesData.success) {
                    throw new Error(vehiclesData.message || 'Failed to load pending vehicles');
                }

                if (!driverPayoutsResponse.ok || !driverPayoutsData.success) {
                    throw new Error(driverPayoutsData.message || 'Failed to load pending driver payouts');
                }

                if (!guidePayoutsResponse.ok || !guidePayoutsData.success) {
                    throw new Error(guidePayoutsData.message || 'Failed to load pending guide payouts');
                }

                if (!tripLogsResponse.ok || !tripLogsData.success) {
                    throw new Error(tripLogsData.message || 'Failed to load trip logs');
                }

                const verificationCounts = {
                    accounts: Array.isArray(accountsData.accounts) ? accountsData.accounts.length : 0,
                    licenses: Array.isArray(licensesData.licenses) ? licensesData.licenses.length : 0,
                    vehicles: Array.isArray(vehiclesData.vehicles) ? vehiclesData.vehicles.length : 0
                };

                const pendingVerifications = verificationCounts.accounts + verificationCounts.licenses + verificationCounts.vehicles;
                const openSupportChats = this.getOpenSupportChatsCount(supportChatsData);
                const pendingPayouts = this.getPendingPayoutsCount(driverPayoutsData, guidePayoutsData);
                const ongoingTrips = this.getTripCountByStatus(tripLogsData, 'ongoing');

                this.summaryElements.pendingVerifications.textContent = this.formatCount(pendingVerifications);
                this.summaryElements.openSupportChats.textContent = this.formatCount(openSupportChats);
                this.summaryElements.pendingPayouts.textContent = this.formatCount(pendingPayouts);
                this.summaryElements.ongoingTrips.textContent = this.formatCount(ongoingTrips);

                this.summaryMetaElements.pendingVerifications.textContent = `${this.formatCount(verificationCounts.accounts)} accounts, ${this.formatCount(verificationCounts.licenses)} licenses, ${this.formatCount(verificationCounts.vehicles)} vehicles`;
                this.summaryMetaElements.openSupportChats.textContent = openSupportChats === 0
                    ? 'No users are waiting in the helpdesk right now'
                    : `${this.formatCount(openSupportChats)} support conversation${openSupportChats === 1 ? '' : 's'} currently open`;
                this.summaryMetaElements.pendingPayouts.textContent = pendingPayouts === 0
                    ? 'No driver or guide payouts are pending right now'
                    : `${this.formatCount(pendingPayouts)} driver and guide payout${pendingPayouts === 1 ? '' : 's'} awaiting action`;
                this.summaryMetaElements.ongoingTrips.textContent = ongoingTrips === 0
                    ? 'No trips are marked as ongoing right now'
                    : `${this.formatCount(ongoingTrips)} trip${ongoingTrips === 1 ? '' : 's'} currently in progress`;

                this.markSummaryErrorState(false);
            } catch (error) {
                console.error('Error loading dashboard summary:', error);
                this.renderSummaryError(error);
            } finally {
                this.setSummaryLoadingState(false);
            }
        }

        async loadPlatformOverview() {
            this.setOverviewLoadingState(true);

            try {
                const timeRange = this.timeRangeSelect ? this.timeRangeSelect.value : '90days';
                const [
                    userBaseStatsResponse,
                    verificationStatsResponse,
                    registrationTrendResponse,
                    vehicleStatsResponse,
                    vehicleBreakdownResponse,
                    earningsMetricsResponse,
                    revenueTrendResponse
                ] = await Promise.all([
                    fetch(`${this.URL_ROOT}/Admin/getUserBaseStats`),
                    fetch(`${this.URL_ROOT}/Admin/getVerificationStats`),
                    fetch(`${this.URL_ROOT}/Admin/getRegistrationTrend?timeRange=${timeRange}`),
                    fetch(`${this.URL_ROOT}/Admin/getVehicleVerificationStats`),
                    fetch(`${this.URL_ROOT}/Admin/getVehicleVerificationStatusBreakdown`),
                    fetch(`${this.URL_ROOT}/Admin/getEarningsMetrics?timeRange=${timeRange}&viewType=daily`),
                    fetch(`${this.URL_ROOT}/Admin/getRevenueTrend?timeRange=${timeRange}&viewType=daily`)
                ]);

                const [
                    userBaseStatsData,
                    verificationStatsData,
                    registrationTrendData,
                    vehicleStatsData,
                    vehicleBreakdownData,
                    earningsMetricsData,
                    revenueTrendData
                ] = await Promise.all([
                    userBaseStatsResponse.json(),
                    verificationStatsResponse.json(),
                    registrationTrendResponse.json(),
                    vehicleStatsResponse.json(),
                    vehicleBreakdownResponse.json(),
                    earningsMetricsResponse.json(),
                    revenueTrendResponse.json()
                ]);

                if (!userBaseStatsResponse.ok || !userBaseStatsData.success) {
                    throw new Error(userBaseStatsData.message || 'Failed to load user base stats');
                }

                if (!verificationStatsResponse.ok || !verificationStatsData.success) {
                    throw new Error(verificationStatsData.message || 'Failed to load verification stats');
                }

                if (!registrationTrendResponse.ok || !registrationTrendData.success) {
                    throw new Error(registrationTrendData.message || 'Failed to load registration trend');
                }

                if (!vehicleStatsResponse.ok || !vehicleStatsData.success) {
                    throw new Error(vehicleStatsData.message || 'Failed to load vehicle stats');
                }

                if (!vehicleBreakdownResponse.ok || !vehicleBreakdownData.success) {
                    throw new Error(vehicleBreakdownData.message || 'Failed to load vehicle status breakdown');
                }

                if (!earningsMetricsResponse.ok || !earningsMetricsData.success) {
                    throw new Error(earningsMetricsData.message || 'Failed to load earnings metrics');
                }

                if (!revenueTrendResponse.ok || !revenueTrendData.success) {
                    throw new Error(revenueTrendData.message || 'Failed to load revenue trend');
                }

                this.updateOverviewMetrics(
                    userBaseStatsData.stats,
                    verificationStatsData.stats,
                    vehicleStatsData.stats,
                    earningsMetricsData.metrics
                );

                this.renderRegistrationTrendChart(registrationTrendData.trend || {});
                this.renderVerificationMixChart((verificationStatsData.stats && verificationStatsData.stats.overall) || {});
                this.renderRevenueTrendChart(revenueTrendData.trend || {});
                this.renderVehicleStatusChart(vehicleBreakdownData.breakdown || {});

                this.markOverviewErrorState(false);
            } catch (error) {
                console.error('Error loading platform overview:', error);
                this.renderOverviewError(error);
            } finally {
                this.setOverviewLoadingState(false);
            }
        }

        updateOverviewMetrics(userStats, verificationStats, vehicleStats, earningsMetrics) {
            const totalUsers = Number(userStats.totalUsers || 0);
            const verifiedAccounts = Number(verificationStats.overall?.verified || 0);
            const totalVehicles = Number(vehicleStats.totalVehicles || 0);
            const siteProfit = Number(earningsMetrics.siteProfit || 0);
            const drivers = Number(userStats.drivers || 0);
            const guides = Number(userStats.guides || 0);
            const tourists = Number(userStats.regUsers || 0);
            const pendingAccounts = Number(verificationStats.overall?.pending || 0);
            const pendingVehicles = Number(vehicleStats.pendingVerifications || 0);

            this.overviewMetricElements.totalUsers.textContent = this.formatCount(totalUsers);
            this.overviewMetricElements.verifiedAccounts.textContent = this.formatCount(verifiedAccounts);
            this.overviewMetricElements.totalVehicles.textContent = this.formatCount(totalVehicles);
            this.overviewMetricElements.siteProfit.textContent = `LKR ${this.formatCurrency(siteProfit)}`;

            this.overviewMetricMetaElements.totalUsers.textContent = `${this.formatCount(drivers)} drivers, ${this.formatCount(guides)} guides, ${this.formatCount(tourists)} tourists`;
            this.overviewMetricMetaElements.verifiedAccounts.textContent = pendingAccounts === 0
                ? 'No accounts are currently pending review'
                : `${this.formatCount(pendingAccounts)} accounts are still pending approval`;
            this.overviewMetricMetaElements.totalVehicles.textContent = pendingVehicles === 0
                ? 'Vehicle verification queue is currently clear'
                : `${this.formatCount(pendingVehicles)} vehicles are still waiting for review`;
            this.overviewMetricMetaElements.siteProfit.textContent = `Total revenue ${this.formatCurrencyLabel(earningsMetrics.totalRevenue || 0)} in the selected range`;
        }

        renderRegistrationTrendChart(trend) {
            const canvas = document.getElementById('overviewRegistrationTrendChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            if (this.charts.registrationTrend) {
                this.charts.registrationTrend.destroy();
            }

            this.charts.registrationTrend = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: trend.labels || [],
                    datasets: [
                        {
                            label: 'Drivers',
                            data: trend.drivers || [],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.10)',
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Guides',
                            data: trend.guides || [],
                            borderColor: '#d97706',
                            backgroundColor: 'rgba(217, 119, 6, 0.10)',
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Tourists',
                            data: trend.tourists || [],
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.10)',
                            fill: true,
                            tension: 0.35
                        }
                    ]
                },
                options: this.getLineChartOptions('Registrations')
            });
        }

        renderVerificationMixChart(overall) {
            const canvas = document.getElementById('overviewVerificationMixChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            if (this.charts.verificationMix) {
                this.charts.verificationMix.destroy();
            }

            this.charts.verificationMix = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Verified', 'Pending', 'Rejected', 'Not Applied'],
                    datasets: [{
                        data: [
                            overall.verified || 0,
                            overall.pending || 0,
                            overall.rejected || 0,
                            overall.notApplied || 0
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#94a3b8'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: this.getDoughnutChartOptions()
            });
        }

        renderRevenueTrendChart(trend) {
            const canvas = document.getElementById('overviewRevenueTrendChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            if (this.charts.revenueTrend) {
                this.charts.revenueTrend.destroy();
            }

            this.charts.revenueTrend = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: trend.labels || [],
                    datasets: [
                        {
                            label: 'Total Revenue',
                            data: trend.totalRevenue || [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.10)',
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Site Profit',
                            data: trend.siteProfit || [],
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.10)',
                            fill: true,
                            tension: 0.35
                        }
                    ]
                },
                options: this.getCurrencyLineChartOptions()
            });
        }

        renderVehicleStatusChart(breakdown) {
            const canvas = document.getElementById('overviewVehicleStatusChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            if (this.charts.vehicleStatus) {
                this.charts.vehicleStatus.destroy();
            }

            this.charts.vehicleStatus = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [
                            breakdown.approved || 0,
                            breakdown.pending || 0,
                            breakdown.rejected || 0
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: this.getDoughnutChartOptions()
            });
        }

        getLineChartOptions(yLabel) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: yLabel
                        },
                        ticks: {
                            callback: (value) => this.formatCount(value)
                        }
                    }
                }
            };
        }

        getCurrencyLineChartOptions() {
            return {
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
                        title: {
                            display: true,
                            text: 'Amount (LKR)'
                        },
                        ticks: {
                            callback: (value) => `LKR ${this.formatCurrency(value)}`
                        }
                    }
                }
            };
        }

        getDoughnutChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.label}: ${this.formatCount(context.parsed)}`
                        }
                    }
                }
            };
        }

        getOpenSupportChatsCount(supportChatsData) {
            if (supportChatsData && supportChatsData.status === 'success' && Array.isArray(supportChatsData.chats)) {
                return supportChatsData.chats.filter((chat) => chat.status === 'Open').length;
            }

            return 0;
        }

        getPendingPayoutsCount(driverPayoutsData, guidePayoutsData) {
            const driverPending = driverPayoutsData && Array.isArray(driverPayoutsData.payouts)
                ? driverPayoutsData.payouts.length
                : 0;
            const guidePending = guidePayoutsData && Array.isArray(guidePayoutsData.payouts)
                ? guidePayoutsData.payouts.length
                : 0;

            return driverPending + guidePending;
        }

        getTripCountByStatus(tripLogsData, status) {
            if (!tripLogsData || !Array.isArray(tripLogsData.trips)) {
                return 0;
            }

            return tripLogsData.trips.filter((trip) => trip.status === status).length;
        }

        renderSummaryError(error) {
            Object.values(this.summaryElements).forEach((element) => {
                if (element) {
                    element.textContent = '--';
                }
            });

            Object.values(this.summaryMetaElements).forEach((element) => {
                if (element) {
                    element.textContent = error.message || 'Unable to load summary data right now.';
                }
            });

            this.markSummaryErrorState(true);
        }

        renderOverviewError(error) {
            Object.values(this.overviewMetricElements).forEach((element) => {
                if (element) {
                    element.textContent = '--';
                }
            });

            Object.values(this.overviewMetricMetaElements).forEach((element) => {
                if (element) {
                    element.textContent = error.message || 'Unable to load overview data right now.';
                }
            });

            this.destroyCharts();
            this.markOverviewErrorState(true);
        }

        destroyCharts() {
            Object.keys(this.charts).forEach((key) => {
                if (this.charts[key] && typeof this.charts[key].destroy === 'function') {
                    this.charts[key].destroy();
                }
            });

            this.charts = {};
        }

        setSummaryLoadingState(isLoading) {
            this.summaryCards.forEach((card) => {
                card.classList.toggle('is-loading', isLoading);
            });
        }

        setOverviewLoadingState(isLoading) {
            this.overviewMetricCards.forEach((card) => {
                card.classList.toggle('is-loading', isLoading);
            });

            this.overviewChartCards.forEach((card) => {
                card.classList.toggle('is-loading', isLoading);
            });
        }

        setRefreshButtonLoading(isLoading) {
            if (this.refreshButton) {
                this.refreshButton.disabled = isLoading;
                this.refreshButton.innerHTML = isLoading
                    ? '<i class="fas fa-spinner fa-spin"></i> Refreshing'
                    : '<i class="fas fa-rotate-right"></i> Refresh Summary';
            }
        }

        markSummaryErrorState(hasError) {
            this.summaryCards.forEach((card) => {
                card.classList.toggle('is-error', hasError);
            });
        }

        markOverviewErrorState(hasError) {
            this.overviewMetricCards.forEach((card) => {
                card.classList.toggle('is-error', hasError);
            });

            this.overviewChartCards.forEach((card) => {
                card.classList.toggle('is-error', hasError);
            });
        }

        formatCount(count) {
            return Number(count || 0).toLocaleString();
        }

        formatCurrency(value) {
            return Number(value || 0).toLocaleString(undefined, {
                maximumFractionDigits: 0
            });
        }

        formatCurrencyLabel(value) {
            return `LKR ${this.formatCurrency(value)}`;
        }

        destroy() {
            if (this.refreshButton) {
                this.refreshButton.removeEventListener('click', this.handleRefreshClick);
            }

            if (this.timeRangeSelect) {
                this.timeRangeSelect.removeEventListener('change', this.handleTimeRangeChange);
            }

            this.destroyCharts();
        }
    }

    window.AdminDashboardHome = AdminDashboardHome;
    window.adminDashboardHome = new AdminDashboardHome();
})();
