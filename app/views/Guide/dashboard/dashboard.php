<?php
	$guideDashboardUser = getLoggedInUser();
	$guideDashboardName = trim((string)($guideDashboardUser['fullname'] ?? 'Guide'));
	$guideDashboardFirstName = $guideDashboardName !== '' ? preg_split('/\s+/', $guideDashboardName)[0] : 'Guide';
?>

<section id="guideDashboardPage" class="guide-dashboard-page" data-url-root="<?php echo htmlspecialchars(URL_ROOT, ENT_QUOTES, 'UTF-8'); ?>">
	<header class="guide-hero-card">
		<div class="guide-hero-copy">
			<p class="guide-hero-kicker">Guide Overview</p>
			<h1 class="guide-hero-title">Welcome back, <?php echo htmlspecialchars($guideDashboardFirstName, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="guide-hero-subtitle">Track requests, visits, earnings, support, and spot coverage from one place.</p>

			<div class="guide-hero-actions">
				<a class="guide-primary-btn" href="<?php echo URL_ROOT; ?>/Guide/requests">
					<i class="fas fa-code-pull-request"></i>
					Open Requests
				</a>
				<a class="guide-ghost-btn" href="<?php echo URL_ROOT; ?>/Guide/schedule">
					<i class="fas fa-calendar-days"></i>
					Open Schedule
				</a>
				<a class="guide-ghost-btn" href="<?php echo URL_ROOT; ?>/Guide/guideSpots">
					<i class="fas fa-map-location-dot"></i>
					Guide Spots
				</a>
				<button type="button" id="guideDashboardRefreshBtn" class="guide-refresh-btn" title="Refresh dashboard">
					<i class="fas fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="guide-hero-aside">
			<div class="guide-hero-chip">
				<span class="label">Today</span>
				<span class="value" id="guideDashTodayDate">-</span>
			</div>
			<div class="guide-hero-chip">
				<span class="label">Upcoming Visits</span>
				<span class="value" id="guideDashHeroUpcoming">0</span>
			</div>
			<div class="guide-hero-chip">
				<span class="label">Unread Messages</span>
				<span class="value" id="guideDashHeroUnread">0</span>
			</div>
		</div>
	</header>

	<div class="guide-stats-grid">
		<div class="guide-stat-card">
			<div class="guide-stat-icon total"><i class="fas fa-code-pull-request"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashTotalRequests">0</div>
				<div class="guide-stat-label">Total Requests</div>
			</div>
		</div>

		<div class="guide-stat-card">
			<div class="guide-stat-icon pending"><i class="fas fa-clock"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashPendingRequests">0</div>
				<div class="guide-stat-label">Pending Requests</div>
			</div>
		</div>

		<div class="guide-stat-card">
			<div class="guide-stat-icon accepted"><i class="fas fa-check-circle"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashAcceptedRequests">0</div>
				<div class="guide-stat-label">Accepted Requests</div>
			</div>
		</div>

		<div class="guide-stat-card">
			<div class="guide-stat-icon visits"><i class="fas fa-map-marked-alt"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashOngoingVisits">0</div>
				<div class="guide-stat-label">Ongoing Visits</div>
			</div>
		</div>

		<div class="guide-stat-card">
			<div class="guide-stat-icon spots"><i class="fas fa-map-location-dot"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashActiveSpots">0</div>
				<div class="guide-stat-label">Active Spots</div>
			</div>
		</div>

		<div class="guide-stat-card">
			<div class="guide-stat-icon messages"><i class="fas fa-envelope"></i></div>
			<div class="guide-stat-content">
				<div class="guide-stat-number" id="guideDashUnreadMessages">0</div>
				<div class="guide-stat-label">Unread Messages</div>
			</div>
		</div>
	</div>

	<div class="guide-dashboard-grid">
		<article class="guide-panel request-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-signal"></i> Request Pipeline</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/requests">Manage</a>
			</div>
			<div class="guide-breakdown-list">
				<div class="guide-breakdown-row">
					<div class="meta"><span>Pending</span><strong id="guideDashPendingCountRow">0</strong></div>
					<div class="bar"><span id="guideDashPendingBar"></span></div>
				</div>
				<div class="guide-breakdown-row">
					<div class="meta"><span>Accepted</span><strong id="guideDashAcceptedCountRow">0</strong></div>
					<div class="bar"><span id="guideDashAcceptedBar"></span></div>
				</div>
				<div class="guide-breakdown-row">
					<div class="meta"><span>Rejected</span><strong id="guideDashRejectedCountRow">0</strong></div>
					<div class="bar"><span id="guideDashRejectedBar"></span></div>
				</div>
			</div>
		</article>

		<article class="guide-panel earnings-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-sack-dollar"></i> Earnings Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/earnings">View earnings</a>
			</div>
			<div class="guide-earnings-metrics">
				<div class="guide-metric-card">
					<span class="label">Pending Amount</span>
					<strong id="guideDashPendingAmount">LKR 0.00</strong>
					<span class="meta" id="guideDashPendingTrips">0 trips</span>
				</div>
				<div class="guide-metric-card">
					<span class="label">Paid Amount</span>
					<strong id="guideDashPaidAmount">LKR 0.00</strong>
					<span class="meta" id="guideDashPaidTrips">0 trips</span>
				</div>
				<div class="guide-metric-card">
					<span class="label">Refunded Amount</span>
					<strong id="guideDashRefundedAmount">LKR 0.00</strong>
					<span class="meta" id="guideDashRefundedTrips">0 trips</span>
				</div>
			</div>
		</article>

		<article class="guide-panel upcoming-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-calendar-check"></i> Upcoming & Ongoing Visits</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/schedule">Open schedule</a>
			</div>
			<div id="guideDashUpcomingList" class="guide-list-wrap"></div>
			<p id="guideDashUpcomingEmpty" class="guide-empty-text" style="display: none;">No upcoming or ongoing visits.</p>
		</article>

		<article class="guide-panel recent-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-history"></i> Recent Request Activity</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/requests">Open requests</a>
			</div>
			<div id="guideDashRecentList" class="guide-list-wrap"></div>
			<p id="guideDashRecentEmpty" class="guide-empty-text" style="display: none;">No request activity yet.</p>
		</article>

		<article class="guide-panel spots-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-map-location-dot"></i> Spot Coverage</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/guideSpots">Manage spots</a>
			</div>
			<div class="guide-spots-metrics">
				<div class="chip"><span class="chip-label">Total</span><strong id="guideDashTotalSpots">0</strong></div>
				<div class="chip"><span class="chip-label">Active</span><strong id="guideDashActiveSpotsChip">0</strong></div>
				<div class="chip"><span class="chip-label">Inactive</span><strong id="guideDashInactiveSpots">0</strong></div>
			</div>
			<div id="guideDashSpotsList" class="guide-list-wrap"></div>
			<p id="guideDashSpotsEmpty" class="guide-empty-text" style="display: none;">No guide spots added yet.</p>
		</article>

		<article class="guide-panel support-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-life-ring"></i> Support Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/Guide/support">Open support</a>
			</div>
			<div id="guideDashSupportList" class="guide-list-wrap"></div>
			<p id="guideDashSupportEmpty" class="guide-empty-text" style="display: none;">No support activity yet.</p>
		</article>

		<article class="guide-panel actions-panel">
			<div class="guide-panel-header">
				<h2><i class="fas fa-bolt"></i> Quick Actions</h2>
			</div>
			<div class="guide-quick-actions-grid">
				<a href="<?php echo URL_ROOT; ?>/Guide/requests" class="guide-quick-action"><i class="fas fa-code-pull-request"></i><span>Review Requests</span></a>
				<a href="<?php echo URL_ROOT; ?>/Guide/schedule" class="guide-quick-action"><i class="fas fa-calendar-days"></i><span>Update Schedule</span></a>
				<a href="<?php echo URL_ROOT; ?>/Guide/guideSpots" class="guide-quick-action"><i class="fas fa-map-location-dot"></i><span>Manage Spots</span></a>
				<a href="<?php echo URL_ROOT; ?>/Guide/earnings" class="guide-quick-action"><i class="fas fa-sack-dollar"></i><span>Check Earnings</span></a>
				<a href="<?php echo URL_ROOT; ?>/Guide/support" class="guide-quick-action"><i class="fas fa-headset"></i><span>Open Helpdesk</span></a>
				<a href="<?php echo URL_ROOT; ?>/Guide/guideProfile" class="guide-quick-action"><i class="fas fa-user"></i><span>Update Profile</span></a>
			</div>
		</article>
	</div>
</section>
