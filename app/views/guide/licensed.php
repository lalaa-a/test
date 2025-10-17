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
    <title>Licensed Guides</title>
    <link rel="stylesheet" href="/test/public/components/driver/reviewedDrivers/reviewedDriver.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    
    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>
</head>
<body>
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <main class="main-content">
        <h1 class="page-title">Licensed Guides</h1>

        <div class="frame13-layout">
            <!-- Filters / Aside -->
            <aside class="filters">
                <h2 class="filters-title">Filter By</h2>

                <div class="filter-group">
                    <h3 class="filter-label">Specialization</h3>
                    <label class="checkbox"><input type="checkbox"> Cultural Tours</label>
                    <label class="checkbox"><input type="checkbox"> Historical Sites</label>
                    <label class="checkbox"><input type="checkbox"> Wildlife Safari</label>
                    <label class="checkbox"><input type="checkbox"> Beach Tours</label>
                    <label class="checkbox"><input type="checkbox"> Hill Country</label>
                    <label class="checkbox"><input type="checkbox"> Tea Plantation Tours</label>
                    <label class="checkbox"><input type="checkbox"> Adventure Sports</label>
                    <label class="checkbox"><input type="checkbox"> Photography Tours</label>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Language spoken</h3>
                    <select class="filter-select">
                        <option>English</option>
                        <option>Sinhala</option>
                        <option>Tamil</option>
                    </select>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Region coverage</h3>
                    <select class="filter-select">
                        <option>Colombo</option>
                        <option>Kandy</option>
                        <option>Galle</option>
                        <option>Jaffna</option>
                    </select>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Rating</h3>
                    <div class="rating-line">★ ★ ★ ★ ☆ <span class="rating-text">4.0+</span></div>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Price range</h3>
                    <div class="price-range">
                        <span>$10</span>
                        <div class="range-bar"></div>
                        <span>$500</span>
                    </div>
                </div>
            </aside>

            <!-- Cards -->
            <section class="cards-grid">
                <?php if(isset($data['guides']) && !empty($data['guides'])): ?>
                    <?php foreach($data['guides'] as $guide): ?>
                        <article class="profile-card">
                            <div class="profile-avatar">
                                <img src="<?php echo $guide->image_url; ?>" alt="<?php echo $guide->name; ?>">
                            </div>
                            <h3 class="profile-name"><?php echo $guide->name; ?></h3>
                            <div class="profile-rating">★ <?php echo $guide->rating; ?> <span class="reviews">(<?php echo $guide->total_reviews; ?> reviews)</span></div>
                            <p class="profile-desc"><?php echo $guide->description; ?></p>
                            <button class="select-driver-btn">Select Guide</button>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback to static content when no dynamic data is available -->
                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png" alt="Jane Smith">
                        </div>
                        <h3 class="profile-name">Jane Smith</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(98 reviews)</span></div>
                        <p class="profile-desc">Expert cultural guide with deep knowledge of Sri Lankan heritage and traditions.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Licensed historical tour guide specializing in ancient sites and archaeological wonders.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/fae394ceb9cdbaf029e13c0b4d17726dad1b4291.png" alt="Kumar Fernando">
                        </div>
                        <h3 class="profile-name">Kumar Fernando</h3>
                        <div class="profile-rating">★ 5.0 <span class="reviews">(210 reviews)</span></div>
                        <p class="profile-desc">Wildlife expert and nature guide with extensive knowledge of Sri Lankan fauna and flora.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png" alt="Jane Smith">
                        </div>
                        <h3 class="profile-name">Jane Smith</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(98 reviews)</span></div>
                        <p class="profile-desc">Expert cultural guide with deep knowledge of Sri Lankan heritage and traditions.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Licensed historical tour guide specializing in ancient sites and archaeological wonders.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script src="/test/public/components/driver/reviewedDrivers/reviewedDriver.js"></script>
</body>
</html>
