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
            this.unavailableDates = new Set(); // Store dates as YYYY-MM-DD strings
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadAvailability();
            this.renderCalendar();
            this.updateStats();
        }

        bindEvents() {
            // Navigation buttons
            document.getElementById('prevMonthBtn').addEventListener('click', () => this.navigateMonth(-1));
            document.getElementById('nextMonthBtn').addEventListener('click', () => this.navigateMonth(1));

            // Action buttons
            document.getElementById('saveAvailabilityBtn').addEventListener('click', () => this.showSaveConfirmation());
            document.getElementById('resetAvailabilityBtn').addEventListener('click', () => this.resetAvailability());

            // Quick selection buttons
            document.getElementById('selectWeekendsBtn').addEventListener('click', () => this.selectWeekends());
            document.getElementById('selectWeekdaysBtn').addEventListener('click', () => this.selectWeekdays());
            document.getElementById('clearSelectionBtn').addEventListener('click', () => this.clearSelection());

            // Modal events
            document.getElementById('cancelSaveBtn').addEventListener('click', () => this.closeModal());
            document.getElementById('confirmSaveBtn').addEventListener('click', () => this.saveAvailability());

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
                const response = await fetch(`${this.URL_ROOT}/driver/getAvailability`, {
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
                    this.unavailableDates = new Set(data.unavailableDates);
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

            // Calculate calendar dates
            const firstDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth(), 1);
            const lastDay = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth() + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const endDate = new Date(lastDay);
            endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));

            // Generate calendar days
            const currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayElement = document.createElement('div');
                const dateString = this.formatDate(currentDate);
                const isCurrentMonth = currentDate.getMonth() === this.selectedMonth.getMonth();
                const isToday = this.isToday(currentDate);
                const isPast = currentDate < this.currentDate && !isToday;
                const isUnavailable = this.unavailableDates.has(dateString);

                dayElement.className = 'calendar-day';
                dayElement.textContent = currentDate.getDate();
                dayElement.dataset.date = dateString;

                // Apply appropriate classes
                if (!isCurrentMonth) {
                    dayElement.classList.add('past');
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

                // Add click handler for current month days that are not in the past
                if (isCurrentMonth && !isPast) {
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

            // Don't allow navigation beyond 60 days from today
            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 60);

            const minDate = new Date(this.currentDate);
            minDate.setDate(minDate.getDate() - 1);

            if (newMonth > maxDate || newMonth < minDate) {
                return;
            }

            this.selectedMonth = newMonth;
            this.renderCalendar();
        }

        updateNavigationButtons() {
            const prevBtn = document.getElementById('prevMonthBtn');
            const nextBtn = document.getElementById('nextMonthBtn');

            const minDate = new Date(this.currentDate);
            minDate.setDate(minDate.getDate() - 1);

            const maxDate = new Date(this.currentDate);
            maxDate.setDate(maxDate.getDate() + 60);

            prevBtn.disabled = this.selectedMonth <= minDate;
            nextBtn.disabled = new Date(this.selectedMonth.getFullYear(), this.selectedMonth.getMonth() + 1, 0) >= maxDate;
        }

        toggleDay(dateString) {
            if (this.unavailableDates.has(dateString)) {
                this.unavailableDates.delete(dateString);
            } else {
                this.unavailableDates.add(dateString);
            }

            this.renderCalendar();
            this.updateStats();
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
                    this.unavailableDates.add(dateString);
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
                    this.unavailableDates.add(dateString);
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

        resetAvailability() {
            if (confirm('Are you sure you want to reset all availability? This will mark all days as available.')) {
                this.clearSelection();
            }
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

        showSaveConfirmation() {
            const availableCount = 60 - this.unavailableDates.size;
            const unavailableCount = this.unavailableDates.size;

            document.getElementById('confirmAvailableDays').textContent = availableCount;
            document.getElementById('confirmUnavailableDays').textContent = unavailableCount;

            this.openModal(document.getElementById('saveConfirmationModal'));
        }

        async saveAvailability() {
            this.closeModal();

            try {
                this.showLoading();
                const response = await fetch(`${this.URL_ROOT}/driver/saveAvailability`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        unavailableDates: Array.from(this.unavailableDates)
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Availability saved successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to save availability');
                }
            } catch (error) {
                console.error('Error saving availability:', error);
                window.showNotification('Failed to save availability. Please try again.', 'error');
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
