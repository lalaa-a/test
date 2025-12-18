<style>
    /* ============================== */
    /*         CSS VARIABLES         */
    /* ============================== */
    :root {
        /* Card Sizing - Easy to adjust */
        --card-width: 270px;
        --card-min-height: 300px;
        --card-image-height: 150px;
        --card-padding: 18px;
        --card-gap: 20px;
        --card-border-radius: 12px;
        
        /* Colors */
        --primary-color: #006a71;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-light: #4b5563;
        --background-gray: #f9fafb;
        --card-background: white;
        --border-color: #e5e7eb;
        --shadow-color: rgba(0, 0, 0, 0.13);
        
        /* Typography */
        --font-primary: 'Geologica', sans-serif;
        --font-secondary: 'Roboto', sans-serif;
        
        /* Spacing */
        --section-spacing: 40px;
        --title-spacing: 30px;
    }

    /* ============================== */
    /*           CSS RESET           */
    /* ============================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        font-family: var(--font-primary);
        background-color: var(--background-gray);
        max-width: 100vw;
        box-sizing: border-box;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    /* ============================== */
    /*        LAYOUT STRUCTURE       */
    /* ============================== */
    .content-wrapper {
        width: 100%;
        padding: 20px;
        position: relative;
        overflow: hidden;
        max-width: 100vw;
        box-sizing: border-box;
        margin: 0 auto;
    }

    /* ============================== */
    /*       SEARCH SECTION          */
    /* ============================== */
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
        background: #ffffff;
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
        position: relative;
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
        display: none;
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

    .highlight {
        background: rgba(255, 235, 59, 0.4);
        padding: 2px 4px;
        border-radius: 3px;
    }


    /* ============================== */
    /*         SECTION LAYOUT        */
    /* ============================== */
    .categories-section,
    .trending-section {
        margin-bottom: 40px;
        padding: 0 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .section-title {
        font-family: 'Geologica', sans-serif;
        font-weight: 700;
        font-size: 24px;
        color: #374151;
        margin-bottom: 30px;
        margin-left: 10px;
        text-align: left;
    }

    /* ============================== */
    /*       CONTAINER LAYOUTS       */
    /* ============================== */
    
    /* Horizontal Scrolling Container */
    .category-container {
        display: flex;
        gap: var(--card-gap);
        overflow-x: auto;
        padding: 10px 0;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) #f1f1f1;
        max-width: 100%;
        box-sizing: border-box;
    }

    .category-container::-webkit-scrollbar {
        height: 6px;
    }

    .category-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .category-container::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 3px;
    }

    .category-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-hover);
    }

    .category-container.no-scroll {
        overflow-x: visible;
        flex-wrap: wrap;
    }

    /* Grid Container */
    .trending-places-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, var(--card-width));
        gap: var(--card-gap);
        width: 100%;
        padding: 5px 0;
        max-width: 100%;
        box-sizing: border-box;
        position: relative;
    }

    /* See More Arrow Button */
    .see-more-arrow {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: var(--primary-color);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 106, 113, 0.3);
        z-index: 10;
        font-size: 16px;
    }

    .see-more-arrow:hover {
        background: var(--primary-hover);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 106, 113, 0.4);
    }

    .see-more-arrow i {
        font-size: 16px;
    }

    /* ============================== */
    /*         PLACE CARDS           */
    /* ============================== */
    
    /* Base Card Styles */
    .place-card {
        position: relative;
        border-radius: var(--card-border-radius);
        overflow: hidden;
        width: var(--card-width);
        min-height: var(--card-min-height);
        box-shadow: 0 4px 24px 0 var(--shadow-color);
        cursor: pointer;
        outline: none;
        transition: transform 0.3s cubic-bezier(.4,0,.2,1), 
                   box-shadow 0.3s cubic-bezier(.4,0,.2,1), 
                   filter 0.3s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: var(--card-background);
    }

    /* Cards in horizontal container need flex-shrink */
    .category-container .place-card {
        flex-shrink: 0;
    }

    /* Card Interactions */
    .place-card:focus {
        box-shadow: 0 0 0 3px var(--primary-color), 0 4px 24px 0 var(--shadow-color);
    }

    .place-card:hover, 
    .place-card:focus-visible {
        transform: scale(1.035);
        filter: brightness(1.08);
        z-index: 3;
    }

    /* Card Image */
    .place-image {
        width: 100%;
        height: var(--card-image-height);
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

    /* Card Content */
    .place-info {
        padding: var(--card-padding);
        background: var(--card-background);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .place-title {
        font-family: var(--font-secondary);
        font-weight: 600;
        font-size: 18px;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .place-category {
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-secondary);
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
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-secondary);
    }

    .place-description {
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-light);
        line-height: 1.45;
        margin-bottom: var(--card-padding);
        flex: 1;
    }

    .explore-place-btn {
        width: 100%;
        background: var(--primary-color);
        border: none;
        border-radius: 6px;
        padding: 12px;
        font-family: var(--font-secondary);
        font-size: 16px;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .explore-place-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* ============================== */
    /*       SCROLL CONTROLS        */
    /* ============================== */
    .scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10;
        display: none;
        margin: 0 5px;
    }

    .scroll-btn-left {
        left: 5px;
    }

    .scroll-btn-right {
        right: 5px;
    }

    /* ============================== */
    /*       filter icon        */
    /* ============================== */

    .filter-icon{
        margin-right: 30px;
        border: none;
        display: flex;
        position: relative;
        color: var(--primary-color);
        background-color: #ffffff;
        font-size: 17px;
        padding-top: 6px;
    }

    .filter-icon:hover{
        cursor: pointer;
        border-bottom: 3px solid var(--primary-color);  
        border-radius: 2px;
    }

    /* ============================== */
    /*       RESPONSIVE DESIGN       */
    /* ============================== */
    @media (max-width: 900px) {
        :root {
            --card-width: 220px;
            --card-gap: 18px;
        }

        .trending-places-grid {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .search-title {
            font-size: 24px;
        }

        .search-container {
            max-width: 90%;
        }

        .search-input {
            padding: 14px 45px 14px 18px;
            font-size: 14px;
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
            font-size: 12px;
        }
    }

    @media (max-width: 500px) {
        :root {
            --card-gap: 12px;
        }

        .content-wrapper {
            padding: 0;
        }

        .search-title {
            font-size: 20px;
        }

        .search-subtitle {
            font-size: 13px;
        }

        .trending-places-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
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
                <div class="search-icon" id="searchButton">🔍</div>
            </div>
        </div>


        <div class="search-filters">
            <button class="filter-icon" id="filterToggle">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            <div class="filter-chip active" data-category="all">All Places</div>
            <div class="filter-chip" data-category="culture">Culture & Heritage</div>
            <div class="filter-chip" data-category="nature">Nature & Adventure</div>
            <div class="filter-chip" data-category="beach">Beach & Relaxation</div>
            <div class="filter-chip" data-category="entertainment">Entertainment & Activities</div>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <!-- Trending Places -->
    <section class="categories-section">
        <h2 class="section-title">Trending Places</h2>
        <div class="category-container">
            <div class="place-card">
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
            <div class="place-card">
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
            <div class="place-card">
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
            <div class="place-card">
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
            <div class="place-card">
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
            <div class="place-card">
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
            <button class="see-more-arrow" data-category="culture" title="See More Culture & Heritage">
               <i class="fas fa-arrow-right"></i>
            </button>
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
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/sigiriya.png" alt="Sigiriya">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Sigiriya</h3>
                    <span class="place-category">Culture & Heritage</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.9 (245 reviews)</span>
                    </div>
                    <p class="place-description">UNESCO World Heritage Site featuring the ancient rock fortress with frescoes, gardens, and panoramic views.</p>
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
                        <span class="rating-value">4.7 (198 reviews)</span>
                    </div>
                    <p class="place-description">Medieval capital with well-preserved ruins, temples, and statues showcasing Sri Lanka's architectural brilliance.</p>
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
        </div>

        
    </section>

    <!-- Nature & Adventure -->
    <section class="trending-section">
        <h2 class="section-title">Nature & Adventure</h2>
        <div class="trending-places-grid">
            <button class="see-more-arrow" data-category="nature" title="See More Nature & Adventure">
                <i class="fas fa-arrow-right"></i>
            </button>
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
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/adams.png" alt="Adam's Peak">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Adam's Peak</h3>
                    <span class="place-category">Nature & Adventure</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.6 (187 reviews)</span>
                    </div>
                    <p class="place-description">Sacred mountain with a pilgrimage trail leading to a summit featuring a mysterious footprint and sunrise views.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
            <div class="place-card">
                <div class="place-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/knuckles.png" alt="Knuckles Range">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Knuckles Range</h3>
                    <span class="place-category">Nature & Adventure</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.5 (156 reviews)</span>
                    </div>
                    <p class="place-description">UNESCO biosphere reserve with rugged mountains, waterfalls, and diverse wildlife for hiking enthusiasts.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Relaxation & Leisure -->
    <section class="trending-section">
        <h2 class="section-title">Relaxation & Leisure</h2>
        <div class="trending-places-grid">
            <button class="see-more-arrow" data-category="beach" title="See More Relaxation & Leisure">
                <i class="fas fa-arrow-right"></i>
            </button>
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
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/unawatuna.png" alt="Unawatuna Beach">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Unawatuna Beach</h3>
                    <span class="place-category">Relaxation & Leisure</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.7 (234 reviews)</span>
                    </div>
                    <p class="place-description">Tranquil bay with calm waters, ideal for snorkeling, swimming, and relaxing in a peaceful environment.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
            <div class="place-card">
                <div class="place-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/benthota.png" alt="Bentota Beach">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Bentota Beach</h3>
                    <span class="place-category">Relaxation & Leisure</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.6 (198 reviews)</span>
                    </div>
                    <p class="place-description">Popular beach resort with luxury accommodations, water sports, and beautiful sunset views.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Entertainment & Activities -->
    <section class="trending-section">
        <h2 class="section-title">Entertainment & Activities</h2>
        <div class="trending-places-grid">
            <button class="see-more-arrow" data-category="entertainment" title="See More Entertainment & Activities">
                <i class="fas fa-arrow-right"></i>
            </button>
            <div class="place-card">
                <div class="place-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/colombo.png" alt="Colombo">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Colombo</h3>
                    <span class="place-category">Entertainment & Activities</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.6 (312 reviews)</span>
                    </div>
                    <p class="place-description">Vibrant capital city with bustling markets, modern shopping malls, cultural attractions, and lively nightlife.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
            <div class="place-card">
                <div class="place-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/hikkaduwa.png" alt="Hikkaduwa">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Hikkaduwa</h3>
                    <span class="place-category">Entertainment & Activities</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.4 (187 reviews)</span>
                    </div>
                    <p class="place-description">Vibrant beach town with surfing, diving, and nightlife activities. Perfect for adventure seekers.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
            <div class="place-card">
                <div class="place-image">
                    <img src="<?php echo IMG_ROOT; ?>/explore/destinations/arugam.png" alt="Arugam Bay">
                </div>
                <div class="place-info">
                    <h3 class="place-title">Arugam Bay</h3>
                    <span class="place-category">Entertainment & Activities</span>
                    <div class="place-rating">
                        <span class="star">★</span>
                        <span class="rating-value">4.5 (156 reviews)</span>
                    </div>
                    <p class="place-description">World-famous surfing destination with beautiful beaches, laid-back atmosphere, and water sports.</p>
                    <button class="explore-place-btn">Explore This Place</button>
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    // Initialize all functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeDestinationCards();
        initializeCategoryCards();
        initializePlaceCards();
        initializeExploreButtons();
        initializeScrollBehavior();
        initializeSearchFunctionality();
        initializeSeeMoreArrows();
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
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 10;
            display: none;
        `;

        const rightBtn = document.createElement('button');
        rightBtn.innerHTML = '›';
        rightBtn.className = 'scroll-btn scroll-btn-right';
        rightBtn.style.cssText = leftBtn.style.cssText.replace('left: 5px', 'right: 5px');

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

    // See More arrows functionality
    function initializeSeeMoreArrows() {
        const seeMoreArrows = document.querySelectorAll('.see-more-arrow');

        seeMoreArrows.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                const categoryName = getCategoryDisplayName(category);
                console.log(`See More arrow clicked for: ${categoryName}`);
                showNotification(`Viewing all ${categoryName} destinations`, 'info');
                // Here you could implement navigation to a dedicated category page
                // or expand the current section to show more items
            });
        });
    }

    // Helper function to get display name for category
    function getCategoryDisplayName(category) {
        const categoryNames = {
            'culture': 'Culture & Heritage',
            'nature': 'Nature & Adventure',
            'beach': 'Relaxation & Leisure',
            'entertainment': 'Entertainment & Activities'
        };
        return categoryNames[category] || category;
    }

    // Navigation function for destinations
    function navigateToDestination(destinationName) {
        // For admin dashboard context, show a notification instead of navigating
        showNotification(`Selected destination: ${destinationName}`, 'info');
    }

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
                    document.querySelector('.content-wrapper').appendChild(noResultsElement);
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
        // Create notification element
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
            max-width: 250px;
            word-wrap: break-word;
            font-size: 14px;
        `;

        // Try to find the admin dashboard container first, then fallback to content wrapper
        const adminDashboard = document.getElementById('dashboard');
        const contentWrapper = document.querySelector('.content-wrapper');

        if (adminDashboard) {
            adminDashboard.appendChild(notification);
        } else if (contentWrapper) {
            contentWrapper.appendChild(notification);
        } else {
            document.body.appendChild(notification);
        }

        // Fade in
        setTimeout(() => notification.style.opacity = '1', 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
</script>
