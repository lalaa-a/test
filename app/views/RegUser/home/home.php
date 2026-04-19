<?php
	$travellerHomeUser = getLoggedInUser();
	$travellerHomeName = trim((string)($travellerHomeUser['fullname'] ?? 'Traveller'));
	$travellerHomeFirstName = $travellerHomeName !== '' ? preg_split('/\s+/', $travellerHomeName)[0] : 'Traveller';
?>

<section class="traveller-home-page" data-url-root="<?php echo htmlspecialchars(URL_ROOT, ENT_QUOTES, 'UTF-8'); ?>">
	<header class="home-hero-card">
		<div class="home-hero-copy">
			<p class="home-hero-kicker">Traveller Overview</p>
			<h1 class="home-hero-title">Welcome back, <?php echo htmlspecialchars($travellerHomeFirstName, ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="home-hero-subtitle">Track trips, pending actions, messages, and support updates from one place.</p>

			<div class="home-hero-actions">
				<a class="home-primary-btn" href="<?php echo URL_ROOT; ?>/RegUser/trips">
					<i class="fa-solid fa-suitcase-rolling"></i>
					Open Trips
				</a>
				<a class="home-ghost-btn" href="<?php echo URL_ROOT; ?>/RegUser/destinations">
					<i class="fa-solid fa-location-dot"></i>
					Explore Destinations
				</a>
				<button type="button" class="home-icon-btn" id="homeRefreshBtn" title="Refresh dashboard">
					<i class="fa-solid fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="home-hero-aside">
			<div class="home-hero-chip">
				<span class="label">Today</span>
				<span class="value" id="homeTodayDate">-</span>
			</div>
			<div class="home-hero-chip">
				<span class="label">Upcoming Trips</span>
				<span class="value" id="homeHeroUpcomingCount">0</span>
			</div>
			<div class="home-hero-chip">
				<span class="label">Pending Payments</span>
				<span class="value" id="homeHeroPaymentCount">0</span>
			</div>
		</div>
	</header>

	<section class="home-stats-grid" aria-label="Traveller summary">
		<article class="home-stat-card">
			<div class="home-stat-icon total"><i class="fa-solid fa-route"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homeTotalTrips">0</div>
				<div class="home-stat-label">Total Trips</div>
			</div>
		</article>

		<article class="home-stat-card">
			<div class="home-stat-icon active"><i class="fa-solid fa-plane-departure"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homeActiveTrips">0</div>
				<div class="home-stat-label">Active Journeys</div>
			</div>
		</article>

		<article class="home-stat-card">
			<div class="home-stat-icon pending"><i class="fa-solid fa-hourglass-half"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homePendingActions">0</div>
				<div class="home-stat-label">Pending Actions</div>
			</div>
		</article>

		<article class="home-stat-card">
			<div class="home-stat-icon message"><i class="fa-solid fa-envelope"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homeUnreadMessages">0</div>
				<div class="home-stat-label">Unread Messages</div>
			</div>
		</article>

		<article class="home-stat-card">
			<div class="home-stat-icon support"><i class="fa-solid fa-handshake-angle"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homeOpenProblems">0</div>
				<div class="home-stat-label">Open Support Items</div>
			</div>
		</article>

		<article class="home-stat-card">
			<div class="home-stat-icon package"><i class="fa-solid fa-box-open"></i></div>
			<div class="home-stat-content">
				<div class="home-stat-number" id="homePackageCount">0</div>
				<div class="home-stat-label">Active Packages</div>
			</div>
		</article>
	</section>

	<section class="home-content-grid">
		<article class="home-panel home-status-panel">
			<div class="home-panel-head">
				<h2>Trip Status Breakdown</h2>
				<a href="<?php echo URL_ROOT; ?>/RegUser/trips">View all trips</a>
			</div>

			<div id="homeStatusBreakdown" class="home-breakdown-list">
				<div class="home-breakdown-row" data-status="pending">
					<div class="meta"><span>Pending</span><strong id="homeStatusPending">0</strong></div>
					<div class="bar"><span id="homeStatusPendingBar"></span></div>
				</div>
				<div class="home-breakdown-row" data-status="wconfirmation">
					<div class="meta"><span>Waiting Confirmation</span><strong id="homeStatusWconfirmation">0</strong></div>
					<div class="bar"><span id="homeStatusWconfirmationBar"></span></div>
				</div>
				<div class="home-breakdown-row" data-status="awpayment">
					<div class="meta"><span>Awaiting Payment</span><strong id="homeStatusAwpayment">0</strong></div>
					<div class="bar"><span id="homeStatusAwpaymentBar"></span></div>
				</div>
				<div class="home-breakdown-row" data-status="scheduled">
					<div class="meta"><span>Scheduled</span><strong id="homeStatusScheduled">0</strong></div>
					<div class="bar"><span id="homeStatusScheduledBar"></span></div>
				</div>
				<div class="home-breakdown-row" data-status="ongoing">
					<div class="meta"><span>Ongoing</span><strong id="homeStatusOngoing">0</strong></div>
					<div class="bar"><span id="homeStatusOngoingBar"></span></div>
				</div>
				<div class="home-breakdown-row" data-status="completed">
					<div class="meta"><span>Completed</span><strong id="homeStatusCompleted">0</strong></div>
					<div class="bar"><span id="homeStatusCompletedBar"></span></div>
				</div>
			</div>
		</article>

		<article class="home-panel home-trips-panel">
			<div class="home-panel-head">
				<h2>Upcoming & Recent Trips</h2>
				<a href="<?php echo URL_ROOT; ?>/RegUser/trips">Manage trips</a>
			</div>

			<div id="homeTripsList" class="home-list"></div>
			<p id="homeTripsEmpty" class="home-empty" style="display: none;">No trips yet. Create your first itinerary to get started.</p>
		</article>

		<article class="home-panel home-actions-panel">
			<div class="home-panel-head">
				<h2>Quick Actions</h2>
			</div>

			<div class="home-quick-actions">
				<a href="<?php echo URL_ROOT; ?>/RegUser/trips" class="home-quick-action">
					<i class="fa-solid fa-plus"></i>
					<span>Create Trip</span>
				</a>
				<a href="<?php echo URL_ROOT; ?>/RegUser/drivers" class="home-quick-action">
					<i class="fa-solid fa-car"></i>
					<span>Find Drivers</span>
				</a>
				<a href="<?php echo URL_ROOT; ?>/RegUser/guides" class="home-quick-action">
					<i class="fa-solid fa-compass"></i>
					<span>Find Guides</span>
				</a>
				<a href="<?php echo URL_ROOT; ?>/RegUser/packages" class="home-quick-action">
					<i class="fa-solid fa-box-open"></i>
					<span>Browse Packages</span>
				</a>
				<a href="<?php echo URL_ROOT; ?>/RegUser/support" class="home-quick-action">
					<i class="fa-solid fa-headset"></i>
					<span>Open Helpdesk</span>
				</a>
				<a href="<?php echo URL_ROOT; ?>/RegUser/destinations" class="home-quick-action">
					<i class="fa-solid fa-location-dot"></i>
					<span>Explore Spots</span>
				</a>
			</div>
		</article>

		<article class="home-panel home-support-panel">
			<div class="home-panel-head">
				<h2>Support Snapshot</h2>
				<a href="<?php echo URL_ROOT; ?>/RegUser/support">Open support</a>
			</div>

			<div id="homeProblemsList" class="home-list"></div>
			<p id="homeProblemsEmpty" class="home-empty" style="display: none;">No support updates yet.</p>
		</article>

		<article class="home-panel home-packages-panel">
			<div class="home-panel-head">
				<h2>Package Highlights</h2>
				<a href="<?php echo URL_ROOT; ?>/RegUser/packages">View packages</a>
			</div>

			<div id="homePackageHighlights" class="home-package-highlights"></div>
		</article>
	</section>
</section>
