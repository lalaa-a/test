(function () {
    function applyTripEventListSummaryModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        // Show trip summary for finalization
        TripEventListManager.prototype.showTripSummary = async function () {
            try {
                await this.fetchTripStatus();
                this.hideTripChargeSummary();

                // Remove active class from all date navigation items
                document.querySelectorAll('.date-nav-item').forEach(i => i.classList.remove('active'));

                // Hide events section, show summary section
                this.eventsSection.style.display = 'none';
                this.tripSummarySection.style.display = 'block';

                if (typeof this.setSummaryMapVisibility === 'function') {
                    this.setSummaryMapVisibility(false);
                } else {
                    this.eventsMapContainer.classList.remove('summary-map-visible');
                    this.eventsMapContainer.classList.add('summary-active');
                    const routeMapSection = document.querySelector('.route-map-section');
                    if (routeMapSection) {
                        routeMapSection.style.display = 'none';
                    }
                    if (this.viewMapBtn) {
                        this.viewMapBtn.innerHTML = '<i class="fas fa-map"></i> View Map';
                        this.viewMapBtn.title = 'View route map';
                    }
                }

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

                await this.renderTripChargeSummary();

                // Render all trip locations on map
                await this.renderAllTripLocationsOnMap();

            } catch (error) {
                console.error('Error loading trip summary:', error);
                alert('Error loading trip summary: ' + error.message);
            }
        };

        TripEventListManager.prototype.formatLkrAmount = function (value) {
            const numericValue = Number.parseFloat(value);
            const safeValue = Number.isFinite(numericValue) ? numericValue : 0;
            return `LKR ${safeValue.toLocaleString('en-LK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        };

        TripEventListManager.prototype.hideTripChargeSummary = function () {
            const card = document.getElementById('trip-charge-summary');
            if (card) {
                card.hidden = true;
            }
        };

        TripEventListManager.prototype.renderTripChargeSummary = async function () {
            const card = document.getElementById('trip-charge-summary');
            if (!card) {
                return;
            }

            const status = String(this.tripStatus || '').toLowerCase();
            if (status !== 'awpayment' && status !== 'scheduled') {
                this.hideTripChargeSummary();
                return;
            }

            const summary = this.tripPaymentSummary;
            if (!summary || typeof summary !== 'object') {
                this.hideTripChargeSummary();
                return;
            }

            const getNumeric = (amount) => {
                const parsed = Number.parseFloat(amount);
                return Number.isFinite(parsed) ? parsed : 0;
            };

            const driverChargeTotal = getNumeric(summary.driverChargeTotal);
            const guideChargeTotal = getNumeric(summary.guideChargeTotal);
            const providerChargeTotal = getNumeric(summary.providerChargeTotal || (driverChargeTotal + guideChargeTotal));
            const siteChargeTotal = getNumeric(summary.siteChargeTotal);
            const totalCharge = getNumeric(summary.totalCharge || (providerChargeTotal + siteChargeTotal));
            const driverBookingCount = Math.max(0, Math.trunc(getNumeric(summary.driverBookingCount)));
            const guideBookingCount = Math.max(0, Math.trunc(getNumeric(summary.guideBookingCount)));
            const driverBookingUnitCharge = getNumeric(summary.driverBookingUnitCharge);
            const guideBookingUnitCharge = getNumeric(summary.guideBookingUnitCharge);
            const driverBookingSiteCharge = getNumeric(summary.driverBookingSiteCharge);
            const guideBookingSiteCharge = getNumeric(summary.guideBookingSiteCharge);
            const serviceChargeRate = getNumeric(summary.serviceChargeRate);
            const serviceChargeAmount = getNumeric(summary.serviceChargeAmount);

            const totalLabel = document.getElementById('trip-total-charge-label');
            const stateBadge = document.getElementById('trip-charge-state');

            const paymentStatus = String(summary.paymentStatus || '').toLowerCase();
            const isPaid = status === 'scheduled' || paymentStatus === 'completed';

            if (totalLabel) {
                totalLabel.textContent = isPaid ? 'Total Paid' : 'Total Charge';
            }

            if (stateBadge) {
                stateBadge.textContent = isPaid ? 'Scheduled - Paid' : 'Awaiting Payment';
                stateBadge.classList.toggle('is-paid', isPaid);
                stateBadge.classList.toggle('is-pending', !isPaid);
            }

            const driverNode = document.getElementById('trip-driver-charges');
            const guideNode = document.getElementById('trip-guide-charges');
            const subTotalNode = document.getElementById('trip-sub-total');
            const siteNode = document.getElementById('trip-site-charges');
            const totalNode = document.getElementById('trip-total-charge');
            const driverBookingLabelNode = document.getElementById('trip-driver-booking-label');
            const driverBookingValueNode = document.getElementById('trip-driver-booking-charge');
            const guideBookingLabelNode = document.getElementById('trip-guide-booking-label');
            const guideBookingValueNode = document.getElementById('trip-guide-booking-charge');
            const serviceFeeLabelNode = document.getElementById('trip-service-fee-label');
            const serviceFeeValueNode = document.getElementById('trip-service-fee-charge');

            if (driverNode) {
                driverNode.textContent = this.formatLkrAmount(driverChargeTotal);
            }
            if (guideNode) {
                guideNode.textContent = this.formatLkrAmount(guideChargeTotal);
            }
            if (subTotalNode) {
                subTotalNode.textContent = this.formatLkrAmount(providerChargeTotal);
            }
            if (siteNode) {
                siteNode.textContent = this.formatLkrAmount(siteChargeTotal);
            }
            if (totalNode) {
                totalNode.textContent = this.formatLkrAmount(totalCharge);
            }

            if (driverBookingLabelNode) {
                driverBookingLabelNode.textContent = `Driver booking fee (${driverBookingCount} x ${this.formatLkrAmount(driverBookingUnitCharge)})`;
            }

            if (driverBookingValueNode) {
                driverBookingValueNode.textContent = this.formatLkrAmount(driverBookingSiteCharge);
            }

            if (guideBookingLabelNode) {
                guideBookingLabelNode.textContent = `Guide booking fee (${guideBookingCount} x ${this.formatLkrAmount(guideBookingUnitCharge)})`;
            }

            if (guideBookingValueNode) {
                guideBookingValueNode.textContent = this.formatLkrAmount(guideBookingSiteCharge);
            }

            if (serviceFeeLabelNode) {
                serviceFeeLabelNode.textContent = `Service fee (${serviceChargeRate.toFixed(2)}% of Sub Total)`;
            }

            if (serviceFeeValueNode) {
                serviceFeeValueNode.textContent = this.formatLkrAmount(serviceChargeAmount);
            }

            card.hidden = false;
        };

        // Render all trip locations on the map
        TripEventListManager.prototype.renderAllTripLocationsOnMap = async function () {
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
        };

        // Render trip summary HTML
        TripEventListManager.prototype.renderTripSummary = function (eventsByDate) {
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
        };

        // Render events for a specific day
        TripEventListManager.prototype.renderDayEvents = function (events) {
            return events.map(event => {
                const showStartTime = event.eventStatus !== 'end';
                const showEndTime = event.eventStatus !== 'start';
                const startTime = showStartTime ? this.formatTimeToAMPM(event.startTime) : '';
                const endTime = showEndTime ? this.formatTimeToAMPM(event.endTime) : '';
                let timeBlock = '';

                if (showStartTime && showEndTime) {
                    timeBlock = `${startTime}<br>to<br>${endTime}`;
                } else if (showStartTime) {
                    timeBlock = `${startTime}`;
                } else if (showEndTime) {
                    timeBlock = `${endTime}`;
                }

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
                    const normalizedGuideStatus = String(guide.status || '').toLowerCase();
                    const parsedGuideTotalCharge = Number.parseFloat(guide.totalCharge);
                    const safeGuideTotalCharge = Number.isFinite(parsedGuideTotalCharge) ? parsedGuideTotalCharge : 0;

                    if (normalizedGuideStatus === 'pending' || normalizedGuideStatus === 'requested' || normalizedGuideStatus === 'accepted' || normalizedGuideStatus === 'rejected') {
                        const statusMeta = {
                            pending: { text: 'Pending', className: 'guide-pending' },
                            requested: { text: 'Pending', className: 'guide-pending' },
                            accepted: { text: 'Accepted', className: 'guide-accepted' },
                            rejected: { text: 'Rejected', className: 'guide-rejected' }
                        };
                        const currentStatus = statusMeta[normalizedGuideStatus] || statusMeta.pending;
                        guideInfoHTML = `
                            <div class="summary-guide-info">
                                <div class="summary-guide-header">
                                    <i class="fas fa-user-tie"></i>
                                    <strong>Guide:</strong> ${this.escapeHtml(guide.guideFullName)}
                                    <span class="guide-status ${currentStatus.className}">${currentStatus.text}</span>
                                </div>
                                <div class="summary-guide-price">
                                    <i class="fas fa-dollar-sign"></i>
                                    <strong>Price:</strong> ${safeGuideTotalCharge.toFixed(2)} LKR
                                </div>
                            </div>
                        `;
                    } else if (normalizedGuideStatus === 'notselected') {
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
                            ${timeBlock}
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
        };

        // Toggle day summary collapse
        TripEventListManager.prototype.toggleDaySummary = function (dayIndex) {
            const eventsDiv = document.getElementById(`day-events-${dayIndex}`);
            const toggleIcon = document.getElementById(`toggle-${dayIndex}`);

            if (eventsDiv.classList.contains('collapsed')) {
                eventsDiv.classList.remove('collapsed');
                toggleIcon.classList.remove('collapsed');
            } else {
                eventsDiv.classList.add('collapsed');
                toggleIcon.classList.add('collapsed');
            }
        };

        // Hide trip summary
        TripEventListManager.prototype.hideTripSummary = function () {
            this.tripSummarySection.style.display = 'none';
            this.eventsSection.style.display = 'block';
            this.eventsMapContainer.classList.remove('summary-active');
            this.eventsMapContainer.classList.remove('summary-map-visible');

            const routeMapSection = document.querySelector('.route-map-section');
            if (routeMapSection) {
                routeMapSection.style.display = '';
            }

            if (this.viewMapBtn) {
                this.viewMapBtn.innerHTML = '<i class="fas fa-map"></i> View Map';
                this.viewMapBtn.title = 'View route map';
            }

            this.hideTripChargeSummary();
        };
    }

    window.applyTripEventListSummaryModule = applyTripEventListSummaryModule;
})();