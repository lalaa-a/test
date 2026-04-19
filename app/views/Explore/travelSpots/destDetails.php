<?php
$spotData = isset($travelSpotData) && is_array($travelSpotData) ? $travelSpotData : [];
$mainDetailsRaw = $spotData['mainDetails'] ?? null;
$mainDetails = is_object($mainDetailsRaw) ? (array)$mainDetailsRaw : (is_array($mainDetailsRaw) ? $mainDetailsRaw : []);
$photos = isset($spotData['photos']) && is_array($spotData['photos']) ? $spotData['photos'] : [];
$filters = isset($spotData['filters']) && is_array($spotData['filters']) ? $spotData['filters'] : [];
$itinerary = isset($spotData['itinerary']) && is_array($spotData['itinerary']) ? $spotData['itinerary'] : [];
$nearbySpots = isset($spotData['nearbySpots']) && is_array($spotData['nearbySpots']) ? $spotData['nearbySpots'] : [];

$getValue = static function ($source, $key, $default = null) {
    if (is_array($source) && array_key_exists($key, $source)) {
        return $source[$key];
    }

    if (is_object($source) && isset($source->$key)) {
        return $source->$key;
    }

    return $default;
};

$requestedSpotId = isset($spotId) ? (int)$spotId : 0;
$resolvedSpotId = (int)$getValue($mainDetails, 'spotId', $requestedSpotId);
$spotName = trim((string)$getValue($mainDetails, 'spotName', 'Travel Spot'));
$overview = trim((string)$getValue($mainDetails, 'overview', ''));
$province = trim((string)$getValue($mainDetails, 'province', ''));
$district = trim((string)$getValue($mainDetails, 'district', ''));
$bestTimeFrom = trim((string)$getValue($mainDetails, 'bestTimeFrom', ''));
$bestTimeTo = trim((string)$getValue($mainDetails, 'bestTimeTo', ''));
$visitingDurationMin = $getValue($mainDetails, 'visitingDurationMin', null);
$visitingDurationMax = $getValue($mainDetails, 'visitingDurationMax', null);
$ticketPriceLocal = $getValue($mainDetails, 'ticketPriceLocal', null);
$ticketPriceForeigner = $getValue($mainDetails, 'ticketPriceForeigner', null);
$openingHours = trim((string)$getValue($mainDetails, 'openingHours', ''));
$ticketDetails = trim((string)$getValue($mainDetails, 'ticketDetails', ''));
$parkingDetails = trim((string)$getValue($mainDetails, 'parkingDetails', ''));
$accessibility = trim((string)$getValue($mainDetails, 'accessibility', ''));
$facilities = trim((string)$getValue($mainDetails, 'facilities', ''));
$travelerTips = trim((string)$getValue($mainDetails, 'travelerTips', ''));
$averageRating = (float)$getValue($mainDetails, 'averageRating', 0);
$totalReviews = (int)$getValue($mainDetails, 'totalReviews', 0);

$toPhotoUrl = static function ($photoPath) {
    $photoPath = trim((string)$photoPath);
    if ($photoPath === '') {
        return '';
    }

    if (strpos($photoPath, '/public/uploads') === 0) {
        return URL_ROOT . $photoPath;
    }

    return URL_ROOT . '/public/uploads/' . ltrim($photoPath, '/');
};

$photoUrls = [];
foreach ($photos as $photo) {
    $photoPath = (string)$getValue($photo, 'photoPath', '');
    $photoUrl = $toPhotoUrl($photoPath);
    if ($photoUrl !== '' && !in_array($photoUrl, $photoUrls, true)) {
        $photoUrls[] = $photoUrl;
    }
}

if (empty($photoUrls)) {
    $photoUrls[] = URL_ROOT . '/public/img/explore/destinations/hero1.jpg';
}

$tagNames = [];
foreach ($filters as $filterGroup) {
    $mainFilterName = trim((string)$getValue($filterGroup, 'mainFilterName', ''));
    if ($mainFilterName !== '') {
        $tagNames[$mainFilterName] = $mainFilterName;
    }

    $subFilters = $getValue($filterGroup, 'subFilters', []);
    if (is_array($subFilters)) {
        foreach ($subFilters as $subFilter) {
            $subFilterName = trim((string)$getValue($subFilter, 'subFilterName', ''));
            if ($subFilterName !== '') {
                $tagNames[$subFilterName] = $subFilterName;
            }
        }
    }
}

$spotExists = !empty($mainDetails);
$bestTimeLabel = '';
if ($bestTimeFrom !== '' && $bestTimeTo !== '') {
    $bestTimeLabel = $bestTimeFrom . ' - ' . $bestTimeTo;
} elseif ($bestTimeFrom !== '') {
    $bestTimeLabel = $bestTimeFrom;
}

$durationLabel = '';
if (is_numeric($visitingDurationMin) && is_numeric($visitingDurationMax)) {
    $visitingDurationMin = (int)$visitingDurationMin;
    $visitingDurationMax = (int)$visitingDurationMax;

    if ($visitingDurationMin > 0 && $visitingDurationMax > 0) {
        if ($visitingDurationMin === $visitingDurationMax) {
            $durationLabel = $visitingDurationMax . ' hour(s)';
        } else {
            $durationLabel = $visitingDurationMin . '-' . $visitingDurationMax . ' hour(s)';
        }
    }
} elseif (is_numeric($visitingDurationMax) && (int)$visitingDurationMax > 0) {
    $durationLabel = ((int)$visitingDurationMax) . ' hour(s)';
}
?>

