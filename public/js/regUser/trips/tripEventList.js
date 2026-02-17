
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
            this.selectedSpot = null;

            this.currentSelectedDate = null;
            this.selectedLocation = null;
            this.currentEditingEventId = null;

            this.mapElement = null;
            this.marker = null;

            this.initializeElemenets();
            this.attachEventListeners();
            this.handleDateNavigation();
        }

        initializeElemenets(){
            this.addTravelSpotPopup = document.getElementById('add-travel-spot-popup');

            this.addEventButton = document.getElementById('add-event-btn');
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
            
            // Initialize Flatpickr for time inputs
            this.initializeFlatpickr();
        }

        attachEventListeners(){
            this.addEventButton.addEventListener('click', () => {
                this.addTravelSpotPopup.classList.add('show');
                this.setNextEventStartTime(this.tripId.textContent, this.currentSelectedDate);
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

            // Time input change listeners are now handled by Flatpickr onChange callbacks

            this.eventStatusSelect.addEventListener('change',() => {
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
        }

        handleDateNavigation(){
            document.querySelectorAll('.date-nav-item').forEach(item => {

                item.addEventListener('click', () => {

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

            const availableEventCard = this.selectedSpotContainer.querySelector(".event-card-wrapper");
            if(availableEventCard) {
                availableEventCard.remove();
            }

            const availableEventCardLo = this.selectedLocationContainer.querySelector(".event-card-wrapper");
            if(availableEventCardLo) {
                availableEventCardLo.remove();
            }

            this.gotoTravelSpotsElement.style.display = 'block';
            this.selectedSpot = null;
            this.selectedLocation = null;

            document.getElementById('autocomplete-container').style.display = 'block';
            this.selectedLocationContainer.innerHTML = '';

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

        async saveEvent(){
        
            const type = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            const locationDescription = this.locationDescription.value;

            if(!this.validateInput()){
                return;
            }

            // Convert 12-hour format to 24-hour format strings (HH:MM)
            const startTime24 = this.convertTo24Hour(this.startTimeInput.value);
            const endTime24 = this.convertTo24Hour(this.endTimeInput.value);

            console.log('Start time 24h:', startTime24);
            console.log('End time 24h:', endTime24);

            let eventData = {
                eventDate: this.currentSelectedDate,
                startTime: startTime24,
                endTime: endTime24,
                eventType: type,
                eventStatus: eventStatus,
                tripId : this.tripId.textContent
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

                if (result.success) {
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
                this.loadEventCardsForDate(this.currentSelectedDate);
                //this.submitBtn.disabled = false;
                //this.submitBtn.textContent = 'Add Travel Spot';
                this.resetForm();
            }
        }

        async loadEventCardsForDate(eventDate){
            try{
                const response = await fetch(this.URL_ROOT+`/RegUser/getEventCardsByDate/${this.tripId.textContent}/${eventDate}`);
                const data = await response.json();

                if (data.success) {
                    
                    this.eventsContainer.innerHTML = '';
                    
                    for (const card of data.eventCards) {
                        if(card.eventType === 'travelSpot'){
                            const travelSpot = await this.getSpotData(card.travelSpotId);
                            const eventFormData = {
                                eventId: card.eventId,
                                type: card.eventType,
                                status: card.eventStatus,
                                startTime: card.startTime,
                                endTime: card.endTime
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
            this.currentEditingEventId = eventId;
            try{
                const eventData = await fetch(this.URL_ROOT + `/RegUser/retrieveEventData/${tripId}/${eventId}`);
                const data = await eventData.json(); 
                if(data.success){
                    console.log(data.eventData);
                    this.resetForm();

                    const [startHours,startMinutes] = data.eventData.startTime.split(':');
                    const [endHours, endMinutes]= data.eventData.endTime.split(':');
                    
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
                        this.handleSpotSelection(data.eventData.travelSpotId);
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
                        this.loadEventCardsForDate(this.currentSelectedDate); // Reload cards after a deletion
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
            
            wrapper.appendChild(card);
            if (isPopup) {
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-card-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
                removeBtn.onclick = () => { wrapper.remove();
                    if(eventFormData.type === 'travelSpot'){
                        this.gotoTravelSpotsElement.style.display = 'block'; 
                    } else{
                        document.getElementById('autocomplete-container').style.display = 'block';
                    }
                };
                wrapper.appendChild(removeBtn);
            } 
            return wrapper;
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

            if(!startTime){
                alert("Please fill in location Start time");
                return false;
            }
            if(!endTime){
                alert("Please fill in location End time");
                return false;
            }
            
            if(endTime < startTime){
                alert("End time must be later than start time");
                return false;
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

    }
    // Initialize the travel spot manager
    window.TripEventListManager = TripEventListManager;
    window.tripEventListManager = new TripEventListManager();

})();



        

 
    