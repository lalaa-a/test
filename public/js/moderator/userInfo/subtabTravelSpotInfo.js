(function(){
	if (window.TravelSpotInfoManager) {
		if (window.travelSpotInfoManager) {
			delete window.travelSpotInfoManager;
		}
		delete window.TravelSpotInfoManager;
	}

	class TravelSpotInfoManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.UP_ROOT = 'http://localhost/test/public/uploads';
			this.allSpots = [];
			this.filteredSpots = [];
			this.currentSection = 'all';
			this.init();
		}

		init() {
			this.bindEvents();
			this.loadTravelSpots();
		}

		bindEvents() {
			// Search functionality
			const searchInput = document.getElementById('searchInput');
			if (searchInput) {
				searchInput.addEventListener('input', (e) => this.filterSpots(e.target.value));
			}

			// Section navigation
			const navLinks = document.querySelectorAll('.nav-link[data-section]');
			navLinks.forEach(link => {
				link.addEventListener('click', (e) => {
					e.preventDefault();
					this.switchSection(e.target.dataset.section);
				});
			});

			// Modal close
			const modal = document.getElementById('travelSpotModal');
			if (modal) {
				const closeBtn = modal.querySelector('.modal-close');
				if (closeBtn) {
					closeBtn.addEventListener('click', () => this.closeModal());
				}

				// Close modal when clicking outside
				modal.addEventListener('click', (e) => {
					if (e.target === modal) {
						this.closeModal();
					}
				});
			}
		}

		async loadTravelSpots() {
			this.setLoading(true);
			this.showAlert('Loading travel spots...', 'info');

			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getAllTravelSpotsForModerator`);
				const data = await response.json();

				if (!response.ok || !data.success) {
					this.showAlert(data.message || 'Failed to load travel spots', 'error');
					return;
				}

				this.allSpots = data.spots || [];
				this.updateStats(data.stats || {});
				this.renderSpotsTable(this.allSpots);
				this.hideAlert();

			} catch (error) {
				console.error('Error loading travel spots:', error);
				this.showAlert('Failed to load travel spots', 'error');
			} finally {
				this.setLoading(false);
			}
		}

		updateStats(stats) {
			const totalEl = document.getElementById('totalSpots');
			const guidesEl = document.getElementById('totalGuides');
			const activeEl = document.getElementById('activeSpots');
			const inactiveEl = document.getElementById('inactiveSpots');

			if (totalEl) totalEl.textContent = stats.total || 0;
			if (guidesEl) guidesEl.textContent = stats.totalGuides || 0;
			if (activeEl) activeEl.textContent = stats.active || 0;
			if (inactiveEl) inactiveEl.textContent = stats.inactive || 0;
		}

		switchSection(section) {
			this.currentSection = section;

			// Update navigation
			document.querySelectorAll('.nav-link').forEach(link => {
				link.classList.remove('active');
			});
			document.querySelector(`[data-section="${section}"]`).classList.add('active');

			// Filter and render
			let filtered = this.allSpots;
			switch (section) {
				case 'active':
					filtered = this.allSpots.filter(spot => spot.isActive == 1);
					break;
				case 'inactive':
					filtered = this.allSpots.filter(spot => spot.isActive == 0);
					break;
				default:
					filtered = this.allSpots;
			}

			this.filteredSpots = filtered;
			this.renderSpotsTable(filtered);
		}

		filterSpots(searchTerm) {
			let filtered = this.allSpots;

			// Apply section filter first
			switch (this.currentSection) {
				case 'active':
					filtered = filtered.filter(spot => spot.isActive == 1);
					break;
				case 'inactive':
					filtered = filtered.filter(spot => spot.isActive == 0);
					break;
			}

			// Apply search filter
			if (searchTerm.trim()) {
				const term = searchTerm.toLowerCase();
				filtered = filtered.filter(spot =>
					(spot.spotName && spot.spotName.toLowerCase().includes(term)) ||
					(spot.spotId && spot.spotId.toString().includes(term)) ||
					(spot.district && spot.district.toLowerCase().includes(term)) ||
					(spot.province && spot.province.toLowerCase().includes(term))
				);
			}

			this.filteredSpots = filtered;
			this.renderSpotsTable(filtered);
		}

		renderSpotsTable(spots) {
			const tbody = document.getElementById('spotsTableBody');
			if (!tbody) return;

			if (!spots || spots.length === 0) {
				tbody.innerHTML = `
					<tr>
						<td colspan="6" class="no-accounts">
							<i class="fas fa-map-marker-alt"></i>
							<p>No travel spots found</p>
						</td>
					</tr>
				`;
				return;
			}

			tbody.innerHTML = spots.map(spot => this.renderSpotRow(spot)).join('');
		}

		renderSpotRow(spot) {
			const statusClass = spot.isActive == 1 ? 'active' : 'inactive';
			const statusLabel = spot.isActive == 1 ? 'Active' : 'Inactive';
			const guideCount = spot.guideCount || 0;
			const avgRating = spot.averageRating ? parseFloat(spot.averageRating).toFixed(1) : '-';
			const totalReviews = spot.totalReviews || 0;

			return `
				<tr data-spot-id="${spot.spotId}">
					<td>
						<div class="profile-cell">
							<img src="${this.getSpotImage(spot.spotImage)}" alt="Spot" class="account-avatar-small" onerror="this.src='${this.URL_ROOT}/public/img/default-spot.png'">
						</div>
					</td>
					<td>
						<div>
							<strong>${this.escapeHtml(spot.spotName || '-')}</strong>
							<br>
							<small>ID: ${spot.spotId}</small>
						</div>
					</td>
					<td>${this.escapeHtml(spot.district || '-')}</td>
					<td>${this.escapeHtml(spot.province || '-')}</td>
					<td>
						<div>
							<span class="status-badge ${statusClass}">${statusLabel}</span>
							<br>
							<small>${guideCount} guide${guideCount !== 1 ? 's' : ''}</small>
						</div>
					</td>
					<td>
						<div>
							${avgRating !== '-' ? `<strong>${avgRating}★</strong><br>` : ''}
							<small>${totalReviews} review${totalReviews !== 1 ? 's' : ''}</small>
						</div>
					</td>
					<td>
						<button class="btn-view" onclick="window.travelSpotInfoManager.viewSpotDetails(${spot.spotId})">
							<i class="fas fa-eye"></i> View
						</button>
					</td>
				</tr>
			`;
		}

		async viewSpotDetails(spotId) {
			const spot = this.allSpots.find(s => s.spotId == spotId);
			if (!spot) return;

			this.setModalLoading(true);
			this.openModal();

			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getTravelSpotDetails?spotId=${spotId}`);
				const data = await response.json();

				if (!response.ok || !data.success) {
					this.showModalAlert(data.message || 'Failed to load spot details', 'error');
					return;
				}

				this.renderSpotModal(data.spot, data.guides || []);

			} catch (error) {
				console.error('Error loading spot details:', error);
				this.showModalAlert('Failed to load spot details', 'error');
			} finally {
				this.setModalLoading(false);
			}
		}

		renderSpotModal(spot, guides) {
			const modalBody = document.getElementById('modalBody');
			if (!modalBody) return;

			const statusClass = spot.isActive == 1 ? 'active' : 'inactive';
			const statusLabel = spot.isActive == 1 ? 'Active' : 'Inactive';
			const avgRating = spot.averageRating ? parseFloat(spot.averageRating).toFixed(1) : '-';
			const totalReviews = spot.totalReviews || 0;

			modalBody.innerHTML = `
				<div class="spot-details">
					<div class="spot-header">
						<img src="${this.getSpotImage(spot.spotImage)}" alt="Spot" class="spot-image" onerror="this.src='${this.URL_ROOT}/public/img/default-spot.png'">
						<div class="spot-info">
							<h3>${this.escapeHtml(spot.spotName || '-')}</h3>
							<p><strong>ID:</strong> ${spot.spotId}</p>
							<p><strong>Location:</strong> ${this.escapeHtml(spot.district || '-')} | ${this.escapeHtml(spot.province || '-')}</p>
							<p><strong>Status:</strong> <span class="status-badge ${statusClass}">${statusLabel}</span></p>
							<p><strong>Rating:</strong> ${avgRating !== '-' ? `${avgRating}★ (${totalReviews} reviews)` : 'No reviews yet'}</p>
							<p><strong>Description:</strong> ${this.escapeHtml(spot.description || 'No description available')}</p>
							<p><strong>Created:</strong> ${this.formatDateTime(spot.created_at)}</p>
							<p><strong>Updated:</strong> ${this.formatDateTime(spot.updated_at)}</p>
						</div>
					</div>

					<div class="guides-section">
						<h4>Associated Guides (${guides.length})</h4>
						${guides.length > 0 ? this.renderGuidesList(guides) : '<p>No guides associated with this travel spot.</p>'}
					</div>
				</div>
			`;
		}

		renderGuidesList(guides) {
			return `
				<div class="guides-list">
					${guides.map(guide => `
						<div class="guide-item">
							<img src="${guide.guideProfilePhoto ? `${this.UP_ROOT}${guide.guideProfilePhoto}` : `${this.URL_ROOT}/public/img/default-avatar.png`}"
								 alt="Guide" class="guide-avatar-small" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
							<div class="guide-info">
								<strong>${this.escapeHtml(guide.guideName || '-')}</strong>
								<p>ID: ${guide.guideId} | ${this.escapeHtml(guide.guideEmail || '-')}</p>
								<p>Language: ${this.escapeHtml(guide.guideLanguage || '-')} | Base Charge: LKR ${this.formatMoney(guide.baseCharge)}</p>
								<p>Status: <span class="status-badge ${guide.isActive == 1 ? 'active' : 'inactive'}">${guide.isActive == 1 ? 'Active' : 'Inactive'}</span></p>
							</div>
						</div>
					`).join('')}
				</div>
			`;
		}

		openModal() {
			const modal = document.getElementById('travelSpotModal');
			if (modal) {
				modal.style.display = 'block';
				document.body.style.overflow = 'hidden';
			}
		}

		closeModal() {
			const modal = document.getElementById('travelSpotModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = 'auto';
			}
		}

		setLoading(isLoading) {
			// Could add loading indicators if needed
		}

		setModalLoading(isLoading) {
			const modalBody = document.getElementById('modalBody');
			if (!modalBody) return;

			if (isLoading) {
				modalBody.innerHTML = `
					<div class="loading">
						<i class="fas fa-spinner fa-spin"></i>
						<p>Loading spot details...</p>
					</div>
				`;
			}
		}

		showAlert(message, type) {
			// Could implement alert system if needed
		}

		showModalAlert(message, type) {
			const modalBody = document.getElementById('modalBody');
			if (!modalBody) return;

			modalBody.innerHTML = `
				<div class="alert alert-${type}">
					<i class="fas fa-exclamation-triangle"></i>
					<p>${message}</p>
				</div>
			`;
		}

		getSpotImage(imagePath) {
			if (!imagePath) return `${this.URL_ROOT}/public/img/default-spot.png`;
			return imagePath.startsWith('http') ? imagePath : `${this.UP_ROOT}${imagePath}`;
		}

		formatMoney(value) {
			const num = Number(value);
			if (Number.isNaN(num)) return '0.00';
			return num.toFixed(2);
		}

		formatDateTime(value) {
			if (!value) return '-';
			const date = new Date(value);
			if (Number.isNaN(date.getTime())) return String(value);
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
	}

	const initializeTravelSpotInfoManager = function() {
		window.TravelSpotInfoManager = TravelSpotInfoManager;
		window.travelSpotInfoManager = new TravelSpotInfoManager();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeTravelSpotInfoManager);
	} else {
		initializeTravelSpotInfoManager();
	}
})();