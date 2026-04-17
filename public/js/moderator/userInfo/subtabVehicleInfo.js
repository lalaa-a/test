(function(){
	if (window.VehicleInfoManager) {
		if (window.vehicleInfoManager) {
			delete window.vehicleInfoManager;
		}
		delete window.VehicleInfoManager;
	}

	class VehicleInfoManager {
		constructor() {
			this.URL_ROOT = 'http://localhost/test';
			this.UP_ROOT = 'http://localhost/test/public/uploads';
			this.init();
		}

		init() {
			this.bindEvents();
		}

		bindEvents() {
			const searchBtn = document.getElementById('vehicleSearchBtn');
			const vehicleIdInput = document.getElementById('vehicleIdInput');

			if (searchBtn) {
				searchBtn.addEventListener('click', () => this.searchVehicle());
			}

			if (vehicleIdInput) {
				vehicleIdInput.addEventListener('keypress', (e) => {
					if (e.key === 'Enter') {
						this.searchVehicle();
					}
				});
			}
		}

		async searchVehicle() {
			const vehicleIdInput = document.getElementById('vehicleIdInput');
			const vehicleIdRaw = vehicleIdInput ? vehicleIdInput.value.trim() : '';
			const vehicleId = parseInt(vehicleIdRaw, 10);

			if (!vehicleIdRaw || Number.isNaN(vehicleId) || vehicleId <= 0) {
				this.showAlert('Please enter a valid vehicle ID', 'error');
				this.hideVehicleInfo();
				return;
			}

			this.setLoading(true);
			this.hideAlert();

			try {
				const response = await fetch(`${this.URL_ROOT}/moderator/getVehicleInfo/${vehicleId}`);
				const data = await response.json();

				if (!response.ok || !data.success) {
					this.showAlert(data.message || 'Vehicle not found', 'error');
					this.hideVehicleInfo();
					return;
				}

				this.renderVehicleInfo(data.vehicle);
				this.showAlert('Vehicle details loaded successfully', 'success');

			} catch (error) {
				this.showAlert('Failed to fetch vehicle information', 'error');
				this.hideVehicleInfo();
			} finally {
				this.setLoading(false);
			}
		}

		renderVehicleInfo(vehicle) {
			const mainPhoto = vehicle.frontViewPhoto ? `${this.UP_ROOT}${vehicle.frontViewPhoto}` : `${this.URL_ROOT}/public/img/default-vehicle.jpg`;
			const ownerPhoto = vehicle.ownerPhoto ? `${this.UP_ROOT}${vehicle.ownerPhoto}` : `${this.URL_ROOT}/public/img/default-avatar.png`;

			this.setText('v_title', `${vehicle.make || '-'} ${vehicle.model || '-'}`.trim());
			this.setText('v_plate', vehicle.licensePlate);
			this.setText('v_status_badge', this.verificationLabel(vehicle.verificationStatus));
			this.setText('v_vehicleId', vehicle.vehicleId);
			this.setText('v_ownerId', vehicle.ownerId);
			this.setText('v_verification', this.verificationLabel(vehicle.verificationStatus));

			this.setImage('vehicleMainPhoto', mainPhoto);
			this.setImage('ownerPhoto', ownerPhoto);

			this.setText('d_make', vehicle.make);
			this.setText('d_model', vehicle.model);
			this.setText('d_year', vehicle.year);
			this.setText('d_color', vehicle.color);
			this.setText('d_licensePlate', vehicle.licensePlate);
			this.setText('d_seatingCapacity', vehicle.seatingCapacity);
			this.setText('d_childSeats', vehicle.childSeats);
			this.setText('d_fuelEfficiency', this.numberOrDash(vehicle.fuelEfficiency));
			this.setText('d_description', vehicle.description);
			this.setText('d_status', this.yesNo(vehicle.status));
			this.setText('d_availability', this.yesNo(vehicle.availability));
			this.setText('d_isApproved', this.yesNo(vehicle.isApproved));

			this.setText('o_name', vehicle.ownerName);
			this.setText('o_type', vehicle.ownerAccountType);
			this.setText('o_email', vehicle.ownerEmail);
			this.setText('o_phone', vehicle.ownerPhone);
			this.setText('o_secondary', vehicle.ownerSecondaryPhone);
			this.setText('o_address', vehicle.ownerAddress);
			this.setText('o_last_login', this.formatDateTime(vehicle.ownerLastLogin));

			this.setText('ver_status', this.verificationLabel(vehicle.verificationStatus));
			this.setText('ver_reviewer', vehicle.reviewerName || vehicle.reviewedBy || '-');
			this.setText('ver_reviewedAt', this.formatDateTime(vehicle.reviewedAt));
			this.setText('ver_expiryDate', this.formatDate(vehicle.expiryDate));
			this.setText('ver_rejectionReason', vehicle.rejectionReason);

			this.setText('p_vehiclePerKm', this.currencyOrDash(vehicle.vehicleChargePerKm));
			this.setText('p_driverPerKm', this.currencyOrDash(vehicle.driverChargePerKm));
			this.setText('p_vehiclePerDay', this.currencyOrDash(vehicle.vehicleChargePerDay));
			this.setText('p_driverPerDay', this.currencyOrDash(vehicle.driverChargePerDay));
			this.setText('p_minKm', this.numberOrDash(vehicle.minimumKm));
			this.setText('p_minDays', this.numberOrDash(vehicle.minimumDays));
			this.setText('p_createdAt', this.formatDateTime(vehicle.pricingCreatedAt));
			this.setText('p_updatedAt', this.formatDateTime(vehicle.pricingUpdatedAt));

			this.renderPhotoGallery(vehicle);

			const container = document.getElementById('vehicleInfoContainer');
			if (container) {
				container.style.display = 'block';
			}
		}

		renderPhotoGallery(vehicle) {
			const grid = document.getElementById('vehiclePhotoGrid');
			if (!grid) {
				return;
			}

			const photoEntries = [
				{ label: 'Front View', path: vehicle.frontViewPhoto },
				{ label: 'Back View', path: vehicle.backViewPhoto },
				{ label: 'Side View', path: vehicle.sideViewPhoto },
				{ label: 'Interior 1', path: vehicle.interiorPhoto1 },
				{ label: 'Interior 2', path: vehicle.interiorPhoto2 },
				{ label: 'Interior 3', path: vehicle.interiorPhoto3 }
			].filter((item) => !!item.path);

			if (photoEntries.length === 0) {
				grid.innerHTML = '<p class="empty-reviews">No vehicle photos available.</p>';
				return;
			}

			grid.innerHTML = photoEntries.map((item) => {
				const src = `${this.UP_ROOT}${item.path}`;
				return `
					<div class="photo-item">
						<img src="${src}" alt="${this.escapeHtml(item.label)}" onerror="this.src='${this.URL_ROOT}/public/img/default-vehicle.jpg'">
						<p>${this.escapeHtml(item.label)}</p>
					</div>
				`;
			}).join('');
		}

		setLoading(isLoading) {
			const btn = document.getElementById('vehicleSearchBtn');
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

		setImage(id, src) {
			const el = document.getElementById(id);
			if (!el) {
				return;
			}
			el.src = src;
		}

		hideVehicleInfo() {
			const container = document.getElementById('vehicleInfoContainer');
			if (container) {
				container.style.display = 'none';
			}
		}

		showAlert(message, type) {
			const alertEl = document.getElementById('vehicleInfoAlert');
			if (!alertEl) {
				return;
			}
			alertEl.className = `vehicle-alert ${type}`;
			alertEl.textContent = message;
			alertEl.style.display = 'block';
		}

		hideAlert() {
			const alertEl = document.getElementById('vehicleInfoAlert');
			if (!alertEl) {
				return;
			}
			alertEl.style.display = 'none';
			alertEl.textContent = '';
			alertEl.className = 'vehicle-alert';
		}

		yesNo(value) {
			return Number(value) === 1 ? 'Yes' : 'No';
		}

		verificationLabel(value) {
			if (!value) {
				return 'Pending';
			}
			return String(value).charAt(0).toUpperCase() + String(value).slice(1);
		}

		numberOrDash(value) {
			if (value === null || value === undefined || value === '') {
				return '-';
			}
			return String(value);
		}

		currencyOrDash(value) {
			if (value === null || value === undefined || value === '') {
				return '-';
			}
			return `LKR ${Number(value).toFixed(2)}`;
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
	}

	const initializeVehicleInfoManager = function() {
		window.VehicleInfoManager = VehicleInfoManager;
		window.vehicleInfoManager = new VehicleInfoManager();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeVehicleInfoManager);
	} else {
		initializeVehicleInfoManager();
	}
})();
