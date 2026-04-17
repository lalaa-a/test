
    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Guide</h1>
        <p class="search-subtitle">Discover trusted guides for your Sri Lankan adventure</p>

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
            <?php if(isset($mainFilters) && is_array($mainFilters)): ?>
                <?php foreach($mainFilters as $filterKey => $filter): ?>
                    <div class="filter-chip <?php echo $filterKey === 'all' ? 'active' : ''; ?>" 
                         data-category="<?php echo $filterKey; ?>">
                        <?php echo htmlspecialchars($filter['name']); ?> (<?php echo $filter['count']; ?>)
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <?php if(isset($mainFilters) && is_array($mainFilters)): ?>
        <?php foreach($mainFilters as $filterKey => $filter): ?>
            <?php if(empty($filter['accounts'])) continue; ?>
           
            <!-- <?php echo htmlspecialchars($filter['name']); ?> Section -->
            <section class="drivers-section" data-filter="<?php echo $filterKey; ?>">
                <h2 class="section-title"><?php echo htmlspecialchars($filter['name']); ?> (<?php echo $filter['count']; ?>)</h2>
                
                <div class="<?php echo $filterKey === 'all' ? 'drivers-container' : 'drivers-container-grid'; ?>">
                    <?php if($filterKey !== 'all'): ?>
                        <button class="see-more-arrow" data-category="<?php echo $filterKey; ?>" 
                                title="See More <?php echo htmlspecialchars($filter['name']); ?>">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php 
                    $displayDrivers = ($filterKey === 'all') ? $filter['accounts'] : array_slice($filter['accounts'], 0, 6);
                    foreach($displayDrivers as $driver): 
                    ?>
                        <div class="driver-card" data-user-id="<?php echo htmlspecialchars($driver->userId); ?>">
                            <button class="save-driver-btn" title="Save Driver">
                                <i class="far fa-heart"></i>
                            </button>
                            
                            <?php if($driver->dlVerified): ?>
                                <div class="verified-chip">
                                    <i class="fas fa-check-circle"></i> Verified
                                </div>
                            <?php endif; ?>
                            
                            <?php if($filterKey === 'high_rated'): ?>
                                <div class="driver-badge top-rated">Top Rated</div>
                            <?php endif; ?>
                            
                            <div class="driver-avatar">
                                <img src="<?php echo !empty($driver->profile_photo) ? URL_ROOT . '/public/uploads' . htmlspecialchars($driver->profile_photo) : URL_ROOT . '/public/img/signup/profile.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($driver->fullname); ?>">
                            </div>
                            <div class="driver-info">
                                <h3 class="driver-name"><?php echo htmlspecialchars($driver->fullname); ?></h3>
                                <div class="driver-rating">
                                    <span class="star">★</span>
                                    <span class="rating"><?php echo $driver->averageRating ? number_format($driver->averageRating, 1) : ''; ?></span>
                                    <span class="reviews">(<?php echo $driver->age; ?> years)</span>
                                </div>
                                <p class="driver-description">
                                    <?php 
                                    if(!empty($driver->bio)) {
                                        echo htmlspecialchars(substr($driver->bio, 0, 100)) . (strlen($driver->bio) > 100 ? '...' : '');
                                    } else {
                                        echo 'Professional driver';
                                    }
                                    ?>
                                </p>
                                <div class="driver-actions">
                                    <button class="select-driver-btn">Select Driver</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
        
    <?php else: ?>
        <section class="drivers-section">
            <p>No drivers available at the moment.</p>
        </section>
    <?php endif; ?>
