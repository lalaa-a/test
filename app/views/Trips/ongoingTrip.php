<?php
	$trip = is_array($trip ?? null) ? $trip : [];
	$datePills = is_array($datePills ?? null) ? $datePills : [];

	$tripId = (int)($trip['tripId'] ?? 0);
	$tripTitle = trim((string)($trip['tripTitle'] ?? 'Ongoing Trip'));
	$tripDescription = trim((string)($trip['description'] ?? 'Follow your ongoing trip events and completion status by date.'));
	$status = strtolower((string)($trip['status'] ?? 'ongoing'));

	$statusLabels = [
		'pending' => 'Pending',
		'wconfirmation' => 'Waiting Confirmation',
		'awpayment' => 'Awaiting Payment',
		'scheduled' => 'Scheduled',
		'ongoing' => 'Ongoing',
		'completed' => 'Completed',
		'cancelled' => 'Cancelled'
	];

	$statusLabel = $statusLabels[$status] ?? ucfirst($status ?: 'ongoing');

	$startDateRaw = $trip['startDate'] ?? null;
	$endDateRaw = $trip['endDate'] ?? null;

	$startDateLabel = 'Not set';
	$endDateLabel = 'Not set';
	$durationLabel = 'Not set';

	if (!empty($startDateRaw)) {
		$startDateLabel = date('d M Y', strtotime((string)$startDateRaw));
	}

	if (!empty($endDateRaw)) {
		$endDateLabel = date('d M Y', strtotime((string)$endDateRaw));
	}

	if (!empty($startDateRaw) && !empty($endDateRaw)) {
		$startDateObj = new DateTime((string)$startDateRaw);
		$endDateObj = new DateTime((string)$endDateRaw);
		$dayCount = $startDateObj->diff($endDateObj)->days + 1;
		$durationLabel = $dayCount . ' day' . ($dayCount === 1 ? '' : 's');
	}

	$activePill = null;
	foreach ($datePills as $pill) {
		if (!empty($pill['isActive'])) {
			$activePill = $pill;
			break;
		}
	}

	$selectedDateLabel = 'Select a date to load event timeline.';
	if (!empty($activePill['iso'])) {
		$selectedDateLabel = date('D, M d, Y', strtotime((string)$activePill['iso']));
	}
?>

<div class="content-wrapper">
	<section class="ongoing-trip-page" data-trip-id="<?php echo htmlspecialchars((string)$tripId, ENT_QUOTES, 'UTF-8'); ?>">
		<div class="ongoing-hero">
			<div class="hero-copy">
				<p class="hero-eyebrow">Live Trip Timeline</p>
				<h1 class="hero-title"><?php echo htmlspecialchars($tripTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
				<p class="hero-description"><?php echo htmlspecialchars($tripDescription, ENT_QUOTES, 'UTF-8'); ?></p>
			</div>
			<div class="hero-status-pill">
				<i class="fas fa-route"></i>
				<span><?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?></span>
			</div>
		</div>

		<div class="meta-grid">
			<article class="meta-card">
				<span class="meta-label">Trip Start</span>
				<span class="meta-value"><?php echo htmlspecialchars($startDateLabel, ENT_QUOTES, 'UTF-8'); ?></span>
				<span class="meta-help">Beginning of your confirmed timeline</span>
			</article>

			<article class="meta-card">
				<span class="meta-label">Trip End</span>
				<span class="meta-value"><?php echo htmlspecialchars($endDateLabel, ENT_QUOTES, 'UTF-8'); ?></span>
				<span class="meta-help">Final day for planned events</span>
			</article>

			<article class="meta-card">
				<span class="meta-label">Duration</span>
				<span class="meta-value"><?php echo htmlspecialchars($durationLabel, ENT_QUOTES, 'UTF-8'); ?></span>
				<span class="meta-help">Total number of trip days</span>
			</article>

			<article class="meta-card meta-card-pin">
				<span class="meta-label">Trip Start PIN</span>
				<span class="meta-value" id="start-pin-value">Loading...</span>
				<div class="pin-action-row">
					<button type="button" class="pin-action-btn" id="start-pin-toggle">View PIN</button>
				</div>
				<span class="meta-help" id="start-pin-hint">Start PIN details will appear after snapshot sync.</span>
			</article>
		</div>

		<section class="timeline-shell">
			<div class="timeline-head">
				<h3>Trip Timeline By Date</h3>
				<p id="selected-date-label"><?php echo htmlspecialchars($selectedDateLabel, ENT_QUOTES, 'UTF-8'); ?></p>
			</div>

			<div class="date-pills" id="ongoing-date-grid">
				<?php if (empty($datePills)): ?>
					<div class="state-block">Trip dates are not available.</div>
				<?php else: ?>
					<?php foreach ($datePills as $pill): ?>
						<button
							type="button"
							class="date-pill <?php echo !empty($pill['isActive']) ? 'active' : ''; ?>"
							data-date="<?php echo htmlspecialchars((string)$pill['iso'], ENT_QUOTES, 'UTF-8'); ?>">
							<span class="pill-day"><?php echo htmlspecialchars((string)$pill['day'], ENT_QUOTES, 'UTF-8'); ?></span>
							<span class="pill-date"><?php echo htmlspecialchars((string)$pill['date'], ENT_QUOTES, 'UTF-8'); ?></span>
							<span class="pill-month"><?php echo htmlspecialchars((string)$pill['month'], ENT_QUOTES, 'UTF-8'); ?></span>
						</button>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</section>

		<div class="content-grid">
			<section class="panel">
				<div class="panel-head">
					<h3>Event Timeline</h3>
					<span class="panel-sub">Non-editable ongoing view with traveller completion marks</span>
				</div>
				<div class="event-list" id="ongoing-event-list">
					<div class="state-block">Loading event timeline...</div>
				</div>
			</section>

			<section class="panel map-panel">
				<div class="panel-head">
					<h3>Route Map</h3>
					<span class="panel-sub">Points and path for the selected date</span>
				</div>
				<div class="route-map" id="ongoing-route-map"></div>
				<p class="map-empty-state" id="map-empty-state">Map points are not available for this date.</p>
			</section>
		</div>

		<section class="completion-strip">
			<h3>Completion Summary</h3>
			<p id="completion-summary">Loading completion status...</p>
		</section>
	</section>
</div>
