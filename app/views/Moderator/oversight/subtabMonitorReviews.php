
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Review Monitoring</h1>
            <p class="page-subtitle">Monitor and manage user reviews</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalReviewsCount">0</div>
            <div class="stat-label">Total Reviews</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon average">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="averageRating">0.0</div>
            <div class="stat-label">Average Rating</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon low">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="lowRatedCount">0</div>
            <div class="stat-label">Low Rated (≤2.0)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon recent">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="recentReviewsCount">0</div>
            <div class="stat-label">Recent (Last 7 days)</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#all-reviews-section" class="nav-link active" data-section="all">
        <i class="fas fa-list"></i>
        All Reviews
    </a>
    <a href="#low-rated-section" class="nav-link" data-section="low">
        <i class="fas fa-exclamation-triangle"></i>
        Low Rated
    </a>
    <a href="#recent-section" class="nav-link" data-section="recent">
        <i class="fas fa-clock"></i>
        Recent
    </a>
</div>

<!-- Review Sections -->
<div class="review-sections">
    <!-- All Reviews Section -->
    <div class="review-section active" id="all-reviews-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-list"></i>
                    All Reviews
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="allSearchInput" placeholder="Search reviews..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="allRatingFilter" class="filter-select">
                                <option value="all">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews-table-container" id="all-reviews-container">
            <table class="reviews-table" id="allReviewsTable">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Target</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="allReviewsGrid">
                    <tr class="no-reviews">
                        <td colspan="6">
                            <i class="fas fa-comments"></i>
                            <p>No reviews found</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Rated Reviews Section -->
    <div class="review-section" id="low-rated-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Rated Reviews (≤2.0)
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="lowSearchInput" placeholder="Search low rated reviews..." class="search-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews-table-container" id="low-rated-container">
            <table class="reviews-table" id="lowRatedReviewsTable">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Target</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="lowRatedReviewsGrid">
                    <tr class="no-reviews">
                        <td colspan="6">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>No low rated reviews found</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Reviews Section -->
    <div class="review-section" id="recent-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-clock"></i>
                    Recent Reviews (Last 7 days)
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="recentSearchInput" placeholder="Search recent reviews..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="recentRatingFilter" class="filter-select">
                                <option value="all">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews-table-container" id="recent-reviews-container">
            <table class="reviews-table" id="recentReviewsTable">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Target</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recentReviewsGrid">
                    <tr class="no-reviews">
                        <td colspan="6">
                            <i class="fas fa-clock"></i>
                            <p>No recent reviews found</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Review Modal -->
<div class="modal" id="deleteReviewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Delete Review</h3>
            <button class="modal-close" onclick="reviewMonitoringManager.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this review? This action cannot be undone.</p>
            <div class="review-preview" id="deleteReviewPreview">
                <!-- Review details will be shown here -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary modal-close" onclick="reviewMonitoringManager.closeModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete Review</button>
        </div>
    </div>
</div>

<!-- Review Details Modal -->
<div id="reviewDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Review Details</h3>
            <button class="modal-close" onclick="reviewMonitoringManager.closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="reviewDetailsContent">
            <!-- Review details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="reviewMonitoringManager.closeModal()">Close</button>
            <button class="btn btn-danger" id="deleteReviewBtn">Delete Review</button>
        </div>
    </div>
</div>
