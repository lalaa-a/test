(function () {
    function applyTripEventListPopupModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        TripEventListManager.prototype.ensureTripPendingForAssignments = async function () {
            const tripId = this.tripId?.textContent;

            if (!tripId) {
                alert('Trip ID not found. Please try again.');
                return false;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/getTripDetails/${tripId}`);
                const result = await response.json();

                if (result.success && result.trip && result.trip.status) {
                    this.tripStatus = result.trip.status;
                    this.assignmentsLocked = this.tripStatus !== 'pending';

                    if (typeof this.updateTripRevisionState === 'function') {
                        this.updateTripRevisionState(result.trip);
                    } else {
                        this.revisionMode = !!result.trip.revisionMode;
                        this.rejectedDriverCount = Number(result.trip.rejectedDriverCount || 0);
                        this.rejectedGuideCount = Number(result.trip.rejectedGuideCount || 0);
                        this.eventChangesLocked = (this.tripStatus !== 'pending') || this.revisionMode;
                    }
                }
            } catch (error) {
                console.error('Error checking trip status:', error);
            }

            if (this.tripStatus && this.tripStatus !== 'pending') {
                alert('Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.');
                return false;
            }

            return true;
        };

        TripEventListManager.prototype.canChangeGuideForCurrentContext = async function (showAlert = false) {
            if (!(await this.ensureTripPendingForAssignments())) {
                return false;
            }

            const rejectedDriverCount = Number(this.rejectedDriverCount || 0);
            const rejectedGuideCount = Number(this.rejectedGuideCount || 0);
            const currentGuideStatus = (this.selectedGuide?.requestStatus || this.selectedGuide?.status || '').toLowerCase();

            if (rejectedDriverCount > 0 && rejectedGuideCount === 0) {
                if (showAlert) {
                    alert('Only rejected driver requests can be changed at this stage.');
                }
                return false;
            }

            if (rejectedGuideCount > 0 && currentGuideStatus !== 'rejected') {
                if (showAlert) {
                    alert('Only rejected guide requests can be changed right now.');
                }
                return false;
            }

            if (this.revisionMode && rejectedGuideCount === 0) {
                if (showAlert) {
                    alert('No rejected guide request is available to change right now.');
                }
                return false;
            }

            return true;
        };

        TripEventListManager.prototype.handleGuideSelection = async function (guideData) {

            if (!(await this.canChangeGuideForCurrentContext(true))) {
                return;
            }

            console.log("===== HANDLE GUIDE SELECTION CALLED =====");
            console.log("selected guide data:", guideData);

            const incomingGuideStatus = (guideData.requestStatus || guideData.status || '').toLowerCase();
            const normalizedGuideStatus = incomingGuideStatus === 'accepted' ? 'accepted' : 'pending';

            // Store selected guide data
            this.selectedGuide = {
                ...guideData,
                requestStatus: normalizedGuideStatus,
                status: normalizedGuideStatus
            };
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
            const parseChargeValue = (value) => {
                if (value === null || value === undefined) {
                    return 0;
                }

                if (typeof value === 'number') {
                    return Number.isFinite(value) ? value : 0;
                }

                const normalized = String(value).replace(/[^0-9.-]/g, '');
                const parsed = Number.parseFloat(normalized);
                return Number.isFinite(parsed) ? parsed : 0;
            };

            const charge = parseChargeValue(
                guideData.convertedCharge !== undefined ? guideData.convertedCharge : guideData.baseCharge
            );
            const rawChargeType = String(guideData.chargeType || 'per_day');
            const normalizedChargeType = rawChargeType.toLowerCase();
            const currencySymbol = guideData.currencySymbol || 'Rs ';
            const currency = guideData.currency || 'USD';

            let chargeHtml = '';

            if (normalizedChargeType === 'per_person' || normalizedChargeType === 'perperson') {
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
                                <span class="charge-value">${currencySymbol}${charge.toFixed(2)} x ${numberOfPeople}</span>
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
            } else if (normalizedChargeType === 'per_hour' || normalizedChargeType === 'perhour') {
                chargeHtml = `
                    <div class="guide-charge">
                        <span class="charge-label">Fee:</span>
                        <span class="charge-value">${currencySymbol}${charge.toFixed(2)} per hour</span>
                    </div>
                `;
            } else if (normalizedChargeType === 'per_day' || normalizedChargeType === 'perday') {
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

        };

        TripEventListManager.prototype.removeGuide = function () {
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
        };

        TripEventListManager.prototype.openGuideSelection = async function (spotId = null) {
            if (!(await this.canChangeGuideForCurrentContext(true))) {
                return;
            }

            // Get the current spot ID from the selected spot data
            const selectedSpotId = spotId || this.selectedSpot?.spotId || this.selectedSpot?.id;

            if (!selectedSpotId) {
                alert('Please select a travel spot first before adding a guide.');
                return;
            }

            // Open guide selection window
            const tripId = this.tripId?.textContent;
            const guideSelectUrl = `${this.URL_ROOT}/RegUser/guidesSelect/${selectedSpotId}?tripId=${encodeURIComponent(tripId)}`;
            window.open(guideSelectUrl, 'guideSelection', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        };

        TripEventListManager.prototype.handleDriverSelection = async function (driverData) {
            if (!(await this.ensureTripPendingForAssignments())) {
                return;
            }

            console.log("===== HANDLE DRIVER SELECTION CALLED =====");
            console.log("Selected driver data:", driverData);

            // Store selected driver data
            if (!this.selectedDrivers) {
                this.selectedDrivers = {};
            }

            // Use the stored segment index from when the window was opened
            const segmentIndex = driverData.segmentIndex !== undefined ? driverData.segmentIndex :
                (this.currentDriverSegmentIndex !== null ? this.currentDriverSegmentIndex : 0);

            if (typeof this.canChangeDriverForSegment === 'function' && !(await this.canChangeDriverForSegment(segmentIndex, true))) {
                return;
            }

            console.log(`Storing driver for segment index: ${segmentIndex}`);

            const hadPreviousDriver = Object.prototype.hasOwnProperty.call(this.selectedDrivers, segmentIndex);
            const previousDriver = hadPreviousDriver ? { ...this.selectedDrivers[segmentIndex] } : null;

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
                currencySymbol: driverData.currencySymbol,
                requestStatus: 'pending'
            };

            console.log("Driver stored in this.selectedDrivers:", this.selectedDrivers);
            console.log(`Total drivers stored: ${Object.keys(this.selectedDrivers).length}`);
            console.log(`Segments with drivers: ${Object.keys(this.selectedDrivers).join(', ')}`);

            if (typeof this.persistDriverSelections === 'function') {
                const saved = await this.persistDriverSelections();
                if (!saved) {
                    if (hadPreviousDriver) {
                        this.selectedDrivers[segmentIndex] = previousDriver;
                    } else {
                        delete this.selectedDrivers[segmentIndex];
                    }
                    return;
                }
            }

            // Clear the current segment index
            this.currentDriverSegmentIndex = null;

            // Refresh the trip summary to show the selected driver
            await this.showTripSummary();
        };

        TripEventListManager.prototype.openDriverSelection = async function (segmentIndex = 0) {
            const tripId = this.tripId?.textContent;

            if (!tripId) {
                alert('Trip ID not found. Please try again.');
                return;
            }

            if (!(await this.ensureTripPendingForAssignments())) {
                return;
            }

            if (typeof this.canChangeDriverForSegment === 'function' && !(await this.canChangeDriverForSegment(segmentIndex, true))) {
                return;
            }

            // Store the segment index for when the driver is selected
            const selectedDriverEntries = Object.values(this.selectedDrivers || {});
            const isDriverRevisionMode = this.tripStatus === 'pending' && selectedDriverEntries.some((driver) => {
                const status = (driver?.requestStatus || '').toLowerCase();
                return status === 'accepted' || status === 'rejected';
            });

            const currentDriverStatus = (this.selectedDrivers?.[segmentIndex]?.requestStatus || '').toLowerCase();
            if (isDriverRevisionMode && currentDriverStatus && currentDriverStatus !== 'rejected') {
                alert('Only rejected driver requests can be changed right now.');
                return;
            }

            this.currentDriverSegmentIndex = segmentIndex;

            console.log(`Opening driver selection for segment ${segmentIndex}`);

            // Open driver selection window with segment index in URL
            const driverSelectUrl = `${this.URL_ROOT}/RegUser/driversSelect/${tripId}?segment=${segmentIndex}`;
            window.open(driverSelectUrl, 'driverSelection', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        };

        TripEventListManager.prototype.closePopup = function () {
            this.addTravelSpotPopup.classList.remove('show');
            this.resetForm();
        };

        TripEventListManager.prototype.clearLocationAutocompleteInput = function () {
            const autocompleteInput = this.locationInputContainer || document.getElementById('location-input-container');
            if (!autocompleteInput) {
                return;
            }

            this.locationInputContainer = autocompleteInput;
            this.placeAutocomplete = autocompleteInput;

            if (typeof autocompleteInput.value !== 'undefined') {
                autocompleteInput.value = '';
            }

            autocompleteInput.setAttribute('value', '');
        };

        TripEventListManager.prototype.resetForm = function () {
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
            this.guideOnlyEditMode = false;
            this.spotTypeSelect.disabled = false;
            this.eventStatusSelect.disabled = false;
            this.locationDescription.disabled = false;
            this.startTimeInput.disabled = false;
            this.endTimeInput.disabled = false;

            if (this.startTimePicker) {
                this.startTimePicker.set('clickOpens', true);
            }

            if (this.endTimePicker) {
                this.endTimePicker.set('clickOpens', true);
            }

            this.clearLocationAutocompleteInput();
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

        };

        TripEventListManager.prototype.displayEventTypeData = function (event) {

            this.locationSelect.classList.remove('active');
            this.travelSpotSelect.classList.remove('active');

            if (event.target.value === "travelSpot") {
                this.travelSpotSelect.classList.add('active');

            } else if (event.target.value === 'location') {
                this.locationSelect.classList.add('active');

                // Initialize map after location UI becomes visible.
                setTimeout(() => {
                    this.initMap().catch((error) => {
                        console.error('Error initializing location map:', error);
                    });
                }, 100);
            }
        };

        // When the travelSpotsSelect page closes the data would be sent to here getSpotData
        TripEventListManager.prototype.getSpotData = async function (spotId) {
            console.log('Selected travel spot ID:', spotId);
            try {

                const response = await fetch(this.URL_ROOT + `/RegUser/retrieveSelectedSpot/${spotId}`);
                const data = await response.json();

                if (data.success) {
                    console.log(data.spotData);

                    const spotCardData = {
                        spotId: spotId,
                        spotName: data.spotData.mainDetails.spotName,
                        description: data.spotData.mainDetails.overview,
                        averageRating: data.spotData.mainDetails.averageRating,
                        itinerary: data.spotData.itinerary,
                    };
                    return spotCardData;

                } else {
                    console.error('Failed to load trips:', data.message);
                    alert('Failed to load trips: ' + data.message);
                }

            } catch (error) {
                console.error('Error loading trips:', error);
                alert('Error loading trips: ' + error.message);
            }
        };

        // When a spot selected it will display through this
        TripEventListManager.prototype.handleSpotSelection = async function (spotId) {

            const availableSpotCard = this.selectedSpotContainer.querySelector(".event-card-wrapper");
            if (availableSpotCard) {
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
                const parseChargeValue = (value) => {
                    if (value === null || value === undefined) {
                        return 0;
                    }

                    if (typeof value === 'number') {
                        return Number.isFinite(value) ? value : 0;
                    }

                    const normalized = String(value).replace(/[^0-9.-]/g, '');
                    const parsed = Number.parseFloat(normalized);
                    return Number.isFinite(parsed) ? parsed : 0;
                };

                const originalChargeType = String(this.selectedGuide.chargeType || 'whole_trip');
                const normalizedChargeType = originalChargeType.toLowerCase();
                const selectedGuidePeople = Number.parseInt(this.selectedGuide.numberOfPeople || this.numberOfPeople, 10);
                const numberOfPeople = Number.isFinite(selectedGuidePeople) && selectedGuidePeople > 0 ? selectedGuidePeople : 1;
                const unitCharge = parseChargeValue(
                    this.selectedGuide.convertedCharge !== undefined
                        ? this.selectedGuide.convertedCharge
                        : (this.selectedGuide.baseCharge !== undefined ? this.selectedGuide.baseCharge : this.selectedGuide.totalCharge)
                );
                const storedTotalCharge = parseChargeValue(this.selectedGuide.totalCharge);
                const computedTotalCharge = (normalizedChargeType === 'per_person' || normalizedChargeType === 'perperson')
                    ? unitCharge * numberOfPeople
                    : unitCharge;
                const totalCharge = computedTotalCharge > 0 ? computedTotalCharge : storedTotalCharge;

                const normalizedGuideStatus = String(this.selectedGuide.requestStatus || this.selectedGuide.status || 'pending').toLowerCase();
                const statusToRender = normalizedGuideStatus === 'accepted'
                    ? 'accepted'
                    : (normalizedGuideStatus === 'rejected' ? 'rejected' : 'pending');

                eventFormData.guideData = {
                    guideId: this.selectedGuide.guideId,
                    guideFullName: this.selectedGuide.fullName,
                    guideProfilePhoto: this.selectedGuide.profilePhoto,
                    guideAverageRating: this.selectedGuide.averageRating,
                    guideBio: this.selectedGuide.bio,
                    numberOfPeople: numberOfPeople,
                    chargeType: originalChargeType,
                    totalCharge: Number(totalCharge.toFixed(2)),
                    status: statusToRender
                };
            }

            this.selectedSpotContainer.appendChild(this.renderSelectedSpot(this.selectedSpot, true, eventFormData));
        };

        // For render a selected spot or a location (follow the spot object data pattern)
        TripEventListManager.prototype.renderSelectedSpot = function (spot, isPopup, eventFormData) {

            const selectedType = eventFormData.type; // 'travelSpot' or 'location'
            const selectedStatus = eventFormData.status || 'Intermediate'; // 'start', 'intermediate', or 'end'

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
            const showStartTime = selectedStatus !== 'end';
            const showEndTime = selectedStatus !== 'start';
            const formattedStartTime = isPopup
                ? (eventFormData.startTime || '')
                : this.formatTimeToAMPM(eventFormData.startTime);
            const formattedEndTime = isPopup
                ? (eventFormData.endTime || '')
                : this.formatTimeToAMPM(eventFormData.endTime);
            const addBelowBoundaryTime = selectedStatus === 'start'
                ? (eventFormData.startTime || '')
                : (eventFormData.endTime || '');
            const guideStatusForCard = (eventFormData?.guideData?.status || eventFormData?.guideData?.requestStatus || '').toLowerCase();
            const canModifyEventStructure = !this.eventChangesLocked;
            const canGuideOnlyEdit = this.eventChangesLocked && selectedType === 'travelSpot' && guideStatusForCard === 'rejected';
            const canOpenEdit = canModifyEventStructure || canGuideOnlyEdit;
            const editMenuTitle = canOpenEdit
                ? (canGuideOnlyEdit ? 'Change rejected guide' : 'Edit event')
                : 'Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.';

            let eventLocationMarkup = `<span>${this.escapeHtml(spot.spotName || 'Location not specified')}</span>`;
            if (selectedType === 'location') {
                const latitudeRaw = (eventFormData && eventFormData.latitude !== undefined && eventFormData.latitude !== null)
                    ? eventFormData.latitude
                    : (spot && spot.itinerary && spot.itinerary[0] ? spot.itinerary[0].latitude : null);
                const longitudeRaw = (eventFormData && eventFormData.longitude !== undefined && eventFormData.longitude !== null)
                    ? eventFormData.longitude
                    : (spot && spot.itinerary && spot.itinerary[0] ? spot.itinerary[0].longitude : null);

                const latitude = Number(latitudeRaw);
                const longitude = Number(longitudeRaw);

                if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
                    eventLocationMarkup = `<span>${this.escapeHtml(`Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`)}</span>`;
                }
            } else if (selectedType === 'travelSpot') {
                const travelSpotId = (eventFormData && eventFormData.travelSpotId)
                    ? eventFormData.travelSpotId
                    : (spot ? spot.spotId : null);

                if (travelSpotId) {
                    const destinationUrl = `${this.URL_ROOT}/destinations/${travelSpotId}`;
                    eventLocationMarkup = `<a href="${this.escapeHtml(destinationUrl)}" target="_blank" rel="noopener noreferrer">${this.escapeHtml(destinationUrl)}</a>`;
                }
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'event-card-wrapper';

            const card = document.createElement('div');
            card.className = 'event-card';
            card.dataset.type = selectedType;
            card.dataset.status = selectedStatus;
            card.innerHTML = `
                                <div class="event-time-section">
                                    ${showStartTime ? `<div class="time-label">START</div><div class="event-start-time">${this.escapeHtml(formattedStartTime)}</div>` : ''}
                                    ${showEndTime ? `<div class="time-label">END</div><div class="event-end-time">${this.escapeHtml(formattedEndTime)}</div>` : ''}
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
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.addEventAbove(${this.tripId.textContent},${eventFormData.eventId},'${eventFormData.startTime}')" title="${canModifyEventStructure ? 'Add event above' : 'Trip events are locked.'}" ${canModifyEventStructure ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                                                            <i class="fa-solid fa-arrow-up"></i> Add event above
                                                        </button>
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.addEventBelow(${this.tripId.textContent},${eventFormData.eventId},'${addBelowBoundaryTime}')" title="${canModifyEventStructure ? 'Add event below' : 'Trip events are locked.'}" ${canModifyEventStructure ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                                                            <i class="fa-solid fa-arrow-down"></i> Add event below
                                                        </button>
                                                        <button class="dot-menu-item edit" onclick="tripEventListManager.editEvent(${this.tripId.textContent},${eventFormData.eventId})" title="${editMenuTitle}" ${canOpenEdit ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                                                            <i class="fa-solid fa-pen-to-square"></i> ${canGuideOnlyEdit ? 'Change Rejected Guide' : 'Edit'}
                                                        </button>
                                                        <button class="dot-menu-item delete" onclick="tripEventListManager.deleteEvent(${this.tripId.textContent},${eventFormData.eventId})" title="${canModifyEventStructure ? 'Remove event' : 'Trip events are locked.'}" ${canModifyEventStructure ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                ` : ''}
                                        </div>
                                    </div>
                                    <p class="event-description">
                                        ${this.escapeHtml(selectedType === 'location' ? eventFormData.description : spot.description)}
                                    </p>
                                    <div class="event-details">
                                        <div class="event-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            ${eventLocationMarkup}
                                        </div>
                                        <div class="event-detail">
                                            <i class="fas fa-star"></i>
                                            <span>${this.escapeHtml(spot.averageRating || 'N/A')}/5</span>
                                        </div>
                                    </div>
                                    ${(selectedType === 'travelSpot') ? `
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
            if (isPopup && !this.guideOnlyEditMode) {
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-card-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
                removeBtn.onclick = () => {
                    wrapper.remove();
                    if (eventFormData.type === 'travelSpot') {
                        this.selectedGuide = null;
                        this.gotoTravelSpotsElement.style.display = 'block';
                    } else {
                        document.getElementById('autocomplete-container').style.display = 'block';
                    }
                };
                wrapper.appendChild(removeBtn);
            }
            return wrapper;
        };

        TripEventListManager.prototype.renderGuideSection = function (guideData, isPopup, spotId) {
            if (guideData && guideData.guideId) {
                const parseChargeValue = (value) => {
                    if (value === null || value === undefined) {
                        return 0;
                    }

                    if (typeof value === 'number') {
                        return Number.isFinite(value) ? value : 0;
                    }

                    const normalized = String(value).replace(/[^0-9.-]/g, '');
                    const parsed = Number.parseFloat(normalized);
                    return Number.isFinite(parsed) ? parsed : 0;
                };

                const rawGuideStatus = (guideData.status || guideData.requestStatus || 'pending').toLowerCase();
                const guideStatusMeta = {
                    pending: { label: 'Pending', className: 'status-pending' },
                    requested: { label: 'Requested', className: 'status-pending' },
                    accepted: { label: 'Accepted', className: 'status-accepted' },
                    rejected: { label: 'Rejected', className: 'status-rejected' },
                    notselected: { label: 'Not Selected', className: 'status-not-selected' }
                }[rawGuideStatus] || { label: 'Pending', className: 'status-pending' };

                const rejectedDriverCount = Number(this.rejectedDriverCount || 0);
                const rejectedGuideCount = Number(this.rejectedGuideCount || 0);

                let canChangeGuide = false;
                let changeGuideTitle = 'Guide changes are unavailable';

                if (!isPopup) {
                    canChangeGuide = false;
                } else if (this.assignmentsLocked) {
                    changeGuideTitle = 'Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.';
                } else if (rejectedDriverCount > 0 && rejectedGuideCount === 0) {
                    changeGuideTitle = 'Only rejected driver requests can be changed right now.';
                } else if (rejectedGuideCount > 0 && rawGuideStatus !== 'rejected') {
                    changeGuideTitle = 'Only rejected guide requests can be changed right now.';
                } else if (this.revisionMode && rejectedGuideCount === 0) {
                    changeGuideTitle = 'No rejected guide request is available to change right now.';
                } else if (rawGuideStatus === 'accepted') {
                    changeGuideTitle = 'Accepted guide requests cannot be changed';
                } else {
                    canChangeGuide = true;
                    changeGuideTitle = 'Change Guide';
                }

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

                const normalizedChargeType = String(guideData.chargeType || '').toLowerCase();
                const totalCharge = parseChargeValue(guideData.totalCharge);
                const guidePeopleCountRaw = Number.parseInt(guideData.numberOfPeople, 10);
                const guidePeopleCount = Number.isFinite(guidePeopleCountRaw) && guidePeopleCountRaw > 0 ? guidePeopleCountRaw : 1;
                const currencySymbol = guideData.currencySymbol || 'Rs ';

                let chargeText = `${currencySymbol}${totalCharge.toFixed(2)}`;
                if (normalizedChargeType === 'per_person' || normalizedChargeType === 'perperson') {
                    const perPersonCharge = guidePeopleCount > 0 ? (totalCharge / guidePeopleCount) : totalCharge;
                    chargeText = `${currencySymbol}${perPersonCharge.toFixed(2)} x ${guidePeopleCount} = ${currencySymbol}${totalCharge.toFixed(2)}`;
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
                                    <span class="guide-request-badge ${guideStatusMeta.className}">${guideStatusMeta.label}</span>
                                    <div class="guide-rating">
                                        <div class="stars">${starsHtml}</div>
                                        <span class="rating-value">${rating.toFixed(1)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="guide-charge">
                                <span class="charge-label">Fee:</span>
                                <span class="charge-value">${chargeText}</span>
                            </div>
                        </div>
                        ${isPopup ? `
                            <div class="guide-actions">
                                <button class="change-guide-btn" onclick="window.tripEventListManager.openGuideSelection(${spotId})" title="${changeGuideTitle}" ${canChangeGuide ? '' : 'disabled style="opacity:0.5;cursor:not-allowed;"'}>
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            // No guide selected
            return `
                <div class="guide-info guide-none">
                    <i class="fas fa-user-slash"></i>
                    <span>No guide added</span>
                </div>
                ${isPopup ? `
                    <button class="guide-booking-btn" onclick="window.tripEventListManager.openGuideSelection(${spotId})">
                        <i class="fas fa-plus"></i>
                        Add Guide
                    </button>
                ` : ''}
            `;
        };

        TripEventListManager.prototype.handleLocationSelection = function (locationData) {

            const availableLocationCard = this.selectedLocationContainer.querySelector(".event-card-wrapper");
            if (availableLocationCard) {
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
            this.clearLocationAutocompleteInput();
            document.getElementById('autocomplete-container').style.display = 'none';
        };
    }

    window.applyTripEventListPopupModule = applyTripEventListPopupModule;
})();