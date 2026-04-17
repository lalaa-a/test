<?php
    $tripStatus = $basicTripDetails->status ?? 'pending';

    $statusLabels = [
        'pending' => 'Pending',
        'wConfirmation' => 'Waiting Confirmation',
        'awPayment' => 'Awaiting Payment',
        'scheduled' => 'Scheduled',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    $statusIcons = [
        'pending' => 'fa-clock',
        'wConfirmation' => 'fa-hourglass-half',
        'awPayment' => 'fa-credit-card',
        'scheduled' => 'fa-calendar-check',
        'ongoing' => 'fa-plane-departure',
        'completed' => 'fa-check-circle',
        'cancelled' => 'fa-ban'
    ];

    $statusLabel = $statusLabels[$tripStatus] ?? ucfirst((string)$tripStatus);
    $statusIcon = $statusIcons[$tripStatus] ?? 'fa-calendar-check';
?>

    <div class="content-wrapper">
        <div class="trip-details-card">
            <div class="trip-header">
                <div class="trip-title-row">
                    <div class="trip-title-section">
                        <h2 class="trip-title"><?php echo htmlspecialchars($basicTripDetails->tripTitle); ?></h2>
                        <div class="trip-id">
                            <span class="trip-id-label">Trip ID:</span>
                            <span class="trip-id-value" id = "trip-id-value"><?php echo htmlspecialchars($basicTripDetails->tripId ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="trip-status-section">
                        <span class="trip-status" id="trip-status-badge" data-status="<?php echo htmlspecialchars($tripStatus); ?>">
                            <i class="fas <?php echo htmlspecialchars($statusIcon); ?>"></i>
                            <span id="trip-status-text"><?php echo htmlspecialchars($statusLabel); ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="trip-image">
                <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop&crop=center" alt="Sri Lanka Landscape" loading="lazy">
            </div>

            <div class="trip-description">
                <?php echo htmlspecialchars($basicTripDetails->description); ?>
            </div>

            <div class="trip-dates-grid">
                <div class="date-card">
                    <div class="date-label">Start Date</div>
                    <div class="date-value">
                        <i class="fas fa-plane-departure"></i>
                        <?php 
                        $date = strtotime($basicTripDetails->startDate);
                        echo htmlspecialchars(date('j F Y', $date)) ?>
                    </div>
                </div>

                <div class="date-card">
                    <div class="date-label">End Date</div>
                    <div class="date-value">
                        <i class="fas fa-plane-arrival"></i>
                        <?php 
                        $date = strtotime($basicTripDetails->endDate);
                        echo htmlspecialchars(date('j F Y', $date)) ?>
                    </div>
                </div>

                <div class="date-card">
                    <div class="date-label">Duration</div>
                    <div class="date-value">
                        <i class="fas fa-clock"></i>
                        <?php 
                            $start = new DateTime($basicTripDetails->startDate);
                            $end = new DateTime($basicTripDetails->endDate);
                            $interval = $start->diff($end);
                            echo $interval->days . ' days';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Navigation Bar -->
        <div class="date-navigation">
            <div class="date-nav-header">
                <h3 class="date-nav-title">Trip Timeline</h3>
                <div class="date-nav-controls">
                    <button class="nav-btn"> <i class="fa-solid fa-arrow-left"></i> Prev</button>
                    <button class="nav-btn">Today</button>
                    <button class="nav-btn">Next <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>
            <div class="date-nav-grid">
                <?php
                    // Assuming you have startDate and endDate
                    $startDate = new DateTime($basicTripDetails->startDate);
                    $endDate = new DateTime($basicTripDetails->endDate);
                    $interval = new DateInterval('P1D'); // 1 day interval
                    $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day')); // +1 day to include end date

                    foreach ($period as $date) {
                        $isActive = ($date->format('Y-m-d') === $startDate->format('Y-m-d')) ? 'active' : '';
                        ?>
                        <div class="date-nav-item <?php echo $isActive; ?>">
                            <div class="date-nav-day"><?php echo strtoupper($date->format('D')); ?></div>
                            <div class="date-nav-date"><?php echo $date->format('j'); ?></div>
                            <div class="date-nav-month"><?php echo $date->format('M'); ?></div>
                            <div class = "timelineDate" style = "display:none;"><?php echo $date->format('Y-m-d')?></div>
                        </div>
                        <?php
                    }
                ?>
                
                <!-- Finalize Trip Button -->
                <div class="date-nav-item finalize-item" id="finalize-trip-btn">
                    <div class="finalize-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="finalize-text">FINALIZE</div>
                </div>
            </div>
        </div>

        <div class="popup-overlay" id="add-travel-spot-popup">
            <div class="popup-content">
                <div class="popup-header">
                    <h2>Add Event To Timeline</h2>
                    <button class="popup-close-btn" id="popup-close-btn">&times;</button>
                </div>
                
                <div class="popup-body">

                    <!-- Type and Status Dropdowns -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="spot-type">Type</label>
                            <select id="spot-type" class="form-select">
                                <option value="">Select a type</option>
                                <option value="travelSpot">Travel Spot</option>
                                <option value="location">Location</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="event-status">Status</label>
                            <select id="event-status" class="form-select">
                                <option value="">Select a status</option>
                                <option value="start">Start</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="end">End </option>
                            </select>
                        </div>
                    </div>

                    <!-- Time Selection Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-time">Start Time</label>
                            <input type="text" id="start-time" class="form-input" placeholder="Select start time" readonly>
                        </div>
                        <div class="form-group">
                            <label for="end-time">End Time</label>
                            <input type="text" id="end-time" class="form-input" placeholder="Select end time" readonly>
                        </div>
                    </div>

                    <!-- Content Display Area -->
                    <div class="event-type-data" id="event-type-data">

                        <div id="location-select" class = "location-select">

                            <div class="form-group">
                                <label for="location-description">Description</label>
                                <textarea id="location-description" class="form-textarea location-description" placeholder="Add a small description about what would you do in this location..." rows="3" required></textarea>
                            </div>
                            
                        <div class="form-group">
                            <label for="location-description">Enter a place you need to search around</label>
                            <div id="autocomplete-container"> 
                                <gmp-place-autocomplete id="location-input-container"></gmp-place-autocomplete>
                            </div>
                        </div>

                            <!-- Search Controls Row -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="spot-type-select">Spot Type</label>
                                    <select id="controls" class="form-select">
                                        <option value="restaurant" selected>Restaurant</option>
                                        <option value="cafe">Cafe</option>
                                        <option value="museum">Museum</option>
                                        <option value="monument">Monument</option>
                                        <option value="park">Park</option>
                                        <option value="tourist_attraction">Tourist Attraction</option>
                                        <option value="lodging">Lodging</option>
                                        <option value="shopping_mall">Shopping Mall</option>
                                        <option value="point_of_interest">Point of Interest</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="search-radius">Search Radius (meters)</label>
                                    <input type="number" id="search-radius" class="form-input" value="1500" min="100" max="5000" step="100" placeholder="1500">
                                </div>
                            </div>

                            <div id='selected-location-container'></div>

                            <gmp-map center="45.438646,12.327573" zoom="14" map-id="12b46b4ecb983b59886cb6a1"
                                ><!-- Map id is required for Advanced Markers. -->
                                <gmp-advanced-marker></gmp-advanced-marker>
                                
                            </gmp-map>

                        </div>

                        <div id="travel-spot-select" class="travel-spot-select">
                            <div class="travel-spot-info" id='selected-spot-container'>
                                <div id="goto-travelspots-element">
                                    <p>Discover amazing travel spots and destinations for your trip.</p>
                                    <button class="btn-travel-spots" id="btn-travel-spots" onclick="window.open('<?php echo URL_ROOT; ?>/RegUser/selectTravelSpot', '_blank')">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Go to Travel Spots
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="popup-footer">
                    <button class="btn-cancel" id="btn-cancel">Cancel</button>
                    <button class="btn-save" id="btn-save">Save</button>
                </div>
            </div>
        </div>

        <!-- Recommendations Modal -->
        <div class="popup-overlay" id="recommendations-popup">
            <div class="popup-content recommendations-popup-content">
                <div class="popup-header">
                    <h2>Add Recommendations</h2>
                    <button class="popup-close-btn" id="recommendations-close-btn">&times;</button>
                </div>
                
                <div class="popup-body">
                    <div class="recommendations-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="province-select">Select Province</label>
                                <select id="province-select" class="form-select">
                                    <option value="">Select a province</option>
                                    <option value="western">Western Province</option>
                                    <option value="central">Central Province</option>
                                    <option value="southern">Southern Province</option>
                                    <option value="northern">Northern Province</option>
                                    <option value="eastern">Eastern Province</option>
                                    <option value="north-western">North Western Province</option>
                                    <option value="north-central">North Central Province</option>
                                    <option value="uva">Uva Province</option>
                                    <option value="sabaragamuwa">Sabaragamuwa Province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="district-select">Select District</label>
                                <select id="district-select" class="form-select" disabled>
                                    <option value="">Select a district</option>
                                    <!-- Districts will be populated based on province selection -->
                                </select>
                            </div>
                        </div>
                        
                        <p>When you select district and province with this, the places will be recommended based on this.</p>
                        
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="popup-footer">
                    <button class="btn-cancel" id="recommendations-cancel-btn">Cancel</button>
                    <button class="btn-save" id="recommendations-save-btn" disabled>Show Places on Map</button>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="events-map-container">
            <!-- Trip Summary Section (Hidden by default, shown when Finalize is clicked) -->
            <div class="trip-summary-section" id="trip-summary-section" style="display: none;">
                <div class="summary-header">
                    <h3 class="summary-title">
                        <i class="fas fa-clipboard-list"></i>
                        Trip Summary & Confirmation
                    </h3>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <button class="view-map-btn" id="view-map-btn" title="View route map">
                            <i class="fas fa-map"></i>
                            View Map
                        </button>
                        <button class="close-summary-btn" id="close-summary-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="summary-content" id="summary-content">
                    <!-- Summary will be dynamically loaded here -->
                </div>
                
                <div class="summary-footer">
                    <div class="summary-stats">
                        <div class="stat-item">
                            <i class="fas fa-calendar-day"></i>
                            <span id="total-days">0 Days</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="total-events">0 Events</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-route"></i>
                            <span id="total-spots">0 Locations</span>
                        </div>
                    </div>
                    <div class="summary-middle-section">
                        <div class="summary-start-end" id="summary-start-end">
                            <!-- Start and End events will be dynamically loaded here -->
                        </div>
                    </div>
                    <div class="trip-charge-summary" id="trip-charge-summary" hidden>
                        <div class="trip-charge-summary-header">
                            <div class="trip-charge-summary-title">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Payment Breakdown</span>
                            </div>
                            <span class="trip-charge-state" id="trip-charge-state">Awaiting Payment</span>
                        </div>
                        <div class="trip-charge-summary-body">
                            <div class="trip-charge-line">
                                <span class="line-label">Driver Charges</span>
                                <span class="line-value" id="trip-driver-charges">LKR 0.00</span>
                            </div>
                            <div class="trip-charge-line">
                                <span class="line-label">Guide Charges</span>
                                <span class="line-value" id="trip-guide-charges">LKR 0.00</span>
                            </div>
                            <div class="trip-charge-line subtotal-line">
                                <span class="line-label">Sub Total</span>
                                <span class="line-value" id="trip-sub-total">LKR 0.00</span>
                            </div>
                            <div class="trip-charge-line">
                                <span class="line-label">Site Charges</span>
                                <span class="line-value" id="trip-site-charges">LKR 0.00</span>
                            </div>
                            <div class="trip-site-charge-breakdown" id="trip-site-charge-breakdown">
                                <div class="trip-site-charge-title">How site charges are calculated</div>
                                <div class="trip-charge-line detail-line">
                                    <span class="line-label" id="trip-driver-booking-label">Driver booking fee (0 x LKR 0.00)</span>
                                    <span class="line-value" id="trip-driver-booking-charge">LKR 0.00</span>
                                </div>
                                <div class="trip-charge-line detail-line">
                                    <span class="line-label" id="trip-guide-booking-label">Guide booking fee (0 x LKR 0.00)</span>
                                    <span class="line-value" id="trip-guide-booking-charge">LKR 0.00</span>
                                </div>
                                <div class="trip-charge-line detail-line">
                                    <span class="line-label" id="trip-service-fee-label">Service fee (0% of Sub Total)</span>
                                    <span class="line-value" id="trip-service-fee-charge">LKR 0.00</span>
                                </div>
                            </div>
                            <div class="trip-charge-line total-line">
                                <span class="line-label" id="trip-total-charge-label">Total Charge</span>
                                <span class="line-value" id="trip-total-charge">LKR 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="confirm-trip-section">
                        <button class="confirm-trip-btn" id="confirm-trip-btn">
                            <i class="fas fa-check-double"></i>
                            Confirm Trip
                        </button>
                    </div>
                </div>
            </div>

            <!-- Events Section -->
            <div class="events-section">
                <div class="events-header">
                    <div>
                        <h3 class="events-title">Events Schedule</h3>
                        <p class="selected-date-info"><?php  
                                                      $date =  new DateTime($basicTripDetails->startDate);
                                                      echo $date->format('D, M j,Y'); 
                        ?></p>
                    </div>
                    <div class="events-header-actions">
                        <button class="add-recommendations-btn" id="add-recommendations-btn">
                            <i class="fas fa-lightbulb"></i>
                            Add Recommendations
                        </button>
                    </div>
                </div>

            <div class="events-container" id="events-container">
                <!-- Custom Event Card with Two Badge Types -->
                <div class="event-card" data-type="travelSpot" data-status="normal">
                    <div class="event-time-section">
                        <div class="time-label">START</div>
                        <div class="event-start-time">09:00</div>
                        <div class="time-label">END</div>
                        <div class="event-end-time">12:00</div>
                    </div>
                    <div class="event-image">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">Sigiriya Rock Fortress</h4>
                            </div>
                            <div class="event-header-actions">
                                <div class="event-badges">
                                    <span class="event-type-badge type-travelspot">Travel Spot</span>
                                    <span class="event-status-badge status-normal">Normal</span>
                                </div>
                                <div class="dot-menu-container">
                                    <button class="dot-menu-btn" onclick="tripEventListManager.toggleEventMenu(event, 'custom-1')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dot-menu-dropdown" id="event-menu-custom-1">
                                        <button class="dot-menu-item edit" onclick="tripEventListManager.editEvent('custom-1')">
                                            <i class="fa-solid fa-arrow-up"></i> Add event above
                                        </button>
                                        <button class="dot-menu-item edit" onclick="tripEventListManager.deleteEvent('custom-1')">
                                            <i class="fa-solid fa-arrow-down"></i> Add event below
                                        </button>
                                        <button class="dot-menu-item edit" onclick="tripEventListManager.deleteEvent('custom-1')">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        <button class="dot-menu-item delete" onclick="tripEventListManager.deleteEvent('custom-1')">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="event-description">
                            Ancient rock fortress and UNESCO World Heritage Site featuring stunning frescoes and panoramic views.
                        </p>
                        <div class="event-details">
                            <div class="event-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Sigiriya, Matale District</span>
                            </div>
                            <div class="event-detail">
                                <i class="fas fa-star"></i>
                                <span>4.9/5</span>
                            </div>
                        </div>
                        <div class="guide-section">
                            <div class="guide-info guide-none">
                                <i class="fas fa-user-slash"></i>
                                <span>No guide added</span>
                            </div>
                            <button class="guide-booking-btn" onclick="tripEventListManager.bookGuide(this, 'custom-1')">
                                <i class="fas fa-plus"></i>
                                Add Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State (hidden when events exist) -->
                <!-- <div class="empty-events" style="display: none;">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>No events scheduled</h3>
                    <p>Click "Add Event" to start planning your day</p>
                </div> -->
            </div>

            <!-- Add Event Button with Dropdown -->
            <div class="events-footer">
                <div class="add-event-wrapper">
                    <button class="add-event-btn" id="add-event-btn">
                        <i class="fas fa-plus"></i>
                        Add Event
                    </button>
                </div>
            </div>
        </div>

        <!-- Route Map Section -->
        <div class="route-map-section">
            <div class="route-map-header">
                <h3 class="route-map-title">
                    <i class="fas fa-route"></i>
                    Route Map
                </h3>
            </div>
            <div id="route-map" class="route-map"></div>
        </div>
    </div>
    </div>

