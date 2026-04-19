<?php
	$moderatorDashboardUser = getLoggedInUser();
	$moderatorDashboardName = trim((string)($moderatorDashboardUser['fullname'] ?? 'Moderator'));
	$moderatorDashboardFirstName = $moderatorDashboardName !== '' ? preg_split('/\s+/', $moderatorDashboardName)[0] : 'Moderator';
?>

<section id="moderatorDashboardPage" class="moderator-dashboard-page" data-url-root="<?php echo htmlspecialchars(URL_ROOT, ENT_QUOTES, 'UTF-8'); ?>">
	<header class="moddash-hero-card">
		<div class="moddash-hero-copy">
			<p class="moddash-hero-kicker">Moderator Overview</p>
			<h1 class="moddash-hero-title">Welcome back, <?php echo htmlspecialchars($moderatorDashboardFirstName, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="moddash-hero-subtitle">Stay ahead of verification queues, disputes, payouts, and platform revenue from one control center.</p>

			<div class="moddash-hero-actions">
				<a class="moddash-primary-btn" href="<?php echo URL_ROOT; ?>/moderator/verification">
					<i class="fas fa-circle-check"></i>
					Verification
				</a>
				<a class="moddash-ghost-btn" href="<?php echo URL_ROOT; ?>/moderator/oversight">
					<i class="fas fa-eye"></i>
					Oversight
				</a>
				<a class="moddash-ghost-btn" href="<?php echo URL_ROOT; ?>/moderator/transaction">
					<i class="fas fa-money-bill-transfer"></i>
					Transaction
				</a>
				<a class="moddash-ghost-btn" href="<?php echo URL_ROOT; ?>/moderator/analytics">
					<i class="fas fa-chart-line"></i>
					Analytics
				</a>
				<button type="button" id="modDashRefreshBtn" class="moddash-refresh-btn" title="Refresh dashboard">
					<i class="fas fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="moddash-hero-aside">
			<div class="moddash-hero-chip">
				<span class="label">Today</span>
				<span class="value" id="modDashTodayDate">-</span>
			</div>
			<div class="moddash-hero-chip">
				<span class="label">Work Queue</span>
				<span class="value" id="modDashHeroQueue">0</span>
			</div>
			<div class="moddash-hero-chip">
				<span class="label">Unread Messages</span>
				<span class="value" id="modDashHeroUnread">0</span>
			</div>
		</div>
	</header>

	<div class="moddash-stats-grid">
		<div class="moddash-stat-card">
			<div class="moddash-stat-icon account"><i class="fas fa-user-clock"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashPendingAccounts">0</div>
				<div class="moddash-stat-label">Pending Accounts</div>
			</div>
		</div>

		<div class="moddash-stat-card">
			<div class="moddash-stat-icon license"><i class="fas fa-id-card"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashPendingLicenses">0</div>
				<div class="moddash-stat-label">Pending Licenses</div>
			</div>
		</div>

		<div class="moddash-stat-card">
			<div class="moddash-stat-icon vehicle"><i class="fas fa-car-side"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashPendingVehicles">0</div>
				<div class="moddash-stat-label">Pending Vehicles</div>
			</div>
		</div>

		<div class="moddash-stat-card">
			<div class="moddash-stat-icon complaint"><i class="fas fa-triangle-exclamation"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashOpenComplaints">0</div>
				<div class="moddash-stat-label">Open Complaints</div>
			</div>
		</div>

		<div class="moddash-stat-card">
			<div class="moddash-stat-icon payout"><i class="fas fa-hand-holding-dollar"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashPendingPayouts">0</div>
				<div class="moddash-stat-label">Pending Payouts</div>
			</div>
		</div>

		<div class="moddash-stat-card">
			<div class="moddash-stat-icon profit"><i class="fas fa-sack-dollar"></i></div>
			<div class="moddash-stat-content">
				<div class="moddash-stat-number" id="modDashSiteProfit">LKR 0.00</div>
				<div class="moddash-stat-label">30 Day Site Profit</div>
			</div>
		</div>
	</div>

	<div class="moddash-grid">
		<article class="moddash-panel moddash-panel-wide">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-layer-group"></i> Queue Breakdown</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/verification">Open verification</a>
			</div>
			<div class="moddash-breakdown-list">
				<div class="moddash-breakdown-row">
					<div class="meta"><span>Account verifications</span><strong id="modDashQueueAccounts">0</strong></div>
					<div class="bar"><span id="modDashQueueAccountsBar"></span></div>
				</div>
				<div class="moddash-breakdown-row">
					<div class="meta"><span>Tourist license checks</span><strong id="modDashQueueLicenses">0</strong></div>
					<div class="bar"><span id="modDashQueueLicensesBar"></span></div>
				</div>
				<div class="moddash-breakdown-row">
					<div class="meta"><span>Vehicle checks</span><strong id="modDashQueueVehicles">0</strong></div>
					<div class="bar"><span id="modDashQueueVehiclesBar"></span></div>
				</div>
				<div class="moddash-breakdown-row">
					<div class="meta"><span>Open complaints</span><strong id="modDashQueueComplaints">0</strong></div>
					<div class="bar"><span id="modDashQueueComplaintsBar"></span></div>
				</div>
				<div class="moddash-breakdown-row">
					<div class="meta"><span>Pending payouts</span><strong id="modDashQueuePayouts">0</strong></div>
					<div class="bar"><span id="modDashQueuePayoutsBar"></span></div>
				</div>
			</div>
		</article>

		<article class="moddash-panel moddash-panel-wide">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-wallet"></i> Finance Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/transaction">Open transaction</a>
			</div>
			<div class="moddash-metrics-grid">
				<div class="moddash-metric-card">
					<span class="label">Completed Traveler Payments</span>
					<strong id="modDashCompletedPaymentsCount">0</strong>
					<span class="meta">Total Revenue: <b id="modDashRevenueAmount">LKR 0.00</b></span>
				</div>
				<div class="moddash-metric-card">
					<span class="label">Cancelled and Refunded</span>
					<strong><span id="modDashCancelledPaymentsCount">0</span> + <span id="modDashRefundedPaymentsCount">0</span></strong>
					<span class="meta">Cancelled + refunded payment count</span>
				</div>
				<div class="moddash-metric-card">
					<span class="label">Pending Payout Queue</span>
					<strong id="modDashPendingPayoutCount">0</strong>
					<span class="meta">Amount in queue: <b id="modDashPendingPayoutAmount">LKR 0.00</b></span>
				</div>
				<div class="moddash-metric-card">
					<span class="label">Completed Payouts</span>
					<strong id="modDashPaidPayoutCount">0</strong>
					<span class="meta">Paid amount: <b id="modDashPaidPayoutAmount">LKR 0.00</b></span>
				</div>
			</div>
		</article>

		<article class="moddash-panel moddash-chart-panel">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-chart-column"></i> Queue Mix</h2>
			</div>
			<div class="moddash-chart-wrap">
				<canvas id="modDashQueueChart"></canvas>
				<p id="modDashQueueChartEmpty" class="moddash-chart-empty" style="display: none;">Not enough queue data for chart.</p>
			</div>
		</article>

		<article class="moddash-panel moddash-chart-panel">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-chart-line"></i> Revenue Trend (30 Days)</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/analytics">Open analytics</a>
			</div>
			<div class="moddash-chart-wrap">
				<canvas id="modDashRevenueTrendChart"></canvas>
				<p id="modDashRevenueTrendEmpty" class="moddash-chart-empty" style="display: none;">No revenue trend data available yet.</p>
			</div>
		</article>

		<article class="moddash-panel moddash-panel-list">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-bullhorn"></i> Recent Complaints</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/oversight">Open oversight</a>
			</div>
			<div id="modDashComplaintList" class="moddash-list-wrap"></div>
			<p id="modDashComplaintEmpty" class="moddash-empty-text" style="display: none;">No active complaints right now.</p>
		</article>

		<article class="moddash-panel moddash-panel-list">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-shield-halved"></i> Pending Verifications</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/verification">Review queue</a>
			</div>
			<div id="modDashVerificationList" class="moddash-list-wrap"></div>
			<p id="modDashVerificationEmpty" class="moddash-empty-text" style="display: none;">No pending verification items.</p>
		</article>

		<article class="moddash-panel moddash-panel-list">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-money-check-dollar"></i> Pending Payout Queue</h2>
				<a href="<?php echo URL_ROOT; ?>/moderator/transaction">Process payouts</a>
			</div>
			<div id="modDashPayoutList" class="moddash-list-wrap"></div>
			<p id="modDashPayoutEmpty" class="moddash-empty-text" style="display: none;">No pending payout requests.</p>
		</article>

		<article class="moddash-panel moddash-actions-panel">
			<div class="moddash-panel-header">
				<h2><i class="fas fa-bolt"></i> Quick Actions</h2>
			</div>
			<div class="moddash-actions-grid">
				<a href="<?php echo URL_ROOT; ?>/moderator/verification" class="moddash-action-link"><i class="fas fa-circle-check"></i><span>Review Verification</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/oversight" class="moddash-action-link"><i class="fas fa-headset"></i><span>Handle Complaints</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/transaction" class="moddash-action-link"><i class="fas fa-hand-holding-dollar"></i><span>Process Payouts</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/analytics" class="moddash-action-link"><i class="fas fa-chart-line"></i><span>View Analytics</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/userInfo" class="moddash-action-link"><i class="fas fa-users"></i><span>User Directory</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/tripInfo" class="moddash-action-link"><i class="fas fa-suitcase-rolling"></i><span>Trip Monitoring</span></a>
				<a href="<?php echo URL_ROOT; ?>/moderator/content" class="moddash-action-link"><i class="fas fa-folder-plus"></i><span>Manage Content</span></a>
			</div>
		</article>
	</div>
</section>
