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
    .drivers-section,
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

    .subsection-title {
        font-family: 'Geologica', sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #374151;
        margin-bottom: 20px;
        margin-left: 10px;
        text-align: left;
    }

    /* ============================== */
    /*       CONTAINER LAYOUTS       */
    /* ============================== */
    
    /* Horizontal Scrolling Container */
    .drivers-container {
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

    .drivers-container::-webkit-scrollbar {
        height: 6px;
    }

    .drivers-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .drivers-container::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 3px;
    }

    .drivers-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-hover);
    }

    /* Grid Container */
    .drivers-container-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, var(--card-width));
        gap: var(--card-gap);
        width: 100%;
        padding: 5px 0;
        max-width: 100%;
        box-sizing: border-box;
        position: relative;
    }

    /* See More Button */
    .see-more-btn {
        display: block;
        margin: 20px auto;
        padding: 12px 24px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-family: 'Roboto', sans-serif;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }

    .see-more-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .arrow-icon {
        width: 16px;
        height: 16px;
        margin-left: 8px;
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
    /*         DRIVER CARDS          */
    /* ============================== */
    
    /* Base Card Styles */
    .driver-card {
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
        padding: var(--card-padding);
    }

    /* Cards in horizontal container need flex-shrink */
    .drivers-container .driver-card {
        flex-shrink: 0;
    }

    /* Card Interactions */
    .driver-card:focus {
        box-shadow: 0 0 0 3px var(--primary-color), 0 4px 24px 0 var(--shadow-color);
    }

    .driver-card:hover, 
    .driver-card:focus-visible {
        transform: scale(1.035);
        filter: brightness(1.08);
        z-index: 3;
    }

    /* Driver Avatar */
    .driver-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 16px;
        border: 3px solid var(--primary-color);
    }

    .driver-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Driver Badge */
    .driver-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 10;
    }

    .driver-badge.top-rated {
        background: #fbbf24;
        color: white;
    }

    .driver-badge.most-booked {
        background: #10b981;
        color: white;
    }

    /* Driver Info */
    .driver-info {
        text-align: center;
        flex-grow: 1;
    }

    .driver-name {
        font-family: var(--font-secondary);
        font-weight: 600;
        font-size: 18px;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .driver-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .star {
        color: #fbbf24;
        font-size: 16px;
    }

    .rating {
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-secondary);
    }

    .reviews {
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-secondary);
    }

    .driver-description {
        font-family: var(--font-secondary);
        font-size: 14px;
        color: var(--text-light);
        line-height: 1.45;
        margin-bottom: var(--card-padding);
        flex: 1;
    }

    .select-driver-btn {
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

    .select-driver-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
    /*       filter popup        */
    /* ============================== */

    .filter-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }

    .filter-popup.show {
        display: flex;
    }

    .filter-popup-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .filter-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .filter-popup-header h3 {
        margin: 0;
        color: var(--primary);
        font-size: 1.25rem;
        font-weight: 600;
    }

    .filter-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .filter-close-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .filter-popup-body {
        padding: 20px;
    }

    .filter-section {
        margin-bottom: 24px;
    }

    .filter-section h4 {
        margin: 0 0 12px 0;
        color: var(--primary);
        font-size: 1rem;
        font-weight: 600;
    }

    .filter-checkbox {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 14px;
        user-select: none;
        color: #374151;
    }

    .filter-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #f3f4f6;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .filter-checkbox:hover input ~ .checkmark {
        background-color: #e5e7eb;
        border-color: var(--primary);
    }

    .filter-checkbox input:checked ~ .checkmark {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .filter-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    .filter-checkbox .checkmark:after {
        left: 6px;
        top: 2px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .filter-popup-footer {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .filter-popup-footer .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-popup-footer .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
    }

    .filter-popup-footer .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .filter-popup-footer .btn {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .filter-popup-footer .btn:hover {
        background: #e5e7eb;
    }
    @media (max-width: 900px) {
        :root {
            --card-width: 220px;
            --card-gap: 18px;
        }

        .drivers-container-grid {
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

        .drivers-container-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Driver</h1>
        <p class="search-subtitle">Discover trusted drivers for your Sri Lankan adventure</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="driverSearch"
                    placeholder="Search drivers by name, location, or expertise..."
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
            <div class="filter-chip active" data-category="all">All Drivers</div>
            <div class="filter-chip" data-category="licensed">Licensed</div>
            <div class="filter-chip" data-category="reviewed">Reviewed</div>
            <div class="filter-chip" data-category="tourist">Tourist</div>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <!-- Trending Drivers -->
    <section class="drivers-section">
        <h2 class="section-title">Trending Drivers</h2>
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
                <div class="driver-card"><div class="driver-info"><h3 class="driver-name">Extra Driver 1</h3><p class="driver-description">Test description for scrolling</p><button class="select-driver-btn">Select Driver</button></div></div>
            <?php else: ?>
                <p>No trending drivers available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Licensed Drivers -->
    <section class="drivers-section">
        <h2 class="section-title">Licensed Drivers</h2>
        <div class="drivers-container-grid">
            <button class="see-more-arrow" data-category="licensed" title="See More Licensed Drivers">
                <i class="fas fa-arrow-right"></i>
            </button>
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
    </section>

    <!-- Reviewed Drivers -->
    <section class="drivers-section">
        <h2 class="section-title">Reviewed Drivers</h2>
        <div class="drivers-container-grid">
            <button class="see-more-arrow" data-category="reviewed" title="See More Reviewed Drivers">
                <i class="fas fa-arrow-right"></i>
            </button>
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
    </section>

    <!-- Tourist Drivers -->
    <section class="drivers-section">
        <h2 class="section-title">Tourist Drivers</h2>
        <div class="drivers-container-grid">
            <button class="see-more-arrow" data-category="tourist" title="See More Tourist Drivers">
                <i class="fas fa-arrow-right"></i>
            </button>
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
                <div class="guide-card"><div class="guide-info"><h3 class="guide-name">Extra Guide 1</h3><p class="guide-description">Test description for scrolling</p><button class="select-guide-btn">Select</button></div></div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Initialize all functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeDriverCards();
        initializeSearchFunctionality();
        initializeSeeMoreArrows();
        initializeFilterPopup();
        initializeScrollBehavior();
    });

    // Driver cards interaction
    function initializeDriverCards() {
        const driverCards = document.querySelectorAll('.driver-card');

        driverCards.forEach(card => {
            card.addEventListener('click', function() {
                const name = this.querySelector('.driver-name').textContent;
                console.log(`Driver clicked: ${name}`);
                // Add navigation logic here
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

    // Search functionality
    function initializeSearchFunctionality() {
        const searchInput = document.getElementById('driverSearch');
        const searchButton = document.getElementById('searchButton');
        const filterChips = document.querySelectorAll('.filter-chip');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        let currentFilter = 'all';
        let searchTerm = '';

        // Get all searchable items
        function getAllSearchableItems() {
            const items = [];

            // Add driver cards
            document.querySelectorAll('.driver-card').forEach(card => {
                const name = card.querySelector('.driver-name')?.textContent || '';
                const description = card.querySelector('.driver-description')?.textContent || '';
                const section = card.closest('section').querySelector('.section-title')?.textContent || '';

                let categoryType = 'all';
                if (section.includes('Licensed')) categoryType = 'licensed';
                else if (section.includes('Reviewed')) categoryType = 'reviewed';
                else if (section.includes('Tourist')) categoryType = 'tourist';

                items.push({
                    element: card,
                    title: name,
                    description: description,
                    category: categoryType,
                    searchText: (name + ' ' + description + ' ' + section).toLowerCase()
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
                if (shouldShow) {
                    item.style.display = '';
                    visibleCount++;
                    highlightSearchTerm(item, searchTerm);
                } else {
                    item.style.display = 'none';
                    removeHighlights(item);
                }
            });

            // Show/hide sections based on visible items
            document.querySelectorAll('section').forEach(section => {
                const visibleItems = section.querySelectorAll('.driver-card:not([style*="display: none"])');
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

            const nameElement = element.querySelector('.driver-name');
            const descElement = element.querySelector('.driver-description');

            [nameElement, descElement].forEach(el => {
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
                message = `Showing ${count} ${getFilterName(filter)} drivers`;
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
                'licensed': 'Licensed Drivers',
                'reviewed': 'Reviewed Drivers',
                'tourist': 'Tourist Drivers'
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
                        <div class="no-results-title">No drivers found</div>
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
            filterChips[0].classList.add('active'); // Activate "All Drivers"
            filterAndSearchItems();
        };
    }

    // See More arrows functionality
    function initializeSeeMoreArrows() {
        const seeMoreArrows = document.querySelectorAll('.see-more-arrow');

        seeMoreArrows.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                const categoryName = getCategoryDisplayName(category);
                console.log(`See More arrow clicked for: ${categoryName}`);
                showNotification(`Viewing all ${categoryName} drivers`, 'info');
                // Here you could implement navigation to a dedicated category page
                // or expand the current section to show more items
            });
        });
    }

    // Helper function to get display name for category
    function getCategoryDisplayName(category) {
        const categoryNames = {
            'licensed': 'Licensed Drivers',
            'reviewed': 'Reviewed Drivers',
            'tourist': 'Tourist Drivers'
        };
        return categoryNames[category] || category;
    }

    // Filter popup functionality
    function initializeFilterPopup() {
        const filterToggle = document.getElementById('filterToggle');
        const filterPopup = document.getElementById('filterPopup');
        const filterCloseBtn = document.getElementById('filterCloseBtn');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        // Toggle filter popup
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            filterPopup.classList.toggle('show');
        });

        // Close filter popup
        filterCloseBtn.addEventListener('click', function() {
            filterPopup.classList.remove('show');
        });

        // Close on outside click
        filterPopup.addEventListener('click', function(e) {
            if (e.target === filterPopup) {
                filterPopup.classList.remove('show');
            }
        });

        // Apply filters
        applyFiltersBtn.addEventListener('click', function() {
            // Get selected filters
            const selectedFilters = [];
            const checkboxes = filterPopup.querySelectorAll('input[type="checkbox"]:checked');
            
            checkboxes.forEach(checkbox => {
                selectedFilters.push(checkbox.getAttribute('data-filter'));
            });

            console.log('Applied filters:', selectedFilters);
            showNotification('Filters applied successfully!', 'success');
            filterPopup.classList.remove('show');
            
            // Here you would implement the actual filtering logic
            // For now, just show a notification
        });

        // Clear filters
        clearFiltersBtn.addEventListener('click', function() {
            const checkboxes = filterPopup.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true; // Reset to default (all checked)
            });
            showNotification('Filters cleared!', 'info');
        });
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


    // Scroll behavior for category containers
    function initializeScrollBehavior() {
        const categoryContainers = document.querySelectorAll('.drivers-container:not(.no-scroll)');

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

</script>
