(function(){
    // Availability Calendar JavaScript
    if (window.AvailabilityCalendarManager) {
        console.log('AvailabilityCalendarManager already exists, cleaning up...');
        if (window.availabilityCalendarManager) {
            delete window.availabilityCalendarManager;
        }
        delete window.AvailabilityCalendarManager;
    }

    class AvailabilityCalendarManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentDate = new Date();
            this.selectedMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
            this.unavailableDates = new Map(); // Store dates as YYYY-MM-DD -> {reason, personalReason, tripId}
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

            // Modal events
            // Personal reason modal events
            document.getElementById('cancelReasonBtn').addEventListener('click', () => this.closeModal());
            document.getElementById('saveReasonBtn').addEventListener('click', () => this.savePersonalReason());
            document.getElementById('removeUnavailableBtn').addEventListener('click', () => this.removeUnavailableDate());

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
                const response = await fetch(`${this.URL_ROOT}/driver/getDriverAvailability`, {
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

                if (data.success && data.unavailableDates) {
                    // Clear existing dates
                    this.unavailableDates.clear();

                    // Add dates from server with full information
                    data.unavailableDates.forEach(dateInfo => {
                        this.unavailableDates.set(dateInfo.date, {
                            reason: dateInfo.reason,
                            personalReason: dateInfo.personalReason,
                            tripId: dateInfo.tripId
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

            // Calculate calendar dates - only show days within 60-day window
            const firstDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth(), 1);
            const lastDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth() + 1, 0);

            // Calculate 60-day window from today
            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 60);

            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const endDate = new Date(lastDay);
            endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));

            // Generate calendar days - only within 60-day window
            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayElement = document.createElement('div');
                const dateString = this.formatDate(currentDate);
                const isCurrentMonth = currentDate.getMonth() === this.selectedMonth.getMonth();
                const isToday = this.isToday(currentDate);
                const isPast = currentDate < this.currentDate && !isToday;
                const isBeyond60Days = currentDate > maxDate;
                const isUnavailable = this.unavailableDates.has(dateString);

                dayElement.className = 'calendar-day';
                dayElement.textContent = currentDate.getDate();
                dayElement.dataset.date = dateString;

                // Apply appropriate classes
                if (!isCurrentMonth || isBeyond60Days) {
                    dayElement.classList.add('disabled');
                    dayElement.classList.add('outside-window');
                } else if (isPast) {
                    dayElement.classList.add('past');
                } else if (isUnavailable) {
                    dayElement.classList.add('unavailable');
                } else {
                    dayElement.classList.add('available');
                }

                if (isToday) {
                    dayElement.classList.add('today');
                }

                // Add click handler only for current month days within 60-day window and not in the past
                if (isCurrentMonth && !isPast && !isBeyond60Days) {
                    dayElement.addEventListener('click', () => this.toggleDay(dateString));
                }

                calendarGrid.appendChild(dayElement);
                currentDate.setDate(currentDate.getDate() + 1);
            }

            this.updateNavigationButtons();
        }

        navigateMonth(direction) {
            const newMonth = new Date(this.selectedMonth);
            newMonth.setMonth(newMonth.getMonth() + direction);

            // Calculate 60-day window from today
            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 60);

            const minDate = new Date(this.currentDate);
            minDate.setDate(minDate.getDate() - 1);

            // Check if the new month has any days within the 60-day window
            const monthStart = new Date(newMonth.getFullYear(), newMonth.getMonth(), 1);
            const monthEnd = new Date(newMonth.getFullYear(), newMonth.getMonth() + 1, 0);

            // If the entire month is before today or after 60 days, don't allow navigation
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
            maxDate.setDate(maxDate.getDate() + 60);

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

        toggleDay(dateString) {
            if (this.unavailableDates.has(dateString)) {
                // Show personal reason modal for removing unavailable date
                this.showPersonalReasonModal(dateString, 'remove');
            } else {
                // Show personal reason modal for adding unavailable date
                this.showPersonalReasonModal(dateString, 'add');
            }
        }

        showPersonalReasonModal(dateString, action) {
            const modal = document.getElementById('personalReasonModal');
            const dateInput = document.getElementById('reasonDate');
            const reasonTextarea = document.getElementById('personalReason');

            // Format date for display
            const date = new Date(dateString);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateInput.value = date.toLocaleDateString('en-US', options);

            // Store the date and action for later use
            this.pendingDate = dateString;
            this.pendingAction = action;

            // Update modal title and buttons based on action
            const modalTitle = modal.querySelector('h3');
            const saveBtn = document.getElementById('saveReasonBtn');
            const removeBtn = document.getElementById('removeUnavailableBtn');

            if (action === 'add') {
                modalTitle.innerHTML = '<i class="fas fa-comment"></i> Add Personal Reason';
                saveBtn.style.display = 'inline-flex';
                removeBtn.style.display = 'none';
                reasonTextarea.required = true;
                // Clear previous reason for new entries
                reasonTextarea.value = '';
            } else {
                modalTitle.innerHTML = '<i class="fas fa-comment"></i> Remove Unavailable Date';
                saveBtn.style.display = 'none';
                removeBtn.style.display = 'inline-flex';
                reasonTextarea.required = false;
                // Load existing reason for removal
                const dateInfo = this.unavailableDates.get(dateString);
                reasonTextarea.value = dateInfo ? dateInfo.personalReason || '' : '';
            }

            this.openModal(modal);
        }

        selectWeekends() {
            this.clearSelection();
            const startDate = new Date(this.currentDate);
            const endDate = new Date(this.currentDate);
            endDate.setDate(endDate.getDate() + 60);

            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday = 0, Saturday = 6
                    const dateString = this.formatDate(currentDate);
                    this.unavailableDates.set(dateString, {
                        reason: 'Weekend selection',
                        personalReason: 'Weekend availability selection',
                        tripId: null
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
            endDate.setDate(endDate.getDate() + 60);

            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek >= 1 && dayOfWeek <= 5) { // Monday to Friday
                    const dateString = this.formatDate(currentDate);
                    this.unavailableDates.set(dateString, {
                        reason: 'Weekday selection',
                        personalReason: 'Weekday availability selection',
                        tripId: null
                    });
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            this.renderCalendar();
            this.updateStats();
        }

        clearSelection() {
            this.unavailableDates.clear();
            this.renderCalendar();
            this.updateStats();
        }

        updateStats() {
            const totalDays = 60;
            const unavailableCount = this.unavailableDates.size;
            const availableCount = totalDays - unavailableCount;
            const percentage = Math.round((availableCount / totalDays) * 100);

            document.getElementById('availableDaysCount').textContent = availableCount;
            document.getElementById('unavailableDaysCount').textContent = unavailableCount;
            document.getElementById('totalDaysCount').textContent = totalDays;
            document.getElementById('availabilityPercentage').textContent = `${percentage}%`;
        }

        async savePersonalReason() {
            const reasonTextarea = document.getElementById('personalReason');
            const reason = reasonTextarea.value.trim();

            if (!reason) {
                window.showNotification('Please provide a reason for being unavailable', 'warning');
                reasonTextarea.focus();
                return;
            }

            try {
                this.showLoading();

                const response = await fetch(`${this.URL_ROOT}/driver/addUnavailableDate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        date: this.pendingDate,
                        personalReason: reason
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Add to local unavailable dates with reason info
                    this.unavailableDates.set(this.pendingDate, {
                        reason: 'Personal reason',
                        personalReason: reason,
                        tripId: null
                    });
                    this.renderCalendar();
                    this.updateStats();
                    this.closeModal();
                    window.showNotification('Date marked as unavailable', 'success');
                } else {
                    throw new Error(data.message || 'Failed to save reason');
                }
            } catch (error) {
                console.error('Error saving personal reason:', error);
                window.showNotification('Failed to save reason. Please try again.', 'error');
            } finally {
                this.hideLoading();
            }
        }

        async removeUnavailableDate() {
            try {
                this.showLoading();

                const response = await fetch(`${this.URL_ROOT}/driver/removeUnavailableDate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        date: this.pendingDate
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Remove from local unavailable dates
                    this.unavailableDates.delete(this.pendingDate);
                    this.renderCalendar();
                    this.updateStats();
                    this.closeModal();
                    window.showNotification('Date removed from unavailable list', 'success');
                } else {
                    throw new Error(data.message || 'Failed to remove date');
                }
            } catch (error) {
                console.error('Error removing unavailable date:', error);
                window.showNotification('Failed to remove date. Please try again.', 'error');
            } finally {
                this.hideLoading();
            }
        }

        // Utility methods
        formatDate(date) {
            return date.toISOString().split('T')[0];
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
            // You can implement a loading overlay here
            console.log('Loading...');
        }

        hideLoading() {
            this.isLoading = false;
            console.log('Loading complete');
        }
    }

    // Initialize the manager
    window.AvailabilityCalendarManager = AvailabilityCalendarManager;
    window.availabilityCalendarManager = new AvailabilityCalendarManager();

})();
