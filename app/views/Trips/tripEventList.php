
    <div class="content-wrapper">
        <div class="trip-details-card">
            <div class="trip-header">
                <div class="trip-title-row">
                    <div class="trip-title-section">
                        <h2 class="trip-title"><?php echo htmlspecialchars($basicTripDetails->tripTitle); ?></h2>
                    </div>
                    <div class="trip-status-section">
                        <span class="trip-status">
                            <i class="fas fa-calendar-check"></i>
                            Scheduled
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
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>

        <div class="popup-overlay" id="add-travel-spot-popup">
            <div class="popup-content">
                <div class="popup-header">
                    <h2>Add Event To Timeline</h2>
                    <button class="popup-close-btn" id="popup-close-btn">&times;</button>
                </div>
                
                <div class="popup-body">
                    <!-- Time Selection Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-time">Start Time</label>
                            <input type="time" id="start-time" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="end-time">End Time</label>
                            <input type="time" id="end-time" class="form-input">
                        </div>
                    </div>

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
                                <option value="checking">Checking</option>
                                <option value="normal">Normal</option>
                                <option value="checkout">Checkout</option>
                            </select>
                        </div>
                    </div>

                    <!-- Content Display Area -->
                    <div class="event-type-data" id="event-type-data">

                        <div id="location-select" class = "location-select">

                            <div class="form-group">
                                <label for="location-description">Description</label>
                                <textarea id="location-description" class="form-textarea location-description" placeholder="Add a small description about what would you do in this location..." rows="3" required></textarea>
                            </div>

                            <div id="autocomplete-container"> 
                                <gmp-place-autocomplete id="location-input-container"></gmp-place-autocomplete>
                            </div>
                            
                            <div id="map" class="location-map"></div>
                        </div>

                        <div id="travel-spot-select" class="travel-spot-select">
                            <div class="travel-spot-info" id='selected-spot-container'>
                                <p>Discover amazing travel spots and destinations for your trip.</p>
                                <button class="btn-travel-spots" id="btn-travel-spots" onclick="window.open('<?php echo URL_ROOT; ?>/RegUser/selectTravelSpot', '_blank')">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Go to Travel Spots
                                </button>
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
            </div>

            <div class="events-container">
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
                            <div class="event-badges">
                                <span class="event-type-badge type-travelspot">Travel Spot</span>
                                <span class="event-status-badge status-normal">Normal</span>
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
    </div>


