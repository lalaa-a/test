<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Plan Your Way Through Sri-Lanka\'s Wonders'; ?></title>
    
    <!-- Driver page specific styles -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/components/driver/driver.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>

    <style>
                
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

    </style>
</head>
<body>
    <!-- Navigation -->
    <?php renderComponent('inc','navigation',[]); ?>

    <!-- Main Content -->
    <main class="main-content">

                <!-- Search Section -->
        <section class="search-section">
            <h1 class="search-title">Find your drivers from one place </h1>
            <p class="search-subtitle">Find your driver from our trusted network</p>
            
            <div class="search-container">
                <div class="search-input-wrapper">
                    <input 
                        type="text" 
                        class="search-input" 
                        id="destinationSearch"
                        placeholder="Search destinations, activities, or places..."
                        autocomplete="off"
                    >
                    <button class="search-icon" id="searchButton">
                        🔍
                    </button>
                </div>
            </div>
        </section>


        <!-- Hero Section -->
        <section class="hero-section" style="margin-bottom:40px; margin-top:0;">
            <div class="hero-destinations-grid">
                <article class="destination-card" tabindex="0" aria-label="Ella, lush green hills and scenic train rides">
                    <div class="destination-bg" style="background-image:url('<?php echo IMG_ROOT; ?>/explore/destinations/hero1.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Friendy</h2>
                        <p class="destination-subtitle">Your local travel companion</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Galle, historic fort and coastal charm">
                    <div class="destination-bg" style="background-image:url('<?php echo IMG_ROOT; ?>/explore/destinations/hero2.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Professional</h2>
                        <p class="destination-subtitle">Reliable rides with comfort throughout journey</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Yala, wildlife safaris and national park">
                    <div class="destination-bg" style="background-image:url('<?php echo IMG_ROOT; ?>/explore/destinations/hero3.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Knowledge</h2>
                        <p class="destination-subtitle">Knows the roads and hidden gems</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Sigiriya, ancient rock fortress and panoramic views">
                    <div class="destination-bg" style="background-image:url('<?php echo IMG_ROOT; ?>/explore/destinations/hero4.png');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Experience</h2>
                        <p class="destination-subtitle">Your trusted guide through Sri Lanka</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Trending Drivers Section -->
        <section class="drivers-section">
            <h2 class="section-title">Trending Drivers</h2>
            <div class="drivers-row">
                <div class="drivers-container">
                    <?php if(isset($trendingDrivers) && !empty($trendingDrivers)): ?>
                        <?php foreach($trendingDrivers as $driver): ?>
                        <div class="driver-card">
                            <?php if($driver->badge_type !== 'none'): ?>
                                <div class="driver-badge <?php echo $driver->badge_type; ?>">
                                    <?php echo ($driver->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                                </div>
                            <?php endif; ?>
                            <div class="driver-avatar">
                                <img src="<?php echo htmlspecialchars($driver->image_url); ?>" alt="<?php echo htmlspecialchars($driver->name); ?>">
                            </div>
                            <div class="driver-info">
                                <h3 class="driver-name"><?php echo htmlspecialchars($driver->name); ?></h3>
                                <div class="driver-rating">
                                    <span class="star">★</span>
                                    <span class="rating"><?php echo $driver->rating; ?></span>
                                    <span class="reviews">(<?php echo $driver->total_reviews; ?> reviews)</span>
                                </div>
                                <p class="driver-description">
                                    <?php echo htmlspecialchars($driver->description); ?>
                                </p>
                                <button class="select-driver-btn">Select Driver</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p> No trending drivers available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- All Drivers Section -->
        <section class="drivers-section">
            <h2 class="section-title">All Drivers</h2>
            
            <!-- Licensed Drivers Subsection -->
            <h3 class="subsection-title">Licensed Drivers</h3>
            <div class="drivers-container-grid">
                <?php if(isset($licensedDrivers) && !empty($licensedDrivers)): ?>
                    <?php foreach(array_slice($licensedDrivers, 0, 6) as $driver): ?>
                    <div class="driver-card">
                        <?php if($driver->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $driver->badge_type; ?>">
                                <?php echo ($driver->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($driver->image_url); ?>" alt="<?php echo htmlspecialchars($driver->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($driver->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $driver->rating; ?></span>
                                <span class="reviews">(<?php echo $driver->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($driver->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/DriverController/licensed'">
                See More
                <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>

            <!-- Reviewed Drivers Subsection -->
            <h3 class="subsection-title">Reviewed Drivers</h3>
            <div class="drivers-container-grid">
                <?php if(isset($reviewedDrivers) && !empty($reviewedDrivers)): ?>
                    <?php foreach(array_slice($reviewedDrivers, 0, 6) as $driver): ?>
                    <div class="driver-card">
                        <?php if($driver->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $driver->badge_type; ?>">
                                <?php echo ($driver->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($driver->image_url); ?>" alt="<?php echo htmlspecialchars($driver->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($driver->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $driver->rating; ?></span>
                                <span class="reviews">(<?php echo $driver->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($driver->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/DriverController/reviewed'">
                See More
                <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>

            <!-- Tourist Drivers Subsection -->
            <h3 class="subsection-title">Tourist Drivers</h3>
            <div class="drivers-container-grid">
                <?php if(isset($touristDrivers) && !empty($touristDrivers)): ?>
                    <?php foreach(array_slice($touristDrivers, 0, 6) as $driver): ?>
                    <div class="driver-card">
                        <?php if($driver->badge_type !== 'none'): ?>
                            <div class="driver-badge <?php echo $driver->badge_type; ?>">
                                <?php echo ($driver->badge_type === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($driver->image_url); ?>" alt="<?php echo htmlspecialchars($driver->name); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($driver->name); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $driver->rating; ?></span>
                                <span class="reviews">(<?php echo $driver->total_reviews; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($driver->description); ?>
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="see-more-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/DriverController/tourist'">
                See More
                <img src="<?php echo URL_ROOT; ?>/public/components/driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
            </button>
        </section>
    </main>

    <!-- Footer -->
    <?php renderComponent('inc','footer',[]); ?>

    <script src="<?php echo URL_ROOT; ?>/public/components/driver/driver.js">

    </script>
</body>
</html>
