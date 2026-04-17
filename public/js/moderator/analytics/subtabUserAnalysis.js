(function(){
    // User Analysis JavaScript
    if (window.UserAnalysisManager) {
        console.log('UserAnalysisManager already exists, cleaning up...');
        if (window.userAnalysisManager) {
            delete window.userAnalysisManager;
        }
        delete window.UserAnalysisManager;
    }

    // User Analysis Manager
    class UserAnalysisManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentTimeRange = '90days';
            this.charts = {};

            this.init();
        }

        init() {
            const timeRangeSelect = document.getElementById('userAnalysisTimeRange');
            if (timeRangeSelect?.value) {
                this.currentTimeRange = timeRangeSelect.value;
            }

            this.bindEvents();
            this.loadUserAnalysisData();
        }

        bindEvents() {
            // Time range filter
            const timeRangeSelect = document.getElementById('userAnalysisTimeRange');
            if (timeRangeSelect) {
                timeRangeSelect.addEventListener('change', (e) => {
                    this.currentTimeRange = e.target.value;
                    this.loadUserAnalysisData();
                });
            }

            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToSection(targetId);
                });
            });
        }

        async loadUserAnalysisData() {
            try {
                const [userStatsRes, verificationStatsRes, registrationTrendRes, licenseStatsRes] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getUserBaseStats`),
                    fetch(`${this.URL_ROOT}/moderator/getVerificationStats`),
                    fetch(`${this.URL_ROOT}/moderator/getRegistrationTrend?timeRange=${this.currentTimeRange}`),
                    fetch(`${this.URL_ROOT}/moderator/getLicenseStats`)
                ]);

                const [userStats, verificationStats, registrationTrend, licenseStats] = await Promise.all([
                    userStatsRes.json(),
                    verificationStatsRes.json(),
                    registrationTrendRes.json(),
                    licenseStatsRes.json()
                ]);

                if (userStats.success) {
                    this.updateUserStats(userStats.stats);
                    this.updateUserDistributionChart(userStats.stats);
                }

                if (verificationStats.success) {
                    this.updateVerificationStats(verificationStats.stats);
                    this.updateVerificationCharts(verificationStats.stats);
                    this.updateVerificationOverviewChart(verificationStats.stats.overall);
                }

                if (registrationTrend.success) {
                    let trendData = registrationTrend.trend || {};

                    // If selected range has no registrations, auto-fallback to 90 days for visibility.
                    if ((!trendData.labels || trendData.labels.length === 0) && this.currentTimeRange === '30days') {
                        const fallbackRes = await fetch(`${this.URL_ROOT}/moderator/getRegistrationTrend?timeRange=90days`);
                        const fallbackJson = await fallbackRes.json();
                        if (fallbackJson.success) {
                            trendData = fallbackJson.trend || trendData;
                            this.currentTimeRange = '90days';
                            const timeRangeSelect = document.getElementById('userAnalysisTimeRange');
                            if (timeRangeSelect) {
                                timeRangeSelect.value = '90days';
                            }
                        }
                    }

                    this.updateRegistrationTrendChart(trendData);
                    this.updateGrowthCharts(this.buildGrowthData(trendData));
                }

                if (licenseStats.success) {
                    this.updateLicenseStats(licenseStats.stats);
                    this.updateLicenseCharts(licenseStats.stats);
                }

            } catch (error) {
                console.error('Error loading user analysis data:', error);
                window.showNotification?.('Failed to load user analysis data', 'error');
            }
        }

        updateUserStats(stats) {
            this.setText('totalUsersCount', this.formatNumber(stats.totalUsers || 0));
            this.setText('totalDriversCount', this.formatNumber(stats.drivers || 0));
            this.setText('totalGuidesCount', this.formatNumber(stats.guides || 0));
            this.setText('totalTouristsCount', this.formatNumber(stats.regUsers || 0));
            this.setText('verifiedUsersCount', this.formatNumber((stats.drivers || 0) + (stats.guides || 0))); // Verified drivers + guides
            this.setText('pendingVerificationsCount', this.formatNumber(0)); // This would need separate API call
        }

        updateVerificationStats(stats) {
            this.setText('verifiedDriversCount', this.formatNumber(stats.drivers.verified || 0));
            this.setText('verifiedGuidesCount', this.formatNumber(stats.guides.verified || 0));
            this.setText('pendingDriversCount', this.formatNumber(stats.drivers.pending || 0));
            this.setText('pendingGuidesCount', this.formatNumber(stats.guides.pending || 0));
            this.setText('rejectedDriversCount', this.formatNumber(stats.drivers.rejected || 0));
            this.setText('rejectedGuidesCount', this.formatNumber(stats.guides.rejected || 0));
        }

        updateLicenseStats(stats) {
            this.setText('driversWithLicenseCount', this.formatNumber(stats.drivers.verified || 0));
            this.setText('guidesWithLicenseCount', this.formatNumber(stats.guides.verified || 0));
            this.setText('pendingDriverLicensesCount', this.formatNumber(stats.drivers.pending || 0));
            this.setText('pendingGuideLicensesCount', this.formatNumber(stats.guides.pending || 0));
            this.setText('rejectedDriverLicensesCount', this.formatNumber(stats.drivers.rejected || 0));
            this.setText('rejectedGuideLicensesCount', this.formatNumber(stats.guides.rejected || 0));
        }

        updateUserDistributionChart(stats) {
            const ctx = document.getElementById('userDistributionChart');
            if (!ctx) return;

            if (this.charts.userDistribution) {
                this.charts.userDistribution.destroy();
            }

            this.charts.userDistribution = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Drivers', 'Guides', 'Regular Users'],
                    datasets: [{
                        data: [
                            stats.drivers || 0,
                            stats.guides || 0,
                            stats.regUsers || 0
                        ],
                        backgroundColor: ['#2563eb', '#d97706', '#7c3aed'],
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
                                label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                            }
                        }
                    }
                }
            });
        }

        updateVerificationCharts(stats) {
            // Driver verification chart
            const driverCtx = document.getElementById('driverVerificationChart');
            if (driverCtx) {
                if (this.charts.driverVerification) {
                    this.charts.driverVerification.destroy();
                }

                this.charts.driverVerification = new Chart(driverCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Verified', 'Pending', 'Rejected'],
                        datasets: [{
                            data: [
                                stats.drivers.verified || 0,
                                stats.drivers.pending || 0,
                                stats.drivers.rejected || 0
                            ],
                            backgroundColor: ['#0d9f6e', '#d97706', '#dc2626'],
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
                                    label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                                }
                            }
                        }
                    }
                });
            }

            // Guide verification chart
            const guideCtx = document.getElementById('guideVerificationChart');
            if (guideCtx) {
                if (this.charts.guideVerification) {
                    this.charts.guideVerification.destroy();
                }

                this.charts.guideVerification = new Chart(guideCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Verified', 'Pending', 'Rejected'],
                        datasets: [{
                            data: [
                                stats.guides.verified || 0,
                                stats.guides.pending || 0,
                                stats.guides.rejected || 0
                            ],
                            backgroundColor: ['#0d9f6e', '#d97706', '#dc2626'],
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
                                    label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                                }
                            }
                        }
                    }
                });
            }
        }

        updateVerificationOverviewChart(overallStats) {
            const ctx = document.getElementById('verificationOverviewChart');
            if (!ctx) return;

            if (this.charts.verificationOverview) {
                this.charts.verificationOverview.destroy();
            }

            this.charts.verificationOverview = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Verified', 'Pending', 'Rejected', 'Not Applied'],
                    datasets: [{
                        data: [
                            overallStats.verified || 0,
                            overallStats.pending || 0,
                            overallStats.rejected || 0,
                            overallStats.notApplied || 0
                        ],
                        backgroundColor: ['#0d9f6e', '#d97706', '#dc2626', '#6b7280'],
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
                                label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                            }
                        }
                    }
                }
            });
        }

        updateRegistrationTrendChart(trend) {
            const ctx = document.getElementById('registrationTrendChart');
            if (!ctx) return;

            if (this.charts.registrationTrend) {
                this.charts.registrationTrend.destroy();
            }

            this.charts.registrationTrend = new Chart(ctx, {
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
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${this.formatNumber(context.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => this.formatNumber(value)
                            }
                        }
                    }
                }
            });
        }

        buildGrowthData(trend) {
            const labels = trend.labels || [];
            const totals = (trend.totals && trend.totals.length)
                ? trend.totals
                : labels.map((_, index) =>
                    (trend.drivers?.[index] || 0) + (trend.guides?.[index] || 0) + (trend.tourists?.[index] || 0)
                );

            const monthlyMap = {};
            labels.forEach((label, index) => {
                const monthKey = String(label).slice(0, 7);
                monthlyMap[monthKey] = (monthlyMap[monthKey] || 0) + (totals[index] || 0);
            });

            const monthlyLabels = Object.keys(monthlyMap).sort();
            const monthlyData = monthlyLabels.map((key) => monthlyMap[key]);

            const peakRows = labels.map((label, index) => ({
                label,
                value: totals[index] || 0
            }))
                .sort((a, b) => b.value - a.value)
                .slice(0, 7)
                .sort((a, b) => String(a.label).localeCompare(String(b.label)));

            return {
                monthly: {
                    labels: monthlyLabels,
                    data: monthlyData
                },
                peakDays: {
                    labels: peakRows.map((row) => row.label),
                    data: peakRows.map((row) => row.value)
                }
            };
        }

        updateGrowthCharts(growth) {
            // Monthly growth chart
            const monthlyCtx = document.getElementById('monthlyGrowthChart');
            if (monthlyCtx) {
                if (this.charts.monthlyGrowth) {
                    this.charts.monthlyGrowth.destroy();
                }

                this.charts.monthlyGrowth = new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: growth.monthly.labels || [],
                        datasets: [{
                            label: 'New Registrations',
                            data: growth.monthly.data || [],
                            backgroundColor: '#0d9f6e',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `Registrations: ${this.formatNumber(context.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => this.formatNumber(value)
                                }
                            }
                        }
                    }
                });
            }

            // Peak days chart
            const peakCtx = document.getElementById('peakDaysChart');
            if (peakCtx) {
                if (this.charts.peakDays) {
                    this.charts.peakDays.destroy();
                }

                this.charts.peakDays = new Chart(peakCtx, {
                    type: 'bar',
                    data: {
                        labels: growth.peakDays.labels || [],
                        datasets: [{
                            label: 'Registrations',
                            data: growth.peakDays.data || [],
                            backgroundColor: '#7c3aed',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `Registrations: ${this.formatNumber(context.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => this.formatNumber(value)
                                }
                            }
                        }
                    }
                });
            }
        }

        updateLicenseCharts(stats) {
            // Driver license chart
            const driverLicenseCtx = document.getElementById('driverLicenseChart');
            if (driverLicenseCtx) {
                if (this.charts.driverLicense) {
                    this.charts.driverLicense.destroy();
                }

                this.charts.driverLicense = new Chart(driverLicenseCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Valid License', 'Pending', 'Rejected', 'No License'],
                        datasets: [{
                            data: [
                                stats.drivers.verified || 0,
                                stats.drivers.pending || 0,
                                stats.drivers.rejected || 0,
                                stats.drivers.notApplied || 0
                            ],
                            backgroundColor: ['#0d9f6e', '#d97706', '#dc2626', '#6b7280'],
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
                                    label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                                }
                            }
                        }
                    }
                });
            }

            // Guide license chart
            const guideLicenseCtx = document.getElementById('guideLicenseChart');
            if (guideLicenseCtx) {
                if (this.charts.guideLicense) {
                    this.charts.guideLicense.destroy();
                }

                this.charts.guideLicense = new Chart(guideLicenseCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Valid License', 'Pending', 'Rejected', 'No License'],
                        datasets: [{
                            data: [
                                stats.guides.verified || 0,
                                stats.guides.pending || 0,
                                stats.guides.rejected || 0,
                                stats.guides.notApplied || 0
                            ],
                            backgroundColor: ['#0d9f6e', '#d97706', '#dc2626', '#6b7280'],
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
                                    label: (context) => `${context.label}: ${this.formatNumber(context.parsed)}`
                                }
                            }
                        }
                    }
                });
            }
        }

        switchToSection(targetId) {
            // Update navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            // Update sections
            document.querySelectorAll('.analysis-section').forEach(sec => {
                sec.style.display = 'none';
            });

            const sectionElement = document.getElementById(targetId);
            if (sectionElement) {
                sectionElement.style.display = 'block';
            }
        }

        setText(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text;
            }
        }

        formatNumber(value) {
            return Number(value || 0).toLocaleString();
        }
    }

    window.UserAnalysisManager = UserAnalysisManager;
    window.userAnalysisManager = new UserAnalysisManager();
})();