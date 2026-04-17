(function(){
	if (window.ProfileInfoManager) {
		if (window.profileInfoManager) {
			delete window.profileInfoManager;
		}
		delete window.ProfileInfoManager;
	}

	class ProfileInfoManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.UP_ROOT = 'http://localhost/test/public/uploads';
			this.init();
		}

		init() {
			this.bindEvents();
		}

		bindEvents() {
			const searchBtn = document.getElementById('profileSearchBtn');
			const userIdInput = document.getElementById('profileUserIdInput');

			if (searchBtn) {
				searchBtn.addEventListener('click', () => this.searchUserProfile());
			}

			if (userIdInput) {
				userIdInput.addEventListener('keypress', (e) => {
					if (e.key === 'Enter') {
						this.searchUserProfile();
					}
				});
			}
		}

		async searchUserProfile() {
			const userIdInput = document.getElementById('profileUserIdInput');
			const userIdRaw = userIdInput ? userIdInput.value.trim() : '';
			const userId = parseInt(userIdRaw, 10);

			if (!userIdRaw || Number.isNaN(userId) || userId <= 0) {
				this.showAlert('Please enter a valid user ID', 'error');
				this.hideProfile();
				return;
			}

			this.setLoading(true);
			this.hideAlert();

			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getUserProfileInfo/${userId}`);
				const data = await response.json();

				if (!response.ok || !data.success) {
					this.showAlert(data.message || 'User not found', 'error');
					this.hideProfile();
					return;
				}

				this.renderProfile(data.user);
				this.showAlert('User profile loaded successfully', 'success');

			} catch (error) {
				this.showAlert('Failed to fetch user information', 'error');
				this.hideProfile();
			} finally {
				this.setLoading(false);
			}
		}

		renderProfile(user) {
			const profilePhoto = user.profile_photo ? `${this.UP_ROOT}${user.profile_photo}` : `${this.URL_ROOT}/public/img/default-avatar.png`;

			this.setText('profileFullname', user.fullname);
			this.setText('profileEmail', user.email);
			this.setText('profileAccountType', (user.account_type || '-').toUpperCase());
			this.setText('profileUserId', user.id);
			this.setText('profileRating', user.averageRating !== null && user.averageRating !== undefined ? Number(user.averageRating).toFixed(2) : '-');
			this.setText('profileReviewCount', user.reviewsCount || 0);

			const profilePhotoEl = document.getElementById('profilePhoto');
			if (profilePhotoEl) {
				profilePhotoEl.src = profilePhoto;
			}

			this.setText('d_fullname', user.fullname);
			this.setText('d_language', user.language);
			this.setText('d_dob', this.formatDate(user.dob));
			this.setText('d_gender', user.gender);
			this.setText('d_currency', user.currency_code);

			this.setText('d_phone', user.phone);
			this.setText('d_secondary_phone', user.secondary_phone);
			this.setText('d_email', user.email);
			this.setText('d_address', user.address);

			this.setText('d_bio', user.bio);
			this.setText('d_languages', user.languages);
			this.setText('d_insta', user.instaAccount);
			this.setText('d_facebook', user.facebookAccount);

			this.setText('d_dlVerified', this.yesNo(user.dlVerified));
			this.setText('d_tlSubmitted', this.yesNo(user.tlSubmitted));
			this.setText('d_tlVerified', this.yesNo(user.tlVerified));
			this.setText('d_tLicenseNumber', user.tLicenseNumber);
			this.setText('d_tLicenseExpiryDate', this.formatDate(user.tLicenseExpiryDate));
			this.setText('d_last_login', this.formatDateTime(user.last_login));
			this.setText('d_created_at', this.formatDateTime(user.created_at));
			this.setText('d_updated_at', this.formatDateTime(user.updated_at));

			this.renderReviews(user.reviews || []);
			this.renderAccountSpecificInfo(user);

			const container = document.getElementById('profileInfoContainer');
			if (container) {
				container.style.display = 'block';
			}
		}

		renderAccountSpecificInfo(user) {
			const driverSection = document.getElementById('driverVehiclesSection');
			const guideSection = document.getElementById('guideLocationsSection');

			if (driverSection) {
				driverSection.style.display = 'none';
			}

			if (guideSection) {
				guideSection.style.display = 'none';
			}

			if (user.account_type === 'driver') {
				this.renderDriverVehicles(user.driverVehicles || []);
				if (driverSection) {
					driverSection.style.display = 'block';
				}
			}

			if (user.account_type === 'guide') {
				this.renderGuideLocations(user.guideLocations || []);
				if (guideSection) {
					guideSection.style.display = 'block';
				}
			}
		}

		renderDriverVehicles(vehicles) {
			const listEl = document.getElementById('driverVehiclesList');
			if (!listEl) {
				return;
			}

			if (!vehicles || vehicles.length === 0) {
				listEl.innerHTML = '<p class="empty-reviews">No vehicle records found for this driver.</p>';
				return;
			}

			listEl.innerHTML = vehicles.map((vehicle) => {
				const title = `${vehicle.make || '-'} ${vehicle.model || '-'}`.trim();
				return `
					<div class="related-card">
						<h5>${this.escapeHtml(title)} (ID: ${this.escapeHtml(vehicle.vehicleId || '-')})</h5>
						<div class="mini-grid">
							<div><strong>Plate:</strong> ${this.escapeHtml(vehicle.licensePlate || '-')}</div>
							<div><strong>Year:</strong> ${this.escapeHtml(vehicle.year || '-')}</div>
							<div><strong>Seats:</strong> ${this.escapeHtml(vehicle.seatingCapacity || '-')}</div>
							<div><strong>Child Seats:</strong> ${this.escapeHtml(vehicle.childSeats || '-')}</div>
							<div><strong>Vehicle/Km:</strong> ${this.formatMoney(vehicle.vehicleChargePerKm)}</div>
							<div><strong>Driver/Km:</strong> ${this.formatMoney(vehicle.driverChargePerKm)}</div>
							<div><strong>Vehicle/Day:</strong> ${this.formatMoney(vehicle.vehicleChargePerDay)}</div>
							<div><strong>Driver/Day:</strong> ${this.formatMoney(vehicle.driverChargePerDay)}</div>
							<div><strong>Approved:</strong> ${this.yesNo(vehicle.isApproved)}</div>
							<div><strong>Availability:</strong> ${this.yesNo(vehicle.availability)}</div>
						</div>
					</div>
				`;
			}).join('');
		}

		renderGuideLocations(locations) {
			const listEl = document.getElementById('guideLocationsList');
			if (!listEl) {
				return;
			}

			if (!locations || locations.length === 0) {
				listEl.innerHTML = '<p class="empty-reviews">No guide location records found for this guide.</p>';
				return;
			}

			listEl.innerHTML = locations.map((location) => {
				const spotTitle = location.spotName || `Spot ID ${location.spotId || '-'}`;
				return `
					<div class="related-card">
						<h5>${this.escapeHtml(spotTitle)}</h5>
						<div class="mini-grid">
							<div><strong>Spot ID:</strong> ${this.escapeHtml(location.spotId || '-')}</div>
							<div><strong>Base Charge:</strong> ${this.formatMoney(location.baseCharge)}</div>
							<div><strong>Charge Type:</strong> ${this.escapeHtml(location.chargeType || '-')}</div>
							<div><strong>Group Size:</strong> ${this.escapeHtml(location.minGroupSize || '-')} - ${this.escapeHtml(location.maxGroupSize || '-')}</div>
							<div><strong>Active:</strong> ${this.yesNo(location.isActive)}</div>
							<div><strong>Updated:</strong> ${this.formatDateTime(location.updated_at)}</div>
						</div>
						<div class="review-meta" style="margin-top:6px;">${this.escapeHtml(location.description || '-')}</div>
					</div>
				`;
			}).join('');
		}

		renderReviews(reviews) {
			const reviewsEl = document.getElementById('profileReviewsList');
			if (!reviewsEl) {
				return;
			}

			if (!reviews || reviews.length === 0) {
				reviewsEl.innerHTML = '<p class="empty-reviews">No reviews found for this user.</p>';
				return;
			}

			reviewsEl.innerHTML = reviews.map((review) => {
				const rating = review.rating !== null && review.rating !== undefined ? Number(review.rating).toFixed(1) : '-';
				return `
					<div class="review-item">
						<div class="review-top">
							<div>
								<div class="reviewer">${this.escapeHtml(review.reviewerName || 'Traveller')}</div>
								<div class="review-meta">${this.escapeHtml(review.reviewerEmail || '-')}</div>
							</div>
							<div class="rating"><i class="fas fa-star"></i> ${rating}</div>
						</div>
						<p class="review-text">${this.escapeHtml(review.reviewText || '-')}</p>
						<div class="review-meta">${this.formatDateTime(review.createdAt)}</div>
					</div>
				`;
			}).join('');
		}

		setLoading(isLoading) {
			const btn = document.getElementById('profileSearchBtn');
			if (!btn) {
				return;
			}

			btn.disabled = isLoading;
			btn.innerHTML = isLoading
				? '<i class="fas fa-spinner fa-spin"></i> Searching...'
				: '<i class="fas fa-search"></i> Search';
		}

		setText(id, value) {
			const el = document.getElementById(id);
			if (!el) {
				return;
			}
			el.textContent = (value === null || value === undefined || value === '') ? '-' : String(value);
		}

		hideProfile() {
			const container = document.getElementById('profileInfoContainer');
			if (container) {
				container.style.display = 'none';
			}
		}

		showAlert(message, type) {
			const alertEl = document.getElementById('profileInfoAlert');
			if (!alertEl) {
				return;
			}
			alertEl.className = `profile-alert ${type}`;
			alertEl.textContent = message;
			alertEl.style.display = 'block';
		}

		hideAlert() {
			const alertEl = document.getElementById('profileInfoAlert');
			if (!alertEl) {
				return;
			}
			alertEl.style.display = 'none';
			alertEl.textContent = '';
			alertEl.className = 'profile-alert';
		}

		yesNo(value) {
			return Number(value) === 1 ? 'Yes' : 'No';
		}

		formatDate(value) {
			if (!value) {
				return '-';
			}
			const date = new Date(value);
			if (Number.isNaN(date.getTime())) {
				return String(value);
			}
			return date.toLocaleDateString();
		}

		formatDateTime(value) {
			if (!value) {
				return '-';
			}
			const date = new Date(value);
			if (Number.isNaN(date.getTime())) {
				return String(value);
			}
			return date.toLocaleString();
		}

		escapeHtml(str) {
			return String(str)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}

		formatMoney(value) {
			if (value === null || value === undefined || value === '') {
				return '-';
			}
			const num = Number(value);
			if (Number.isNaN(num)) {
				return this.escapeHtml(value);
			}
			return `LKR ${num.toFixed(2)}`;
		}
	}

	window.ProfileInfoManager = ProfileInfoManager;
	window.profileInfoManager = new ProfileInfoManager();

})();
