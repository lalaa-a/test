<?php
// DEV only: show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Public base URL for assets/images (adjust if needed)
$BASE_URL = '/test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Plan Your Way Through Sri-Lanka\'s Wonders - Guides'; ?></title>
    
    <!-- Guide page specific styles -->
    <link rel="stylesheet" href="/test/public/components/driver/driver.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>
</head>
<body>
    <!-- Navigation -->
    <?php renderComponent('inc','navigation',[]); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section" style="margin-bottom:40px; margin-top:0;">
            <div class="hero-destinations-grid">
                <article class="destination-card" tabindex="0" aria-label="Ella, lush green hills and scenic train rides">
                    <div class="destination-bg" style="background-image:url('/test/public/components/driver/images/img01.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Ella</h2>
                        <p class="destination-subtitle">Green hills & scenic train rides</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Galle, historic fort and coastal charm">
                    <div class="destination-bg" style="background-image:url('/test/public/components/driver/images/img02.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Galle</h2>
                        <p class="destination-subtitle">Historic fort and coastal charm</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Yala, wildlife safaris and national park">
                    <div class="destination-bg" style="background-image:url('/test/public/components/driver/images/img03.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Yala</h2>
                        <p class="destination-subtitle">Wildlife safaris & national parks</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Sigiriya, ancient rock fortress and panoramic views">
                    <div class="destination-bg" style="background-image:url('/test/public/components/driver/images/img04.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Sigiriya</h2>
                        <p class="destination-subtitle">Rock fort & views</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Trending Guides Section -->
        <section class="drivers-section">
            <h2 class="section-title">Trending Guides</h2>
            <div class="drivers-row">
                <div class="drivers-container">
                    <?php if(isset($trendingGuides) && !empty($trendingGuides)): ?>
                        <?php foreach($trendingGuides as $guide): ?>
                        <div class="driver-card">
                            <?php if($guide->badge_type !== 'none'): ?>
                                <div class="driver-badge <?php echo $guide->badge_type; ?>">
                                    <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                                </div>
                            <?php endif; ?>
                            <div class="driver-avatar">
                                <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                            </div>
                            <div class="driver-info">
                                <h3 class="driver-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                                <div class="driver-rating">
                                    <span class="star">★</span>
                                    <span class="rating"><?php echo $guide->rating; ?></span>
                                    <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                                </div>
                                <p class="driver-description">
                                    <?php echo htmlspecialchars($guide->description); ?>
                                </p>
                                <button class="select-driver-btn">Select Guide</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No trending guides available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- All Guides Section -->
        <section class="drivers-section">
            <h2 class="section-title">All Guides</h2>
            
            <!-- Licensed Guides Subsection -->
            <h3 class="subsection-title">Licensed Guides</h3>
            <div class="drivers-container-grid">
                <?php if(isset($licensedGuides) && !empty($licensedGuides)): ?>
                    <?php foreach(array_slice($licensedGuides, 0, 6) as $guide): ?>
                    <div class="driver-card">
                        <?php if($guide->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $guide->badge_type; ?>">
                                <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $guide->rating; ?></span>
                                <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($guide->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Guide</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='/test/guide/licensed'">
                See More
                <img src="/test/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>

            <!-- Reviewed Guides Subsection -->
            <h3 class="subsection-title">Reviewed Guides</h3>
            <div class="drivers-container-grid">
                <?php if(isset($reviewedGuides) && !empty($reviewedGuides)): ?>
                    <?php foreach(array_slice($reviewedGuides, 0, 6) as $guide): ?>
                    <div class="driver-card">
                        <?php if($guide->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $guide->badge_type; ?>">
                                <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $guide->rating; ?></span>
                                <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($guide->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Guide</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='/test/guide/reviewed'">
                See More
                <img src="/test/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>

            <!-- Tourist Guides Subsection -->
            <h3 class="subsection-title">Tourist Guides</h3>
            <div class="drivers-container-grid">
                <?php if(isset($touristGuides) && !empty($touristGuides)): ?>
                    <?php foreach(array_slice($touristGuides, 0, 6) as $guide): ?>
                    <div class="driver-card">
                        <?php if($guide->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $guide->badge_type; ?>">
                                <?php echo ($guide->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($guide->image_url); ?>" alt="<?php echo htmlspecialchars($guide->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($guide->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $guide->rating; ?></span>
                                <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($guide->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Guide</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='/test/guide/tourist'">
                See More
                <img src="/test/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>
        </section>
    </main>

    <!-- Footer -->
    <?php renderComponent('inc','footer',[]); ?>

    <script src="/test/public/components/driver/driver.js"></script>
</body>
</html>