<div class="destination-detail-page">
    <a class="back-link" href="<?php echo URL_ROOT; ?>/RegUser/destinations">
        <i class="fas fa-arrow-left"></i>
        Back to destinations
    </a>

    <?php if (!$spotExists): ?>
        <section class="detail-empty-state">
            <h2>Destination Not Found</h2>
            <p>We could not find details for this travel spot.</p>
        </section>
    <?php else: ?>
        <header class="detail-header">
            <div>
                <h1 class="detail-title"><?php echo htmlspecialchars($spotName, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="detail-location">
                    <?php echo htmlspecialchars(trim($district . ($district !== '' && $province !== '' ? ', ' : '') . $province), ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <div class="detail-rating-row">
                    <span class="star">★</span>
                    <span class="rating-value"><?php echo number_format($averageRating, 1); ?></span>
                    <span class="review-count">(<?php echo $totalReviews; ?> review(s))</span>
                </div>
            </div>
            <div class="quick-meta">
                <?php if ($bestTimeLabel !== ''): ?>
                    <span><i class="fas fa-calendar-alt"></i> Best time: <?php echo htmlspecialchars($bestTimeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if ($durationLabel !== ''): ?>
                    <span><i class="fas fa-clock"></i> Duration: <?php echo htmlspecialchars($durationLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
        </header>

        <section class="photo-gallery-panel">
            <div class="main-photo-wrap">
                <img id="mainSpotPhoto" src="<?php echo htmlspecialchars($photoUrls[0], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($spotName, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <?php if (count($photoUrls) > 1): ?>
                <div class="photo-thumbs" id="spotPhotoThumbs">
                    <?php foreach ($photoUrls as $photoIndex => $photoUrl): ?>
                        <button
                            type="button"
                            class="photo-thumb-btn <?php echo $photoIndex === 0 ? 'active' : ''; ?>"
                            data-photo-url="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            aria-label="Show photo <?php echo $photoIndex + 1; ?>"
                        >
                            <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Photo <?php echo $photoIndex + 1; ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="detail-layout">
            <article class="detail-panel">
                <h2>Overview</h2>
                <p class="detail-paragraph">
                    <?php echo htmlspecialchars($overview !== '' ? $overview : 'No overview available yet.', ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <?php if (!empty($tagNames)): ?>
                    <div class="tag-list">
                        <?php foreach ($tagNames as $tagName): ?>
                            <span class="tag-chip"><?php echo htmlspecialchars((string)$tagName, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>

            <article class="detail-panel">
                <h2>Practical Information</h2>
                <ul class="detail-list">
                    <?php if ($openingHours !== ''): ?>
                        <li><strong>Opening Hours:</strong> <?php echo htmlspecialchars($openingHours, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                    <?php if ($ticketPriceLocal !== null): ?>
                        <li><strong>Ticket (Local):</strong> LKR <?php echo number_format((float)$ticketPriceLocal, 2); ?></li>
                    <?php endif; ?>
                    <?php if ($ticketPriceForeigner !== null): ?>
                        <li><strong>Ticket (Foreigner):</strong> LKR <?php echo number_format((float)$ticketPriceForeigner, 2); ?></li>
                    <?php endif; ?>
                    <?php if ($ticketDetails !== ''): ?>
                        <li><strong>Ticket Details:</strong> <?php echo htmlspecialchars($ticketDetails, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                    <?php if ($parkingDetails !== ''): ?>
                        <li><strong>Parking:</strong> <?php echo htmlspecialchars($parkingDetails, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                    <?php if ($accessibility !== ''): ?>
                        <li><strong>Accessibility:</strong> <?php echo htmlspecialchars($accessibility, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                    <?php if ($facilities !== ''): ?>
                        <li><strong>Facilities:</strong> <?php echo htmlspecialchars($facilities, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                    <?php if ($travelerTips !== ''): ?>
                        <li><strong>Traveller Tips:</strong> <?php echo htmlspecialchars($travelerTips, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                </ul>
            </article>
        </section>

        <?php if (!empty($itinerary)): ?>
            <section class="detail-panel">
                <h2>Itinerary Points</h2>
                <ol class="itinerary-list">
                    <?php foreach ($itinerary as $point): ?>
                        <?php
                        $pointName = trim((string)$getValue($point, 'pointName', 'Unnamed point'));
                        $latitude = trim((string)$getValue($point, 'latitude', ''));
                        $longitude = trim((string)$getValue($point, 'longitude', ''));
                        ?>
                        <li>
                            <span class="point-name"><?php echo htmlspecialchars($pointName, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if ($latitude !== '' && $longitude !== ''): ?>
                                <span class="point-coordinates">(<?php echo htmlspecialchars($latitude, ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($longitude, ENT_QUOTES, 'UTF-8'); ?>)</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>
        <?php endif; ?>

        <?php if (!empty($nearbySpots)): ?>
            <section class="detail-panel">
                <h2>Nearby Spots</h2>
                <div class="nearby-list">
                    <?php foreach ($nearbySpots as $nearbySpot): ?>
                        <?php
                        $nearbySpotId = (int)$getValue($nearbySpot, 'spotId', 0);
                        $nearbySpotName = trim((string)$getValue($nearbySpot, 'spotName', 'Nearby Spot'));
                        if ($nearbySpotId <= 0) {
                            continue;
                        }
                        ?>
                        <a class="nearby-chip" href="<?php echo URL_ROOT; ?>/RegUser/destination/<?php echo $nearbySpotId; ?>">
                            <?php echo htmlspecialchars($nearbySpotName, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</div>
