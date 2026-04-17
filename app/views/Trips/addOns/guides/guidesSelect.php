    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Guide</h1>
        <p class="search-subtitle">Discover trusted guides for your Sri Lankan adventure</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="guideSearch"
                    placeholder="Search guides by name, location, or expertise..."
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
            <?php if(isset($mainFilters) && is_array($mainFilters)): ?>
                <?php foreach($mainFilters as $filterKey => $filter): ?>
                    <div class="filter-chip <?php echo $filterKey === 'all' ? 'active' : ''; ?>" 
                         data-category="<?php echo $filterKey; ?>">
                        <?php echo htmlspecialchars($filter['name']); ?> (<?php echo $filter['count']; ?>)
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
        <!-- Advanced Filter Popup -->
        <div class="filter-popup" id="filterPopup">
            <div class="filter-popup-content">
                <div class="filter-popup-header">
                    <div class="filter-title-area">
                        <h3>Advanced Filters</h3>
                        <div class="filter-header-sub">
                            <span id="filterMatchCount">0</span> guides match
                            <span class="dot">•</span>
                            <span id="filterActiveCount">0</span> active filters
                        </div>
                    </div>
                    <button class="filter-close-btn" id="filterCloseBtn">×</button>
                </div>
                <div class="filter-popup-body" id="filterPopupBody">
                    <div class="active-chips" id="filterActiveChips"></div>
                    <!-- Two-column responsive grid for compact controls -->
                    <div class="filter-grid">
                        <div class="filter-column">
                            <div class="filter-section">
                                <h4>Rating</h4>
                                <div class="filter-control compact">
                                    <input type="range" id="filterRatingSlider" min="0" max="5" step="0.5" value="0">
                                    <div class="range-value">Min: <span id="filterRatingDisplay">0</span></div>
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Price</h4>
                                <div class="filter-control range-dual-single" data-range="price">
                                    <div class="range-dual-track">
                                        <div class="range-track"></div>
                                        <div class="range-selection"></div>
                                        <input type="range" id="filterPriceMinSlider" min="0" max="1000" step="50" value="0" aria-label="Minimum price">
                                        <input type="range" id="filterPriceMaxSlider" min="0" max="1000" step="50" value="1000" aria-label="Maximum price">
                                    </div>
                                    <div class="range-legend">
                                        <div class="range-value">Min: <span id="filterPriceMinDisplay">Any</span></div>
                                        <div class="range-value">Max: <span id="filterPriceMaxDisplay">Any</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Verification & Availability</h4>
                                <label class="filter-checkbox">Verified Guides
                                    <input type="checkbox" id="filterVerified">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="filter-checkbox">Available (Active)
                                    <input type="checkbox" id="filterAvailable">
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                            <div class="filter-section">
                                <h4>Charge Type</h4>
                                <select id="filterChargeType">
                                    <option value="any">Any</option>
                                    <option value="per_day">Per Day</option>
                                    <option value="per_person">Per Person</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-column">
                            <div class="filter-section">
                                <h4>Languages</h4>
                                <div class="filter-control">
                                    <select id="filterLanguageSelect" class="filter-text-input">
                                        <option value="">All Languages</option>
                                    </select>
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Age</h4>
                                <div class="filter-control range-dual-single" data-range="age">
                                    <div class="range-dual-track">
                                        <div class="range-track"></div>
                                        <div class="range-selection"></div>
                                        <input type="range" id="filterAgeMinSlider" min="18" max="80" step="1" value="18" aria-label="Minimum age">
                                        <input type="range" id="filterAgeMaxSlider" min="18" max="80" step="1" value="80" aria-label="Maximum age">
                                    </div>
                                    <div class="range-legend">
                                        <div class="range-value">Min: <span id="filterAgeMinDisplay">Any</span></div>
                                        <div class="range-value">Max: <span id="filterAgeMaxDisplay">Any</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Group Size</h4>
                                <div class="filter-control compact">
                                    <input type="number" id="filterGroupMin" placeholder="Min" min="1">
                                    <input type="number" id="filterGroupMax" placeholder="Max" min="1">
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Quick Presets</h4>
                                    <div class="presets">
                                        <button class="preset-btn" data-preset="top_rated">
                                            <i class="fas fa-star"></i>
                                            <span class="preset-label">Top Rated</span>
                                        </button>
                                        <button class="preset-btn" data-preset="budget">
                                            <i class="fas fa-wallet"></i>
                                            <span class="preset-label">Budget Friendly</span>
                                        </button>
                                        <button class="preset-btn" data-preset="verified">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="preset-label">Verified</span>
                                        </button>
                                    </div>

                                    <style>
                                    /* Quick Presets layout */
                                    .presets {
                                        display:flex;
                                        gap:6px;
                                        flex-wrap:nowrap;
                                        align-items:center;
                                        margin-top:6px;
                                        overflow-x:auto;
                                    }

                                    .preset-btn {
                                        display:inline-flex;
                                        align-items:center;
                                        gap:6px;
                                        padding:6px 10px;
                                        border-radius:999px;
                                        border:1px solid #e6e6e6;
                                        background:#f7f9fb;
                                        color:#111827;
                                        font-weight:600;
                                        font-size:13px;
                                        cursor:pointer;
                                        box-shadow: 0 1px 2px rgba(16,24,40,0.04);
                                        transition: all 0.12s ease;
                                        white-space:nowrap;
                                    }

                                    .preset-btn i { font-size:0.95rem; color:#f59e0b; }

                                    .preset-btn[data-preset="budget"] i { color:#10b981; }
                                    .preset-btn[data-preset="verified"] i { color:#3b82f6; }

                                    .preset-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,24,40,0.06); }

                                    .preset-btn.active {
                                        background: var(--primary, #006a71);
                                        color: #fff;
                                        border-color: var(--primary, #006a71);
                                    }

                                    .preset-label { display:inline-block; line-height:1; }
                                    </style>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter-popup-footer">
                    <label class="live-preview">
                        <input type="checkbox" id="filterLivePreview" checked>
                        Live preview
                    </label>
                    <div class="footer-actions">
                        <button class="btn" id="filterResetBtn">Reset</button>
                        <button class="btn btn-primary" id="filterApplyBtn">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if(isset($mainFilters) && is_array($mainFilters)): ?>
        <?php foreach($mainFilters as $filterKey => $filter): ?>
            <?php if(empty($filter['accounts'])) continue; ?>
           
            <!-- <?php echo htmlspecialchars($filter['name']); ?> Section -->
            <section class="drivers-section" data-filter="<?php echo $filterKey; ?>">
                <h2 class="section-title"><?php echo htmlspecialchars($filter['name']); ?> (<?php echo $filter['count']; ?>)</h2>
                
                <div class="<?php echo $filterKey === 'all' ? 'drivers-container' : 'drivers-container-grid'; ?>">
                    <?php if($filterKey !== 'all'): ?>
                        <button class="see-more-arrow" data-category="<?php echo $filterKey; ?>" 
                                title="See More <?php echo htmlspecialchars($filter['name']); ?>">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php 
                    $displayDrivers = ($filterKey === 'all') ? $filter['accounts'] : array_slice($filter['accounts'], 0, 6);
                    foreach($displayDrivers as $driver): 
                    ?>
                        <div class="driver-card" data-user-id="<?php echo htmlspecialchars($driver->userId); ?>">
                            <button class="save-driver-btn" title="Save Driver">
                                <i class="far fa-heart"></i>
                            </button>
                            
                            <?php if($driver->dlVerified): ?>
                                <div class="verified-chip">
                                    <i class="fas fa-check-circle"></i> Verified
                                </div>
                            <?php endif; ?>
                            
                            <?php if($filterKey === 'high_rated'): ?>
                                <div class="driver-badge top-rated">Top Rated</div>
                            <?php endif; ?>
                            
                            <div class="driver-avatar">
                                <img src="<?php echo !empty($driver->profile_photo) ? URL_ROOT . '/public/uploads' . htmlspecialchars($driver->profile_photo) : URL_ROOT . '/public/img/signup/profile.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($driver->fullname); ?>">
                            </div>
                            <div class="driver-info">
                                <h3 class="driver-name"><?php echo htmlspecialchars($driver->fullname); ?></h3>
                                <div class="driver-rating">
                                    <span class="star">★</span>
                                    <span class="rating"><?php echo $driver->averageRating ? number_format($driver->averageRating, 1) : ''; ?></span>
                                    <span class="reviews">(<?php echo $driver->age; ?> years)</span>
                                </div>
                                
                                <p class="driver-description">
                                    <?php 
                                    // Show the place-specific guide description if available, otherwise fall back to bio
                                    $placeDesc = !empty($driver->guideDescription) ? $driver->guideDescription : $driver->bio;
                                    if(!empty($placeDesc)) {
                                        echo htmlspecialchars(substr($placeDesc, 0, 120)) . (strlen($placeDesc) > 120 ? '...' : '');
                                    } else {
                                        echo 'Professional Guide';
                                    }
                                    ?>
                                </p>

                                <div class="guide-pricing">
                                    <span class="price-amount">
                                        <?php 
                                        $userCurrency = $data['userCurrency'] ?? 'USD';
                                        $currencySymbol = $data['currencySymbol'] ?? '$';
                                        $converted = convertCharge($driver->baseCharge, $userCurrency);
                                        echo $converted['formatted'];
                                        ?>
                                    </span>
                                    <span class="price-type">
                                        <?php 
                                        echo $driver->chargeType === 'per_day' ? 'Per Day' : 
                                             ($driver->chargeType === 'per_person' ? 'Per Person' : ucwords(str_replace('_', ' ', $driver->chargeType)));
                                        ?>
                                    </span>
                                </div>
                                <div class="driver-actions">
                                    <button class="select-driver-btn">Select</button>
                                    <button class="view-driver-btn">View</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
        
    <?php else: ?>
        <section class="drivers-section">
            <p>No drivers available at the moment.</p>
        </section>
    <?php endif; ?>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content confirm-modal">
            <div class="modal-header">
                <h3><i class="fas fa-user-tie"></i> Confirm Guide Selection</h3>
            </div>
            <div class="modal-body">
                <div class="confirm-message">
                    <i class="fas fa-check-circle"></i>
                    <p>Are you sure you want to select this guide?</p>
                    <p class="confirm-warning">This will add <strong id="selecting-guide-name"></strong> to your trip.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button class="btn btn-primary" id="confirmBtn">Select Guide</button>
            </div>
        </div>
    </div>
