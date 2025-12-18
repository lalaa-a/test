
// Travel Spot Management JavaScript
(function() {

    // Check if CategoryManager already exists and clean up
    if (window.TravelSpotManager) {
        console.log('TravelSpotManger already exists, cleaning up...');
        // Clean up any existing instance
        if (window.travelSpotManager) {
            // Clean up event listeners if needed
            delete window.travelSpotManager;
        }
        delete window.TravelSpotManager;
    }

    class TravelSpotManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';

            this.allFilters = [];
            this.selectedSubfilters = [];
            this.selectedNearbySpots = [];
            this.itinerary = [];
            this.travelSpotCardData = [];
            this.editingTravelSpot = null;

            this.initializeElements();
            this.attachEventListeners();
            this.loadAllFilters(); // Load all filters for search functionality
            this.closeDisplayResults();
            this.initMap();
            this.loadTravelSpotCards();
        }

        initializeElements() {
            // Popup elements
            this.popup = document.getElementById('travel-spot-popup');
            this.popupClose = document.getElementById('popup-close');
            this.cancelBtn = document.getElementById('cancel-btn');
            this.submitBtn = document.getElementById('submit-btn');
            this.addTravelSpotBtn = document.getElementById('add-main-filter-btn');
            this.travelSpotCardSection = document.getElementById('travel-spot-cards');

            // Form elements
            this.form = document.getElementById('travel-spot-form');

            // Subfilter elements
            this.subfilterSearch = document.getElementById('subfilter-search');
            this.searchSubfilterBtn = document.getElementById('search-subfilter-btn');
            this.subfilterResults = document.getElementById('subfilter-results');
            this.selectedSubfiltersContainer = document.getElementById('selected-subfilters');

            // Photo elements
            this.photoInputs = [
                document.getElementById('photo1'),
                document.getElementById('photo2'),
                document.getElementById('photo3'),
                document.getElementById('photo4')
            ];
            this.photoPreviews = [
                document.getElementById('photo1-preview'),
                document.getElementById('photo2-preview'),
                document.getElementById('photo3-preview'),
                document.getElementById('photo4-preview')
            ];

            // Nearby spots elements
            this.nearbySearch = document.getElementById('nearby-search');
            this.searchNearbyBtn = document.getElementById('search-nearby-btn');
            this.nearbyResults = document.getElementById('nearby-results');
            this.selectedNearby = document.getElementById('selected-nearby');
            this.itineraryContainer = document.getElementById('itinerary');
        }

        attachEventListeners() {
            // Popup controls
            this.addTravelSpotBtn.addEventListener('click', () => this.openPopup());
            this.popupClose.addEventListener('click', () => this.closePopup());
            this.cancelBtn.addEventListener('click', () => this.closePopup());

            // Close popup when clicking outside
            this.popup.addEventListener('click', (e) => {
                if (e.target === this.popup) {
                    this.closePopup();
                }
            });

            // Form submission
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));

            // Photo uploads
            this.photoInputs.forEach((input, index) => {
                input.addEventListener('change', (e) => this.handlePhotoUpload(e, index));
            });

            // Subfilter search
            this.searchSubfilterBtn.addEventListener('click', () => this.searchSubfilters());
            this.subfilterSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.searchSubfilters();
                }
            });

            // Nearby spots search
            this.searchNearbyBtn.addEventListener('click', () => this.searchNearbySpots());
            this.nearbySearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.searchNearbySpots();
                }
            });
        }

        openPopup() {
            this.popup.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        closePopup() {
            this.popup.classList.remove('show');
            document.body.style.overflow = '';
            this.resetForm();
        }

        resetForm() {
            this.form.reset();
            this.selectedSubfilters = [];
            this.selectedNearbySpots = [];

            // Reset photo previews
            this.photoPreviews.forEach(preview => {
                preview.innerHTML = `
                    <i class="fas fa-camera"></i>
                    <span>Click to upload</span>
                `;
            });

            // Clear selected subfilters
            this.selectedSubfiltersContainer.innerHTML = '';
            this.subfilterResults.innerHTML = '';

            // Clear selected nearby spots
            this.selectedNearby.innerHTML = '';
            this.nearbyResults.innerHTML = '';
        }

        async loadAllFilters() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Moderator/getAllFilters`);
                const data = await response.json();

                if (data.success) {
                    this.allFilters = data.allFilters;
                    console.log('All filters loaded for search functionality');
                } else {
                    console.error('Failed to load filters:', data.message);
                }
            } catch (error) {
                console.error('Error loading filters:', error);
            }
        }

        async searchSubfilters() {
            const query = this.subfilterSearch.value.trim();
            if (!query) {
                alert('Please enter a search term');
                return;
            }

            try {
                // Filter subfilters based on search query
                const filteredSubfilters = this.allFilters
                    .filter(item => item.subFilterId && (
                        item.subFilterName.toLowerCase().includes(query.toLowerCase()) ||
                        item.mainFilterName.toLowerCase().includes(query.toLowerCase())
                    ))
                    .filter(item => !this.selectedSubfilters.some(selected => selected.subFilterId === item.subFilterId));

                this.displaySubfilterResults(filteredSubfilters);
            } catch (error) {
                console.error('Error searching subfilters:', error);
            }
        }

        displaySubfilterResults(results) {
            this.subfilterResults.innerHTML = '';

            if (results.length === 0) {
                this.subfilterResults.innerHTML = '<div class="subfilter-result-item">No subfilters found</div>';
                return;
            }

            results.forEach(subfilter => {
                const subfilterElement = document.createElement('div');
                subfilterElement.className = 'subfilter-result-item';
                subfilterElement.innerHTML = `
                    <div class="subfilter-info">
                        <strong>${this.escapeHtml(subfilter.subFilterName)}</strong>
                        <span>${this.escapeHtml(subfilter.mainFilterName)}</span>
                    </div>
                    <button type="button" class="add-subfilter-btn" onclick="travelSpotManager.addSubfilter(${subfilter.subFilterId}, '${this.escapeHtml(subfilter.subFilterName)}', '${this.escapeHtml(subfilter.mainFilterName)}')">
                        <i class="fas fa-plus"></i>
                    </button>
                `;
                this.subfilterResults.appendChild(subfilterElement);
            });
        }

        addSubfilter(subFilterId, subFilterName, mainFilterName) {
            // Check if already added
            if (this.selectedSubfilters.some(subfilter => subfilter.subFilterId === subFilterId)) {
                return;
            }

            this.selectedSubfilters.push({ subFilterId, subFilterName, mainFilterName });
            this.renderSelectedSubfilters();
        }

        removeSubfilter(subFilterId) {
            this.selectedSubfilters = this.selectedSubfilters.filter(subfilter => subfilter.subFilterId !== subFilterId);
            this.renderSelectedSubfilters();
        }

        renderSelectedSubfilters() {
            this.selectedSubfiltersContainer.innerHTML = '';

            this.selectedSubfilters.forEach(subfilter => {
                const subfilterElement = document.createElement('div');
                subfilterElement.className = 'selected-subfilter';
                subfilterElement.innerHTML = `
                    <span>${this.escapeHtml(subfilter.subFilterName)} (${this.escapeHtml(subfilter.mainFilterName)})</span>
                    <span class="remove-subfilter" onclick="travelSpotManager.removeSubfilter(${subfilter.subFilterId})">
                        <i class="fas fa-times"></i>
                    </span>
                `;
                this.selectedSubfiltersContainer.appendChild(subfilterElement);
            });
        }

        handlePhotoUpload(event, index) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreviews[index].innerHTML = `<img src="${e.target.result}" alt="Photo ${index + 1}">`;
                };
                reader.readAsDataURL(file);
            }
        }

        async searchNearbySpots() {
            const query = this.nearbySearch.value.trim();
            if (!query) {
                alert('Please enter a search term');
                return;
            }

            try {
                const searchingData = {name:query};

                const response = await fetch(this.URL_ROOT+'/Moderator/getTravelSpotByName',{
                    method:'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(searchingData)
                });

                const data = await response.json();

                if (data.success) {
                    this.displayNearbyResults(data.travelSpots);
                    console.log('All filters loaded for search functionality');

                } else {
                    console.error('Failed to load filters:', data.message);
                }
            } catch (error) {
                console.error('Error searching nearby spots:', error);
            }
        }

        displayNearbyResults(results) {
            this.nearbyResults.innerHTML = '';

            if (results.length === 0) {
                this.nearbyResults.innerHTML = '<div class="nearby-result-item">No spots found</div>';
                return;
            }

            results.forEach(spot => {
                const spotElement = document.createElement('div');
                spotElement.className = 'nearby-result-item';
                spotElement.innerHTML = `
                    <div class="spot-info">
                        <strong>${this.escapeHtml(spot.spotName)}</strong>
                    </div>
                    <button type="button" class="add-spot-btn" onclick="travelSpotManager.addNearbySpot(${spot.spotId}, '${this.escapeHtml(spot.spotName)}')">
                        <i class="fas fa-plus"></i>
                    </button>
                `;
                this.nearbyResults.appendChild(spotElement);
            });
        }

        //function to close search displaying results when clicking on outside 
        closeDisplayResults(){
            // Close display results when clicking outside
            document.addEventListener('click', (e) => {

                const isClickInsideSubfilterSearch = this.subfilterSearch && this.subfilterSearch.contains(e.target);// this checks the clicked on thing is inside the this.subFilterSearch(which is a input element)
                const isClickInsideSubfilterResults = this.subfilterResults && this.subfilterResults.contains(e.target);
                const isClickInsideNearbySearch = this.nearbySearch && this.nearbySearch.contains(e.target);
                const isClickInsideNearbyResults = this.nearbyResults && this.nearbyResults.contains(e.target);
                const isClickOnSearchBtn = e.target.closest('#search-nearby-btn') || e.target.closest('#search-subfilter-btn');// but these buttons are not inside that inpu element no so we should check whether the clicked element is that button or its anesestor

                // Close subfilter results if clicking outside
                if (!isClickInsideSubfilterSearch && !isClickInsideSubfilterResults && !isClickOnSearchBtn && this.subfilterResults) {
                    this.subfilterResults.innerHTML = '';
                }

                // Close nearby results if clicking outside
                if (!isClickInsideNearbySearch && !isClickInsideNearbyResults && !isClickOnSearchBtn && this.nearbyResults) {
                    this.nearbyResults.innerHTML = '';
                }
            });
        }

        addNearbySpot(spotId, spotName) {
            // Check if already added
            if (this.selectedNearbySpots.some(spot => spot.spotId === spotId)) {
                return;
            }

            this.selectedNearbySpots.push({ spotId, spotName});
            this.renderSelectedNearbySpots();
        }

        removeNearbySpot(spotId) {
            this.selectedNearbySpots = this.selectedNearbySpots.filter(spot => spot.spotId !== spotId);
            this.renderSelectedNearbySpots();
        }

        renderSelectedNearbySpots() {
            this.selectedNearby.innerHTML = '';

            this.selectedNearbySpots.forEach(spot => {
                const spotElement = document.createElement('div');
                spotElement.className = 'selected-spot';
                spotElement.innerHTML = `
                    <span>${this.escapeHtml(spot.spotName)}</span>
                    <span class="remove-spot" onclick="travelSpotManager.removeNearbySpot(${spot.spotId})">
                        <i class="fas fa-times"></i>
                    </span>
                `;
                this.selectedNearby.appendChild(spotElement);
            });
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
                mapId: "12b46b4ecb983b59bcc14db4"
            });

            // Create PlaceAutocompleteElement
            const placeAutocomplete = new PlaceAutocompleteElement();
            
            // Insert after your input or in a specific container
            const container = document.getElementById("autocomplete-container");
            container.appendChild(placeAutocomplete);   

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

                this.addLocation(selectedPlace);

                map.setCenter(place.location);
                map.setZoom(17);
                placeAutocomplete.value = '';
            });
        }

        addLocation(addingLocation){
            // Check if already added
            console.log("add location is working");
            if (this.itinerary.some(existLocation => (existLocation.lat === addingLocation.lat)&&(existLocation.lng === addingLocation.lng))) {
                return;
            }
            this.itinerary.push(addingLocation);
            this.renderSelectedLocation();
        }

        removeLocation(removingLat,removingLng) {
            this.itinerary = this.itinerary.filter(existLocation => (existLocation.lat !== removingLat)&&(existLocation.lng !== removingLng));
            this.renderSelectedLocation();
        }

        renderSelectedLocation() {
            this.itineraryContainer.innerHTML = '';

            this.itinerary.forEach(spot => {
                const spotElement = document.createElement('div');
                spotElement.className = 'selected-spot';
                spotElement.innerHTML = `
                    <span>${this.escapeHtml(spot.name)}</span>
                    <span class="remove-spot" onclick="travelSpotManager.removeLocation(${spot.lat},${spot.lng})">
                        <i class="fas fa-times"></i>
                    </span>
                `;
                this.itineraryContainer.appendChild(spotElement);
            });
        }

        async handleSubmit(e) {
            e.preventDefault();

            // Validate form
            if (!this.validateForm()) {
                return;
            }

            // Collect form data
            const formData = new FormData(this.form);

            // Add selected subfilters
            formData.append('subFilters', JSON.stringify(this.selectedSubfilters.map(s => s.subFilterId)));

            // Add selected nearby spots
            formData.append('nearbySpots', JSON.stringify(this.selectedNearbySpots.map(s => s.spotId)));

            formData.append('itinerary',JSON.stringify(this.itinerary.map(s => ({name:s.name,lat:s.lat,lng:s.lng}))))

            console.log('this is the form data');
            for (const [key, value] of formData.entries()) {
                console.log(key, value);
            }

            // Add photos
            this.photoInputs.forEach((input, index) => {
                if (input.files[0]) {
                    formData.append(`photo${index + 1}`, input.files[0]);
                }
            });

            // Disable submit button
            this.submitBtn.disabled = true;
            this.submitBtn.textContent = this.editingTravelSpot ? 'Updating...' : 'Adding...';

            let METHOD;
            let URL;
            let msg;

            if(this.editingTravelSpot){
                formData.append('spotId',this.editingTravelSpot);
                METHOD = 'POST';
                URL = `${this.URL_ROOT}/Moderator/editTravelSpot`;
                msg = 'Travel Spot Updated Successfully.'
            } else{
                METHOD = 'POST'
                URL = `${this.URL_ROOT}/Moderator/addTravelSpot`;
                msg = 'Travel Spot Added Successfully.'
            }

            try {
                const response = await fetch(URL, {
                    method: METHOD,
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(msg);
                    this.closePopup();
                    this.renderTravelSpotCards();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                this.submitBtn.disabled = false;
                this.submitBtn.textContent = 'Add Travel Spot';
            }
        }

        validateForm() {
            const spotName = document.getElementById('spotName').value.trim();
            const province = document.getElementById('province').value;
            const district = document.getElementById('district').value;
            const bestTimeFrom = document.getElementById('bestTimeFrom').value;
            const bestTimeTo = document.getElementById('bestTimeTo').value;
            const visitingDurationMax = document.getElementById('visitingDurationMax').value;
            const photo1 = document.getElementById('photo1').files[0];

            if (!spotName) {
                alert('Please enter a spot name');
                return false;
            }

            if (!province) {
                alert('Please select a province');
                return false;
            }

            if (!district) {
                alert('Please select a district');
                return false;
            }

            if (!bestTimeFrom || !bestTimeTo) {
                alert('Please select best visiting times');
                return false;
            }

            if (!visitingDurationMax) {
                alert('Please enter visiting duration');
                return false;
            }

            if (!photo1) {
                alert('Please upload at least one photo');
                return false;
            }

            if (this.selectedSubfilters.length === 0) {
                alert('Please select at least one subfilter');
                return false;
            }

            return true;
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async loadTravelSpotCards(){
            try {

                let travelSpotCardDataTemp = [];
                const response = await fetch(this.URL_ROOT + '/Moderator/getTravelSpotCardData');
                const data = await response.json();

                console.log('this is travel spot data: ',data);
                
                if (data.success) {
                    
                    travelSpotCardDataTemp = data.travelSpotCardData;
                   // console.log("all filters ",travelSpotCardData);

                    // Clear the array before re-populating to avoid duplicates
                    this.travelSpotCardData = [];

                    //Grouping elemets 
                    travelSpotCardDataTemp.forEach(item => {
                        // check if the main filter already exist
                        let existing = this.travelSpotCardData.find(g => g.mainFilterId === item.mainFilterId);
                        let index = this.travelSpotCardData.findIndex(g => g.mainFilterId === item.mainFilterId);
                        // if not, create it
                        if (!existing) {

                            existing = {
                                mainFilterId: item.mainFilterId,
                                mainFilterName: item.mainFilterName,
                                travelSpots:[]
                            };

                            let spotData = {
                                            spotId: item.spotId,
                                            spotName: item.spotName,
                                            overview: item.overview,
                                            totalReviews: item.totalReviews,
                                            averageRating: item.averageRating,
                                            subFilters:[],
                                            photoPaths:[]
                                        };
                            let subFilterData = {subFilterId:item.subFilterId, subFilterName: item.subFilterName};
                            let photoPath = {photo1:item.photoPath};
                            spotData.subFilters.push(subFilterData);
                            spotData.photoPaths.push(photoPath);
                            existing.travelSpots.push(spotData);
                            this.travelSpotCardData.push(existing); //pushing to the main array

                        } else {

                            let existingSpot  = existing.travelSpots.find(g => g.spotId === item.spotId);
                            let existingIndex = existing.travelSpots.findIndex(g => g.spotId === item.spotId); //existing index of the spot
                            if(!existingSpot){
                                
                                existingSpot =  {
                                                    spotId: item.spotId,
                                                    spotName: item.spotName,
                                                    overview: item.overview,
                                                    totalReviews: item.totalReviews,
                                                    averageRating: item.averageRating,
                                                    subFilters:[],
                                                    photoPaths:[]
                                                };
                                let subFilterData = {subFilterId:item.subFilterId, subFilterName: item.subFilterName};
                                existingSpot.subFilters.push(subFilterData);
                                existingSpot.photoPaths.push(item.photoPath);
                                this.travelSpotCardData[index].travelSpots.push(existingSpot);

                            } else {
                                let subFilterData;

                                let existSubFilter = this.travelSpotCardData[index].travelSpots[existingIndex].subFilters.find(filter => filter.subFilterId===item.subFilterId);
                                let existPhotoPath = this.travelSpotCardData[index].travelSpots[existingIndex].photoPaths.find(photoPath => photoPath === item.photoPath);

                                if(!existSubFilter){
                                    subFilterData = {subFilterId:item.subFilterId, subFilterName: item.subFilterName};
                                    this.travelSpotCardData[index].travelSpots[existingIndex].subFilters.push(subFilterData);
                                }

                                if(!existPhotoPath){
                                    this.travelSpotCardData[index].travelSpots[existingIndex].photoPaths.push(item.photoPath);
                                }
                            }
                        }
                    });

                //console.log(this.travelSpotCardData);
                this.renderTravelSpotCards();

                } else {
                    console.error('Failed to load travel spot cards :', data.message);
                    alert('Failed to load travel spot cards: ' + data.message);
                }
                    
            } catch (error) {
                console.error('Error loading travel spot cards:', error);
                alert('EError loading travel spot cards ' + error.message);
            }  
        }

        renderTravelSpotCards(){

            this.travelSpotCardSection.innerHTML = this.travelSpotCardData.map( mainFilter => `
                
                <section class="trending-section">
                    <h2 class="section-title-cards">${mainFilter.mainFilterName}</h2>
                    <button class="see-more-arrow" data-category="culture" title="See More ${mainFilter.mainFilterName}">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <div class="trending-places-grid">
                        ${mainFilter.travelSpots.map(spot =>
                                `<div class="place-card">
                                    <div class="place-image">
                                        <img src="<?php echo IMG_ROOT; ?>/explore/destinations/anuradhapura.png" alt="Anuradhapura">
                                    </div>
                                    <div class="place-info">
                                        <h3 class="place-title">${spot.spotName}</h3>
                                        <span class="place-category">${mainFilter.mainFilterName}</span>
                                        <div class="place-rating">
                                            <span class="star">★</span>
                                            <span class="rating-value">${spot.averageRating}(${spot.totalReviews})</span>
                                        </div>
                                        <p class="place-description">${spot.overview}</p>
                                        <div class="place-actions">
                                            <button type="button" class="btn btn-edit"onclick='travelSpotManager.editTravelSpot(${spot.spotId},event)' >Edit</button>
                                            <button type="button" class="btn btn-delete">Delete</button>
                                            <button type="button" class="btn btn-view">View</button>
                                        </div>
                                    </div>
                                </div>`
                            ).join('')
                        }
                    </div>
                </section>
            `).join('');
        }

        async editTravelSpot(travelSpotId,event){
            event.preventDefault();
            console.log("editing travel spot..",travelSpotId);

            try{
                const travelSpotData = await fetch(this.URL_ROOT + `/Moderator/getTravelSpotData/${travelSpotId}`);
                const data = await travelSpotData.json(); 

                if(data.success){

                    console.log(data.travelSpotData);
                    this.resetForm();

                    Object.keys(data.travelSpotData.mainDetails).forEach(detailKey => {
                        if(document.getElementById(detailKey)){
                            document.getElementById(detailKey).value =  data.travelSpotData.mainDetails[detailKey] || '';
                        } else {
                            console.warn(`Element with id ${detailKey} not found in the form.`);
                        }   
                    });

                    console.log("up to this point ok");

                    Object.keys(data.travelSpotData.filters).forEach( key => {
                        const mainFilterName = data.travelSpotData.filters[key].mainFilterName;

                        data.travelSpotData.filters[key].subFilters.forEach( subfilter => {
                            this.addSubfilter(subfilter.subFilterId, subfilter.subFilterName, mainFilterName);
                        });
                    });
                    console.log("subfilters added");
                    this.renderSelectedSubfilters();

                    data.travelSpotData.nearbySpots.forEach(spot => {
                        this.addNearbySpot(spot.spotId, spot.spotName);
                    });

                    for (let index = 0; index < data.travelSpotData.photos.length; index++) {
                        this.photoPreviews[index].innerHTML = `<img src="${this.URL_ROOT + '/uploads/' + data.travelSpotData.photos[index].photoPath}" alt="Photo ${index + 1}">`;
                    }

                    data.travelSpotData.itinerary.forEach( location => {
                        const locationData = { name:location.pointName,lat:location.latitude, lng:location.longitude}
                        this.addLocation(locationData); 
                    });
                    this.openPopup();
                    this.editingTravelSpot = travelSpotId; //setting the current editing travel spot
                }else{
                    alert('Error loading travel spot cards');
                    console.error('Error loading travel spot cards');
                }
            }catch(error){
                console.error('Error loading travel spot cards:', error);
                alert('Error loading travel spot cards ' + error.message);
            }
        }

        deleteTravelSpot(spotId, event){
            console.log('Deleting travel spot:', tripId);
            event,preventDefault();
        
            if (!confirm('Are you sure you want to delete this travel spot? This action cannot be undone.')) {
                return;
            }

            // Make delete request
            fetch(URL_ROOT + '/RegUser/deleteTravelSpot', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ spotId: spotId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Travel Spot deleted successfully!');
                    loadUserTrips(); // Reload trips
                } else {
                    alert('Error deleting Travel Spot: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the travel spot.');
            });
        }
    }

    // Initialize the travel spot manager
    window.TravelSpotManager = TravelSpotManager;
    window.travelSpotManager = new TravelSpotManager();

})();