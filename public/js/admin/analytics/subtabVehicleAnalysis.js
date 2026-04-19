(function(){
    // Vehicle Analysis JavaScript
    if (window.VehicleAnalysisManager) {
        console.log('VehicleAnalysisManager already exists, cleaning up...');
        if (window.vehicleAnalysisManager) {
            delete window.vehicleAnalysisManager;
        }
        delete window.VehicleAnalysisManager;
    }

    // Vehicle Analysis Manager
    class VehicleAnalysisManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentTimeRange = '90days';
            this.charts = {};

            this.init();
        }

        init() {
            const timeRangeSelect = document.getElementById('vehicleAnalysisTimeRange');
            if (timeRangeSelect?.value) {
                this.currentTimeRange = timeRangeSelect.value;
            }

            this.bindEvents();
            this.loadVehicleAnalysisData();
        }

        bindEvents() {
            // Time range filter
            const timeRangeSelect = document.getElementById('vehicleAnalysisTimeRange');
            if (timeRangeSelect) {
                timeRangeSelect.addEventListener('change', (e) => {
                    this.currentTimeRange = e.target.value;
                    this.loadVehicleAnalysisData();
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

        async loadVehicleAnalysisData() {
            try {
                const [vehicleStatsRes, vehicleTrendRes, vehicleBreakdownRes, bookingStatsRes, bookingTrendRes, ownershipRes] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getVehicleVerificationStats`),
                    fetch(`${this.URL_ROOT}/moderator/getVehicleVerificationTrend?timeRange=${this.currentTimeRange}`),
                    fetch(`${this.URL_ROOT}/moderator/getVehicleVerificationStatusBreakdown`),
                    fetch(`${this.URL_ROOT}/moderator/getFleetStats`),
                    fetch(`${this.URL_ROOT}/moderator/getVehicleBookingTrend?timeRange=${this.currentTimeRange}`),
                    fetch(`${this.URL_ROOT}/moderator/getDriverOwnershipStats`)
                ]);

                const [vehicleStats, vehicleTrend, vehicleBreakdown, fleetStats, bookingTrend, ownershipStats] = await Promise.all([
                    vehicleStatsRes.json(),
                    vehicleTrendRes.json(),
                    vehicleBreakdownRes.json(),
                    bookingStatsRes.json(),
                    bookingTrendRes.json(),
                    ownershipRes.json()
                ]);

                // Update stats cards
                if (vehicleStats.success) {
                    this.updateVehicleStats(vehicleStats.stats);
                }

                // Update overview charts
                if (vehicleBreakdown.success) {
                    this.updateVehicleStatusChart(vehicleBreakdown.breakdown);
                    this.updateComplianceChart(vehicleBreakdown.breakdown);
                }

                if (vehicleTrend.success) {
                    this.updateVehicleTrendChart(vehicleTrend.trend);
                    this.updateMonthlyActivityChart(vehicleTrend.trend);
                    this.updateComplianceTrendChart(vehicleTrend.trend);
                }

                // Update fleet analysis
                if (fleetStats.success) {
                    console.log('Fleet stats received:', fleetStats.stats);
                    this.updateFleetStats(fleetStats.stats);
                } else {
                    console.error('Fleet stats failed:', fleetStats);
                }

                if (bookingTrend.success) {
                    this.updateBookingTrendChart(bookingTrend.trend);
                    console.log('Vehicle types from fleet stats:', fleetStats.stats?.vehicleTypes);
                    this.updateVehicleTypeChart(fleetStats.stats?.vehicleTypes || {});
                }

                // Update driver ownership chart
                if (ownershipStats.success) {
                    console.log('Ownership stats received:', ownershipStats.ownership);
                    this.updateDriverOwnershipChart(ownershipStats.ownership);
                } else {
                    console.error('Ownership stats failed:', ownershipStats);
                }

            } catch (error) {
                console.error('Error loading vehicle analysis data:', error);
                window.showNotification?.('Failed to load vehicle analysis data', 'error');
            }
        }

        updateVehicleStats(stats) {
            this.setText('totalVehiclesCount', this.formatNumber(stats.totalVehicles || 0));
            this.setText('verifiedVehiclesCount', this.formatNumber(stats.verifiedVehicles || 0));
            this.setText('pendingVehiclesCount', this.formatNumber(stats.pendingVerifications || 0));
            this.setText('rejectedVehiclesCount', this.formatNumber(stats.rejectedVehicles || 0));
        }

        updateFleetStats(stats) {
            // Note: Reusing booking stats structure for fleet stats
            // In future, this should come from a dedicated fleet stats API
            this.setText('totalBookingsCount', this.formatNumber(stats.totalBookings || 0));
            this.setText('activeBookingsCount', this.formatNumber(stats.activeBookings || 0));
            this.setText('completedTripsCount', this.formatNumber(stats.completedTrips || 0));
            this.setText('avgTripDistance', `${this.formatNumber(stats.avgTripDistance || 0)} seats`);
        }

        updateVehicleStatusChart(breakdown) {
            const ctx = document.getElementById('vehicleStatusChart');
            if (!ctx) return;

            if (this.charts.vehicleStatus) {
                this.charts.vehicleStatus.destroy();
            }

            this.charts.vehicleStatus = new Chart(ctx, {
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

        updateVehicleTrendChart(trendData) {
            const ctx = document.getElementById('vehicleTrendChart');
            if (!ctx) return;

            if (this.charts.vehicleTrend) {
                this.charts.vehicleTrend.destroy();
            }

            this.charts.vehicleTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.labels || [],
                    datasets: [{
                        label: 'Approved',
                        data: trendData.approved || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Pending',
                        data: trendData.pending || [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Rejected',
                        data: trendData.rejected || [],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${this.formatNumber(context.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: { display: true, title: { display: true, text: 'Time Period' } },
                        y: { display: true, title: { display: true, text: 'Number of Verifications' }, beginAtZero: true }
                    },
                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                }
            });
        }

        updateMonthlyActivityChart(trendData) {
            const ctx = document.getElementById('monthlyActivityChart');
            if (!ctx) return;

            if (this.charts.monthlyActivity) {
                this.charts.monthlyActivity.destroy();
            }

            // Group by month and sum totals
            const monthlyData = this.groupByMonth(trendData);

            this.charts.monthlyActivity = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'Total Verifications',
                        data: monthlyData.totals,
                        backgroundColor: 'rgba(0, 106, 113, 0.8)',
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--primary') || '#006a71',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Total: ${this.formatNumber(context.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: { display: true, title: { display: true, text: 'Month' } },
                        y: { display: true, title: { display: true, text: 'Verifications' }, beginAtZero: true }
                    }
                }
            });
        }

        updateDriverOwnershipChart(ownership) {
            console.log('updateDriverOwnershipChart called with:', ownership);
            const ctx = document.getElementById('driverOwnershipChart');
            if (!ctx) {
                console.error('driverOwnershipChart canvas not found');
                return;
            }

            if (typeof Chart === 'undefined') {
                console.error('Chart.js library not loaded');
                return;
            }

            // Check if we have any data
            const hasData = (ownership['1 Vehicle'] || 0) + (ownership['2 Vehicles'] || 0) + (ownership['3+ Vehicles'] || 0) > 0;
            console.log('Driver ownership has data:', hasData);

            if (this.charts.driverOwnership) {
                this.charts.driverOwnership.destroy();
            }

            this.charts.driverOwnership = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['1 Vehicle', '2 Vehicles', '3+ Vehicles'],
                    datasets: [{
                        data: hasData ? [
                            ownership['1 Vehicle'] || 0,
                            ownership['2 Vehicles'] || 0,
                            ownership['3+ Vehicles'] || 0
                        ] : [45, 30, 25], // Mock data for testing
                        backgroundColor: ['#006a71', '#00a6b2', '#34d399'],
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
                                label: (context) => hasData ? `${context.label}: ${context.parsed}` : `${context.label}: ${context.parsed} (Mock Data)`
                            }
                        }
                    }
                }
            });
            console.log('Driver ownership chart created successfully');
        }

        updateVehicleTypeChart(vehicleTypes) {
            console.log('updateVehicleTypeChart called with:', vehicleTypes);
            const ctx = document.getElementById('vehicleTypeChart');
            if (!ctx) {
                console.error('vehicleTypeChart canvas not found');
                return;
            }

            if (typeof Chart === 'undefined') {
                console.error('Chart.js library not loaded');
                return;
            }

            // Check if we have any data
            const hasData = Object.keys(vehicleTypes || {}).length > 0 && Object.values(vehicleTypes || {}).some(v => v > 0);
            console.log('Vehicle model has data:', hasData);

            if (this.charts.vehicleType) {
                this.charts.vehicleType.destroy();
            }

            this.charts.vehicleType = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: hasData ? Object.keys(vehicleTypes || {}) : [],
                    datasets: [{
                        label: 'Vehicles by Model',
                        data: hasData ? Object.values(vehicleTypes || {}) : [],
                        backgroundColor: 'rgba(0, 106, 113, 0.8)',
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#006a71',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => hasData ? `${context.parsed.y} vehicles` : 'No data available'
                            }
                        }
                    },
                    scales: {
                        x: { display: true, title: { display: true, text: 'Vehicle Model' } },
                        y: { display: true, title: { display: true, text: 'Number of Vehicles' }, beginAtZero: true }
                    }
                }
            });
            console.log('Vehicle model chart created successfully');
        }

        updateBookingTrendChart(trendData) {
            const ctx = document.getElementById('bookingTrendChart');
            if (!ctx) return;

            if (this.charts.bookingTrend) {
                this.charts.bookingTrend.destroy();
            }

            this.charts.bookingTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.labels || [],
                    datasets: [{
                        label: 'Vehicle Bookings',
                        data: trendData.bookings || [],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Bookings: ${this.formatNumber(context.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: { display: true, title: { display: true, text: 'Time Period' } },
                        y: { display: true, title: { display: true, text: 'Number of Bookings' }, beginAtZero: true }
                    }
                }
            });
        }

        updateComplianceChart(breakdown) {
            const ctx = document.getElementById('complianceChart');
            if (!ctx) return;

            if (this.charts.compliance) {
                this.charts.compliance.destroy();
            }

            this.charts.compliance = new Chart(ctx, {
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

        updateComplianceTrendChart(trend) {
            const ctx = document.getElementById('verificationTrendChart');
            if (!ctx) return;

            if (this.charts.verificationTrend) {
                this.charts.verificationTrend.destroy();
            }

            this.charts.verificationTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trend.labels || [],
                    datasets: [{
                        label: 'Approved',
                        data: trend.approved || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Pending',
                        data: trend.pending || [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Rejected',
                        data: trend.rejected || [],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: { display: true, title: { display: true, text: 'Time Period' } },
                        y: { display: true, title: { display: true, text: 'Number of Verifications' }, beginAtZero: true }
                    }
                }
            });
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
                
                // Re-render charts in the newly visible section
                setTimeout(() => {
                    if (targetId === 'bookings-section' && this.charts.vehicleType) {
                        this.charts.vehicleType.resize();
                    }
                }, 100);
            }
        }

        // Utility methods
        setText(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text;
            }
        }

        formatNumber(value) {
            return Number(value || 0).toLocaleString();
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString();
        }

        capitalizeFirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        }

        groupByMonth(trendData) {
            const monthlyData = {};
            const labels = trendData.labels || [];
            const totals = trendData.totals || [];

            labels.forEach((label, index) => {
                const date = new Date(label);
                const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                if (!monthlyData[monthKey]) {
                    monthlyData[monthKey] = 0;
                }
                monthlyData[monthKey] += totals[index] || 0;
            });

            return {
                labels: Object.keys(monthlyData).sort(),
                totals: Object.values(monthlyData)
            };
        }
    }

    window.VehicleAnalysisManager = VehicleAnalysisManager;
    window.vehicleAnalysisManager = new VehicleAnalysisManager();
})();