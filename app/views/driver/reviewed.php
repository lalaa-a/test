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
    <title>Reviewed Drivers</title>
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
        <h1 class="page-title">Reviewed Drivers</h1>

        <div class="frame13-layout">
            <!-- Filters / Aside -->
            <aside class="filters">
                <h2 class="filters-title">Filter By</h2>

                <div class="filter-group">
                    <h3 class="filter-label">Vehicle type</h3>
                    <label class="checkbox"><input type="checkbox"> Child Seats</label>
                    <label class="checkbox"><input type="checkbox"> Minicar (max 2)</label>
                    <label class="checkbox"><input type="checkbox"> Normal car (max 3)</label>
                    <label class="checkbox"><input type="checkbox"> SUV (max 3)</label>
                    <label class="checkbox"><input type="checkbox"> Large car (max 4)</label>
                    <label class="checkbox"><input type="checkbox"> Mini van (max 6)</label>
                    <label class="checkbox"><input type="checkbox"> Large van (max 10)</label>
                    <label class="checkbox"><input type="checkbox"> Mini bus (max 15)</label>
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
                <?php if(isset($data['drivers']) && !empty($data['drivers'])): ?>
                    <?php foreach($data['drivers'] as $driver): ?>
                        <article class="profile-card">
                            <div class="profile-avatar">
                                <img src="<?php echo $driver->image_url; ?>" alt="<?php echo $driver->name; ?>">
                            </div>
                            <h3 class="profile-name"><?php echo $driver->name; ?></h3>
                            <div class="profile-rating">★ <?php echo $driver->rating; ?> <span class="reviews">(<?php echo $driver->total_reviews; ?> reviews)</span></div>
                            <p class="profile-desc"><?php echo $driver->description; ?></p>
                            <button class="select-driver-btn">Select Driver</button>
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
                        <p class="profile-desc">Friendly and reliable driver with a spacious vehicle. Safety is my priority.</p>
                        <button class="select-driver-btn">Select Driver</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.</p>
                        <button class="select-driver-btn">Select Driver</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/fae394ceb9cdbaf029e13c0b4d17726dad1b4291.png" alt="Kumar Fernando">
                        </div>
                        <h3 class="profile-name">Kumar Fernando</h3>
                        <div class="profile-rating">★ 5.0 <span class="reviews">(210 reviews)</span></div>
                        <p class="profile-desc">Your local guide on wheels! I'll not only drive you, but also share stories about our beautiful country.</p>
                        <button class="select-driver-btn">Select Driver</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png" alt="Jane Smith">
                        </div>
                        <h3 class="profile-name">Jane Smith</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(98 reviews)</span></div>
                        <p class="profile-desc">Friendly and reliable driver with a spacious vehicle. Safety is my priority.</p>
                        <button class="select-driver-btn">Select Driver</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.</p>
                        <button class="select-driver-btn">Select Driver</button>
                    </article>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script src="/test/public/components/driver/reviewedDrivers/reviewedDriver.js"></script>
</body>
</html>
