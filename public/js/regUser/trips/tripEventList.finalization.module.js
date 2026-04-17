(function () {
    function applyTripEventListFinalizationModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        TripEventListManager.prototype.updateTripRevisionState = function (tripMeta) {
            const meta = tripMeta || {};

            const nextRejectedDriverCount = Number(
                meta.rejectedDriverCount !== undefined
                    ? meta.rejectedDriverCount
                    : (this.rejectedDriverCount || 0)
            ) || 0;

            const nextRejectedGuideCount = Number(
                meta.rejectedGuideCount !== undefined
                    ? meta.rejectedGuideCount
                    : (this.rejectedGuideCount || 0)
            ) || 0;

            const nextRevisionMode = typeof meta.revisionMode === 'boolean'
                ? meta.revisionMode
                : (this.revisionMode || false);

            this.revisionMode = nextRevisionMode;
            this.rejectedDriverCount = nextRejectedDriverCount;
            this.rejectedGuideCount = nextRejectedGuideCount;
            this.eventChangesLocked = (this.tripStatus !== 'pending') || this.revisionMode;
        };

        TripEventListManager.prototype.canModifyEventStructure = async function (showAlert = false) {
            await this.fetchTripStatus();

            if (this.eventChangesLocked) {
                if (showAlert) {
                    alert('Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.');
                }
                return false;
            }

            return true;
        };

        TripEventListManager.prototype.canChangeDriverForSegment = async function (segmentIndex, showAlert = false) {
            await this.fetchTripStatus();

            if (this.assignmentsLocked) {
                if (showAlert) {
                    alert('Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.');
                }
                return false;
            }

            const rejectedDriverCount = Number(this.rejectedDriverCount || 0);
            const rejectedGuideCount = Number(this.rejectedGuideCount || 0);
            const targetDriverStatus = (this.selectedDrivers?.[segmentIndex]?.requestStatus || '').toLowerCase();

            if (rejectedGuideCount > 0 && rejectedDriverCount === 0) {
                if (showAlert) {
                    alert('Only rejected guide requests can be changed at this stage.');
                }
                return false;
            }

            if (rejectedDriverCount > 0 && targetDriverStatus !== 'rejected') {
                if (showAlert) {
                    alert('Only rejected driver requests can be changed right now.');
                }
                return false;
            }

            if (this.revisionMode && rejectedDriverCount === 0) {
                if (showAlert) {
                    alert('No rejected driver request is available to change right now.');
                }
                return false;
            }

            return true;
        };

        TripEventListManager.prototype.updateTripStatusState = function (status) {
            this.tripStatus = status || this.tripStatus;
            this.assignmentsLocked = this.tripStatus !== 'pending';
            if (this.tripStatus !== 'awPayment' && this.tripStatus !== 'scheduled') {
                this.tripPaymentSummary = null;
            }
            this.updateTripRevisionState();

            const statusConfig = {
                pending: {
                    label: 'Pending',
                    icon: 'fa-clock',
                    buttonText: 'Confirm Trip',
                    buttonDisabled: false,
                    buttonColor: ''
                },
                wConfirmation: {
                    label: 'Waiting Confirmation',
                    icon: 'fa-hourglass-half',
                    buttonText: 'Waiting Confirmations',
                    buttonDisabled: true,
                    buttonColor: '#6b7280'
                },
                awPayment: {
                    label: 'Awaiting Payment',
                    icon: 'fa-credit-card',
                    buttonText: 'Pay Now',
                    buttonDisabled: false,
                    buttonColor: '#d97706'
                },
                scheduled: {
                    label: 'Scheduled',
                    icon: 'fa-calendar-check',
                    buttonText: 'Scheduled',
                    buttonDisabled: true,
                    buttonColor: '#0f766e'
                },
                ongoing: {
                    label: 'Ongoing',
                    icon: 'fa-plane-departure',
                    buttonText: 'Ongoing',
                    buttonDisabled: true,
                    buttonColor: '#15803d'
                },
                completed: {
                    label: 'Completed',
                    icon: 'fa-check-circle',
                    buttonText: 'Completed',
                    buttonDisabled: true,
                    buttonColor: '#6b7280'
                }
            };

            const config = statusConfig[this.tripStatus] || {
                label: this.tripStatus || 'Pending',
                icon: 'fa-calendar-check',
                buttonText: this.assignmentsLocked ? 'Confirmed' : 'Confirm Trip',
                buttonDisabled: this.assignmentsLocked,
                buttonColor: this.assignmentsLocked ? '#6b7280' : ''
            };

            const statusBadge = document.getElementById('trip-status-badge');
            const statusText = document.getElementById('trip-status-text');
            if (statusBadge) {
                statusBadge.dataset.status = this.tripStatus || 'pending';

                const iconElement = statusBadge.querySelector('i');
                if (iconElement) {
                    iconElement.className = `fas ${config.icon}`;
                }
            }

            if (statusText) {
                statusText.textContent = config.label;
            }

            if (!this.confirmTripBtn) {
                return;
            }

            this.confirmTripBtn.textContent = config.buttonText;
            this.confirmTripBtn.disabled = config.buttonDisabled;
            this.confirmTripBtn.style.backgroundColor = config.buttonColor;
        };

        TripEventListManager.prototype.fetchTripStatus = async function () {
            const tripId = this.tripId?.textContent;
            if (!tripId) {
                return null;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/getTripDetails/${tripId}`);
                const result = await response.json();

                if (result.success && result.trip && result.trip.status) {
                    this.tripPaymentSummary = result.trip.paymentSummary || null;

                    const startDateRaw = result.trip.startDate ? String(result.trip.startDate).split(' ')[0] : null;
                    const endDateRaw = result.trip.endDate ? String(result.trip.endDate).split(' ')[0] : null;

                    if (startDateRaw && endDateRaw) {
                        const startDate = new Date(`${startDateRaw}T00:00:00`);
                        const endDate = new Date(`${endDateRaw}T00:00:00`);

                        if (!Number.isNaN(startDate.getTime()) && !Number.isNaN(endDate.getTime()) && endDate >= startDate) {
                            const daysDiff = Math.floor((endDate - startDate) / (24 * 60 * 60 * 1000)) + 1;
                            this.tripDurationDays = Math.max(1, daysDiff);
                        }
                    }

                    this.updateTripStatusState(result.trip.status);
                    this.updateTripRevisionState(result.trip);
                    return result.trip.status;
                }
            } catch (error) {
                console.error('Error fetching trip status:', error);
            }

            return this.tripStatus;
        };

        TripEventListManager.prototype.persistDriverSelections = async function () {
            const tripId = this.tripId?.textContent;
            if (!tripId) {
                return false;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/saveDriverRequests/${tripId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        selectedDrivers: this.selectedDrivers || {}
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    console.error('Failed to persist driver selections:', result.message);
                    alert(result.message || 'Failed to save driver selection');
                    return false;
                }

                return true;
            } catch (error) {
                console.error('Error persisting driver selections:', error);
                alert('Failed to save driver selection. Please try again.');
                return false;
            }
        };

        // Confirm trip
        TripEventListManager.prototype.loadExistingDrivers = async function () {
            try {
                const tripId = this.tripId?.textContent;
                if (!tripId) {
                    console.log('Trip ID not available yet, drivers will be loaded later');
                    return;
                }

                await this.fetchTripStatus();

                const response = await fetch(`${this.URL_ROOT}/RegUser/getDriverRequests/${tripId}`);
                const result = await response.json();

                if (result.success && result.tripStatus) {
                    this.updateTripStatusState(result.tripStatus);
                }

                if (result.success && result.drivers && Object.keys(result.drivers).length > 0) {
                    console.log('Loaded existing drivers from database:', result.drivers);
                    this.selectedDrivers = result.drivers;
                    console.log('Drivers loaded into this.selectedDrivers:', this.selectedDrivers);
                } else {
                    console.log('No existing drivers found for this trip');
                }
            } catch (error) {
                console.error('Error loading existing drivers:', error);
                // Continue without drivers, user can add them
            }
        };

        TripEventListManager.prototype.confirmTrip = async function () {
            await this.fetchTripStatus();

            if (this.tripStatus === 'awPayment') {
                if (typeof this.startTripPayment === 'function') {
                    await this.startTripPayment();
                } else {
                    alert('Trip is awaiting payment. Payment action is unavailable right now.');
                }
                return;
            }

            if (this.assignmentsLocked) {
                alert('Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.');
                return;
            }

            if (Number(this.rejectedDriverCount || 0) > 0 || Number(this.rejectedGuideCount || 0) > 0) {
                alert('Trip cannot be confirmed while rejected driver or guide requests exist. Please replace rejected requests first.');
                return;
            }

            if (!confirm('Are you sure you want to confirm this trip? It will move to waiting confirmation until the driver and booked guides accept.')) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/confirmTrip/${this.tripId.textContent}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        selectedDrivers: this.selectedDrivers
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message || 'Trip confirmed successfully!');

                    this.updateTripStatusState(result.status || 'wConfirmation');

                    this.hideTripSummary();
                    // Optionally redirect or update UI
                } else {
                    alert('Error confirming trip: ' + result.message);
                }
            } catch (error) {
                console.error('Error confirming trip:', error);
                alert('Error confirming trip: ' + error.message);
            }
        };

        TripEventListManager.prototype.removeDriverSelection = async function (segmentIndex) {
            if (!(await this.canChangeDriverForSegment(segmentIndex, true))) {
                return;
            }

            if (!this.selectedDrivers || !this.selectedDrivers.hasOwnProperty(segmentIndex)) {
                return;
            }

            if (!confirm('Remove selected driver from this segment?')) {
                return;
            }

            const previousDrivers = { ...this.selectedDrivers };
            delete this.selectedDrivers[segmentIndex];

            if (typeof this.persistDriverSelections === 'function') {
                const saved = await this.persistDriverSelections();
                if (!saved) {
                    this.selectedDrivers = previousDrivers;
                    return;
                }
            }

            await this.showTripSummary();
        };

        // Display start and end events in footer
        TripEventListManager.prototype.displayStartEndEvents = async function () {
            const container = this.summaryStartEndContainer;
            let eventsHTML = '';
            await this.fetchTripStatus();
            const timelineDayCount = document.querySelectorAll('.date-nav-item:not(.finalize-item)').length;
            const tripDurationDays = (Number.isFinite(Number(this.tripDurationDays)) && Number(this.tripDurationDays) > 0)
                ? Number(this.tripDurationDays)
                : Math.max(1, timelineDayCount || 1);
            const canModifyAssignments = !this.assignmentsLocked && this.tripStatus === 'pending';
            const rejectedDriverCount = Number(this.rejectedDriverCount || 0);
            const rejectedGuideCount = Number(this.rejectedGuideCount || 0);
            const isGuideOnlyRevision = this.tripStatus === 'pending' && rejectedGuideCount > 0 && rejectedDriverCount === 0;
            const isDriverRevisionMode = this.tripStatus === 'pending' && rejectedDriverCount > 0;

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
                                        const driverRequestStatus = (driverData?.requestStatus || '').toLowerCase();
                                        const driverStatusMeta = {
                                            pending: { label: 'Pending', className: 'pending' },
                                            requested: { label: 'Requested', className: 'pending' },
                                            accepted: { label: 'Accepted', className: 'accepted' },
                                            rejected: { label: 'Rejected', className: 'rejected' }
                                        }[driverRequestStatus] || { label: 'Pending', className: 'pending' };
                                        const revisionLocksDriver = this.revisionMode && rejectedDriverCount === 0;
                                        const canChangeDriver = canModifyAssignments
                                            && !isGuideOnlyRevision
                                            && !revisionLocksDriver
                                            && (!isDriverRevisionMode || driverRequestStatus === 'rejected');

                                        const lockedDriverReason = !canModifyAssignments
                                            ? 'Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver changes are locked.'
                                            : ((isGuideOnlyRevision || revisionLocksDriver)
                                                ? 'Only rejected guide requests can be changed right now.'
                                                : 'Only rejected driver requests can be changed right now.');

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
                                            const dayFeeForTrip = driverFee * tripDurationDays;
                                            const kmFee = parseFloat(driverData.totalChargePerKm) || 0;
                                            feeBreakdown.push({
                                                type: `Per Day x ${tripDurationDays} Day${tripDurationDays === 1 ? '' : 's'}`,
                                                amount: dayFeeForTrip,
                                                formatted: `${driverData.currencySymbol || ''}${dayFeeForTrip.toFixed(2)}`
                                            });
                                            feeBreakdown.push({
                                                type: 'Per Km',
                                                amount: kmFee,
                                                formatted: driverData.formattedChargePerKm || `${driverData.currencySymbol}${kmFee.toFixed(2)}`
                                            });
                                            totalFees = dayFeeForTrip; // Show trip-day adjusted charge
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
                                                                ${driverData && driverData.requestStatus ? `<div class="driver-status-badge status-${driverStatusMeta.className}">${driverStatusMeta.label}</div>` : ''}
                                                            </div>
                                                            <button class="change-driver-btn" onclick="window.tripEventListManager.openDriverSelection(${index})" title="${canChangeDriver ? 'Change Driver' : lockedDriverReason}" ${canChangeDriver ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                            ${canChangeDriver ? `
                                                                <button class="change-driver-btn" onclick="window.tripEventListManager.removeDriverSelection(${index})" title="Remove Driver">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            ` : ''}
                                                        </div>
                                                    ` : `
                                                        <div class="driver-empty">
                                                            <div class="driver-status">
                                                                <i class="fas fa-user"></i>
                                                                <span class="driver-text">No driver selected</span>
                                                            </div>
                                                            <button class="select-driver-btn primary" onclick="window.tripEventListManager.openDriverSelection(${index})" ${(canModifyAssignments && !isGuideOnlyRevision && !revisionLocksDriver && (!isDriverRevisionMode || driverRequestStatus === 'rejected')) ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'}>
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
                                                                <img src="${this.UP_ROOT + vehicleInfo.vehiclePhoto || '/default-vehicle.png'}" alt="Vehicle Photo">
                                                            </div>
                                                            <div class="vehicle-info">
                                                                <div class="vehicle-model">${vehicleInfo.model}</div>
                                                                <div class="vehicle-details">${vehicleInfo.type} | ${vehicleInfo.capacity}</div>
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
        };

        // Show vehicle selection for a specific segment
        TripEventListManager.prototype.showVehicleSelection = function (segmentIndex) {
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
                        content: '[Type]';
                    }

                    .vehicle-capacity:before {
                        content: '[Seats]';
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
        };

        // Handle vehicle selection
        TripEventListManager.prototype.selectVehicle = function (segmentIndex, vehicleId, model, type, capacity) {
            // Remove the modal
            document.querySelector('.vehicle-selection-modal').remove();

            // In a real implementation, this would make an API call to save the vehicle selection
            console.log(`Selected vehicle for segment ${segmentIndex}: ${model} (${type})`);

            // Show success message
            this.showNotification(`Vehicle "${model}" selected for Segment ${segmentIndex + 1}`, 'success');

            // In a real implementation, you would refresh the segments display
            // For now, we'll just show a notification
        };

        // Show notification
        TripEventListManager.prototype.showNotification = function (message, type = 'info') {
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
        };

        TripEventListManager.prototype.setSummaryMapVisibility = function (showMap) {
            const routeMapSection = document.querySelector('.route-map-section');

            if (!this.eventsMapContainer || !routeMapSection) {
                return;
            }

            const shouldShowMap = Boolean(showMap);

            this.eventsMapContainer.classList.toggle('summary-map-visible', shouldShowMap);
            this.eventsMapContainer.classList.toggle('summary-active', !shouldShowMap);
            routeMapSection.style.display = shouldShowMap ? 'flex' : 'none';

            if (this.viewMapBtn) {
                if (shouldShowMap) {
                    this.viewMapBtn.innerHTML = '<i class="fas fa-list"></i> Hide Map';
                    this.viewMapBtn.title = 'Hide route map';
                } else {
                    this.viewMapBtn.innerHTML = '<i class="fas fa-map"></i> View Map';
                    this.viewMapBtn.title = 'View route map';
                }
            }

            if (!shouldShowMap) {
                return;
            }

            // Trigger map resize and recenter to Sri Lanka
            setTimeout(() => {
                if (this.routeMap && typeof google !== 'undefined' && google.maps) {
                    google.maps.event.trigger(this.routeMap, 'resize');
                    this.routeMap.setCenter({ lat: 7.8731, lng: 80.7718 });
                    this.routeMap.setZoom(8);
                }
            }, 100);
        };

        TripEventListManager.prototype.toggleMapView = function () {
            const isMapVisible = this.eventsMapContainer
                ? this.eventsMapContainer.classList.contains('summary-map-visible')
                : false;

            this.setSummaryMapVisibility(!isMapVisible);
        };
    }

    window.applyTripEventListFinalizationModule = applyTripEventListFinalizationModule;
})();