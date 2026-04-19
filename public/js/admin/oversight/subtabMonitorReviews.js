(function(){
// Review Monitoring JavaScript
    if (window.ReviewMonitoringManager) {
        console.log('ReviewMonitoringManager already exists, cleaning up...');
        if (window.reviewMonitoringManager) {
            delete window.reviewMonitoringManager;
        }
        delete window.ReviewMonitoringManager;
    }

    // Review Monitoring Manager
    class ReviewMonitoringManager {
        constructor() {
            this.URL_ROOT = '/test';
            this.currentSection = 'all';
            this.reviews = {
                all: [],
                low: [],
                recent: []
            };
            this.filteredReviews = {
                all: [],
                low: [],
                recent: []
            };
            this.pendingDeleteReviewId = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadReviews();
        }

        bindEvents() {
            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetSection = link.getAttribute('data-section');
                    this.switchToSection(targetSection);
                });
            });

            // Search and filter for all reviews
            const allSearchInput = document.getElementById('allSearchInput');
            if (allSearchInput) {
                allSearchInput.addEventListener('input', () => {
                    this.filterReviews('all');
                });
            }

            const allRatingFilter = document.getElementById('allRatingFilter');
            if (allRatingFilter) {
                allRatingFilter.addEventListener('change', () => {
                    this.filterReviews('all');
                });
            }

            // Search for low rated reviews
            const lowSearchInput = document.getElementById('lowSearchInput');
            if (lowSearchInput) {
                lowSearchInput.addEventListener('input', () => {
                    this.filterReviews('low');
                });
            }

            // Search and filter for recent reviews
            const recentSearchInput = document.getElementById('recentSearchInput');
            if (recentSearchInput) {
                recentSearchInput.addEventListener('input', () => {
                    this.filterReviews('recent');
                });
            }

            const recentRatingFilter = document.getElementById('recentRatingFilter');
            if (recentRatingFilter) {
                recentRatingFilter.addEventListener('change', () => {
                    this.filterReviews('recent');
                });
            }

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal());
            });

            // Modal overlay
            const deleteModal = document.getElementById('deleteReviewModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', (e) => {
                    if (e.target === deleteModal) {
                        this.closeModal();
                        // Reset pending delete ID when modal is closed via overlay click
                        this.pendingDeleteReviewId = null;
                    }
                });
            }

            // Confirm delete button
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                console.log('Found confirmDeleteBtn, binding event');
                confirmDeleteBtn.addEventListener('click', () => {
                    console.log('confirmDeleteBtn clicked');
                    this.confirmDeleteReview();
                });
            } else {
                console.error('confirmDeleteBtn not found');
            }
        }

        async loadReviews() {
            try {
                const response = await fetch(`${this.URL_ROOT}/admin/reviews`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load reviews');
                }

                const data = await response.json();

                this.reviews.all = data.reviews || [];
                this.categorizeReviews();
                this.updateStats(data.stats || {});
                this.renderAllSections();

            } catch (error) {
                console.error('Error loading reviews:', error);
                this.showError('Failed to load reviews. Please try again.');
            }
        }

        categorizeReviews() {
            // Low rated reviews (≤2.0)
            this.reviews.low = this.reviews.all.filter(review => parseFloat(review.rating) <= 2.0);

            // Recent reviews (last 7 days)
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

            this.reviews.recent = this.reviews.all.filter(review => {
                const reviewDate = new Date(review.createdAt);
                return reviewDate >= sevenDaysAgo;
            });

            // Copy to filtered arrays initially
            this.filteredReviews.all = [...this.reviews.all];
            this.filteredReviews.low = [...this.reviews.low];
            this.filteredReviews.recent = [...this.reviews.recent];
        }

        updateStats(stats) {
            document.getElementById('totalReviewsCount').textContent = stats.total || 0;
            document.getElementById('averageRating').textContent = stats.average ? stats.average.toFixed(1) : '0.0';
            document.getElementById('lowRatedCount').textContent = stats.lowRated || 0;
            document.getElementById('recentReviewsCount').textContent = stats.recent || 0;
        }

        filterReviews(section) {
            const searchInput = document.getElementById(`${section}SearchInput`);
            const ratingFilter = document.getElementById(`${section}RatingFilter`);

            let filtered = [...this.reviews[section]];

            // Apply search filter
            if (searchInput && searchInput.value.trim()) {
                const searchTerm = searchInput.value.toLowerCase();
                filtered = filtered.filter(review =>
                    review.reviewText.toLowerCase().includes(searchTerm) ||
                    review.reviewerName.toLowerCase().includes(searchTerm) ||
                    (review.targetName && review.targetName.toLowerCase().includes(searchTerm))
                );
            }

            // Apply rating filter
            if (ratingFilter && ratingFilter.value !== 'all') {
                const rating = parseInt(ratingFilter.value);
                filtered = filtered.filter(review => Math.floor(parseFloat(review.rating)) === rating);
            }

            this.filteredReviews[section] = filtered;
            this.renderSection(section);
        }

        renderAllSections() {
            this.renderSection('all');
            this.renderSection('low');
            this.renderSection('recent');
        }

        renderSection(section) {
            // Map section names to grid IDs
            const gridIdMap = {
                'all': 'allReviewsGrid',
                'low': 'lowRatedReviewsGrid',
                'recent': 'recentReviewsGrid'
            };

            const tbody = document.getElementById(gridIdMap[section]);
            if (!tbody) return;

            const reviews = this.filteredReviews[section];

            if (reviews.length === 0) {
                const sectionName = section === 'all' ? 'reviews' : section === 'low' ? 'low rated reviews' : 'recent reviews';
                const icon = section === 'all' ? 'comments' : section === 'low' ? 'exclamation-triangle' : 'clock';
                tbody.innerHTML = `
                    <tr class="no-reviews">
                        <td colspan="6">
                            <i class="fas fa-${icon}"></i>
                            <p>No ${sectionName} found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = reviews.map(review => this.createReviewRow(review)).join('');
        }

        createReviewRow(review) {
            const stars = this.generateStarsSmall(review.rating);
            const targetType = review.guideDriverId ? (review.account_type === 'guide' ? 'Guide' : 'Driver') : 'Unknown';
            const targetName = review.targetName || 'Unknown';
            const targetPhoto = review.targetPhoto || '/test/public/img/default-avatar.png';

            return `
                <tr class="review-row" data-review-id="${review.reviewId}">
                    <td class="reviewer-cell">
                        <div class="reviewer-name">${this.escapeHtml(review.reviewerName)}</div>
                        <div class="reviewer-email">${this.escapeHtml(review.reviewerEmail || '')}</div>
                    </td>
                    <td class="target-cell">
                        <div class="target-name">${this.escapeHtml(targetName)}</div>
                        <div class="target-type">${this.escapeHtml(targetType)}</div>
                    </td>
                    <td class="rating-cell">
                        <div class="stars-small">${stars}</div>
                        <span class="rating-number-small">${review.rating}</span>
                    </td>
                    <td class="review-text-cell" title="${this.escapeHtml(review.reviewText)}">
                        ${this.escapeHtml(review.reviewText)}
                    </td>
                    <td class="date-cell">${this.formatDate(review.createdAt)}</td>
                    <td class="actions-cell">
                        <button class="btn-view" onclick="reviewMonitoringManager.showReviewDetails(${review.reviewId})">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                        <button class="btn-delete-small" onclick="reviewMonitoringManager.showDeleteModal(${review.reviewId})">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </td>
                </tr>
            `;
        }

        generateStars(rating) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            let stars = '';

            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="fas fa-star star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    stars += '<i class="fas fa-star-half-alt star"></i>';
                } else {
                    stars += '<i class="far fa-star star empty"></i>';
                }
            }

            return stars;
        }

        generateStarsSmall(rating) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            let stars = '';

            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="fas fa-star star-small"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    stars += '<i class="fas fa-star-half-alt star-small"></i>';
                } else {
                    stars += '<i class="far fa-star star-small empty"></i>';
                }
            }

            return stars;
        }

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        switchToSection(section) {
            // Update navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`[data-section="${section}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            // Update sections
            document.querySelectorAll('.review-section').forEach(sec => {
                sec.classList.remove('active');
            });

            // Map section names to actual IDs
            const sectionIdMap = {
                'all': 'all-reviews-section',
                'low': 'low-rated-section',
                'recent': 'recent-section'
            };

            const sectionElement = document.getElementById(sectionIdMap[section]);
            if (sectionElement) {
                sectionElement.classList.add('active');
            }

            this.currentSection = section;
        }

        showReviewDetails(reviewId) {
            const review = this.reviews.all.find(r => r.reviewId == reviewId);
            if (!review) return;

            const stars = this.generateStars(review.rating);
            const targetType = review.guideDriverId ? (review.account_type === 'guide' ? 'Guide' : 'Driver') : 'Unknown';
            const targetName = review.targetName || 'Unknown';
            const targetPhoto = review.targetPhoto || '/test/public/img/default-avatar.png';

            const modalContent = document.getElementById('reviewDetailsContent');
            modalContent.innerHTML = `
                <div class="review-details-grid">
                    <div class="reviewer-profile-section">
                        <img src="${review.reviewerPhoto || '/test/public/img/default-avatar.png'}"
                             alt="${review.reviewerName}" class="user-profile-photo"
                             onerror="this.src='/test/public/img/default-avatar.png'">
                        <h3>${this.escapeHtml(review.reviewerName)}</h3>
                        <p class="user-account-type">
                            <i class="fas fa-user"></i>
                            Reviewer
                        </p>
                    </div>
                    <div class="review-info-section">
                        <div class="info-group">
                            <h4>Review Information</h4>
                            <div class="info-item">
                                <label>Rating:</label>
                                <span><div class="stars">${stars}</div> ${review.rating}/5</span>
                            </div>
                            <div class="info-item">
                                <label>Date:</label>
                                <span>${this.formatDate(review.createdAt)}</span>
                            </div>
                            <div class="info-item">
                                <label>Reviewer ID:</label>
                                <span class="id-field">${review.travellerId}</span>
                            </div>
                            <div class="info-item">
                                <label>Target:</label>
                                <span>${this.escapeHtml(targetName)} (${this.escapeHtml(targetType)})</span>
                            </div>
                            ${review.guideDriverId ? `
                            <div class="info-item">
                                <label>${targetType} ID:</label>
                                <span class="id-field">${review.guideDriverId}</span>
                            </div>
                            ` : ''}
                        </div>
                        <div class="info-group">
                            <h4>Review Text</h4>
                            <div class="review-full-text">
                                ${this.escapeHtml(review.reviewText)}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            this.openModal('reviewDetailsModal');

            // Update delete button to include review ID
            const deleteBtn = document.getElementById('deleteReviewBtn');
            if (deleteBtn) {
                deleteBtn.onclick = () => this.showDeleteModal(review.reviewId);
            }
        }

        showDeleteModal(reviewId) {
            const review = this.reviews.all.find(r => r.reviewId == reviewId);
            if (!review) return;

            this.pendingDeleteReviewId = reviewId;

            const stars = this.generateStars(review.rating);
            const preview = document.getElementById('deleteReviewPreview');
            preview.innerHTML = `
                <div class="reviewer-name">${review.reviewerName}</div>
                <div class="stars">${stars} ${review.rating}</div>
                <div class="review-text">${this.escapeHtml(review.reviewText)}</div>
                <div class="review-date">${this.formatDate(review.createdAt)}</div>
            `;

            // Close the review details modal first, then open delete modal
            this.closeModal();
            this.openModal('deleteReviewModal');
        }

        async confirmDeleteReview() {
            if (!this.pendingDeleteReviewId) {
                console.error('No pending delete review ID');
                return;
            }

            console.log('Deleting review:', this.pendingDeleteReviewId);

            try {
                const response = await fetch(`${this.URL_ROOT}/admin/deleteReview/${this.pendingDeleteReviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Delete response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Delete result:', result);

                if (result.success) {
                    // Remove from local arrays
                    this.reviews.all = this.reviews.all.filter(r => r.reviewId != this.pendingDeleteReviewId);
                    this.categorizeReviews();
                    this.renderAllSections();

                    this.showSuccess('Review deleted successfully');
                    this.closeModal();
                    this.pendingDeleteReviewId = null; // Reset after successful deletion
                } else {
                    this.showError(result.message || 'Failed to delete review');
                }

            } catch (error) {
                console.error('Error deleting review:', error);
                this.showError('Failed to delete review. Please try again.');
            }
        }

        openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeModal() {
            // Check if delete modal was open before closing
            const deleteModalWasOpen = document.getElementById('deleteReviewModal')?.classList.contains('show');
            
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
            });
            
            // Reset pending delete ID if delete modal was closed
            if (deleteModalWasOpen) {
                this.pendingDeleteReviewId = null;
            }
        }

        showSuccess(message) {
            // You can implement a toast notification here
            alert(message);
        }

        showError(message) {
            // You can implement a toast notification here
            alert('Error: ' + message);
        }
    }

    // Initialize the manager when DOM is ready
    window.ReviewMonitoringManager = ReviewMonitoringManager;

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.reviewMonitoringManager = new ReviewMonitoringManager();
        });
    } else {
        window.reviewMonitoringManager = new ReviewMonitoringManager();
    }

})();
