<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Guide</h1>
        <p class="search-subtitle">Discover experienced guides for your Sri Lankan adventure</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="guideSearch"
                    placeholder="Search guides by name, location, or expertise..."
                    autocomplete="off"
                >
                <div class="search-icon" id="searchButton">🔍</div>
            </div>
        </div>

        <div class="search-filters">
            <button class="filter-icon" id="filterToggle">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            <div class="filter-chip active" data-category="all">All Guides</div>
            <div class="filter-chip" data-category="licensed">Licensed</div>
            <div class="filter-chip" data-category="reviewed">Reviewed</div>
            <div class="filter-chip" data-category="tourist">Tourist</div>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <!-- Trending Guides -->
    <section class="guides-section">
        <h2 class="section-title">Trending Guides</h2>
        <div class="guides-container">
            <?php if(isset($trendingGuides) && !empty($trendingGuides)): ?>
                <?php foreach($trendingGuides as $guide): ?>
                <div class="guide-card">
                    <?php if($guide->badge_type !== 'none'): ?>
                        <div class="guide-badge <?php echo $guide->badge_type; ?>">
                            <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="guide-avatar">
                        <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating"><?php echo $guide->rating; ?></span>
                            <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                        </div>
                        <p class="guide-description">
                            <?php echo htmlspecialchars($guide->description); ?>
                        </p>
                        <button class="select-guide-btn">Select Guide</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="guide-card"><div class="guide-info"><h3 class="guide-name">Extra Guide 1</h3><p class="guide-description">Test description for scrolling</p><button class="select-guide-btn">Select</button></div></div>
        </div>
    </section>

    <!-- Licensed Guides -->
    <section class="guides-section">
        <h2 class="section-title">Licensed Guides</h2>
        <div class="guides-container-grid">
            <button class="see-more-arrow" data-category="licensed" title="See More Licensed Guides">
                <i class="fas fa-arrow-right"></i>
            </button>
            <?php if(isset($licensedGuides) && !empty($licensedGuides)): ?>
                <?php foreach(array_slice($licensedGuides, 0, 6) as $guide): ?>
                <div class="guide-card">
                    <?php if($guide->badge_type !== 'none'): ?>
                        <div class="guide-badge <?php echo $guide->badge_type; ?>">
                            <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="guide-avatar">
                        <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating"><?php echo $guide->rating; ?></span>
                            <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                        </div>
                        <p class="guide-description">
                            <?php echo htmlspecialchars($guide->description); ?>
                        </p>
                        <button class="select-guide-btn">Select Guide</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Reviewed Guides -->
    <section class="guides-section">
        <h2 class="section-title">Reviewed Guides</h2>
        <div class="guides-container-grid">
            <button class="see-more-arrow" data-category="reviewed" title="See More Reviewed Guides">
                <i class="fas fa-arrow-right"></i>
            </button>
            <?php if(isset($reviewedGuides) && !empty($reviewedGuides)): ?>
                <?php foreach(array_slice($reviewedGuides, 0, 6) as $guide): ?>
                <div class="guide-card">
                    <?php if($guide->badge_type !== 'none'): ?>
                        <div class="guide-badge <?php echo $guide->badge_type; ?>">
                            <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="guide-avatar">
                        <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating"><?php echo $guide->rating; ?></span>
                            <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                        </div>
                        <p class="guide-description">
                            <?php echo htmlspecialchars($guide->description); ?>
                        </p>
                        <button class="select-guide-btn">Select Guide</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Tourist Guides -->
    <section class="guides-section">
        <h2 class="section-title">Tourist Guides</h2>
        <div class="guides-container-grid">
            <button class="see-more-arrow" data-category="tourist" title="See More Tourist Guides">
                <i class="fas fa-arrow-right"></i>
            </button>
            <?php if(isset($touristGuides) && !empty($touristGuides)): ?>
                <?php foreach(array_slice($touristGuides, 0, 6) as $guide): ?>
                <div class="guide-card">
                    <?php if($guide->badge_type !== 'none'): ?>
                        <div class="guide-badge <?php echo $guide->badge_type; ?>">
                            <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="guide-avatar">
                        <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating"><?php echo $guide->rating; ?></span>
                            <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                        </div>
                        <p class="guide-description">
                            <?php echo htmlspecialchars($guide->description); ?>
                        </p>
                        <button class="select-guide-btn">Select Guide</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Initialize all functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeGuideCards();
        initializeSearchFunctionality();
        initializeSeeMoreArrows();
        initializeFilterPopup();
    });

    // Guide cards interaction
    function initializeGuideCards() {
        const guideCards = document.querySelectorAll('.guide-card');

        guideCards.forEach(card => {
            card.addEventListener('click', function() {
                const name = this.querySelector('.guide-name').textContent;
                console.log(`Guide clicked: ${name}`);
                // Add navigation logic here
            });

            // Add keyboard navigation
            card.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    }

    // Search functionality
    function initializeSearchFunctionality() {
        const searchInput = document.getElementById('guideSearch');
        const searchButton = document.getElementById('searchButton');
        const filterChips = document.querySelectorAll('.filter-chip');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        let currentFilter = 'all';
        let searchTerm = '';

        // Get all searchable items
        function getAllSearchableItems() {
            const items = [];

            // Add guide cards
            document.querySelectorAll('.guide-card').forEach(card => {
                const name = card.querySelector('.guide-name')?.textContent || '';
                const description = card.querySelector('.guide-description')?.textContent || '';
                const section = card.closest('section').querySelector('.section-title')?.textContent || '';

                let categoryType = 'all';
                if (section.includes('Licensed')) categoryType = 'licensed';
                else if (section.includes('Reviewed')) categoryType = 'reviewed';
                else if (section.includes('Tourist')) categoryType = 'tourist';

                items.push({
                    element: card,
                    title: name,
                    description: description,
                    category: categoryType,
                    searchText: (name + ' ' + description + ' ' + section).toLowerCase()
                });
            });

            return items;
        }

        // Filter and search items
        function filterAndSearchItems() {
            const items = getAllSearchableItems();
            let visibleCount = 0;
            let filteredItems = items;

            // Apply category filter
            if (currentFilter !== 'all') {
                filteredItems = items.filter(item => item.category === currentFilter);
            }

            // Apply search filter
            if (searchTerm) {
                filteredItems = filteredItems.filter(item =>
                    item.searchText.includes(searchTerm.toLowerCase())
                );
            }

            // Show/hide items
            items.forEach(item => {
                const shouldShow = filteredItems.includes(item);
                if (shouldShow) {
                    item.style.display = '';
                    visibleCount++;
                    highlightSearchTerm(item, searchTerm);
                } else {
                    item.style.display = 'none';
                    removeHighlights(item);
                }
            });

            // Show/hide sections based on visible items
            document.querySelectorAll('section').forEach(section => {
                const visibleItems = section.querySelectorAll('.guide-card:not([style*="display: none"])');
                if (visibleItems.length === 0 && (searchTerm || currentFilter !== 'all')) {
                    section.style.display = 'none';
                } else {
                    section.style.display = '';
                }
            });

            // Update results info
            updateResultsInfo(visibleCount, searchTerm, currentFilter);

            // Show no results message if needed
            showNoResultsIfNeeded(visibleCount);
        }

        // Highlight search terms
        function highlightSearchTerm(element, term) {
            if (!term) return;

            const nameElement = element.querySelector('.guide-name');
            const descElement = element.querySelector('.guide-description');

            [nameElement, descElement].forEach(el => {
                if (el && el.textContent) {
                    const originalText = el.getAttribute('data-original-text') || el.textContent;
                    if (!el.getAttribute('data-original-text')) {
                        el.setAttribute('data-original-text', originalText);
                    }

                    const regex = new RegExp(`(${term})`, 'gi');
                    const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
                    el.innerHTML = highlightedText;
                }
            });
        }

        // Remove highlights
        function removeHighlights(element) {
            const elements = element.querySelectorAll('[data-original-text]');
            elements.forEach(el => {
                el.innerHTML = el.getAttribute('data-original-text');
            });
        }

        // Update results info
        function updateResultsInfo(count, term, filter) {
            let message = '';
            if (term && filter !== 'all') {
                message = `Found ${count} results for "${term}" in ${getFilterName(filter)}`;
            } else if (term) {
                message = `Found ${count} results for "${term}"`;
            } else if (filter !== 'all') {
                message = `Showing ${count} ${getFilterName(filter)} guides`;
            } else {
                message = '';
            }

            if (message) {
                searchResultsInfo.textContent = message;
                searchResultsInfo.style.display = 'block';
            } else {
                searchResultsInfo.style.display = 'none';
            }
        }

        // Get filter display name
        function getFilterName(filter) {
            const filterNames = {
                'licensed': 'Licensed Guides',
                'reviewed': 'Reviewed Guides',
                'tourist': 'Tourist Guides'
            };
            return filterNames[filter] || filter;
        }

        // Show no results message
        function showNoResultsIfNeeded(count) {
            let noResultsElement = document.getElementById('noResultsMessage');

            if (count === 0 && (searchTerm || currentFilter !== 'all')) {
                if (!noResultsElement) {
                    noResultsElement = document.createElement('div');
                    noResultsElement.id = 'noResultsMessage';
                    noResultsElement.className = 'no-results';
                    noResultsElement.innerHTML = `
                        <div class="no-results-icon">🔍</div>
                        <div class="no-results-title">No guides found</div>
                        <div class="no-results-text">Try adjusting your search terms or filters to find more results.</div>
                    `;
                    document.querySelector('.content-wrapper').appendChild(noResultsElement);
                }
                noResultsElement.style.display = 'block';
            } else if (noResultsElement) {
                noResultsElement.style.display = 'none';
            }
        }

        // Search input event
        searchInput.addEventListener('input', function() {
            searchTerm = this.value.trim();
            filterAndSearchItems();
        });

        // Search button event
        searchButton.addEventListener('click', function() {
            searchTerm = searchInput.value.trim();
            filterAndSearchItems();
        });

        // Enter key search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchTerm = this.value.trim();
                filterAndSearchItems();
            }
        });

        // Filter chip events
        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                // Update active chip
                filterChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Update current filter
                currentFilter = this.getAttribute('data-category');

                // Apply filter
                filterAndSearchItems();
            });
        });

        // Clear search function
        window.clearSearch = function() {
            searchInput.value = '';
            searchTerm = '';
            currentFilter = 'all';
            filterChips.forEach(c => c.classList.remove('active'));
            filterChips[0].classList.add('active'); // Activate "All Guides"
            filterAndSearchItems();
        };
    }

    // See More arrows functionality
    function initializeSeeMoreArrows() {
        const seeMoreArrows = document.querySelectorAll('.see-more-arrow');

        seeMoreArrows.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                const categoryName = getCategoryDisplayName(category);
                console.log(`See More arrow clicked for: ${categoryName}`);
                showNotification(`Viewing all ${categoryName} guides`, 'info');
                // Here you could implement navigation to a dedicated category page
                // or expand the current section to show more items
            });
        });
    }

    // Helper function to get display name for category
    function getCategoryDisplayName(category) {
        const categoryNames = {
            'licensed': 'Licensed Guides',
            'reviewed': 'Reviewed Guides',
            'tourist': 'Tourist Guides'
        };
        return categoryNames[category] || category;
    }

    // Filter popup functionality
    function initializeFilterPopup() {
        const filterToggle = document.getElementById('filterToggle');
        const filterPopup = document.getElementById('filterPopup');
        const filterCloseBtn = document.getElementById('filterCloseBtn');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        // Toggle filter popup
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            filterPopup.classList.toggle('show');
        });

        // Close filter popup
        filterCloseBtn.addEventListener('click', function() {
            filterPopup.classList.remove('show');
        });

        // Close on outside click
        filterPopup.addEventListener('click', function(e) {
            if (e.target === filterPopup) {
                filterPopup.classList.remove('show');
            }
        });

        // Apply filters
        applyFiltersBtn.addEventListener('click', function() {
            // Get selected filters
            const selectedFilters = [];
            const checkboxes = filterPopup.querySelectorAll('input[type="checkbox"]:checked');
            
            checkboxes.forEach(checkbox => {
                selectedFilters.push(checkbox.getAttribute('data-filter'));
            });

            console.log('Applied filters:', selectedFilters);
            showNotification('Filters applied successfully!', 'success');
            filterPopup.classList.remove('show');
            
            // Here you would implement the actual filtering logic
            // For now, just show a notification
        });

        // Clear filters
        clearFiltersBtn.addEventListener('click', function() {
            const checkboxes = filterPopup.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true; // Reset to default (all checked)
            });
            showNotification('Filters cleared!', 'info');
        });
    }

    // Utility functions
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
            max-width: 250px;
            word-wrap: break-word;
            font-size: 14px;
        `;

        // Try to find the admin dashboard container first, then fallback to content wrapper
        const adminDashboard = document.getElementById('dashboard');
        const contentWrapper = document.querySelector('.content-wrapper');

        if (adminDashboard) {
            adminDashboard.appendChild(notification);
        } else if (contentWrapper) {
            contentWrapper.appendChild(notification);
        } else {
            document.body.appendChild(notification);
        }

        // Fade in
        setTimeout(() => notification.style.opacity = '1', 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }


    // Scroll behavior for category containers
    function initializeScrollBehavior() {
        const categoryContainers = document.querySelectorAll('.guides-container:not(.no-scroll)');

        categoryContainers.forEach(container => {
            // Add smooth scrolling
            container.style.scrollBehavior = 'smooth';

            // Optional: Add scroll indicators
            if (container.scrollWidth > container.clientWidth) {
                addScrollIndicators(container);
            }
        });
    }

    // Add scroll indicators for horizontally scrollable containers
    function addScrollIndicators(container) {
        const wrapper = container.parentElement;

        // Create left and right scroll buttons
        const leftBtn = document.createElement('button');
        leftBtn.innerHTML = '‹';
        leftBtn.className = 'scroll-btn scroll-btn-left';
        leftBtn.style.cssText = `
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 10;
            display: none;
        `;

        const rightBtn = document.createElement('button');
        rightBtn.innerHTML = '›';
        rightBtn.className = 'scroll-btn scroll-btn-right';
        rightBtn.style.cssText = leftBtn.style.cssText.replace('left: 5px', 'right: 5px');

        wrapper.style.position = 'relative';
        wrapper.appendChild(leftBtn);
        wrapper.appendChild(rightBtn);

        // Scroll functionality
        leftBtn.addEventListener('click', () => {
            container.scrollBy({ left: -300, behavior: 'smooth' });
        });

        rightBtn.addEventListener('click', () => {
            container.scrollBy({ left: 300, behavior: 'smooth' });
        });

        // Show/hide scroll buttons based on scroll position
        function updateScrollButtons() {
            leftBtn.style.display = container.scrollLeft > 0 ? 'block' : 'none';
            rightBtn.style.display =
                container.scrollLeft < (container.scrollWidth - container.clientWidth) ? 'block' : 'none';
        }

        container.addEventListener('scroll', updateScrollButtons);
        updateScrollButtons();
    }
</script>
