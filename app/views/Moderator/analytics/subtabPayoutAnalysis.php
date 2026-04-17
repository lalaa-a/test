<!-- Payout Analysis -->

<div class="page-header">
	<div class="page-header-content">
		<div class="page-title-section">
			<h1 class="page-title">Payout Analysis</h1>
			<p class="page-subtitle">Track payments to drivers and guides over time</p>
		</div>
		<div class="header-controls">
			<select id="payoutTimeRange" class="filter-select">
				<option value="7days">Last 7 Days</option>
				<option value="30days" selected>Last 30 Days</option>
				<option value="90days">Last 90 Days</option>
				<option value="1year">Last Year</option>
			</select>
			<select id="payoutViewType" class="filter-select">
				<option value="daily" selected>Daily</option>
				<option value="weekly">Weekly</option>
				<option value="monthly">Monthly</option>
			</select>
		</div>
	</div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
	<a href="#overview-section" class="nav-link active" data-section="overview">
		<i class="fas fa-chart-line"></i>
		Overview
	</a>
	<a href="#user-explorer-section" class="nav-link" data-section="user-explorer">
		<i class="fas fa-user-chart"></i>
		User Explorer
	</a>
</div>

<!-- Payout Analysis Sections -->
<div class="payout-sections">
	<!-- Overview Section -->
	<div class="payout-section" id="overview-section">
		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-icon total">
					<i class="fas fa-money-check-alt"></i>
				</div>
				<div class="stat-content">
					<div class="stat-number" id="totalPaidValue">LKR 0.00</div>
					<div class="stat-label">Total Paid Out</div>
				</div>
			</div>
			<div class="stat-card">
				<div class="stat-icon driver">
					<i class="fas fa-car"></i>
				</div>
				<div class="stat-content">
					<div class="stat-number" id="driverPaidValue">LKR 0.00</div>
					<div class="stat-label">Driver Payouts</div>
				</div>
			</div>
			<div class="stat-card">
				<div class="stat-icon guide">
					<i class="fas fa-map-signs"></i>
				</div>
				<div class="stat-content">
					<div class="stat-number" id="guidePaidValue">LKR 0.00</div>
					<div class="stat-label">Guide Payouts</div>
				</div>
			</div>
			<div class="stat-card">
				<div class="stat-icon pending">
					<i class="fas fa-hourglass-half"></i>
				</div>
				<div class="stat-content">
					<div class="stat-number" id="pendingPayoutCount">0</div>
					<div class="stat-label">Pending Payout Records</div>
				</div>
			</div>
		</div>

		<div class="charts-grid">
			<div class="chart-card wide">
				<div class="chart-header">
					<h3>Payout Trend</h3>
					<p>Payments completed by the site to drivers and guides</p>
				</div>
				<div class="chart-body">
					<canvas id="payoutTrendChart"></canvas>
				</div>
			</div>
			<div class="chart-card">
				<div class="chart-header">
					<h3>Payout Distribution</h3>
					<p>Driver vs Guide vs Refunded amounts</p>
				</div>
				<div class="chart-body">
					<canvas id="payoutDistributionChart"></canvas>
				</div>
			</div>
		</div>
	</div>

	<!-- User Explorer Section -->
	<div class="payout-section" id="user-explorer-section">
		<!-- Jump to Individual Analysis Button -->
		<div class="jump-to-section">
			<button class="jump-btn" onclick="document.getElementById('user-analysis-view').scrollIntoView({behavior: 'smooth'});">
				<i class="fas fa-arrow-down"></i>
				Jump to Individual User Analysis
			</button>
		</div>

		<!-- Top Earners Overview -->
		<div class="subsection-content" id="top-earners-view">
			<div class="chart-card top-earners-card">
				<div class="chart-header">
					<h2>Top Earners Overview</h2>
					<p>Top 5 drivers and guides by total payout amount</p>
				</div>
				<div class="chart-body">
					<canvas id="topEarnersChart"></canvas>
				</div>
				<div class="chart-legend" id="topEarnersLegend"></div>
			</div>
		</div>

		<!-- Individual User Analysis -->
		<div class="subsection-content" id="user-analysis-view">
			<div class="user-analysis-card">
				<div class="section-header">
					<div>
						<h2>Individual User Analysis</h2>
						<p>Search for a specific driver or guide to view their payout details</p>
					</div>
					<div class="user-search-controls">
						<input type="number" id="payoutUserIdInput" class="search-input" placeholder="Enter user ID (driver or guide)" min="1">
						<button id="loadUserPayoutBtn" class="action-btn">
							<i class="fas fa-search"></i>
							Analyze
						</button>
					</div>
				</div>

			<div id="userPayoutEmptyState" class="empty-state">
				<i class="fas fa-user-chart"></i>
				<p>Enter a user ID above to load their payout profile and earning history</p>
			</div>

			<div id="userPayoutContent" class="user-content" style="display: none;">
				<!-- User Profile Summary -->
				<div class="user-profile-card" id="userProfileCard"></div>

				<!-- User Statistics -->
				<div class="user-summary-grid">
					<div class="mini-stat">
						<span class="mini-label">Total Paid</span>
						<span class="mini-value" id="userTotalPaid">LKR 0.00</span>
					</div>
					<div class="mini-stat">
						<span class="mini-label">Completed Payouts</span>
						<span class="mini-value" id="userCompletedCount">0</span>
					</div>
					<div class="mini-stat">
						<span class="mini-label">Pending Records</span>
						<span class="mini-value" id="userPendingCount">0</span>
					</div>
					<div class="mini-stat">
						<span class="mini-label">Refunded Amount</span>
						<span class="mini-value" id="userRefundedAmount">LKR 0.00</span>
					</div>
				</div>

				<!-- User Earnings Chart -->
				<div class="chart-card user-chart">
					<div class="chart-header">
						<h3>Earnings Trend</h3>
						<p id="userTrendLabel">Payout earnings over the selected time range</p>
					</div>
					<div class="chart-body">
						<canvas id="userPayoutTrendChart"></canvas>
					</div>
				</div>

				<!-- Recent Payout Records -->
				<div class="table-card">
					<div class="table-header">
						<h3>Recent Payout Records</h3>
					</div>
					<div class="table-wrapper">
						<table class="records-table">
							<thead>
								<tr>
									<th>Payout ID</th>
									<th>Trip ID</th>
									<th>Status</th>
									<th>Amount</th>
									<th>Date</th>
									<th>Transaction</th>
								</tr>
							</thead>
							<tbody id="userPayoutTableBody">
								<tr>
									<td colspan="6" class="table-empty">No payout records found</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
