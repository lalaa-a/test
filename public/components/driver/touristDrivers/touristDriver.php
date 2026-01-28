<?php
// DEV only: show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define APP_ROOT constant
define('APP_ROOT', dirname(__DIR__, 4) . '/app');

// Public base URL for assets/images (adjust if needed)
$BASE_URL = '<?php echo URL_ROOT; ?>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Drivers</title>
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/components/driver/touristDrivers/touristDriver.css">
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
        <h1 class="page-title">Tourist Drivers</h1>

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
                    <h3 class="filter-label">Tourist specialization</h3>
                    <label class="checkbox"><input type="checkbox"> Cultural Tours</label>
                    <label class="checkbox"><input type="checkbox"> Wildlife Safari</label>
                    <label class="checkbox"><input type="checkbox"> Beach Tours</label>
                    <label class="checkbox"><input type="checkbox"> Hill Country</label>
                    <label class="checkbox"><input type="checkbox"> Historical Sites</label>
                    <label class="checkbox"><input type="checkbox"> Tea Plantation Tours</label>
                    <label class="checkbox"><input type="checkbox"> Adventure Sports</label>
                    <label class="checkbox"><input type="checkbox"> Photography Tours</label>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Region coverage</h3>
                    <select class="filter-select">
                        <option>All Sri Lanka</option>
                        <option>Western Province</option>
                        <option>Central Province</option>
                        <option>Southern Province</option>
                        <option>Northern Province</option>
                        <option>Eastern Province</option>
                        <option>North Western Province</option>
                        <option>North Central Province</option>
                        <option>Uva Province</option>
                        <option>Sabaragamuwa Province</option>
                    </select>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Rating</h3>
                    <div class="rating-line">★ ★ ★ ★ ☆ <span class="rating-text">4.0+</span></div>
                </div>

                <div class="filter-group">
                    <h3 class="filter-label">Price range (per day)</h3>
                    <div class="price-range">
                        <span>$50</span>
                        <div class="range-bar"></div>
                        <span>$200</span>
                    </div>
                </div>
            </aside>

            <!-- Cards -->
            <section class="cards-grid">
                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-saman-perera-84.png" alt="Saman Perera">
                    </div>
                    <h3 class="profile-name">Saman Perera</h3>
                    <div class="profile-rating">★ 4.8 <span class="reviews">(156 reviews)</span></div>
                    <p class="profile-desc">Expert cultural tour guide with 15 years experience. Specialized in ancient temples and historical sites across Sri Lanka.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Cultural Tours</span>
                        <span class="badge specialization-badge">Historical Sites</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>

                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-jane-smith-123.png" alt="Ruwan Silva">
                    </div>
                    <h3 class="profile-name">Ruwan Silva</h3>
                    <div class="profile-rating">★ 4.9 <span class="reviews">(203 reviews)</span></div>
                    <p class="profile-desc">Wildlife safari expert and nature enthusiast. Perfect guide for national parks and wildlife photography tours.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Wildlife Safari</span>
                        <span class="badge specialization-badge">Photography Tours</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>

                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-john-doe-112.png" alt="Nimal Fernando">
                    </div>
                    <h3 class="profile-name">Nimal Fernando</h3>
                    <div class="profile-rating">★ 4.7 <span class="reviews">(89 reviews)</span></div>
                    <p class="profile-desc">Hill country specialist with deep knowledge of tea plantations, scenic routes, and mountain adventures.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Hill Country</span>
                        <span class="badge specialization-badge">Tea Tours</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>

                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-saman-perera-84.png" alt="Chaminda Rathnayake">
                    </div>
                    <h3 class="profile-name">Chaminda Rathnayake</h3>
                    <div class="profile-rating">★ 4.6 <span class="reviews">(112 reviews)</span></div>
                    <p class="profile-desc">Coastal tour expert specializing in southern beaches, fishing villages, and marine life experiences.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Beach Tours</span>
                        <span class="badge specialization-badge">Marine Life</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>

                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-jane-smith-123.png" alt="Kasun Mendis">
                    </div>
                    <h3 class="profile-name">Kasun Mendis</h3>
                    <div class="profile-rating">★ 4.8 <span class="reviews">(176 reviews)</span></div>
                    <p class="profile-desc">All-island tour guide with expertise in comprehensive Sri Lankan cultural and natural heritage tours.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Cultural Tours</span>
                        <span class="badge specialization-badge">Heritage Sites</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>

                <article class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/driver-john-doe-112.png" alt="Lakshan Perera">
                    </div>
                    <h3 class="profile-name">Lakshan Perera</h3>
                    <div class="profile-rating">★ 4.7 <span class="reviews">(134 reviews)</span></div>
                    <p class="profile-desc">Adventure sports specialist and mountain guide. Expert in hiking, rock climbing, and extreme sports tours.</p>
                    <div class="tourist-badges">
                        <span class="badge tourist-badge">Adventure Sports</span>
                        <span class="badge specialization-badge">Mountain Guide</span>
                    </div>
                    <button class="select-driver-btn">Select Driver</button>
                </article>
            </section>
        </div>
    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script src="<?php echo URL_ROOT; ?>/public/components/driver/touristDrivers/touristDriver.js"></script>
</body>
</html>
