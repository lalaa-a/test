(function() {
    // Clean up any existing instance
    if (window.DriverProfileManager) {
        console.log('DriverProfileManager already exists, cleaning up...');
        if (window.driverProfileManager) {
            delete window.driverProfileManager;
        }
        delete window.DriverProfileManager;
    }

    // Driver Profile Manager
    class DriverProfileManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.driverId = null;
            this.currentUser = null;
            this.isSaved = false;

            // Data storage
            this.profileData = null;
            this.coverPhotos = [];
            this.vehicles = [];
            this.reviews = [];

            this.init();
        }

        init() {
            this.getDriverIdFromUrl();
            this.bindEvents();
            this.loadProfileData();
        }

        getDriverIdFromUrl() {
            // Get driver ID from URL path (e.g., /RegUser/driverVisibleProfile/123)
            const pathSegments = window.location.pathname.split('/');
            const driverIdIndex = pathSegments.indexOf('driverVisibleProfile');
            
            if (driverIdIndex !== -1 && pathSegments[driverIdIndex + 1]) {
                this.driverId = pathSegments[driverIdIndex + 1];
            }

            if (!this.driverId) {
                this.showNotification('Driver ID not found', 'error');
                return;
            }
        }

        bindEvents() {
            // Tab navigation
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    this.switchTab(e.target.dataset.tab);
                });
            });

            // Save driver button
            const saveBtn = document.getElementById('saveDriverBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', () => this.toggleSaveDriver());
            }

            // Contact driver button
            const contactBtn = document.getElementById('contactDriverBtn');
            if (contactBtn) {
                contactBtn.addEventListener('click', () => this.contactDriver());
            }

            // Cover photo navigation
            const coverPrevBtn = document.getElementById('coverPrevBtn');
            const coverNextBtn = document.getElementById('coverNextBtn');

            if (coverPrevBtn) {
                coverPrevBtn.addEventListener('click', () => this.navigateCoverPhoto(-1));
            }
            if (coverNextBtn) {
                coverNextBtn.addEventListener('click', () => this.navigateCoverPhoto(1));
            }

            // Review modal
            const writeReviewBtn = document.getElementById('writeReviewBtn');
            if (writeReviewBtn) {
                writeReviewBtn.addEventListener('click', () => this.openReviewModal());
            }

            const reviewModal = document.getElementById('reviewModal');
            const cancelReviewBtn = document.getElementById('cancelReviewBtn');
            const modalCloseBtn = document.querySelector('#reviewModal .modal-close');

            if (reviewModal) {
                reviewModal.addEventListener('click', (e) => {
                    if (e.target === reviewModal) {
                        this.closeReviewModal();
                    }
                });
            }

            if (cancelReviewBtn) {
                cancelReviewBtn.addEventListener('click', () => this.closeReviewModal());
            }

            if (modalCloseBtn) {
                modalCloseBtn.addEventListener('click', () => this.closeReviewModal());
            }

            // Review form
            const reviewForm = document.getElementById('reviewForm');
            if (reviewForm) {
                reviewForm.addEventListener('submit', (e) => this.submitReview(e));
            }

            // Rating input
            const ratingStars = document.querySelectorAll('#reviewRating i');
            ratingStars.forEach(star => {
                star.addEventListener('click', () => this.setReviewRating(star.dataset.rating));
            });

            // Character count
            const reviewText = document.getElementById('reviewText');
            if (reviewText) {
                reviewText.addEventListener('input', () => this.updateCharCount());
            }

            // Vehicle modal
            const vehicleModal = document.getElementById('vehicleModal');
            if (vehicleModal) {
                vehicleModal.addEventListener('click', (e) => {
                    if (e.target === vehicleModal) {
                        this.closeVehicleModal();
                    }
                });
            }

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeReviewModal();
                    this.closeVehicleModal();
                }
            });
        }

        async loadProfileData() {
            try {
                console.log('Loading driver profile data...');

                // Load all data in parallel
                const [profileResponse, coverResponse, vehiclesResponse, reviewsResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/Driver/getDriverProfile/${this.driverId}`),
                    fetch(`${this.URL_ROOT}/RegUser/getDriverCoverPhotos/${this.driverId}`),
                    fetch(`${this.URL_ROOT}/Driver/getDriverVehicles/${this.driverId}`),
                    fetch(`${this.URL_ROOT}/Driver/getDriverReviews/${this.driverId}`)
                ]);

                const [profileData, coverData, vehiclesData, reviewsData] = await Promise.all([
                    profileResponse.json(),
                    coverResponse.json(),
                    vehiclesResponse.json(),
                    reviewsResponse.json()
                ]);

                if (profileData.success) {
                    this.profileData = profileData.profile;
                    this.renderProfile();
                }

                if (coverData.success) {
                    this.coverPhotos = coverData.photos;
                    this.renderCoverPhotos();
                }

                if (vehiclesData.success) {
                    this.vehicles = vehiclesData.vehicles;
                    this.renderVehicles();
                }

                if (reviewsData.success) {
                    this.reviews = reviewsData.reviews;
                    this.renderReviews();
                }

                // Check if driver is saved
                this.checkIfDriverSaved();

            } catch (error) {
                console.error('Error loading profile data:', error);
                this.showNotification('Error loading profile data', 'error');
            }
        }

        renderProfile() {
            if (!this.profileData) return;

            const data = this.profileData;

            // Basic info
            document.getElementById('driverName').textContent = data.profile_name || 'Driver';
            document.getElementById('driverBio').textContent = data.bio || 'No bio available';
            document.getElementById('driverLanguages').textContent = data.languages || 'Not specified';
            document.getElementById('driverPhone').textContent = data.phone || 'Not available';
            document.getElementById('licenseNumber').textContent = data.tLicenseNumber || 'Not available';
            document.getElementById('licenseExpiry').textContent = data.tLicenseExpiryDate ? this.formatDate(data.tLicenseExpiryDate) : 'Not available';

            // Profile photo
            const profilePhoto = document.getElementById('profilePhoto');
            if (profilePhoto && data.profile_photo) {
                profilePhoto.src = `${this.UP_ROOT}/profile/${data.profile_photo}`;
            }

            // Rating
            this.renderRating('ratingStars', data.averageRating || 0);
            document.getElementById('ratingText').textContent = `${parseFloat(data.averageRating || 0).toFixed(1)} (${data.totalReviews || 0} reviews)`;

            // Verification badges
            this.renderVerificationBadges(data);

            // Social links
            this.renderSocialLinks(data);

            // Stats
            document.getElementById('totalTrips').textContent = data.totalTrips || 0;
            document.getElementById('yearsExperience').textContent = data.yearsExperience || 0;
            document.getElementById('responseRate').textContent = `${data.responseRate || 0}%`;
        }

        renderRating(containerId, rating) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

            let starsHtml = '';

            // Full stars
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star star"></i>';
            }

            // Half star
            if (hasHalfStar) {
                starsHtml += '<i class="fas fa-star-half-alt star"></i>';
            }

            // Empty stars
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star star empty"></i>';
            }

            container.innerHTML = starsHtml;
        }

        renderVerificationBadges(data) {
            const container = document.getElementById('verificationBadges');
            if (!container) return;

            let badgesHtml = '';

            // DL Verification
            if (data.dlVerified) {
                badgesHtml += '<span class="verification-badge verified"><i class="fas fa-check-circle"></i> Driving License</span>';
            } else {
                badgesHtml += '<span class="verification-badge unverified"><i class="fas fa-times-circle"></i> Driving License</span>';
            }

            // TL Verification
            if (data.tlVerified) {
                badgesHtml += '<span class="verification-badge verified"><i class="fas fa-check-circle"></i> Transport License</span>';
            } else if (data.tlSubmitted) {
                badgesHtml += '<span class="verification-badge pending"><i class="fas fa-clock"></i> Transport License</span>';
            } else {
                badgesHtml += '<span class="verification-badge unverified"><i class="fas fa-times-circle"></i> Transport License</span>';
            }

            container.innerHTML = badgesHtml;
        }

        renderSocialLinks(data) {
            const container = document.getElementById('socialLinks');
            if (!container) return;

            let linksHtml = '';

            if (data.instaAccount) {
                linksHtml += `
                    <a href="https://instagram.com/${data.instaAccount}" target="_blank" class="social-link-item">
                        <i class="fab fa-instagram"></i>
                        <span>@${data.instaAccount}</span>
                    </a>
                `;
            }

            if (data.facebookAccount) {
                linksHtml += `
                    <a href="https://facebook.com/${data.facebookAccount}" target="_blank" class="social-link-item">
                        <i class="fab fa-facebook"></i>
                        <span>${data.facebookAccount}</span>
                    </a>
                `;
            }

            if (data.whatsappNumber) {
                linksHtml += `
                    <a href="https://wa.me/${data.whatsappNumber.replace(/[^0-9]/g, '')}" target="_blank" class="social-link-item">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                `;
            }

            if (!linksHtml) {
                linksHtml = '<p class="no-data">No social media links available</p>';
            }

            container.innerHTML = linksHtml;
        }

        renderCoverPhotos() {
            const slider = document.getElementById('coverPhotosSlider');
            const indicators = document.getElementById('coverIndicators');

            if (!slider || this.coverPhotos.length === 0) {
                // Show default cover
                slider.innerHTML = `
                    <div class="cover-photo-item">
                        <img src="/public/img/default-cover.jpg" alt="Default Cover">
                        <div class="cover-photo-overlay">
                            <h2>Driver Profile</h2>
                        </div>
                    </div>
                `;
                return;
            }

            let photosHtml = '';
            let indicatorsHtml = '';

            this.coverPhotos.forEach((photo, index) => {
                photosHtml += `
                    <div class="cover-photo-item">
                        <img src="${this.UP_ROOT}${photo.photo_path}" alt="Cover Photo ${index + 1}">
                    </div>
                `;

                indicatorsHtml += `<div class="cover-indicator ${index === 0 ? 'active' : ''}" data-slide="${index}"></div>`;
            });

            slider.innerHTML = photosHtml;
            indicators.innerHTML = indicatorsHtml;

            // Bind indicator clicks
            document.querySelectorAll('.cover-indicator').forEach((indicator, index) => {
                indicator.addEventListener('click', () => this.goToCoverSlide(index));
            });

            this.currentCoverSlide = 0;
            this.totalCoverSlides = this.coverPhotos.length;
        }

        renderVehicles() {
            const grid = document.getElementById('vehiclesGrid');
            const count = document.getElementById('vehiclesCount');

            if (!grid) return;

            if (count) {
                count.textContent = `${this.vehicles.length} vehicle${this.vehicles.length !== 1 ? 's' : ''}`;
            }

            if (this.vehicles.length === 0) {
                grid.innerHTML = '<p class="no-data">No vehicles available</p>';
                return;
            }

            const vehiclesHtml = this.vehicles.map(vehicle => `
                <div class="vehicle-card" onclick="window.driverProfileManager.showVehicleDetails(${vehicle.vehicleId})">
                    <div class="vehicle-image">
                        <img src="${this.UP_ROOT}/vehicles/${vehicle.frontViewPhoto || 'default-vehicle.jpg'}" alt="${vehicle.make} ${vehicle.model}">
                        <div class="vehicle-status ${vehicle.availability ? 'available' : 'unavailable'}">
                            ${vehicle.availability ? 'Available' : 'Unavailable'}
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-title">${vehicle.make} ${vehicle.model} ${vehicle.year}</h3>
                        <div class="vehicle-details">
                            <span class="vehicle-detail">
                                <i class="fas fa-users"></i>
                                ${vehicle.seatingCapacity} seats
                            </span>
                            <span class="vehicle-detail">
                                <i class="fas fa-couch"></i>
                                ${vehicle.childSeats} child seats
                            </span>
                            <span class="vehicle-detail">
                                <i class="fas fa-palette"></i>
                                ${vehicle.color}
                            </span>
                        </div>
                        <p class="vehicle-description">${vehicle.description || 'No description available'}</p>
                    </div>
                </div>
            `).join('');

            grid.innerHTML = vehiclesHtml;
        }

        renderReviews() {
            const list = document.getElementById('reviewsList');
            if (!list) return;

            if (this.reviews.length === 0) {
                list.innerHTML = '<p class="no-data">No reviews yet</p>';
                return;
            }

            const reviewsHtml = this.reviews.map(review => `
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <img src="${review.reviewerPhoto || '/public/img/signup/profile.png'}" alt="${review.reviewerName}" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h4>${this.escapeHtml(review.reviewerName)}</h4>
                                <span>${this.formatDate(review.createdAt)}</span>
                            </div>
                        </div>
                        <div class="review-rating">
                            ${this.renderStars(review.rating)}
                        </div>
                    </div>
                    <div class="review-content">
                        <h5>${this.escapeHtml(review.title)}</h5>
                        <p>${this.escapeHtml(review.comment)}</p>
                    </div>
                </div>
            `).join('');

            list.innerHTML = reviewsHtml;

            // Update overall rating
            this.updateOverallRating();
        }

        renderStars(rating) {
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                starsHtml += `<i class="fas fa-star ${i <= rating ? 'star' : 'empty'}"></i>`;
            }
            return starsHtml;
        }

        updateOverallRating() {
            if (this.reviews.length === 0) return;

            const totalRating = this.reviews.reduce((sum, review) => sum + review.rating, 0);
            const averageRating = totalRating / this.reviews.length;

            document.getElementById('overallRating').textContent = averageRating.toFixed(1);
            this.renderRating('overallStars', averageRating);
            document.getElementById('totalReviews').textContent = `${this.reviews.length} review${this.reviews.length !== 1 ? 's' : ''}`;

            // Update rating breakdown
            this.renderRatingBreakdown();
        }

        renderRatingBreakdown() {
            const breakdown = document.getElementById('ratingBreakdown');
            if (!breakdown) return;

            const ratingCounts = [0, 0, 0, 0, 0, 0]; // Index 0 unused, 1-5 for ratings

            this.reviews.forEach(review => {
                ratingCounts[review.rating]++;
            });

            let breakdownHtml = '';
            for (let i = 5; i >= 1; i--) {
                const count = ratingCounts[i];
                const percentage = this.reviews.length > 0 ? (count / this.reviews.length) * 100 : 0;

                breakdownHtml += `
                    <div class="rating-bar">
                        <span class="rating-bar-label">${i} star${i !== 1 ? 's' : ''}</span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: ${percentage}%"></div>
                        </div>
                        <span class="rating-bar-count">${count}</span>
                    </div>
                `;
            }

            breakdown.innerHTML = breakdownHtml;
        }

        switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }

            // Update tab content
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            const activePane = document.getElementById(`${tabName}-tab`);
            if (activePane) {
                activePane.classList.add('active');
            }
        }

        navigateCoverPhoto(direction) {
            if (this.coverPhotos.length <= 1) return;

            this.currentCoverSlide = (this.currentCoverSlide + direction + this.totalCoverSlides) % this.totalCoverSlides;
            this.updateCoverSlide();
        }

        goToCoverSlide(slideIndex) {
            this.currentCoverSlide = slideIndex;
            this.updateCoverSlide();
        }

        updateCoverSlide() {
            const slider = document.getElementById('coverPhotosSlider');
            if (slider) {
                slider.style.transform = `translateX(-${this.currentCoverSlide * 100}%)`;
            }

            // Update indicators
            document.querySelectorAll('.cover-indicator').forEach((indicator, index) => {
                indicator.classList.toggle('active', index === this.currentCoverSlide);
            });
        }

        async toggleSaveDriver() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/toggleSaveDriver`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        driverId: this.driverId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.isSaved = data.saved;
                    this.updateSaveButton();
                    this.showNotification(data.message, 'success');
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error toggling save:', error);
                this.showNotification('Error saving driver', 'error');
            }
        }

        updateSaveButton() {
            const saveBtn = document.getElementById('saveDriverBtn');
            const saveText = document.getElementById('saveText');

            if (this.isSaved) {
                saveBtn.classList.add('saved');
                saveText.textContent = 'Saved';
                saveBtn.innerHTML = '<i class="fas fa-bookmark"></i><span id="saveText">Saved</span>';
            } else {
                saveBtn.classList.remove('saved');
                saveText.textContent = 'Save Driver';
                saveBtn.innerHTML = '<i class="far fa-bookmark"></i><span id="saveText">Save Driver</span>';
            }
        }

        async checkIfDriverSaved() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/isDriverSaved/${this.driverId}`);
                const data = await response.json();

                if (data.success) {
                    this.isSaved = data.saved;
                    this.updateSaveButton();
                }
            } catch (error) {
                console.error('Error checking save status:', error);
            }
        }

        contactDriver() {
            // Implement contact functionality
            const phone = this.profileData?.phone;
            if (phone) {
                window.location.href = `tel:${phone}`;
            } else {
                this.showNotification('Phone number not available', 'error');
            }
        }

        openReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.style.display = 'block';
                document.getElementById('reviewText').focus();
            }
        }

        closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.style.display = 'none';
                this.resetReviewForm();
            }
        }

        resetReviewForm() {
            document.getElementById('reviewForm').reset();
            document.querySelectorAll('#reviewRating i').forEach(star => {
                star.classList.remove('active');
            });
            document.getElementById('charCount').textContent = '0/500';
        }

        setReviewRating(rating) {
            const stars = document.querySelectorAll('#reviewRating i');
            stars.forEach((star, index) => {
                star.classList.toggle('active', index < rating);
            });
        }

        updateCharCount() {
            const text = document.getElementById('reviewText').value;
            document.getElementById('charCount').textContent = `${text.length}/500`;
        }

        async submitReview(e) {
            e.preventDefault();

            const comment = document.getElementById('reviewText').value.trim();
            const rating = document.querySelectorAll('#reviewRating i.active').length;

            if (!comment || rating === 0) {
                this.showNotification('Please write a review and select a rating', 'error');
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/submitReview`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        driverId: this.driverId,
                        comment: comment,
                        rating: rating
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Review submitted successfully!', 'success');
                    this.closeReviewModal();
                    // Reload reviews
                    this.loadReviews();
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                this.showNotification('Error submitting review', 'error');
            }
        }

        async loadReviews() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Driver/getDriverReviews/${this.driverId}`);
                const data = await response.json();

                if (data.success) {
                    this.reviews = data.reviews;
                    this.renderReviews();
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
            }
        }

        showVehicleDetails(vehicleId) {
            const vehicle = this.vehicles.find(v => v.vehicleId == vehicleId);
            if (!vehicle) return;

            const modal = document.getElementById('vehicleModal');
            const title = document.getElementById('vehicleModalTitle');
            const details = document.getElementById('vehicleDetails');

            title.textContent = `${vehicle.make} ${vehicle.model} ${vehicle.year}`;

            const photos = [
                { label: 'Front View', path: vehicle.frontViewPhoto },
                { label: 'Back View', path: vehicle.backViewPhoto },
                { label: 'Side View', path: vehicle.sideViewPhoto },
                { label: 'Interior 1', path: vehicle.interiorPhoto1 },
                { label: 'Interior 2', path: vehicle.interiorPhoto2 },
                { label: 'Interior 3', path: vehicle.interiorPhoto3 }
            ].filter(photo => photo.path);

            const detailsHtml = `
                <div class="vehicle-detail-section">
                    <h4>Photos</h4>
                    <div class="vehicle-photos-grid">
                        ${photos.map(photo => `
                            <div class="vehicle-photo-item">
                                <img src="${this.UP_ROOT}/vehicles/${photo.path}" alt="${photo.label}">
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="vehicle-detail-section">
                    <h4>Specifications</h4>
                    <div class="vehicle-specs">
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Make & Model</span>
                            <span class="vehicle-spec-value">${vehicle.make} ${vehicle.model}</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Year</span>
                            <span class="vehicle-spec-value">${vehicle.year}</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Color</span>
                            <span class="vehicle-spec-value">${vehicle.color || 'Not specified'}</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">License Plate</span>
                            <span class="vehicle-spec-value">${vehicle.licensePlate}</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Seating Capacity</span>
                            <span class="vehicle-spec-value">${vehicle.seatingCapacity} seats</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Child Seats</span>
                            <span class="vehicle-spec-value">${vehicle.childSeats}</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Fuel Efficiency</span>
                            <span class="vehicle-spec-value">${vehicle.fuelEfficiency || 'Not specified'} L/100km</span>
                        </div>
                        <div class="vehicle-spec-item">
                            <span class="vehicle-spec-label">Status</span>
                            <span class="vehicle-spec-value ${vehicle.isApproved ? 'approved' : 'pending'}">${vehicle.isApproved ? 'Approved' : 'Pending Approval'}</span>
                        </div>
                    </div>
                </div>
            `;

            details.innerHTML = detailsHtml;
            modal.style.display = 'block';
        }

        closeVehicleModal() {
            const modal = document.getElementById('vehicleModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        showNotification(message, type = 'info') {
            // Use the global notification system if available
            if (window.showNotification) {
                window.showNotification(message, type);
            } else {
                // Fallback to alert
                alert(message);
            }
        }
    }

    // Initialize the manager
    window.DriverProfileManager = DriverProfileManager;
    window.driverProfileManager = new DriverProfileManager();

})();
