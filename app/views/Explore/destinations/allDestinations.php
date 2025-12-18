<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Your Way Through Sri-Lanka's Wonders</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
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
        
        
        
        /* Hero Section */
        .hero-section {
            margin-bottom: 40px;
        }
        
        .hero-destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 28px;
            width: 100%;
            max-width: 1320px;
            margin: 0 auto 0 auto;
            padding: 32px 0 24px 0;
            position: relative;
            z-index: 2;
        }
        
        .destination-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            min-height: 500px;
            box-shadow: 0 4px 24px 0 rgba(0,0,0,0.13);
            cursor: pointer;
            outline: none;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1), filter 0.3s cubic-bezier(.4,0,.2,1);
            display: flex;
            align-items: flex-end;
            background: #222;
        }
        
        .destination-card:focus {
            box-shadow: 0 0 0 3px #006a71, 0 4px 24px 0 rgba(0,0,0,0.13);
        }
        
        .destination-card:hover, .destination-card:focus-visible {
            transform: scale(1.035);
            filter: brightness(1.08);
            z-index: 3;
        }
        
        .destination-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 1;
            transition: filter 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .destination-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.48);
            z-index: 2;
            transition: background 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .destination-card:hover .destination-overlay,
        .destination-card:focus-visible .destination-overlay {
            background: rgba(0,0,0,0.60);
        }
        
        .destination-text {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            padding: 0 0 22px 22px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-end;
            min-height: 80px;
            transition: padding-bottom 0.3s cubic-bezier(.4,0,.2,1);
            pointer-events: none;
        }
        
        .destination-title {
            color: #fff;
            font-family: 'Poppins', 'Inter', Arial, sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin: 0 0 0 0;
            text-shadow: 0 4px 24px rgba(0,0,0,0.45), 0 2px 8px rgba(0,0,0,0.18);
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .destination-subtitle {
            color: #fff;
            font-family: 'Poppins', 'Inter', Arial, sans-serif;
            font-size: 1.08rem;
            font-weight: 400;
            letter-spacing: 0.5px;
            margin: 0;
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.3s cubic-bezier(.4,0,.2,1), transform 0.3s cubic-bezier(.4,0,.2,1);
            text-shadow: 0 2px 8px rgba(0,0,0,0.35);
        }
        
        .destination-card:hover .destination-title,
        .destination-card:focus-visible .destination-title {
            transform: translateY(-18px);
        }
        
        .destination-card:hover .destination-subtitle,
        .destination-card:focus-visible .destination-subtitle {
            opacity: 1;
            transform: translateY(0);
        }
        
        @media (max-width: 900px) {
            .hero-destinations-grid { gap: 18px; }
            .destination-title { font-size: 1.5rem; }
            .destination-subtitle { font-size: 0.98rem; }
        }
        
        @media (max-width: 600px) {
            .hero-destinations-grid { grid-template-columns: 1fr; gap: 12px; }
            .destination-title { font-size: 1.1rem; }
            .destination-subtitle { font-size: 0.85rem; }
            .destination-text { padding: 0 0 18px 14px; }
        }
        
        /* Categories Section */
        .categories-section {
            margin-bottom: 80px;
        }
        
        .section-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 24px;
            color: #374151;
            margin-bottom: 30px;
            margin-left: 20px;
        }
        
        .category-container {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding: 16px 0;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #006a71 #f1f1f1;
        }
        
        .category-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .category-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .category-container::-webkit-scrollbar-thumb {
            background: #006a71;
            border-radius: 3px;
        }
        
        .category-container::-webkit-scrollbar-thumb:hover {
            background: #005a61;
        }
        
        .category-container.no-scroll {
            overflow-x: visible;
            flex-wrap: wrap;
        }
        
        /* Category Place Cards - Smaller version of place cards */
        .category-place-card {
            /* slightly larger so they're closer to trending cards but still smaller */
            min-width: 280px;
            max-width: 280px;
            min-height: 320px;
            flex-shrink: 0;
        }

        .category-place-card .place-image {
            height: 150px;
        }

        .category-place-card .place-info {
            padding: 18px;
        }

        .category-place-card .place-title {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .category-place-card .place-category {
            font-size: 14px;
            padding: 4px 10px;
            margin-bottom: 10px;
        }

        .category-place-card .rating-value {
            font-size: 14px;
        }

        .category-place-card .place-rating {
            margin-bottom: 16px;
        }

        .category-place-card .place-description {
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 18px;
        }

        .category-place-card .explore-place-btn {
            padding: 12px;
            font-size: 16px;
        }
        

        
        .see-more-btn {
            background: #ffffff;
            border: none;
            border-radius: 75px;
            padding: 10px 20px;
            box-shadow: 0 4px 4px 0px rgba(0, 0, 0, 0.25);
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 15px;
            color: #000000;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            margin-right: 20px;
            transition: all 0.3s ease;
        }
        
        .see-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
        }
        
        .arrow-icon {
            width: 22px;
            height: 22px;
        }
        
        /* Trending Places Section */
        .trending-section {
            margin-bottom: 80px;
        }
        
        .trending-places-grid {
            display: grid;
            /* increase a bit more: min width 280px */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
            width: 100%;
            margin: 0 auto 24px auto;
            padding: 20px 0;
        }
        
        @media (max-width: 900px) {
            .trending-places-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
                gap: 18px;
            }
        }
        
        @media (max-width: 600px) {
            .trending-places-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        
        .place-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            /* bump card height further to match larger width */
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
            /* increase image height a bit more */
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
        
        .star {
            color: #fbbf24;
            font-size: 16px;
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
            /* keep button full-width but reduce padding for tighter layout */
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
        
        
        /* Scroll indicators for horizontal containers */
        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 10;
            display: none;
        }
        
        .scroll-btn-left {
            left: 10px;
        }
        
        .scroll-btn-right {
            right: 10px;
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
    
    <?php renderComponent('inc','navigation',[]); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Search Section -->
        <section class="search-section">
            <h1 class="search-title">Discover Sri Lanka's Wonders</h1>
            <p class="search-subtitle">Find your perfect destination from hundreds of amazing places across the island</p>
            
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
        <section class="hero-section">
            <div class="hero-destinations-grid">
                <article class="destination-card" tabindex="0" aria-label="Ella, lush green hills and scenic train rides">
                    <div class="destination-bg" style="background-image:url('<?php echo URL_ROOT; ?>/public/components/driver/images/img01.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Ella</h2>
                        <p class="destination-subtitle">Green hills & scenic train rides</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Galle, historic fort and coastal charm">
                    <div class="destination-bg" style="background-image:url('<?php echo URL_ROOT; ?>/public/components/driver/images/img02.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Galle</h2>
                        <p class="destination-subtitle">Historic fort and coastal charm</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Yala, wildlife safaris and national park">
                    <div class="destination-bg" style="background-image:url('<?php echo URL_ROOT; ?>/public/components/driver/images/img03.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Yala</h2>
                        <p class="destination-subtitle">Wildlife safaris & national parks</p>
                    </div>
                </article>
                <article class="destination-card" tabindex="0" aria-label="Sigiriya, ancient rock fortress and panoramic views">
                    <div class="destination-bg" style="background-image:url('<?php echo URL_ROOT; ?>/public/components/driver/images/img04.jpg');"></div>
                    <div class="destination-overlay"></div>
                    <div class="destination-text">
                        <h2 class="destination-title">Sigiriya</h2>
                        <p class="destination-subtitle">Rock fort & views</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Trending Places -->
        <section class="categories-section">
            <h2 class="section-title">Trending Places</h2>
            <div class="category-container">
                <div class="place-card category-place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card category-place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card category-place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/benthota.png" alt="Bentota">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Bentota</h3>
                        <span class="place-category">Relaxation & Leisure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (189 reviews)</span>
                        </div>
                        <p class="place-description">Beautiful beach resort town perfect for water sports and relaxation.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card category-place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/dambulla.png" alt="Dambulla">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Dambulla</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.5 (174 reviews)</span>
                        </div>
                        <p class="place-description">Ancient cave temple complex with stunning Buddhist art and statues.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card category-place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/hikkaduwa.png" alt="Hikkaduwa">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Hikkaduwa</h3>
                        <span class="place-category">Entertainment & Activities</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.4 (142 reviews)</span>
                        </div>
                        <p class="place-description">Vibrant beach town with surfing, diving, and nightlife activities.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card category-place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/negombo.png" alt="Negombo">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Negombo</h3>
                        <span class="place-category">Food & Cuisine</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.3 (198 reviews)</span>
                        </div>
                        <p class="place-description">Fishing town famous for fresh seafood and traditional fish markets.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Culture & Heritage -->
        <section class="trending-section">
            <h2 class="section-title">Culture & Heritage</h2>
            <div class="trending-places-grid">
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/mirissa.png" alt="Mirissa Beach">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Mirissa Beach</h3>
                        <span class="place-category">Relaxation & Leisure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (287 reviews)</span>
                        </div>
                        <p class="place-description">Beautiful beach with golden sands and crystal-clear waters. Perfect for swimming, sunbathing, and whale watching.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/horton.png" alt="Horton Plains">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Horton Plains</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (245 reviews)</span>
                        </div>
                        <p class="place-description">Highland plateau with stunning views, waterfalls, and diverse flora and fauna. Famous for World's End cliff.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/colombo.png" alt="Colombo">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Colombo</h3>
                        <span class="place-category">Entertainment & Activities</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (198 reviews)</span>
                        </div>
                        <p class="place-description">Vibrant capital city with bustling markets, modern shopping malls, cultural attractions, and lively nightlife.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Polonnaruwa">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Polonnaruwa</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (267 reviews)</span>
                        </div>
                        <p class="place-description">Ancient city with well-preserved ruins, temples, and statues showcasing the architectural brilliance of medieval Sri Lanka.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Adam's Peak">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Adam's Peak</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (234 reviews)</span>
                        </div>
                        <p class="place-description">Sacred mountain with a pilgrimage trail leading to a summit featuring a mysterious footprint, offering breathtaking sunrise views.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
            </div>
            <a href="<?php URL_ROOT?>/test/User/cultureHeritage" style="text-decoration: none;">
                <button class="see-more-btn">
                    See More
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </a>
        </section>

        <!-- Nature & Adventure -->
        <section class="trending-section">
            <h2 class="section-title">Nature & Adventure</h2>
            <div class="trending-places-grid">
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/mirissa.png" alt="Mirissa Beach">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Mirissa Beach</h3>
                        <span class="place-category">Relaxation & Leisure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (287 reviews)</span>
                        </div>
                        <p class="place-description">Beautiful beach with golden sands and crystal-clear waters. Perfect for swimming, sunbathing, and whale watching.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/horton.png" alt="Horton Plains">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Horton Plains</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (245 reviews)</span>
                        </div>
                        <p class="place-description">Highland plateau with stunning views, waterfalls, and diverse flora and fauna. Famous for World's End cliff.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/colombo.png" alt="Colombo">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Colombo</h3>
                        <span class="place-category">Entertainment & Activities</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (198 reviews)</span>
                        </div>
                        <p class="place-description">Vibrant capital city with bustling markets, modern shopping malls, cultural attractions, and lively nightlife.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Polonnaruwa">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Polonnaruwa</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (267 reviews)</span>
                        </div>
                        <p class="place-description">Ancient city with well-preserved ruins, temples, and statues showcasing the architectural brilliance of medieval Sri Lanka.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Adam's Peak">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Adam's Peak</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (234 reviews)</span>
                        </div>
                        <p class="place-description">Sacred mountain with a pilgrimage trail leading to a summit featuring a mysterious footprint, offering breathtaking sunrise views.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
            </div>

            <a href="<?php echo URL_ROOT?>/User/natureAdventure" style="text-decoration: none;">
                <button class="see-more-btn">
                    See More
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </a>
        </section>

        <!-- Relaxation & Leisure -->
        <section class="trending-section">
            <h2 class="section-title">Relaxation & Leisure</h2>
            <div class="trending-places-grid">
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/mirissa.png" alt="Mirissa Beach">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Mirissa Beach</h3>
                        <span class="place-category">Relaxation & Leisure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (287 reviews)</span>
                        </div>
                        <p class="place-description">Beautiful beach with golden sands and crystal-clear waters. Perfect for swimming, sunbathing, and whale watching.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/horton.png" alt="Horton Plains">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Horton Plains</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (245 reviews)</span>
                        </div>
                        <p class="place-description">Highland plateau with stunning views, waterfalls, and diverse flora and fauna. Famous for World's End cliff.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/colombo.png" alt="Colombo">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Colombo</h3>
                        <span class="place-category">Entertainment & Activities</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (198 reviews)</span>
                        </div>
                        <p class="place-description">Vibrant capital city with bustling markets, modern shopping malls, cultural attractions, and lively nightlife.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Polonnaruwa">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Polonnaruwa</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (267 reviews)</span>
                        </div>
                        <p class="place-description">Ancient city with well-preserved ruins, temples, and statues showcasing the architectural brilliance of medieval Sri Lanka.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Adam's Peak">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Adam's Peak</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (234 reviews)</span>
                        </div>
                        <p class="place-description">Sacred mountain with a pilgrimage trail leading to a summit featuring a mysterious footprint, offering breathtaking sunrise views.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
            </div>
            <a href="<?php echo URL_ROOT?>/User/relaxationLeisure" style="text-decoration: none;">
                <button class="see-more-btn">
                    See More
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </a>
        </section>

        <!-- Entertainment & Activities -->
        <section class="trending-section">
            <h2 class="section-title">Entertainment & Activities</h2>
            <div class="trending-places-grid">
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/mirissa.png" alt="Mirissa Beach">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Mirissa Beach</h3>
                        <span class="place-category">Relaxation & Leisure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (287 reviews)</span>
                        </div>
                        <p class="place-description">Beautiful beach with golden sands and crystal-clear waters. Perfect for swimming, sunbathing, and whale watching.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
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
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/horton.png" alt="Horton Plains">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Horton Plains</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (245 reviews)</span>
                        </div>
                        <p class="place-description">Highland plateau with stunning views, waterfalls, and diverse flora and fauna. Famous for World's End cliff.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/colombo.png" alt="Colombo">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Colombo</h3>
                        <span class="place-category">Entertainment & Activities</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (198 reviews)</span>
                        </div>
                        <p class="place-description">Vibrant capital city with bustling markets, modern shopping malls, cultural attractions, and lively nightlife.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Polonnaruwa">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Polonnaruwa</h3>
                        <span class="place-category">Culture & Heritage</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (267 reviews)</span>
                        </div>
                        <p class="place-description">Ancient city with well-preserved ruins, temples, and statues showcasing the architectural brilliance of medieval Sri Lanka.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
                <div class="place-card">
                    <div class="place-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Adam's Peak">
                    </div>
                    <div class="place-info">
                        <h3 class="place-title">Adam's Peak</h3>
                        <span class="place-category">Nature & Adventure</span>
                        <div class="place-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (234 reviews)</span>
                        </div>
                        <p class="place-description">Sacred mountain with a pilgrimage trail leading to a summit featuring a mysterious footprint, offering breathtaking sunrise views.</p>
                        <button class="explore-place-btn">Explore This Place</button>
                    </div>
                </div>
            </div>

            <a href="<?php echo URL_ROOT?>/User/entertainmentActivities" style="text-decoration: none;">
                <button class="see-more-btn">
                    See More
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </a>

        </section>


    </main>

    <?php renderComponent('inc','footer',[]); ?>


    <script>
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeDestinationCards();
            initializeCategoryCards();
            initializePlaceCards();
            initializeExploreButtons();
            initializeScrollBehavior();
            initializeSearchFunctionality();
        });

        // Destination cards interaction
        function initializeDestinationCards() {
            const destinationCards = document.querySelectorAll('.destination-card');
            
            destinationCards.forEach(card => {
                card.addEventListener('click', function() {
                    const title = this.querySelector('.destination-title').textContent;
                    console.log(`Destination clicked: ${title}`);
                    navigateToDestination(title);
                });
                
                // Add keyboard navigation
                card.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        }

        // Category cards interaction
        function initializeCategoryCards() {
            const categoryCards = document.querySelectorAll('.category-card');
            
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const categoryName = this.querySelector('.category-name').textContent;
                    console.log(`Category clicked: ${categoryName}`);
                    // Add navigation logic here
                });
                
                // Add hover effects
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }

        // Place cards interaction
        function initializePlaceCards() {
            const placeCards = document.querySelectorAll('.place-card');
            
            placeCards.forEach(card => {
                card.addEventListener('click', function() {
                    const placeName = this.querySelector('.place-title').textContent;
                    console.log(`Place clicked: ${placeName}`);
                    navigateToDestination(placeName);
                });
                
                // Add hover effects
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }

        // Scroll behavior for category containers
        function initializeScrollBehavior() {
            const categoryContainers = document.querySelectorAll('.category-container:not(.no-scroll)');
            
            categoryContainers.forEach(container => {
                // Add smooth scrolling
                container.style.scrollBehavior = 'smooth';
                
                // Optional: Add scroll indicators
                if (container.scrollWidth > container.clientWidth) {
                    addScrollIndicators(container);
                }
            });
        }

        // Add scroll indicators for horizontally scrollable containers
        function addScrollIndicators(container) {
            const wrapper = container.parentElement;
            
            // Create left and right scroll buttons
            const leftBtn = document.createElement('button');
            leftBtn.innerHTML = '‹';
            leftBtn.className = 'scroll-btn scroll-btn-left';
            leftBtn.style.cssText = `
                position: absolute;
                left: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255,255,255,0.9);
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                z-index: 10;
                display: none;
            `;
            
            const rightBtn = document.createElement('button');
            rightBtn.innerHTML = '›';
            rightBtn.className = 'scroll-btn scroll-btn-right';
            rightBtn.style.cssText = leftBtn.style.cssText.replace('left: 10px', 'right: 10px');
            
            wrapper.style.position = 'relative';
            wrapper.appendChild(leftBtn);
            wrapper.appendChild(rightBtn);
            
            // Scroll functionality
            leftBtn.addEventListener('click', () => {
                container.scrollBy({ left: -300, behavior: 'smooth' });
            });
            
            rightBtn.addEventListener('click', () => {
                container.scrollBy({ left: 300, behavior: 'smooth' });
            });
            
            // Show/hide scroll buttons based on scroll position
            function updateScrollButtons() {
                leftBtn.style.display = container.scrollLeft > 0 ? 'block' : 'none';
                rightBtn.style.display = 
                    container.scrollLeft < (container.scrollWidth - container.clientWidth) ? 'block' : 'none';
            }
            
            container.addEventListener('scroll', updateScrollButtons);
            updateScrollButtons();
        }


// when one destination card clicked this function will guide to that places full deatiled page
        // Explore buttons interaction
        function initializeExploreButtons() {
            const exploreButtons = document.querySelectorAll('.explore-place-btn');
            
            exploreButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent card click event
                    
                    const placeName = this.closest('.place-card').querySelector('.place-title').textContent;
                    console.log(`Explore button clicked for: ${placeName}`);
                    navigateToDestination(placeName);
                });
            });
        }

        // Navigation function for destinations
        function navigateToDestination(destinationName) {
            // Option 1: Navigate to generic dest page (current setup)
            // This will show the same dest.php page for all destinations
            const destUrl = `<?php echo URL_ROOT; ?>/user/destDetails`;
            
            console.log(`Navigating to destination: ${destinationName}`);
            console.log(`URL: ${destUrl}`);
            
            // Navigate to the destination page
            window.location.href = destUrl;
            
            /* Option 2: Dynamic routing (for future implementation)
            // Convert destination name to URL-friendly format
            const urlSlug = destinationName.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single
                .trim('-'); // Remove leading/trailing hyphens

            // Create dynamic destination URL
            const destinationUrl = `<?php echo URL_ROOT; ?>/Home/destination/${urlSlug}`;
            window.location.href = destinationUrl;
            */
        }

        // Alternative: Navigate with destination parameter (for future dynamic content)
        function navigateToSpecificDestination(destinationName) {
            const urlSlug = encodeURIComponent(destinationName.toLowerCase().replace(/\s+/g, '-'));
            const destUrl = `<?php echo URL_ROOT; ?>/Home/dest?destination=${urlSlug}`;
            
            console.log(`Navigating to specific destination: ${destinationName}`);
            console.log(`URL: ${destUrl}`);
            
            window.location.href = destUrl;
        }

