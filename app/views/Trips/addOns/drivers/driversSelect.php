    <!-- Search Section -->
    <section class="search-section">
        <h1 class="search-title">Find Your Perfect Driver</h1>
        <p class="search-subtitle">Discover trusted drivers with reliable vehicles for your journey</p>

        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="driverSearch"
                    placeholder="Search drivers by name, vehicle, or location..."
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
                            <span id="filterMatchCount">0</span> drivers match
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
                                <h4>Price Per Day</h4>
                                <div class="filter-control range-dual-single" data-range="price">
                                    <div class="range-dual-track">
                                        <div class="range-track"></div>
                                        <div class="range-selection"></div>
                                        <input type="range" id="filterPriceMinSlider" min="0" max="500" step="10" value="0" aria-label="Minimum price">
                                        <input type="range" id="filterPriceMaxSlider" min="0" max="500" step="10" value="500" aria-label="Maximum price">
                                    </div>
                                    <div class="range-legend">
                                        <div class="range-value">Min: <span id="filterPriceMinDisplay">Any</span></div>
                                        <div class="range-value">Max: <span id="filterPriceMaxDisplay">Any</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Verification</h4>
                                <label class="filter-checkbox">Verified Drivers
                                    <input type="checkbox" id="filterVerified">
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                            <div class="filter-section">
                                <h4>Vehicle Capacity</h4>
                                <div class="filter-control compact">
                                    <input type="number" id="filterMinSeatingCapacity" placeholder="Min Seats" min="1" max="15">
                                    <input type="number" id="filterMaxSeatingCapacity" placeholder="Max Seats" min="1" max="15">
                                </div>
                            </div>

                            <div class="filter-section">
                                <h4>Child Seats</h4>
                                <div class="filter-control compact">
                                    <input type="number" id="filterChildSeats" placeholder="Min Child Seats" min="0">
                                </div>
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
                                <h4>Driver Age</h4>
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
                                <h4>Vehicle Type</h4>
                                <select id="filterVehicleType" class="filter-text-input">
                                    <option value="all">All Types</option>
                                    <option value="Toyota">Toyota</option>
                                    <option value="Honda">Honda</option>
                                    <option value="Nissan">Nissan</option>
                                    <option value="Mitsubishi">Mitsubishi</option>
                                    <option value="Suzuki">Suzuki</option>
                                    <option value="Other">Other</option>
                                </select>
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
                        <div class="driver-card" data-user-id="<?php echo htmlspecialchars($driver->userId); ?>" data-vehicle-id="<?php echo htmlspecialchars($driver->vehicleId); ?>">
                            <button class="save-driver-btn" title="Save Driver">
                                <i class="far fa-heart"></i>
                            </button>
                            
                            <?php if($driver->dlVerified || $driver->verified): ?>
                                <div class="verified-chip">
                                    <i class="fas fa-check-circle"></i> Verified
                                </div>
                            <?php endif; ?>
                            
                            <?php if($filterKey === 'high_rated'): ?>
                                <div class="driver-badge top-rated">Top Rated</div>
                            <?php endif; ?>
                            
                            <!-- Vehicle Photo (Large) -->
                            <div class="vehicle-photo-main">
                                <img src="<?php echo !empty($driver->vehiclePhoto) ? URL_ROOT . '/public/uploads' . htmlspecialchars($driver->vehiclePhoto) : URL_ROOT . '/public/img/signup/profile.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($driver->make . ' ' . $driver->model); ?>">
                            </div>
                            
                            <div class="driver-info">
                                <!-- Vehicle Info as Main Title -->
                                <h3 class="vehicle-title"><?php echo htmlspecialchars($driver->make . ' ' . $driver->model); ?></h3>
                                <p class="vehicle-year"><?php echo htmlspecialchars($driver->year); ?></p>
                                
                                <!-- Capacity and Child Seats (Prominent) -->
                                <div class="vehicle-specs">
                                    <div class="spec-item">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $driver->seatingCapacity; ?> seats</span>
                                    </div>
                                    <?php if($driver->childSeats > 0): ?>
                                        <div class="spec-item">
                                            <i class="fas fa-baby"></i>
                                            <span><?php echo $driver->childSeats; ?> child seats</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Driver Info (Small Photo with Rating) -->
                                <div class="driver-profile-compact">
                                    <div class="driver-avatar-small">
                                        <img src="<?php echo !empty($driver->profilePhoto) ? URL_ROOT . '/public/uploads' . htmlspecialchars($driver->profilePhoto) : URL_ROOT . '/public/img/signup/profile.png'; ?>" 
                                             alt="<?php echo htmlspecialchars($driver->fullname); ?>">
                                    </div>
                                    <div class="driver-details-compact">
                                        <p class="driver-name-compact"><?php echo htmlspecialchars($driver->fullname); ?></p>
                                        <div class="driver-rating-compact">
                                            <span class="star">★</span>
                                            <span class="rating"><?php echo $driver->averageRating ? number_format($driver->averageRating, 1) : '0.0'; ?></span>
                                            <span class="reviews">(<?php echo $driver->age; ?> years)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="driver-pricing">
                                    <div class="price-row">
                                        <span class="price-label">Per Day:</span>
                                        <span class="price-amount">
                                            <?php 
                                            $userCurrency = $data['userCurrency'] ?? 'USD';
                                            $currencySymbol = $data['currencySymbol'] ?? '$';
                                            $converted = convertCharge($driver->totalChargePerDay, $userCurrency);
                                            echo $converted['formatted'];
                                            ?>
                                        </span>
                                    </div>
                                    <div class="price-row">
                                        <span class="price-label">Per Km:</span>
                                        <span class="price-amount">
                                            <?php 
                                            $convertedKm = convertCharge($driver->totalChargePerKm, $userCurrency);
                                            echo $convertedKm['formatted'];
                                            ?>
                                        </span>
                                    </div>
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
            <p>No drivers available for your trip dates and requirements.</p>
        </section>
    <?php endif; ?>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content confirm-modal">
            <div class="modal-header">
                <h3><i class="fas fa-car"></i> Confirm Driver Selection</h3>
            </div>
            <div class="modal-body">
                <div class="confirm-message">
                    <i class="fas fa-check-circle"></i>
                    <p>Are you sure you want to select this driver?</p>
                    <p class="confirm-warning">This will add <strong id="selecting-driver-name"></strong> to your trip.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button class="btn btn-primary" id="confirmBtn">Select Driver</button>
            </div>
        </div>
    </div>
    <!-- Embed all driver data in JavaScript to avoid backend fetch -->
    <script>
        // All drivers data embedded directly from PHP - accessible to driverSelect.js
        window.driversDataEmbedded = <?php echo json_encode(isset($mainFilters) ? array_reduce($mainFilters, function($carry, $filter) {
            if (isset($filter['accounts']) && is_array($filter['accounts'])) {
                foreach ($filter['accounts'] as $driver) {
                    $carry[] = [
                        'userId' => $driver->userId ?? null,
                        'vehicleId' => $driver->vehicleId ?? null,
                        'fullname' => $driver->fullname ?? '',
                        'profilePhoto' => $driver->profilePhoto ?? '',
                        'averageRating' => $driver->averageRating ?? 0,
                        'age' => $driver->age ?? 0,
                        'languages' => $driver->languages ?? '',
                        'verified' => $driver->verified ?? false,
                        'dlVerified' => $driver->dlVerified ?? false,
                        'make' => $driver->make ?? '',
                        'model' => $driver->model ?? '',
                        'year' => $driver->year ?? '',
                        'vehicleType' => $driver->vehicleType ?? '',
                        'vehiclePhoto' => $driver->vehiclePhoto ?? '',
                        'color' => $driver->color ?? '',
                        'seatingCapacity' => $driver->seatingCapacity ?? 0,
                        'childSeats' => $driver->childSeats ?? 0,
                        'licensePlate' => $driver->licensePlate ?? '',
                        'vehicleChargePerDay' => $driver->vehicleChargePerDay ?? 0,
                        'driverChargePerDay' => $driver->driverChargePerDay ?? 0,
                        'totalChargePerDay' => $driver->totalChargePerDay ?? 0,
                        'vehicleChargePerKm' => $driver->vehicleChargePerKm ?? 0,
                        'driverChargePerKm' => $driver->driverChargePerKm ?? 0,
                        'totalChargePerKm' => $driver->totalChargePerKm ?? 0,
                        'currency' => $driver->currency ?? 'USD',
                        'currencySymbol' => $driver->currencySymbol ?? '$'
                    ];
                }
            }
            return $carry;
        }, []) : []); ?>;
        console.log('Drivers data embedded:', window.driversDataEmbedded.length, 'drivers loaded');
    </script>