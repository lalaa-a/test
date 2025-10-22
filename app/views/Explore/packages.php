<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Sri Lanka - Tour Packages</title>
    
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
        
        /* Hero Section */
        .hero-section {
            margin-bottom: 40px;
        }
        
        .hero-packages-grid {
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
        
        .package-card {
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
        
        .package-card:focus {
            box-shadow: 0 0 0 3px #006a71, 0 4px 24px 0 rgba(0,0,0,0.13);
        }
        
        .package-card:hover, .package-card:focus-visible {
            transform: scale(1.035);
            filter: brightness(1.08);
            z-index: 3;
        }
        
        .package-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 1;
            transition: filter 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .package-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.48);
            z-index: 2;
            transition: background 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .package-card:hover .package-overlay,
        .package-card:focus-visible .package-overlay {
            background: rgba(0,0,0,0.60);
        }
        
        .package-text {
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
        
        .package-title {
            color: #fff;
            font-family: 'Poppins', 'Inter', Arial, sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin: 0 0 0 0;
            text-shadow: 0 4px 24px rgba(0,0,0,0.45), 0 2px 8px rgba(0,0,0,0.18);
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        
        .package-subtitle {
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
        
        .package-card:hover .package-title,
        .package-card:focus-visible .package-title {
            transform: translateY(-18px);
        }
        
        .package-card:hover .package-subtitle,
        .package-card:focus-visible .package-subtitle {
            opacity: 1;
            transform: translateY(0);
        }
        
        @media (max-width: 900px) {
            .hero-packages-grid { gap: 18px; }
            .package-title { font-size: 1.5rem; }
            .package-subtitle { font-size: 0.98rem; }
        }
        
        @media (max-width: 600px) {
            .hero-packages-grid { grid-template-columns: 1fr; gap: 12px; }
            .package-title { font-size: 1.1rem; }
            .package-subtitle { font-size: 0.85rem; }
            .package-text { padding: 0 0 18px 14px; }
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
        
        /* Package Place Cards - Based on place cards from destinations */
        .category-package-card {
            min-width: 280px;
            max-width: 280px;
            min-height: 380px;
            flex-shrink: 0;
        }

        .category-package-card .package-info {
            padding: 18px;
        }

        .category-package-card .package-name {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .category-package-card .package-type {
            font-size: 14px;
            padding: 4px 10px;
            margin-bottom: 10px;
        }

        .category-package-card .package-price {
            font-size: 16px;
            font-weight: 600;
            color: #006a71;
            margin-bottom: 10px;
        }

        .category-package-card .package-duration {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .category-package-card .package-description {
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 18px;
        }

        .category-package-card .book-package-btn {
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
        
        /* Package Grid Section */
        .packages-section {
            margin-bottom: 80px;
        }
        
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
            width: 100%;
            margin: 0 auto 24px auto;
            padding: 20px 0;
        }
        
        @media (max-width: 900px) {
            .packages-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
                gap: 18px;
            }
        }
        
        @media (max-width: 600px) {
            .packages-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        
        .package-detail-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            min-height: 420px;
            box-shadow: 0 4px 24px 0 rgba(0,0,0,0.13);
            cursor: pointer;
            outline: none;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1), filter 0.3s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
        }
        
        .package-detail-card:focus {
            box-shadow: 0 0 0 3px #006a71, 0 4px 24px 0 rgba(0,0,0,0.13);
        }
        
        .package-detail-card:hover, .package-detail-card:focus-visible {
            transform: scale(1.035);
            filter: brightness(1.08);
            z-index: 3;
        }
        
        .package-image {
            width: 100%;
            height: 180px;
            overflow: hidden;
        }
        
        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .package-detail-card:hover .package-image img {
            transform: scale(1.05);
        }
        
        .package-info {
            padding: 20px;
            background: white;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .package-name {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .package-type {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 12px;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 9999px;
            background-color: #f3f4f6;
        }
        
        .package-price {
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #006a71;
            margin-bottom: 8px;
        }
        
        .package-duration {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .package-highlights {
            margin-bottom: 16px;
        }
        
        .package-highlights h4 {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .highlights-list {
            list-style: none;
            padding: 0;
        }
        
        .highlights-list li {
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
            position: relative;
            padding-left: 16px;
        }
        
        .highlights-list li:before {
            content: "•";
            color: #006a71;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .package-description {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.45;
            margin-bottom: 20px;
            flex: 1;
        }
        
        .book-package-btn {
            width: 100%;
            background: #006a71;
            border: none;
            border-radius: 6px;
            padding: 14px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .book-package-btn:hover {
            background: #005a61;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Badge for popular packages */
        .popular-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f59e0b;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            z-index: 4;
        }
        
        /* Special styles for featured packages */
        .featured-package {
            border: 2px solid #006a71;
        }
        
        .featured-package .package-info {
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);
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
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-packages-grid">
                <article class="package-card" tabindex="0" aria-label="Cultural Heritage Tour, 7 days exploring ancient sites">
                    <div class="package-bg" style="background-image:url('https://placehold.co/600x400/2d3748/ffffff?text=Cultural+Heritage');"></div>
                    <div class="package-overlay"></div>
                    <div class="package-text">
                        <h2 class="package-title">Cultural Heritage</h2>
                        <p class="package-subtitle">7 days • Ancient sites & temples</p>
                    </div>
                </article>
                <article class="package-card" tabindex="0" aria-label="Adventure & Wildlife, 10 days safari and hiking">
                    <div class="package-bg" style="background-image:url('https://placehold.co/600x400/2d3748/ffffff?text=Adventure+Safari');"></div>
                    <div class="package-overlay"></div>
                    <div class="package-text">
                        <h2 class="package-title">Adventure & Wildlife</h2>
                        <p class="package-subtitle">10 days • Safari & hiking</p>
                    </div>
                </article>
                <article class="package-card" tabindex="0" aria-label="Beach & Relaxation, 5 days coastal paradise">
                    <div class="package-bg" style="background-image:url('https://placehold.co/600x400/2d3748/ffffff?text=Beach+Paradise');"></div>
                    <div class="package-overlay"></div>
                    <div class="package-text">
                        <h2 class="package-title">Beach & Relaxation</h2>
                        <p class="package-subtitle">5 days • Coastal paradise</p>
                    </div>
                </article>
                <article class="package-card" tabindex="0" aria-label="Hill Country Escape, 6 days tea plantations and mountains">
                    <div class="package-bg" style="background-image:url('https://placehold.co/600x400/2d3748/ffffff?text=Hill+Country');"></div>
                    <div class="package-overlay"></div>
                    <div class="package-text">
                        <h2 class="package-title">Hill Country</h2>
                        <p class="package-subtitle">6 days • Tea plantations & peaks</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Popular Packages -->
        <section class="categories-section">
            <h2 class="section-title">Popular Packages</h2>
            <div class="category-container">
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Golden+Triangle" alt="Golden Triangle Tour">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Golden Triangle Tour</h3>
                        <span class="package-type">Cultural Heritage</span>
                        <div class="package-price">$899</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            8 Days / 7 Nights
                        </div>
                        <p class="package-description">Explore Anuradhapura, Sigiriya, and Kandy in this comprehensive cultural journey through Sri Lanka's ancient kingdoms.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Wildlife+Safari" alt="Wildlife Safari">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Wildlife Safari Adventure</h3>
                        <span class="package-type">Adventure & Wildlife</span>
                        <div class="package-price">$1,299</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            12 Days / 11 Nights
                        </div>
                        <p class="package-description">Experience the best of Sri Lankan wildlife with visits to Yala, Udawalawe, and Minneriya National Parks.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Beach+Bliss" alt="Beach Paradise">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Beach Paradise Escape</h3>
                        <span class="package-type">Beach & Relaxation</span>
                        <div class="package-price">$699</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            6 Days / 5 Nights
                        </div>
                        <p class="package-description">Relax on pristine beaches in Bentota, Mirissa, and Unawatuna with luxury accommodations and water activities.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Hill+Country" alt="Hill Country">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Hill Country Explorer</h3>
                        <span class="package-type">Nature & Adventure</span>
                        <div class="package-price">$799</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            7 Days / 6 Nights
                        </div>
                        <p class="package-description">Journey through Nuwara Eliya, Ella, and Haputale with scenic train rides and tea plantation visits.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Colombo+City" alt="City Experience">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Colombo City Experience</h3>
                        <span class="package-type">Urban & Culture</span>
                        <div class="package-price">$399</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            3 Days / 2 Nights
                        </div>
                        <p class="package-description">Discover the vibrant capital with city tours, shopping, dining, and cultural attractions.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
                <div class="package-detail-card category-package-card">
                    <div class="package-image">
                        <img src="https://placehold.co/280x150/1f2937/ffffff?text=Grand+Tour" alt="Grand Tour">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Grand Sri Lanka Tour</h3>
                        <span class="package-type">Complete Experience</span>
                        <div class="package-price">$1,899</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            15 Days / 14 Nights
                        </div>
                        <p class="package-description">The ultimate Sri Lankan experience covering culture, wildlife, beaches, and hill country in one comprehensive tour.</p>
                        <button class="book-package-btn">Book Now</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cultural Heritage Packages -->
        <section class="packages-section">
            <h2 class="section-title">Cultural Heritage Packages</h2>
            <div class="packages-grid">
                <div class="package-detail-card featured-package">
                    <div class="popular-badge">Most Popular</div>
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Ancient+Kingdoms" alt="Ancient Kingdoms Tour">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Ancient Kingdoms Discovery</h3>
                        <span class="package-type">Cultural Heritage</span>
                        <div class="package-price">$1,199</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            10 Days / 9 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Anuradhapura Ancient City</li>
                                <li>Sigiriya Rock Fortress</li>
                                <li>Polonnaruwa Ruins</li>
                                <li>Temple of Tooth Relic</li>
                                <li>Dambulla Cave Temple</li>
                            </ul>
                        </div>
                        <p class="package-description">Journey through 2,500 years of history visiting Sri Lanka's most significant archaeological and cultural sites with expert guides.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Buddhist+Pilgrimage" alt="Buddhist Pilgrimage">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Buddhist Pilgrimage Tour</h3>
                        <span class="package-type">Cultural Heritage</span>
                        <div class="package-price">$899</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            8 Days / 7 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Adam's Peak (Sri Pada)</li>
                                <li>Temple of Tooth Relic</li>
                                <li>Kelaniya Raja Maha Vihara</li>
                                <li>Mihintale Sacred Site</li>
                                <li>Meditation Sessions</li>
                            </ul>
                        </div>
                        <p class="package-description">A spiritual journey visiting the most sacred Buddhist sites in Sri Lanka, perfect for meditation and cultural immersion.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Colonial+Heritage" alt="Colonial Heritage">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Colonial Heritage Trail</h3>
                        <span class="package-type">Cultural Heritage</span>
                        <div class="package-price">$749</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            6 Days / 5 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Galle Dutch Fort</li>
                                <li>Colombo Colonial Architecture</li>
                                <li>Tea Plantation Estates</li>
                                <li>British Hill Stations</li>
                                <li>Railway Heritage</li>
                            </ul>
                        </div>
                        <p class="package-description">Explore Sri Lanka's colonial past through Dutch forts, British architecture, and plantation heritage sites.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                
        <!--!   <button class="see-more-btn"> 
                See More Cultural Packages
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </button>
                        -->
        </section>

        <!-- Adventure & Wildlife Packages -->
        <section class="packages-section">
            <h2 class="section-title">Adventure & Wildlife Packages</h2>
            <div class="packages-grid">
                <div class="package-detail-card featured-package">
                    <div class="popular-badge">Best Value</div>
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Safari+Adventure" alt="Ultimate Safari">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Ultimate Safari Adventure</h3>
                        <span class="package-type">Adventure & Wildlife</span>
                        <div class="package-price">$1,499</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            12 Days / 11 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Yala National Park Safari</li>
                                <li>Udawalawe Elephant Park</li>
                                <li>Minneriya Gathering</li>
                                <li>Leopard Spotting</li>
                                <li>Bird Watching Tours</li>
                            </ul>
                        </div>
                        <p class="package-description">The ultimate wildlife experience covering all major national parks with expert naturalist guides and luxury safari accommodations.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Whale+Watching" alt="Marine Adventure">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Marine Adventure Tour</h3>
                        <span class="package-type">Adventure & Wildlife</span>
                        <div class="package-price">$999</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            8 Days / 7 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Blue Whale Watching</li>
                                <li>Dolphin Encounters</li>
                                <li>Snorkeling & Diving</li>
                                <li>Turtle Watching</li>
                                <li>Boat Expeditions</li>
                            </ul>
                        </div>
                        <p class="package-description">Explore Sri Lanka's incredible marine life with whale watching, dolphin encounters, and underwater adventures.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Hiking+Adventure" alt="Hiking Adventure">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Mountain Hiking Adventure</h3>
                        <span class="package-type">Adventure & Wildlife</span>
                        <div class="package-price">$849</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            7 Days / 6 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Adam's Peak Sunrise Trek</li>
                                <li>Ella Rock Hiking</li>
                                <li>Horton Plains World's End</li>
                                <li>Knuckles Range Trails</li>
                                <li>Waterfall Expeditions</li>
                            </ul>
                        </div>
                        <p class="package-description">Challenge yourself with Sri Lanka's best hiking trails, from sacred peaks to scenic mountain ranges.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                

        <!--!    <button class="see-more-btn">
                See More Adventure Packages
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </button> 
            -->

        </section>

        <!-- Beach & Relaxation Packages -->
        <section class="packages-section">
            <h2 class="section-title">Beach & Relaxation Packages</h2>
            <div class="packages-grid">
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Luxury+Beach" alt="Luxury Beach Resort">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Luxury Beach Resort Experience</h3>
                        <span class="package-type">Beach & Relaxation</span>
                        <div class="package-price">$1,299</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            7 Days / 6 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>5-Star Beach Resort</li>
                                <li>Private Beach Access</li>
                                <li>Spa & Wellness Center</li>
                                <li>Water Sports Activities</li>
                                <li>Sunset Cruise</li>
                            </ul>
                        </div>
                        <p class="package-description">Indulge in luxury at pristine beaches with world-class resorts, spa treatments, and exclusive beach experiences.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Beach+Hopping" alt="Beach Hopping">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Southern Coast Beach Hopping</h3>
                        <span class="package-type">Beach & Relaxation</span>
                        <div class="package-price">$799</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            6 Days / 5 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Mirissa Beach</li>
                                <li>Unawatuna Bay</li>
                                <li>Tangalle Golden Sands</li>
                                <li>Hikkaduwa Coral Reefs</li>
                                <li>Beach Activities & Sports</li>
                            </ul>
                        </div>
                        <p class="package-description">Explore the best beaches along Sri Lanka's southern coast with crystal clear waters and vibrant coral reefs.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>
                <div class="package-detail-card">
                    <div class="package-image">
                        <img src="https://placehold.co/320x180/1f2937/ffffff?text=Surfing+Package" alt="Surfing Adventure">
                    </div>
                    <div class="package-info">
                        <h3 class="package-name">Surfing Paradise Package</h3>
                        <span class="package-type">Beach & Relaxation</span>
                        <div class="package-price">$649</div>
                        <div class="package-duration">
                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                            5 Days / 4 Nights
                        </div>
                        <div class="package-highlights">
                            <h4>Package Highlights:</h4>
                            <ul class="highlights-list">
                                <li>Surfing Lessons</li>
                                <li>Professional Instructor</li>
                                <li>Equipment Included</li>
                                <li>Arugam Bay & Mirissa</li>
                                <li>Beach Accommodation</li>
                            </ul>
                        </div>
                        <p class="package-description">Learn to surf or improve your skills at Sri Lanka's best surf spots with professional instruction and equipment.</p>
                        <button class="book-package-btn">Book This Package</button>
                    </div>
                </div>

        <!--!    <button class="see-more-btn">
                See More Beach Packages
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </button>
        </section> -->

    </main>

    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializePackageCards();
            initializeCategoryPackages();
            initializeBookingButtons();
            initializeScrollBehavior();
        });

        // Package cards interaction
        function initializePackageCards() {
            const packageCards = document.querySelectorAll('.package-card');
            
            packageCards.forEach(card => {
                card.addEventListener('click', function() {
                    const title = this.querySelector('.package-title').textContent;
                    console.log(`Package clicked: ${title}`);
                    navigateToPackageDetails(title);
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

        // Category package cards interaction
        function initializeCategoryPackages() {
            const categoryCards = document.querySelectorAll('.category-package-card');
            
            categoryCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on book button
                    if (!e.target.classList.contains('book-package-btn')) {
                        const packageName = this.querySelector('.package-name').textContent;
                        console.log(`Category package clicked: ${packageName}`);
                        navigateToPackageDetails(packageName);
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
        }

        // Detail package cards interaction
        function initializeDetailPackages() {
            const detailCards = document.querySelectorAll('.package-detail-card');
            
            detailCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on book button
                    if (!e.target.classList.contains('book-package-btn')) {
                        const packageName = this.querySelector('.package-name').textContent;
                        console.log(`Detail package clicked: ${packageName}`);
                        navigateToPackageDetails(packageName);
                    }
                });
            });
        }

        // Booking buttons interaction
        function initializeBookingButtons() {
            const bookingButtons = document.querySelectorAll('.book-package-btn');
            
            bookingButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent card click event
                    
                    const packageCard = this.closest('.package-detail-card') || this.closest('.category-package-card');
                    const packageName = packageCard.querySelector('.package-name').textContent;
                    const packagePrice = packageCard.querySelector('.package-price').textContent;
                    
                    console.log(`Book button clicked for: ${packageName} - ${packagePrice}`);
                    handlePackageBooking(packageName, packagePrice);
                });
            });
        }

        // Scroll behavior for category containers
        function initializeScrollBehavior() {
            const categoryContainers = document.querySelectorAll('.category-container:not(.no-scroll)');
            
            categoryContainers.forEach(container => {
                // Add smooth scrolling
                container.style.scrollBehavior = 'smooth';
                
                // Optional: Add scroll indicators if container overflows
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

        // Navigation function for package details
        function navigateToPackageDetails(packageName) {
            // Create URL-friendly slug
            const urlSlug = packageName.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single
                .trim('-'); // Remove leading/trailing hyphens

            // Navigate to package details page
            const packageUrl = `<?php echo URL_ROOT; ?>/Home/packageDetails/${urlSlug}`;
            
            console.log(`Navigating to package: ${packageName}`);
            console.log(`URL: ${packageUrl}`);
            
            window.location.href = packageUrl;
        }

        // Handle package booking
        function handlePackageBooking(packageName, packagePrice) {
            // Show booking confirmation
            showNotification(`Booking initiated for ${packageName} - ${packagePrice}`, 'success');
            
            // Here you can add more sophisticated booking logic:
            // - Open booking modal
            // - Navigate to booking form
            // - Add to cart
            // - Etc.
            
            // For now, let's simulate navigation to booking page
            setTimeout(() => {
                const bookingUrl = `<?php echo URL_ROOT; ?>/Home/bookPackage?package=${encodeURIComponent(packageName)}`;
                console.log(`Booking URL: ${bookingUrl}`);
                // window.location.href = bookingUrl;
            }, 1500);
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
        window.PackagesPage = {
            showNotification,
            bookPackage: function(packageName, price) {
                console.log(`Programmatically booking package: ${packageName} for ${price}`);
                handlePackageBooking(packageName, price);
            },
            navigateToPackage: function(packageName) {
                console.log(`Programmatically navigating to package: ${packageName}`);
                navigateToPackageDetails(packageName);
            }
        };
    </script>
</body>
</html>