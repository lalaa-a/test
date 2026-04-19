<!-- Earnings Breakdown Analytics -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Earnings Breakdown Analytics</h1>
            <p class="page-subtitle">Comprehensive analysis of site revenue, profits, and payment trends</p>
        </div>
        <div class="header-controls">
            <div class="time-range-selector">
                <select id="timeRangeSelect" class="filter-select">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="90days">Last 90 Days</option>
                    <option value="1year">Last Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
            <div class="view-selector">
                <select id="viewTypeSelect" class="filter-select">
                    <option value="daily">Daily View</option>
                    <option value="weekly">Weekly View</option>
                    <option value="monthly">Monthly View</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalRevenue">LKR 0.00</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-change" id="totalRevenueChange">
                <span class="change-indicator" id="totalRevenueIndicator">↗️</span>
                <span id="totalRevenuePercent">0.00%</span> from last period
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon profit">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="siteProfit">LKR 0.00</div>
            <div class="stat-label">Site Profit</div>
            <div class="stat-change" id="siteProfitChange">
                <span class="change-indicator" id="siteProfitIndicator">↗️</span>
                <span id="siteProfitPercent">0.00%</span> from last period
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon driver">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="driverRevenue">LKR 0.00</div>
            <div class="stat-label">Driver Revenue</div>
            <div class="stat-change" id="driverRevenueChange">
                <span class="change-indicator" id="driverRevenueIndicator">↗️</span>
                <span id="driverRevenuePercent">0.00%</span> from last period
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon guide">
            <i class="fas fa-user-friends"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="guideRevenue">LKR 0.00</div>
            <div class="stat-label">Guide Revenue</div>
            <div class="stat-change" id="guideRevenueChange">
                <span class="change-indicator" id="guideRevenueIndicator">↗️</span>
                <span id="guideRevenuePercent">0.00%</span> from last period
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <!-- Revenue Trend Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h3>Revenue Trend</h3>
            <div class="chart-controls">
                <select id="revenueChartType" class="chart-type-select">
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                    <option value="area">Area Chart</option>
                </select>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="revenueTrendChart" height="300"></canvas>
        </div>
    </div>

    <!-- Revenue Breakdown Pie Chart -->
    <div class="chart-container breakdown-chart">
        <div class="chart-header">
            <h3>Revenue Breakdown</h3>
        </div>
        <div class="chart-wrapper">
            <canvas id="revenueBreakdownChart" height="300"></canvas>
        </div>
    </div>

    <!-- Profit Margin Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h3>Profit Margin Trend</h3>
        </div>
        <div class="chart-wrapper">
            <canvas id="profitMarginChart" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Breakdown Table -->
<div class="breakdown-section">
    <div class="section-header">
        <h3>Detailed Earnings Breakdown</h3>
        <div class="table-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="earningsSearch" placeholder="Search by trip ID, driver, or guide..." class="search-input">
            </div>
            <select id="statusFilter" class="filter-select">
                <option value="all">All Status</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="earnings-table" id="earningsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Trip ID</th>
                    <th>Total Revenue</th>
                    <th>Driver Charge</th>
                    <th>Guide Charge</th>
                    <th>Site Charge</th>
                    <th>Site Profit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="earningsTableBody">
                <tr class="no-data">
                    <td colspan="9">
                        <i class="fas fa-chart-bar"></i>
                        <p>Loading earnings data...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">
            <span id="paginationInfo">Showing 0 to 0 of 0 entries</span>
        </div>
        <div class="pagination-controls">
            <button id="prevPage" class="pagination-btn" disabled>
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <div class="page-numbers" id="pageNumbers"></div>
            <button id="nextPage" class="pagination-btn" disabled>
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>Loading earnings data...</p>
    </div>
</div>
