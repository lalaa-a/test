<section class="support-subtab complain-feedback-subtab">
	<div class="subtab-header">
		<h2><i class="fas fa-circle-question"></i> Help Center - Complain and Feedback</h2>
		<p>Submit your complaint or feedback and track updates from the support team.</p>
	</div>

	<div class="feedback-layout">
		<div class="feedback-card">
			<h3><i class="fas fa-pen-to-square"></i> Submit Complaint / Feedback</h3>

			<form id="complaintFeedbackForm" class="feedback-form" novalidate>
				<div class="form-group">
					<label for="problemSubject">Subject</label>
					<select id="problemSubject" required>
						<option value="">Select a topic</option>
						<option value="booking">Booking Issue</option>
						<option value="payment">Payment Problem</option>
						<option value="trip">Trip Experience</option>
						<option value="guide_driver">Guide / Driver Concern</option>
						<option value="account">Account Help</option>
						<option value="feature">Feature Suggestion</option>
						<option value="other">Other</option>
					</select>
				</div>

				<div class="form-group">
					<label for="problemMessage">Message</label>
					<textarea
						id="problemMessage"
						rows="5"
						required
						placeholder="Describe your complaint or feedback clearly..."
					></textarea>
				</div>

				<div class="form-actions">
					<button type="submit" id="submitProblemBtn" class="submit-btn">
						<i class="fas fa-paper-plane"></i> Submit
					</button>
					<span id="problemFormNotice" class="form-notice" role="status" aria-live="polite"></span>
				</div>
			</form>
		</div>

		<div class="feedback-card">
			<div class="history-header">
				<h3><i class="fas fa-clock-rotate-left"></i> Your Submissions</h3>
				<div class="history-filters">
					<button type="button" class="history-filter-btn active" data-filter="all">All</button>
					<button type="button" class="history-filter-btn" data-filter="pending">Pending</button>
					<button type="button" class="history-filter-btn" data-filter="in_progress">In Progress</button>
					<button type="button" class="history-filter-btn" data-filter="completed">Completed</button>
				</div>
			</div>

			<div id="userProblemsList" class="problems-list" aria-live="polite">
				<div class="loading-state">
					<i class="fas fa-spinner fa-spin"></i>
					<span>Loading your submissions...</span>
				</div>
			</div>
		</div>
	</div>
</section>
