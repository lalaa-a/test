
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
            <?php foreach ($cardData as $mainFilterId => $data):?>
                <div class="filter-chip" data-category="all"><?php echo $data["mainFilterName"]?></div>
            <?php endforeach;?>
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
                    <div class="place-actions">
                        <button class="select-place-btn">Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
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
                    <div class="place-actions">
                        <button class="select-place-btn" >Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
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
                    <div class="place-actions">
                        <button class="select-place-btn">Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
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
                    <div class="place-actions">
                        <button class="select-place-btn">Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
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
                    <div class="place-actions">
                        <button class="select-place-btn">Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
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
                    <div class="place-actions">
                        <button class="select-place-btn">Select</button>
                        <button class="view-place-btn">View</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if(isset($cardData)&& !empty($cardData)):?>
        <?php foreach($cardData as $mainFilterId => $mainFilterDeatails):?>
            <section class="trending-section">
                <h2 class="section-title"><?php echo $mainFilterDeatails["mainFilterName"]?></h2>
                <div class="trending-places-grid">
                    <button class="see-more-arrow" data-category="culture" title="See More Culture & Heritage">
                    <i class="fas fa-arrow-right"></i>
                    </button>
                    <?php foreach ($mainFilterDeatails["travelSpots"] as $travelSpotId => $spot):?>
                        <div class="place-card" id = "spot-<?php echo $travelSpotId ?>" >
                            <div class="place-image">
                                <img src="<?php echo IMG_ROOT; ?>/explore/destinations/anuradhapura.png" alt="Anuradhapura">
                            </div>
                                <div class="place-info">
                                    <h3 class="place-title"><?php echo $spot["spotName"]?></h3>
                                    <span class="place-category">Culture & Heritage</span>
                                    <div class="place-rating">
                                        <span class="star">★</span>
                                        <span class="rating-value"><?php echo $spot["averageRating"]?> (<?php echo $spot["totalReviews"]?>)</span>
                                    </div>
                                    <p class="place-description"><?php echo $spot["overview"]?></p>
                                    <div class="place-actions">
                                        <button class="select-place-btn" onclick="spotSelectionManager.showConfirmation('<?php echo $travelSpotId?>','<?php echo htmlspecialchars($spot['spotName'], ENT_QUOTES)?>')">Select</button>
                                        <button class="view-place-btn">View</button>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>  
            </section>
        <?php endforeach; ?>
    <?php endif;?>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to select  <strong id="selecting-spot-name"></strong> ?</p>
            <div class="modal-buttons">
                <button id="confirmBtn" class="modal-btn confirm">Confirm</button>
                <button id="cancelBtn" class="modal-btn cancel">Cancel</button>
            </div>
        </div>
    </div>
    
</div>