// end of the destination clicked function

        // Search functionality
        function initializeSearchFunctionality() {
            const searchInput = document.getElementById('destinationSearch');
            const searchButton = document.getElementById('searchButton');
            const filterChips = document.querySelectorAll('.filter-chip');
            const searchResultsInfo = document.getElementById('searchResultsInfo');
            let currentFilter = 'all';
            let searchTerm = '';

            // Get all searchable items
            function getAllSearchableItems() {
                const items = [];
                
                // Add destination cards
                document.querySelectorAll('.destination-card').forEach(card => {
                    const title = card.querySelector('.destination-title')?.textContent || '';
                    const subtitle = card.querySelector('.destination-subtitle')?.textContent || '';
                    items.push({
                        element: card,
                        title: title,
                        description: subtitle,
                        category: 'destination',
                        searchText: (title + ' ' + subtitle).toLowerCase()
                    });
                });

                // Add place cards
                document.querySelectorAll('.place-card').forEach(card => {
                    const title = card.querySelector('.place-title')?.textContent || '';
                    const description = card.querySelector('.place-description')?.textContent || '';
                    const category = card.querySelector('.place-category')?.textContent || '';
                    
                    let categoryType = 'all';
                    if (category.includes('Culture') || category.includes('Heritage')) categoryType = 'culture';
                    else if (category.includes('Nature') || category.includes('Adventure')) categoryType = 'nature';
                    else if (category.includes('Relaxation') || category.includes('Leisure')) categoryType = 'beach';
                    else if (category.includes('Entertainment') || category.includes('Activities')) categoryType = 'entertainment';
                    else if (category.includes('Food') || category.includes('Cuisine')) categoryType = 'food';

                    items.push({
                        element: card,
                        title: title,
                        description: description,
                        category: categoryType,
                        searchText: (title + ' ' + description + ' ' + category).toLowerCase()
                    });
                });

                return items;
            }

            // Filter and search items
            function filterAndSearchItems() {
                const items = getAllSearchableItems();
                let visibleCount = 0;
                let filteredItems = items;

                // Apply category filter
                if (currentFilter !== 'all') {
                    filteredItems = items.filter(item => item.category === currentFilter);
                }

                // Apply search filter
                if (searchTerm) {
                    filteredItems = filteredItems.filter(item => 
                        item.searchText.includes(searchTerm.toLowerCase())
                    );
                }

                // Show/hide items
                items.forEach(item => {
                    const shouldShow = filteredItems.includes(item);
                    const section = item.element.closest('section');
                    
                    if (shouldShow) {
                        item.element.style.display = '';
                        visibleCount++;
                        highlightSearchTerm(item.element, searchTerm);
                    } else {
                        item.element.style.display = 'none';
                        removeHighlights(item.element);
                    }
                });

                // Show/hide sections based on visible items
                document.querySelectorAll('section').forEach(section => {
                    const visibleItems = section.querySelectorAll('.destination-card:not([style*="display: none"]), .place-card:not([style*="display: none"])');
                    if (visibleItems.length === 0 && (searchTerm || currentFilter !== 'all')) {
                        section.style.display = 'none';
                    } else {
                        section.style.display = '';
                    }
                });

                // Update results info
                updateResultsInfo(visibleCount, searchTerm, currentFilter);

                // Show no results message if needed
                showNoResultsIfNeeded(visibleCount);
            }

            // Highlight search terms
            function highlightSearchTerm(element, term) {
                if (!term) return;

                const titleElement = element.querySelector('.destination-title, .place-title');
                const descElement = element.querySelector('.destination-subtitle, .place-description');

                [titleElement, descElement].forEach(el => {
                    if (el && el.textContent) {
                        const originalText = el.getAttribute('data-original-text') || el.textContent;
                        if (!el.getAttribute('data-original-text')) {
                            el.setAttribute('data-original-text', originalText);
                        }

                        const regex = new RegExp(`(${term})`, 'gi');
                        const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
                        el.innerHTML = highlightedText;
                    }
                });
            }

            // Remove highlights
            function removeHighlights(element) {
                const elements = element.querySelectorAll('[data-original-text]');
                elements.forEach(el => {
                    el.innerHTML = el.getAttribute('data-original-text');
                });
            }

            // Update results info
            function updateResultsInfo(count, term, filter) {
                let message = '';
                if (term && filter !== 'all') {
                    message = `Found ${count} results for "${term}" in ${getFilterName(filter)}`;
                } else if (term) {
                    message = `Found ${count} results for "${term}"`;
                } else if (filter !== 'all') {
                    message = `Showing ${count} ${getFilterName(filter)} destinations`;
                } else {
                    message = '';
                }

                if (message) {
                    searchResultsInfo.textContent = message;
                    searchResultsInfo.style.display = 'block';
                } else {
                    searchResultsInfo.style.display = 'none';
                }
            }

            // Get filter display name
            function getFilterName(filter) {
                const filterNames = {
                    'culture': 'Culture & Heritage',
                    'nature': 'Nature & Adventure',
                    'beach': 'Beach & Relaxation',
                    'entertainment': 'Entertainment & Activities',
                    'food': 'Food & Cuisine'
                };
                return filterNames[filter] || filter;
            }

            // Show no results message
            function showNoResultsIfNeeded(count) {
                let noResultsElement = document.getElementById('noResultsMessage');
                
                if (count === 0 && (searchTerm || currentFilter !== 'all')) {
                    if (!noResultsElement) {
                        noResultsElement = document.createElement('div');
                        noResultsElement.id = 'noResultsMessage';
                        noResultsElement.className = 'no-results';
                        noResultsElement.innerHTML = `
                            <div class="no-results-icon">🔍</div>
                            <div class="no-results-title">No destinations found</div>
                            <div class="no-results-text">Try adjusting your search terms or filters to find more results.</div>
                        `;
                        document.querySelector('.main-content').appendChild(noResultsElement);
                    }
                    noResultsElement.style.display = 'block';
                } else if (noResultsElement) {
                    noResultsElement.style.display = 'none';
                }
            }

            // Search input event
            searchInput.addEventListener('input', function() {
                searchTerm = this.value.trim();
                filterAndSearchItems();
            });

            // Search button event
            searchButton.addEventListener('click', function() {
                searchTerm = searchInput.value.trim();
                filterAndSearchItems();
            });

            // Enter key search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchTerm = this.value.trim();
                    filterAndSearchItems();
                }
            });

            // Filter chip events
            filterChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    // Update active chip
                    filterChips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update current filter
                    currentFilter = this.getAttribute('data-category');
                    
                    // Apply filter
                    filterAndSearchItems();
                });
            });

            // Clear search function
            window.clearSearch = function() {
                searchInput.value = '';
                searchTerm = '';
                currentFilter = 'all';
                filterChips.forEach(c => c.classList.remove('active'));
                filterChips[0].classList.add('active'); // Activate "All Places"
                filterAndSearchItems();
            };
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
                border-radius: 5px;
                color: white;
                font-weight: 600;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
                background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
            `;
            
            document.body.appendChild(notification);
            
            // Fade in
            setTimeout(() => notification.style.opacity = '1', 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }

        // Export functions for external use
        window.DestinationPage = {
            showNotification,
            selectPlace: function(placeName) {
                console.log(`Programmatically selected place: ${placeName}`);
                showNotification(`Selected place: ${placeName}`, 'success');
            }
        };
    </script>
</body>
</html>