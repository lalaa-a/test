(function(){
    // Guide Availability Calendar JavaScript
    if (window.GuideAvailabilityCalendarManager) {
        console.log('GuideAvailabilityCalendarManager already exists, cleaning up...');
        if (window.guideAvailabilityCalendarManager) {
            delete window.guideAvailabilityCalendarManager;
        }
        delete window.GuideAvailabilityCalendarManager;
    }

    class GuideAvailabilityCalendarManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentDate = new Date();
            this.selectedMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
            // Store time slots as Map: date -> Array of slot objects
            this.unavailableSlots = new Map();
            this.isLoading = false;

            this.init();
        }

        async init() {
            this.bindEvents();
            await this.loadAvailability();
            this.renderCalendar();
            this.updateStats();
        }

        bindEvents() {
            // Navigation buttons
            document.getElementById('prevMonthBtn').addEventListener('click', () => this.navigateMonth(-1));
            document.getElementById('nextMonthBtn').addEventListener('click', () => this.navigateMonth(1));

            // Quick selection buttons
            document.getElementById('selectWeekendsBtn').addEventListener('click', () => this.selectWeekends());
            document.getElementById('selectWeekdaysBtn').addEventListener('click', () => this.selectWeekdays());
            document.getElementById('clearSelectionBtn').addEventListener('click', () => this.clearSelection());

            // Time slot modal events
            document.getElementById('cancelSlotBtn').addEventListener('click', () => this.closeModal());
            document.getElementById('addSlotBtn').addEventListener('click', () => this.addTimeSlot());
            document.getElementById('saveSlotsBtn').addEventListener('click', () => this.saveAllSlots());

            // Reason change handler
            document.getElementById('slotReason').addEventListener('change', (e) => this.handleReasonChange(e.target.value));

            // Recurring checkbox handler
            document.getElementById('isRecurring').addEventListener('change', (e) => this.handleRecurringChange(e.target.checked));

            // Modal close
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal());
            });

            // Modal overlay click
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal();
                    }
                });
            });
        }

        async loadAvailability() {
            try {
                this.showLoading();
                const response = await fetch(`${this.URL_ROOT}/guide/getGuideUnavailability`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success && data.unavailableSlots) {
                    // Clear existing slots
                    this.unavailableSlots.clear();

                    // Group slots by date
                    data.unavailableSlots.forEach(slot => {
                        const dateKey = slot.unavailableDate;
                        if (!this.unavailableSlots.has(dateKey)) {
                            this.unavailableSlots.set(dateKey, []);
                        }
                        this.unavailableSlots.get(dateKey).push({
                            id: slot.id,
                            startTime: slot.startTime,
                            endTime: slot.endTime,
                            reason: slot.reason,
                            personalReason: slot.personalReason,
                            tripId: slot.tripId,
                            isRecurring: slot.isRecurring,
                            recurringDayOfWeek: slot.recurringDayOfWeek,
                            recurringEndDate: slot.recurringEndDate
                        });
                    });
                }
            } catch (error) {
                console.error('Error loading availability:', error);
                window.showNotification('Failed to load availability data', 'error');
            } finally {
                this.hideLoading();
            }
        }

        renderCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            const calendarTitle = document.getElementById('calendarTitle');

            // Clear existing calendar
            calendarGrid.innerHTML = '';

            // Update title
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            calendarTitle.textContent = `${monthNames[this.selectedMonth.getMonth()]} ${this.selectedMonth.getFullYear()}`;

            // Add day names
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayNames.forEach(dayName => {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day calendar-day-name';
                dayElement.textContent = dayName;
                calendarGrid.appendChild(dayElement);
            });

            // Calculate calendar dates - only show days within 90-day window
            const firstDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth(), 1);
            const lastDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth() + 1, 0);

            // Calculate 90-day window from today
            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 90);

            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const endDate = new Date(lastDay);
            endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));

            // Generate calendar days - only within 90-day window
            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayElement = document.createElement('div');
                const dateString = this.formatDate(currentDate);
                const isCurrentMonth = currentDate.getMonth() === this.selectedMonth.getMonth();
                const isToday = this.isToday(currentDate);
                const isPast = currentDate < this.currentDate && !isToday;
                const isBeyond90Days = currentDate > maxDate;
                const daySlots = this.unavailableSlots.get(dateString) || [];
                const hasSlots = daySlots.length > 0;
                const isFullyUnavailable = this.isDayFullyUnavailable(dateString);

                dayElement.className = 'calendar-day';
                dayElement.textContent = currentDate.getDate();
                dayElement.dataset.date = dateString;

                // Apply appropriate classes
                if (!isCurrentMonth || isBeyond90Days) {
                    dayElement.classList.add('disabled');
                    dayElement.classList.add('outside-window');
                } else if (isPast) {
                    dayElement.classList.add('past');
                } else if (isFullyUnavailable) {
                    dayElement.classList.add('unavailable');
                } else if (hasSlots) {
                    dayElement.classList.add('partial');
                } else {
                    dayElement.classList.add('available');
                }

                if (isToday) {
                    dayElement.classList.add('today');
                }

                // Add click handler only for current month days within 90-day window and not in the past
                if (isCurrentMonth && !isPast && !isBeyond90Days) {
                    dayElement.addEventListener('click', () => this.showTimeSlotModal(dateString));
                }

                calendarGrid.appendChild(dayElement);
                currentDate.setDate(currentDate.getDate() + 1);
            }

            this.updateNavigationButtons();
        }

        navigateMonth(direction) {
            const newMonth = new Date(this.selectedMonth);
            newMonth.setMonth(newMonth.getMonth() + direction);

            // Calculate 90-day window from today
            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 90);

            const minDate = new Date(this.currentDate);
            minDate.setDate(minDate.getDate() - 1);

            // Check if the new month has any days within the 90-day window
            const monthStart = new Date(newMonth.getFullYear(), newMonth.getMonth(), 1);
            const monthEnd = new Date(newMonth.getFullYear(), newMonth.getMonth() + 1, 0);

            // If the entire month is before today or after 90 days, don't allow navigation
            if (monthEnd < this.currentDate || monthStart > maxDate) {
                return;
            }

            this.selectedMonth = newMonth;
            this.renderCalendar();
        }

        updateNavigationButtons() {
            const prevBtn = document.getElementById('prevMonthBtn');
            const nextBtn = document.getElementById('nextMonthBtn');

            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 90);

            // Check if there's a previous month with valid days
            const prevMonth = new Date(this.selectedMonth);
            prevMonth.setMonth(prevMonth.getMonth() - 1);
            const prevMonthEnd = new Date(prevMonth.getFullYear(), prevMonth.getMonth() + 1, 0);
            const canGoPrev = prevMonthEnd >= this.currentDate;

            // Check if there's a next month with valid days
            const nextMonth = new Date(this.selectedMonth);
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            const nextMonthStart = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 1);
            const canGoNext = nextMonthStart <= maxDate;

            prevBtn.disabled = !canGoPrev;
            nextBtn.disabled = !canGoNext;
        }

        showTimeSlotModal(dateString) {
            const modal = document.getElementById('timeSlotModal');
            const dateInput = document.getElementById('slotDate');

            // Format date for display
            const date = new Date(dateString);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateInput.value = date.toLocaleDateString('en-US', options);

            // Store the selected date
            this.selectedDate = dateString;

            // Load existing slots for this date
            this.loadExistingSlots(dateString);

            // Reset form
            this.resetSlotForm();

            this.openModal(modal);
        }

        loadExistingSlots(dateString) {
            const slotsList = document.getElementById('slotsList');
            const daySlots = this.unavailableSlots.get(dateString) || [];

            slotsList.innerHTML = '';

            if (daySlots.length === 0) {
                slotsList.innerHTML = '<p style="color: #6c757d; font-style: italic;">No unavailable time slots for this date.</p>';
                return;
            }

            daySlots.forEach((slot, index) => {
                const slotElement = document.createElement('div');
                slotElement.className = 'slot-item';
                slotElement.innerHTML = `
                    <div>
                        <div class="slot-time">${this.formatTime(slot.startTime)} - ${this.formatTime(slot.endTime)}</div>
                        <div class="slot-reason">${this.getReasonText(slot)}</div>
                    </div>
                    <div class="slot-actions">
                        <button class="btn-remove-slot" onclick="window.guideAvailabilityCalendarManager.removeSlot('${dateString}', ${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                slotsList.appendChild(slotElement);
            });
        }

        getReasonText(slot) {
            if (slot.reason === 'personal') {
                return `Personal: ${slot.personalReason || 'No reason specified'}`;
            } else if (slot.reason === 'booked') {
                return `Booked for Trip #${slot.tripId || 'Unknown'}`;
            }
            return 'Unknown reason';
        }

        resetSlotForm() {
            document.getElementById('startTime').value = '';
            document.getElementById('endTime').value = '';
            document.getElementById('slotReason').value = 'personal';
            document.getElementById('slotPersonalReason').value = '';
            document.getElementById('slotTripId').value = '';
            document.getElementById('isRecurring').checked = false;
            document.getElementById('recurringEndDate').value = '';

            this.handleReasonChange('personal');
            this.handleRecurringChange(false);
        }

        handleReasonChange(reason) {
            const personalGroup = document.getElementById('personalReasonGroup');
            const tripGroup = document.getElementById('tripIdGroup');

            if (reason === 'personal') {
                personalGroup.style.display = 'block';
                tripGroup.style.display = 'none';
                document.getElementById('slotPersonalReason').required = true;
                document.getElementById('slotTripId').required = false;
            } else if (reason === 'booked') {
                personalGroup.style.display = 'none';
                tripGroup.style.display = 'block';
                document.getElementById('slotPersonalReason').required = false;
                document.getElementById('slotTripId').required = false; // Optional for booked
            }
        }

        handleRecurringChange(isRecurring) {
            const recurringOptions = document.getElementById('recurringOptions');
            recurringOptions.style.display = isRecurring ? 'block' : 'none';

            if (isRecurring) {
                // Set default end date to 3 months from selected date
                const selectedDate = new Date(this.selectedDate);
                const endDate = new Date(selectedDate);
                endDate.setMonth(endDate.getMonth() + 3);
                document.getElementById('recurringEndDate').value = this.formatDate(endDate);
            }
        }

        addTimeSlot() {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const reason = document.getElementById('slotReason').value;
            const personalReason = document.getElementById('slotPersonalReason').value;
            const tripId = document.getElementById('slotTripId').value;
            const isRecurring = document.getElementById('isRecurring').checked;
            const recurringEndDate = document.getElementById('recurringEndDate').value;

            // Validation
            if (!startTime || !endTime) {
                window.showNotification('Please select both start and end times', 'warning');
                return;
            }

            if (startTime >= endTime) {
                window.showNotification('End time must be after start time', 'warning');
                return;
            }

            if (reason === 'personal' && !personalReason.trim()) {
                window.showNotification('Please provide a personal reason', 'warning');
                return;
            }

            // Check for overlapping slots
            if (this.hasOverlappingSlot(this.selectedDate, startTime, endTime)) {
                window.showNotification('This time slot overlaps with an existing unavailable slot', 'warning');
                return;
            }

            // Add to local storage
            if (!this.unavailableSlots.has(this.selectedDate)) {
                this.unavailableSlots.set(this.selectedDate, []);
            }

            const newSlot = {
                startTime: startTime,
                endTime: endTime,
                reason: reason,
                personalReason: reason === 'personal' ? personalReason : null,
                tripId: reason === 'booked' ? (tripId ? parseInt(tripId) : null) : null,
                isRecurring: isRecurring,
                recurringDayOfWeek: isRecurring ? new Date(this.selectedDate).getDay() : null,
                recurringEndDate: isRecurring ? recurringEndDate : null,
                isNew: true // Mark as new for saving
            };

            this.unavailableSlots.get(this.selectedDate).push(newSlot);

            // Refresh the slots display
            this.loadExistingSlots(this.selectedDate);
            this.resetSlotForm();
            this.renderCalendar();
            this.updateStats();

            window.showNotification('Time slot added successfully', 'success');
        }

        async removeSlot(dateString, index) {
            const daySlots = this.unavailableSlots.get(dateString);
            if (!daySlots || !daySlots[index]) return;

            const slot = daySlots[index];

            // If the slot was persisted in the DB (has an id), delete it via API first
            if (slot.id) {
                try {
                    this.showLoading();
                    const response = await fetch(`${this.URL_ROOT}/guide/removeGuideUnavailabilitySlot`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ slotId: slot.id })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to remove slot');
                    }
                } catch (error) {
                    console.error('Error removing slot:', error);
                    window.showNotification('Failed to remove time slot. Please try again.', 'error');
                    return;
                } finally {
                    this.hideLoading();
                }
            }

            // Remove from local map
            daySlots.splice(index, 1);
            if (daySlots.length === 0) {
                this.unavailableSlots.delete(dateString);
            }
            this.loadExistingSlots(dateString);
            this.renderCalendar();
            this.updateStats();
            window.showNotification('Time slot removed', 'success');
        }

        hasOverlappingSlot(dateString, startTime, endTime) {
            const daySlots = this.unavailableSlots.get(dateString) || [];
            return daySlots.some(slot => {
                return (startTime < slot.endTime && endTime > slot.startTime);
            });
        }

        isDayFullyUnavailable(dateString) {
            const daySlots = this.unavailableSlots.get(dateString) || [];
            if (daySlots.length === 0) return false;

            // Check if slots cover the entire day (assuming 24 hours)
            // This is a simplified check - in reality, you'd need more complex logic
            // For now, if there are multiple slots or one long slot, consider it partially unavailable
            return daySlots.length > 2; // Simple heuristic
        }

        async saveAllSlots() {
            try {
                this.showLoading();

                // Collect all slots that need to be saved (new ones)
                const slotsToSave = [];
                this.unavailableSlots.forEach((daySlots, dateString) => {
                    daySlots.forEach(slot => {
                        if (slot.isNew) {
                            slotsToSave.push({
                                unavailableDate: dateString,
                                startTime: slot.startTime,
                                endTime: slot.endTime,
                                reason: slot.reason,
                                personalReason: slot.personalReason,
                                tripId: slot.tripId,
                                isRecurring: slot.isRecurring,
                                recurringEndDate: slot.recurringEndDate
                            });
                        }
                    });
                });

                if (slotsToSave.length === 0) {
                    window.showNotification('No new slots to save', 'info');
                    this.closeModal();
                    return;
                }

                console.log('Saving slots:', slotsToSave);

                const response = await fetch(`${this.URL_ROOT}/guide/addGuideUnavailability`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ slots: slotsToSave })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Mark slots as saved (remove isNew flag)
                    this.unavailableSlots.forEach((daySlots) => {
                        daySlots.forEach(slot => {
                            delete slot.isNew;
                        });
                    });

                    this.closeModal();
                    window.showNotification('All time slots saved successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to save slots');
                }
            } catch (error) {
                console.error('Error saving slots:', error);
                window.showNotification('Failed to save time slots. Please try again.', 'error');
            } finally {
                this.hideLoading();
            }
        }

        selectWeekends() {
            this.clearSelection();
            const startDate = new Date(this.currentDate);
            const endDate = new Date(this.currentDate);
            endDate.setDate(endDate.getDate() + 90);

            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday = 0, Saturday = 6
                    const dateString = this.formatDate(currentDate);
                    // Add a full day slot (9 AM to 5 PM as example)
                    if (!this.unavailableSlots.has(dateString)) {
                        this.unavailableSlots.set(dateString, []);
                    }
                    this.unavailableSlots.get(dateString).push({
                        startTime: '09:00',
                        endTime: '17:00',
                        reason: 'personal',
                        personalReason: 'Weekend availability selection',
                        tripId: null,
                        isRecurring: false,
                        isNew: true
                    });
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            this.renderCalendar();
            this.updateStats();
        }

        selectWeekdays() {
            this.clearSelection();
            const startDate = new Date(this.currentDate);
            const endDate = new Date(this.currentDate);
            endDate.setDate(endDate.getDate() + 90);

            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek >= 1 && dayOfWeek <= 5) { // Monday to Friday
                    const dateString = this.formatDate(currentDate);
                    // Add a full day slot (9 AM to 5 PM as example)
                    if (!this.unavailableSlots.has(dateString)) {
                        this.unavailableSlots.set(dateString, []);
                    }
                    this.unavailableSlots.get(dateString).push({
                        startTime: '09:00',
                        endTime: '17:00',
                        reason: 'personal',
                        personalReason: 'Weekday availability selection',
                        tripId: null,
                        isRecurring: false,
                        isNew: true
                    });
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            this.renderCalendar();
            this.updateStats();
        }

        clearSelection() {
            this.unavailableSlots.clear();
            this.renderCalendar();
            this.updateStats();
        }

        updateStats() {
            let totalSlots = 0;
            let totalHours = 0;

            this.unavailableSlots.forEach((daySlots) => {
                totalSlots += daySlots.length;
                daySlots.forEach(slot => {
                    const start = new Date(`2000-01-01T${slot.startTime}`);
                    const end = new Date(`2000-01-01T${slot.endTime}`);
                    const hours = (end - start) / (1000 * 60 * 60);
                    totalHours += hours;
                });
            });

            const availableSlots = 90 * 8 - totalHours; // Assuming 8 hours per day as "available"
            const percentage = Math.max(0, Math.round((availableSlots / (90 * 8)) * 100));

            document.getElementById('unavailableSlotsCount').textContent = totalSlots;
            document.getElementById('totalHoursCount').textContent = Math.round(totalHours);
            document.getElementById('availabilityPercentage').textContent = `${percentage}%`;
        }

        // Utility methods
        formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        isToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        }

        openModal(modal) {
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
            });
            document.body.style.overflow = 'auto';
        }

        showLoading() {
            this.isLoading = true;
            console.log('Loading...');
        }

        hideLoading() {
            this.isLoading = false;
            console.log('Loading complete');
        }
    }

    // Initialize the manager
    window.GuideAvailabilityCalendarManager = GuideAvailabilityCalendarManager;
    window.guideAvailabilityCalendarManager = new GuideAvailabilityCalendarManager();

})();