(function() { 

    if (window.TripEventListManager) {
        console.log('TripEventListManager already exists, cleaning up...');
        if (window.tripEventListManager) {
            delete window.tripEventListManager;
        }
        delete window.TripEventListManager;
    }

    class TripEventListManager{

        constructor(){

            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.selectedSpot = null;

            this.currentSelectedDate = null;
            this.selectedLocation = null;
            this.currentEditingEventId = null;
            this.selectedGuide = null;
            this.selectedDrivers = {};
            this.currentDriverSegmentIndex = null;

            //this.mapElement = null;
            this.marker = null;

            // Route map properties
            this.routeMap = null;
            this.routeMarkers = [];
            this.routePath = null;
            this.directionsService = null;
            this.directionsRenderer = null;

            this.initializeElemenets();
            this.attachEventListeners();
            this.handleDateNavigation();


            this.nearbyMarkers = [];
            this.popupMap = null;
            this.placesService = null;
            this.selectedNearbyPlace = null; // Store data of clicked nearby place

          
            this.locationInputContainer;
            this.locationDescription;
            this.selectedLocation;
            this.placesService; // Declare as a class property
            this.recommendationMarkers = []; // Declare as a class property


            this.mapElement = document.querySelector('gmp-map');
            this.advancedMarkerElement = document.querySelector('gmp-advanced-marker');
            this.center;
            this.typeSelect;
            this.infoWindow;
            this.selectedLocation = null; // Store data of clicked place from autocomplete

            this.placeAutocomplete = document.querySelector('gmp-place-autocomplete');
            this.marker2 = null;

            this.numberOfPeople = 1; // Default value, will be updated when guide is selected   
            
            // Initialize route map when Google Maps is ready
            this.waitForGoogleMaps().then(() => {
                this.initializeRouteMap();
            }).catch(error => {
                console.error('Failed to load Google Maps:', error);
            });
        }

        // Wait for Google Maps API to load
        async waitForGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (typeof google !== 'undefined' && google.maps) {
                    resolve();
                } else {
                    const checkInterval = setInterval(() => {
                        if (typeof google !== 'undefined' && google.maps) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 100);
                    
                    // Timeout after 10 seconds
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        reject(new Error('Google Maps API failed to load'));
                    }, 10000);
                }
            });
        }

        initializeElemenets(){
            this.addTravelSpotPopup = document.getElementById('add-travel-spot-popup');

            this.addEventButton = document.getElementById('add-event-btn');
            
            // Finalize trip elements
            this.finalizeTripBtn = document.getElementById('finalize-trip-btn');
            this.tripSummarySection = document.getElementById('trip-summary-section');
            this.closeSummaryBtn = document.getElementById('close-summary-btn');
            this.confirmTripBtn = document.getElementById('confirm-trip-btn');
            this.summaryContent = document.getElementById('summary-content');
            this.eventsSection = document.querySelector('.events-section');
            this.viewMapBtn = document.getElementById('view-map-btn');
            this.summaryStartEndContainer = document.getElementById('summary-start-end');
            this.eventsMapContainer = document.querySelector('.events-map-container');
            this.popupCloseBtn = document.getElementById('popup-close-btn');
            this.btnCancel = document.getElementById('btn-cancel');
            this.btnSave = document.getElementById('btn-save');

            this.startTimeInput = document.getElementById('start-time');
            this.endTimeInput = document.getElementById('end-time');

            this.startTime = null;
            this.endTime = null;

            this.spotTypeSelect = document.getElementById('spot-type');
            this.eventTypeData = document.getElementById('event-type-data');
            this.locationSelect = document.getElementById('location-select');
            this.travelSpotSelect = document.getElementById('travel-spot-select');
            this.locationInputContainer = document.getElementById('location-input-container');
            this.locationDescription = document.getElementById('location-description');
            this.eventStatusSelect = document.getElementById('event-status');

            this.selectedSpotContainer = document.getElementById("selected-spot-container");
            this.gotoTravelSpotsElement = document.getElementById("goto-travelspots-element");
            this.selectedLocationContainer = document.getElementById("selected-location-container");

            this.tripId = document.getElementById('trip-id-value')
            this.eventsContainer = document.getElementById('events-container');
            
            // Recommendations elements
            this.recommendationsPopup = document.getElementById('recommendations-popup');
            this.addRecommendationsBtn = document.getElementById('add-recommendations-btn');
            this.recommendationsCloseBtn = document.getElementById('recommendations-close-btn');
            this.recommendationsCancelBtn = document.getElementById('recommendations-cancel-btn');
            this.recommendationsSaveBtn = document.getElementById('recommendations-save-btn');
            this.provinceSelect = document.getElementById('province-select');
            this.districtSelect = document.getElementById('district-select');
            // Initialize Flatpickr for time inputs
            this.initializeFlatpickr();
        }

        attachEventListeners(){
            this.addEventButton.addEventListener('click', () => {
                this.resetForm(); // Clear any previous event data
                this.addTravelSpotPopup.classList.add('show');
                this.setNextEventStartTime(this.tripId.textContent, this.currentSelectedDate);
            });

            this.popupCloseBtn.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.remove('show');
                const wasEditing = this.currentEditingEventId !== null;
                this.resetForm();
                // If we were editing, reload the events to restore the original state (unless in summary view)
                if (wasEditing && this.tripSummarySection.style.display !== 'block') {
                    this.loadEventCardsForDate(this.currentSelectedDate);
                }
            });

            this.btnCancel.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.remove('show');
                const wasEditing = this.currentEditingEventId !== null;
                this.resetForm();
                // If we were editing, reload the events to restore the original state (unless in summary view)
                if (wasEditing && this.tripSummarySection.style.display !== 'block') {
                    this.loadEventCardsForDate(this.currentSelectedDate);
                }
            });

            this.btnSave.addEventListener('click', () => {
                console.log('===== SAVE BUTTON CLICKED =====');
                this.saveEvent();
            });
            this.addTravelSpotPopup.addEventListener('click', (e) => {
                if (e.target === this.addTravelSpotPopup) {
                    this.closePopup();
                }
            });

            // Time input change listeners are now handled by Flatpickr onChange callbacks

            this.eventStatusSelect.addEventListener('change',() => {
                // Show/hide time fields based on event status
                const eventStatus = this.eventStatusSelect.value;
                const startTimeGroup = this.startTimeInput.closest('.form-group');
                const endTimeGroup = this.endTimeInput.closest('.form-group');
                
                if (startTimeGroup && endTimeGroup) {
                    if (eventStatus === 'start') {
                        // Start event: show start time only
                        startTimeGroup.style.display = '';
                        endTimeGroup.style.display = 'none';
                    } else if (eventStatus === 'end') {
                        // End event: show end time only
                        startTimeGroup.style.display = 'none';
                        endTimeGroup.style.display = '';
                    } else {
                        // Intermediate or other: show both times
                        startTimeGroup.style.display = '';
                        endTimeGroup.style.display = '';
                    }
                }
                
                if((this.spotTypeSelect.value === "travelSpot")&&(this.selectedSpot)){
                    this.handleSpotSelection(this.selectedSpot.spotId);
                } else if((this.spotTypeSelect.value === "location")&&(this.selectedLocation)){
                    this.handleLocationSelection(this.selectedLocation);
                }
            })

            this.locationDescription.addEventListener('input', () => {
                if((this.spotTypeSelect.value === "location")&&(this.selectedLocation)){
                    this.handleLocationSelection(this.selectedLocation);
                }
            });

            this.spotTypeSelect.addEventListener('change',(e) => this.displayEventTypeData(e));
            
            // Finalize trip event listeners
            this.finalizeTripBtn.addEventListener('click', () => this.showTripSummary());
            this.closeSummaryBtn.addEventListener('click', () => this.hideTripSummary());
            this.confirmTripBtn.addEventListener('click', () => this.confirmTrip());
            this.viewMapBtn.addEventListener('click', () => this.toggleMapView());
            
            // Recommendations event listeners
            this.addRecommendationsBtn.addEventListener('click', () => this.openRecommendationsPopup());
            this.recommendationsCloseBtn.addEventListener('click', () => this.closeRecommendationsPopup());
            this.recommendationsCancelBtn.addEventListener('click', () => this.closeRecommendationsPopup());
            this.recommendationsSaveBtn.addEventListener('click', () => this.saveRecommendations());
            this.provinceSelect.addEventListener('change', (e) => this.handleProvinceChange(e));
            this.districtSelect.addEventListener('change', (e) => this.handleDistrictChange(e));
            this.recommendationsPopup.addEventListener('click', (e) => {
                if (e.target === this.recommendationsPopup) {
                    this.closeRecommendationsPopup();
                }
            });
        }

        handleDateNavigation(){
            document.querySelectorAll('.date-nav-item').forEach(item => {

                item.addEventListener('click', () => {
                    
                    // Skip finalize button
                    if (item.classList.contains('finalize-item')) {
                        return;
                    }

                    // Hide trip summary, show events section
                    if (this.tripSummarySection && this.tripSummarySection.style.display === 'block') {
                        this.tripSummarySection.style.display = 'none';
                        this.eventsSection.style.display = 'block';
                        this.eventsMapContainer.classList.remove('summary-active');
                    }

                    document.querySelectorAll('.date-nav-item').forEach(i => i.classList.remove('active'));
                                
                    item.classList.add('active');
                                
                    const day = item.querySelector('.date-nav-day').textContent;
                    const date = item.querySelector('.date-nav-date').textContent;
                    const month = item.querySelector('.date-nav-month').textContent;
                    const timelineDate = item.querySelector(".timelineDate").textContent.trim();

                    const jsDate = new Date(timelineDate);
                    const formattedDate = jsDate.toISOString().split('T')[0];

                    this.currentSelectedDate = formattedDate; //getting the current seleted date when selecting from the timeline
                            
                    const selectedDateInfo = document.querySelector('.selected-date-info');
                    selectedDateInfo.textContent = `${day}, ${month} ${date}, ${jsDate.getFullYear()}`;
                            
                    console.log('Loading events for:',selectedDateInfo.textContent);
                    this.loadEventCardsForDate(formattedDate);
                });
            });

            const timelineDate = document.querySelector(".timelineDate").textContent.trim();
            const jsDate = new Date(timelineDate);
            this.currentSelectedDate = jsDate.toISOString().split('T')[0];
            this.loadEventCardsForDate(this.currentSelectedDate);
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        initializeFlatpickr() {
            // Initialize start time picker
            this.startTimePicker = flatpickr(this.startTimeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: false,
                minTime: "00:00",
                maxTime: "23:59",
                onChange: (selectedDates, dateStr) => {
                    
                    // Update end time min when start time changes
                    if (selectedDates.length > 0) {
                        
                        const selectedTime = selectedDates[0];
                        const hours = selectedTime.getHours().toString().padStart(2, '0');
                        const minutes = selectedTime.getMinutes().toString().padStart(2, '0');
                        const time24 = `${hours}:${minutes}`;
                        
                        // Set minimum time for end time picker
                        this.endTimePicker.set('minTime', time24);
                        
                        // If end time is set and is earlier than start time, clear it
                        if (this.endTimePicker.selectedDates[0]) {
                            // Get the end time value and compare
                            const endTimeInstance = this.endTimePicker.selectedDates[0];
                            if (endTimeInstance && endTimeInstance < selectedTime) {
                                this.endTimePicker.clear();
                                alert('End time must be later than start time. Please select a new end time.');
                            }
                        }
                    }

                    // Trigger card update if spot/location selected
                    if((this.spotTypeSelect.value === "travelSpot") && this.selectedSpot){
                        this.handleSpotSelection(this.selectedSpot.spotId);
                    } else if((this.spotTypeSelect.value === "location") && this.selectedLocation){
                        this.handleLocationSelection(this.selectedLocation);
                    }
                }
            });

            // Initialize end time picker
            this.endTimePicker = flatpickr(this.endTimeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: false,
                minTime: "00:00",
                maxTime: "23:59",
                onChange: (selectedDates, dateStr) => {
                    // Validate end time is not earlier than start time
                    if (selectedDates.length > 0 && this.startTimePicker.selectedDates.length > 0) {
                        const startTime = this.startTimePicker.selectedDates[0];
                        const endTime = selectedDates[0];
                        
                        if (endTime < startTime) {
                            alert('End time cannot be earlier than start time.');
                            this.endTimePicker.clear();
                            return;
                        }
                    }

                    // Trigger card update if spot/location selected
                    if((this.spotTypeSelect.value === "travelSpot") && this.selectedSpot){
                        this.handleSpotSelection(this.selectedSpot.spotId);
                    } else if((this.spotTypeSelect.value === "location") && this.selectedLocation){
                        this.handleLocationSelection(this.selectedLocation);
                    }
                }
            });
        }

        async setNextEventStartTime(tripId, eventDate) {

            try{
                const lastAddedEvent = await fetch(this.URL_ROOT+`/RegUser/getLastAddedEvent/${tripId}/${eventDate}`);
                const data = await lastAddedEvent.json();

                if (data.success && data.eventCard) {
                    console.log(data.eventCard);
                    
                    // Parse 24-hour time and set to Flatpickr
                    const timeString = data.eventCard.endTime;
                    const [hours, minutes] = timeString.split(':');

                    const time = `${hours}:${minutes}`;
                    console.log(time);

                    this.startTimePicker.setDate(time); //setting the start time to prvious event end time (user can change it this assigns automatically for ease)
                    this.endTimePicker.set('minTime', time); //setiing the endTime min to start time begining
                } else {
                    
                }
            } catch (error){
                console.error('Error fetching last added event:', error);
            }
            
        }

        formatTimeToAMPM(time) {
            if (!time) return '';
            
            // Split time string (e.g., "14:30" or "14:30:00")
            const [hours, minutes] = time.split(':');
            let hour = parseInt(hours, 10);
            const minute = minutes || '00';
            
            // Determine AM/PM
            const period = hour >= 12 ? 'PM' : 'AM';
            
            // Convert to 12-hour format
            hour = hour % 12 || 12; // Convert 0 to 12 for midnight
            
            return `${hour}:${minute} ${period}`;
        }

        convertTo24Hour(time12h) {
            if (!time12h) return '';
            
            const [time, period] = time12h.split(' ');
            let [hours, minutes] = time.split(':');
            hours = parseInt(hours, 10);
            
            if (period === 'PM' && hours !== 12) {
                hours += 12;
            } else if (period === 'AM' && hours === 12) {
                hours = 0;
            }
            
            return `${hours.toString().padStart(2, '0')}:${minutes}`;
        }

        async handleGuideSelection(guideData){

            console.log("===== HANDLE GUIDE SELECTION CALLED =====");
            console.log("selected guide data:", guideData);
            
            // Store selected guide data
            this.selectedGuide = guideData;
            console.log("Guide stored in this.selectedGuide:", this.selectedGuide);
            
            const guideSection = document.getElementById('selected-guide-section-pop'); // selected guide displaying area

            guideSection.classList.remove('guide-available', 'guide-none', 'guide-unavailable');
            guideSection.classList.add('guide-booked');

            // Remove existing guide details if any
            const existingDetails = guideSection.parentElement.querySelector('.guide-details');
            if (existingDetails) {
                existingDetails.remove();
            }

            // Create structured guide display
            const rating = parseFloat(guideData.averageRating) || 0;
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

            let starsHtml = '';
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star"></i>';
            }
            if (hasHalfStar) {
                starsHtml += '<i class="fas fa-star-half-alt"></i>';
            }
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star"></i>';
            }

            // Calculate and display guide charge
            const charge = guideData.convertedCharge || guideData.baseCharge || 0;
            const chargeType = guideData.chargeType || 'per_day';
            const currencySymbol = guideData.currencySymbol || '$';
            const currency = guideData.currency || 'USD';

            let chargeHtml = '';

            if (chargeType === 'per_person') {
                // Calculate based on number of people
                try {
                    const tripId = this.tripId.textContent;
                    const response = await fetch(`${this.URL_ROOT}/RegUser/getTripDetails/${tripId}`);
                    const data = await response.json();
                    
                    if (data.success && data.trip) {
                        const numberOfPeople = parseInt(data.trip.numberOfPeople) || 1;
                        this.numberOfPeople = numberOfPeople; // Store for later use in summary
                        const totalCharge = charge * numberOfPeople;
                        
                        chargeHtml = `
                            <div class="guide-charge">
                                <span class="charge-label">Fee:</span>
                                <span class="charge-value">${currencySymbol}${charge.toFixed(2)} × ${numberOfPeople}</span>
                                <div class="charge-total">
                                    <span class="total-label">=</span>
                                    <span class="total-value">${currencySymbol}${totalCharge.toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                    }
                } catch (error) {
                    console.error('Error calculating guide charge:', error);
                    chargeHtml = `
                        <div class="guide-charge">
                            <span class="charge-label">Fee:</span>
                            <span class="charge-value">${currencySymbol}${charge.toFixed(2)} per person</span>
                        </div>
                    `;
                }
            } else if (chargeType === 'per_hour') {
                chargeHtml = `
                    <div class="guide-charge">
                        <span class="charge-label">Fee:</span>
                        <span class="charge-value">${currencySymbol}${charge.toFixed(2)} per hour</span>
                    </div>
                `;
            } else if (chargeType === 'per_day') {
                chargeHtml = `
                    <div class="guide-charge">
                        <span class="charge-label">Fee:</span>
                        <span class="charge-value">${currencySymbol}${charge.toFixed(2)} per day</span>
                    </div>
                `;
            } else {
                // Fixed rate
                chargeHtml = `
                    <div class="guide-charge">
                        <span class="charge-label">Fee:</span>
                        <span class="charge-value">${currencySymbol}${charge.toFixed(2)} (Fixed)</span>
                    </div>
                `;
            }

            // Update guide section with compact layout: first row name+rating, second row fee
            guideSection.innerHTML = `
                <div class="guide-info-card">
                    <div class="guide-avatar">
                        <img src="${guideData.profilePhoto || '/public/img/signup/profile.png'}" alt="${this.escapeHtml(guideData.fullName)}" onerror="this.src='/public/img/signup/profile.png'">
                    </div>
                    <div class="guide-details">
                        <div class="guide-header">
                            <div class="guide-header-left">
                                <div class="guide-name">${this.escapeHtml(guideData.fullName)}</div>
                                <div class="guide-rating">
                                    <div class="stars">${starsHtml}</div>
                                    <span class="rating-value">${rating.toFixed(1)}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Second row: compact fee calculation -->
                        ${chargeHtml}
                    </div>
                    <div class="guide-actions">
                        <button class="change-guide-btn" onclick="window.tripEventListManager.openGuideSelection()" title="Change Guide">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    </div>
                </div>
            `;

        }

        removeGuide() {
            // Clear selected guide data
            this.selectedGuide = null;
            
            const guideSection = document.getElementById('selected-guide-section');
            
            guideSection.classList.remove('guide-booked');
            guideSection.classList.add('guide-none');
            
            guideSection.innerHTML = `
                <div class="guide-status">
                    <i class="fas fa-user-plus"></i>
                    <span class="guide-status-text">No Guide Selected</span>
                </div>
                <button class="add-guide-btn" onclick="window.tripEventListManager.openGuideSelection()">
                    <i class="fas fa-plus"></i>
                    Add Guide
                </button>
            `;
        }

        openGuideSelection() {
            // Get the current spot ID from the selected spot data
            const spotId = this.selectedSpot?.spotId || this.selectedSpot?.id;
            
            if (!spotId) {
                alert('Please select a travel spot first before adding a guide.');
                return;
            }
            
            // Open guide selection window
            const guideSelectUrl = `${this.URL_ROOT}/RegUser/guidesSelect/${spotId}`;
            window.open(guideSelectUrl, 'guideSelection', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        }

        async handleDriverSelection(driverData) {
            console.log("===== HANDLE DRIVER SELECTION CALLED =====");
            console.log("Selected driver data:", driverData);
            
            // Store selected driver data
            if (!this.selectedDrivers) {
                this.selectedDrivers = {};
            }
            
            // Use the stored segment index from when the window was opened
            const segmentIndex = driverData.segmentIndex !== undefined ? driverData.segmentIndex : 
                                (this.currentDriverSegmentIndex !== null ? this.currentDriverSegmentIndex : 0);
            
            console.log(`Storing driver for segment index: ${segmentIndex}`);
            
            this.selectedDrivers[segmentIndex] = {
                userId: driverData.userId,
                vehicleId: driverData.vehicleId,
                fullName: driverData.fullName,
                profilePhoto: driverData.profilePhoto,
                averageRating: driverData.averageRating,
                age: driverData.age,
                languages: driverData.languages,
                verified: driverData.verified,
                make: driverData.make,
                model: driverData.model,
                year: driverData.year,
                vehicleType: driverData.vehicleType,
                vehiclePhoto: driverData.vehiclePhoto,
                seatingCapacity: driverData.seatingCapacity,
                childSeats: driverData.childSeats,
                totalChargePerDay: driverData.totalChargePerDay,
                totalChargePerKm: driverData.totalChargePerKm,
                formattedChargePerDay: driverData.formattedChargePerDay,
                formattedChargePerKm: driverData.formattedChargePerKm,
                currency: driverData.currency,
                currencySymbol: driverData.currencySymbol
            };
            
            console.log("Driver stored in this.selectedDrivers:", this.selectedDrivers);
            console.log(`Total drivers stored: ${Object.keys(this.selectedDrivers).length}`);
            console.log(`Segments with drivers: ${Object.keys(this.selectedDrivers).join(', ')}`);
            
            // Clear the current segment index
            this.currentDriverSegmentIndex = null;
            
            // Refresh the trip summary to show the selected driver
            await this.showTripSummary();
        }

        openDriverSelection(segmentIndex = 0) {
            const tripId = this.tripId?.textContent;
            
            if (!tripId) {
                alert('Trip ID not found. Please try again.');
                return;
            }
            
            // Store the segment index for when the driver is selected
            this.currentDriverSegmentIndex = segmentIndex;
            
            console.log(`Opening driver selection for segment ${segmentIndex}`);
            
            // Open driver selection window with segment index in URL
            const driverSelectUrl = `${this.URL_ROOT}/RegUser/driversSelect/${tripId}?segment=${segmentIndex}`;
            window.open(driverSelectUrl, 'driverSelection', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        }

        closePopup(){
            this.addTravelSpotPopup.classList.remove('show');
            this.resetForm();
        }

        resetForm(){
            console.log('===== RESET FORM CALLED =====');
            console.log('Clearing selectedSpot:', this.selectedSpot);
            console.log('Clearing selectedGuide:', this.selectedGuide);
            
            // Clear Flatpickr instances
            if (this.startTimePicker) {
                this.startTimePicker.clear();
            }
            if (this.endTimePicker) {
                this.endTimePicker.clear();
                this.endTimePicker.set('minTime', '00:00'); // Reset min time
            }
            
            this.spotTypeSelect.value = '';
            this.eventStatusSelect.value = '';
            this.locationDescription.value = '';
            this.locationSelect.classList.remove('active');
            this.travelSpotSelect.classList.remove('active');
            
            // Show both time fields by default
            const startTimeGroup = this.startTimeInput.closest('.form-group');
            const endTimeGroup = this.endTimeInput.closest('.form-group');
            if (startTimeGroup) {
                startTimeGroup.style.display = '';
            }
            if (endTimeGroup) {
                endTimeGroup.style.display = '';
            }

            // Remove all event card wrappers from selected spot container
            if (this.selectedSpotContainer) {
                const spotCards = this.selectedSpotContainer.querySelectorAll('.event-card-wrapper');
                spotCards.forEach(card => card.remove());
            }

            // Remove all event card wrappers from selected location container
            if (this.selectedLocationContainer) {
                const locationCards = this.selectedLocationContainer.querySelectorAll('.event-card-wrapper');
                locationCards.forEach(card => card.remove());
                // Clear any remaining content
                this.selectedLocationContainer.innerHTML = '';
            }

            // Ensure goto element is visible
            if (this.gotoTravelSpotsElement) {
                this.gotoTravelSpotsElement.style.display = 'block';
            }

            this.selectedSpot = null;
            this.selectedLocation = null;
            this.currentEditingEventId = null; // Clear editing mode
            
            // Clear selected guide data
            this.selectedGuide = null;
            
            // Reset guide section to default state
            const guideSection = document.getElementById('selected-guide-section-pop');
            if (guideSection) {
                guideSection.classList.remove('guide-booked', 'guide-available', 'guide-unavailable');
                guideSection.classList.add('guide-none');
                guideSection.innerHTML = `
                    <div class="guide-status">
                        <i class="fas fa-user-plus"></i>
                        <span class="guide-status-text">No Guide Selected</span>
                    </div>
                    <button class="add-guide-btn" onclick="window.tripEventListManager.openGuideSelection()">
                        <i class="fas fa-plus"></i>
                        Add Guide
                    </button>
                `;
            }

            document.getElementById('autocomplete-container').style.display = 'block';
            this.selectedLocationContainer.innerHTML = '';
            
            console.log('===== RESET FORM COMPLETE =====');
            console.log('selectedSpot is now:', this.selectedSpot);
            console.log('selectedGuide is now:', this.selectedGuide);
            console.log('currentEditingEventId is now:', this.currentEditingEventId);

        }

        displayEventTypeData(event){

            this.locationSelect.classList.remove('active');
            this.travelSpotSelect.classList.remove('active');

            if(event.target.value === "travelSpot"){
                this.travelSpotSelect.classList.add('active');

            } else if(event.target.value === 'location'){
                this.locationSelect.classList.add('active');
                // Initialize map after the container is visible
                setTimeout(() => {
                    this.initMap();
                }, 100);

            } else {
                console.log("No selection..");
            }
        }
        
        /*
        async initMap() {

            let selectedPlace = null;  

            const { Map } = await google.maps.importLibrary("maps");
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");

            this.mapElement = new Map(document.getElementById("map"), {
                center: { lat: -34.397, lng: 150.644 },
                zoom: 8,
                mapId: "12b46b4ecb983b59de763776"
            });

            // getting the autocomplete element
            const placeAutocomplete = this.locationInputContainer;  

            this.findNearbyPlaces();


            console.log("PlaceAutocomplete appended to DOM i");

            placeAutocomplete.addEventListener("gmp-select", async ({ placePrediction }) => {

                const place = placePrediction.toPlace();

                await place.fetchFields({
                    fields: ['displayName', 'formattedAddress', 'location'],
                });

                if (!place.location) {
                    console.log("No location available");
                    return;
                }

                if (this.marker) {
                    this.marker.setMap(null);
                }

                //creating the advanced marker element
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                this.marker = new AdvancedMarkerElement({
                    map: this.mapElement,
                    position: place.location,
                    title: place.displayName,
                });

                selectedPlace = {
                    name: place.displayName,
                    address: place.formattedAddress,
                    lat: place.location.lat(),
                    lng: place.location.lng()
                };

                this.mapElement.setCenter(place.location);
                this.mapElement.setZoom(17);
                placeAutocomplete.value = '';

                const locationData = {
                    spotName : selectedPlace.name,
                    description: this.locationDescription.value,
                    averageRating : null,
                    itinerary : [{latitude: selectedPlace.lat, longitude: selectedPlace.lng, pointId: null, pointName: selectedPlace.name}]
                };
                this.selectedLocation = locationData;
                this.locationInputContainer.innerHTML='';
                this.handleLocationSelection(locationData); //to render the selected location card
            });
        }   
       */

        /*
        async initMap() {
            let selectedPlace = null;

            const { Map } = await google.maps.importLibrary("maps");
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

            
            this.mapElement = new Map(document.getElementById("map"), {
                center: { lat: 6.053519, lng: 80.220978 }, // Centered on Galle, Sri Lanka
                zoom: 14,
                mapId: "12b46b4ecb983b59de763776"
            });
        

            this.mapElement = document.querySelector('gmp-map');

            // Initialize the PlacesService as a class property
            this.placesService = new google.maps.places.PlacesService(this.mapElement);

            this.nearbySearch();

            const placeAutocomplete = this.locationInputContainer; // Assuming this is set elsewhere in your class

            placeAutocomplete.addEventListener("gmp-select", async ({ placePrediction }) => {
                const place = await placePrediction.toPlace();

                await place.fetchFields({
                    fields: ['displayName', 'formattedAddress', 'location'],
                });

                if (!place.location) {
                    console.log("No location available");
                    return;
                }

                if (this.marker) {
                    this.marker.setMap(null);
                }

                this.marker = new AdvancedMarkerElement({
                    map: this.mapElement,
                    position: place.location,
                    title: place.displayName,
                });

                selectedPlace = {
                    name: place.displayName,
                    address: place.formattedAddress,
                    lat: place.location.lat(),
                    lng: place.location.lng()
                };

                this.mapElement.setCenter(place.location);
                this.mapElement.setZoom(17);
                placeAutocomplete.value = '';

                const locationData = {
                    spotName : selectedPlace.name,
                    description: this.locationDescription ? this.locationDescription.value : '',
                    averageRating : null,
                    itinerary : [{latitude: selectedPlace.lat, longitude: selectedPlace.lng, pointId: null, pointName: selectedPlace.name}]
                };
                this.selectedLocation = locationData;
                this.locationInputContainer.innerHTML='';
                this.handleLocationSelection(locationData);

                // Optional: Re-run nearby search around the newly selected place
                // this.findNearbyRecommendations(place.location);
            });
        }
        */
        
        async initMap() {
            // 1. Request all needed libraries at once
            const [
                { PlaceAutocompleteElement, Place },
                { AdvancedMarkerElement, PinElement },
                { Map, InfoWindow },
                { LatLng }
            ] = await Promise.all([
                google.maps.importLibrary('places'),
                google.maps.importLibrary('marker'),
                google.maps.importLibrary('maps'),
                google.maps.importLibrary('core'),
                google.maps.importLibrary('geometry')
            ]);

            // 2. Initialize Map properties
            this.innerMap = this.mapElement.innerMap;
            this.innerMap.setOptions({
                mapTypeControl: false,
            });

            this.infoWindow = new InfoWindow();
            this.nearbyMarkers = []; // Array to track search results markers

            this.marker2 = new AdvancedMarkerElement({
                map: this.innerMap,
                title: "Selected Place"
            });

            // 4. Set up Autocomplete Listener
            this.placeAutocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                

                await place.fetchFields({
                    fields: ['displayName', 'formattedAddress', 'location', 'viewport', 'rating'],
                });


                this.placeData = {
                    spotName: place.displayName ,
                    averageRating: place.rating || 0,
                    description : this.locationDescription ? this.locationDescription.value : '',
                    itinerary : [{latitude: place.location.lat(), longitude: place.location.lng(), pointId: null, pointName: place.displayName}],
                };
                this.selectedLocation = this.placeData; // Store selected location data for later use

                console.log('Selected Place Data:', this.placeData);

                if (!place.location) return;

                // Update marker position
                this.marker2.position = place.location;

                // Adjust map view
                if (place.viewport) {
                    this.innerMap.fitBounds(place.viewport);
                } else {
                    this.innerMap.setCenter(place.location);
                    this.innerMap.setZoom(17);
                }

                const content = document.createElement('div');
                const addressText = document.createElement('span');
                addressText.textContent = place.formattedAddress;
                content.appendChild(addressText);

                this.updateInfoWindow(place.displayName, content, this.marker2);

                // Trigger nearby search automatically when a new place is selected
                if (this.typeSelect && this.typeSelect.value) {
                    this.nearbySearch();
                }
            });

            // 5. Set up UI listeners
            this.typeSelect = document.getElementById('controls');
            if (this.typeSelect) {
                this.typeSelect.addEventListener('change', () => {
                    this.nearbySearch();
                });
            }

            console.log("Map initialization complete");
        }

        async nearbySearch() {
            const { Place, SearchNearbyRankPreference } = await google.maps.importLibrary('places');
            const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary('marker');
            const { spherical } = await google.maps.importLibrary('geometry');
            const { LatLngBounds } = await google.maps.importLibrary('core');

            // FIX: Use the position of the selected place (marker2) as the search center
            // If marker2 hasn't been placed yet, fall back to the current map center
            const searchCenter = this.marker2.position ;
            
            const bounds = this.innerMap.getBounds();
            if (!bounds || !searchCenter) return;

            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();
            const diameter = spherical.computeDistanceBetween(ne, sw);
            //const radius = Math.min(diameter / 2, 50000); 
            const radius = 1000; 

            const request = {
                fields: ['displayName', 'location', 'formattedAddress', 'googleMapsURI', 'id', 'rating'],
                locationRestriction: {
                    center: searchCenter,
                    radius: radius,
                },
                includedPrimaryTypes: [this.typeSelect.value],
                maxResultCount: 20,
                rankPreference: SearchNearbyRankPreference.POPULARITY,
            };

            const { places } = await Place.searchNearby(request);

            // Clear existing nearby markers
            this.nearbyMarkers.forEach(marker => {
                marker.map = null;
            });
            this.nearbyMarkers = [];

            if (places && places.length > 0) {
                const newBounds = new LatLngBounds();
                // Include the original selected place in the new bounds
                newBounds.extend(searchCenter);

                places.forEach((place) => {
                    if (!place.location) return;
                    newBounds.extend(place.location);

                    const recommendationPin = new PinElement({
                        background: '#006A71',
                        borderColor: '#48A6A7',
                        glyphColor: '#48A6A7'
                    });

                    const marker = new AdvancedMarkerElement({
                        map: this.innerMap,
                        position: place.location,
                        title: place.displayName,
                    });
                    marker.append(recommendationPin);
                    
                    // Track this marker so we can remove it later
                    this.nearbyMarkers.push(marker);

                    const content = document.createElement('div');
                    const address = document.createElement('div');
                    address.textContent = place.formattedAddress || '';
                    
                    // Add rating if available
                    if (place.rating) {
                        const ratingDiv = document.createElement('div');
                        ratingDiv.style.marginTop = '5px';
                        ratingDiv.style.fontWeight = 'bold';
                        ratingDiv.innerHTML = `⭐ Rating: ${place.rating}/5`;
                        content.appendChild(ratingDiv);
                    }
                    

                    const placeId = document.createElement('div');
                    placeId.style.fontSize = '0.8em';
                    placeId.textContent = `ID: ${place.id}`;
                    content.append(placeId);

                    if (place.googleMapsURI) {
                        const link = document.createElement('a');
                        link.href = place.googleMapsURI;
                        link.target = '_blank';
                        link.textContent = 'View Details on Google Maps';
                        link.style.display = 'block';
                        link.style.marginTop = '5px';
                        content.appendChild(link);
                    }

                    marker.addListener('gmp-click', () => {
                        this.innerMap.panTo(place.location);
                        
                        // Capture the place data
                        this.placeData = {
                            name: place.displayName ? place.displayName : this.selectedLocation.displayName,
                            rating: place.rating || 0,
                            lat: place.location.lat() ? place.location.lat() : this.selectedLocation.location.lat(),
                            lng: place.location.lng() ? place.location.lng() : this.selectedLocation.location.lng(),
                            address: place.formattedAddress ? place.formattedAddress : this.selectedLocation.formattedAddress,
                        };
                        
                        console.log('Selected Place Data:', this.placeData);
                        
                        // Store the selected place data for potential use
                        this.selectedNearbyPlace = this.placeData;
                        
                        // Render a chip to indicate the selected place
                        this.renderSelectedNearbyPlace(this.placeData);

                        this.updateInfoWindow(place.displayName, content, marker);

                        const locationData = {
                            spotName : this.placeData.name,
                            description: this.locationDescription ? this.locationDescription.value : '',
                            averageRating : this.placeData.rating,
                            itinerary : [{latitude: this.placeData.lat, longitude: this.placeData.lng, pointId: null, pointName: this.placeData.name}]
                        };
                        this.selectedLocation = locationData;
                        this.locationInputContainer.innerHTML='';
                        this.handleLocationSelection(locationData);
                    });
                });

                this.innerMap.fitBounds(newBounds, 100);
            } else {
                console.log('No results found for nearby search');
            }
        }

        updateInfoWindow(title, content, anchor) {
            this.infoWindow.setContent(content);
            this.infoWindow.setHeaderContent(title);
            this.infoWindow.open({
                anchor: anchor,
                map: this.innerMap
            });
        }

        // Render a removable chip showing the currently selected nearby place
        renderSelectedNearbyPlace(placeData) {
            try {
                const container = document.getElementById('selected-location-container');
                if (!container) return;

                // Clear previous chip
                container.innerHTML = '';

                const chip = document.createElement('div');
                chip.className = 'selected-place-chip';

                const title = document.createElement('span');
                title.className = 'chip-title';
                title.textContent = placeData.name;

                const meta = document.createElement('span');
                meta.className = 'chip-meta';
                meta.textContent = placeData.rating ? `⭐ ${placeData.rating}` : '';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'chip-remove';
                removeBtn.title = 'Remove selected place';
                removeBtn.innerHTML = '&times;';
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.clearSelectedNearbyPlace();
                });

                chip.appendChild(title);
                chip.appendChild(meta);
                chip.appendChild(removeBtn);

                container.appendChild(chip);

                // Store for reference
                this.selectedNearbyPlace = placeData;
            } catch (err) {
                console.error('Error rendering selected place chip:', err);
            }
        }

        clearSelectedNearbyPlace() {
            this.selectedNearbyPlace = null;
            this.selectedLocation = null;
            const container = document.getElementById('selected-location-container');
            if (container) container.innerHTML = '';
        }

        async saveEvent(){
        
            console.log('===== SAVE EVENT CALLED =====');
            console.log('Current editing event ID:', this.currentEditingEventId);
            console.log('Selected guide:', this.selectedGuide);
            
            const type = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            const locationDescription = this.locationDescription.value;

            if(!this.validateInput()){
                return;
            }

            // Convert 12-hour format to 24-hour format strings (HH:MM) based on event status
            let startTime24 = null;
            let endTime24 = null;
            
            if (eventStatus === 'start') {
                // Start event: only start time
                startTime24 = this.convertTo24Hour(this.startTimeInput.value);
            } else if (eventStatus === 'end') {
                // End event: only end time
                endTime24 = this.convertTo24Hour(this.endTimeInput.value);
            } else {
                // Intermediate: both times
                startTime24 = this.convertTo24Hour(this.startTimeInput.value);
                endTime24 = this.convertTo24Hour(this.endTimeInput.value);
            }

            console.log('Start time 24h:', startTime24);
            console.log('End time 24h:', endTime24);
            console.log('Event status:', eventStatus);

            let eventData = {
                eventDate: this.currentSelectedDate,
                eventType: type,
                eventStatus: eventStatus,
                tripId : this.tripId.textContent
            };
            
            // Add times based on what's available
            if (startTime24) {
                eventData.startTime = startTime24;
            }
            if (endTime24) {
                eventData.endTime = endTime24;
            }

            if(type === 'location'){
                eventData.locationName = this.selectedLocation.spotName;
                eventData.latitude = this.selectedLocation.itinerary[0].latitude;
                eventData.longitude = this.selectedLocation.itinerary[0].longitude;
                eventData.description = locationDescription;

            } else if(type === 'travelSpot'){
                eventData.travelSpotId = this.selectedSpot.spotId;
            }
            //console.log("event data ", eventData);

            let URL;
            let msg;
            let METHOD;

            if(this.currentEditingEventId){

                METHOD = 'PUT';
                URL = `${this.URL_ROOT}/RegUser/editEvent/${this.currentEditingEventId}`;
                msg =  `Event Edited to ${this.currentSelectedDate} Successfully.`
            } else{
                METHOD = 'POST'
                URL = `${this.URL_ROOT}/RegUser/addEvent`;
                msg = `Event added to ${this.currentSelectedDate} Successfully.`
            }

            try {
                const response = await fetch(URL, {
                    method: METHOD,
                    body: JSON.stringify(eventData)
                });

                const result = await response.json();
                console.log('===== EVENT SAVE RESPONSE =====', result);

                if (result.success) {
                    console.log('Event saved successfully, checking guide save conditions...');
                    console.log('  - selectedGuide:', !!this.selectedGuide);
                    console.log('  - result.eventId:', result.eventId);
                    console.log('  - type:', type);
                    console.log('  - eventData.travelSpotId:', eventData.travelSpotId);
                    console.log('  - currentEditingEventId:', this.currentEditingEventId);
                    
                    // Determine which eventId to use: for edits use currentEditingEventId, for new use result.eventId
                    const eventIdToUse = this.currentEditingEventId || result.eventId;
                    
                    // Always save guide request for travelSpot events (new or edit)
                    // With guide = status 'pending', without guide = status 'notSelected'
                    // IMPORTANT: Only for travelSpot type, not location
                    if (eventIdToUse && type === 'travelSpot' && eventData.travelSpotId) {
                        console.log('===== SAVING GUIDE REQUEST FOR TRAVEL SPOT =====');
                        console.log('  eventId:', eventIdToUse);
                        console.log('  travelSpotId:', eventData.travelSpotId);
                        console.log('  guide selected:', !!this.selectedGuide);
                        console.log('  is editing:', !!this.currentEditingEventId);
                        await this.saveGuideRequest(eventIdToUse, eventData.travelSpotId);
                        console.log('===== GUIDE REQUEST SAVE COMPLETE =====');
                    } else {
                        console.log('===== SKIPPING GUIDE SAVE =====');
                        console.log('  Reason: type=' + type + ', travelSpotId=' + eventData.travelSpotId);
                    }
                    
                    alert(msg);
                    this.closePopup();
                    //this.loadTravelSpotCards();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                // Only reload cards if not in summary view
                if (this.tripSummarySection.style.display !== 'block') {
                    this.loadEventCardsForDate(this.currentSelectedDate);
                }
                //this.submitBtn.disabled = false;
                //this.submitBtn.textContent = 'Add Travel Spot';
                this.resetForm();
            }
        }

        async saveGuideRequest(eventId, travelSpotId) {
            console.log('===== SAVE GUIDE REQUEST FUNCTION CALLED =====');
            console.log('  eventId:', eventId);
            console.log('  travelSpotId:', travelSpotId);
            console.log('  this.selectedGuide:', this.selectedGuide);
            
            try {
                let guideRequestData = {
                    eventId: eventId,
                    tripId: this.tripId.textContent,
                    travelSpotId: travelSpotId
                };

                if (this.selectedGuide) {
                    console.log('  → Guide IS selected, setting status to PENDING');
                    // Guide selected - save with status 'pending'
                    guideRequestData.guideId = this.selectedGuide.guideId;
                    guideRequestData.guideFullName = this.selectedGuide.fullName;
                    guideRequestData.guideProfilePhoto = this.selectedGuide.profilePhoto;
                    guideRequestData.guideAverageRating = this.selectedGuide.averageRating;
                    guideRequestData.guideBio = this.selectedGuide.bio;
                    guideRequestData.totalCharge = this.selectedGuide.convertedCharge || this.selectedGuide.baseCharge || 0;
                    guideRequestData.numberOfPeople = this.numberOfPeople || 1;
                    guideRequestData.chargeType = this.selectedGuide.chargeType || 'perDay';
                    guideRequestData.status = 'pending';
                } else {
                    console.log('  → Guide NOT selected, setting status to NOTSELECTED');
                    // No guide selected - save with status 'notSelected'
                    guideRequestData.guideId = null;
                    guideRequestData.guideFullName = null;
                    guideRequestData.guideProfilePhoto = null;
                    guideRequestData.guideAverageRating = null;
                    guideRequestData.guideBio = null;
                    guideRequestData.totalCharge = 0;
                    guideRequestData.numberOfPeople = this.numberOfPeople || 1;
                    guideRequestData.status = 'notSelected';
                }

                console.log('Sending guide request data:', guideRequestData);

                const response = await fetch(`${this.URL_ROOT}/RegUser/saveGuideRequest`, {
                    method: 'POST',
                    body: JSON.stringify(guideRequestData)
                });

                const result = await response.json();
                console.log('Guide request save result:', result);
                
                if (!result.success) {
                    console.error('Failed to save guide request:', result.message);
                }
            } catch (error) {
                console.error('Error saving guide request:', error);
            }
        }

        async loadEventCardsForDate(eventDate){
            try{
                const response = await fetch(this.URL_ROOT+`/RegUser/getEventCardsByDate/${this.tripId.textContent}/${eventDate}`);
                const data = await response.json();

                if (data.success) {
                    
                    this.eventsContainer.innerHTML = '';
                    
                    for (const card of data.eventCards) {
                        // Fetch guide data for this event if it exists
                        let guideData = null;
                        try {
                            const guideResponse = await fetch(`${this.URL_ROOT}/RegUser/getGuideRequestByEventId/${card.eventId}`);
                            const guideResult = await guideResponse.json();
                            console.log(`Guide data for eventId ${card.eventId}:`, guideResult);
                            if (guideResult.success && guideResult.guideRequest) {
                                guideData = guideResult.guideRequest;
                                console.log(`Guide loaded for event ${card.eventId}:`, guideData);
                            } else {
                                console.log(`No guide for event ${card.eventId}`);
                            }
                        } catch (error) {
                            console.error('Error fetching guide data:', error);
                        }

                        if(card.eventType === 'travelSpot'){
                            const travelSpot = await this.getSpotData(card.travelSpotId);
                            const eventFormData = {
                                eventId: card.eventId,
                                type: card.eventType,
                                status: card.eventStatus,
                                startTime: card.startTime,
                                endTime: card.endTime,
                                guideData: guideData
                            }; 
                            this.eventsContainer.appendChild(this.renderSelectedSpot(travelSpot, false, eventFormData));

                        } else if(card.eventType === 'location') {

                            const locationData = {
                                spotName : card.locationName,
                                description: card.description,
                                averageRating : null,
                                itinerary : [
                                    {latitude: card.latitude, longitude: card.longitude, pointId: null, pointName: card.locationName}
                                ]
                            };
                            const eventFormData = {
                                eventId: card.eventId,
                                type: card.eventType,
                                status: card.eventStatus,
                                startTime: card.startTime,
                                endTime: card.endTime,
                                description: card.description,
                                guideData: guideData
                            };

                            this.eventsContainer.appendChild(this.renderSelectedSpot(locationData, false, eventFormData));
                        } else {
                            console.error('Unknown event type:', card.eventType);
                        }
                    }
                } else {
                    console.error('Failed to load event cards:', data.message);
                    alert('Failed to load event cards: ' + data.message);
                }   
            } catch(error) {
                console.error('Error loading event cards:', error);
                alert('Error loading event cards: ' + error.message);
            } finally {
                // Update the route map after loading events (only if not in summary view)
                if (this.tripSummarySection.style.display !== 'block') {
                    await this.updateRouteMap(eventDate);
                }
            }
        }

        //When the travelSpotsSelect page closes the data would be send to here handleSpotSelection
        async getSpotData(spotId){
            console.log('Selected travel spot ID:', spotId);     
            try{

                const response = await fetch(this.URL_ROOT+`/RegUser/retrieveSelectedSpot/${spotId}`);
                const data = await response.json();

                if (data.success) {
                    console.log(data.spotData);

                    const spotCardData = {
                                            spotId : spotId,
                                            spotName  : data.spotData.mainDetails.spotName,
                                            description: data.spotData.mainDetails.overview,
                                            averageRating : data.spotData.mainDetails.averageRating,
                                            itinerary : data.spotData.itinerary,                
                    };
                    return spotCardData;
                    
                } else {
                    console.error('Failed to load trips:', data.message);
                    alert('Failed to load trips: ' + data.message);
                }

            } catch(error) {
                console.error('Error loading trips:', error);
                alert('Error loading trips: ' + error.message);
            }
        }

        //When a spot selected it will display through this
        async handleSpotSelection(spotId){

            const availableSpotCard = this.selectedSpotContainer.querySelector(".event-card-wrapper");
            if(availableSpotCard) {
                    availableSpotCard.remove();
            }

            this.selectedSpot = await this.getSpotData(spotId);
            this.gotoTravelSpotsElement.style.display = 'none';
            
            const eventFormData = {
                type: this.spotTypeSelect.value,
                status: this.eventStatusSelect.value,
                startTime: this.startTimeInput.value,
                endTime: this.endTimeInput.value
            };
            
            // If there's a selected guide, add it to eventFormData in the format expected by renderGuideSection
            if (this.selectedGuide) {
                eventFormData.guideData = {
                    guideId: this.selectedGuide.guideId,
                    guideFullName: this.selectedGuide.fullName,
                    guideProfilePhoto: this.selectedGuide.profilePhoto,
                    guideAverageRating: this.selectedGuide.averageRating,
                    guideBio: this.selectedGuide.bio,
                    numberOfPeople: this.numberOfPeople || 1,
                    chargeType: this.selectedGuide.chargeType || 'perDay',
                    totalCharge: this.selectedGuide.convertedCharge || this.selectedGuide.baseCharge || 0
                };
            }
            
            this.selectedSpotContainer.appendChild(this.renderSelectedSpot(this.selectedSpot, true, eventFormData));
            //this.selectedSpotContainer.appendChild(this.renderSelectedSpot(this.selectedSpot, true, this.spotTypeSelect.value, this.eventStatusSelect.value));
        }

        // Toggle event menu dropdown
        toggleEventMenu(event, eventId) {
            event.stopPropagation();
            
            // Close all other menus
            document.querySelectorAll('.dot-menu-dropdown.show').forEach(menu => {  
                if(menu.id !== `event-menu-${eventId}`) {
                    menu.classList.remove('show');
                }
            });
         
            // Toggle current menu
            const menu = document.getElementById(`event-menu-${eventId}`);
            menu.classList.toggle('show');
        }

        // Edit event
        async editEvent(tripId, eventId) {
            console.log('Editing event:', eventId);
            try{
                const eventData = await fetch(this.URL_ROOT + `/RegUser/retrieveEventData/${tripId}/${eventId}`);
                const data = await eventData.json(); 
                if(data.success){
                    console.log(data.eventData);
                    this.resetForm();
                    
                    // Set currentEditingEventId AFTER resetForm so it doesn't get cleared
                    this.currentEditingEventId = eventId;
                    console.log('Set currentEditingEventId to:', this.currentEditingEventId);

                    let startHours, startMinutes, endHours, endMinutes;

                    if(data.eventData.eventStatus === 'start'){
                        this.endTimeInput.closest('.form-group').style.display = 'none';
                        [startHours,startMinutes] = data.eventData.startTime.split(':');
                    }

                    if(data.eventData.eventStatus === 'intermediate'){
                        [startHours,startMinutes] = data.eventData.startTime.split(':');
                        [endHours, endMinutes]= data.eventData.endTime.split(':');
                    }

                    if(data.eventData.eventStatus === 'end'){
                        this.startTimeInput.closest('.form-group').style.display = 'none';
                        [endHours, endMinutes]= data.eventData.endTime.split(':');
                    }


                    
                    this.startTimePicker.setDate(`${startHours}:${startMinutes}`);
                    this.endTimePicker.setDate(`${endHours}:${endMinutes}`)

                    this.spotTypeSelect.value = data.eventData.eventType;
                    this.eventStatusSelect.value = data.eventData.eventStatus;

                    if(data.eventData.eventType ==='location'){
                        this.locationDescription.value = data.eventData.description;
                        this.locationSelect.classList.add('active');
                        
                        // Initialize map after showing location container
                        setTimeout(async () => {
                            await this.initMap();
                            
                            // After map is initialized, add marker with location data
                            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
                            
                            const position = { 
                                lat: parseFloat(data.eventData.latitude), 
                                lng: parseFloat(data.eventData.longitude) 
                            };

                            this.marker = new AdvancedMarkerElement({
                                map: this.mapElement,
                                position: position,
                                title: data.eventData.locationName,
                            });
                            
                            // Center map on marker
                            this.mapElement.setCenter(position);
                            this.mapElement.setZoom(17);
                            
                            // Store the selected location
                            this.selectedLocation = {
                                spotName: data.eventData.locationName,
                                description: data.eventData.description,
                                averageRating : null,
                                itinerary : [{latitude: parseFloat(data.eventData.latitude), longitude: parseFloat(data.eventData.longitude), pointId: null, pointName: data.eventData.locationName}]
                            };

                            this.handleLocationSelection(this.selectedLocation);
                            this.locationInputContainer.value = '';
                        }, 100);

                    } else if(data.eventData.eventType === 'travelSpot'){
                        this.travelSpotSelect.classList.add('active');
                        
                        // Load guide data for this event FIRST
                        let guideData = null;
                        try {
                            const guideResponse = await fetch(`${this.URL_ROOT}/RegUser/getGuideRequestByEventId/${eventId}`);
                            const guideResult = await guideResponse.json();
                            console.log('Loaded guide data for editing:', guideResult);
                            
                            if (guideResult.success && guideResult.guideRequest && guideResult.guideRequest.guideId) {
                                // Reconstruct guide data from the saved request
                                this.selectedGuide = {
                                    guideId: guideResult.guideRequest.guideId,
                                    fullName: guideResult.guideRequest.guideFullName,
                                    profilePhoto: guideResult.guideRequest.guideProfilePhoto,
                                    averageRating: guideResult.guideRequest.guideAverageRating,
                                    bio: guideResult.guideRequest.guideBio,
                                    convertedCharge: guideResult.guideRequest.totalCharge,
                                    baseCharge: guideResult.guideRequest.totalCharge,
                                    chargeType: 'per_day',
                                    currency: 'USD',
                                    currencySymbol: '$'
                                };
                                console.log('Set this.selectedGuide for editing:', this.selectedGuide);
                            } else {
                                console.log('No guide found for this event');
                            }
                        } catch (error) {
                            console.error('Error loading guide data for edit:', error);
                        }
                        
                        // Now handle spot selection - it will use this.selectedGuide
                        await this.handleSpotSelection(data.eventData.travelSpotId);
                    }
                    this.addTravelSpotPopup.classList.add('show');
                    
                }
            } catch(error) {
                console.error('Error fetching event data:', error);
                alert('Error fetching event data: ' + error.message);
            }

        }

        // Delete event
        deleteEvent(tripId, eventId) {
            console.log('Deleting event:', eventId, `From trip: ${tripId}`);
            if (confirm('Are you sure you want to delete this event?')) {
                
                // Make delete request
                fetch(this.URL_ROOT + '/RegUser/deleteEvent', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ tripId: tripId, eventId: eventId })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Travel Spot deleted successfully!');
                        // Reload cards after deletion (unless in summary view)
                        if (this.tripSummarySection.style.display !== 'block') {
                            this.loadEventCardsForDate(this.currentSelectedDate);
                        }
                    } else {
                        alert('Error deleting Travel Spot: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`An error occurred while deleting the event from day - ${this.currentSelectedDate}.`);
                });
            }
        }

        async addEventAbove(tripId,selectedEventId,selectedEventStartTime){
            console.log("add event above ", selectedEventStartTime);
            
            this.resetForm();
            
            try {
                const aboveEventDetails = await fetch(this.URL_ROOT + `/RegUser/retrieveAboveEventEndTime/${tripId}/${selectedEventId}/${this.currentSelectedDate}`);
                const data = await aboveEventDetails.json();

                if(data.success && data.eventData){
                    console.log("Above event data:", data.eventData);
                    
                    // Extract the end time of the above event (24-hour format from database)
                    const aboveEndTime = data.eventData.endTime;
                    const [aboveEndHours, aboveEndMinutes] = aboveEndTime.split(':');
                    
                    // Set minimum start time to the above event's end time
                    this.startTimePicker.set('minTime', `${aboveEndHours}:${aboveEndMinutes}`);
                    this.endTimePicker.set('minTime', `${aboveEndHours}:${aboveEndMinutes}`);
                    
                } else {
                    console.log("No above event found, setting min time to 00:00");
                    // No event above, so allow any start time
                    this.startTimePicker.set('minTime', '00:00');
                }
                
                const [selectedStartHours, selectedStartMinutes] = selectedEventStartTime.split(':');
                // Set maximum end time to the selected event's start time (already in 24-hour format)
                this.endTimePicker.set('maxTime', `${selectedStartHours}:${selectedStartMinutes}`);
                this.startTimePicker.set('maxTime', `${selectedStartHours}:${selectedStartMinutes}`);
                
                this.addTravelSpotPopup.classList.add('show');

            } catch (error) {
                console.error('Error fetching above event data:', error);
                alert('Error loading event data. Please try again.');
            }
        }

        async addEventBelow(tripId,selectedEventId,selectedEventEndTime){

            console.log("add event below ");
            this.resetForm();
            
            try {

                const belowEventDetails = await fetch(this.URL_ROOT + `/RegUser/retrieveBelowEventStartTime/${tripId}/${selectedEventId}/${this.currentSelectedDate}`);
                const data = await belowEventDetails.json();

                if(data.success && data.eventData){
                    console.log("Below event data:", data.eventData);
                    // Extract the start time of the below event (24-hour format from database)
                    const belowStartTime = data.eventData.startTime;
                    const [belowStartHours, belowStartMinutes] = belowStartTime.split(':'); 
                    // Set maximum start time to the below event's start time
                    this.startTimePicker.set('maxTime', `${belowStartHours}:${belowStartMinutes}`);
                    this.endTimePicker.set('maxTime', `${belowStartHours}:${belowStartMinutes}`);
                } else {
                    console.log("No below event found, setting max time to 23:59");
                    // No event below, so allow any end time
                    this.endTimePicker.set('maxTime', '23:59');
                }

                // Set minimum start time to the selected event's end time (already in 24-hour format)
                const [selectedEndHours, selectedEndMinutes] = selectedEventEndTime.split(':');
                this.startTimePicker.set('minTime', `${selectedEndHours}:${selectedEndMinutes}`);
                this.endTimePicker.set('minTime', `${selectedEndHours}:${selectedEndMinutes}`);

                this.addTravelSpotPopup.classList.add('show');

            } catch (error) {
                console.error('Error fetching below event data:', error);
                alert('Error loading event data. Please try again.');
            }
        }

        //for render a selected spot or a location (follow the spot object data pattern)
        renderSelectedSpot(spot ,isPopup, eventFormData){

            const selectedType = eventFormData.type; // 'travelSpot' or 'location'
            const selectedStatus = eventFormData.status || 'Intermediate'; // 'checking', 'normal', or 'checkout'
            
            // Determine badge text and class based on type
            const typeConfig = {
                'travelSpot': {
                    badge: 'Travel Spot',
                    class: 'type-travelspot',
                    icon: 'fas fa-map-marked-alt'
                },
                'location': {
                    badge: 'Location',
                    class: 'type-location',
                    icon: 'fas fa-map-marker-alt'
                }
            };

            // Determine status badge text and class
            const statusConfig = {
                'start': {
                    badge: 'Start',
                    class: 'status-checking'
                },
                'intermediate': {
                    badge: 'Intermediate',
                    class: 'status-normal'
                },
                'end': {
                    badge: 'End',
                    class: 'status-checkout'
                }
            };

            const currentType = typeConfig[selectedType] || typeConfig['travelSpot'];
            const currentStatus = statusConfig[selectedStatus] || statusConfig['intermediate'];

            const wrapper = document.createElement('div');
            wrapper.className = 'event-card-wrapper';
            
            const card = document.createElement('div');
            card.className = 'event-card';
            card.dataset.type = selectedType;
            card.dataset.status = selectedStatus;
            card.innerHTML = `
                                <div class="event-time-section">
                                    <div class="time-label">START</div>

                                    <div class="event-start-time">${this.escapeHtml(isPopup ? eventFormData.startTime : this.formatTimeToAMPM(eventFormData.startTime))}</div>
                                    <div class="time-label">END</div>
                                    <div class="event-end-time">${this.escapeHtml(isPopup ? eventFormData.endTime : this.formatTimeToAMPM(eventFormData.endTime))}</div>
                                </div>
                                <div class="event-image">
                                    <i class="${currentType.icon}"></i>
                                </div>
                                <div class="event-content">
                                    <div class="event-header">
                                        <div>
                                            <h4 class="event-title">${this.escapeHtml(spot.spotName)}</h4>
                                        </div>
                                        <div class="event-header-actions">
                                            <div class="event-badges">
                                                <span class="event-type-badge ${currentType.class}">${currentType.badge}</span>
                                                <span class="event-status-badge ${currentStatus.class}">${currentStatus.badge}</span>
                                            </div>

                                            ${!isPopup ? `
                                                <div class="dot-menu-container">
                                                    <button class="dot-menu-btn" onclick="tripEventListManager.toggleEventMenu(event, ${eventFormData.eventId})">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dot-menu-dropdown" id="event-menu-${eventFormData.eventId}">
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.addEventAbove(${this.tripId.textContent},${eventFormData.eventId},'${eventFormData.startTime}')">
                                                            <i class="fa-solid fa-arrow-up"></i> Add event above
                                                        </button>
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.addEventBelow(${this.tripId.textContent},${eventFormData.eventId},'${eventFormData.endTime}')">
                                                            <i class="fa-solid fa-arrow-down"></i> Add event below
                                                        </button>
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.editEvent(${this.tripId.textContent},${eventFormData.eventId})">
                                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                                        </button>
                                                        <button class="dot-menu-item delete" onclick="tripEventListManager.deleteEvent(${this.tripId.textContent},${eventFormData.eventId})">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                ` : ''}  
                                        </div>
                                    </div>
                                    <p class="event-description">
                                        ${this.escapeHtml(selectedType === 'location'? eventFormData.description : spot.description)}
                                    </p>
                                    <div class="event-details">
                                        <div class="event-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>${this.escapeHtml( 'Location not specified')}</span>
                                        </div>
                                        <div class="event-detail">
                                            <i class="fas fa-star"></i>
                                            <span>${this.escapeHtml(spot.averageRating || 'N/A')}/5</span>
                                        </div>
                                    </div>
                                    ${(selectedType==='travelSpot') ? `
                                        ${(isPopup && !this.selectedGuide) ? `
                                            <div class="guide-section" id="selected-guide-section-pop">
                                                <div class="guide-status">
                                                    <i class="fas fa-user-plus"></i>
                                                    <span class="guide-status-text">No Guide Selected</span>
                                                </div>
                                                <button class="add-guide-btn" onclick="window.tripEventListManager.openGuideSelection()">
                                                    <i class="fas fa-plus"></i>
                                                    Add Guide
                                                </button>
                                            </div>
                                        ` : `
                                            <div class="guide-section" id="selected-guide-section">
                                                ${this.renderGuideSection(eventFormData.guideData, isPopup, spot.spotId)}
                                            </div>
                                            `
                                        }
                                    ` : ''}
                                    
                                </div>
                            `;
            
            wrapper.appendChild(card);
            if (isPopup) {
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-card-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
                removeBtn.onclick = () => { wrapper.remove();
                    if(eventFormData.type === 'travelSpot'){
                        this.selectedGuide = null; 
                        this.gotoTravelSpotsElement.style.display = 'block'; 
                    } else{
                        document.getElementById('autocomplete-container').style.display = 'block';
                    }
                };
                wrapper.appendChild(removeBtn);
            } 
            return wrapper;
        }

        renderGuideSection(guideData, isPopup, spotId) {
            if (guideData && guideData.guideId) {
                // Guide is selected - display guide info
                const rating = parseFloat(guideData.guideAverageRating) || 0;
                const fullStars = Math.floor(rating);
                const hasHalfStar = rating % 1 >= 0.5;
                const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                let starsHtml = '';
                for (let i = 0; i < fullStars; i++) {
                    starsHtml += '<i class="fas fa-star"></i>';
                }
                if (hasHalfStar) {
                    starsHtml += '<i class="fas fa-star-half-alt"></i>';
                }
                for (let i = 0; i < emptyStars; i++) {
                    starsHtml += '<i class="far fa-star"></i>';
                }

                return `
                    <div class="guide-info-card">
                        <div class="guide-avatar">
                            <img src="${guideData.guideProfilePhoto || '/public/img/signup/profile.png'}" 
                                 alt="${this.escapeHtml(guideData.guideFullName)}" 
                                 onerror="this.src='/public/img/signup/profile.png'">
                        </div>
                        <div class="guide-details">
                            <div class="guide-header">
                                <div class="guide-header-left">
                                    <div class="guide-name">${this.escapeHtml(guideData.guideFullName)}</div>
                                    <div class="guide-rating">
                                        <div class="stars">${starsHtml}</div>
                                        <span class="rating-value">${rating.toFixed(1)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="guide-charge">
                                <span class="charge-label">Fee:</span>
                                <span class="charge-value">$${parseFloat(guideData.totalCharge || 0).toFixed(2)}</span>
                            </div>
                        </div>
                        ${isPopup ? `
                            <div class="guide-actions">
                                <button class="change-guide-btn" onclick="window.open('${this.URL_ROOT}/RegUser/guidesSelect/${spotId}', '_blank')" title="Change Guide">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
            } else {
                // No guide selected
                return `
                    <div class="guide-info guide-none">
                        <i class="fas fa-user-slash"></i>
                        <span>No guide added</span>
                    </div>
                    ${isPopup ? `
                        <button class="guide-booking-btn" onclick="window.open('${this.URL_ROOT}/RegUser/guidesSelect/${spotId}', '_blank')">
                            <i class="fas fa-plus"></i>
                            Add Guide
                        </button>
                    ` : ''}
                `;
            }
        }
        
        handleLocationSelection(locationData){

            const availableLocationCard = this.selectedLocationContainer.querySelector(".event-card-wrapper");
            if(availableLocationCard) {
                    availableLocationCard.remove();
            }
            const eventFormData = {
                type: this.spotTypeSelect.value,
                status: this.eventStatusSelect.value,
                startTime: this.startTimeInput.value,
                endTime: this.endTimeInput.value,
                description: this.locationDescription.value
            };
            this.selectedLocationContainer.appendChild(this.renderSelectedSpot(locationData, true, eventFormData));
            document.getElementById('autocomplete-container').style.display = 'none';
        }

        validateInput() {
            const startTime = this.startTimePicker.selectedDates[0];
            const endTime = this.endTimePicker.selectedDates[0];
            const type = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            const locationDescription = this.locationDescription.value;

            // Validate time based on event status
            if (eventStatus === 'start') {
                // Start event: only start time required
                if(!startTime){
                    alert("Please fill in Start time");
                    return false;
                }
            } else if (eventStatus === 'end') {
                // End event: only end time required
                if(!endTime){
                    alert("Please fill in End time");
                    return false;
                }
            } else {
                // Intermediate: both times required
                if(!startTime){
                    alert("Please fill in Start time");
                    return false;
                }
                if(!endTime){
                    alert("Please fill in End time");
                    return false;
                }
                // Validate time order
                if(endTime < startTime){
                    alert("End time must be later than start time");
                    return false;
                }
            }

            if(!type){
                alert("Please fill in location spot type");
                return false;
            }
            if(!eventStatus){
                alert("Please fill in location event status");
                return false;
            }

            if(type === 'location'){
                if(!locationDescription){
                    alert("Please fill in location description");
                    return false;
                }

                if(!this.selectedLocation){
                    alert("Please select a location");
                    return false;
                }
            }

            if(type ==='travelSpot'){
                if(!this.selectedSpot){
                    alert("Please select a travel spot");
                    return false;
                }
            }  
            console.log("validate input working");
            return true;
        }

        // Initialize the route map
        async initializeRouteMap() {
            try {
                // Check if the new importLibrary method is available
                if (google.maps.importLibrary) {
                    const { Map } = await google.maps.importLibrary("maps");
                    
                    this.routeMap = new Map(document.getElementById("route-map"), {
                        center: { lat: 7.8731, lng: 80.7718 }, // Center of Sri Lanka
                        zoom: 8,
                        mapId: "route_map_id"
                    });
                } else {
                    // Fallback to traditional Google Maps initialization
                    this.routeMap = new google.maps.Map(document.getElementById("route-map"), {
                        center: { lat: 7.8731, lng: 80.7718 }, // Center of Sri Lanka
                        zoom: 8
                    });
                }

                this.directionsService = new google.maps.DirectionsService();
                this.directionsRenderer = new google.maps.DirectionsRenderer({
                    map: this.routeMap,
                    suppressMarkers: true, // We'll add custom markers
                    polylineOptions: {
                        strokeColor: '#006a71',
                        strokeWeight: 4,
                        strokeOpacity: 0.7
                    }
                });

                console.log('Route map initialized successfully');
            } catch (error) {
                console.error('Error initializing route map:', error);
            }
        }

        // Update the route map with event coordinates
        async updateRouteMap(eventDate) {
            if (!this.routeMap) {
                console.warn('Route map not initialized');
                return;
            }

            try {
                // Fetch coordinates for events on this date
                const response = await fetch(`${this.URL_ROOT}/RegUser/getEventCoordinates/${this.tripId.textContent}/${eventDate}`);
                const data = await response.json();

                if (!data.success) {
                    console.error('Failed to fetch coordinates:', data.message);
                    return;
                }

                const coordinates = data.coordinates;
                console.log('Fetched coordinates:', coordinates);

                // Clear existing markers
                this.clearRouteMarkers();

                if (coordinates.length === 0) {
                    console.log('No coordinates to display');
                    return;
                }

                // Create waypoints for directions
                if (coordinates.length >= 2) {
                    await this.renderDirections(coordinates);
                } else if (coordinates.length === 1) {
                    // Single location - just add a marker
                    await this.addSingleMarker(coordinates[0]);
                }

            } catch (error) {
                console.error('Error updating route map:', error);
            }
        }

        // Render directions with waypoints
        async renderDirections(coordinates) {
            
            // Origin is the first coordinate
            const origin = { lat: coordinates[0].lat, lng: coordinates[0].lng };
            
            // Destination is the last coordinate
            const destination = { 
                lat: coordinates[coordinates.length - 1].lat, 
                lng: coordinates[coordinates.length - 1].lng 
            };

            // Waypoints are everything in between
            const waypoints = coordinates.slice(1, -1).map(coord => ({
                location: { lat: coord.lat, lng: coord.lng },
                stopover: true
            }));

            const request = {
                origin: origin,
                destination: destination,
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING,
                optimizeWaypoints: false // Keep the order as scheduled
            };

            try {
                const result = await this.directionsService.route(request);
                this.directionsRenderer.setDirections(result);

                // Add custom markers for each location
                coordinates.forEach((coord, index) => {
                    const marker = new google.maps.Marker({
                        map: this.routeMap,
                        position: { lat: coord.lat, lng: coord.lng },
                        label: {
                            text: String(index + 1),
                            color: 'white',
                            fontWeight: 'bold'
                        },
                        title: coord.name,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#006a71',
                            fillOpacity: 1,
                            strokeColor: 'white',
                            strokeWeight: 3
                        }
                    });

                    // Add info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(this.routeMap, marker);
                    });

                    this.routeMarkers.push(marker);
                });

            } catch (error) {
                console.error('Error rendering directions:', error);
                // Fallback: just show markers with polyline
                this.renderMarkersWithPolyline(coordinates);
            }
        }

        // Fallback: render markers with a simple polyline
        async renderMarkersWithPolyline(coordinates) {
            
            // Add markers
            coordinates.forEach((coord, index) => {
                const marker = new google.maps.Marker({
                    map: this.routeMap,
                    position: { lat: coord.lat, lng: coord.lng },
                    label: {
                        text: String(index + 1),
                        color: 'white',
                        fontWeight: 'bold'
                    },
                    title: coord.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 12,
                        fillColor: '#006a71',
                        fillOpacity: 1,
                        strokeColor: 'white',
                        strokeWeight: 3
                    }
                });

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
                });

                marker.addListener('click', () => {
                    infoWindow.open(this.routeMap, marker);
                });

                this.routeMarkers.push(marker);
            });

            // Draw polyline
            const path = coordinates.map(coord => ({ lat: coord.lat, lng: coord.lng }));
            
            this.routePath = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: '#006a71',
                strokeOpacity: 0.7,
                strokeWeight: 4,
                map: this.routeMap
            });

            // Fit bounds to show all markers
            const bounds = new google.maps.LatLngBounds();
            coordinates.forEach(coord => {
                bounds.extend({ lat: coord.lat, lng: coord.lng });
            });
            this.routeMap.fitBounds(bounds);
        }

        // Add a single marker
        async addSingleMarker(coord) {
            
            const marker = new google.maps.Marker({
                map: this.routeMap,
                position: { lat: coord.lat, lng: coord.lng },
                label: {
                    text: '1',
                    color: 'white',
                    fontWeight: 'bold'
                },
                title: coord.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 12,
                    fillColor: '#006a71',
                    fillOpacity: 1,
                    strokeColor: 'white',
                    strokeWeight: 3
                }
            });

            // Add info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(this.routeMap, marker);
            });

            this.routeMarkers.push(marker);
            
            // Center map on this marker
            this.routeMap.setCenter({ lat: coord.lat, lng: coord.lng });
            this.routeMap.setZoom(12);
        }

        // Clear all route markers and paths
        clearRouteMarkers() {
            // Clear markers
            this.routeMarkers.forEach(marker => {
                marker.setMap(null);
            });
            this.routeMarkers = [];

            // Clear polyline
            if (this.routePath) {
                this.routePath.setMap(null);
                this.routePath = null;
            }

            // Clear directions
            if (this.directionsRenderer) {
                this.directionsRenderer.setDirections({ routes: [] });
            }
        }

        // Show trip summary for finalization
        async showTripSummary() {
            try {
                // Remove active class from all date navigation items
                document.querySelectorAll('.date-nav-item').forEach(i => i.classList.remove('active'));
                
                // Hide events section, show summary section
                this.eventsSection.style.display = 'none';
                this.tripSummarySection.style.display = 'block';
                this.eventsMapContainer.classList.add('summary-active');
                
                // Clear inline style on route map to let CSS control it
                const routeMapSection = document.querySelector('.route-map-section');
                routeMapSection.style.display = '';
                
                // Load all trip events
                const response = await fetch(`${this.URL_ROOT}/RegUser/getAllTripEvents/${this.tripId.textContent}`);
                const data = await response.json();
                
                if (!data.success) {
                    alert('Failed to load trip summary: ' + data.message);
                    return;
                }
                
                // Group events by date and collect charges
                const eventsByDate = {};
                let totalEvents = 0;
                let totalLocations = 0;
                const guideCharges = [];
                
                for (const event of data.events) {
                    const date = event.eventDate;
                    if (!eventsByDate[date]) {
                        eventsByDate[date] = [];
                    }
                    
                    // Get full event details
                    let eventDetails = {
                        eventId: event.eventId,
                        startTime: event.startTime,
                        endTime: event.endTime,
                        eventType: event.eventType,
                        eventStatus: event.eventStatus
                    };
                    
                    if (event.eventType === 'location') {
                        eventDetails.name = event.locationName;
                        eventDetails.description = event.description;
                        totalLocations++;
                    } else if (event.eventType === 'travelSpot') {
                        const spotData = await this.getSpotData(event.travelSpotId);
                        eventDetails.name = spotData.spotName;
                        eventDetails.description = spotData.description;
                        
                        // Get guide data if exists
                        try {
                            const guideResponse = await fetch(`${this.URL_ROOT}/RegUser/getGuideRequestByEventId/${event.eventId}`);
                            const guideResult = await guideResponse.json();
                            if (guideResult.success && guideResult.guideRequest) {
                                eventDetails.guideData = guideResult.guideRequest;
                                // Collect guide charge for display
                                guideCharges.push(guideResult.guideRequest);
                            }
                        } catch (error) {
                            console.error('Error fetching guide data:', error);
                        }
                        
                        totalLocations++;
                    }
                    
                    eventsByDate[date].push(eventDetails);
                    totalEvents++;
                }
                
                // Render summary
                this.renderTripSummary(eventsByDate);
                
                // Display start and end events
                await this.displayStartEndEvents();
                
                // Update stats
                document.getElementById('total-days').textContent = `${Object.keys(eventsByDate).length} Days`;
                document.getElementById('total-events').textContent = `${totalEvents} Events`;
                document.getElementById('total-spots').textContent = `${totalLocations} Locations`;
                
                // Render all trip locations on map
                await this.renderAllTripLocationsOnMap();
                
            } catch (error) {
                console.error('Error loading trip summary:', error);
                alert('Error loading trip summary: ' + error.message);
            }
        }
        
        // Render all trip locations on the map
        async renderAllTripLocationsOnMap() {
            try {
                console.log('=== Rendering all trip locations on map ===');
                console.log('Trip ID:', this.tripId.textContent);
                
                // Fetch all event coordinates for the entire trip (all dates)
                const response = await fetch(`${this.URL_ROOT}/RegUser/getAllTripCoordinates/${this.tripId.textContent}`);
                const data = await response.json();
                
                console.log('All trip coordinates response:', data);
                
                if (!data.success || !data.coordinates || data.coordinates.length === 0) {
                    console.log('No coordinates to display on map');
                    return;
                }
                
                console.log(`Found ${data.coordinates.length} coordinates to display`);
                
                // Clear existing markers and paths
                this.clearRouteMarkers();
                
                const coordinates = data.coordinates;
                
                // Create waypoints for directions (using the same method as daily view)
                if (coordinates.length >= 2) {
                    console.log('Rendering directions for all trip locations');
                    await this.renderDirections(coordinates);
                } else if (coordinates.length === 1) {
                    console.log('Rendering single marker');
                    await this.addSingleMarker(coordinates[0]);
                }
                
                console.log('=== Finished rendering all trip locations ===');
                
            } catch (error) {
                console.error('Error rendering trip locations on map:', error);
            }
        }
        
        // Render trip summary HTML
        renderTripSummary(eventsByDate) {
            let summaryHTML = '';
            
            // Sort dates
            const sortedDates = Object.keys(eventsByDate).sort();
            
            sortedDates.forEach((date, index) => {
                const events = eventsByDate[date];
                const dateObj = new Date(date);
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                summaryHTML += `
                    <div class="summary-day-section">
                        <div class="summary-day-header" onclick="tripEventListManager.toggleDaySummary(${index})">
                            <div class="summary-day-title">
                                <i class="fas fa-calendar-day"></i>
                                <span>Day ${index + 1}: ${dayName}, ${formattedDate}</span>
                            </div>
                            <div>
                                <span class="summary-day-count">${events.length} event${events.length !== 1 ? 's' : ''}</span>
                                <i class="fas fa-chevron-down summary-day-toggle" id="toggle-${index}"></i>
                            </div>
                        </div>
                        <div class="summary-day-events" id="day-events-${index}">
                            ${this.renderDayEvents(events)}
                        </div>
                    </div>
                `;
            });
            
            this.summaryContent.innerHTML = summaryHTML;
        }
        
        // Render events for a specific day
        renderDayEvents(events) {
            return events.map(event => {
                const startTime = this.formatTimeToAMPM(event.startTime);
                const endTime = this.formatTimeToAMPM(event.endTime);
                
                const typeIcon = event.eventType === 'travelSpot' ? 'fa-map-marked-alt' : 'fa-map-marker-alt';
                const statusBadge = {
                    'start': 'Start Point',
                    'intermediate': 'Intermediate',
                    'end': 'End Point'
                }[event.eventStatus] || event.eventStatus;
                
                // Build guide info HTML if travel spot has guide
                let guideInfoHTML = '';
                if (event.eventType === 'travelSpot' && event.guideData) {
                    const guide = event.guideData;
                    if (guide.status === 'pending' || guide.status === 'accepted') {
                        const statusText = guide.status === 'pending' ? 'Pending' : 'Accepted';
                        const statusClass = guide.status === 'pending' ? 'guide-pending' : 'guide-accepted';
                        guideInfoHTML = `
                            <div class="summary-guide-info">
                                <div class="summary-guide-header">
                                    <i class="fas fa-user-tie"></i>
                                    <strong>Guide:</strong> ${this.escapeHtml(guide.guideFullName)}
                                    <span class="guide-status ${statusClass}">${statusText}</span>
                                </div>
                                <div class="summary-guide-price">
                                    <i class="fas fa-dollar-sign"></i>
                                    <strong>Price:</strong> ${parseFloat(guide.totalCharge).toFixed(2)} LKR
                                </div>
                            </div>
                        `;
                    } else if (guide.status === 'notSelected') {
                        guideInfoHTML = `
                            <div class="summary-guide-info">
                                <div class="summary-guide-header">
                                    <i class="fas fa-user-slash"></i>
                                    <span class="guide-status guide-not-selected">No Guide Selected</span>
                                </div>
                            </div>
                        `;
                    }
                }
                
                return `
                    <div class="summary-event-item">
                        <div class="summary-event-time">
                            ${startTime}<br>to<br>${endTime}
                        </div>
                        <div class="summary-event-details">
                            <div class="summary-event-title">${this.escapeHtml(event.name)}</div>
                            <div class="summary-event-meta">
                                <span><i class="fas ${typeIcon}"></i> ${event.eventType === 'travelSpot' ? 'Travel Spot' : 'Location'}</span>
                                <span><i class="fas fa-flag"></i> ${statusBadge}</span>
                            </div>
                            ${guideInfoHTML}
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Toggle day summary collapse
        toggleDaySummary(dayIndex) {
            const eventsDiv = document.getElementById(`day-events-${dayIndex}`);
            const toggleIcon = document.getElementById(`toggle-${dayIndex}`);
            
            if (eventsDiv.classList.contains('collapsed')) {
                eventsDiv.classList.remove('collapsed');
                toggleIcon.classList.remove('collapsed');
            } else {
                eventsDiv.classList.add('collapsed');
                toggleIcon.classList.add('collapsed');
            }
        }
        
        // Hide trip summary
        hideTripSummary() {
            this.tripSummarySection.style.display = 'none';
            this.eventsSection.style.display = 'block';
            this.eventsMapContainer.classList.remove('summary-active');
        }
        
        // Confirm trip
        async confirmTrip() {
            if (!confirm('Are you sure you want to confirm this trip? Once confirmed, the trip status will be updated.')) {
                return;
            }
            
            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/confirmTrip/${this.tripId.textContent}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Trip confirmed successfully!');
                    this.hideTripSummary();
                    // Optionally redirect or update UI
                } else {
                    alert('Error confirming trip: ' + result.message);
                }
            } catch (error) {
                console.error('Error confirming trip:', error);
                alert('Error confirming trip: ' + error.message);
            }
        }

        // Display start and end events in footer
        async displayStartEndEvents() {
            const container = this.summaryStartEndContainer;
            let eventsHTML = '';

            try {
                // Fetch start and end events for this trip
                const response = await fetch(`${this.URL_ROOT}/RegUser/getTripStartEndEvents/${this.tripId.textContent}`);
                const data = await response.json();

                console.log("start end events data:", data);

                if (data.success && data.events && data.events.length > 0) {
                    // Sort events by date and time
                    // Sort by date, then by the actual time for the event (startTime for start events, endTime for end events)
                    const sortedEvents = data.events.sort((a, b) => {
                        const dateCompare = new Date(a.eventDate) - new Date(b.eventDate);
                        if (dateCompare !== 0) return dateCompare;
                        const timeA = (a.startTime && a.startTime !== 'null') ? a.startTime : ((a.endTime && a.endTime !== 'null') ? a.endTime : '00:00:00');
                        const timeB = (b.startTime && b.startTime !== 'null') ? b.startTime : ((b.endTime && b.endTime !== 'null') ? b.endTime : '00:00:00');
                        return timeA.localeCompare(timeB);
                    });

                    // Group events into segments (start to end pairs)
                    const segments = [];
                    let currentSegment = null;

                    sortedEvents.forEach(event => {
                        if (event.eventStatus === 'start') {
                            currentSegment = {
                                start: event,
                                end: null,
                                driver: null
                            };
                            segments.push(currentSegment);
                        } else if (event.eventStatus === 'end' && currentSegment) {
                            currentSegment.end = event;
                        }
                    });

                    // Create professional table structure
                    eventsHTML = `
                        <div class="trip-segments-table-container">
                            <table class="trip-segments-table">
                                <thead>
                                    <tr>
                                        <th class="segment-col">Segment</th>
                                        <th class="start-col">Start Location & Time</th>
                                        <th class="end-col">End Location & Time</th>
                                        <th class="driver-col">Driver</th>
                                        <th class="vehicle-col">Vehicle</th>
                                        <th class="fees-col">Fees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${segments.map((segment, index) => {
                                        const startEvent = segment.start;
                                        const endEvent = segment.end;

                                        const startDate = new Date(startEvent.eventDate).toLocaleDateString('en-US', {
                                            weekday: 'short',
                                            month: 'short',
                                            day: 'numeric',
                                            year: 'numeric'
                                        });

                                        const endDate = endEvent ? new Date(endEvent.eventDate).toLocaleDateString('en-US', {
                                            weekday: 'short',
                                            month: 'short',
                                            day: 'numeric',
                                            year: 'numeric'
                                        }) : startDate;

                                        const startLocation = startEvent.eventType === 'travelSpot' ? 
                                            (startEvent.travelSpotName || 'Unknown Spot') : 
                                            (startEvent.locationName || 'Custom Location');

                                        const endLocation = endEvent ? (endEvent.eventType === 'travelSpot' ? 
                                            (endEvent.travelSpotName || 'Unknown Spot') : 
                                            (endEvent.locationName || 'Custom Location')) : 'Not specified';

                                        // Start events only have startTime, End events only have endTime
                                        const startTime = (startEvent && startEvent.startTime) ? this.formatTime(startEvent.startTime) : '-';
                                        const endTime = (endEvent && endEvent.endTime) ? this.formatTime(endEvent.endTime) : '-';

                                        // Get actual driver data from selectedDrivers
                                        const driverData = this.selectedDrivers && this.selectedDrivers[index] ? this.selectedDrivers[index] : null;
                                        
                                        console.log(`Driver data for segment ${index}--------------------------:`, this.selectedDrivers);

                                        const driverInfo = driverData ? {
                                            name: driverData.fullName,
                                            profilePhoto: driverData.profilePhoto,
                                            rating: parseFloat(driverData.averageRating) || 0,
                                            verified: driverData.verified,
                                            status: 'selected'
                                        } : null;

                                        const vehicleInfo = driverData ? {
                                            vehicleId: driverData.vehicleId,
                                            model: `${driverData.make} ${driverData.model}`,
                                            year: driverData.year,
                                            type: driverData.vehicleType,
                                            vehiclePhoto: driverData.vehiclePhoto,
                                            capacity: `${driverData.seatingCapacity} passengers`,
                                            childSeats: driverData.childSeats,
                                            status: 'selected'
                                        } : null;

                                        // Calculate fees based on actual driver data
                                        let totalFees = 0;
                                        let feeBreakdown = [];

                                        if (driverData) {
                                            const driverFee = parseFloat(driverData.totalChargePerDay) || 0;
                                            const kmFee = parseFloat(driverData.totalChargePerKm) || 0;
                                            feeBreakdown.push({ 
                                                type: 'Per Day', 
                                                amount: driverFee,
                                                formatted: driverData.formattedChargePerDay || `${driverData.currencySymbol}${driverFee.toFixed(2)}`
                                            });
                                            feeBreakdown.push({ 
                                                type: 'Per Km', 
                                                amount: kmFee,
                                                formatted: driverData.formattedChargePerKm || `${driverData.currencySymbol}${kmFee.toFixed(2)}`
                                            });
                                            totalFees = driverFee; // Show per-day charge in total
                                        }

                                        return `
                                            <tr class="segment-row">
                                                <td class="segment-cell">
                                                    <div class="segment-number">
                                                        <span>${index + 1}</span>
                                                    </div>
                                                </td>
                                                <td class="start-cell">
                                                    <div class="location-info">
                                                        <div class="location-name">${this.escapeHtml(startLocation)}</div>
                                                        <div class="location-details">
                                                            <span class="date">${startDate}</span>
                                                            <span class="time">${startTime}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="end-cell">
                                                    <div class="location-info">
                                                        <div class="location-name">${this.escapeHtml(endLocation)}</div>
                                                        <div class="location-details">
                                                            <span class="date">${endDate}</span>
                                                            <span class="time">${endTime}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="driver-cell">
                                                    ${driverInfo ? `
                                                        <div class="driver-selected">
                                                            <div class="driver-avatar">
                                                                <img src="${driverInfo.profilePhoto || '/public/img/signup/profile.png'}" alt="${this.escapeHtml(driverInfo.name)}" onerror="this.src='/public/img/signup/profile.png'">
                                                            </div>
                                                            <div class="driver-info">
                                                                <div class="driver-name">${this.escapeHtml(driverInfo.name)}</div>
                                                                <div class="driver-rating">
                                                                    <i class="fas fa-star"></i>
                                                                    <span>${driverInfo.rating.toFixed(1)}</span>
                                                                </div>
                                                                ${driverInfo.verified ? '<span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>' : ''}
                                                            </div>
                                                            <button class="change-driver-btn" onclick="window.tripEventListManager.openDriverSelection(${index})" title="Change Driver">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                        </div>
                                                    ` : `
                                                        <div class="driver-empty">
                                                            <div class="driver-status">
                                                                <i class="fas fa-user"></i>
                                                                <span class="driver-text">No driver selected</span>
                                                            </div>
                                                            <button class="select-driver-btn primary" onclick="window.tripEventListManager.openDriverSelection(${index})">
                                                                <i class="fas fa-plus"></i>
                                                                Select Driver
                                                            </button>
                                                        </div>
                                                    `}
                                                </td>
                                                <td class="vehicle-cell">
                                                    ${vehicleInfo ? `
                                                        <div class="vehicle-selected">
                                                            <div class="driver-avatar">
                                                                <img src="${this.UP_ROOT+vehicleInfo.vehiclePhoto || '/default-vehicle.png'}" alt="Vehicle Photo">
                                                            </div>
                                                            <div class="vehicle-info">
                                                                <div class="vehicle-model">${vehicleInfo.model}</div>
                                                                <div class="vehicle-details">${vehicleInfo.type} • ${vehicleInfo.capacity}</div>
                                                            </div>
                                                            <div class="vehicle-status ${vehicleInfo.status}">
                                                                <i class="fas fa-check-circle"></i>
                                                            </div>
                                                        </div>
                                                    ` : driverInfo ? `
                                                        <div class="vehicle-empty">
                                                            <div class="vehicle-status">
                                                                <i class="fas fa-car"></i>
                                                                <span class="vehicle-text">No vehicle selected</span>
                                                            </div>
                                                            <button class="select-vehicle-btn primary" onclick="window.tripEventListManager.showVehicleSelection(${index})">
                                                                <i class="fas fa-plus"></i>
                                                                Select Vehicle
                                                            </button>
                                                        </div>
                                                    ` : `
                                                        <div class="vehicle-status disabled">
                                                            <i class="fas fa-car"></i>
                                                            <span class="vehicle-text">Select driver first</span>
                                                        </div>
                                                    `}
                                                </td>
                                                <td class="fees-cell">
                                                    ${totalFees > 0 ? `
                                                        <div class="fees-breakdown">
                                                            ${feeBreakdown.map(fee => `
                                                                <div class="fee-item">
                                                                    <span class="fee-label">${fee.type}:</span>
                                                                    <span class="fee-amount">${fee.formatted || fee.amount.toLocaleString()}</span>
                                                                </div>
                                                            `).join('')}
                                                        </div>
                                                    ` : `
                                                        <div class="fees-empty">
                                                            <span class="fees-text">No fees yet</span>
                                                        </div>
                                                    `}
                                                </td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    eventsHTML = `
                        <div class="no-segments">
                            <div class="no-segments-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="no-segments-text">
                                <h3>No trip segments found</h3>
                                <p>Unable to load trip start/end points. Please try again later.</p>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error fetching start/end events:', error);
                eventsHTML = `
                    <div class="no-segments">
                        <div class="no-segments-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="no-segments-text">
                            <h3>Error loading trip segments</h3>
                            <p>Unable to load trip start/end points. Please try again later.</p>
                        </div>
                    </div>
                `;
            }

            container.innerHTML = eventsHTML;
        }

        // Show vehicle selection for a specific segment
        showVehicleSelection(segmentIndex) {
            // Mock vehicle data for the selected driver
            const mockVehicles = [
                { id: 1, model: 'Toyota Prius', type: 'Sedan', capacity: '4 passengers', price: 'LKR 2,500/day' },
                { id: 2, model: 'Honda Civic', type: 'Sedan', capacity: '4 passengers', price: 'LKR 2,800/day' },
                { id: 3, model: 'Suzuki Alto', type: 'Hatchback', capacity: '4 passengers', price: 'LKR 2,200/day' },
                { id: 4, model: 'Toyota Van', type: 'Van', capacity: '8 passengers', price: 'LKR 4,500/day' }
            ];

            // Create vehicle selection modal/popup
            const vehicleModal = document.createElement('div');
            vehicleModal.className = 'vehicle-selection-modal';
            vehicleModal.innerHTML = `
                <div class="vehicle-modal-overlay" onclick="this.parentElement.remove()"></div>
                <div class="vehicle-modal-content">
                    <div class="vehicle-modal-header">
                        <h3>Select Vehicle for Segment ${segmentIndex + 1}</h3>
                        <button class="vehicle-modal-close" onclick="this.closest('.vehicle-selection-modal').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="vehicle-modal-body">
                        <div class="vehicle-grid">
                            ${mockVehicles.map(vehicle => `
                                <div class="vehicle-card" onclick="window.tripEventListManager.selectVehicle(${segmentIndex}, ${vehicle.id}, '${vehicle.model}', '${vehicle.type}', '${vehicle.capacity}')">
                                    <div class="vehicle-card-header">
                                        <div class="vehicle-icon">
                                            <i class="fas fa-car"></i>
                                        </div>
                                        <div class="vehicle-price">${vehicle.price}</div>
                                    </div>
                                    <div class="vehicle-card-body">
                                        <h4 class="vehicle-model">${vehicle.model}</h4>
                                        <div class="vehicle-details">
                                            <span class="vehicle-type">${vehicle.type}</span>
                                            <span class="vehicle-capacity">${vehicle.capacity}</span>
                                        </div>
                                    </div>
                                    <div class="vehicle-card-footer">
                                        <button class="select-vehicle-card-btn">
                                            <i class="fas fa-check"></i>
                                            Select This Vehicle
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(vehicleModal);

            // Add modal styles dynamically
            if (!document.getElementById('vehicle-modal-styles')) {
                const style = document.createElement('style');
                style.id = 'vehicle-modal-styles';
                style.textContent = `
                    .vehicle-selection-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 10000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .vehicle-modal-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        backdrop-filter: blur(4px);
                    }

                    .vehicle-modal-content {
                        position: relative;
                        background: white;
                        border-radius: 16px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                        max-width: 800px;
                        width: 90%;
                        max-height: 80vh;
                        overflow: hidden;
                        z-index: 10001;
                    }

                    .vehicle-modal-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 24px;
                        border-bottom: 1px solid #e1e5e9;
                        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    }

                    .vehicle-modal-header h3 {
                        margin: 0;
                        font-size: 1.25rem;
                        font-weight: 600;
                        color: #1f2937;
                    }

                    .vehicle-modal-close {
                        background: none;
                        border: none;
                        font-size: 1.25rem;
                        color: #6b7280;
                        cursor: pointer;
                        padding: 8px;
                        border-radius: 8px;
                        transition: all 0.2s ease;
                    }

                    .vehicle-modal-close:hover {
                        background: #f3f4f6;
                        color: #374151;
                    }

                    .vehicle-modal-body {
                        padding: 24px;
                        max-height: calc(80vh - 80px);
                        overflow-y: auto;
                    }

                    .vehicle-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                        gap: 16px;
                    }

                    .vehicle-card {
                        border: 1px solid #e1e5e9;
                        border-radius: 12px;
                        padding: 20px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        background: white;
                    }

                    .vehicle-card:hover {
                        border-color: var(--primary-color);
                        box-shadow: 0 8px 24px rgba(0, 106, 113, 0.1);
                        transform: translateY(-2px);
                    }

                    .vehicle-card-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 16px;
                    }

                    .vehicle-icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.1rem;
                    }

                    .vehicle-price {
                        font-weight: 600;
                        color: var(--primary-color);
                        font-size: 0.9rem;
                    }

                    .vehicle-card-body {
                        margin-bottom: 16px;
                    }

                    .vehicle-model {
                        margin: 0 0 8px 0;
                        font-size: 1.1rem;
                        font-weight: 600;
                        color: #1f2937;
                    }

                    .vehicle-details {
                        display: flex;
                        flex-direction: column;
                        gap: 4px;
                    }

                    .vehicle-type,
                    .vehicle-capacity {
                        font-size: 0.85rem;
                        color: #6b7280;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                    }

                    .vehicle-type:before {
                        content: '🏷️';
                    }

                    .vehicle-capacity:before {
                        content: '👥';
                    }

                    .vehicle-card-footer {
                        text-align: center;
                    }

                    .select-vehicle-card-btn {
                        background: var(--primary-color);
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 8px;
                        font-size: 0.85rem;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        width: 100%;
                    }

                    .select-vehicle-card-btn:hover {
                        background: var(--secondary-color);
                        transform: translateY(-1px);
                    }

                    @media (max-width: 768px) {
                        .vehicle-grid {
                            grid-template-columns: 1fr;
                        }

                        .vehicle-modal-content {
                            width: 95%;
                            max-height: 90vh;
                        }

                        .vehicle-modal-header,
                        .vehicle-modal-body {
                            padding: 16px;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }

        // Handle vehicle selection
        selectVehicle(segmentIndex, vehicleId, model, type, capacity) {
            // Remove the modal
            document.querySelector('.vehicle-selection-modal').remove();

            // In a real implementation, this would make an API call to save the vehicle selection
            console.log(`Selected vehicle for segment ${segmentIndex}: ${model} (${type})`);

            // Show success message
            this.showNotification(`Vehicle "${model}" selected for Segment ${segmentIndex + 1}`, 'success');

            // In a real implementation, you would refresh the segments display
            // For now, we'll just show a notification
        }

        // Show notification
        showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.trip-notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `trip-notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Add to page
            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);

            // Add notification styles if not already present
            if (!document.getElementById('notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    .trip-notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                        padding: 16px 20px;
                        z-index: 10002;
                        min-width: 300px;
                        border-left: 4px solid var(--primary-color);
                        animation: slideIn 0.3s ease;
                    }

                    .trip-notification.success {
                        border-left-color: #10b981;
                    }

                    .trip-notification.error {
                        border-left-color: #ef4444;
                    }

                    .notification-content {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                    }

                    .notification-content i {
                        font-size: 1.1rem;
                        color: var(--primary-color);
                    }

                    .trip-notification.success .notification-content i {
                        color: #10b981;
                    }

                    .trip-notification.error .notification-content i {
                        color: #ef4444;
                    }

                    .notification-content span {
                        font-size: 0.9rem;
                        color: #374151;
                        font-weight: 500;
                    }

                    .notification-close {
                        position: absolute;
                        top: 12px;
                        right: 12px;
                        background: none;
                        border: none;
                        color: #6b7280;
                        cursor: pointer;
                        padding: 4px;
                        border-radius: 4px;
                        transition: all 0.2s ease;
                    }

                    .notification-close:hover {
                        background: #f3f4f6;
                        color: #374151;
                    }

                    @keyframes slideIn {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }

                    @media (max-width: 768px) {
                        .trip-notification {
                            left: 10px;
                            right: 10px;
                            min-width: auto;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }

        toggleMapView() {
            const routeMapSection = document.querySelector('.route-map-section');
            
            if (routeMapSection.style.display === 'none' || routeMapSection.style.display === '') {
                // Show map
                this.eventsMapContainer.classList.remove('summary-active');
                routeMapSection.style.display = 'block';
                this.viewMapBtn.innerHTML = '<i class="fas fa-list"></i> Hide Map';
                this.viewMapBtn.title = 'Hide route map';
                
                // Trigger map resize and recenter to Sri Lanka
                setTimeout(() => {
                    if (this.routeMap) {
                        google.maps.event.trigger(this.routeMap, 'resize');
                        this.routeMap.setCenter({ lat: 7.8731, lng: 80.7718 });
                        this.routeMap.setZoom(8);
                    }
                }, 100);
            } else {
                // Hide map
                this.eventsMapContainer.classList.add('summary-active');
                routeMapSection.style.display = 'none';
                this.viewMapBtn.innerHTML = '<i class="fas fa-map"></i> View Map';
                this.viewMapBtn.title = 'View route map';
            }
        }

        // Recommendations functionality
        openRecommendationsPopup() {
            this.resetRecommendationsForm();
            this.recommendationsPopup.classList.add('show');
        }

        closeRecommendationsPopup() {
            this.recommendationsPopup.classList.remove('show');
            this.resetRecommendationsForm();
        }

        resetRecommendationsForm() {
            this.provinceSelect.value = '';
            this.districtSelect.value = '';
            this.districtSelect.disabled = true;
            this.recommendationsSaveBtn.disabled = true;
        }

        handleProvinceChange(e) {
            const province = e.target.value;
            this.districtSelect.innerHTML = '<option value="">Select a district</option>';
            this.districtSelect.disabled = true;
            this.recommendationsSaveBtn.disabled = true;

            if (province) {
                this.populateDistricts(province);
                this.districtSelect.disabled = false;
            }
        }

        populateDistricts(province) {
            const districts = {
                'western': ['Colombo', 'Gampaha', 'Kalutara'],
                'central': ['Kandy', 'Matale', 'Nuwara Eliya'],
                'southern': ['Galle', 'Matara', 'Hambantota'],
                'northern': ['Jaffna', 'Kilinochchi', 'Mannar', 'Mullaitivu', 'Vavuniya'],
                'eastern': ['Trincomalee', 'Batticaloa', 'Ampara'],
                'north-western': ['Kurunegala', 'Puttalam'],
                'north-central': ['Anuradhapura', 'Polonnaruwa'],
                'uva': ['Badulla', 'Moneragala'],
                'sabaragamuwa': ['Ratnapura', 'Kegalle']
            };

            const provinceDistricts = districts[province] || [];
            provinceDistricts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.toLowerCase().replace(/\s+/g, '-');
                option.textContent = district;
                this.districtSelect.appendChild(option);
            });
        }

        // Utility methods
        formatTime(timeString) {
            if (!timeString) return 'N/A';
            // Convert HH:MM:SS to HH:MM format
            return timeString.substring(0, 5);
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        isValidCoordinate(value) {
            return value !== null && value !== undefined && !isNaN(parseFloat(value)) && isFinite(value);
        }

        /*
        findNearbyPlaces() {
            // Clear any existing nearby markers before adding new ones
            this.clearNearbyMarkers();

            const request = {
                location: this.popupMap.getCenter(), // Search around the current map center
                radius: '1500', // Search within 1500 meters (1.5 km)
                type: ['restaurant'], // Search for restaurants. You can add more types.
                // keyword: 'pizza', // Uncomment to search for specific keywords
            };

            this.placesService.nearbySearch(request, (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && results) {
                    for (let i = 0; i < results.length; i++) {
                        this.createMarker(results[i]);
                    }
                    console.log(`Found ${results.length} nearby places.`);
                } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                    console.log('No nearby places found for the current criteria.');
                } else {
                    console.error('Places service request failed:', status);
                }
            });
        }

        createMarker(place) {
            if (!place.geometry || !place.geometry.location) return;

            const marker = new google.maps.Marker({
                map: this.popupMap,
                position: place.geometry.location,
                title: place.name,
                // You can customize the icon here, e.g., based on place.types
                // icon: { url: 'http://maps.google.com/mapfiles/ms/icons/restaurant.png' }
            });

            // Create an InfoWindow to display place details
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <strong>${place.name}</strong><br>
                    ${place.vicinity || place.formatted_address || ''}<br>
                    ${place.rating ? `Rating: ${place.rating} (${place.user_ratings_total})` : ''}
                `,
            });

            // Show InfoWindow when marker is clicked
            marker.addListener('click', () => {
                infoWindow.open(this.popupMap, marker);
            });

            this.nearbyMarkers.push(marker); // Keep track of markers to clear them later
        }

        clearNearbyMarkers() {
            for (let i = 0; i < this.nearbyMarkers.length; i++) {
                this.nearbyMarkers[i].setMap(null);
            }
            this.nearbyMarkers = [];
        }
        */

    }
    // Initialize the travel spot manager
    window.TripEventListManager = TripEventListManager;
    window.tripEventListManager = new TripEventListManager();

})();



        

 
    