<?php
// DEV only: show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Public base URL for assets/images (adjust if needed)
$BASE_URL = '<?php echo URL_ROOT; ?>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewed Guides</title>
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/components/driver/reviewedDrivers/reviewedDriver.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    
    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>
            
        /* Search Section */
        .search-section {
            margin-bottom: 40px;
            text-align: center;
            padding: 30px 0;
        }
        
        .search-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #111827;
            margin-bottom: 12px;
        }
        
        .search-subtitle {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input-wrapper {
            position: relative;
            background: white;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .search-input-wrapper:focus-within {
            border-color: #006a71;
            box-shadow: 0 8px 32px rgba(0, 106, 113, 0.2);
            transform: translateY(-2px);
        }
        
        .search-input {
            width: 100%;
            padding: 18px 60px 18px 24px;
            border: none;
            border-radius: 50px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            background: transparent;
            outline: none;
        }
        
        .search-input::placeholder {
            color: #9ca3af;
        }
        
        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background:#ffffff;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .search-icon:hover {
            background: #005a61;
            transform: translateY(-50%) scale(1.05);
        }
        
        .search-filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .filter-chip {
            background: #f3f4f6;
            color: #374151;
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .filter-chip:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }
        
        .filter-chip.active {
            background: #006a71;
            color: white;
        }
        
        .filter-chip.active:hover {
            background: #005a61;
        }
        
        .search-results-info {
            margin-top: 20px;
            color: #6b7280;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .no-results-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .no-results-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 8px;
        }
        
        .no-results-text {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
        }
        
        /* Search Results Highlighting */
        .highlight {
            background: rgba(255, 235, 59, 0.4);
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        @media (max-width: 768px) {
            .search-title {
                font-size: 24px;
            }
            
            .search-container {
                max-width: 90%;
            }
            
            .search-input {
                padding: 16px 50px 16px 20px;
                font-size: 15px;
            }
            
            .search-icon {
                width: 35px;
                height: 35px;
                right: 15px;
            }
            
            .search-filters {
                gap: 8px;
            }
            
            .filter-chip {
                padding: 6px 12px;
                font-size: 13px;
            }
        }

</head>
<body>
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <main class="main-content">
                <!-- Search Section -->
        <section class="search-section">
            <h1 class="search-title">Find your guides from one place </h1>
            <p class="search-subtitle">Find your guid from our trusted network</p>
            
            <div class="search-container">
                <div class="search-input-wrapper">
                    <input 
                        type="text" 
                        class="search-input" 
                        id="destinationSearch"
                        placeholder="Search guide"
                        autocomplete="off"
                    >
                    <button class="search-icon" id="searchButton">
                        🔍
                    </button>
                </div>
            </div>
        </section>
        <h1 class="page-title">Reviewed Guides</h1>

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
                        <span>Rs. 1,500</span>
                        <div class="range-bar"></div>
                        <span>Rs. 75,000</span>
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
                        <p class="profile-desc">Highly reviewed cultural guide with excellent customer feedback and deep local knowledge.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Top-reviewed adventure guide with outstanding service and safety record.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/fae394ceb9cdbaf029e13c0b4d17726dad1b4291.png" alt="Kumar Fernando">
                        </div>
                        <h3 class="profile-name">Kumar Fernando</h3>
                        <div class="profile-rating">★ 5.0 <span class="reviews">(210 reviews)</span></div>
                        <p class="profile-desc">Exceptional wildlife guide with perfect reviews and unmatched expertise in nature tours.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png" alt="Jane Smith">
                        </div>
                        <h3 class="profile-name">Jane Smith</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(98 reviews)</span></div>
                        <p class="profile-desc">Highly reviewed cultural guide with excellent customer feedback and deep local knowledge.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>

                    <article class="profile-card">
                        <div class="profile-avatar">
                            <img src="http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png" alt="John Doe">
                        </div>
                        <h3 class="profile-name">John Doe</h3>
                        <div class="profile-rating">★ 4.9 <span class="reviews">(124 reviews)</span></div>
                        <p class="profile-desc">Top-reviewed adventure guide with outstanding service and safety record.</p>
                        <button class="select-driver-btn">Select Guide</button>
                    </article>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Add event listeners to all explore destination buttons
        document.querySelectorAll('.select-driver-btn').forEach(button => {
            button.addEventListener('click', function() {
                window.location.href = 'http://localhost/test/GuideController/guideDetail'
            });
        });
    </script>

    <script src="<?php echo URL_ROOT; ?>/public/components/driver/reviewedDrivers/reviewedDriver.js"></script>
</body>
</html>
