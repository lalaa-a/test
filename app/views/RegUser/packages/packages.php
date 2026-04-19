<section class="package-catalog-page">
    <header class="package-catalog-header">
        <div>
            <h1>Explore Travel Packages</h1>
            <p>Browse moderator-curated routes and use them to build your own itinerary.</p>
        </div>
        <a class="plan-trip-link" href="<?php echo URL_ROOT . '/RegUser/trips'; ?>">
            <i class="fa-solid fa-route"></i>
            Make My Itinerary
        </a>
    </header>

    <section class="package-search-wrap">
        <div class="package-search-input-wrap">
            <input
                type="text"
                id="user-package-search"
                class="package-search-input"
                placeholder="Search packages by name, spots, or description"
                autocomplete="off"
            >
            <button id="user-package-search-btn" type="button" class="package-search-btn" aria-label="Search packages">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
        <p id="user-package-search-info" class="package-search-info"></p>
    </section>

    <section class="package-grid-section">
        <div id="user-package-grid" class="user-package-grid"></div>

        <div id="user-package-empty" class="user-package-empty" style="display: none;">
            <i class="fa-solid fa-box-open"></i>
            <h3>No packages found</h3>
            <p>Try a different search or check back later.</p>
        </div>
    </section>
</section>

<div id="user-package-modal" class="user-package-modal" style="display: none;">
    <div class="user-package-modal-content">
        <button id="user-package-modal-close" type="button" class="user-package-modal-close" aria-label="Close package details">&times;</button>

        <div class="user-package-modal-hero">
            <img id="user-package-main-photo" src="" alt="Package cover image">
        </div>

        <div id="user-package-thumbnails" class="user-package-thumbnails"></div>

        <div class="user-package-modal-body">
            <h2 id="user-package-title"></h2>
            <p id="user-package-overview" class="user-package-overview"></p>

            <div class="user-package-meta">
                <span id="user-package-duration"></span>
                <span id="user-package-price"></span>
            </div>

            <h3>Included Stops</h3>
            <div id="user-package-spots" class="user-package-spots"></div>

            <div class="user-package-modal-actions">
                <a class="plan-trip-link" href="<?php echo URL_ROOT . '/RegUser/trips'; ?>">
                    <i class="fa-solid fa-map"></i>
                    Use This For My Itinerary
                </a>
            </div>
        </div>
    </div>
</div>
