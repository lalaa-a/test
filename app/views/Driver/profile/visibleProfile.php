<!-- Driver Profile Page -->
<div class="profile-container">
    <!-- Cover Photos Section -->
    <div class="cover-photos-section">
        <div class="cover-photos-slider" id="coverPhotosSlider">
            <!-- Cover photos will be loaded here -->
        </div>
        <div class="cover-nav">
            <button class="cover-nav-btn prev" id="coverPrevBtn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="cover-indicators" id="coverIndicators"></div>
            <button class="cover-nav-btn next" id="coverNextBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar-section">
            <div class="profile-avatar">
                <img id="profilePhoto" src="/public/img/signup/profile.png" alt="Driver Profile">
                <div class="online-status" id="onlineStatus"></div>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-name-section">
                <h1 id="driverName">Driver Name</h1>
                <div class="rating-section">
                    <div class="stars" id="ratingStars">
                        <!-- Stars will be generated here -->
                    </div>
                    <span class="rating-text" id="ratingText">0.0 (0 reviews)</span>
                </div>
            </div>

            <div class="verification-badges" id="verificationBadges">
                <!-- Verification badges will be loaded here -->
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-number" id="totalTrips">0</span>
                    <span class="stat-label">Total Trips</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="yearsExperience">0</span>
                    <span class="stat-label">Years Experience</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="responseRate">0%</span>
                    <span class="stat-label">Response Rate</span>
                </div>
            </div>

            <div class="social-links" id="socialLinks">
                <!-- Social links will be loaded here -->
            </div>
        </div>

        <div class="profile-actions">
            <button class="btn btn-primary" id="saveDriverBtn">
                <i class="fas fa-bookmark"></i>
                <span id="saveText">Save Driver</span>
            </button>
            <button class="btn btn-outline" id="contactDriverBtn">
                <i class="fas fa-phone"></i>
                Contact
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="profile-nav">
        <button class="nav-tab active" data-tab="about">
            <i class="fas fa-user"></i>
            About
        </button>
        <button class="nav-tab" data-tab="vehicles">
            <i class="fas fa-car"></i>
            Vehicles
        </button>
        <button class="nav-tab" data-tab="reviews">
            <i class="fas fa-star"></i>
            Reviews
        </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- About Tab -->
        <div class="tab-pane active" id="about-tab">
            <div class="about-section">
                <div class="section-header">
                    <h2>About Driver</h2>
                </div>

                <div class="about-content">
                    <div class="bio-section">
                        <h3>Bio</h3>
                        <p id="driverBio">No bio available</p>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Languages</label>
                            <div class="detail-value" id="driverLanguages">Not specified</div>
                        </div>

                        <div class="detail-item">
                            <label>Phone</label>
                            <div class="detail-value" id="driverPhone">Not available</div>
                        </div>

                        <div class="detail-item">
                            <label>License Number</label>
                            <div class="detail-value" id="licenseNumber">Not available</div>
                        </div>

                        <div class="detail-item">
                            <label>License Expiry</label>
                            <div class="detail-value" id="licenseExpiry">Not available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicles Tab -->
        <div class="tab-pane" id="vehicles-tab">
            <div class="vehicles-section">
                <div class="section-header">
                    <h2>Driver's Vehicles</h2>
                    <span class="section-count" id="vehiclesCount">0 vehicles</span>
                </div>

                <div class="vehicles-grid" id="vehiclesGrid">
                    <!-- Vehicles will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane" id="reviews-tab">
            <div class="reviews-section">
                <div class="section-header">
                    <h2>Reviews & Ratings</h2>
                    <button class="btn btn-primary" id="writeReviewBtn">
                        <i class="fas fa-pen"></i>
                        Write Review
                    </button>
                </div>

                <div class="reviews-summary">
                    <div class="rating-overview">
                        <div class="overall-rating">
                            <div class="rating-number" id="overallRating">0.0</div>
                            <div class="rating-stars" id="overallStars">
                                <!-- Stars will be generated here -->
                            </div>
                            <div class="rating-count" id="totalReviews">0 reviews</div>
                        </div>

                        <div class="rating-breakdown" id="ratingBreakdown">
                            <!-- Rating breakdown will be loaded here -->
                        </div>
                    </div>
                </div>

                <div class="reviews-list" id="reviewsList">
                    <!-- Reviews will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Write a Review</h3>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="reviewForm">
                <div class="form-group">
                    <label>Rating</label>
                    <div class="rating-input" id="reviewRating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reviewText">Your Review</label>
                    <textarea id="reviewText" class="form-input" placeholder="Share your experience with this driver..." rows="4" maxlength="500"></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span>/500
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelReviewBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitReviewBtn">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Vehicle Details Modal -->
<div id="vehicleModal" class="modal">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="vehicleModalTitle">Vehicle Details</h3>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="vehicle-details" id="vehicleDetails">
                <!-- Vehicle details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript -->
<script src="/public/js/driver/profile/visibleProfile.js"></script>
