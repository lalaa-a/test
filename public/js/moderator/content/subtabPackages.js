(function() {

	if (window.travelPackageManager && typeof window.travelPackageManager.destroy === 'function') {
		window.travelPackageManager.destroy();
		delete window.travelPackageManager;
	}

	class TravelPackageManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.UP_ROOT = 'http://localhost/test/public/uploads';
			this.selectedSpots = [];
			this.uploadedPhotos = [];
			this.travelPackageCardData = [];

			this.initializeElements();
			this.attachEventListeners();
			this.loadTravelPackageCards();
		}

		initializeElements() {
			this.popup = document.getElementById('package-popup');
			this.popupClose = document.getElementById('package-popup-close');
			this.cancelBtn = document.getElementById('cancel-package-btn');
			this.submitBtn = document.getElementById('submit-package-btn');
			this.addPackageBtn = document.getElementById('add-package-btn');

			this.form = document.getElementById('package-form');

			this.spotSearchInput = document.getElementById('package-spot-search');
			this.spotSearchBtn = document.getElementById('search-package-spot-btn');
			this.spotResults = document.getElementById('package-spot-results');
			this.selectedSpotsContainer = document.getElementById('selected-package-spots');

			this.packageSearchInput = document.getElementById('package-search-input');
			this.packageSearchBtn = document.getElementById('package-search-btn');
			this.searchResultsInfo = document.getElementById('package-search-results-info');

			this.packageCardsGrid = document.getElementById('packages-card-grid');
			this.emptyState = document.getElementById('packages-empty-state');

			this.photoInputs = [];
			this.photoPreviews = [];
			for (let i = 1; i <= 6; i++) {
				this.photoInputs.push(document.getElementById(`packagePhotoUpload${i}`));
				this.photoPreviews.push(document.getElementById(`packageUploadPreview${i}`));
			}
		}

		attachEventListeners() {
			if (this.addPackageBtn) {
				this.addPackageBtn.addEventListener('click', () => this.openPopup());
			}

			if (this.popupClose) {
				this.popupClose.addEventListener('click', () => this.closePopup());
			}

			if (this.cancelBtn) {
				this.cancelBtn.addEventListener('click', () => this.closePopup());
			}

			if (this.popup) {
				this.popup.addEventListener('click', (e) => {
					if (e.target === this.popup) {
						this.closePopup();
					}
				});
			}

			if (this.form) {
				this.form.addEventListener('submit', (event) => this.handleSubmit(event));
			}

			if (this.spotSearchBtn) {
				this.spotSearchBtn.addEventListener('click', () => this.searchSpots());
			}

			if (this.spotSearchInput) {
				this.spotSearchInput.addEventListener('keypress', (event) => {
					if (event.key === 'Enter') {
						event.preventDefault();
						this.searchSpots();
					}
				});
			}

			document.addEventListener('click', (event) => {
				const clickedInsideSpotSearch = this.spotSearchInput && this.spotSearchInput.contains(event.target);
				const clickedInsideSpotResults = this.spotResults && this.spotResults.contains(event.target);
				const clickedSearchButton = event.target.closest('#search-package-spot-btn');

				if (!clickedInsideSpotSearch && !clickedInsideSpotResults && !clickedSearchButton && this.spotResults) {
					this.spotResults.innerHTML = '';
				}
			});

			if (this.packageSearchBtn) {
				this.packageSearchBtn.addEventListener('click', () => this.filterPackageCards());
			}

			if (this.packageSearchInput) {
				this.packageSearchInput.addEventListener('input', () => this.filterPackageCards());
				this.packageSearchInput.addEventListener('keypress', (event) => {
					if (event.key === 'Enter') {
						event.preventDefault();
						this.filterPackageCards();
					}
				});
			}

			const uploadButtons = document.querySelectorAll('.btn-upload-photo');
			uploadButtons.forEach((button) => {
				button.addEventListener('click', () => {
					const target = button.getAttribute('data-target');
					const inputElement = document.getElementById(target);
					if (inputElement) {
						inputElement.click();
					}
				});
			});

			this.photoInputs.forEach((input, index) => {
				if (!input) {
					return;
				}

				input.addEventListener('change', (event) => this.handlePhotoUpload(event, index));
			});

			const removeButtons = document.querySelectorAll('.btn-remove-photo');
			removeButtons.forEach((button) => {
				button.addEventListener('click', () => {
					const slot = parseInt(button.getAttribute('data-slot'), 10);
					this.removePhoto(slot);
				});
			});
		}

		destroy() {
			if (this.popup) {
				this.popup.classList.remove('show');
			}
			document.body.style.overflow = '';
		}

		openPopup() {
			if (!this.popup) {
				return;
			}

			this.popup.classList.add('show');
			document.body.style.overflow = 'hidden';
		}

		closePopup() {
			if (!this.popup) {
				return;
			}

			this.popup.classList.remove('show');
			document.body.style.overflow = '';
			this.resetForm();
		}

		resetForm() {
			if (this.form) {
				this.form.reset();
			}

			this.selectedSpots = [];
			this.uploadedPhotos = [];

			this.renderSelectedSpots();

			if (this.spotResults) {
				this.spotResults.innerHTML = '';
			}

			this.photoPreviews.forEach((preview, index) => {
				if (!preview) {
					return;
				}

				const slot = index + 1;
				preview.innerHTML = `
					<div class="upload-placeholder">
						<i class="fas fa-image"></i>
						<p>${slot === 1 ? 'Cover Photo' : `Photo ${slot}`}</p>
						${slot === 1 ? '<span class="upload-hint">Required</span>' : ''}
					</div>
				`;
			});

			this.photoInputs.forEach((input) => {
				if (input) {
					input.value = '';
				}
			});

			const removeButtons = document.querySelectorAll('.btn-remove-photo');
			removeButtons.forEach((button) => {
				button.style.display = 'none';
			});
		}

		async searchSpots() {
			const query = this.spotSearchInput ? this.spotSearchInput.value.trim() : '';
			if (!query) {
				this.notify('Please enter a spot name to search', 'warning');
				return;
			}

			try {
				const response = await fetch(`${this.URL_ROOT}/Moderator/searchTravelSpotsForPackage`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({ name: query })
				});

				const data = await response.json();
				if (!data.success) {
					this.notify(data.message || 'Failed to search travel spots', 'error');
					return;
				}

				this.displaySpotResults(data.travelSpots || []);

			} catch (error) {
				console.error('Error searching travel spots for package:', error);
				this.notify('Failed to search travel spots', 'error');
			}
		}

		displaySpotResults(results) {
			if (!this.spotResults) {
				return;
			}

			this.spotResults.innerHTML = '';

			const filteredResults = results.filter((spot) => {
				return !this.selectedSpots.some((selectedSpot) => Number(selectedSpot.spotId) === Number(spot.spotId));
			});

			if (filteredResults.length === 0) {
				this.spotResults.innerHTML = '<div class="nearby-result-item">No spots found</div>';
				return;
			}

			filteredResults.forEach((spot) => {
				const item = document.createElement('div');
				item.className = 'nearby-result-item';

				const summaryText = `${spot.province || ''}${spot.province && spot.district ? ', ' : ''}${spot.district || ''}`;
				item.innerHTML = `
					<div class="spot-info">
						<strong>${this.escapeHtml(spot.spotName || '')}</strong>
						<span>${this.escapeHtml(summaryText)}</span>
					</div>
				`;

				const addButton = document.createElement('button');
				addButton.type = 'button';
				addButton.className = 'add-spot-btn';
				addButton.innerHTML = '<i class="fas fa-plus"></i>';
				addButton.addEventListener('click', () => {
					this.addSpotToPackage({
						spotId: Number(spot.spotId),
						spotName: String(spot.spotName || ''),
						province: String(spot.province || ''),
						district: String(spot.district || '')
					});
				});

				item.appendChild(addButton);
				this.spotResults.appendChild(item);
			});
		}

		addSpotToPackage(spot) {
			if (!spot || !spot.spotId) {
				return;
			}

			const alreadySelected = this.selectedSpots.some((selectedSpot) => Number(selectedSpot.spotId) === Number(spot.spotId));
			if (alreadySelected) {
				this.notify('This travel spot is already selected', 'warning');
				return;
			}

			const nextOrder = this.selectedSpots.length + 1;
			this.selectedSpots.push({
				spotId: Number(spot.spotId),
				spotName: spot.spotName,
				province: spot.province,
				district: spot.district,
				dayNumber: 1,
				visitOrder: nextOrder,
				spotNote: ''
			});

			this.renderSelectedSpots();
			this.notify('Spot added to package', 'success');
		}

		removeSelectedSpot(spotId) {
			this.selectedSpots = this.selectedSpots.filter((spot) => Number(spot.spotId) !== Number(spotId));

			this.selectedSpots = this.selectedSpots.map((spot, index) => {
				return {
					...spot,
					visitOrder: index + 1
				};
			});

			this.renderSelectedSpots();
		}

		renderSelectedSpots() {
			if (!this.selectedSpotsContainer) {
				return;
			}

			this.selectedSpotsContainer.innerHTML = '';

			if (this.selectedSpots.length === 0) {
				this.selectedSpotsContainer.innerHTML = '<div class="selected-spots-empty">No spots selected yet</div>';
				return;
			}

			this.selectedSpots.forEach((spot, index) => {
				const card = document.createElement('div');
				card.className = 'selected-package-spot';

				card.innerHTML = `
					<div class="selected-spot-head">
						<div>
							<h5>${this.escapeHtml(spot.spotName)}</h5>
							<p>${this.escapeHtml(`${spot.province}${spot.province && spot.district ? ', ' : ''}${spot.district}`)}</p>
						</div>
						<button type="button" class="remove-spot-btn" aria-label="Remove spot">
							<i class="fas fa-times"></i>
						</button>
					</div>

					<div class="selected-spot-controls">
						<label>
							Day
							<input type="number" min="1" value="${Number(spot.dayNumber)}" class="selected-spot-day">
						</label>
						<label>
							Order
							<input type="number" min="1" value="${Number(spot.visitOrder)}" class="selected-spot-order">
						</label>
					</div>

					<label class="spot-note-wrap">
						Spot Note
						<textarea rows="2" class="selected-spot-note" placeholder="Optional note for this stop">${this.escapeHtml(spot.spotNote || '')}</textarea>
					</label>
				`;

				const removeButton = card.querySelector('.remove-spot-btn');
				removeButton.addEventListener('click', () => this.removeSelectedSpot(spot.spotId));

				const dayInput = card.querySelector('.selected-spot-day');
				dayInput.addEventListener('input', (event) => {
					const dayValue = Math.max(1, parseInt(event.target.value || '1', 10));
					this.selectedSpots[index].dayNumber = dayValue;
				});

				const orderInput = card.querySelector('.selected-spot-order');
				orderInput.addEventListener('input', (event) => {
					const orderValue = Math.max(1, parseInt(event.target.value || '1', 10));
					this.selectedSpots[index].visitOrder = orderValue;
				});

				const noteInput = card.querySelector('.selected-spot-note');
				noteInput.addEventListener('input', (event) => {
					this.selectedSpots[index].spotNote = event.target.value;
				});

				this.selectedSpotsContainer.appendChild(card);
			});
		}

		handlePhotoUpload(event, index) {
			const file = event.target.files[0];
			if (!file) {
				return;
			}

			const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
			if (!allowedTypes.includes(file.type)) {
				this.notify('Please select JPEG, JPG, PNG, or WEBP images', 'error');
				event.target.value = '';
				return;
			}

			if (file.size > (5 * 1024 * 1024)) {
				this.notify('Image must be less than 5MB', 'error');
				event.target.value = '';
				return;
			}

			this.uploadedPhotos[index] = file;

			const reader = new FileReader();
			reader.onload = (readerEvent) => {
				const preview = this.photoPreviews[index];
				if (!preview) {
					return;
				}

				preview.innerHTML = `<img src="${readerEvent.target.result}" alt="Package Photo ${index + 1}" style="width: 100%; height: 100%; object-fit: cover;">`;

				const removeButton = document.querySelector(`.btn-remove-photo[data-slot="${index + 1}"]`);
				if (removeButton) {
					removeButton.style.display = 'flex';
				}
			};
			reader.readAsDataURL(file);
		}

		removePhoto(slot) {
			const index = slot - 1;

			if (this.photoInputs[index]) {
				this.photoInputs[index].value = '';
			}

			this.uploadedPhotos[index] = null;

			const preview = this.photoPreviews[index];
			if (preview) {
				preview.innerHTML = `
					<div class="upload-placeholder">
						<i class="fas fa-image"></i>
						<p>${slot === 1 ? 'Cover Photo' : `Photo ${slot}`}</p>
						${slot === 1 ? '<span class="upload-hint">Required</span>' : ''}
					</div>
				`;
			}

			const removeButton = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
			if (removeButton) {
				removeButton.style.display = 'none';
			}
		}

		async handleSubmit(event) {
			event.preventDefault();

			if (this.selectedSpots.length === 0) {
				this.notify('Please add at least one travel spot to the package', 'warning');
				return;
			}

			if (!this.uploadedPhotos[0]) {
				this.notify('Cover photo is required', 'warning');
				return;
			}

			const formData = new FormData(this.form);
			formData.set('selectedSpots', JSON.stringify(this.selectedSpots));

			this.uploadedPhotos.forEach((file, index) => {
				if (file) {
					formData.append(`packagePhoto${index + 1}`, file);
				}
			});

			if (this.submitBtn) {
				this.submitBtn.disabled = true;
				this.submitBtn.textContent = 'Saving...';
			}

			try {
				const response = await fetch(`${this.URL_ROOT}/Moderator/addTravelPackage`, {
					method: 'POST',
					body: formData
				});

				const result = await response.json();

				if (!response.ok || !result.success) {
					this.notify(result.message || 'Failed to save package', 'error');
					return;
				}

				this.notify(result.message || 'Package saved successfully', 'success');
				this.closePopup();
				await this.loadTravelPackageCards();

			} catch (error) {
				console.error('Error submitting package form:', error);
				this.notify('Failed to save package', 'error');

			} finally {
				if (this.submitBtn) {
					this.submitBtn.disabled = false;
					this.submitBtn.textContent = 'Save Package';
				}
			}
		}

		async loadTravelPackageCards() {
			try {
				const response = await fetch(`${this.URL_ROOT}/Moderator/getTravelPackageCardData`);
				const data = await response.json();

				if (!data.success) {
					this.notify(data.message || 'Failed to load packages', 'error');
					return;
				}

				this.travelPackageCardData = Array.isArray(data.travelPackageCardData)
					? data.travelPackageCardData
					: [];

				this.filterPackageCards();

			} catch (error) {
				console.error('Error loading package cards:', error);
				this.notify('Failed to load packages', 'error');
			}
		}

		filterPackageCards() {
			const query = this.packageSearchInput
				? this.packageSearchInput.value.trim().toLowerCase()
				: '';

			const filteredCards = this.travelPackageCardData.filter((packageItem) => {
				if (!query) {
					return true;
				}

				const searchableText = [
					packageItem.packageName,
					packageItem.overview,
					packageItem.packageDetails,
					packageItem.spotNames
				]
					.filter(Boolean)
					.join(' ')
					.toLowerCase();

				return searchableText.includes(query);
			});

			this.renderPackageCards(filteredCards);
			this.updateSearchInfo(filteredCards.length, this.travelPackageCardData.length, query);
		}

		updateSearchInfo(filteredCount, totalCount, query) {
			if (!this.searchResultsInfo) {
				return;
			}

			if (!query) {
				this.searchResultsInfo.style.display = 'none';
				this.searchResultsInfo.textContent = '';
				return;
			}

			this.searchResultsInfo.style.display = 'block';
			this.searchResultsInfo.textContent = `${filteredCount} of ${totalCount} packages match "${query}"`;
		}

		renderPackageCards(packageCards) {
			if (!this.packageCardsGrid) {
				return;
			}

			this.packageCardsGrid.innerHTML = '';

			if (!packageCards || packageCards.length === 0) {
				if (this.emptyState) {
					this.emptyState.style.display = 'block';
				}
				return;
			}

			if (this.emptyState) {
				this.emptyState.style.display = 'none';
			}

			packageCards.forEach((packageItem) => {
				const card = document.createElement('article');
				card.className = 'package-card';

				const coverPath = packageItem.coverPhotoPath
					? `${this.UP_ROOT}${packageItem.coverPhotoPath}`
					: '';

				const price = packageItem.estimatedPriceLkr !== null && packageItem.estimatedPriceLkr !== ''
					? `LKR ${Number(packageItem.estimatedPriceLkr).toLocaleString()}`
					: 'Price not set';

				const status = (packageItem.status || 'inactive').toLowerCase();
				const statusLabel = status === 'active' ? 'Active' : 'Inactive';

				card.innerHTML = `
					<div class="package-card-image-wrap">
						${coverPath
							? `<img src="${coverPath}" alt="${this.escapeHtml(packageItem.packageName || '')}" class="package-card-image">`
							: '<div class="package-card-image placeholder"><i class="fas fa-image"></i></div>'
						}
						<span class="package-status ${status}">${statusLabel}</span>
					</div>

					<div class="package-card-content">
						<h3 class="package-card-title">${this.escapeHtml(packageItem.packageName || '')}</h3>
						<p class="package-card-overview">${this.escapeHtml(packageItem.overview || '')}</p>

						<div class="package-card-meta">
							<span><i class="fas fa-calendar-day"></i> ${Number(packageItem.durationDays) || 1} days</span>
							<span><i class="fas fa-map-marker-alt"></i> ${Number(packageItem.spotCount) || 0} spots</span>
						</div>

						<div class="package-card-footer">
							<strong class="package-card-price">${price}</strong>
						</div>

						${packageItem.spotNames
							? `<p class="package-spot-list"><i class="fas fa-route"></i> ${this.escapeHtml(packageItem.spotNames)}</p>`
							: ''
						}
					</div>
				`;

				this.packageCardsGrid.appendChild(card);
			});
		}

		escapeHtml(value) {
			const text = String(value || '');
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		notify(message, type) {
			if (typeof window.showNotification === 'function') {
				window.showNotification(message, type || 'info');
				return;
			}

			console.log(`[${type || 'info'}] ${message}`);
		}
	}

	window.travelPackageManager = new TravelPackageManager();

})();
