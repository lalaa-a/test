<?php
	$driverDashboardUser = getLoggedInUser();
	$driverDashboardName = trim((string)($driverDashboardUser['fullname'] ?? 'Driver'));
	$driverDashboardFirstName = $driverDashboardName !== '' ? preg_split('/\s+/', $driverDashboardName)[0] : 'Driver';
?>

<section id="driverDashboardPage" class="driver-dashboard-page" data-url-root="<?php echo htmlspecialchars(URL_ROOT, ENT_QUOTES, 'UTF-8'); ?>">
	<header class="dashboard-hero-card">
		<div class="dashboard-hero-copy">
			<p class="dashboard-hero-kicker">Driver Overview</p>
			<h1 class="dashboard-hero-title">Welcome back, <?php echo htmlspecialchars($driverDashboardFirstName, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="dashboard-hero-subtitle">Monitor requests, earnings, fleet status, and support activity from one place.</p>

			<div class="dashboard-hero-actions">
				<a class="dashboard-primary-btn" href="<?php echo URL_ROOT; ?>/Driver/requests">
					<i class="fas fa-code-pull-request"></i>
					Open Requests
				</a>
				<a class="dashboard-ghost-btn" href="<?php echo URL_ROOT; ?>/Driver/schedule">
					<i class="fas fa-calendar-days"></i>
					Open Schedule
				</a>
				<button type="button" id="driverDashboardRefreshBtn" class="refresh-btn" title="Refresh dashboard">
					<i class="fas fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="dashboard-hero-aside">
			<div class="dashboard-hero-chip">
				<span class="label">Today</span>
				<span class="value" id="dashTodayDate">-</span>
			</div>
			<div class="dashboard-hero-chip">
				<span class="label">Pending Requests</span>
				<span class="value" id="dashHeroPendingCount">0</span>
			</div>
			<div class="dashboard-hero-chip">
				<span class="label">Unread Messages</span>
				<span class="value" id="dashHeroUnreadCount">0</span>
			</div>
		</div>
	</header>

	<div class="stats-grid">
		<div class="stat-card">
			<div class="stat-icon total"><i class="fas fa-code-pull-request"></i></div>
			<div class="stat-content">
				<div class="stat-number" id="dashTotalRequests">0</div>
				<div class="stat-label">Total Requests</div>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon pending"><i class="fas fa-clock"></i></div>
			<div class="stat-content">
				<div class="stat-number" id="dashPendingRequests">0</div>
				<div class="stat-label">Pending Requests</div>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon approved"><i class="fas fa-check-circle"></i></div>
			<div class="stat-content">
				<div class="stat-number" id="dashAcceptedRequests">0</div>
				<div class="stat-label">Accepted Requests</div>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon vehicles"><i class="fas fa-car"></i></div>
			<div class="stat-content">
				<div class="stat-number" id="dashActiveVehicles">0</div>
				<div class="stat-label">Active Vehicles</div>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon messages"><i class="fas fa-envelope"></i></div>
			<div class="stat-content">
				<div class="stat-number" id="dashUnreadMessages">0</div>
				<div class="stat-label">Unread Messages</div>
			</div>
		</div>
	</div>

	<div class="dashboard-grid">
		<article class="dashboard-panel request-panel">
			<div class="panel-header">
				<h2><i class="fas fa-signal"></i> Request Pipeline</h2>
				<a href="<?php echo URL_ROOT; ?>/Driver/requests">Manage</a>
			</div>
			<div class="breakdown-list">
				<div class="breakdown-row">
					<div class="meta"><span>Pending</span><strong id="dashPendingCountRow">0</strong></div>
					<div class="bar"><span id="dashPendingBar"></span></div>
				</div>
				<div class="breakdown-row">
					<div class="meta"><span>Accepted</span><strong id="dashAcceptedCountRow">0</strong></div>
					<div class="bar"><span id="dashAcceptedBar"></span></div>
				</div>
				<div class="breakdown-row">
					<div class="meta"><span>Rejected</span><strong id="dashRejectedCountRow">0</strong></div>
					<div class="bar"><span id="dashRejectedBar"></span></div>
				</div>
			</div>
		</article>

		<article class="dashboard-panel earnings-panel">
			<div class="panel-header">
				<h2><i class="fas fa-sack-dollar"></i> Earnings Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/Driver/earnings">View earnings</a>
			</div>
			<div class="earnings-metrics">
				<div class="metric-card">
					<span class="label">Pending Amount</span>
					<strong id="dashPendingAmount">LKR 0.00</strong>
					<span class="meta" id="dashPendingTrips">0 trips</span>
				</div>
				<div class="metric-card">
					<span class="label">Paid Amount</span>
					<strong id="dashPaidAmount">LKR 0.00</strong>
					<span class="meta" id="dashPaidTrips">0 trips</span>
				</div>
				<div class="metric-card">
					<span class="label">Refunded Amount</span>
					<strong id="dashRefundedAmount">LKR 0.00</strong>
					<span class="meta" id="dashRefundedTrips">0 trips</span>
				</div>
			</div>
		</article>

		<article class="dashboard-panel upcoming-panel">
			<div class="panel-header">
				<h2><i class="fas fa-calendar-check"></i> Upcoming Accepted Trips</h2>
				<a href="<?php echo URL_ROOT; ?>/Driver/schedule">Open schedule</a>
			</div>
			<div id="dashUpcomingList" class="list-wrap"></div>
			<p id="dashUpcomingEmpty" class="empty-text" style="display: none;">No upcoming accepted trips.</p>
		</article>

		<article class="dashboard-panel recent-panel">
			<div class="panel-header">
				<h2><i class="fas fa-history"></i> Recent Request Activity</h2>
				<a href="<?php echo URL_ROOT; ?>/Driver/requests">Open requests</a>
			</div>
			<div id="dashRecentList" class="list-wrap"></div>
			<p id="dashRecentEmpty" class="empty-text" style="display: none;">No request activity yet.</p>
		</article>

		<article class="dashboard-panel fleet-panel">
			<div class="panel-header">
				<h2><i class="fas fa-car-side"></i> Fleet Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/Driver/vehicles">Manage vehicles</a>
			</div>
			<div class="fleet-metrics">
				<div class="chip"><span class="chip-label">Total</span><strong id="dashTotalVehicles">0</strong></div>
				<div class="chip"><span class="chip-label">In Use</span><strong id="dashInUseVehicles">0</strong></div>
				<div class="chip"><span class="chip-label">Pending Approval</span><strong id="dashPendingVehicles">0</strong></div>
			</div>
			<div id="dashFleetList" class="list-wrap"></div>
			<p id="dashFleetEmpty" class="empty-text" style="display: none;">No vehicles found. Add one to start accepting trips.</p>
		</article>

		<article class="dashboard-panel actions-panel">
			<div class="panel-header">
				<h2><i class="fas fa-bolt"></i> Quick Actions</h2>
			</div>
			<div class="quick-actions-grid">
				<a href="<?php echo URL_ROOT; ?>/Driver/requests" class="quick-action"><i class="fas fa-code-pull-request"></i><span>Review Requests</span></a>
				<a href="<?php echo URL_ROOT; ?>/Driver/schedule" class="quick-action"><i class="fas fa-calendar-days"></i><span>Update Schedule</span></a>
				<a href="<?php echo URL_ROOT; ?>/Driver/pricing" class="quick-action"><i class="fas fa-money-check-dollar"></i><span>Adjust Pricing</span></a>
				<a href="<?php echo URL_ROOT; ?>/Driver/vehicles" class="quick-action"><i class="fas fa-car"></i><span>Manage Vehicles</span></a>
				<a href="<?php echo URL_ROOT; ?>/Driver/earnings" class="quick-action"><i class="fas fa-sack-dollar"></i><span>Check Earnings</span></a>
				<a href="<?php echo URL_ROOT; ?>/Driver/support" class="quick-action"><i class="fas fa-headset"></i><span>Open Helpdesk</span></a>
			</div>
		</article>
	</div>
</section>
