<div class="page-header">
	<div class="page-header-content">
		<div class="page-title-section">
			<h1 class="page-title">Profile Information</h1>
			<p class="page-subtitle">Search and view tourist, driver, and guide profile details by user ID</p>
		</div>
	</div>
</div>

<div class="verification-section profile-search-section" id="profile-search-section">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-user"></i>
				Find User
			</h2>
			<div class="section-controls">
				<div class="search-filter-section">
					<div class="search-box">
						<i class="fas fa-search"></i>
						<input type="number" id="profileUserIdInput" placeholder="Enter user ID" class="search-input" min="1">
					</div>
					<button id="profileSearchBtn" class="btn btn-primary profile-search-btn">
						<i class="fas fa-search"></i>
						Search
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="profileInfoAlert" class="profile-alert" style="display:none;"></div>

<div id="profileInfoContainer" class="verification-section profile-info-container" style="display:none;">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-id-card"></i>
				User Profile Details
			</h2>
		</div>
	</div>

	<div class="profile-summary">
		<div class="profile-summary-left">
			<img id="profilePhoto" src="" alt="Profile photo" class="profile-avatar" onerror="this.src='http://localhost/test/public/img/default-avatar.png'">
			<div>
				<h3 id="profileFullname">-</h3>
				<p id="profileEmail">-</p>
				<p id="profileAccountType" class="account-type-badge">-</p>
			</div>
		</div>
		<div class="profile-summary-right">
			<div class="summary-chip"><span>User ID</span><strong id="profileUserId">-</strong></div>
			<div class="summary-chip"><span>Rating</span><strong id="profileRating">-</strong></div>
			<div class="summary-chip"><span>Reviews</span><strong id="profileReviewCount">0</strong></div>
		</div>
	</div>

	<div class="profile-grid">
		<div class="profile-card">
			<h4><i class="fas fa-user-circle"></i> Basic Information</h4>
			<ul class="detail-list">
				<li><span>Full Name</span><strong id="d_fullname">-</strong></li>
				<li><span>Language</span><strong id="d_language">-</strong></li>
				<li><span>DOB</span><strong id="d_dob">-</strong></li>
				<li><span>Gender</span><strong id="d_gender">-</strong></li>
				<li><span>Currency</span><strong id="d_currency">-</strong></li>
			</ul>
		</div>

		<div class="profile-card">
			<h4><i class="fas fa-address-book"></i> Contact Information</h4>
			<ul class="detail-list">
				<li><span>Primary Phone</span><strong id="d_phone">-</strong></li>
				<li><span>Secondary Phone</span><strong id="d_secondary_phone">-</strong></li>
				<li><span>Email</span><strong id="d_email">-</strong></li>
				<li><span>Address</span><strong id="d_address">-</strong></li>
			</ul>
		</div>

		<div class="profile-card">
			<h4><i class="fas fa-id-badge"></i> Profile Details</h4>
			<ul class="detail-list">
				<li><span>Bio</span><strong id="d_bio">-</strong></li>
				<li><span>Languages</span><strong id="d_languages">-</strong></li>
				<li><span>Instagram</span><strong id="d_insta">-</strong></li>
				<li><span>Facebook</span><strong id="d_facebook">-</strong></li>
			</ul>
		</div>

		<div class="profile-card">
			<h4><i class="fas fa-shield-alt"></i> Verification & Activity</h4>
			<ul class="detail-list">
				<li><span>DL Verified</span><strong id="d_dlVerified">-</strong></li>
				<li><span>TL Submitted</span><strong id="d_tlSubmitted">-</strong></li>
				<li><span>TL Verified</span><strong id="d_tlVerified">-</strong></li>
				<li><span>TL Number</span><strong id="d_tLicenseNumber">-</strong></li>
				<li><span>TL Expiry</span><strong id="d_tLicenseExpiryDate">-</strong></li>
				<li><span>Last Login</span><strong id="d_last_login">-</strong></li>
				<li><span>Created At</span><strong id="d_created_at">-</strong></li>
				<li><span>Updated At</span><strong id="d_updated_at">-</strong></li>
			</ul>
		</div>
	</div>

	<div id="driverVehiclesSection" class="profile-related-section" style="display:none;">
		<h4><i class="fas fa-car"></i> Driver Vehicle Information</h4>
		<div id="driverVehiclesList" class="related-list">
			<p class="empty-reviews">No vehicle records found for this driver.</p>
		</div>
	</div>

	<div class="profile-reviews">
		<h4><i class="fas fa-star"></i> Traveller Reviews Received</h4>
		<div id="profileReviewsList" class="reviews-list">
			<p class="empty-reviews">No reviews found for this user.</p>
		</div>
	</div>

	<div id="guideLocationsSection" class="profile-related-section" style="display:none;">
		<h4><i class="fas fa-map-marked-alt"></i> Guide Location Information</h4>
		<div id="guideLocationsList" class="related-list">
			<p class="empty-reviews">No guide location records found for this guide.</p>
		</div>
	</div>
</div>
