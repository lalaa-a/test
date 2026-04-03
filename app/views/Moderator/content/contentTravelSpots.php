<!-- Page Header with Title and Action Button -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Travel Spots Management</h1>
            <p class="page-subtitle">Manage and organize travel destinations</p>
        </div>
        <button id="add-main-filter-btn" class="btn-add-vehicle">
            <i class="fas fa-plus"></i>
            Add Travel Spot
        </button>
    </div>
</div>

<!-- Travel Spot Popup -->
<div id="travel-spot-popup" class="modal">
    <div class="modal-content travel-spot-modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Travel Spot</h3>
            <button class="modal-close" id="popup-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="travel-spot-form" class="travel-spot-form">
                <!-- Basic Information -->
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
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
                    <h4><i class="fas fa-map-marker-alt"></i> Location</h4>
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
                    <h4><i class="fas fa-route"></i> Itinerary</h4>
                    <div id="autocomplete-container"> </div>
                    
                    <div id="map" class="location-map"></div>
                    <div id="itinerary" class="selected-nearby">
                            <!-- Selected locations spots will appear here -->
                    </div>       
            </div>

                <!-- Timing Information -->
                <div class="form-section">
                    <h4><i class="fas fa-clock"></i> Timing Information</h4>
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
                    <h4><i class="fas fa-ticket-alt"></i> Practical Information</h4>
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
                    <h4><i class="fas fa-wheelchair"></i> Accessibility & Facilities</h4>
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
                    <h4><i class="fas fa-filter"></i> Subfilters</h4>
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
                    <h4><i class="fas fa-camera"></i> Photos</h4>
                    <div class="upload-instructions">
                        <p><i class="fas fa-info-circle"></i> Upload up to 10 photos to showcase the travel spot. The first photo will be featured as the main image.</p>
                    </div>
                    
                    <div class="photo-upload-grid">
                        <!-- Photo Upload Slots -->
                        <div class="photo-upload-slot" data-slot="1">
                            <div class="upload-preview" id="uploadPreview1">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Main Photo</p>
                                    <span class="upload-hint">Recommended: 1200x800px</span>
                                </div>
                            </div>
                            <input type="file" id="photoUpload1" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload1">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="1" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="2">
                            <div class="upload-preview" id="uploadPreview2">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 2</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload2" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload2">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="2" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="3">
                            <div class="upload-preview" id="uploadPreview3">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 3</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload3" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload3">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="3" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="4">
                            <div class="upload-preview" id="uploadPreview4">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 4</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload4" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload4">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="4" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="5">
                            <div class="upload-preview" id="uploadPreview5">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 5</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload5" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload5">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="5" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="6">
                            <div class="upload-preview" id="uploadPreview6">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 6</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload6" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload6">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="6" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="7">
                            <div class="upload-preview" id="uploadPreview7">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 7</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload7" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload7">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="7" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="8">
                            <div class="upload-preview" id="uploadPreview8">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 8</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload8" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload8">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="8" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="9">
                            <div class="upload-preview" id="uploadPreview9">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 9</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload9" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload9">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="9" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="photo-upload-slot" data-slot="10">
                            <div class="upload-preview" id="uploadPreview10">
                                <div class="upload-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Photo 10</p>
                                </div>
                            </div>
                            <input type="file" id="photoUpload10" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload-photo" data-target="photoUpload10">
                                <i class="fas fa-plus"></i> Upload
                            </button>
                            <button type="button" class="btn-remove-photo" data-slot="10" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Nearby Spots -->
                <div class="form-section">
                    <h4><i class="fas fa-map-marked-alt"></i> Nearby Spots</h4>
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
            </form>
            <div class="form-actions">
                <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                <button type="submit" id="submit-btn" class="btn-primary" form="travel-spot-form">
                    <i class="fas fa-save"></i> Add Travel Spot
                </button>
            </div>
        </div>
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

<!-- Delete Travel Spot Confirmation Modal -->
<div id="deleteTravelSpotConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-trash-alt"></i> Confirm Delete Travel Spot</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to delete this travel spot?</p>
                <p class="confirm-warning">This action cannot be undone and will permanently remove all data associated with this travel spot.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelDeleteTravelSpotBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteTravelSpotBtn">Delete Travel Spot</button>
        </div>
    </div>
</div>
