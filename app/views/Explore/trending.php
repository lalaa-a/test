<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending - Destinations, Drivers & Guides</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Driver cards styles -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/components/driver/driver.css">
    
    <style>
        /* CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Geologica', sans-serif;
            background-color: #f9fafb;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Page Header */
        .page-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 0;
        }
        
        .page-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 36px;
            color: #111827;
            margin-bottom: 16px;
        }
        
        .page-subtitle {
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            padding: 12px 24px;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #374151;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-tab:hover {
            border-color: #006a71;
            color: #006a71;
            transform: translateY(-2px);
        }
        
        .filter-tab.active {
            background: #006a71;
            border-color: #006a71;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 106, 113, 0.3);
        }
        
        /* Section Styles */
        .trending-section {
            margin-bottom: 60px;
        }
        
        .section-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #374151;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title .section-icon {
            font-size: 32px;
            color: #006a71;
        }
        
        /* Grid Layout - Responsive */
        .trending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 28px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 900px) {
            .trending-grid {
                grid-template-columns: repeat(2, minmax(280px, 1fr));
                gap: 18px;
            }
        }
        
        @media (max-width: 600px) {
            .trending-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
        
        /* Destination Cards */
        .destination-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        
        .destination-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }
        
        .destination-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .destination-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .destination-card:hover .destination-image img {
            transform: scale(1.1);
        }
        
        .trending-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .destination-info {
            padding: 20px;
        }
        
        .destination-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .destination-category {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #006a71;
            background: rgba(0, 106, 113, 0.1);
            padding: 4px 12px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 12px;
        }
        
        .destination-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .star {
            color: #fbbf24;
            font-size: 16px;
        }
        
        .rating-value {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 14px;
            color: #111827;
        }
        
        .review-count {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
        }
        
        .destination-description {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        
        .card-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-primary {
            flex: 1;
            background: #006a71;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #005a61;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            color: #006a71;
            border: 2px solid #006a71;
            border-radius: 8px;
            padding: 8px 16px;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #006a71;
            color: white;
        }
        
        /* Show More Button */
        .show-more-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .show-more-btn {
            background: linear-gradient(135deg, #006a71, #005a61);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 32px;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .show-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 106, 113, 0.3);
        }
        
        /* Hide/Show functionality */
        .trending-section.hidden {
            display: none;
        }
        
        /* Override for driver cards in trending */
        .trending-grid .driver-card {
            min-width: 0;
            min-height: 300px;
        }
        
        /* Place Card Styles from allDestinations.php */
        .place-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            min-height: 345px;
            box-shadow: 0 4px 24px 0 rgba(0,0,0,0.13);
            cursor: pointer;
            outline: none;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1), filter 0.3s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .place-card:focus {
            box-shadow: 0 0 0 3px #006a71, 0 4px 24px 0 rgba(0,0,0,0.13);
        }
        
        .place-card:hover, .place-card:focus-visible {
            transform: scale(1.035);
            filter: brightness(1.08);
            z-index: 3;
        }
        
        .place-image {
            width: 100%;
            height: 150px;
            overflow: hidden;
        }
        
        .place-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .place-card:hover .place-image img {
            transform: scale(1.05);
        }
        
        .place-info {
            padding: 18px;
            background: white;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .place-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            margin-bottom: 6px;
        }
        
        .place-category {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 9999px;
            background-color: #f3f4f6;
        }
        
        .place-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .rating-value {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
        }
        
        .place-description {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.45;
            margin-bottom: 18px;
            flex: 1;
        }
        
        .explore-place-btn {
            width: 100%;
            background: #006a71;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .explore-place-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
            }
            
            .page-subtitle {
                font-size: 16px;
            }
            
            .filter-tabs {
                gap: 6px;
            }
            
            .filter-tab {
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .section-title {
                font-size: 24px;
            }
        }
    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>

</head>
<body>

    <!-- Navigation Bar -->
    <?php renderComponent('inc','navigation',[]); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">Trending Now</h1>
            <p class="page-subtitle">Discover the most popular destinations, trusted drivers, and experienced guides chosen by travelers like you</p>
            
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">
                    <span class="material-icons" style="font-size: 18px; margin-right: 6px;">trending_up</span>
                    All Trending
                </button>
                <button class="filter-tab" data-filter="destinations">
                    <span class="material-icons" style="font-size: 18px; margin-right: 6px;">place</span>
                    Destinations
                </button>
                <button class="filter-tab" data-filter="drivers">
                    <span class="material-icons" style="font-size: 18px; margin-right: 6px;">drive_eta</span>
                    Drivers
                </button>
                <button class="filter-tab" data-filter="guides">
                    <span class="material-icons" style="font-size: 18px; margin-right: 6px;">person</span>
                    Guides
                </button>
            </div>
        </header>

        <!-- Trending Destinations Section -->
        <section class="trending-section" id="destinations-section">
            <h2 class="section-title">
                <span class="material-icons section-icon">place</span>
                Trending Destinations
            </h2>
            <div class="trending-grid">
                <!-- Place Card 1 - Anuradhapura -->
                <div class="place-card" onclick="navigateToDestination('Anuradhapura')">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/anuradhapura.png" alt="Anuradhapura">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Anuradhapura</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (312 reviews)</span>
                        </div>
                        <p class="place-description">Ancient capital of Sri Lanka with magnificent ruins, sacred sites, and Buddhist monuments dating back over 2,000 years.</p>
                        <button class="explore-place-btn" onclick="event.stopPropagation(); navigateToDestDetails();">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Place Card 2 - Kandy -->
                <div class="place-card" onclick="navigateToDestination('Kandy')">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/kandy.png" alt="Kandy">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Kandy</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (156 reviews)</span>
                        </div>
                        <p class="place-description">Cultural capital with the Temple of Tooth and beautiful lake views.</p>
                        <button class="explore-place-btn" onclick="event.stopPropagation(); navigateToDestDetails();">Explore This Place</button>
                    </div>
                </div>

                <!-- Place Card 3 - Nuwara Eliya -->
                <div class="place-card" onclick="navigateToDestination('Nuwara Eliya')">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/nuwaraeliya.png" alt="Nuwara Eliya">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Nuwara Eliya</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (203 reviews)</span>
                        </div>
                        <p class="place-description">Hill station with tea plantations and cool mountain climate.</p>
                        <button class="explore-place-btn" onclick="event.stopPropagation(); navigateToDestDetails();">Explore This Place</button>
                    </div>
                </div>

            </div>
            
        </section>

        <!-- Trending Drivers Section -->
        <section class="trending-section" id="drivers-section">
            <h2 class="section-title">
                <span class="material-icons section-icon">drive_eta</span>
                Trending Drivers
            </h2>
            <div class="trending-grid">
                <!-- Driver Card 1 -->
                <div class="driver-card" onclick="navigateToDriver('david-brown')">
                    <div class="driver-badge top-rated">
                        #1 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample1.png" alt="David Brown">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">David Brown</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.9</span>
                            <span class="reviews">(127 trips)</span>
                        </div>
                        <p class="driver-description">
                            Licensed driver specializing in cultural tours, airport transfers, and hill country excursions with 5+ years of experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToDriver('david-brown');">Select Driver</button>
                    </div>
                </div>

                <!-- Driver Card 2 -->
                <div class="driver-card" onclick="navigateToDriver('sarah-silva')">
                    <div class="driver-badge most-booked">
                        #2 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample2.png" alt="Sarah Silva">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Sarah Silva</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.8</span>
                            <span class="reviews">(98 trips)</span>
                        </div>
                        <p class="driver-description">
                            Tourist driver specialized in wildlife safaris, beach tours, and photography sessions with 3+ years of experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToDriver('sarah-silva');">Select Driver</button>
                    </div>
                </div>

                <!-- Driver Card 3 -->
                <div class="driver-card" onclick="navigateToDriver('michael-fernando')">
                    <div class="driver-badge top-rated">
                        #3 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample3.png" alt="Michael Fernando">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Michael Fernando</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.7</span>
                            <span class="reviews">(156 trips)</span>
                        </div>
                        <p class="driver-description">
                            Licensed driver for city tours, business travel, and long distance journeys with 7+ years of professional experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToDriver('michael-fernando');">Select Driver</button>
                    </div>
                </div>
            </div>
            
            <div class="show-more-container">

                

            </div>
        </section>

        <!-- Trending Guides Section -->
        <section class="trending-section" id="guides-section">
            <h2 class="section-title">
                <span class="material-icons section-icon">person</span>
                Trending Guides
            </h2>
            <div class="trending-grid">
                <!-- Guide Card 1 -->
                <div class="driver-card" onclick="navigateToGuide('ravi-perera')">
                    <div class="driver-badge top-rated">
                        #1 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample1.png" alt="Ravi Perera">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Ravi Perera</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.9</span>
                            <span class="reviews">(184 tours)</span>
                        </div>
                        <p class="driver-description">
                            Licensed guide specializing in cultural heritage, ancient temples, historical sites, and archaeological tours with 8+ years of experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToGuide('ravi-perera');">Select Guide</button>
                    </div>
                </div>

                <!-- Guide Card 2 -->
                <div class="driver-card" onclick="navigateToGuide('chamila-wijesinghe')">
                    <div class="driver-badge most-booked">
                        #2 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample2.png" alt="Chamila Wijesinghe">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Chamila Wijesinghe</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.8</span>
                            <span class="reviews">(142 tours)</span>
                        </div>
                        <p class="driver-description">
                            Nature guide specialized in wildlife tracking, bird watching, national parks, and eco tours with 6+ years of experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToGuide('chamila-wijesinghe');">Select Guide</button>
                    </div>
                </div>

                <!-- Guide Card 3 -->
                <div class="driver-card" onclick="navigateToGuide('nimal-rajapakse')">
                    <div class="driver-badge top-rated">
                        #3 Trending
                    </div>
                    <div class="driver-avatar">
                        <img src="<?php echo IMG_ROOT; ?>/explore/drivers/sample3.png" alt="Nimal Rajapakse">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Nimal Rajapakse</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.7</span>
                            <span class="reviews">(97 tours)</span>
                        </div>
                        <p class="driver-description">
                            Adventure guide specialized in mountain climbing, hiking trails, rock climbing, and adventure sports with 4+ years of experience.
                        </p>
                        <button class="select-driver-btn" onclick="event.stopPropagation(); navigateToGuide('nimal-rajapakse');">Select Guide</button>
                    </div>
                </div>
            </div>
            
            
        </section>

    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Initialize page functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Trending page loaded - initializing functionality...');
            initializeFilterTabs();
            initializeSaveSystem();
            initializePlaceCards();
            console.log('Trending page initialization complete');
        });

        // Filter tab functionality
        function initializeFilterTabs() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const sections = document.querySelectorAll('.trending-section');

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Show/hide sections based on filter
                    sections.forEach(section => {
                        if (filter === 'all') {
                            section.classList.remove('hidden');
                        } else {
                            const sectionId = section.getAttribute('id');
                            if (sectionId.includes(filter)) {
                                section.classList.remove('hidden');
                            } else {
                                section.classList.add('hidden');
                            }
                        }
                    });

                    console.log(`Filter applied: ${filter}`);
                    showNotification(`Showing ${filter === 'all' ? 'all trending items' : filter}`, 'info');
                });
            });
        }

        // Save system initialization
        function initializeSaveSystem() {
            // Check if items are already saved and update UI accordingly
            console.log('Save system initialized');
        }

        // Place cards initialization
        function initializePlaceCards() {
            const placeCards = document.querySelectorAll('.place-card');
            
            placeCards.forEach(card => {
                // Make cards focusable for accessibility
                card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');
                card.setAttribute('aria-label', 'Navigate to destination details');
                
                // Add keyboard navigation
                card.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
            
            // Initialize explore buttons for place cards
            const exploreButtons = document.querySelectorAll('.explore-place-btn');
            
            exploreButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent card click event
                    
                    const placeName = this.closest('.place-card').querySelector('.place-title').textContent;
                    console.log(`Explore button clicked for: ${placeName}`);
                    navigateToDestDetails();
                });
            });
        }

        // Navigation functions
        function navigateToDestination(destinationName) {
            const destUrl = `<?php echo URL_ROOT; ?>/user/destDetails`;
            console.log(`Navigating to destination: ${destinationName}`);
            window.location.href = destUrl;
        }

        function navigateToDestDetails() {
            const destUrl = `<?php echo URL_ROOT; ?>/user/destDetails`;
            console.log(`Navigating to destination details`);
            window.location.href = destUrl;
        }

        function navigateToDriver(driverId) {
            const driverUrl = `<?php echo URL_ROOT; ?>/Home/drive`;
            console.log(`Navigating to driver: ${driverId}`);
            window.location.href = driverUrl;
        }

        function navigateToGuide(guideId) {
            const guideUrl = `<?php echo URL_ROOT; ?>/Home/trips`;
            console.log(`Navigating to guide: ${guideId}`);
            window.location.href = guideUrl;
        }

        // Save functions
        function saveDestination(destinationName) {
            // Get existing saved destinations
            let savedDestinations = JSON.parse(localStorage.getItem('savedDestinations') || '[]');
            
            if (!savedDestinations.includes(destinationName)) {
                savedDestinations.push(destinationName);
                localStorage.setItem('savedDestinations', JSON.stringify(savedDestinations));
                showNotification(`${destinationName} saved to your list!`, 'success');
                console.log(`Saved destination: ${destinationName}`);
            } else {
                showNotification(`${destinationName} is already in your saved list`, 'info');
            }
        }

        function saveDriver(driverId) {
            let savedDrivers = JSON.parse(localStorage.getItem('savedDrivers') || '[]');
            
            if (!savedDrivers.includes(driverId)) {
                savedDrivers.push(driverId);
                localStorage.setItem('savedDrivers', JSON.stringify(savedDrivers));
                showNotification('Driver saved to your list!', 'success');
                console.log(`Saved driver: ${driverId}`);
            } else {
                showNotification('Driver is already in your saved list', 'info');
            }
        }

        function saveGuide(guideId) {
            let savedGuides = JSON.parse(localStorage.getItem('savedGuides') || '[]');
            
            if (!savedGuides.includes(guideId)) {
                savedGuides.push(guideId);
                localStorage.setItem('savedGuides', JSON.stringify(savedGuides));
                showNotification('Guide saved to your list!', 'success');
                console.log(`Saved guide: ${guideId}`);
            } else {
                showNotification('Guide is already in your saved list', 'info');
            }
        }

        // Show more functions
        function showMoreDestinations() {
            const destUrl = `<?php echo URL_ROOT; ?>/Home/dest`;
            console.log('Navigating to all destinations');
            window.location.href = destUrl;
        }

        function showMoreDrivers() {
            const driversUrl = `<?php echo URL_ROOT; ?>/Home/drive`;
            console.log('Navigating to all drivers');
            window.location.href = driversUrl;
        }

        function showMoreGuides() {
            const guidesUrl = `<?php echo URL_ROOT; ?>/Home/trips`;
            console.log('Navigating to all guides');
            window.location.href = guidesUrl;
        }

        // Utility functions
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 8px;
                color: white;
                font-family: 'Geologica', sans-serif;
                font-weight: 600;
                font-size: 14px;
                z-index: 10000;
                opacity: 0;
                transition: opacity 0.3s ease;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#006A71'};
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            `;
            
            document.body.appendChild(notification);
            
            // Fade in
            setTimeout(() => notification.style.opacity = '1', 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Export functions for external use
        window.TrendingPage = {
            showNotification,
            navigateToDestination,
            navigateToDriver,
            navigateToGuide,
            saveDestination,
            saveDriver,
            saveGuide
        };
    </script>
</body>
</html>
