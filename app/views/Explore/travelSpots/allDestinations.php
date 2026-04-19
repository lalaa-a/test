<?php
$filterMeta = isset($filterMeta) && is_array($filterMeta) ? $filterMeta : [];
$visibleSpots = isset($visibleSpots) && is_array($visibleSpots) ? $visibleSpots : [];
$provinces = isset($provinces) && is_array($provinces) ? $provinces : [];

$selectedFilter = isset($selectedFilter) ? (string)$selectedFilter : 'all';
$selectedProvince = isset($selectedProvince) ? (string)$selectedProvince : 'all';

if (!array_key_exists($selectedFilter, $filterMeta)) {
    $selectedFilter = 'all';
}

if ($selectedProvince !== 'all' && !in_array($selectedProvince, $provinces, true)) {
    $selectedProvince = 'all';
}

$activeFilterName = isset($filterMeta[$selectedFilter]['name'])
    ? (string)$filterMeta[$selectedFilter]['name']
    : 'All Destinations';

$buildDestinationFilterUrl = function ($filter, $province) {
    $query = ['filter' => (string)$filter];
    if ((string)$province !== 'all') {
        $query['province'] = (string)$province;
    }

    return URL_ROOT . '/RegUser/destinations?' . http_build_query($query);
};
?>

<div class="destinations-page">
    <section class="search-section">
        <h1 class="search-title">Discover Sri Lanka's Travel Spots</h1>
        <p class="search-subtitle">Filter destinations, keep loading static, and open full details for each spot.</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="destinationSearch"
                    placeholder="Search by name, district, province or tag..."
                    autocomplete="off"
                >
                <button class="search-icon" id="searchButton" type="button" aria-label="Search travel spots">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="search-filters" id="destinationFilters">
            <details class="filter-dropdown">
                <summary class="filter-toggle" aria-label="Open destination filter list">
                    <i class="fas fa-filter"></i>
                    Filters
                </summary>
                <div class="filter-dropdown-menu">
                    <p class="filter-dropdown-title">Main categories</p>
                    <?php foreach ($filterMeta as $filterKey => $meta): ?>
                        <?php $filterUrl = $buildDestinationFilterUrl((string)$filterKey, $selectedProvince); ?>
                        <a
                            class="filter-dropdown-item <?php echo (string)$filterKey === $selectedFilter ? 'active' : ''; ?>"
                            href="<?php echo htmlspecialchars($filterUrl, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                            <?php echo htmlspecialchars((string)$meta['name'], ENT_QUOTES, 'UTF-8'); ?>
                            <span>(<?php echo (int)$meta['count']; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>

            <div class="filter-chip-row">
                <?php foreach ($filterMeta as $filterKey => $meta): ?>
                    <?php $chipUrl = $buildDestinationFilterUrl((string)$filterKey, $selectedProvince); ?>
                    <a
                        class="filter-chip <?php echo (string)$filterKey === $selectedFilter ? 'active' : ''; ?>"
                        href="<?php echo htmlspecialchars($chipUrl, ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        <?php echo htmlspecialchars((string)$meta['name'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo (int)$meta['count']; ?>)
                    </a>
                <?php endforeach; ?>
            </div>

            <form id="provinceFilterForm" class="province-filter-form" method="GET" action="<?php echo URL_ROOT; ?>/RegUser/destinations">
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($selectedFilter, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="provinceFilter" class="province-filter-label">Province</label>
                <select id="provinceFilter" name="province" class="province-filter-select">
                    <option value="all" <?php echo $selectedProvince === 'all' ? 'selected' : ''; ?>>All Provinces</option>
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?php echo htmlspecialchars((string)$province, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (string)$province === $selectedProvince ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string)$province, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <p class="search-results-info" id="searchResultsInfo">
            Showing <?php echo count($visibleSpots); ?> destination(s) in <?php echo htmlspecialchars($activeFilterName, ENT_QUOTES, 'UTF-8'); ?>
            <?php if ($selectedProvince !== 'all'): ?>
                (<?php echo htmlspecialchars($selectedProvince, ENT_QUOTES, 'UTF-8'); ?>)
            <?php endif; ?>
        </p>
    </section>

    <section class="spots-section" id="travelSpotGridSection">
        <?php if (!empty($visibleSpots)): ?>
            <div class="spots-grid" id="travelSpotGrid">
                <?php foreach ($visibleSpots as $spot): ?>
                    <?php
                    $spotId = isset($spot['spotId']) ? (int)$spot['spotId'] : 0;
                    if ($spotId <= 0) {
                        continue;
                    }

                    $spotName = trim((string)($spot['spotName'] ?? 'Unknown destination'));
                    $overview = trim((string)($spot['overview'] ?? 'No overview available.'));
                    $overviewPreview = strlen($overview) > 160 ? substr($overview, 0, 157) . '...' : $overview;
                    $province = trim((string)($spot['province'] ?? ''));
                    $district = trim((string)($spot['district'] ?? ''));
                    $bestTimeFrom = trim((string)($spot['bestTimeFrom'] ?? ''));
                    $bestTimeTo = trim((string)($spot['bestTimeTo'] ?? ''));
                    $visitingDurationMin = isset($spot['visitingDurationMin']) ? (int)$spot['visitingDurationMin'] : null;
                    $visitingDurationMax = isset($spot['visitingDurationMax']) ? (int)$spot['visitingDurationMax'] : null;
                    $averageRating = isset($spot['averageRating']) ? (float)$spot['averageRating'] : 0.0;
                    $totalReviews = isset($spot['totalReviews']) ? (int)$spot['totalReviews'] : 0;
                    $subFilterNames = isset($spot['subFilters']) && is_array($spot['subFilters']) ? array_values($spot['subFilters']) : [];

                    $photoPath = '';
                    if (isset($spot['photoPaths']) && is_array($spot['photoPaths'])) {
                        foreach ($spot['photoPaths'] as $candidatePhotoPath) {
                            $candidatePhotoPath = trim((string)$candidatePhotoPath);
                            if ($candidatePhotoPath !== '') {
                                $photoPath = $candidatePhotoPath;
                                break;
                            }
                        }
                    }

                    if ($photoPath !== '') {
                        if (strpos($photoPath, '/public/uploads') === 0) {
                            $photoUrl = URL_ROOT . $photoPath;
                        } else {
                            $photoUrl = URL_ROOT . '/public/uploads/' . ltrim($photoPath, '/');
                        }
                    } else {
                        $photoUrl = URL_ROOT . '/public/img/explore/destinations/hero1.jpg';
                    }

                    $cardSearchBlob = strtolower(trim(implode(' ', [
                        $spotName,
                        $overview,
                        $province,
                        $district,
                        implode(' ', $subFilterNames)
                    ])));

                    $bestTimeLabel = '';
                    if ($bestTimeFrom !== '' && $bestTimeTo !== '') {
                        $bestTimeLabel = $bestTimeFrom . ' - ' . $bestTimeTo;
                    } elseif ($bestTimeFrom !== '') {
                        $bestTimeLabel = $bestTimeFrom;
                    }

                    $durationLabel = '';
                    if ($visitingDurationMin !== null && $visitingDurationMax !== null && $visitingDurationMin > 0 && $visitingDurationMax > 0) {
                        if ($visitingDurationMin === $visitingDurationMax) {
                            $durationLabel = $visitingDurationMax . ' h';
                        } else {
                            $durationLabel = $visitingDurationMin . '-' . $visitingDurationMax . ' h';
                        }
                    } elseif ($visitingDurationMax !== null && $visitingDurationMax > 0) {
                        $durationLabel = $visitingDurationMax . ' h';
                    }
                    ?>

                    <a
                        class="spot-card"
                        href="<?php echo URL_ROOT; ?>/RegUser/destination/<?php echo $spotId; ?>"
                        data-search="<?php echo htmlspecialchars($cardSearchBlob, ENT_QUOTES, 'UTF-8'); ?>"
                        data-province="<?php echo htmlspecialchars(strtolower($province), ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        <div class="spot-card-image-wrap">
                            <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($spotName, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="spot-card-content">
                            <h3 class="spot-title"><?php echo htmlspecialchars($spotName, ENT_QUOTES, 'UTF-8'); ?></h3>

                            <div class="spot-meta-row">
                                <?php if ($province !== ''): ?>
                                    <span class="spot-badge"><?php echo htmlspecialchars($province, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <?php if ($district !== ''): ?>
                                    <span class="spot-badge"><?php echo htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>

                            <p class="spot-description">
                                <?php echo htmlspecialchars($overviewPreview, ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <div class="spot-rating-row">
                                <span class="star">★</span>
                                <span class="rating-value"><?php echo number_format($averageRating, 1); ?></span>
                                <span class="review-count">(<?php echo $totalReviews; ?> reviews)</span>
                            </div>

                            <div class="spot-detail-row">
                                <?php if ($bestTimeLabel !== ''): ?>
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($bestTimeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <?php if ($durationLabel !== ''): ?>
                                    <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($durationLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($subFilterNames)): ?>
                                <div class="spot-tag-row">
                                    <?php foreach (array_slice($subFilterNames, 0, 3) as $tagName): ?>
                                        <span class="spot-tag"><?php echo htmlspecialchars((string)$tagName, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" id="spotNoResults">
                <h3>No travel spots found for this filter</h3>
                <p>Try another category or province from the filters above.</p>
            </div>
        <?php endif; ?>

        <div class="empty-state" id="spotNoResultsClient" style="display: none;">
            <h3>No travel spots match your search</h3>
            <p>Try a different keyword or clear search text.</p>
        </div>
    </section>
</div>
