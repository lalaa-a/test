<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Culture & Heritage Destinations</title>
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

            <!-- Search Section -->
        <section class="search-section">
            <h1 class="search-title">Discover Sri Lanka's Wonders</h1>
            <p class="search-subtitle">Find your Culture & Heritage destinations</p>
            
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


        <!-- Page Title -->
        <h1 class="page-title">Culture & Heritage Destinations</h1>
        
        <!-- Content Container with Sidebar -->
        <div class="content-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h2 class="sidebar-title">Filter By</h2>
                
                <!-- Historical Period Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Historical Period</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="ancient">
                            <label for="ancient">Ancient (Before 1000 AD)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="medieval">
                            <label for="medieval">Medieval (1000-1500 AD)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="colonial">
                            <label for="colonial">Colonial (1500-1900 AD)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="modern">
                            <label for="modern">Modern (1900-Present)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Type of Heritage Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Type of Heritage</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="architectural">
                            <label for="architectural">Architectural</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="religious">
                            <label for="religious">Religious</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="cultural">
                            <label for="cultural">Cultural</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="historical">
                            <label for="historical">Historical Sites</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="museums">
                            <label for="museums">Museums</label>
                        </div>
                    </div>
                </div>
                
                <!-- Accessibility Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Accessibility</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="wheelchair">
                            <label for="wheelchair">Wheelchair Accessible</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="easy-access">
                            <label for="easy-access">Easy Access</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="moderate-hike">
                            <label for="moderate-hike">Moderate Hike Required</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="challenging">
                            <label for="challenging">Challenging Terrain</label>
                        </div>
                    </div>
                </div>
                
                <!-- Duration Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Recommended Duration</h3>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="half-day">
                            <label for="half-day">Half Day (2-4 hours)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="full-day">
                            <label for="full-day">Full Day (4-8 hours)</label>
                        </div>
                    </div>
                    <div class="filter-option">
                        <div class="checkbox-container">
                            <input type="checkbox" id="multi-day">
                            <label for="multi-day">Multi-Day (1+ days)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Language Spoken Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Language Spoken</h3>
                    <input type="text" class="filter-input" placeholder="English">
                </div>
                
                <!-- Region Coverage Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Region</h3>
                    <input type="text" class="filter-input" placeholder="Colombo">
                </div>
                
                <!-- Rating Filter -->
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
                
                <!-- Price Range Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">Price Range</h3>
                    <div class="price-range">
                        <span>$10</span>
                        <div class="price-slider"></div>
                        <span>$500</span>
                    </div>
                </div>
                
                <!-- Apply Filters Button -->
                <button class="apply-filters-btn">Apply Filters</button>
            </aside>
            
            <!-- Main Content Area -->
            <div class="destinations-grid">
                <!-- Destination Card 1 -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/anuradhapura.png" alt="Anuradhapura">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Anuradhapura</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/polonnaruwa.png" alt="Polonnaruwa">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Polonnaruwa</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/dambulla.png" alt="Dambulla">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Dambulla</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/kandy.png" alt="Kandy">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Kandy</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Sigiriya">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Sigiriya</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/galle.jpg" alt="Galle">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Galle</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/mihinthale.png" alt="Mihintale">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Mihintale</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/rangiri.png" alt="Rangiri">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Rangiri Dambulla</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Sigiriya">
                    </div>
                    <div class="destination-info">
                        <h3 class="destination-title">Sigiriya</h3>
                        <span class="destination-category">Culture & Heritage</span>
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
            console.log('CultureHeritage page loaded - initializing navigation...');
            initializeDestinationCards();
            initializeFilters();
            console.log('Navigation initialization complete');
        });


        // Destination cards interaction
        function initializeDestinationCards() {
            const destinationCards = document.querySelectorAll('.destination-card');
            
            destinationCards.forEach(card => {
                // Make cards focusable for accessibility
                card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');
                card.setAttribute('aria-label', 'Navigate to destination details');
                
                card.addEventListener('click', function() {
                    const placeName = this.querySelector('.destination-title').textContent;
                    console.log(`Destination clicked: ${placeName}`);
                    navigateToDestination(placeName);
                });
                
                // Add keyboard navigation
                card.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
                
                // Add hover effects
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Initialize explore buttons
            initializeExploreButtons();
        }

        // Initialize explore buttons
        function initializeExploreButtons() {
            const exploreButtons = document.querySelectorAll('.explore-destination-btn');
            
            exploreButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent card click event
                    
                    const placeName = this.closest('.destination-card').querySelector('.destination-title').textContent;
                    console.log(`Explore button clicked for: ${placeName}`);
                    navigateToDestDetails();
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

