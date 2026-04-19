
<?php
$selectedFilterKey = isset($selectedFilter) ? (string)$selectedFilter : 'all';
$navigableFilters = [];

if (isset($mainFilters) && is_array($mainFilters)) {
    foreach ($mainFilters as $filterKey => $filter) {
        if ($filterKey === 'all' || !empty($filter['accounts'])) {
            $navigableFilters[$filterKey] = $filter;
        }
    }
}

if (!array_key_exists($selectedFilterKey, $navigableFilters)) {
    $selectedFilterKey = 'all';
}
?>

<div id="driversExploreTop">
    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Driver</h1>
        <p class="search-subtitle">Discover trusted drivers for your Sri Lankan adventure</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="driverSearch"
                    placeholder="Search drivers by name, location, or expertise..."
                    autocomplete="off"
                >
                <button class="search-icon" id="driverSearchButton" type="button" aria-label="Search drivers">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="search-filters" id="driverFilterNav">
            <details class="filter-dropdown" id="filterToggle">
                <summary class="filter-icon" aria-label="Open driver filter list">
                    <i class="fas fa-filter"></i>
                    Filter
                </summary>
                <div class="filter-dropdown-menu">
                    <p class="filter-dropdown-title">Jump to section</p>
                    <?php foreach($navigableFilters as $filterKey => $filter): ?>
                        <?php $sectionId = 'driver-section-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)$filterKey); ?>
                        <a class="filter-dropdown-item <?php echo $filterKey === $selectedFilterKey ? 'active' : ''; ?>"
                           href="#<?php echo htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8'); ?>"
                           data-category="<?php echo htmlspecialchars((string)$filterKey, ENT_QUOTES, 'UTF-8'); ?>"
                           onclick="this.closest('details').removeAttribute('open');">
                            <?php echo htmlspecialchars($filter['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>

            <?php foreach($navigableFilters as $filterKey => $filter): ?>
                <?php $sectionId = 'driver-section-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)$filterKey); ?>
                <a class="filter-chip <?php echo $filterKey === $selectedFilterKey ? 'active' : ''; ?>"
                   href="#<?php echo htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8'); ?>"
                   data-category="<?php echo htmlspecialchars((string)$filterKey, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($filter['name']); ?> (<?php echo (int)$filter['count']; ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <?php if(!empty($navigableFilters)): ?>
        <?php foreach($navigableFilters as $filterKey => $filter): ?>
            <?php $sectionId = 'driver-section-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)$filterKey); ?>

            <!-- <?php echo htmlspecialchars($filter['name']); ?> Section -->
            <section class="drivers-section"
                     id="<?php echo htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8'); ?>"
                     data-filter="<?php echo htmlspecialchars((string)$filterKey, ENT_QUOTES, 'UTF-8'); ?>">
                <h2 class="section-title"><?php echo htmlspecialchars($filter['name']); ?> (<?php echo (int)$filter['count']; ?>)</h2>

                <div class="<?php echo $filterKey === 'all' ? 'drivers-container' : 'drivers-container-grid'; ?>">
                    <?php if($filterKey !== 'all'): ?>
                        <button class="see-more-arrow"
                                data-category="<?php echo htmlspecialchars((string)$filterKey, ENT_QUOTES, 'UTF-8'); ?>"
                                title="See More <?php echo htmlspecialchars($filter['name']); ?>">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    <?php endif; ?>

                    <?php
                    $displayDrivers = ($filterKey === 'all') ? $filter['accounts'] : array_slice($filter['accounts'], 0, 6);
                    foreach($displayDrivers as $driver):
                        $searchBlob = strtolower(trim(implode(' ', [
                            (string)($driver->fullname ?? ''),
                            (string)($driver->bio ?? ''),
                            (string)($driver->address ?? ''),
                            (string)($driver->languages ?? '')
                        ])));
                    ?>
                        <div
                            class="driver-card"
                            data-user-id="<?php echo htmlspecialchars($driver->userId); ?>"
                            data-search="<?php echo htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8'); ?>"
                        >
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
                                    <button class="select-driver-btn" onclick="window.location.href='<?php echo URL_ROOT . '/RegUser/driverVisibleProfile/' . $driver->userId; ?>'">Select Driver</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <div class="no-results" id="driverNoResults" style="display: none;">
            <div class="no-results-icon"><i class="fas fa-search"></i></div>
            <h3 class="no-results-title">No drivers found</h3>
            <p class="no-results-text">Try another search word or filter category.</p>
        </div>
    <?php else: ?>
        <section class="drivers-section">
            <p>No drivers available at the moment.</p>
        </section>
    <?php endif; ?>
</div>
