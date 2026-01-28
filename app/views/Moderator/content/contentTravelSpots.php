<!-- Page Header with Title and Action Button -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Travel Spots Management</h1>
            <p class="page-subtitle">Manage and organize travel destinations</p>
        </div>
        <button id="add-main-filter-btn" class="add-main-filter-btn">
            <i class="fas fa-plus"></i>
            Add Travel Spot
        </button>
    </div>
</div>

<!-- Travel Spot Popup -->
<div id="travel-spot-popup" class="popup-overlay">
    <div class="popup-content">
        <div class="popup-header">
            <h2>Add Travel Spot</h2>
            <button class="popup-close" id="popup-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="travel-spot-form" class="travel-spot-form">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="section-title">Basic Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="spotName">Spot Name *</label>
                        <input type="text" id="spotName" name="spotName" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="overview">Overview</label>
                        <textarea id="overview" name="overview" rows="4" placeholder="Describe the travel spot..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3 class="section-title">Location</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="province">Province *</label>
                        <select id="province" name="province" required>
                            <option value="">Select Province</option>
                            <option value="Western">Western</option>
                            <option value="Central">Central</option>
                            <option value="Southern">Southern</option>
                            <option value="Northern">Northern</option>
                            <option value="Eastern">Eastern</option>
                            <option value="North Western">North Western</option>
                            <option value="North Central">North Central</option>
                            <option value="Uva">Uva</option>
                            <option value="Sabaragamuwa">Sabaragamuwa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district">District *</label>
                        <select id="district" name="district" required>
                            <option value="">Select District</option>
                            <option value="Colombo">Colombo</option>
                            <option value="Gampaha">Gampaha</option>
                            <option value="Kalutara">Kalutara</option>
                            <option value="Kandy">Kandy</option>
                            <option value="Matale">Matale</option>
                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                            <option value="Galle">Galle</option>
                            <option value="Matara">Matara</option>
                            <option value="Hambantota">Hambantota</option>
                            <option value="Jaffna">Jaffna</option>
                            <option value="Kilinochchi">Kilinochchi</option>
                            <option value="Mannar">Mannar</option>
                            <option value="Mullaitivu">Mullaitivu</option>
                            <option value="Vavuniya">Vavuniya</option>
                            <option value="Ampara">Ampara</option>
                            <option value="Batticaloa">Batticaloa</option>
                            <option value="Trincomalee">Trincomalee</option>
                            <option value="Kurunegala">Kurunegala</option>
                            <option value="Puttalam">Puttalam</option>
                            <option value="Anuradhapura">Anuradhapura</option>
                            <option value="Polonnaruwa">Polonnaruwa</option>
                            <option value="Badulla">Badulla</option>
                            <option value="Monaragala">Monaragala</option>
                            <option value="Ratnapura">Ratnapura</option>
                            <option value="Kegalle">Kegalle</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Itinerary</h3>
                    <div id="autocomplete-container"> </div>
                    
                    <div id="itinerary" class="selected-nearby" style="margin: 5px;">
                            <!-- Selected locations spots will appear here -->
                    </div>

                    <div id="map" class="location-map"></div>
                    <div id="itinerary" class="selected-nearby">
                            <!-- Selected locations spots will appear here -->
                    </div>       
            </div>

            <!-- Timing Information -->
            <div class="form-section">
                <h3 class="section-title">Timing Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bestTimeFrom">Best Time From *</label>
                        <select id="bestTimeFrom" name="bestTimeFrom" required>
                            <option value="">Select Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bestTimeTo">Best Time To *</label>
                        <select id="bestTimeTo" name="bestTimeTo" required>
                            <option value="">Select Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="visitingDurationMax">Visiting Duration (Max Hours) *</label>
                        <input type="number" id="visitingDurationMax" name="visitingDurationMax" min="1" required>
                    </div>
                </div>
            </div>

            <!-- Practical Information -->
            <div class="form-section">
                <h3 class="section-title">Practical Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ticketPriceLocal">Ticket Price (Local) - LKR</label>
                        <input type="number" id="ticketPriceLocal" name="ticketPriceLocal" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="ticketPriceForeigner">Ticket Price (Foreigner) - USD</label>
                        <input type="number" id="ticketPriceForeigner" name="ticketPriceForeigner" min="0" step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="openingHours">Opening Hours</label>
                        <textarea id="openingHours" name="openingHours" rows="3" placeholder="e.g., 8:00 AM - 6:00 PM"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ticketDetails">Ticket Details</label>
                        <textarea id="ticketDetails" name="ticketDetails" rows="3" placeholder="Additional ticket information..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="parkingDetails">Parking Details</label>
                        <textarea id="parkingDetails" name="parkingDetails" rows="3" placeholder="Parking information..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Accessibility & Facilities -->
            <div class="form-section">
                <h3 class="section-title">Accessibility & Facilities</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="accessibility">Accessibility</label>
                        <textarea id="accessibility" name="accessibility" rows="3" placeholder="Wheelchair access, transportation options..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="facilities">Facilities</label>
                        <textarea id="facilities" name="facilities" rows="3" placeholder="Restrooms, restaurants, shops..."></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="travelerTips">Traveler Tips</label>
                        <textarea id="travelerTips" name="travelerTips" rows="3" placeholder="Best practices, safety tips..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Subfilters -->
            <div class="form-section">
                <h3 class="section-title">Subfilters</h3>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="subfilter-search">Search and Add Subfilters *</label>
                        <div class="subfilter-search-container">
                            <input type="text" id="subfilter-search" placeholder="Search for subfilters...">
                            <button type="button" id="search-subfilter-btn" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="subfilter-results" class="subfilter-results">
                            <!-- Search results will appear here -->
                        </div>
                        <div id="selected-subfilters" class="selected-subfilters">
                            <!-- Selected subfilters will appear here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="form-section">
                <h3 class="section-title">Photos</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Photo 1 *</label>
                        <div class="photo-upload">
                            <input type="file" id="photo1" accept="image/*" required>
                            <div class="photo-preview" id="photo1-preview">
                                <i class="fas fa-camera"></i>
                                <span>Click to upload</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo 2</label>
                        <div class="photo-upload">
                            <input type="file" id="photo2" accept="image/*">
                            <div class="photo-preview" id="photo2-preview">
                                <i class="fas fa-camera"></i>
                                <span>Click to upload</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo 3</label>
                        <div class="photo-upload">
                            <input type="file" id="photo3" accept="image/*">
                            <div class="photo-preview" id="photo3-preview">
                                <i class="fas fa-camera"></i>
                                <span>Click to upload</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo 4</label>
                        <div class="photo-upload">
                            <input type="file" id="photo4" accept="image/*">
                            <div class="photo-preview" id="photo4-preview">
                                <i class="fas fa-camera"></i>
                                <span>Click to upload</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nearby Spots -->
            <div class="form-section">
                <h3 class="section-title">Nearby Spots</h3>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="nearby-search">Search Nearby Spots</label>
                        <div class="nearby-search-container">
                            <input type="text" id="nearby-search" placeholder="Search for travel spots...">
                            <button type="button" id="search-nearby-btn" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="nearby-results" class="nearby-results">
                            <!-- Search results will appear here -->
                        </div>
                        <div id="selected-nearby" class="selected-nearby">
                            <!-- Selected nearby spots will appear here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                <button type="submit" id="submit-btn" class="btn-primary">Add Travel Spot</button>
            </div>
        </form>
    </div>
</div>


    <!-- Search Section -->
    <section class="search-section">
        <div class="search-container">
            <div class="search-input-wrapper">
                <input
                    type="text"
                    class="search-input"
                    id="destinationSearch"
                    placeholder="Search destinations, activities, or places..."
                    autocomplete="off"
                >
                <div class="search-icon" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></div>
            </div>
        </div>

        <div class="search-filters" id ='filter-chip-container'>
            <button class="filter-icon" id="filterToggle">
                <i class="fas fa-filter"></i>
                More Filters
            </button>

            <div class="filter-chip active" data-category="all">All Places</div>
        </div>

        <div class="search-results-info" id="searchResultsInfo"></div>
    </section>

    <div id='travel-spot-cards'></div>

    </section>



