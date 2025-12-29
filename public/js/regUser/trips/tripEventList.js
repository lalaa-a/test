
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

            //this.selectedSpot = null;

            this.initializeElemenets();
            this.attachEventListeners();
            this.handleDateNavigation();
        }

        initializeElemenets(){
            this.URL_ROOT = 'http://localhost/test';
            this.addTravelSpotPopup = document.getElementById('add-travel-spot-popup');

            this.addEventButton = document.getElementById('add-event-btn');
            this.popupCloseBtn = document.getElementById('popup-close-btn');
            this.btnCancel = document.getElementById('btn-cancel');
            this.btnSave = document.getElementById('btn-save');

            this.startTimeInput = document.getElementById('start-time');
            this.endTimeInput = document.getElementById('end-time');
            this.spotTypeSelect = document.getElementById('spot-type');
            this.eventTypeData = document.getElementById('event-type-data');
            this.locationSelect = document.getElementById('location-select');
            this.travelSpotSelect = document.getElementById('travel-spot-select');
            this.locationInputContainer = document.getElementById('location-input-container');
            this.locationDescription = document.getElementById('location-description');

            this.eventStatusSelect = document.getElementById('event-status');

            this.selectedSpotContainer = document.getElementById("selected-spot-container")
        }

        attachEventListeners(){
            this.addEventButton.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.add('show');
            });

            this.popupCloseBtn.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.remove('show');
                this.resetForm();
            });

            this.btnCancel.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.remove('show');
                this.resetForm();
            });

            this.btnSave.addEventListener('click', () => this.saveEvent());
            this.addTravelSpotPopup.addEventListener('click', (e) => {
                if (e.target === this.addTravelSpotPopup) {
                    this.closePopup();
                }
            });

            this.spotTypeSelect.addEventListener('change',(e) => this.displayEventTypeData(e))
        }

        handleDateNavigation(){
            document.querySelectorAll('.date-nav-item').forEach(item => {

                item.addEventListener('click', function() {

                    document.querySelectorAll('.date-nav-item').forEach(i => i.classList.remove('active'));
                                
                    this.classList.add('active');
                                
                    const day = this.querySelector('.date-nav-day').textContent;
                    const date = this.querySelector('.date-nav-date').textContent;
                    const month = this.querySelector('.date-nav-month').textContent;
                            
                    const selectedDateInfo = document.querySelector('.selected-date-info');
                    selectedDateInfo.textContent = `${day}, ${month} ${date}, 2024`;
                            
                    console.log('Loading events for:', `${day}, ${month} ${date}, 2024`);

                });
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Guide booking functionality
        bookGuide(button, eventId) {
            if (button.classList.contains('booked')) {
                return; // Already booked
            }

            // Show loading state
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
            button.disabled = true;

            // Simulate API call (replace with actual AJAX call)
            setTimeout(() => {
                // Mock successful booking
                button.classList.add('booked');
                button.innerHTML = '<i class="fas fa-check"></i> Guide Booked';
                button.disabled = true;

                // Update guide info
                const guideSection = button.parentElement;
                const guideInfo = guideSection.querySelector('.guide-info');
                
                // Remove all guide status classes
                guideInfo.classList.remove('guide-available', 'guide-none', 'guide-unavailable');
                guideInfo.classList.add('guide-booked');
                
                // Update guide info content
                guideInfo.innerHTML = '<i class="fas fa-user-check"></i><span>Chaminda Silva</span>';

                // Add guide details (in real app, this would come from API response)
                const guideDetails = document.createElement('div');
                guideDetails.className = 'guide-details';
                guideDetails.innerHTML = `
                    <div class="guide-name">Chaminda Silva</div>
                    <div class="guide-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>5.0</span>
                    </div>
                    <div class="guide-price">$40/hour</div>
                `;

                // Insert guide details after the guide section
                guideSection.parentElement.appendChild(guideDetails);

                console.log('Guide booked successfully for event:', eventId);
            }, 1500);
        }

        closePopup(){
            this.addTravelSpotPopup.classList.remove('show');
            this.resetForm();
        }

        resetForm(){
            this.startTimeInput.value = '';
            this.endTimeInput.value = '';
            this.spotTypeSelect.value = '';
            this.eventStatusSelect.value = '';
            this.locationDescription.value = '';
            this.locationSelect.classList.remove('active');
            this.travelSpotSelect.classList.remove('active');
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
        
        async initMap() {

            let map;
            let marker;
            let selectedPlace = null;

            const { Map } = await google.maps.importLibrary("maps");
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");

            map = new Map(document.getElementById("map"), {
                center: { lat: -34.397, lng: 150.644 },
                zoom: 8,
                mapId: "12b46b4ecb983b59de763776"
            });

            // getting the autocomplete element
            const placeAutocomplete = this.locationInputContainer;  

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

                if (marker) {
                    marker.setMap(null);
                }

                //creating the advanced marker element
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                marker = new AdvancedMarkerElement({
                    map,
                    position: place.location,
                    title: place.displayName,
                });

                selectedPlace = {
                    name: place.displayName,
                    address: place.formattedAddress,
                    lat: place.location.lat(),
                    lng: place.location.lng()
                };

               // this.addLocation(selectedPlace);

                map.setCenter(place.location);
                map.setZoom(17);
                placeAutocomplete.value = '';
            });
        }     

        saveEvent(){
            const startTime = this.startTimeInput.value;
            const endTime = this.endTimeInput.value;
            const spotType = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            
            // Get content based on type
            let content = '';
            if (spotType === 'location') {
                content = this.locationDescription.value;
            } else {
                // For other types, you might want to add a general content field
                content = '';
            }

            // Validate required fields
            if (!startTime || !endTime || !spotType) {
                console.log('Please fill in all required fields');
                alert('Please fill in all required fields');
                return;
            }

            // Log the data (replace with actual API call or processing)
            console.log('Form Data:', {
                startTime: startTime,
                endTime: endTime,
                type: spotType,
                status: eventStatus,
                content: content
            });

            // Close the popup after saving
            this.closePopup();
        }

        //When the travelSpotsSelect page closes the data would be send to here
        async handleSpotSelection(spotId){
            console.log('Selected travel spot ID:', spotId);
            
            try{

                const response = await fetch(this.URL_ROOT+`/RegUser/retrieveSelectedSpot/${spotId}`);
                const data = await response.json();

                if (data.success) {
                    console.log(data.spotData);

                    const spotCardData = {
                                            spotName  : data.spotData.spotName,
                                            description: data.spotData.overview,
                                            averageRating : data.spotData.averageRating,
                                            itinerary : data.spotData.itinerary,                
                    };
                    console.log(spotCardData);
                    this.selectedSpot = spotCardData;
                    
                } else {
                    console.error('Failed to load trips:', data.message);
                    alert('Failed to load trips: ' + data.message);
                }

            } catch(error) {
                console.error('Error loading trips:', error);
                alert('Error loading trips: ' + error.message);
            } finally {
                this.selectedSpotContainer.innerHTML = '';
                this.selectedSpotContainer.appendChild(this.renderSelectedEvent(this.selectedSpot, true, false));
            }
        }

        renderSelectedEvent(spot ,isPopup, isLocation){

            const selectedType = this.spotTypeSelect.value; // 'travelSpot' or 'location'
            const selectedStatus = this.eventStatusSelect.value || 'normal'; // 'checking', 'normal', or 'checkout'
            
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
                'checking': {
                    badge: 'Checking',
                    class: 'status-checking'
                },
                'normal': {
                    badge: 'Normal',
                    class: 'status-normal'
                },
                'checkout': {
                    badge: 'Checkout',
                    class: 'status-checkout'
                }
            };

            const currentType = typeConfig[selectedType] || typeConfig['travelSpot'];
            const currentStatus = statusConfig[selectedStatus] || statusConfig['normal'];

            const card = document.createElement('div');
            card.className = 'event-card';
            card.dataset.type = selectedType;
            card.dataset.status = selectedStatus;
            card.innerHTML = `
                                <div class="event-time-section">
                                    <div class="time-label">START</div>
                                    <div class="event-start-time">${this.escapeHtml(this.startTimeInput.value)}</div>
                                    <div class="time-label">END</div>
                                    <div class="event-end-time">${this.escapeHtml(this.endTimeInput.value)}</div>
                                </div>
                                <div class="event-image">
                                    <i class="${currentType.icon}"></i>
                                </div>
                                <div class="event-content">
                                    <div class="event-header">
                                        <div>
                                            <h4 class="event-title">${this.escapeHtml(spot.spotName)}</h4>
                                        </div>
                                        <div class="event-badges">
                                            <span class="event-type-badge ${currentType.class}">${currentType.badge}</span>
                                            <span class="event-status-badge ${currentStatus.class}">${currentStatus.badge}</span>
                                        </div>
                                    </div>
                                    <p class="event-description">
                                        ${this.escapeHtml(spot.description)}
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
                                    ${(isPopup || isLocation) ? `
                                        <div class="guide-section">
                                            <div class="guide-info guide-none">
                                                <i class="fas fa-user-slash"></i>
                                                <span>No guide added</span>
                                            </div>
                                            <button class="guide-booking-btn" onclick="tripEventListManager.bookGuide(this, 'selected-spot')">
                                                <i class="fas fa-plus"></i>
                                                Add Guide
                                            </button>
                                        </div>
                                    ` : ''}
                                    
                                </div>
                            `;
            return card;
        }
            

    }
    // Initialize the travel spot manager
    window.TripEventListManager = TripEventListManager;
    window.tripEventListManager = new TripEventListManager();

})();



        

 
    