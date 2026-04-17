<div class="page-header">
	<div class="page-header-content">
		<div class="page-title-section">
			<h1 class="page-title">Vehicle Information</h1>
			<p class="page-subtitle">Enter a vehicle ID to view vehicle details, owner information, verification and pricing</p>
		</div>
	</div>
</div>

<div class="verification-section vehicle-search-section">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-car"></i>
				Find Vehicle
			</h2>
			<div class="section-controls">
				<div class="search-filter-section">
					<div class="search-box">
						<i class="fas fa-search"></i>
						<input type="number" id="vehicleIdInput" placeholder="Enter vehicle ID" class="search-input" min="1">
					</div>
					<button id="vehicleSearchBtn" class="btn btn-primary vehicle-search-btn">
						<i class="fas fa-search"></i>
						Search
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="vehicleInfoAlert" class="vehicle-alert" style="display:none;"></div>

<div id="vehicleInfoContainer" class="verification-section vehicle-info-container" style="display:none;">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-id-card"></i>
				Vehicle Details
			</h2>
		</div>
	</div>

	<div class="vehicle-summary">
		<div class="vehicle-summary-left">
			<img id="vehicleMainPhoto" src="" alt="Vehicle photo" class="vehicle-avatar" onerror="this.src='http://localhost/test/public/img/default-vehicle.jpg'">
			<div>
				<h3 id="v_title">-</h3>
				<p id="v_plate">-</p>
				<p id="v_status_badge" class="status-badge">-</p>
			</div>
		</div>
		<div class="vehicle-summary-right">
			<div class="summary-chip"><span>Vehicle ID</span><strong id="v_vehicleId">-</strong></div>
			<div class="summary-chip"><span>Owner ID</span><strong id="v_ownerId">-</strong></div>
			<div class="summary-chip"><span>Verification</span><strong id="v_verification">-</strong></div>
		</div>
	</div>

	<div class="vehicle-grid">
		<div class="vehicle-card">
			<h4><i class="fas fa-car-side"></i> Vehicle Specifications</h4>
			<ul class="detail-list">
				<li><span>Make</span><strong id="d_make">-</strong></li>
				<li><span>Model</span><strong id="d_model">-</strong></li>
				<li><span>Year</span><strong id="d_year">-</strong></li>
				<li><span>Color</span><strong id="d_color">-</strong></li>
				<li><span>License Plate</span><strong id="d_licensePlate">-</strong></li>
				<li><span>Seating Capacity</span><strong id="d_seatingCapacity">-</strong></li>
				<li><span>Child Seats</span><strong id="d_childSeats">-</strong></li>
				<li><span>Fuel Efficiency</span><strong id="d_fuelEfficiency">-</strong></li>
				<li><span>Description</span><strong id="d_description">-</strong></li>
				<li><span>Active Status</span><strong id="d_status">-</strong></li>
				<li><span>Availability</span><strong id="d_availability">-</strong></li>
				<li><span>Approved</span><strong id="d_isApproved">-</strong></li>
			</ul>
		</div>

		<div class="vehicle-card">
			<h4><i class="fas fa-user"></i> Owner Information</h4>
			<div class="owner-head">
				<img id="ownerPhoto" src="" alt="Owner photo" class="owner-avatar" onerror="this.src='http://localhost/test/public/img/default-avatar.png'">
				<div>
					<p class="owner-name" id="o_name">-</p>
					<p class="owner-type" id="o_type">-</p>
				</div>
			</div>
			<ul class="detail-list">
				<li><span>Email</span><strong id="o_email">-</strong></li>
				<li><span>Primary Phone</span><strong id="o_phone">-</strong></li>
				<li><span>Secondary Phone</span><strong id="o_secondary">-</strong></li>
				<li><span>Address</span><strong id="o_address">-</strong></li>
				<li><span>Last Login</span><strong id="o_last_login">-</strong></li>
			</ul>
		</div>

		<div class="vehicle-card">
			<h4><i class="fas fa-shield-alt"></i> Verification Details</h4>
			<ul class="detail-list">
				<li><span>Status</span><strong id="ver_status">-</strong></li>
				<li><span>Reviewed By</span><strong id="ver_reviewer">-</strong></li>
				<li><span>Reviewed At</span><strong id="ver_reviewedAt">-</strong></li>
				<li><span>Expiry Date</span><strong id="ver_expiryDate">-</strong></li>
				<li><span>Rejection Reason</span><strong id="ver_rejectionReason">-</strong></li>
			</ul>
		</div>

		<div class="vehicle-card">
			<h4><i class="fas fa-coins"></i> Pricing Details</h4>
			<ul class="detail-list">
				<li><span>Vehicle Charge / Km</span><strong id="p_vehiclePerKm">-</strong></li>
				<li><span>Driver Charge / Km</span><strong id="p_driverPerKm">-</strong></li>
				<li><span>Vehicle Charge / Day</span><strong id="p_vehiclePerDay">-</strong></li>
				<li><span>Driver Charge / Day</span><strong id="p_driverPerDay">-</strong></li>
				<li><span>Minimum Km</span><strong id="p_minKm">-</strong></li>
				<li><span>Minimum Days</span><strong id="p_minDays">-</strong></li>
				<li><span>Pricing Created</span><strong id="p_createdAt">-</strong></li>
				<li><span>Pricing Updated</span><strong id="p_updatedAt">-</strong></li>
			</ul>
		</div>
	</div>

	<div class="vehicle-gallery">
		<h4><i class="fas fa-images"></i> Vehicle Photos</h4>
		<div id="vehiclePhotoGrid" class="photo-grid"></div>
	</div>
</div>
