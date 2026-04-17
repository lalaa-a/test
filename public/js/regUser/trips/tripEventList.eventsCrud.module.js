(function () {
    function applyTripEventListEventsCrudModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        TripEventListManager.prototype.hasBoundaryStatusConflict = async function (eventStatus) {
            if (eventStatus !== 'start' && eventStatus !== 'end') {
                return false;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/getAllTripEvents/${this.tripId.textContent}`);
                const result = await response.json();

                if (!result.success || !Array.isArray(result.events)) {
                    return false;
                }

                const currentEventId = this.currentEditingEventId ? Number(this.currentEditingEventId) : null;

                return result.events.some((event) => {
                    if ((event.eventStatus || '').toLowerCase() !== eventStatus) {
                        return false;
                    }

                    if (currentEventId !== null && Number(event.eventId) === currentEventId) {
                        return false;
                    }

                    return true;
                });
            } catch (error) {
                console.error('Error validating boundary status uniqueness:', error);
                return false;
            }
        };

        TripEventListManager.prototype.saveEvent = async function () {

            console.log('===== SAVE EVENT CALLED =====');
            console.log('Current editing event ID:', this.currentEditingEventId);
            console.log('Selected guide:', this.selectedGuide);

            await this.fetchTripStatus?.();

            if (!this.currentEditingEventId && typeof this.canModifyEventStructure === 'function') {
                const canModifyEvents = await this.canModifyEventStructure(true);
                if (!canModifyEvents) {
                    return;
                }
            }

            const type = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            const locationDescription = this.locationDescription.value;

            // In revision mode, allow only rejected guide replacement without mutating event structure.
            if (this.currentEditingEventId && this.guideOnlyEditMode) {
                if (type !== 'travelSpot' || !this.selectedSpot || !this.selectedSpot.spotId) {
                    alert('Only rejected guide requests can be changed right now.');
                    return;
                }

                try {
                    const guideSaveResult = await this.saveGuideRequest(this.currentEditingEventId, this.selectedSpot.spotId);
                    if (!guideSaveResult?.success) {
                        alert(guideSaveResult?.message || 'Failed to update rejected guide request. Please try again.');
                        return;
                    }

                    await this.fetchTripStatus?.();
                    alert('Rejected guide request updated successfully.');
                    this.closePopup();
                } catch (error) {
                    console.error('Error updating rejected guide request:', error);
                    alert('Failed to update rejected guide request. Please try again.');
                } finally {
                    if (this.tripSummarySection.style.display !== 'block') {
                        this.loadEventCardsForDate(this.currentSelectedDate);
                    }
                    this.resetForm();
                }

                return;
            }

            if (!this.validateInput()) {
                return;
            }

            if (await this.hasBoundaryStatusConflict(eventStatus)) {
                alert(`Only one ${eventStatus} event is allowed for a trip. Please set other events as intermediate.`);
                this.eventStatusSelect.value = 'intermediate';
                this.eventStatusSelect.dispatchEvent(new Event('change'));
                return;
            }

            // Convert 12-hour format to 24-hour format strings (HH:MM) based on event status
            let startTime24 = null;
            let endTime24 = null;
            const toDbTime = (timeValue) => {
                if (!timeValue) {
                    return null;
                }

                return timeValue.length === 5 ? `${timeValue}:00` : timeValue;
            };

            if (eventStatus === 'start') {
                // Start event: only start time
                startTime24 = toDbTime(this.convertTo24Hour(this.startTimeInput.value));
                endTime24 = '23:59:00';
            } else if (eventStatus === 'end') {
                // End event: only end time
                endTime24 = toDbTime(this.convertTo24Hour(this.endTimeInput.value));
                startTime24 = endTime24;
            } else {
                // Intermediate: both times
                startTime24 = toDbTime(this.convertTo24Hour(this.startTimeInput.value));
                endTime24 = toDbTime(this.convertTo24Hour(this.endTimeInput.value));
            }

            console.log('Start time 24h:', startTime24);
            console.log('End time 24h:', endTime24);
            console.log('Event status:', eventStatus);

            let eventData = {
                eventDate: this.currentSelectedDate,
                eventType: type,
                eventStatus: eventStatus,
                tripId: this.tripId.textContent,
                startTime: startTime24,
                endTime: endTime24
            };

            if (type === 'location') {
                eventData.locationName = this.selectedLocation.spotName;
                eventData.latitude = this.selectedLocation.itinerary[0].latitude;
                eventData.longitude = this.selectedLocation.itinerary[0].longitude;
                eventData.description = locationDescription;

            } else if (type === 'travelSpot') {
                eventData.travelSpotId = this.selectedSpot.spotId;
            }

            let URL;
            let msg;
            let METHOD;

            if (this.currentEditingEventId) {

                METHOD = 'PUT';
                URL = `${this.URL_ROOT}/RegUser/editEvent/${this.currentEditingEventId}`;
                msg = `Event Edited to ${this.currentSelectedDate} Successfully.`;
            } else {
                METHOD = 'POST';
                URL = `${this.URL_ROOT}/RegUser/addEvent`;
                msg = `Event added to ${this.currentSelectedDate} Successfully.`;
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
                this.resetForm();
            }
        };

        TripEventListManager.prototype.saveGuideRequest = async function (eventId, travelSpotId) {
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

                const resolveTripPeopleCount = async () => {
                    try {
                        const tripId = this.tripId?.textContent;
                        if (!tripId) {
                            const fallbackLocalCount = Number.parseInt(this.numberOfPeople, 10);
                            return (Number.isFinite(fallbackLocalCount) && fallbackLocalCount > 0) ? fallbackLocalCount : 1;
                        }

                        const response = await fetch(`${this.URL_ROOT}/RegUser/getTripDetails/${tripId}`);
                        const tripResult = await response.json();
                        const fetchedCount = Number.parseInt(tripResult?.trip?.numberOfPeople, 10);
                        if (Number.isFinite(fetchedCount) && fetchedCount > 0) {
                            this.numberOfPeople = fetchedCount;
                            return fetchedCount;
                        }
                    } catch (error) {
                        console.warn('Could not refresh trip people count for guide fee calculation:', error);
                    }

                    const localCount = Number.parseInt(this.numberOfPeople, 10);
                    return (Number.isFinite(localCount) && localCount > 0) ? localCount : 1;
                };

                let persistedGuideStatus = 'notselected';
                let persistedChargeType = null;
                let persistedPeopleCount = 1;
                let persistedTotalCharge = 0;

                if (this.selectedGuide) {
                    console.log('  -> Guide IS selected, setting status to PENDING');
                    // Guide selected - save with status 'pending'
                    const originalChargeType = String(this.selectedGuide.chargeType || 'whole_trip');
                    const normalizedChargeType = originalChargeType.toLowerCase();
                    const numberOfPeople = await resolveTripPeopleCount();
                    const unitCharge = parseChargeValue(
                        this.selectedGuide.convertedCharge !== undefined
                            ? this.selectedGuide.convertedCharge
                            : (this.selectedGuide.baseCharge !== undefined ? this.selectedGuide.baseCharge : this.selectedGuide.totalCharge)
                    );
                    const totalCharge = (normalizedChargeType === 'per_person' || normalizedChargeType === 'perperson')
                        ? unitCharge * numberOfPeople
                        : unitCharge;
                    const safeTotalCharge = Number.isFinite(totalCharge) ? Number(totalCharge.toFixed(2)) : 0;
                    const currentGuideRequestStatus = (this.selectedGuide.requestStatus || this.selectedGuide.status || 'pending').toLowerCase();
                    const statusToSave = currentGuideRequestStatus === 'accepted' ? 'accepted' : 'pending';

                    guideRequestData.guideId = this.selectedGuide.guideId;
                    guideRequestData.guideFullName = this.selectedGuide.fullName;
                    guideRequestData.guideProfilePhoto = this.selectedGuide.profilePhoto;
                    guideRequestData.guideAverageRating = this.selectedGuide.averageRating;
                    guideRequestData.guideBio = this.selectedGuide.bio;
                    guideRequestData.chargeType = originalChargeType;
                    guideRequestData.numberOfPeople = numberOfPeople;
                    guideRequestData.totalCharge = safeTotalCharge;
                    guideRequestData.status = statusToSave;

                    persistedGuideStatus = statusToSave;
                    persistedChargeType = originalChargeType;
                    persistedPeopleCount = numberOfPeople;
                    persistedTotalCharge = safeTotalCharge;
                } else {
                    console.log('  -> Guide NOT selected, setting status to NOTSELECTED');
                    // No guide selected - save with status 'notSelected'
                    guideRequestData.guideId = null;
                    guideRequestData.guideFullName = null;
                    guideRequestData.guideProfilePhoto = null;
                    guideRequestData.guideAverageRating = null;
                    guideRequestData.guideBio = null;
                    guideRequestData.chargeType = null;
                    guideRequestData.numberOfPeople = this.numberOfPeople || 1;
                    guideRequestData.totalCharge = 0;
                    guideRequestData.status = 'notSelected';

                    persistedGuideStatus = 'notselected';
                    persistedChargeType = null;
                    persistedPeopleCount = this.numberOfPeople || 1;
                    persistedTotalCharge = 0;
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
                    if (result.message) {
                        alert(result.message);
                    }
                    return {
                        success: false,
                        message: result.message || 'Failed to save guide request'
                    };
                }

                const responsePeopleCount = Number.parseInt(result.numberOfPeople, 10);
                if (Number.isFinite(responsePeopleCount) && responsePeopleCount > 0) {
                    persistedPeopleCount = responsePeopleCount;
                }

                const responseChargeType = typeof result.chargeType === 'string' ? result.chargeType.trim() : '';
                if (responseChargeType !== '') {
                    persistedChargeType = responseChargeType;
                }

                const responseTotalCharge = parseChargeValue(result.totalCharge);
                if (Number.isFinite(responseTotalCharge) && responseTotalCharge >= 0) {
                    persistedTotalCharge = Number(responseTotalCharge.toFixed(2));
                }

                const responseStatus = typeof result.status === 'string' ? result.status.trim().toLowerCase() : '';
                if (responseStatus !== '') {
                    persistedGuideStatus = responseStatus;
                }

                if (this.selectedGuide) {
                    this.selectedGuide.requestStatus = persistedGuideStatus;
                    this.selectedGuide.status = persistedGuideStatus;
                    this.selectedGuide.chargeType = persistedChargeType || this.selectedGuide.chargeType;
                    this.selectedGuide.numberOfPeople = persistedPeopleCount;
                    this.selectedGuide.totalCharge = persistedTotalCharge;

                    if (persistedPeopleCount > 0) {
                        const normalizedPersistedChargeType = String(this.selectedGuide.chargeType || '').toLowerCase();
                        const perPersonAmount = (normalizedPersistedChargeType === 'per_person' || normalizedPersistedChargeType === 'perperson')
                            ? (persistedTotalCharge / persistedPeopleCount)
                            : persistedTotalCharge;
                        this.selectedGuide.convertedCharge = perPersonAmount;
                        this.selectedGuide.baseCharge = perPersonAmount;
                    }

                    this.numberOfPeople = persistedPeopleCount;
                }

                return {
                    success: true,
                    message: result.message || 'Guide request saved successfully'
                };
            } catch (error) {
                console.error('Error saving guide request:', error);
                return {
                    success: false,
                    message: 'Failed to save guide request'
                };
            }
        };

        TripEventListManager.prototype.loadEventCardsForDate = async function (eventDate) {
            try {
                const response = await fetch(this.URL_ROOT + `/RegUser/getEventCardsByDate/${this.tripId.textContent}/${eventDate}`);
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

                        if (card.eventType === 'travelSpot') {
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

                        } else if (card.eventType === 'location') {

                            const locationData = {
                                spotName: card.locationName,
                                description: card.description,
                                averageRating: null,
                                itinerary: [
                                    { latitude: card.latitude, longitude: card.longitude, pointId: null, pointName: card.locationName }
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
            } catch (error) {
                console.error('Error loading event cards:', error);
                alert('Error loading event cards: ' + error.message);
            } finally {
                // Update the route map after loading events (only if not in summary view)
                if (this.tripSummarySection.style.display !== 'block') {
                    await this.updateRouteMap(eventDate);
                }
            }
        };

        // Toggle event menu dropdown
        TripEventListManager.prototype.toggleEventMenu = function (event, eventId) {
            event.stopPropagation();

            // Close all other menus
            document.querySelectorAll('.dot-menu-dropdown.show').forEach(menu => {
                if (menu.id !== `event-menu-${eventId}`) {
                    menu.classList.remove('show');
                }
            });

            // Toggle current menu
            const menu = document.getElementById(`event-menu-${eventId}`);
            menu.classList.toggle('show');
        };

        // Edit event
        TripEventListManager.prototype.editEvent = async function (tripId, eventId) {
            console.log('Editing event:', eventId);

            await this.fetchTripStatus?.();

            try {
                const eventData = await fetch(this.URL_ROOT + `/RegUser/retrieveEventData/${tripId}/${eventId}`);
                const data = await eventData.json();
                if (data.success) {
                    const eventChangesLocked = !!this.eventChangesLocked;
                    console.log(data.eventData);
                    this.resetForm();

                    // Set currentEditingEventId AFTER resetForm so it doesn't get cleared
                    this.currentEditingEventId = eventId;
                    console.log('Set currentEditingEventId to:', this.currentEditingEventId);

                    let startHours, startMinutes, endHours, endMinutes;

                    if (data.eventData.eventStatus === 'start') {
                        this.endTimeInput.closest('.form-group').style.display = 'none';
                        [startHours, startMinutes] = data.eventData.startTime.split(':');
                    }

                    if (data.eventData.eventStatus === 'intermediate') {
                        [startHours, startMinutes] = data.eventData.startTime.split(':');
                        [endHours, endMinutes] = data.eventData.endTime.split(':');
                    }

                    if (data.eventData.eventStatus === 'end') {
                        this.startTimeInput.closest('.form-group').style.display = 'none';
                        [endHours, endMinutes] = data.eventData.endTime.split(':');
                    }

                    if (startHours !== undefined && startMinutes !== undefined) {
                        this.startTimePicker.setDate(`${startHours}:${startMinutes}`);
                    } else {
                        this.startTimePicker.clear();
                    }

                    if (endHours !== undefined && endMinutes !== undefined) {
                        this.endTimePicker.setDate(`${endHours}:${endMinutes}`);
                    } else {
                        this.endTimePicker.clear();
                    }

                    this.spotTypeSelect.value = data.eventData.eventType;
                    this.eventStatusSelect.value = data.eventData.eventStatus;

                    if (data.eventData.eventType === 'location') {
                        if (eventChangesLocked) {
                            alert('Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.');
                            this.currentEditingEventId = null;
                            this.guideOnlyEditMode = false;
                            return;
                        }

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
                                averageRating: null,
                                itinerary: [{ latitude: parseFloat(data.eventData.latitude), longitude: parseFloat(data.eventData.longitude), pointId: null, pointName: data.eventData.locationName }]
                            };

                            this.handleLocationSelection(this.selectedLocation);
                            this.locationInputContainer.value = '';
                        }, 100);

                    } else if (data.eventData.eventType === 'travelSpot') {
                        this.travelSpotSelect.classList.add('active');

                        // Load guide data for this event FIRST
                        let guideData = null;
                        try {
                            const guideResponse = await fetch(`${this.URL_ROOT}/RegUser/getGuideRequestByEventId/${eventId}`);
                            const guideResult = await guideResponse.json();
                            console.log('Loaded guide data for editing:', guideResult);

                            if (guideResult.success && guideResult.guideRequest && guideResult.guideRequest.guideId) {
                                const guideRequest = guideResult.guideRequest;
                                const savedChargeType = String(guideRequest.chargeType || 'whole_trip');
                                const normalizedSavedChargeType = savedChargeType.toLowerCase();
                                const savedPeopleCountRaw = Number.parseInt(guideRequest.numberOfPeople, 10);
                                const savedPeopleCount = Number.isFinite(savedPeopleCountRaw) && savedPeopleCountRaw > 0
                                    ? savedPeopleCountRaw
                                    : (Number.parseInt(this.numberOfPeople, 10) || 1);
                                const savedTotalChargeRaw = Number.parseFloat(guideRequest.totalCharge);
                                const savedTotalCharge = Number.isFinite(savedTotalChargeRaw) ? savedTotalChargeRaw : 0;
                                const savedUnitCharge = ((normalizedSavedChargeType === 'per_person' || normalizedSavedChargeType === 'perperson') && savedPeopleCount > 0)
                                    ? (savedTotalCharge / savedPeopleCount)
                                    : savedTotalCharge;
                                const normalizedSavedStatus = String(guideRequest.status || 'pending').toLowerCase();
                                const uiStatus = normalizedSavedStatus === 'accepted'
                                    ? 'accepted'
                                    : (normalizedSavedStatus === 'rejected' ? 'rejected' : 'pending');

                                this.numberOfPeople = savedPeopleCount;

                                // Reconstruct guide data from the saved request
                                this.selectedGuide = {
                                    guideId: guideRequest.guideId,
                                    fullName: guideRequest.guideFullName,
                                    profilePhoto: guideRequest.guideProfilePhoto,
                                    averageRating: guideRequest.guideAverageRating,
                                    bio: guideRequest.guideBio,
                                    convertedCharge: savedUnitCharge,
                                    baseCharge: savedUnitCharge,
                                    totalCharge: savedTotalCharge,
                                    chargeType: savedChargeType,
                                    numberOfPeople: savedPeopleCount,
                                    currency: this.selectedGuide?.currency || 'LKR',
                                    currencySymbol: this.selectedGuide?.currencySymbol || 'Rs',
                                    requestStatus: uiStatus,
                                    status: uiStatus
                                };
                                console.log('Set this.selectedGuide for editing:', this.selectedGuide);

                                const currentGuideStatus = normalizedSavedStatus;
                                if (eventChangesLocked && currentGuideStatus !== 'rejected') {
                                    alert('Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.');
                                    this.currentEditingEventId = null;
                                    this.guideOnlyEditMode = false;
                                    return;
                                }

                                this.guideOnlyEditMode = eventChangesLocked;
                            } else {
                                console.log('No guide found for this event');

                                if (eventChangesLocked) {
                                    alert('Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.');
                                    this.currentEditingEventId = null;
                                    this.guideOnlyEditMode = false;
                                    return;
                                }

                                this.guideOnlyEditMode = false;
                            }
                        } catch (error) {
                            console.error('Error loading guide data for edit:', error);

                            if (eventChangesLocked) {
                                alert('Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.');
                                this.currentEditingEventId = null;
                                this.guideOnlyEditMode = false;
                                return;
                            }

                            this.guideOnlyEditMode = false;
                        }

                        // Now handle spot selection - it will use this.selectedGuide
                        await this.handleSpotSelection(data.eventData.travelSpotId);

                        if (this.guideOnlyEditMode) {
                            this.spotTypeSelect.disabled = true;
                            this.eventStatusSelect.disabled = true;
                            this.locationDescription.disabled = true;
                            this.startTimeInput.disabled = true;
                            this.endTimeInput.disabled = true;

                            if (this.startTimePicker) {
                                this.startTimePicker.set('clickOpens', false);
                            }
                            if (this.endTimePicker) {
                                this.endTimePicker.set('clickOpens', false);
                            }
                        }
                    }
                    this.addTravelSpotPopup.classList.add('show');

                }
            } catch (error) {
                console.error('Error fetching event data:', error);
                alert('Error fetching event data: ' + error.message);
            }

        };

        // Delete event
        TripEventListManager.prototype.deleteEvent = async function (tripId, eventId) {
            console.log('Deleting event:', eventId, `From trip: ${tripId}`);

            if (typeof this.canModifyEventStructure === 'function') {
                const canModifyEvents = await this.canModifyEventStructure(true);
                if (!canModifyEvents) {
                    return;
                }
            }

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
        };

        TripEventListManager.prototype.addEventAbove = async function (tripId, selectedEventId, selectedEventStartTime) {
            console.log("add event above ", selectedEventStartTime);

            if (typeof this.canModifyEventStructure === 'function') {
                const canModifyEvents = await this.canModifyEventStructure(true);
                if (!canModifyEvents) {
                    return;
                }
            }

            this.resetForm();

            try {
                const aboveEventDetails = await fetch(this.URL_ROOT + `/RegUser/retrieveAboveEventEndTime/${tripId}/${selectedEventId}/${this.currentSelectedDate}`);
                const data = await aboveEventDetails.json();

                if (data.success && data.eventData) {
                    console.log("Above event data:", data.eventData);

                    // For start events, endTime is synthetic in DB. Use the effective end boundary.
                    const aboveEndTime = data.eventData.eventStatus === 'start'
                        ? data.eventData.startTime
                        : data.eventData.endTime;

                    if (!aboveEndTime) {
                        throw new Error('Above event time boundary is missing');
                    }

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
        };

        TripEventListManager.prototype.addEventBelow = async function (tripId, selectedEventId, selectedEventEndTime) {

            console.log("add event below ");

            if (typeof this.canModifyEventStructure === 'function') {
                const canModifyEvents = await this.canModifyEventStructure(true);
                if (!canModifyEvents) {
                    return;
                }
            }

            this.resetForm();

            try {

                const belowEventDetails = await fetch(this.URL_ROOT + `/RegUser/retrieveBelowEventStartTime/${tripId}/${selectedEventId}/${this.currentSelectedDate}`);
                const data = await belowEventDetails.json();

                if (data.success && data.eventData) {
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

                if (!selectedEventEndTime) {
                    throw new Error('Selected event time boundary is missing');
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
        };

        TripEventListManager.prototype.validateInput = function () {
            const startTime = this.startTimePicker.selectedDates[0];
            const endTime = this.endTimePicker.selectedDates[0];
            const type = this.spotTypeSelect.value;
            const eventStatus = this.eventStatusSelect.value;
            const locationDescription = this.locationDescription.value;

            // Validate time based on event status
            if (eventStatus === 'start') {
                // Start event: only start time required
                if (!startTime) {
                    alert("Please fill in Start time");
                    return false;
                }
            } else if (eventStatus === 'end') {
                // End event: only end time required
                if (!endTime) {
                    alert("Please fill in End time");
                    return false;
                }
            } else {
                // Intermediate: both times required
                if (!startTime) {
                    alert("Please fill in Start time");
                    return false;
                }
                if (!endTime) {
                    alert("Please fill in End time");
                    return false;
                }
                // Validate time order
                if (endTime < startTime) {
                    alert("End time must be later than start time");
                    return false;
                }
            }

            if (!type) {
                alert("Please fill in location spot type");
                return false;
            }
            if (!eventStatus) {
                alert("Please fill in location event status");
                return false;
            }

            if (type === 'location') {
                if (!locationDescription) {
                    alert("Please fill in location description");
                    return false;
                }

                if (!this.selectedLocation) {
                    alert("Please select a location");
                    return false;
                }
            }

            if (type === 'travelSpot') {
                if (!this.selectedSpot) {
                    alert("Please select a travel spot");
                    return false;
                }
            }
            console.log("validate input working");
            return true;
        };
    }

    window.applyTripEventListEventsCrudModule = applyTripEventListEventsCrudModule;
})();