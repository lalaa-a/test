<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Relaxation & Leisure Destinations</title>
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
        
        .btn-register {
            background: none;
            border: none;
            font-weight: 600;
            color: #111827;
            cursor: pointer;
        }
        
        .btn-signin {
            background-color: #006a71;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-signin:hover {
            background-color: #005a61;
        }
        
        /* Page Title */
        .page-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #374151;
            margin-bottom: 30px;
            text-align: center;
            padding: 20px 0;
        }
        
        /* Main Container with Sidebar */
        .content-container {
            display: flex;
            gap: 30px;
            margin-bottom: 80px;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        
        .sidebar-title {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #374151;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .filter-section {
            margin-bottom: 25px;
        }
        
        .filter-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #111827;
            margin-bottom: 12px;
        }
        
        .filter-option {
            margin-bottom: 10px;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .checkbox-container input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #006a71;
        }
        
        .checkbox-container label {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
        }
        
        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #111827;
            margin-bottom: 10px;
        }
        
        .rating-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .star {
            color: #fbbf24;
            font-size: 16px;
        }
        
        .rating-label {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
        }
        
        .price-range {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .price-range span {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
        }
        
        .price-slider {
            flex: 1;
            height: 6px;
            background: linear-gradient(to right, #006a71, #005a61);
            border-radius: 3px;
            position: relative;
        }
        
        .price-slider::before {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: #006a71;
            border-radius: 50%;
            left: 30%;
        }
        
        .apply-filters-btn {
            width: 100%;
            background: #006a71;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .apply-filters-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Main Content Area */
        .destinations-grid {
            flex: 1;
            display: grid;
            /* Force three equal columns on wider screens */
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        /* Two columns on medium screens */
        @media (max-width: 1000px) {
            .destinations-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
            }
        }

        /* Single column on small screens */
        @media (max-width: 600px) {
            .destinations-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        
        .destination-card {
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
        
        .destination-card:focus {
            box-shadow: 0 0 0 3px #006a71, 0 4px 24px 0 rgba(0,0,0,0.13);
        }
        
        .destination-card:hover, .destination-card:focus-visible {
            transform: scale(1.035);
            filter: brightness(1.08);
            z-index: 3;
        }
        
        .destination-image {
            width: 100%;
            height: 150px;
            overflow: hidden;
        }
        
        .destination-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .destination-card:hover .destination-image img {
            transform: scale(1.05);
        }
        
        .destination-info {
            padding: 18px;
            background: white;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .destination-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            margin-bottom: 6px;
        }
        
        .destination-category {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 9999px;
            background-color: #f3f4f6;
        }
        
        .destination-rating {
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
        
        .destination-description {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.45;
            margin-bottom: 18px;
            flex: 1;
        }
        
        .explore-destination-btn {
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
        
        .explore-destination-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Footer */
        .footer {
            background-color: #e0f2fe;
            padding: 30px 20px;
            margin-top: 80px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .footer-brand {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-brand h3 {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #111827;
            margin-bottom: 10px;
        }
        
        .footer-brand p {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #374151;
            line-height: 1.5;
        }
        
        .footer-links {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-links h3 {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #111827;
            margin-bottom: 15px;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            text-decoration: none;
            color: #374151;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #006a71;
        }
        
        .footer-social {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-social h3 {
            font-family: 'Geologica', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #111827;
            margin-bottom: 15px;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #bae6fd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #006a71;
            font-size: 20px;
            transition: background-color 0.3s ease;
        }
        
        .social-icon:hover {
            background-color: #006a71;
            color: white;
        }
        
        .footer-copyright {
            width: 100%;
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #bae6fd;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #374151;
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
            
            .content-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: static;
                margin-bottom: 20px;
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
    <!-- Page Title -->
    <h1 class="page-title">Relaxation & Leisure Destinations</h1>
        
        <!-- Content Container with Sidebar -->
        <div class="content-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h2 class="sidebar-title">Filter By</h2>

                <!-- Relaxation Type (place-based) -->
                <div class="filter-section">
                    <h3 class="filter-title">Relaxation Type</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="beach">
                            <label for="beach">Beaches</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="park">
                            <label for="park">Parks</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="garden">
                            <label for="garden">Gardens</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="viewpoint">
                            <label for="viewpoint">Viewpoints</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="waterfall">
                            <label for="waterfall">Waterfalls</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="lake">
                            <label for="lake">Lakes</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="island">
                            <label for="island">Islands</label>
                        </div>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="filter-section">
                    <h3 class="filter-title">Facilities</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="wifi">
                            <label for="wifi">Free Wi‑Fi</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="pool">
                            <label for="pool">Pool</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="parking">
                            <label for="parking">Parking</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="accessible">
                            <label for="accessible">Wheelchair Accessible</label>
                        </div>
                    </div>
                </div>

                <!-- Ambience / Crowd -->
                <div class="filter-section">
                    <h3 class="filter-title">Ambience</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="quiet">
                            <label for="quiet">Quiet / Tranquil</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="family-friendly">
                            <label for="family-friendly">Family Friendly</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="pet-friendly">
                            <label for="pet-friendly">Pet Friendly</label>
                        </div>
                    </div>
                </div>

                <!-- Duration / Visit Time -->
                <div class="filter-section">
                    <h3 class="filter-title">Visit Length</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="short-visit">
                            <label for="short-visit">Short (under 2 hours)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="half-day">
                            <label for="half-day">Half Day</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="full-day">
                            <label for="full-day">Full Day</label>
                        </div>
                    </div>
                </div>

                <!-- Booking / Price -->
                <div class="filter-section">
                    <h3 class="filter-title">Booking / Price</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="walk-in">
                            <label for="walk-in">Walk-in Available</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="advance-booking">
                            <label for="advance-booking">Advance Booking</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="free-entry">
                            <label for="free-entry">Free Entry</label>
                        </div>
                    </div>
                </div>

                <!-- Region -->
                <div class="filter-section">
                    <h3 class="filter-title">Region</h3>
                    <input type="text" class="filter-input" placeholder="e.g. Colombo coastline">
                </div>

                <!-- Rating -->
                <div class="filter-section">
                    <h3 class="filter-title">Rating</h3>
                    <div class="rating-filter">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">☆</span>
                        <span class="rating-label">4.0+</span>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter-section">
                    <h3 class="filter-title">Price Range</h3>
                    <div class="price-range">
                        <span>Free</span>
                        <div class="price-slider"></div>
                        <span>$300+</span>
                    </div>
                </div>

                <button class="apply-filters-btn">Apply Filters</button>
            </aside>
            
            <!-- Main Content Area -->
            <div class="destinations-grid">
                <!-- Destination Card 1 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Anuradhapura" alt="Anuradhapura">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Anuradhapura</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (312 reviews)</span>
                        </div>
                        <p class="destination-description">Ancient capital of Sri Lanka with magnificent ruins, sacred sites, and Buddhist monuments dating back over 2,000 years.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 2 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Polonnaruwa" alt="Polonnaruwa">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Polonnaruwa</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (267 reviews)</span>
                        </div>
                        <p class="destination-description">Ancient city with well-preserved ruins, temples, and statues showcasing the architectural brilliance of medieval Sri Lanka.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 3 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Dambulla" alt="Dambulla">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Dambulla</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.5 (174 reviews)</span>
                        </div>
                        <p class="destination-description">Ancient cave temple complex with stunning Buddhist art and statues.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 4 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Kandy" alt="Kandy">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Kandy</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.8 (156 reviews)</span>
                        </div>
                        <p class="destination-description">Cultural capital with the Temple of Tooth and beautiful lake views.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 5 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Sigiriya" alt="Sigiriya">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Sigiriya</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.7 (245 reviews)</span>
                        </div>
                        <p class="destination-description">Ancient rock fortress with frescoes and mirror wall, offering panoramic views of the surrounding countryside.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 6 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Galle" alt="Galle">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Galle</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.6 (198 reviews)</span>
                        </div>
                        <p class="destination-description">Historic fort with Dutch colonial architecture, museums, and boutique shops.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 7 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Mihintale" alt="Mihintale">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Mihintale</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.4 (142 reviews)</span>
                        </div>
                        <p class="destination-description">Sacred mountain where Buddhism was first introduced to Sri Lanka, featuring ancient stupas and meditation caves.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>
                
                <!-- Destination Card 8 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Rangiri" alt="Rangiri">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Rangiri Dambulla</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.3 (198 reviews)</span>
                        </div>
                        <p class="destination-description">Modern cultural show that brings Sri Lankan history and traditions to life through music, dance, and storytelling.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>

                <!-- Destination Card 9 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://placehold.co/320x200/1f2937/ffffff?text=Sigiriya" alt="Rangiri">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Sigiriya</h3>
                        <span class="destination-category">Relaxation & Leisure</span>
                        <div class="destination-rating">
                            <span class="star">★</span>
                            <span class="rating-value">4.3 (198 reviews)</span>
                        </div>
                        <p class="destination-description">Modern cultural show that brings Sri Lankan history and traditions to life through music, dance, and storytelling.</p>
                        <button class="explore-destination-btn">Explore This Place</button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeDestinationCards();
            initializeFilters();
        });

        // Destination cards interaction
        function initializeDestinationCards() {
            const destinationCards = document.querySelectorAll('.destination-card');
            
            destinationCards.forEach(card => {
                card.addEventListener('click', function() {
                    const placeName = this.querySelector('.destination-title').textContent;
                    console.log(`Destination clicked: ${placeName}`);
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

        // Add event listeners to all explore destination buttons
        document.querySelectorAll('.explore-destination-btn').forEach(button => {
            button.addEventListener('click', function() {
                window.location.href = `<?php echo URL_ROOT; ?>/user/destDetails`
            });
        });


        // Filter functionality
        function initializeFilters() {
            // Apply filters button
            const applyFiltersBtn = document.querySelector('.apply-filters-btn');
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener('click', function() {
                    console.log('Applying filters...');
                    // Here you would implement the actual filtering logic
                    // For now, just show a notification
                    showNotification('Filters applied successfully!', 'success');
                });
            }
            
            // Checkbox functionality
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log(`Checkbox ${this.id} changed to ${this.checked}`);
                });
            });
            
            // Input field functionality
            const filterInputs = document.querySelectorAll('.filter-input');
            filterInputs.forEach(input => {
                input.addEventListener('input', function() {
                    console.log(`Filter input changed: ${this.value}`);
                });
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

