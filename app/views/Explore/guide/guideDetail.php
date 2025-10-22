<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarah Wilson - Professional Guide</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        
        /* Guide Header */
        .guide-header {
            margin-bottom: 30px;
        }
        
        .guide-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #111827;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .guide-title .guide-icon {
            font-size: 24px;
            color: #006a71;
        }
        
        .guide-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            justify-content: flex-start;
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
        
        .verification {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #006a71;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .verification .check-icon {
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
            gap: 24px;
            align-items: center;
            margin-bottom: 30px;
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
            position: relative;
        }

        .tab::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -2px;
            height: 2px;
            background: #006a71;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 260ms cubic-bezier(.2,.8,.2,1);
        }

        .tab.active {
            color: #006a71;
        }

        .tab.active::after {
            transform: scaleX(1);
        }

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
            background: linear-gradient(180deg, rgba(0,106,113,0.08), rgba(0,106,113,0.03));
            font-size: 20px;
            color: #006a71;
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
        
        /* Locations Section */
        .locations-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1000px) {
            .locations-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .locations-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .location-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        
        .location-card:hover {
            transform: translateY(-8px);
        }
        
        .location-image {
            width: 100%;
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .location-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .location-name {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .location-details {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 15px;
        }
        
        .location-duration {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background: #d4edda;
            color: #155724;
        }
        
        /* Tours Section */
        .tours-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 900px) {
            .tours-container {
                grid-template-columns: 1fr;
            }
        }
        
        .tours-text {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .stats-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #006a71;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        /* Reviews Section */
        .reviews-section {
            margin-bottom: 40px;
        }

        .user-reviews { 
            margin-top: 20px; 
            display: grid; 
            gap: 16px; 
        }

        .review-card { 
            background: #fff; 
            padding: 14px; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(2,6,23,0.06); 
        }

        .review-top { 
            display:flex; 
            align-items:center; 
            gap:12px; 
            margin-bottom:8px; 
        }

        .review-avatar { 
            width:44px; 
            height:44px; 
            border-radius:50%; 
            background:#e6eef0; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            font-weight:700; 
            color:#055; 
        }

        .review-meta { 
            display:flex; 
            flex-direction:column; 
        }

        .reviewer-name { 
            font-weight:700; 
            color:#0f172a; 
        }

        .review-date { 
            font-size:12px; 
            color:#6b7280; 
        }

        .review-stars { 
            color:#fbbf24; 
            margin-left:6px; 
        }

        .review-text { 
            margin-top:8px; 
            color:#374151; 
            line-height:1.5; 
        }

        /* Write review form styling (light) */
        .write-review { 
            margin-top: 12px; 
            margin-bottom: 16px; 
        }
        
        .write-review input, 
        .write-review textarea, 
        .write-review select { 
            font-family: inherit; 
        }
        
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

        .contact-btn, .save-btn {
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
        }

        .contact-btn {
            background: #006a71;
            color: white;
        }
        
        .contact-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .save-btn {
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
            .guide-title {
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
            
            .tours-container {
                grid-template-columns: 1fr;
            }
            
            .locations-grid {
                grid-template-columns: 1fr;
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
        <!-- Guide Header -->
        <section class="guide-header">
            <h1 class="guide-title">
                <span class="guide-icon">🎯</span>
                Sarah Wilson - Professional Guide
            </h1>
            <div class="guide-rating">
                <span class="rating-value">4.9</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="review-count">(156 reviews)</span>
                <span class="verification">
                    <span class="check-icon">✓</span>
                    Verified Guide
                </span>
            </div>
        </section>

        <!-- Image Gallery -->
        <section class="image-gallery">
            <div class="main-image">
                <img src="https://placehold.co/800x400/1f2937/ffffff?text=Sarah+Wilson+Guide" alt="Sarah Wilson - Professional Guide">
            </div>
            <div class="side-images">
                <div class="side-image">
                    <img src="https://placehold.co/400x192/1f2937/ffffff?text=Tour+in+Progress" alt="Tour in Progress">
                </div>
                <div class="side-image">
                    <img src="https://placehold.co/400x192/1f2937/ffffff?text=Happy+Travelers" alt="Happy Travelers">
                </div>
            </div>
        </section>

        <!-- Tabs Navigation -->
        <div class="tabs-navigation">
            <div class="tab active" data-tab="overview">Overview</div>
            <div class="tab" data-tab="details">Details</div>
            <div class="tab" data-tab="locations">Guiding Locations</div>
            <div class="tab" data-tab="experience">Tours & Experience</div>
            <div class="tab" data-tab="reviews">Reviews</div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-section active" id="overview">
                <h2 class="section-title">Overview</h2>
                <p class="overview-text">
                    Certified tour guide with 5 years of professional guiding experience in Paris and surrounding areas. Sarah specializes in historical landmarks, cultural attractions, and hidden local gems, providing immersive and educational experiences for travelers from around the world. Fluent in English, French, and Spanish, ensuring comfortable communication with international visitors.
                </p>
                <p class="overview-text">
                    With extensive knowledge of Paris history, architecture, and culture, Sarah crafts personalized tour experiences that go beyond basic sightseeing. Whether you're interested in Renaissance art at the Louvre, Gothic architecture at Notre Dame, or discovering charming neighborhood secrets in Montmartre, Sarah creates memorable journeys tailored to your interests.
                </p>
                <div class="action-buttons">
                    <button class="contact-btn">
                        <span>📞</span>
                        Contact Guide
                    </button>
                    <button class="save-btn" id="saveGuideBtn">
                        <span>💾</span>
                        Save Guide
                    </button>
                </div>
            </div>

            <!-- Details Tab -->
            <div class="tab-section" id="details">
                <h2 class="section-title">Guide Details</h2>

                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-icon">👤</div>
                        <div class="detail-meta">
                            <span class="detail-label">Full Name</span>
                            <span class="detail-value">Sarah Wilson</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🌟</div>
                        <div class="detail-meta">
                            <span class="detail-label">Guiding Experience</span>
                            <span class="detail-value">5 years of professional tour guiding in Paris</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🗣️</div>
                        <div class="detail-meta">
                            <span class="detail-label">Languages</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">English</span>
                                    <span class="chip">French</span>
                                    <span class="chip">Spanish</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📍</div>
                        <div class="detail-meta">
                            <span class="detail-label">Service Areas</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">Paris</span>
                                    <span class="chip">Île-de-France</span>
                                    <span class="chip">Versailles</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🎯</div>
                        <div class="detail-meta">
                            <span class="detail-label">Specializations</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">Historical Tours</span>
                                    <span class="chip">Art & Culture</span>
                                    <span class="chip">Architecture</span>
                                    <span class="chip">Local Secrets</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📞</div>
                        <div class="detail-meta">
                            <span class="detail-label">Contact</span>
                            <span class="detail-value">+33 1 98 76 54 32</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">✅</div>
                        <div class="detail-meta">
                            <span class="detail-label">Verification Status</span>
                            <span class="detail-value">Fully verified guide with official certification</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🕒</div>
                        <div class="detail-meta">
                            <span class="detail-label">Availability</span>
                            <span class="detail-value">Available 7 days a week, 8:00 AM - 8:00 PM</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">💳</div>
                        <div class="detail-meta">
                            <span class="detail-label">Payment Methods</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">Credit Card</span>
                                    <span class="chip">Cash</span>
                                    <span class="chip">PayPal</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🎓</div>
                        <div class="detail-meta">
                            <span class="detail-label">Certifications</span>
                            <div class="detail-value">
                                <div class="chips">
                                    <span class="chip">Certified Tour Guide</span>
                                    <span class="chip">Art History Degree</span>
                                    <span class="chip">First Aid Certified</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Locations Tab -->
            <div class="tab-section" id="locations">
                <h2 class="section-title">Guiding Locations</h2>
                <div class="locations-grid">
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Eiffel+Tower" alt="Eiffel Tower">
                        </div>
                        <div class="location-name">Eiffel Tower</div>
                        <div class="location-details">
                            <strong>City:</strong> Paris<br>
                            <strong>Specialty:</strong> Architectural History<br>
                            <strong>Languages:</strong> EN, FR, ES
                        </div>
                        <span class="location-duration">3 hours tour</span>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Louvre+Museum" alt="Louvre Museum">
                        </div>
                        <div class="location-name">Louvre Museum</div>
                        <div class="location-details">
                            <strong>City:</strong> Paris<br>
                            <strong>Specialty:</strong> Art & Renaissance History<br>
                            <strong>Languages:</strong> EN, FR, ES
                        </div>
                        <span class="location-duration">4 hours tour</span>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Montmartre" alt="Montmartre">
                        </div>
                        <div class="location-name">Montmartre</div>
                        <div class="location-details">
                            <strong>City:</strong> Paris<br>
                            <strong>Specialty:</strong> Local Culture & Hidden Gems<br>
                            <strong>Languages:</strong> EN, FR
                        </div>
                        <span class="location-duration">3 hours tour</span>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Versailles+Palace" alt="Versailles Palace">
                        </div>
                        <div class="location-name">Versailles Palace</div>
                        <div class="location-details">
                            <strong>City:</strong> Versailles<br>
                            <strong>Specialty:</strong> Royal History & Gardens<br>
                            <strong>Languages:</strong> EN, FR, ES
                        </div>
                        <span class="location-duration">6 hours tour</span>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Notre+Dame" alt="Notre Dame">
                        </div>
                        <div class="location-name">Notre Dame</div>
                        <div class="location-details">
                            <strong>City:</strong> Paris<br>
                            <strong>Specialty:</strong> Gothic Architecture & History<br>
                            <strong>Languages:</strong> EN, FR
                        </div>
                        <span class="location-duration">2 hours tour</span>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-image">
                            <img src="https://placehold.co/300x180/1f2937/ffffff?text=Latin+Quarter" alt="Latin Quarter">
                        </div>
                        <div class="location-name">Latin Quarter</div>
                        <div class="location-details">
                            <strong>City:</strong> Paris<br>
                            <strong>Specialty:</strong> Student Life & Medieval History<br>
                            <strong>Languages:</strong> EN, FR, ES
                        </div>
                        <span class="location-duration">3 hours tour</span>
                    </div>
                </div>
            </div>

            <!-- Experience Tab -->
            <div class="tab-section" id="experience">
                <h2 class="section-title">Tours & Experience</h2>
                <div class="tours-container">
                    <div class="tours-text">
                        <p>With 5 years of professional guiding experience and extensive knowledge of Paris history, culture, and hidden gems, Sarah offers comprehensive tour experiences that combine education with entertainment. Her passion for storytelling and deep understanding of local culture ensures every tour is memorable and engaging.</p>
                        <p><strong>Popular Tour Experiences:</strong></p>
                        <ul style="margin-left: 20px; margin-top: 10px;">
                            <li>Art & Renaissance History at the Louvre</li>
                            <li>Gothic Architecture & Notre Dame</li>
                            <li>Hidden Montmartre Secrets & Local Culture</li>
                            <li>Royal History at Versailles Palace</li>
                            <li>Medieval Paris & Latin Quarter</li>
                            <li>Eiffel Tower Architectural Stories</li>
                        </ul>
                    </div>
                    <div class="stats-container">
                        <h3 style="margin-bottom: 15px; color: #006a71;">Guide Statistics</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number">156</div>
                                <div class="stat-label">Completed Tours</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">4.9</div>
                                <div class="stat-label">Average Rating</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Years Experience</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">6</div>
                                <div class="stat-label">Guiding Locations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-section" id="reviews">
                <h2 class="section-title">Reviews</h2>
                <div class="reviews-header">
                    <span class="overall-rating">4.9</span>
                    <div class="overall-stars">
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                    </div>
                    <span class="total-reviews">[156]</span>
                </div>
                <div class="rating-bars">
                    <div class="rating-bar">
                        <span class="rating-label">Excellent</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 92%;"></div>
                        </div>
                        <span class="rating-count">144</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Very good</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 6%;"></div>
                        </div>
                        <span class="rating-count">9</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Average</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 2%;"></div>
                        </div>
                        <span class="rating-count">3</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Poor</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 0%;"></div>
                        </div>
                        <span class="rating-count">0</span>
                    </div>
                </div>

                <!-- Write a Review Section -->
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
                            <textarea id="reviewText" rows="3" placeholder="Share your experience with this guide" style="width:100%; padding:8px; border-radius:6px; border:1px solid #e5e7eb;" required></textarea>
                        </div>
                        <div style="text-align:right;">
                            <button type="submit" class="contact-btn" style="width:auto; padding:8px 14px;">Submit review</button>
                        </div>
                    </form>
                </div>

                <div class="user-reviews">
                    <!-- Reviews will be populated by JS -->
                </div>
            </div>
        </div>
    </main>
    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeTabs();
            initializeGuideInteraction();
            populateGuideReviews();
            initializeReviewForm();
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

        // Guide interaction
        function initializeGuideInteraction() {
            // Contact button interaction
            const contactBtn = document.querySelector('.contact-btn');
            if (contactBtn) {
                contactBtn.addEventListener('click', function() {
                    console.log('Contact guide clicked');
                    showNotification('Contact form would open here', 'info');
                });
            }

            // Save guide button interaction
            const saveBtn = document.querySelector('.save-btn');
            if (saveBtn) {
                // Check if guide is already saved
                const guideId = 'sarah-wilson'; // This would come from the guide data
                const isSaved = localStorage.getItem(`saved_guide_${guideId}`) === 'true';
                
                if (isSaved) {
                    updateSaveButtonState(saveBtn, true);
                }

                saveBtn.addEventListener('click', function() {
                    const currentlySaved = this.classList.contains('saved');
                    
                    if (currentlySaved) {
                        // Remove from saved guides
                        localStorage.removeItem(`saved_guide_${guideId}`);
                        updateSaveButtonState(this, false);
                        showNotification('Guide removed from saved list', 'info');
                    } else {
                        // Add to saved guides
                        localStorage.setItem(`saved_guide_${guideId}`, 'true');
                        updateSaveButtonState(this, true);
                        showNotification('Guide saved successfully!', 'success');
                    }
                });
            }

            // Location card interactions
            const locationCards = document.querySelectorAll('.location-card');
            locationCards.forEach(card => {
                card.addEventListener('click', function() {
                    console.log('Location card clicked');
                    showNotification('Location details would be shown', 'info');
                });

                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Image gallery interaction
            const mainImage = document.querySelector('.main-image');
            if (mainImage) {
                mainImage.addEventListener('click', function() {
                    console.log('Main image clicked');
                });
            }
            
            const sideImages = document.querySelectorAll('.side-image');
            sideImages.forEach(image => {
                image.addEventListener('click', function() {
                    console.log('Side image clicked');
                });
            });
        }

        // Populate guide reviews
        function populateGuideReviews() {
            const reviewers = ['Emma Thompson', 'Michael Brown', 'Lisa Anderson', 'James Wilson', 'Sophie Martin'];
            const samples = [
                "Fantastic guide! Sarah's knowledge of Paris history is impressive and she made our tour incredibly engaging.",
                "Highly recommend Sarah! She showed us hidden gems we never would have found on our own.",
                "Professional and passionate guide. The Louvre tour was educational and fun for the whole family.",
                "Sarah went above and beyond to customize our tour. Her storytelling brought the city to life.",
                "Excellent experience with Sarah. She speaks multiple languages fluently and was very accommodating."
            ];

            const container = document.querySelector('.user-reviews');
            if (!container) return;

            // Create 5 reviews
            for (let i = 0; i < 5; i++) {
                const name = reviewers[Math.floor(Math.random() * reviewers.length)];
                const text = samples[Math.floor(Math.random() * samples.length)];
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

        // Initialize review form functionality
        function initializeReviewForm() {
            const form = document.getElementById('reviewForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = document.getElementById('reviewerName').value.trim();
                const rating = parseInt(document.getElementById('reviewRating').value, 10) || 5;
                const text = document.getElementById('reviewText').value.trim();

                if (!name || !text) {
                    showNotification('Please fill in your name and review text', 'error');
                    return;
                }

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

                // Prepend the new review to the top
                container.insertBefore(card, container.firstChild);

                // Clear form
                form.reset();

                showNotification('Thanks! Your review was added successfully', 'success');
            });
        }

        // Update save button state
        function updateSaveButtonState(button, isSaved) {
            if (isSaved) {
                button.classList.add('saved');
                button.innerHTML = '<span>✓</span>Saved';
            } else {
                button.classList.remove('saved');
                button.innerHTML = '<span>💾</span>Save Guide';
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
        window.GuidePage = {
            showNotification,
            contactGuide: function() {
                console.log('Contact guide functionality');
                showNotification('Contacting Sarah Wilson...', 'success');
            }
        };
    </script>
</body>
</html>