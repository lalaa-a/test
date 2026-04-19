<?php
	$adminDashboardUser = getLoggedInUser();
	$adminDashboardName = trim((string)($adminDashboardUser['fullname'] ?? 'Admin'));
	$adminDashboardFirstName = $adminDashboardName !== '' ? preg_split('/\s+/', $adminDashboardName)[0] : 'Admin';
?>

<section id="adminDashboardPage" class="admdash-page" data-url-root="<?php echo htmlspecialchars(URL_ROOT, ENT_QUOTES, 'UTF-8'); ?>">
	<header class="admdash-hero-card">
		<div class="admdash-hero-copy">
			<p class="admdash-hero-kicker">Admin Control Center</p>
			<h1 class="admdash-hero-title">Welcome back, <?php echo htmlspecialchars($adminDashboardFirstName, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="admdash-hero-subtitle">Track verification workload, support queues, payouts, and platform growth from one dashboard.</p>

			<div class="admdash-hero-actions">
				<a class="admdash-primary-btn" href="<?php echo URL_ROOT; ?>/admin/verification">
					<i class="fas fa-circle-check"></i>
					Verification
				</a>
				<a class="admdash-ghost-btn" href="<?php echo URL_ROOT; ?>/admin/oversight">
					<i class="fas fa-eye"></i>
					Oversight
				</a>
				<a class="admdash-ghost-btn" href="<?php echo URL_ROOT; ?>/admin/transaction">
					<i class="fas fa-money-bill-transfer"></i>
					Transaction
				</a>
				<a class="admdash-ghost-btn" href="<?php echo URL_ROOT; ?>/admin/analytics">
					<i class="fas fa-chart-line"></i>
					Analytics
				</a>
				<button type="button" id="admDashRefreshBtn" class="admdash-refresh-btn" title="Refresh dashboard">
					<i class="fas fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="admdash-hero-aside">
			<div class="admdash-hero-chip">
				<span class="label">Today</span>
				<span class="value" id="admDashTodayDate">-</span>
			</div>
			<div class="admdash-hero-chip">
				<span class="label">Work Queue</span>
				<span class="value" id="admDashHeroQueue">0</span>
			</div>
			<div class="admdash-hero-chip">
				<span class="label">Unread Messages</span>
				<span class="value" id="admDashHeroUnread">0</span>
			</div>
		</div>
	</header>

	<div class="admdash-stats-grid">
		<div class="admdash-stat-card">
			<div class="admdash-stat-icon account"><i class="fas fa-user-clock"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashPendingAccounts">0</div>
				<div class="admdash-stat-label">Pending Accounts</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon license"><i class="fas fa-id-card"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashPendingLicenses">0</div>
				<div class="admdash-stat-label">Pending Licenses</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon vehicle"><i class="fas fa-car-side"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashPendingVehicles">0</div>
				<div class="admdash-stat-label">Pending Vehicles</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon complaint"><i class="fas fa-triangle-exclamation"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashOpenComplaints">0</div>
				<div class="admdash-stat-label">Open Complaints</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon payout"><i class="fas fa-hand-holding-dollar"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashPendingPayouts">0</div>
				<div class="admdash-stat-label">Pending Payouts</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon trip"><i class="fas fa-suitcase-rolling"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashActiveTrips">0</div>
				<div class="admdash-stat-label">Active Trips</div>
			</div>
		</div>

		<div class="admdash-stat-card">
			<div class="admdash-stat-icon profit"><i class="fas fa-sack-dollar"></i></div>
			<div class="admdash-stat-content">
				<div class="admdash-stat-number" id="admDashSiteProfit">LKR 0.00</div>
				<div class="admdash-stat-label">30 Day Site Profit</div>
			</div>
		</div>
	</div>

	<div class="admdash-grid">
		<article class="admdash-panel admdash-panel-wide">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-layer-group"></i> Queue Breakdown</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/verification">Open verification</a>
			</div>
			<div class="admdash-breakdown-list">
				<div class="admdash-breakdown-row">
					<div class="meta"><span>Account verifications</span><strong id="admDashQueueAccounts">0</strong></div>
					<div class="bar"><span id="admDashQueueAccountsBar"></span></div>
				</div>
				<div class="admdash-breakdown-row">
					<div class="meta"><span>Tourist license checks</span><strong id="admDashQueueLicenses">0</strong></div>
					<div class="bar"><span id="admDashQueueLicensesBar"></span></div>
				</div>
				<div class="admdash-breakdown-row">
					<div class="meta"><span>Vehicle checks</span><strong id="admDashQueueVehicles">0</strong></div>
					<div class="bar"><span id="admDashQueueVehiclesBar"></span></div>
				</div>
				<div class="admdash-breakdown-row">
					<div class="meta"><span>Open complaints</span><strong id="admDashQueueComplaints">0</strong></div>
					<div class="bar"><span id="admDashQueueComplaintsBar"></span></div>
				</div>
				<div class="admdash-breakdown-row">
					<div class="meta"><span>Pending payouts</span><strong id="admDashQueuePayouts">0</strong></div>
					<div class="bar"><span id="admDashQueuePayoutsBar"></span></div>
				</div>
			</div>
		</article>

		<article class="admdash-panel admdash-panel-wide">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-wallet"></i> Finance Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/transaction">Open transaction</a>
			</div>
			<div class="admdash-metrics-grid">
				<div class="admdash-metric-card">
					<span class="label">Completed Traveler Payments</span>
					<strong id="admDashCompletedPaymentsCount">0</strong>
					<span class="meta">Total revenue: <b id="admDashRevenueAmount">LKR 0.00</b></span>
				</div>
				<div class="admdash-metric-card">
					<span class="label">Cancelled and Refunded</span>
					<strong><span id="admDashCancelledPaymentsCount">0</span> + <span id="admDashRefundedPaymentsCount">0</span></strong>
					<span class="meta">Cancelled + refunded payment count</span>
				</div>
				<div class="admdash-metric-card">
					<span class="label">Pending Payout Queue</span>
					<strong id="admDashPendingPayoutCount">0</strong>
					<span class="meta">Amount in queue: <b id="admDashPendingPayoutAmount">LKR 0.00</b></span>
				</div>
				<div class="admdash-metric-card">
					<span class="label">Completed Payouts</span>
					<strong id="admDashPaidPayoutCount">0</strong>
					<span class="meta">Paid amount: <b id="admDashPaidPayoutAmount">LKR 0.00</b></span>
				</div>
				<div class="admdash-metric-card">
					<span class="label">Driver Revenue (30 Days)</span>
					<strong id="admDashDriverRevenueAmount">LKR 0.00</strong>
					<span class="meta">Completed trips only</span>
				</div>
				<div class="admdash-metric-card">
					<span class="label">Guide Revenue (30 Days)</span>
					<strong id="admDashGuideRevenueAmount">LKR 0.00</strong>
					<span class="meta">Completed trips only</span>
				</div>
			</div>
		</article>

		<article class="admdash-panel admdash-chart-panel">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-chart-column"></i> Queue Mix</h2>
			</div>
			<div class="admdash-chart-wrap">
				<canvas id="admDashQueueChart"></canvas>
				<p id="admDashQueueChartEmpty" class="admdash-chart-empty" style="display: none;">Not enough queue data for chart.</p>
			</div>
		</article>

		<article class="admdash-panel admdash-chart-panel">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-chart-line"></i> Revenue Trend (30 Days)</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/analytics">Open analytics</a>
			</div>
			<div class="admdash-chart-wrap">
				<canvas id="admDashRevenueTrendChart"></canvas>
				<p id="admDashRevenueTrendEmpty" class="admdash-chart-empty" style="display: none;">No revenue trend data available yet.</p>
			</div>
		</article>

		<article class="admdash-panel admdash-chart-panel admdash-chart-panel-wide">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-user-plus"></i> Registration Trend (30 Days)</h2>
			</div>
			<div class="admdash-chart-wrap">
				<canvas id="admDashRegistrationTrendChart"></canvas>
				<p id="admDashRegistrationTrendEmpty" class="admdash-chart-empty" style="display: none;">No registration trend data available yet.</p>
			</div>
		</article>

		<article class="admdash-panel admdash-panel-list">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-bullhorn"></i> Recent Complaints</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/oversight">Open oversight</a>
			</div>
			<div id="admDashComplaintList" class="admdash-list-wrap"></div>
			<p id="admDashComplaintEmpty" class="admdash-empty-text" style="display: none;">No active complaints right now.</p>
		</article>

		<article class="admdash-panel admdash-panel-list">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-shield-halved"></i> Pending Verifications</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/verification">Review queue</a>
			</div>
			<div id="admDashVerificationList" class="admdash-list-wrap"></div>
			<p id="admDashVerificationEmpty" class="admdash-empty-text" style="display: none;">No pending verification items.</p>
		</article>

		<article class="admdash-panel admdash-panel-list">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-money-check-dollar"></i> Pending Payout Queue</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/transaction">Process payouts</a>
			</div>
			<div id="admDashPayoutList" class="admdash-list-wrap"></div>
			<p id="admDashPayoutEmpty" class="admdash-empty-text" style="display: none;">No pending payout requests.</p>
		</article>

		<article class="admdash-panel admdash-panel-list">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-suitcase-rolling"></i> Active Trip Watchlist</h2>
				<a href="<?php echo URL_ROOT; ?>/admin/tripInfo">Open trip info</a>
			</div>
			<div id="admDashTripList" class="admdash-list-wrap"></div>
			<p id="admDashTripEmpty" class="admdash-empty-text" style="display: none;">No active trips at the moment.</p>
		</article>

		<article class="admdash-panel admdash-actions-panel">
			<div class="admdash-panel-header">
				<h2><i class="fas fa-bolt"></i> Quick Actions</h2>
			</div>
			<div class="admdash-actions-grid">
				<a href="<?php echo URL_ROOT; ?>/admin/moderator" class="admdash-action-link"><i class="fas fa-user-tie"></i><span>Moderator Accounts</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/userInfo" class="admdash-action-link"><i class="fas fa-users"></i><span>User Directory</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/verification" class="admdash-action-link"><i class="fas fa-circle-check"></i><span>Review Verification</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/oversight" class="admdash-action-link"><i class="fas fa-headset"></i><span>Handle Complaints</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/transaction" class="admdash-action-link"><i class="fas fa-hand-holding-dollar"></i><span>Process Payouts</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/tripInfo" class="admdash-action-link"><i class="fas fa-suitcase-rolling"></i><span>Trip Monitoring</span></a>
				<a href="<?php echo URL_ROOT; ?>/admin/analytics" class="admdash-action-link"><i class="fas fa-chart-line"></i><span>View Analytics</span></a>
			</div>
		</article>
	</div>
</section>
