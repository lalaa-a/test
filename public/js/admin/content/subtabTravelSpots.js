
// Travel Spot Management 
(function() {

    // Check if TravelSpotManager already exists and clean up
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
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.allFilters = [];
            this.selectedSubfilters = [];
            this.selectedNearbySpots = [];
            this.itinerary = [];
            this.travelSpotCardData = [];
            this.editingTravelSpot = null;
            this.existingPhotos = [];
            this.pendingDeleteSpotId = null;

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
            this.filterChipContainer = document.getElementById('filter-chip-container');

            // Form elements
            this.form = document.getElementById('travel-spot-form');

            // Subfilter elements
            this.subfilterSearch = document.getElementById('subfilter-search');
            this.searchSubfilterBtn = document.getElementById('search-subfilter-btn');
            this.subfilterResults = document.getElementById('subfilter-results');
            this.selectedSubfiltersContainer = document.getElementById('selected-subfilters');

            // Photo elements - 10 photos
            this.photoInputs = [];
            this.photoPreviews = [];
            this.uploadedPhotos = [];
            
            for (let i = 1; i <= 10; i++) {
                this.photoInputs.push(document.getElementById(`photoUpload${i}`));
                this.photoPreviews.push(document.getElementById(`uploadPreview${i}`));
            }

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

            // Photo upload buttons
            document.querySelectorAll('.btn-upload-photo').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (input) input.click();
                });
            });

            // Photo uploads
            this.photoInputs.forEach((input, index) => {
                if (input) {
                    input.addEventListener('change', (e) => this.handlePhotoUpload(e, index));
                }
            });

            // Remove photo buttons
            document.querySelectorAll('.btn-remove-photo').forEach(btn => {
                btn.addEventListener('click', () => {
                    const slot = btn.getAttribute('data-slot');
                    this.removePhoto(slot);
                });
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

            // Delete confirmation modal events
            const cancelDeleteBtn = document.getElementById('cancelDeleteTravelSpotBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteTravelSpotBtn');

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', () => this.closeDeleteConfirmModal());
            }

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', () => this.confirmDeleteTravelSpot());
            }

            // ESC key handler for modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeDeleteConfirmModal();
                }
            });

            // Close confirmation modal when clicking outside
            const deleteConfirmModal = document.getElementById('deleteTravelSpotConfirmModal');
            if (deleteConfirmModal) {
                deleteConfirmModal.addEventListener('click', (e) => {
                    if (e.target === deleteConfirmModal) {
                        this.closeDeleteConfirmModal();
                    }
                });
            }
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
            this.existingPhotos = [];
            this.uploadedPhotos = [];
            this.itinerary = [];
            this.editingTravelSpot = null;

            // Reset photo previews
            this.photoPreviews.forEach((preview, index) => {
                const slot = index + 1;
                const isMainPhoto = slot === 1;
                preview.innerHTML = `
                    <div class="upload-placeholder">
                        <i class="fas fa-${isMainPhoto ? 'cloud-upload-alt' : 'image'}"></i>
                        <p>${isMainPhoto ? 'Main Photo' : 'Photo ' + slot}</p>
                        ${isMainPhoto ? '<span class="upload-hint">Recommended: 1200x800px</span>' : ''}
                    </div>
                `;
                
                // Hide remove buttons
                const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
                if (removeBtn) {
                    removeBtn.style.display = 'none';
                }
            });

            // Clear selected subfilters
            this.selectedSubfiltersContainer.innerHTML = '';
            this.subfilterResults.innerHTML = '';

            // Clear selected nearby spots
            this.selectedNearby.innerHTML = '';
            this.nearbyResults.innerHTML = '';
            
            // Clear itinerary container
            if (this.itineraryContainer) {
                this.itineraryContainer.innerHTML = '';
            }
        }

        async loadAllFilters() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Moderator/getAllFilters`);
                const data = await response.json();

                if (data.success) {
                    this.allFilters = data.allFilters;
                    console.log('All filters loaded for search functionality');
                    this.addFilterChips();
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
                window.showNotification('Please enter a search term', 'warning');
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
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                window.showNotification('Please select a valid image file (JPEG, JPG, or PNG).', 'error');
                event.target.value = '';
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                window.showNotification('File size must be less than 5MB.', 'error');
                event.target.value = '';
                return;
            }

            // Store the uploaded file
            this.uploadedPhotos[index] = file;

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = this.photoPreviews[index];
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo ${index + 1}" style="width: 100%; height: 100%; object-fit: cover;">`;
                
                // Show remove button
                const slot = index + 1;
                const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
                if (removeBtn) {
                    removeBtn.style.display = 'flex';
                }
            };
            reader.readAsDataURL(file);
        }

        removePhoto(slot) {
            const index = parseInt(slot) - 1;
            
            // Clear the file input
            if (this.photoInputs[index]) {
                this.photoInputs[index].value = '';
            }
            
            // Clear the uploaded photo
            this.uploadedPhotos[index] = null;
            
            // Reset preview
            const preview = this.photoPreviews[index];
            if (preview) {
                const isMainPhoto = slot === '1';
                preview.innerHTML = `
                    <div class="upload-placeholder">
                        <i class="fas fa-${isMainPhoto ? 'cloud-upload-alt' : 'image'}"></i>
                        <p>${isMainPhoto ? 'Main Photo' : 'Photo ' + slot}</p>
                        ${isMainPhoto ? '<span class="upload-hint">Recommended: 1200x800px</span>' : ''}
                    </div>
                `;
            }
            
            // Hide remove button
            const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
            if (removeBtn) {
                removeBtn.style.display = 'none';
            }
        }

        async searchNearbySpots() {
            const query = this.nearbySearch.value.trim();
            if (!query) {
                window.showNotification('Please enter a search term', 'warning');
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
                    <span class="remove-spot">
                        <i class="fas fa-times"></i>
                    </span>
                `;
                
                // Attach click event listener properly instead of inline onclick
                const removeBtn = spotElement.querySelector('.remove-spot');
                removeBtn.addEventListener('click', () => {
                    this.removeLocation(spot.lat, spot.lng);
                });
                
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

            // Add photos (all as files now, including existing ones)
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
                    window.showNotification(msg, 'success');
                    this.closePopup();
                    this.loadTravelSpotCards();
                } else {
                    window.showNotification('Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showNotification('An error occurred. Please try again.', 'error');
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
            const photo1 = document.getElementById('photoUpload1').files[0];

            if (!spotName) {
                window.showNotification('Please enter a spot name', 'warning');
                return false;
            }

            if (!province) {
                window.showNotification('Please select a province', 'warning');
                return false;
            }

            if (!district) {
                window.showNotification('Please select a district', 'warning');
                return false;
            }

            if (!bestTimeFrom || !bestTimeTo) {
                window.showNotification('Please select best visiting times', 'warning');
                return false;
            }

            if (!visitingDurationMax) {
                window.showNotification('Please enter visiting duration', 'warning');
                return false;
            }

            // Check at least the first photo (main photo) is uploaded
            if (!photo1 && !this.editingTravelSpot) {
                window.showNotification('Please upload at least the main photo (Photo 1)', 'warning');
                return false;
            }

            if (this.selectedSubfilters.length === 0) {
                window.showNotification('Please select at least one subfilter', 'warning');
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
                            //let photoPath = {photo1:item.photoPath};
                            spotData.subFilters.push(subFilterData);
                            spotData.photoPaths.push(item.photoPath);
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
                    window.showNotification('Failed to load travel spot cards: ' + data.message, 'error');
                }
                    
            } catch (error) {
                console.error('Error loading travel spot cards:', error);
                window.showNotification('Error loading travel spot cards: ' + error.message, 'error');
            }  
        }

        renderTravelSpotCards(){

            console.log("rendering travel spot cards..", this.travelSpotCardData);

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
                                        <img src="${this.UP_ROOT + spot.photoPaths[0] || ''}" alt="${spot.spotName}">
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
                                            <button type="button" class="btn btn-delete" onclick='travelSpotManager.deleteTravelSpot(${spot.spotId},event)'>Delete</button>
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
                        const photoUrl = this.UP_ROOT + data.travelSpotData.photos[index].photoPath;
                        this.photoPreviews[index].innerHTML = `<img src="${photoUrl}" alt="Photo ${index + 1}">`;
                        
                        // Load existing image as File object into input
                        this.loadExistingImageToInput(photoUrl, index);
                    }

                    data.travelSpotData.itinerary.forEach( location => {
                        const locationData = { name:location.pointName,lat:location.latitude, lng:location.longitude}
                        this.addLocation(locationData); 
                    });
                    this.openPopup();
                    this.editingTravelSpot = travelSpotId; //setting the current editing travel spot
                }else{
                    window.showNotification('Error loading travel spot data', 'error');
                    console.error('Error loading travel spot data');
                }
            }catch(error){
                console.error('Error loading travel spot data:', error);
                window.showNotification('Error loading travel spot data: ' + error.message, 'error');
            }
        }

        deleteTravelSpot(spotId, event){
            console.log('Deleting travel spot:', spotId);
            event.preventDefault();
        
            // Show confirmation modal instead of browser confirm
            this.pendingDeleteSpotId = spotId;
            this.showDeleteConfirmModal();
        }

        showDeleteConfirmModal() {
            const modal = document.getElementById('deleteTravelSpotConfirmModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeDeleteConfirmModal() {
            const modal = document.getElementById('deleteTravelSpotConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingDeleteSpotId = null;
            }
        }

        async confirmDeleteTravelSpot() {
            if (!this.pendingDeleteSpotId) {
                window.showNotification('No travel spot selected for deletion', 'error');
                return;
            }

            const spotId = this.pendingDeleteSpotId;
            this.closeDeleteConfirmModal();
            this.pendingDeleteSpotId = null;

            try {
                const response = await fetch(this.URL_ROOT + '/Moderator/deleteTravelSpot', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ spotId: spotId })
                });

                const result = await response.json();

                if (result.success) {
                    window.showNotification('Travel Spot deleted successfully!', 'success');
                    this.loadTravelSpotCards();
                } else {
                    window.showNotification('Error deleting Travel Spot: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showNotification('An error occurred while deleting the travel spot.', 'error');
            }
        }

        async loadExistingImageToInput(imageUrl, index) {
            try {
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                
                // Extract file extension from path
                const extension = imageUrl.split('.').pop();

                const mimeType = blob.type || `image/${extension}`;
                
                // Create a File object from the blob
                const file = new File([blob], `photo${index + 1}.${extension}`, { type: mimeType });
                
                // Create a DataTransfer object to set the files
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                
                // Set the files to the input
                this.photoInputs[index].files = dataTransfer.files;
                
                console.log(`Loaded existing photo ${index + 1} into input:`, file);
            } catch (error) {
                console.error(`Error loading existing photo ${index + 1}:`, error);
            }
        }

        addFilterChips(){
            console.log('add all filter chips',this.allFilters);

            this.allFilters.forEach(filter => {
                const filterChip = document.createElement('div');
                
                filterChip.className = 'filter-chip';
                filterChip.id = filter.mainFilterId;
                filterChip.dataset.filter = filter.mainFilterName;
                filterChip.innerHTML = `${filter.mainFilterName}`;
                this.filterChipContainer.appendChild(filterChip);
            });
        }


    }

    // Initialize the travel spot manager
    window.TravelSpotManager = TravelSpotManager;
    window.travelSpotManager = new TravelSpotManager();

})();