// Earnings Breakdown Analytics JavaScript
(function() {
    // Earnings Breakdown Manager
    if (window.EarningsBreakdownManager) {
        console.log('EarningsBreakdownManager already exists, cleaning up...');
        if (window.earningsBreakdownManager) {
            delete window.earningsBreakdownManager;
        }
        delete window.EarningsBreakdownManager;
    }

    class EarningsBreakdownManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentTimeRange = '30days';
            this.currentViewType = 'daily';
            this.currentPage = 1;
            this.itemsPerPage = 25;
            this.totalItems = 0;

            // Chart instances
            this.revenueTrendChart = null;
            this.revenueBreakdownChart = null;
            this.profitMarginChart = null;

            this.init();
        }

        init() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.bindEvents();
                    this.loadData();
                });
            } else {
                this.bindEvents();
                this.loadData();
            }
        }

        bindEvents() {
            // Time range selector
            const timeRangeSelect = document.getElementById('timeRangeSelect');
            if (timeRangeSelect) {
                timeRangeSelect.addEventListener('change', (e) => {
                    this.currentTimeRange = e.target.value;
                    this.loadData();
                });
            }

            // View type selector
            const viewTypeSelect = document.getElementById('viewTypeSelect');
            if (viewTypeSelect) {
                viewTypeSelect.addEventListener('change', (e) => {
                    this.currentViewType = e.target.value;
                    this.loadData();
                });
            }

            // Revenue chart type selector
            const revenueChartType = document.getElementById('revenueChartType');
            if (revenueChartType) {
                revenueChartType.addEventListener('change', (e) => {
                    this.changeRevenueChartType(e.target.value);
                });
            }

            // Search functionality
            const earningsSearch = document.getElementById('earningsSearch');
            if (earningsSearch) {
                earningsSearch.addEventListener('input', (e) => {
                    this.filterTable(e.target.value);
                });
            }

            // Status filter
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', (e) => {
                    this.filterByStatus(statusFilter.value);
                });
            }

            // Pagination
            const prevPage = document.getElementById('prevPage');
            const nextPage = document.getElementById('nextPage');

            if (prevPage) {
                prevPage.addEventListener('click', () => {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.loadEarningsTable();
                    }
                });
            }

            if (nextPage) {
                nextPage.addEventListener('click', () => {
                    const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                    if (this.currentPage < totalPages) {
                        this.currentPage++;
                        this.loadEarningsTable();
                    }
                });
            }
        }

        async loadData() {
            this.showLoading();

            try {
                // Load all data in parallel
                const [metricsResponse, trendResponse, breakdownResponse, marginResponse] = await Promise.all([
                    this.fetchMetrics(),
                    this.fetchRevenueTrend(),
                    this.fetchRevenueBreakdown(),
                    this.fetchProfitMarginTrend()
                ]);

                // Update UI
                this.updateMetrics(metricsResponse);
                this.updateRevenueTrend(trendResponse);
                this.updateRevenueBreakdown(breakdownResponse);
                this.updateProfitMarginTrend(marginResponse);
                this.loadEarningsTable();

            } catch (error) {
                console.error('Error loading earnings data:', error);
                this.showError('Failed to load earnings data. Please try again.');
            } finally {
                this.hideLoading();
            }
        }

        async fetchMetrics() {
            const response = await fetch(`${this.URL_ROOT}/moderator/getEarningsMetrics?timeRange=${this.currentTimeRange}&viewType=${this.currentViewType}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch metrics');
            }

            return data.metrics;
        }

        async fetchRevenueTrend() {
            const response = await fetch(`${this.URL_ROOT}/moderator/getRevenueTrend?timeRange=${this.currentTimeRange}&viewType=${this.currentViewType}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch revenue trend');
            }

            return data.trend;
        }

        async fetchRevenueBreakdown() {
            const response = await fetch(`${this.URL_ROOT}/moderator/getRevenueBreakdown?timeRange=${this.currentTimeRange}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch revenue breakdown');
            }

            return data.breakdown;
        }

        async fetchProfitMarginTrend() {
            const response = await fetch(`${this.URL_ROOT}/moderator/getProfitMarginTrend?timeRange=${this.currentTimeRange}&viewType=${this.currentViewType}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch profit margin trend');
            }

            return data.trend;
        }

        updateMetrics(metrics) {
            // Update metric cards
            this.updateMetricCard('totalRevenue', metrics.totalRevenue, metrics.totalRevenueChange);
            this.updateMetricCard('siteProfit', metrics.siteProfit, metrics.siteProfitChange);
            this.updateMetricCard('driverRevenue', metrics.driverRevenue, metrics.driverRevenueChange);
            this.updateMetricCard('guideRevenue', metrics.guideRevenue, metrics.guideRevenueChange);
        }

        updateMetricCard(cardId, value, change) {
            const valueElement = document.getElementById(cardId);
            const changeElement = document.getElementById(`${cardId}Change`);
            const indicatorElement = document.getElementById(`${cardId}Indicator`);
            const percentElement = document.getElementById(`${cardId}Percent`);

            if (valueElement) {
                valueElement.textContent = `LKR ${this.formatCurrency(value)}`;
            }

            if (changeElement && change !== null) {
                const isPositive = change >= 0;
                indicatorElement.textContent = isPositive ? '↗️' : '↘️';
                percentElement.textContent = `${Math.abs(change).toFixed(2)}%`;
                changeElement.style.color = isPositive ? '#10b981' : '#ef4444';
            }
        }

        updateRevenueTrend(trendData) {
            if (!this.revenueTrendChart) {
                this.initRevenueTrendChart();
            }

            this.revenueTrendChart.data.labels = trendData.labels;
            this.revenueTrendChart.data.datasets[0].data = trendData.totalRevenue;
            this.revenueTrendChart.data.datasets[1].data = trendData.siteProfit;
            this.revenueTrendChart.data.datasets[2].data = trendData.driverRevenue;
            this.revenueTrendChart.data.datasets[3].data = trendData.guideRevenue;

            this.revenueTrendChart.update();
        }

        updateRevenueBreakdown(breakdownData) {
            if (!this.revenueBreakdownChart) {
                this.initRevenueBreakdownChart();
            }

            this.revenueBreakdownChart.data.datasets[0].data = [
                breakdownData.siteProfit,
                breakdownData.driverRevenue,
                breakdownData.guideRevenue
            ];

            this.revenueBreakdownChart.update();
        }

        updateProfitMarginTrend(marginData) {
            if (!this.profitMarginChart) {
                this.initProfitMarginChart();
            }

            this.profitMarginChart.data.labels = marginData.labels;
            this.profitMarginChart.data.datasets[0].data = marginData.profitMargins;

            this.profitMarginChart.update();
        }

        initRevenueTrendChart() {
            const ctx = document.getElementById('revenueTrendChart');
            if (!ctx) return;

            this.revenueTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Revenue',
                        data: [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Site Profit',
                        data: [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Driver Revenue',
                        data: [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Guide Revenue',
                        data: [],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Revenue Trend Over Time',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': LKR ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: this.getXAxisLabel()
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Amount (LKR)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        initRevenueBreakdownChart() {
            const ctx = document.getElementById('revenueBreakdownChart');
            if (!ctx) return;

            this.revenueBreakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Site Profit', 'Driver Revenue', 'Guide Revenue'],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#f59e0b',
                            '#3b82f6',
                            '#8b5cf6'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Revenue Distribution',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': LKR ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        initProfitMarginChart() {
            const ctx = document.getElementById('profitMarginChart');
            if (!ctx) return;

            this.profitMarginChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Profit Margin (%)',
                        data: [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Profit Margin Trend',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Profit Margin: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: this.getXAxisLabel()
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Profit Margin (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        changeRevenueChartType(chartType) {
            if (!this.revenueTrendChart) return;

            // Handle area chart (use line with fill)
            const actualType = chartType === 'area' ? 'line' : chartType;

            // Update fill property
            this.revenueTrendChart.data.datasets.forEach(dataset => {
                if (chartType === 'area') {
                    dataset.fill = true;
                    dataset.backgroundColor = dataset.borderColor.replace('rgb', 'rgba').replace(')', ', 0.1)');
                } else {
                    dataset.fill = false;
                    dataset.backgroundColor = dataset.borderColor;
                }
            });

            this.revenueTrendChart.config.type = actualType;
            this.revenueTrendChart.update();
        }

        async loadEarningsTable() {
            try {
                const statusFilter = document.getElementById('statusFilter')?.value || 'all';
                const response = await fetch(`${this.URL_ROOT}/moderator/getEarningsTable?page=${this.currentPage}&limit=${this.itemsPerPage}&timeRange=${this.currentTimeRange}&status=${statusFilter}`);
                const data = await response.json();

                if (data.success) {
                    this.totalItems = data.total;
                    this.renderEarningsTable(data.earnings);
                    this.updatePagination();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading earnings table:', error);
                this.showTableError('Failed to load earnings table');
            }
        }

        renderEarningsTable(earnings) {
            const tableBody = document.getElementById('earningsTableBody');

            if (!earnings || earnings.length === 0) {
                tableBody.innerHTML = `
                    <tr class="no-data">
                        <td colspan="9">
                            <i class="fas fa-chart-bar"></i>
                            <p>No earnings data found for the selected period</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = earnings.map(earning => `
                <tr>
                    <td>${this.formatDate(earning.createdAt)}</td>
                    <td>${earning.tripId}</td>
                    <td>LKR ${this.formatCurrency(earning.totalRevenue)}</td>
                    <td>LKR ${this.formatCurrency(earning.driverCharge)}</td>
                    <td>LKR ${this.formatCurrency(earning.guideCharge)}</td>
                    <td>LKR ${this.formatCurrency(earning.siteCharge)}</td>
                    <td>LKR ${this.formatCurrency(earning.siteProfit)}</td>
                    <td>
                        <span class="status-badge ${earning.status.toLowerCase()}">
                            ${earning.status}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn view" onclick="earningsBreakdownManager.viewEarningDetails('${earning.tripId}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        updatePagination() {
            const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
            const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
            const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);

            // Update pagination info
            const paginationInfo = document.getElementById('paginationInfo');
            if (paginationInfo) {
                paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${this.totalItems} entries`;
            }

            // Update pagination buttons
            const prevPage = document.getElementById('prevPage');
            const nextPage = document.getElementById('nextPage');

            if (prevPage) {
                prevPage.disabled = this.currentPage === 1;
            }

            if (nextPage) {
                nextPage.disabled = this.currentPage === totalPages;
            }

            // Update page numbers
            this.renderPageNumbers(totalPages);
        }

        renderPageNumbers(totalPages) {
            const pageNumbers = document.getElementById('pageNumbers');
            if (!pageNumbers) return;

            let html = '';

            // Show max 5 page numbers
            let startPage = Math.max(1, this.currentPage - 2);
            let endPage = Math.min(totalPages, this.currentPage + 2);

            // Adjust if we're near the beginning or end
            if (endPage - startPage < 4) {
                if (startPage === 1) {
                    endPage = Math.min(totalPages, startPage + 4);
                } else if (endPage === totalPages) {
                    startPage = Math.max(1, endPage - 4);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="page-number ${i === this.currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
            }

            pageNumbers.innerHTML = html;

            // Add event listeners
            pageNumbers.querySelectorAll('.page-number').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const page = parseInt(e.target.dataset.page);
                    if (page !== this.currentPage) {
                        this.currentPage = page;
                        this.loadEarningsTable();
                    }
                });
            });
        }

        filterTable(searchTerm) {
            // For now, implement client-side search
            // TODO: Implement server-side search for better performance
            const rows = document.querySelectorAll('#earningsTableBody tr:not(.no-data)');

            if (!searchTerm.trim()) {
                // Show all rows if search is empty
                rows.forEach(row => row.style.display = '');
                return;
            }

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matches = text.includes(searchTerm.toLowerCase());
                row.style.display = matches ? '' : 'none';
            });
        }

        filterByStatus(status) {
            // Reload table with new status filter
            this.loadEarningsTable();
        }

        viewEarningDetails(tripId) {
            // Open modal or navigate to detailed view
            console.log('Viewing details for trip:', tripId);
            // For now, just show an alert. In a real implementation, this would open a modal or navigate to a detail page
            alert(`Viewing details for Trip ID: ${tripId}`);
        }

        getXAxisLabel() {
            switch (this.currentViewType) {
                case 'daily':
                    return 'Date';
                case 'weekly':
                    return 'Week';
                case 'monthly':
                    return 'Month';
                default:
                    return 'Date';
            }
        }

        formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }

        hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        showError(message) {
            // Use existing notification system
            if (window.showNotification) {
                window.showNotification(message, 'error');
            } else {
                alert(message);
            }
        }

        showTableError(message) {
            const tableBody = document.getElementById('earningsTableBody');
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr class="no-data">
                        <td colspan="9">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>${message}</p>
                        </td>
                    </tr>
                `;
            }
        }
    }

    // Initialize the manager
    window.EarningsBreakdownManager = EarningsBreakdownManager;
    window.earningsBreakdownManager = new EarningsBreakdownManager();

})();
