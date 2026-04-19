(function () {
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
			this.init();
		}

		init() {
			this.bindEvents();
		}

		bindEvents() {
			const searchBtn = document.getElementById('travelSpotSearchBtn');
			const searchInput = document.getElementById('travelSpotSearchInput');

			if (searchBtn) {
				searchBtn.addEventListener('click', () => this.searchTravelSpots());
			}

			if (searchInput) {
				searchInput.addEventListener('keypress', (event) => {
					if (event.key === 'Enter') {
						event.preventDefault();
						this.searchTravelSpots();
					}
				});
			}
		}

		async searchTravelSpots() {
			const searchInput = document.getElementById('travelSpotSearchInput');
			const searchValue = searchInput ? searchInput.value.trim() : '';

			if (!searchValue) {
				this.showAlert('Please enter a travel spot ID or travel spot name', 'error');
				this.hideResults();
				return;
			}

			this.setLoading(true);
			this.hideAlert();

			try {
				const response = await fetch(
					`${this.URL_ROOT}/moderator/getTravelSpotGuides?search=${encodeURIComponent(searchValue)}`,
					{
						method: 'GET',
						headers: {
							Accept: 'application/json'
						}
					}
				);

				const data = await this.parseJsonResponse(response);

				if (!response.ok || !data.success) {
					this.showAlert(data.message || 'Failed to load travel spot guides', 'error');
					this.hideResults();
					return;
				}

				const results = Array.isArray(data.results) ? data.results : [];
				this.renderResults(results, data.count);

				if (results.length === 0) {
					this.showAlert('No guides found for this travel spot search', 'info');
				} else {
					this.showAlert(
						`Found ${results.length} guide${results.length === 1 ? '' : 's'} for "${searchValue}"`,
						'success'
					);
				}
			} catch (error) {
				console.error('Error loading travel spots:', error);
				this.showAlert(error.message || 'Failed to fetch travel spot guides', 'error');
				this.hideResults();
			} finally {
				this.setLoading(false);
			}
		}

		async parseJsonResponse(response) {
			const raw = await response.text();

			if (!raw) {
				return {};
			}

			try {
				return JSON.parse(raw);
			} catch (error) {
				const preview = raw.replace(/\s+/g, ' ').trim().slice(0, 180);
				throw new Error(`Server returned invalid JSON response: ${preview}`);
			}
		}

		renderResults(results, count) {
			const resultsContainer = document.getElementById('travelSpotResultsContainer');
			const resultsList = document.getElementById('travelSpotResultsList');
			const resultCount = document.getElementById('travelSpotResultCount');

			if (!resultsContainer || !resultsList || !resultCount) {
				return;
			}

			const groupedSpots = this.groupBySpot(results);
			const totalGuides = Number.isFinite(count) ? count : results.length;
			const totalSpots = groupedSpots.length;

			resultCount.textContent = `${totalGuides} guide${totalGuides === 1 ? '' : 's'} found across ${totalSpots} travel spot${totalSpots === 1 ? '' : 's'}`;

			if (groupedSpots.length === 0) {
				resultsList.innerHTML = '<p class="empty-results">No matching travel spots or guides found.</p>';
				resultsContainer.style.display = 'block';
				return;
			}

			resultsList.innerHTML = groupedSpots.map((spot) => this.renderSpotGroup(spot)).join('');
			resultsContainer.style.display = 'block';
		}

		groupBySpot(results) {
			const grouped = new Map();

			results.forEach((entry) => {
				const spotId = entry.spotId || 'unknown';

				if (!grouped.has(spotId)) {
					grouped.set(spotId, {
						spotId: entry.spotId || '-',
						spotName: entry.spotName || `Spot ${entry.spotId || '-'}`,
						overview: entry.overview || '',
						province: entry.province || '-',
						district: entry.district || '-',
						averageRating: entry.averageRating,
						totalReviews: entry.totalReviews,
						guides: []
					});
				}

				grouped.get(spotId).guides.push(entry);
			});

			return Array.from(grouped.values());
		}

		renderSpotGroup(spot) {
			const averageRating = this.numberOrDash(spot.averageRating);
			const totalReviews = this.numberOrDash(spot.totalReviews);
			const location = `${this.escapeHtml(spot.district)} | ${this.escapeHtml(spot.province)}`;
			const safeOverview = this.escapeHtml(spot.overview || 'No overview available.');

			return `
				<article class="spot-group">
					<header class="spot-group-header">
						<h3>${this.escapeHtml(spot.spotName)}</h3>
						<p class="spot-meta">Spot ID: ${this.escapeHtml(spot.spotId)} | Location: ${location}</p>
						<p class="spot-meta spot-overview">${safeOverview}</p>
						<p class="spot-meta">Average Rating: ${averageRating} | Total Reviews: ${totalReviews}</p>
					</header>
					<div class="guides-grid">
						${spot.guides.map((guide) => this.renderGuideCard(guide)).join('')}
					</div>
				</article>
			`;
		}

		renderGuideCard(guide) {
			const guideImage = guide.guideProfilePhoto
				? `${this.UP_ROOT}${guide.guideProfilePhoto}`
				: `${this.URL_ROOT}/public/img/default-avatar.png`;

			const isActive = Number(guide.isActive) === 1;
			const activeClass = isActive ? 'active-yes' : 'active-no';
			const activeLabel = isActive ? 'Active' : 'Inactive';

			return `
				<div class="guide-card">
					<div class="guide-top">
						<img src="${guideImage}" alt="Guide" class="guide-avatar" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
						<div>
							<p class="guide-name">${this.escapeHtml(guide.guideName || '-')}</p>
							<p class="guide-contact">${this.escapeHtml(guide.guideEmail || '-')}</p>
						</div>
					</div>
					<div class="guide-info">
						<div><strong>Guide ID:</strong> ${this.escapeHtml(guide.guideId || '-')}</div>
						<div><strong>Phone:</strong> ${this.escapeHtml(guide.guidePhone || '-')}</div>
						<div><strong>Secondary Phone:</strong> ${this.escapeHtml(guide.guideSecondaryPhone || '-')}</div>
						<div><strong>Language:</strong> ${this.escapeHtml(guide.guideLanguage || '-')}</div>
						<div><strong>Base Charge:</strong> ${this.formatMoney(guide.baseCharge)}</div>
						<div><strong>Charge Type:</strong> ${this.escapeHtml(guide.chargeType || '-')}</div>
						<div><strong>Group Size:</strong> ${this.numberOrDash(guide.minGroupSize)} - ${this.numberOrDash(guide.maxGroupSize)}</div>
						<div><strong>Status:</strong> <span class="${activeClass}">${activeLabel}</span></div>
					</div>
				</div>
			`;
		}

		hideResults() {
			const resultsContainer = document.getElementById('travelSpotResultsContainer');
			const resultsList = document.getElementById('travelSpotResultsList');
			const resultCount = document.getElementById('travelSpotResultCount');

			if (resultsContainer) {
				resultsContainer.style.display = 'none';
			}

			if (resultsList) {
				resultsList.innerHTML = '<p class="empty-results">Search to load guide information.</p>';
			}

			if (resultCount) {
				resultCount.textContent = '0 guides found';
			}
		}

		setLoading(isLoading) {
			const searchBtn = document.getElementById('travelSpotSearchBtn');

			if (!searchBtn) {
				return;
			}

			searchBtn.disabled = isLoading;
			searchBtn.innerHTML = isLoading
				? '<i class="fas fa-spinner fa-spin"></i> Searching...'
				: '<i class="fas fa-search"></i> Search';
		}

		showAlert(message, type) {
			const alertEl = document.getElementById('travelSpotInfoAlert');
			if (!alertEl) {
				return;
			}

			const normalizedType = ['error', 'success', 'info'].includes(type) ? type : 'info';
			alertEl.className = `spot-alert ${normalizedType}`;
			alertEl.textContent = message;
			alertEl.style.display = 'block';
		}

		hideAlert() {
			const alertEl = document.getElementById('travelSpotInfoAlert');
			if (!alertEl) {
				return;
			}

			alertEl.className = 'spot-alert';
			alertEl.textContent = '';
			alertEl.style.display = 'none';
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

		numberOrDash(value) {
			if (value === null || value === undefined || value === '') {
				return '-';
			}
			return this.escapeHtml(String(value));
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

	const initializeTravelSpotInfoManager = function () {
		window.TravelSpotInfoManager = TravelSpotInfoManager;
		window.travelSpotInfoManager = new TravelSpotInfoManager();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeTravelSpotInfoManager);
	} else {
		initializeTravelSpotInfoManager();
	}
})();