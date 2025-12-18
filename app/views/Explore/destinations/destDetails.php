<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigiriya The Ancient Rock Fortress</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="  https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

        /* Force Geologica across all text elements for consistent typography */
        html, body, h1, h2, h3, h4, h5, h6, p, a, span, label, button, input, textarea, select, small, strong {
            font-family: 'Geologica', sans-serif !important;
        }

        /* Keep Material Icons unaffected (use their font) */
        .material-icons, [class*="material-icons"] { font-family: 'Material Icons' !important; }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        
        /* Place Header */
        .place-header {
            margin-bottom: 30px;
        }
        
        .place-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #111827;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .place-title .location-icon {
            font-size: 24px;
            color: #006a71;
        }
        
        .place-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .rating-value {
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }
        
        .star {
            color: #fbbf24;
            font-size: 18px;
        }
        
        .review-count {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #6b7280;
        }
        
        .recommendation {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #006a71;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .recommendation .check-icon {
            color: #006a71;
            font-size: 20px;
        }
        
        /* Image Gallery */
        .image-gallery {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 900px) {
            .image-gallery {
                grid-template-columns: 1fr;
            }
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            overflow: hidden;
            border-radius: 12px;
            position: relative;
        }
        
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .main-image:hover img {
            transform: scale(1.05);
        }
        
        .side-images {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 16px;
        }
        
        .side-image {
            width: 100%;
            height: 192px;
            overflow: hidden;
            border-radius: 12px;
            position: relative;
        }
        
        .side-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .side-image:hover img {
            transform: scale(1.05);
        }
        
        /* Tabs Navigation (flat) */
        .tabs-navigation {
            display: flex;
            gap: 24px; /* increased spacing between tabs */
            align-items: center;
            margin-bottom: 30px;
            /* removed boxed background, padding and shadow to make it flat */
            background: transparent;
            padding: 0;
            box-shadow: none;
            border-radius: 0;
        }

        .tab {
            padding: 12px 0;
            font-family: 'Geologica', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #111827;
            cursor: pointer;
            border-radius: 0;
            transition: color 0.18s ease;
            position: relative; /* for underline pseudo-element */
        }

        /* Underline pseudo-element (animated) */
        .tab::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            /* bring underline closer to text */
            bottom: -2px;
            height: 2px;
            background: #006a71;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 260ms cubic-bezier(.2,.8,.2,1);
        }

        /* Active tab: underline visible and text colored */
        .tab.active {
            color: #006a71;
        }

        .tab.active::after {
            transform: scaleX(1);
        }

        /* Hover shows animated underline (from left to right) */
        .tab:hover::after {
            transform: scaleX(1);
        }

        .tab:hover:not(.active) {
            color: #006a71;
        }
        
        /* Tab Content */
        .tab-content {
            margin-bottom: 40px;
        }
        
        .tab-section {
            display: none;
        }
        
        .tab-section.active {
            display: block;
        }
        
        .section-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .overview-text {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        /* Details Section */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(260px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }
            .detail-card {
                display: flex;
                align-items: flex-start;
                gap: 14px;
                background: #fff;
                padding: 14px;
                border-radius: 10px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            }
            .detail-icon {
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                background: linear-gradient(180deg, #f0f7f7, #e6f2f2);
                font-size: 20px;
            }
            .detail-meta {
                display: flex;
                flex-direction: column;
            }
            .detail-meta .detail-label {
                font-weight: 700;
                color: #233;
                font-size: 14px;
            }
            .detail-meta .detail-value {
                margin-top: 6px;
                color: #445;
                font-size: 13px;
                line-height: 1.35;
            }
            .chips {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 6px;
            }
            .chip {
                background: #f1f7f7;
                color: #055;
                padding: 6px 8px;
                border-radius: 16px;
                font-size: 12px;
                box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.02);
            }
            @media (max-width: 900px) {
                .details-grid {
                    grid-template-columns: 1fr;
                }
                .tabs-navigation {
                    gap: 12px;
                }
            }
        
        .detail-item {
            display: flex;
            gap: 10px;
        }
        
        .detail-label {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #111827;
            width: 180px;
        }
        
        .detail-value {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #4b5563;
            flex: 1;
        }

        /* Enhanced detail card styling */
        .detail-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .detail-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(180deg, rgba(0,106,113,0.08), rgba(0,106,113,0.03));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #006a71;
            font-size: 20px;
            flex-shrink: 0;
        }

        .detail-meta {
            flex: 1;
        }

        .detail-label {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            display: block;
            margin-bottom: 6px;
        }

        .detail-value {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #475569;
            line-height: 1.4;
        }

        .chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .chip {
            background: #f3f4f6;
            color: #0f172a;
            padding: 6px 10px;
            border-radius: 9999px;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Itinerary Section */
        .itinerary-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 900px) {
            .itinerary-container {
                grid-template-columns: 1fr;
            }
        }
        
        .itinerary-text {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .map-container {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            background-color: #e5e7eb;
            position: relative;
        }
        
        .map-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Guides Section */
        .guides-section {
            margin-bottom: 40px;
        }
        
        .guides-grid {
            display: grid;
            /* aim for 3 cards per row on wide screens, 2 on medium, 1 on small */
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }
        
        .guide-card {
            background: white;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 270px;
        }
        
        .guide-card:hover {
            transform: translateY(-8px);
        }
        
        .guide-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 12px;
        }
        
        .guide-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .guide-name {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #111827;
            margin-bottom: 6px;
            text-align: center;
        }
        
        .guide-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .guide-rating .star {
            color: #fbbf24;
            font-size: 14px;
        }

        .guide-rating .rating-value {
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            color: #6b7280;
        }
        
        .guide-description {
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            color: #4b5563;
            text-align: center;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        
        .select-guide-btn {
            width: 100%;
            background: #006a71;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.22s ease;
        }
        
        .select-guide-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        /* Responsive grid: 2 columns for medium screens, 1 column for small */
        @media (max-width: 1100px) {
            .guides-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 700px) {
            .guides-grid { grid-template-columns: 1fr; }
            .guide-card { min-height: auto; }
        }
        
        /* Reviews Section */
        .reviews-section {
            margin-bottom: 40px;
        }

        /* User review cards */
        .user-reviews { margin-top: 20px; display: grid; gap: 16px; }
        .review-card { background: #fff; padding: 14px; border-radius: 10px; box-shadow: 0 4px 12px rgba(2,6,23,0.06); }
        .review-top { display:flex; align-items:center; gap:12px; margin-bottom:8px; }
        .review-avatar { width:44px; height:44px; border-radius:50%; background:#e6eef0; display:flex; align-items:center; justify-content:center; font-weight:700; color:#055; }
        .review-meta { display:flex; flex-direction:column; }
        .reviewer-name { font-weight:700; color:#0f172a; }
        .review-date { font-size:12px; color:#6b7280; }
        .review-stars { color:#fbbf24; margin-left:6px; }
        .review-text { margin-top:8px; color:#374151; line-height:1.5; }

    /* Write review form styling (light) */
    .write-review { margin-top: 12px; margin-bottom: 16px; }
    .write-review input, .write-review textarea, .write-review select { font-family: inherit; }
        
        .reviews-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .overall-rating {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 24px;
            color: #111827;
        }
        
        .overall-stars {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .overall-star {
            color: #fbbf24;
            font-size: 20px;
        }
        
        .total-reviews {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #6b7280;
        }
        
        .rating-bars {
            margin-bottom: 30px;
        }
        
        .rating-bar {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .rating-label {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #111827;
            width: 80px;
        }
        
        .rating-progress {
            flex: 1;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .rating-fill {
            height: 100%;
            background-color: #006a71;
        }
        
        .rating-count {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .save-btn {
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.22s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            color: #006a71;
            border: 2px solid #006a71;
        }

        .save-btn:hover {
            background: #006a71;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 106, 113, 0.15);
        }

        .save-btn.saved {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .save-btn.saved:hover {
            background: #45a049;
            border-color: #45a049;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-auth {
                width: 100%;
                justify-content: center;
            }
            
            .place-title {
                font-size: 24px;
            }
            
            .image-gallery {
                grid-template-columns: 1fr;
            }
            
            .main-image {
                height: 300px;
            }
            
            .side-images {
                grid-template-rows: 1fr;
                gap: 16px;
            }
            
            .side-image {
                height: 300px;
            }
            
            .itinerary-container {
                grid-template-columns: 1fr;
            }
            
            .map-container {
                height: 400px;
            }
            
            .footer-content {
                flex-direction: column;
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
        <!-- Place Header -->
        <section class="place-header">
            <h1 class="place-title">
                <span class="location-icon">📍</span>
                Sigiriya The Ancient Rock Fortress
            </h1>
            <div class="place-rating">
                <span class="rating-value">4.7</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">☆</span>
                <span class="review-count">(2,000 reviews)</span>
                <span class="recommendation">
                    <span class="check-icon">✓</span>
                    recommended by 95% travellers
                </span>
            </div>
        </section>

        <!-- Image Gallery -->
        <section class="image-gallery">
            <div class="main-image">
                <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Sigiriya Rock Fortress">
            </div>
            <div class="side-images">
                <div class="side-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Lion's Paw Entrance">
                </div>
                <div class="side-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/rangiri.png" alt="Group Photo at Sigiriya">
                </div>
            </div>
        </section>

        <!-- Tabs Navigation -->
        <div class="tabs-navigation">
            <div class="tab active" data-tab="overview">Overview</div>
            <div class="tab" data-tab="details">Details</div>
            <div class="tab" data-tab="itinerary">Itinerary</div>
            <div class="tab" data-tab="guides">Guides</div>
            <div class="tab" data-tab="reviews">Reviews</div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-section active" id="overview">
                <h2 class="section-title">Overview</h2>
                <p class="overview-text">
                    Sigiriya, known as The Ancient Rock Fortress, is one of Sri Lanka's most iconic landmarks and a UNESCO World Heritage Site. Rising nearly 200 meters above the surrounding jungle, it was built by King Kashyapa in the 5th century AD as both a royal palace and a fortress. The site is famous for its elaborate gardens, stunning frescoes, and panoramic views from the summit. Often referred to as the Eighth Wonder of the World, Sigiriya attracts thousands of travelers each year for its unique blend of history, architecture, and natural beauty.
                </p>
                <div class="action-buttons">
                    <button class="save-btn" id="saveDestinationBtn">
                        <span>💾</span>
                        Save Destination
                    </button>
                </div>
            </div>

            <!-- Details Tab -->
            <div class="tab-section" id="details">
                <h2 class="section-title">Details</h2>

                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-icon">📍</div>
                        <div class="detail-meta">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">Sigiriya, Central Province, Sri Lanka</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📐</div>
                        <div class="detail-meta">
                            <span class="detail-label">Coordinates</span>
                            <span class="detail-value">7.9569° N, 80.7606° E</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🕘</div>
                        <div class="detail-meta">
                            <span class="detail-label">Opening Hours</span>
                            <span class="detail-value">6:00 AM – 6:00 PM (Daily) — Check local notices for exceptions</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">💵</div>
                        <div class="detail-meta">
                            <span class="detail-label">Entry Fee</span>
                            <span class="detail-value">LKR 5,000 (foreign adults) — variable (check official site)</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">♿</div>
                        <div class="detail-meta">
                            <span class="detail-label">Accessibility</span>
                            <span class="detail-value">Partial — steep steps and uneven surfaces; not fully wheelchair accessible</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🏗️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Facilities</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">Restrooms</span>
                                    <span class="chip">Food stalls</span>
                                    <span class="chip">Shaded rest areas</span>
                                    <span class="chip">Guides</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🌤️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Best Season</span>
                            <span class="detail-value">December to March — drier months with clearer views</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">⏱️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Recommended Duration</span>
                            <span class="detail-value">2–4 hours (including travel and climb)</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🧭</div>
                        <div class="detail-meta">
                            <span class="detail-label">How to Get There</span>
                            <span class="detail-value">Approximately 3–4 hours drive from Colombo; taxis, private cars, and local buses available to Dambulla/Sigiriya</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🚗</div>
                        <div class="detail-meta">
                            <span class="detail-label">Parking</span>
                            <span class="detail-value">On-site parking available (limited); early arrival recommended</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🧑‍🏫</div>
                        <div class="detail-meta">
                            <span class="detail-label">Guided Tours</span>
                            <span class="detail-value">Local guides available at the site; booking recommended for in-depth history tours</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📞</div>
                        <div class="detail-meta">
                            <span class="detail-label">Contact / Website</span>
                            <span class="detail-value">+94 77 123 4567 — official national heritage site information</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">⚠️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Safety Tips</span>
                            <span class="detail-value">Wear comfortable shoes, carry water, avoid peak midday sun, watch your step on steep sections</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🗺️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Nearby Attractions</span>
                            <span class="detail-value">Pidurangala Rock, Dambulla Cave Temple, Minneriya National Park</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itinerary Tab -->
            <div class="tab-section" id="itinerary">
                <h2 class="section-title">Itinerary</h2>
                <div class="itinerary-container">
                    <div class="itinerary-text">
                        <p>Start your journey through the beautiful landscaped gardens at the base, where you'll walk past the water gardens, boulder gardens, and terraced gardens. As you move higher, you'll encounter the world-famous Sigiriya frescoes and the mysterious Mirror Wall, which still carries ancient inscriptions.</p>
                        <p>Climbing further brings you to the impressive Lion's Paw Terrace, the gateway to the summit. From here, the final ascent takes you to the royal palace ruins at the top, where breathtaking 360° views of the jungle and villages stretch as far as the eye can see.</p>
                    </div>
                    <div class="map-container">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Sigiriya Location Map">
                    </div>
                </div>
            </div>

            <!-- Guides Tab -->
            <div class="tab-section" id="guides">
                <h2 class="section-title">Guides</h2>
                <div class="guides-grid">
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="John Doe">
                        </div>
                        <h3 class="guide-name">John Doe</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (124 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="Jane Smith">
                        </div>
                        <h3 class="guide-name">Jane Smith</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (98 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Friendly and reliable driver with a spacious vehicle. I am committed to making your safety my priority.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>
                     <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="John Doe">
                        </div>
                        <h3 class="guide-name">John Doe</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (124 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>

                     <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="John Doe">
                        </div>
                        <h3 class="guide-name">John Doe</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (124 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>

                     <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="John Doe">
                        </div>
                        <h3 class="guide-name">John Doe</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (124 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>

                     <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="John Doe">
                        </div>
                        <h3 class="guide-name">John Doe</h3>
                        <div class="guide-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.9 (124 reviews)</span>
                        </div>
                        <p class="guide-description">
                            Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.
                        </p>
                        <button class="select-guide-btn">Select Driver</button>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-section" id="reviews">
                <h2 class="section-title">Reviews</h2>
                <div class="reviews-header">
                    <span class="overall-rating">4.6</span>
                    <div class="overall-stars">
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">☆</span>
                    </div>
                    <span class="total-reviews">[1485]</span>
                </div>
                <div class="rating-bars">
                    <div class="rating-bar">
                        <span class="rating-label">Excellent</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 75%;"></div>
                        </div>
                        <span class="rating-count">724</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Very good</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 60%;"></div>
                        </div>
                        <span class="rating-count">450</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Average</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 35%;"></div>
                        </div>
                        <span class="rating-count">250</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Poor</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 10%;"></div>
                        </div>
                        <span class="rating-count">61</span>
                    </div>
                </div>
                <!-- User-written reviews list -->
                <div class="write-review">
                    <h3 style="margin-bottom:10px;">Write a review</h3>
                    <form id="reviewForm">
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="reviewerName" placeholder="Your name" style="flex:1; padding:8px; border-radius:6px; border:1px solid #e5e7eb;" required />
                            <select id="reviewRating" style="padding:8px; border-radius:6px; border:1px solid #e5e7eb;">
                                <option value="5">5 ★</option>
                                <option value="4">4 ★</option>
                                <option value="3">3 ★</option>
                                <option value="2">2 ★</option>
                                <option value="1">1 ★</option>
                            </select>
                        </div>
                        <div style="margin-bottom:8px;">
                            <textarea id="reviewText" rows="3" placeholder="Share your experience" style="width:100%; padding:8px; border-radius:6px; border:1px solid #e5e7eb;" required></textarea>
                        </div>
                        <div style="text-align:right;"><button type="submit" class="select-guide-btn" style="width:auto; padding:8px 14px;">Submit review</button></div>
                    </form>
                </div>

                <div class="user-reviews">
                    <!-- will be populated by JS -->
                </div>
            </div>
        </div>
    </main>
    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeTabs();
            initializeGuideCards();
            initializePlaceInteraction();
        });

        // Tabs functionality
        function initializeTabs() {
            const tabs = document.querySelectorAll('.tab');
            const tabSections = document.querySelectorAll('.tab-section');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab sections
                    tabSections.forEach(section => section.classList.remove('active'));
                    
                    // Show corresponding tab section
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        }

        // Guide cards interaction
        function initializeGuideCards() {
            const guideCards = document.querySelectorAll('.guide-card');

            // Randomize names from the provided list
            const names = ['Vihanga','Pasan','Chiran','Ransara'];
            guideCards.forEach(card => {
                const nameEl = card.querySelector('.guide-name');
                if (nameEl) {
                    const randomName = names[Math.floor(Math.random() * names.length)];
                    nameEl.textContent = randomName;
                }

                card.addEventListener('click', function() {
                    const guideName = this.querySelector('.guide-name').textContent;
                    console.log(`Guide selected: ${guideName}`);
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

        // Place interaction
        function initializePlaceInteraction() {
            // Save destination button interaction
            const saveBtn = document.querySelector('.save-btn');
            if (saveBtn) {
                // Check if destination is already saved
                const destinationId = 'sigiriya-rock-fortress'; // This would come from the destination data
                const isSaved = localStorage.getItem(`saved_destination_${destinationId}`) === 'true';
                
                if (isSaved) {
                    updateSaveButtonState(saveBtn, true);
                }

                saveBtn.addEventListener('click', function() {
                    const currentlySaved = this.classList.contains('saved');
                    
                    if (currentlySaved) {
                        // Remove from saved destinations
                        localStorage.removeItem(`saved_destination_${destinationId}`);
                        updateSaveButtonState(this, false);
                        showNotification('Destination removed from saved list', 'info');
                    } else {
                        // Add to saved destinations
                        localStorage.setItem(`saved_destination_${destinationId}`, 'true');
                        updateSaveButtonState(this, true);
                        showNotification('Destination saved successfully!', 'success');
                    }
                });
            }

            // Image gallery interaction
            const mainImage = document.querySelector('.main-image');
            if (mainImage) {
                mainImage.addEventListener('click', function() {
                    console.log('Main image clicked');
                    // Add zoom functionality or lightbox here
                });
            }
            
            const sideImages = document.querySelectorAll('.side-image');
            sideImages.forEach(image => {
                image.addEventListener('click', function() {
                    console.log('Side image clicked');
                    // Add zoom functionality or lightbox here
                });
            });
            
            // Rating interaction
            const ratingStars = document.querySelectorAll('.star');
            ratingStars.forEach(star => {
                star.addEventListener('mouseenter', function() {
                    this.style.color = '#ffcc00';
                });
                
                star.addEventListener('mouseleave', function() {
                    this.style.color = '#fbbf24';
                });
            });
        }

        // Populate sample user reviews under Reviews tab
        function populateSampleReviews() {
            const reviewers = ['Vihanga','Pasan','Chiran','Ransara','Abba'];
            const samples = [
                "Amazing experience — the views from the top are unforgettable. Guide was knowledgeable and friendly.",
                "Well worth the trip. Easy to navigate and plenty of spots for photos. Bring water and comfortable shoes.",
                "A historic place with great guided tours. We learned a lot about the frescoes and the palace ruins.",
                "Crowded during peak hours but the site is magical. Book a morning slot for better weather.",
                "Highly recommended. The local guides added a lot of context and made the climb fun for the family."
            ];

            const container = document.querySelector('.user-reviews');
            if (!container) return;

            // Create 5 reviews
            for (let i=0;i<5;i++){
                const name = reviewers[Math.floor(Math.random()*reviewers.length)];
                const text = samples[Math.floor(Math.random()*samples.length)];
                const rating = 4 + Math.round(Math.random()); // 4 or 5

                const card = document.createElement('div');
                card.className = 'review-card';
                card.innerHTML = `
                    <div class="review-top">
                        <div class="review-avatar">${name.charAt(0)}</div>
                        <div class="review-meta">
                            <div><span class="reviewer-name">${name}</span> <span class="review-date">· ${new Date().toLocaleDateString()}</span></div>
                            <div class="review-stars">${'★'.repeat(rating)}${'☆'.repeat(5-rating)}</div>
                        </div>
                    </div>
                    <div class="review-text">${text}</div>
                `;

                container.appendChild(card);
            }
        }

        // Handle write-review form submission
        (function setupReviewForm(){
            const form = document.getElementById('reviewForm');
            if (!form) return;

            form.addEventListener('submit', function(e){
                e.preventDefault();
                const name = document.getElementById('reviewerName').value.trim();
                const rating = parseInt(document.getElementById('reviewRating').value,10) || 5;
                const text = document.getElementById('reviewText').value.trim();

                if (!name || !text) { showNotification('Please fill in your name and review text','error'); return; }

                const container = document.querySelector('.user-reviews');
                if (!container) return;

                const card = document.createElement('div');
                card.className = 'review-card';
                card.innerHTML = `
                    <div class="review-top">
                        <div class="review-avatar">${name.charAt(0)}</div>
                        <div class="review-meta">
                            <div><span class="reviewer-name">${name}</span> <span class="review-date">· ${new Date().toLocaleDateString()}</span></div>
                            <div class="review-stars">${'★'.repeat(rating)}${'☆'.repeat(5-rating)}</div>
                        </div>
                    </div>
                    <div class="review-text">${text}</div>
                `;

                // Prepend the new review
                container.insertBefore(card, container.firstChild);

                // Clear form
                form.reset();

                showNotification('Thanks — your review was added', 'success');
            });
        })();

        // Update save button state
        function updateSaveButtonState(button, isSaved) {
            if (isSaved) {
                button.classList.add('saved');
                button.innerHTML = '<span>✓</span>Saved';
            } else {
                button.classList.remove('saved');
                button.innerHTML = '<span>💾</span>Save Destination';
            }
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
        window.PlacePage = {
            showNotification,
            selectGuide: function(guideName) {
                console.log(`Programmatically selected guide: ${guideName}`);
                showNotification(`Selected guide: ${guideName}`, 'success');
            }
        };

        // After initial load, populate sample reviews
        document.addEventListener('DOMContentLoaded', function(){ populateSampleReviews(); });
    </script>
</body>
</html>


