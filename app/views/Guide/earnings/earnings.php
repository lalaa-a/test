<!-- Driver Earnings Page -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Earnings</h1>
            <p class="page-subtitle">Track your guiding payments, payouts, and refunds</p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="statTotalEarned">LKR 0.00</div>
            <div class="stat-label">Total Earned</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="statPendingAmount">LKR 0.00</div>
            <div class="stat-label">Pending (<span id="statPendingCount">0</span>)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon paid">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="statPaidAmount">LKR 0.00</div>
            <div class="stat-label">Paid (<span id="statPaidCount">0</span>)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon refunded">
            <i class="fas fa-undo-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="statRefundedAmount">LKR 0.00</div>
            <div class="stat-label">Refunded (<span id="statRefundedCount">0</span>)</div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-line"></i> Monthly Earnings</h3>
        </div>
        <div class="chart-body">
            <canvas id="monthlyEarningsChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-pie"></i> Payment Breakdown</h3>
        </div>
        <div class="chart-body chart-body--doughnut">
            <canvas id="paymentBreakdownChart"></canvas>
            <div class="doughnut-legend" id="doughnutLegend"></div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav-wrapper">
    <div class="section-nav">
        <a href="#" class="nav-link active" data-section="pending">
            <i class="fas fa-clock"></i> Pending
            <span class="nav-badge pending-badge" id="navPendingCount">0</span>
        </a>
        <a href="#" class="nav-link" data-section="paid">
            <i class="fas fa-check-circle"></i> Paid
            <span class="nav-badge paid-badge" id="navPaidCount">0</span>
        </a>
        <a href="#" class="nav-link" data-section="refunded">
            <i class="fas fa-undo-alt"></i> Refunded
            <span class="nav-badge refunded-badge" id="navRefundedCount">0</span>
        </a>
    </div>
</div>

<!-- Earnings Sections -->
<div class="earnings-sections">

    <!-- Pending Section -->
    <div class="earnings-section" id="pending-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2><i class="fas fa-clock"></i> Pending Payments</h2>
                <div class="section-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" id="pendingSearchInput" placeholder="Search trip ID...">
                    </div>
                </div>
            </div>
        </div>
        <div class="earnings-table-container">
            <table class="earnings-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trip ID</th>
                        <th>Guide Charge</th>
                        <th>Total Trip Charge</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="pendingTableBody">
                    <tr class="no-data-row">
                        <td colspan="6">
                            <i class="fas fa-inbox"></i>
                            <p>No pending payments</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paid Section -->
    <div class="earnings-section" id="paid-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2><i class="fas fa-check-circle"></i> Paid Earnings</h2>
                <div class="section-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" id="paidSearchInput" placeholder="Search trip ID...">
                    </div>
                    <div class="filter-dropdown">
                        <select class="filter-select" id="paidSiteFilter">
                            <option value="all">All Payouts</option>
                            <option value="1">Received</option>
                            <option value="0">Awaiting Payout</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="earnings-table-container">
            <table class="earnings-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trip ID</th>
                        <th>Guide Charge</th>
                        <th>Paid Date</th>
                        <th>Payout Date</th>
                        <th>Payout Status</th>
                    </tr>
                </thead>
                <tbody id="paidTableBody">
                    <tr class="no-data-row">
                        <td colspan="6">
                            <i class="fas fa-check-circle"></i>
                            <p>No paid earnings yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Refunded Section -->
    <div class="earnings-section" id="refunded-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2><i class="fas fa-undo-alt"></i> Refunded Trips</h2>
                <div class="section-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" id="refundedSearchInput" placeholder="Search trip ID or reason...">
                    </div>
                </div>
            </div>
        </div>
        <div class="earnings-table-container">
            <table class="earnings-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trip ID</th>
                        <th>Guide Charge</th>
                        <th>Refund Amount</th>
                        <th>Refund Date</th>
                        <th>Reason</th>
                        <th>Payout Status</th>
                    </tr>
                </thead>
                <tbody id="refundedTableBody">
                    <tr class="no-data-row">
                        <td colspan="7">
                            <i class="fas fa-undo-alt"></i>
                            <p>No refunded trips</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
