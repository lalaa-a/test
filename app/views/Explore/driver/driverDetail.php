<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>David Brown - Professional Driver</title>
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
        
        /* Driver Header */
        .driver-header {
            margin-bottom: 30px;
        }
        
        .driver-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #111827;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .driver-title .driver-icon {
            font-size: 24px;
            color: #006a71;
        }
        
        .driver-rating {
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
        
        /* Vehicle Section */
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1000px) {
            .vehicles-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .vehicles-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .vehicle-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        
        .vehicle-card:hover {
            transform: translateY(-8px);
        }
        
        .vehicle-image {
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
        
        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .vehicle-name {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .vehicle-details {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 15px;
        }
        
        .vehicle-status {
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
            .driver-title {
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
            
            .vehicles-grid {
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
        <!-- Driver Header -->
        <section class="driver-header">
            <h1 class="driver-title">
                <span class="driver-icon">🚗</span>
                Darshana Mihiranga
            </h1>
            <div class="driver-rating">
                <span class="rating-value">4.8</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">☆</span>
                <span class="review-count">(127 reviews)</span>
                <span class="verification">
                    <span class="check-icon">✓</span>
                    Verified Driver
                </span>
            </div>
        </section>

        <!-- Image Gallery -->
        <section class="image-gallery">
            <div class="main-image">
                <img src="<?php echo IMG_ROOT.'/explore/drivers/driverPic1.png'?>" alt="David Brown - Professional Driver">
            </div>
            <div class="side-images">
                <div class="side-image">
                    <img src="<?php echo IMG_ROOT.'/explore/drivers/driverPic2.png'?>" alt="Driver License">
                </div>
                <div class="side-image">
                    <img src="<?php echo IMG_ROOT.'/explore/drivers/driverPic3.png'?>" alt="Professional Service">
                </div>
            </div>
        </section>

        <!-- Tabs Navigation -->
        <div class="tabs-navigation">
            <div class="tab active" data-tab="overview">Overview</div>
            <div class="tab" data-tab="details">Details</div>
            <div class="tab" data-tab="vehicle">Vehicle</div>
            <div class="tab" data-tab="tours">Tours & Experience</div>
            <div class="tab" data-tab="reviews">Reviews</div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-section active" id="overview">
                <h2 class="section-title">Overview</h2>
                <p class="overview-text">
                    Professional driver with 8 years of experience providing exceptional transportation services. David specializes in tourist tours, airport transfers, and city exploration with extensive knowledge of local attractions and hidden gems. Fluent in English, French, and Spanish, ensuring comfortable communication with international travelers. Committed to passenger safety, comfort, and creating memorable travel experiences.
                </p>
                <p class="overview-text">
                    Operating with verified vehicles and proper licensing, David maintains the highest standards of professionalism and reliability. Whether you need a quick airport transfer or a full-day sightseeing tour, you can count on punctual, friendly, and knowledgeable service.
                </p>
                <div class="action-buttons">
                    <button class="contact-btn">
                        <span>📞</span>
                        Contact Driver
                    </button>
                    <button class="save-btn" id="saveDriverBtn">
                        <span>💾</span>
                        Save Driver
                    </button>
                </div>
            </div>

            <!-- Details Tab -->
            <div class="tab-section" id="details">
                <h2 class="section-title">Driver Details</h2>

                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-icon">👤</div>
                        <div class="detail-meta">
                            <span class="detail-label">Full Name</span>
                            <span class="detail-value">David Brown</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🌟</div>
                        <div class="detail-meta">
                            <span class="detail-label">Experience</span>
                            <span class="detail-value">8 years of professional driving experience</span>
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
                                    <span class="chip">Airport Transfers</span>
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
                                    <span class="chip">Tourist Tours</span>
                                    <span class="chip">Business Travel</span>
                                    <span class="chip">Airport Transfers</span>
                                    <span class="chip">City Exploration</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📞</div>
                        <div class="detail-meta">
                            <span class="detail-label">Contact</span>
                            <span class="detail-value">+94782498755</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">✅</div>
                        <div class="detail-meta">
                            <span class="detail-label">Verification Status</span>
                            <span class="detail-value">Fully verified driver with valid license and insurance</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">🕒</div>
                        <div class="detail-meta">
                            <span class="detail-label">Availability</span>
                            <span class="detail-value">Available 7 days a week, 6:00 AM - 10:00 PM</span>
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
                                    <span class="chip">Digital Payment</span>
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
                                    <span class="chip">Professional License</span>
                                    <span class="chip">Tourism Guide</span>
                                    <span class="chip">First Aid</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Tab -->
            <div class="tab-section" id="vehicle">
                <h2 class="section-title">Available Vehicles</h2>
                <div class="vehicles-grid">
                    <div class="vehicle-card">
                        <div class="vehicle-image">
                            <img src="<?php echo IMG_ROOT.'/explore/drivers/vehicle1.png'?>" alt="Toyota Camry 2023">
                        </div>
                        <div class="vehicle-name">Toyota Camry 2023</div>
                        <div class="vehicle-details">
                            <strong>License Plate:</strong> ABC-123<br>
                            <strong>Type:</strong> Sedan<br>
                            <strong>Capacity:</strong> 4 passengers<br>
                            
                        </div>
                        <span class="vehicle-status">✓ Verified</span>
                    </div>
                    <div class="vehicle-card">
                        <div class="vehicle-image">
                            <img src="<?php echo IMG_ROOT.'/explore/drivers/vehicle2.png'?>" alt="Honda Accord 2022">
                        </div>
                        <div class="vehicle-name">Honda Accord 2022</div>
                        <div class="vehicle-details">
                            <strong>License Plate:</strong> XYZ-789<br>
                            <strong>Type:</strong> Sedan<br>
                            <strong>Capacity:</strong> 4 passengers<br>
                            
                        </div>
                        <span class="vehicle-status">✓ Verified</span>
                    </div>
                </div>
            </div>

            <!-- Tours Tab -->
            <div class="tab-section" id="tours">
                <h2 class="section-title">Tours & Experience</h2>
                <div class="tours-container">
                    <div class="tours-text">
                        <p>With 8 years of professional driving experience and extensive knowledge of the local area, David offers personalized tour experiences that go beyond basic transportation. Whether you're interested in historical landmarks, cultural attractions, or hidden local gems, David crafts each journey to match your interests and preferences.</p>
                        <p><strong>Popular Tour Routes:</strong></p>
                        <ul style="margin-left: 20px; margin-top: 10px;">
                            <li>Paris City Highlights Tour</li>
                            <li>Airport Transfer with City Overview</li>
                            <li>Customized Cultural Experience</li>
                            <li>Business District Professional Transport</li>
                            <li>Evening Entertainment District Tours</li>
                        </ul>
                    </div>
                    <div class="stats-container">
                        <h3 style="margin-bottom: 15px; color: #006a71;">Driver Statistics</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number">127</div>
                                <div class="stat-label">Completed Trips</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">4.8</div>
                                <div class="stat-label">Average Rating</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">8</div>
                                <div class="stat-label">Years Experience</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">2</div>
                                <div class="stat-label">Verified Vehicles</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-section" id="reviews">
                <h2 class="section-title">Reviews</h2>
                <div class="reviews-header">
                    <span class="overall-rating">4.8</span>
                    <div class="overall-stars">
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                        <span class="overall-star">★</span>
                    </div>
                    <span class="total-reviews">[127]</span>
                </div>
                <div class="rating-bars">
                    <div class="rating-bar">
                        <span class="rating-label">Excellent</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 85%;"></div>
                        </div>
                        <span class="rating-count">108</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Very good</span>
                        <div class="rating-progress">
                            <div class="rating-fill" style="width: 12%;"></div>
                        </div>
                        <span class="rating-count">15</span>
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
                            <div class="rating-fill" style="width: 1%;"></div>
                        </div>
                        <span class="rating-count">1</span>
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
                            <textarea id="reviewText" rows="3" placeholder="Share your experience with this driver" style="width:100%; padding:8px; border-radius:6px; border:1px solid #e5e7eb;" required></textarea>
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
            initializeDriverInteraction();
            populateDriverReviews();
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

        // Driver interaction
        function initializeDriverInteraction() {
            // Contact button interaction
            const contactBtn = document.querySelector('.contact-btn');
            if (contactBtn) {
                contactBtn.addEventListener('click', function() {
                    console.log('Contact driver clicked');
                    showNotification('Contact form would open here', 'info');
                });
            }

            // Save driver button interaction
            const saveBtn = document.querySelector('.save-btn');
            if (saveBtn) {
                // Check if driver is already saved
                const driverId = 'david-brown'; // This would come from the driver data
                const isSaved = localStorage.getItem(`saved_driver_${driverId}`) === 'true';
                
                if (isSaved) {
                    updateSaveButtonState(saveBtn, true);
                }

                saveBtn.addEventListener('click', function() {
                    const currentlySaved = this.classList.contains('saved');
                    
                    if (currentlySaved) {
                        // Remove from saved drivers
                        localStorage.removeItem(`saved_driver_${driverId}`);
                        updateSaveButtonState(this, false);
                        showNotification('Driver removed from saved list', 'info');
                    } else {
                        // Add to saved drivers
                        localStorage.setItem(`saved_driver_${driverId}`, 'true');
                        updateSaveButtonState(this, true);
                        showNotification('Driver saved successfully!', 'success');
                    }
                });
            }

            // Vehicle card interactions
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            vehicleCards.forEach(card => {
                card.addEventListener('click', function() {
                    console.log('Vehicle card clicked');
                    showNotification('Vehicle details would be shown', 'info');
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

        // Populate driver reviews
        function populateDriverReviews() {
            const reviewers = ['John Smith', 'Sarah Johnson', 'Mike Wilson', 'Emily Davis', 'Robert Brown'];
            const samples = [
                "Excellent driver! David was punctual, professional, and very knowledgeable about the city. Highly recommend!",
                "Great service from David. Clean vehicle, smooth ride, and he gave us great recommendations for restaurants.",
                "Professional and friendly driver. Made our airport transfer stress-free and comfortable.",
                "David went above and beyond to make our city tour memorable. His knowledge of local history was impressive.",
                "Reliable and courteous driver. Would definitely book with David again for future trips."
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

        // Update save button state
        function updateSaveButtonState(button, isSaved) {
            if (isSaved) {
                button.classList.add('saved');
                button.innerHTML = '<span>✓</span>Saved';
            } else {
                button.classList.remove('saved');
                button.innerHTML = '<span>💾</span>Save Driver';
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
        window.DriverPage = {
            showNotification,
            contactDriver: function() {
                console.log('Contact driver functionality');
                showNotification('Contacting David Brown...', 'success');
            }
        };
    </script>
</body>
</html>
