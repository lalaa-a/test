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
            this.tripStatus = null;
            this.assignmentsLocked = false;
            this.revisionMode = false;
            this.rejectedDriverCount = 0;
            this.rejectedGuideCount = 0;
            this.eventChangesLocked = false;
            this.guideOnlyEditMode = false;

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
            this.mapInitPromise = null;
            this.mapInitialized = false;
            this.mapAutocompleteListenerAttached = false;
            this.nearbyTypeListenerAttached = false;

            this.numberOfPeople = 1; // Default value, will be updated when guide is selected   
            
            // Load existing drivers when trip is initialized
            this.loadExistingDrivers();
            
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

        async waitForInnerMap(maxAttempts = 20, delayMs = 100) {
            for (let attempt = 0; attempt < maxAttempts; attempt++) {
                if (!this.mapElement) {
                    this.mapElement = document.querySelector('gmp-map');
                }

                if (this.mapElement && this.mapElement.innerMap) {
                    return this.mapElement.innerMap;
                }

                await new Promise((resolve) => setTimeout(resolve, delayMs));
            }

            return null;
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
            this.addEventButton.addEventListener('click', async () => {
                if (typeof this.canModifyEventStructure === 'function') {
                    const canModifyEvents = await this.canModifyEventStructure(true);
                    if (!canModifyEvents) {
                        return;
                    }
                }

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

            this.eventStatusSelect.addEventListener('change', async () => {
                let eventStatus = this.eventStatusSelect.value;
                const startTimeGroup = this.startTimeInput.closest('.form-group');
                const endTimeGroup = this.endTimeInput.closest('.form-group');

                if ((eventStatus === 'start' || eventStatus === 'end') && typeof this.hasBoundaryStatusConflict === 'function') {
                    const hasConflict = await this.hasBoundaryStatusConflict(eventStatus);

                    if (hasConflict) {
                        alert(`Only one ${eventStatus} event is allowed for a trip. Please use intermediate for other events.`);
                        this.eventStatusSelect.value = 'intermediate';
                        eventStatus = 'intermediate';
                    }
                }
                
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
                        this.eventsMapContainer.classList.remove('summary-map-visible');

                        const routeMapSection = document.querySelector('.route-map-section');
                        if (routeMapSection) {
                            routeMapSection.style.display = '';
                        }
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
                    
                    // For start events, endTime is synthetic in DB. Use startTime as effective boundary.
                    const timeString = data.eventCard.eventStatus === 'start'
                        ? data.eventCard.startTime
                        : (data.eventCard.effectiveEndTime || data.eventCard.endTime);

                    if (!timeString) {
                        return;
                    }

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
        
        async initMap() {
            if (this.mapInitialized && this.innerMap) {
                return;
            }

            if (this.mapInitPromise) {
                return this.mapInitPromise;
            }

            this.mapInitPromise = (async () => {
                // Request all needed libraries at once.
                const [
                    { AdvancedMarkerElement },
                    { InfoWindow }
                ] = await Promise.all([
                    google.maps.importLibrary('marker'),
                    google.maps.importLibrary('maps'),
                    google.maps.importLibrary('places'),
                    google.maps.importLibrary('core'),
                    google.maps.importLibrary('geometry')
                ]);

                if (!this.placeAutocomplete) {
                    this.placeAutocomplete = document.querySelector('gmp-place-autocomplete');
                }
                if (!this.locationInputContainer) {
                    this.locationInputContainer = document.getElementById('location-input-container');
                }

                this.innerMap = await this.waitForInnerMap();
                if (!this.innerMap) {
                    throw new Error('Unable to initialize location map.');
                }

                this.innerMap.setOptions({
                    mapTypeControl: false,
                });

                if (!this.infoWindow) {
                    this.infoWindow = new InfoWindow();
                }

                this.nearbyMarkers = []; // Track nearby search markers.

                if (!this.marker2) {
                    this.marker2 = new AdvancedMarkerElement({
                        map: this.innerMap,
                        title: "Selected Place"
                    });
                }

                if (!this.mapAutocompleteListenerAttached && this.placeAutocomplete) {
                    this.placeAutocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                        const place = placePrediction?.toPlace ? placePrediction.toPlace() : null;
                        if (!place) {
                            return;
                        }

                        await place.fetchFields({
                            fields: ['displayName', 'formattedAddress', 'location', 'viewport', 'rating'],
                        });

                        if (!place.location) {
                            return;
                        }

                        const locationData = {
                            spotName: place.displayName,
                            averageRating: place.rating || 0,
                            description: this.locationDescription ? this.locationDescription.value : '',
                            itinerary: [{ latitude: place.location.lat(), longitude: place.location.lng(), pointId: null, pointName: place.displayName }],
                        };

                        this.placeData = locationData;
                        this.selectedLocation = locationData;

                        // Update marker position.
                        this.marker2.position = place.location;

                        // Adjust map view.
                        if (place.viewport) {
                            this.innerMap.fitBounds(place.viewport);
                        } else {
                            this.innerMap.setCenter(place.location);
                            this.innerMap.setZoom(17);
                        }

                        const content = document.createElement('div');
                        const addressText = document.createElement('span');
                        addressText.textContent = place.formattedAddress || '';
                        content.appendChild(addressText);

                        this.updateInfoWindow(place.displayName, content, this.marker2);

                        // Render selected location card so validation and save flow can proceed.
                        if (typeof this.handleLocationSelection === 'function') {
                            this.handleLocationSelection(locationData);
                        }

                        // Trigger nearby search automatically when a type is selected.
                        if (this.typeSelect && this.typeSelect.value) {
                            this.nearbySearch();
                        }
                    });

                    this.mapAutocompleteListenerAttached = true;
                }

                this.typeSelect = document.getElementById('controls');
                if (this.typeSelect && !this.nearbyTypeListenerAttached) {
                    this.typeSelect.addEventListener('change', () => {
                        this.nearbySearch();
                    });
                    this.nearbyTypeListenerAttached = true;
                }

                this.mapInitialized = true;
                console.log("Map initialization complete");
            })();

            try {
                await this.mapInitPromise;
            } finally {
                this.mapInitPromise = null;
            }
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
                        const fallbackItinerary = this.selectedLocation?.itinerary?.[0] || {};
                        
                        // Capture the place data
                        this.placeData = {
                            name: place.displayName || this.selectedLocation?.spotName || 'Selected Place',
                            rating: place.rating || this.selectedLocation?.averageRating || 0,
                            lat: place.location?.lat ? place.location.lat() : fallbackItinerary.latitude,
                            lng: place.location?.lng ? place.location.lng() : fallbackItinerary.longitude,
                            address: place.formattedAddress || '',
                        };

                        if (this.placeData.lat == null || this.placeData.lng == null) {
                            return;
                        }
                        
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

        isValidCoordinate(value) {
            return value !== null && value !== undefined && !isNaN(parseFloat(value)) && isFinite(value);
        }

    }
    function resolveTripEventListScriptBase() {
        const currentScript = document.currentScript;
        if (currentScript && currentScript.src) {
            const cleanSrc = currentScript.src.split('?')[0];
            return cleanSrc.substring(0, cleanSrc.lastIndexOf('/') + 1);
        }

        // Fallback path when currentScript is unavailable.
        return `${window.location.origin}/test/public/js/regUser/trips/`;
    }

    function loadTripEventListModule(scriptBase, fileName) {
        return new Promise((resolve, reject) => {
            const moduleKey = `${scriptBase}${fileName}`;
            const existing = document.querySelector(`script[data-trip-module="${moduleKey}"]`);

            if (existing) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = moduleKey;
            script.async = false;
            script.setAttribute('data-trip-module', moduleKey);

            script.onload = () => resolve();
            script.onerror = () => reject(new Error(`Failed to load ${fileName}`));

            document.head.appendChild(script);
        });
    }

    async function loadTripEventListModules() {
        const scriptBase = resolveTripEventListScriptBase();
        await Promise.all([
            loadTripEventListModule(scriptBase, 'tripEventList.routeMap.module.js'),
            loadTripEventListModule(scriptBase, 'tripEventList.finalization.module.js'),
            loadTripEventListModule(scriptBase, 'tripEventList.popup.module.js'),
            loadTripEventListModule(scriptBase, 'tripEventList.eventsCrud.module.js'),
            loadTripEventListModule(scriptBase, 'tripEventList.summary.module.js'),
            loadTripEventListModule(scriptBase, 'tripEventList.payment.module.js')
        ]);
    }

    function applyTripEventListModules() {
        if (typeof window.applyTripEventListRouteMapModule === 'function') {
            window.applyTripEventListRouteMapModule(TripEventListManager);
        }

        if (typeof window.applyTripEventListFinalizationModule === 'function') {
            window.applyTripEventListFinalizationModule(TripEventListManager);
        }

        if (typeof window.applyTripEventListPopupModule === 'function') {
            window.applyTripEventListPopupModule(TripEventListManager);
        }

        if (typeof window.applyTripEventListEventsCrudModule === 'function') {
            window.applyTripEventListEventsCrudModule(TripEventListManager);
        }

        if (typeof window.applyTripEventListSummaryModule === 'function') {
            window.applyTripEventListSummaryModule(TripEventListManager);
        }

        if (typeof window.applyTripEventListPaymentModule === 'function') {
            window.applyTripEventListPaymentModule(TripEventListManager);
        }
    }

    // Initialize the travel spot manager
    window.TripEventListManager = TripEventListManager;

    (async function bootstrapTripEventList() {
        try {
            await loadTripEventListModules();
            applyTripEventListModules();
        } catch (error) {
            console.warn('TripEventList modules failed to load. Some features may be unavailable.', error);
        }

        window.tripEventListManager = new TripEventListManager();
    })();

})();



        

 
    