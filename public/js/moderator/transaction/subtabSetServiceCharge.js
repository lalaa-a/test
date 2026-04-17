(function() {
    // Service Charge Management JavaScript
    if (window.ServiceChargeManager) {
        console.log('ServiceChargeManager already exists, cleaning up...');
        if (window.serviceChargeManager) {
            delete window.serviceChargeManager;
        }
        delete window.ServiceChargeManager;
    }

    // Service Charge Manager
    class ServiceChargeManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentCharges = {
                driverBookingCharge: 0,
                guideBookingCharge: 0,
                siteServiceCharge: 0,
                lastUpdated: null
            };
            this.chargeHistory = [];
            this.chargeChart = null; // Chart.js instance
            this.chartType = 'line';
            this.timeRange = 'all';

            this.init();
        }

        init() {
            // Wait for DOM to be ready before binding events and loading data
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.bindEvents();
                    this.loadData();
                });
            } else {
                this.bindEvents();
                this.loadData();
            }
        }

        async loadData() {
            try {
                // Load both current charges and charge history in parallel
                const [chargesResponse, historyResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getCurrentServiceCharges`),
                    fetch(`${this.URL_ROOT}/moderator/getServiceChargeHistory`)
                ]);

                const [chargesData, historyData] = await Promise.all([
                    chargesResponse.json(),
                    historyResponse.json()
                ]);

                console.log('Received charges data:', chargesData);
                console.log('Received history data:', historyData);

                // Process charges data
                if (chargesData.success) {
                    console.log('Charges data received successfully:', chargesData);
                    console.log('Charges object:', chargesData.charges);
                    this.currentCharges = chargesData.charges;
                    console.log('Set currentCharges to:', this.currentCharges);
                    this.updateStatsDisplay();
                } else {
                    console.error('Error loading current charges:', chargesData.message);
                    // Set default values if API fails
                    this.currentCharges = {
                        driverBookingCharge: 0,
                        guideBookingCharge: 0,
                        siteServiceCharge: 0,
                        lastUpdated: null
                    };
                    this.updateStatsDisplay();
                }

                // Process history data
                if (historyData.success) {
                    this.chargeHistory = historyData.history;
                    this.renderChargeHistory();
                } else {
                    console.error('Error loading charge history:', historyData.message);
                }

                // Switch to default tab after data is loaded
                this.switchToTab('add-charges-section');

            } catch (error) {
                console.error('Error loading data:', error);
                window.showNotification('Error loading service charge data', 'error');
            }
        }

        bindEvents() {
            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToTab(targetId);
                });
            });

            // Charge history search and filter
            const chargeHistorySearchInput = document.getElementById('chargeHistorySearchInput');
            if (chargeHistorySearchInput) {
                chargeHistorySearchInput.addEventListener('input', (e) => {
                    this.filterChargeHistory(e.target.value);
                });
            }

            const chargeHistoryFilter = document.getElementById('chargeHistoryFilter');
            if (chargeHistoryFilter) {
                chargeHistoryFilter.addEventListener('change', () => {
                    this.filterChargeHistory();
                });
            }

            // Driver charge buttons
            const viewDriverChargeBtn = document.getElementById('viewDriverChargeBtn');
            const editDriverChargeBtn = document.getElementById('editDriverChargeBtn');

            if (viewDriverChargeBtn) {
                viewDriverChargeBtn.addEventListener('click', () => this.viewDriverCharge());
            }
            if (editDriverChargeBtn) {
                editDriverChargeBtn.addEventListener('click', () => this.openDriverChargeModal());
            }

            // Guide charge buttons
            const viewGuideChargeBtn = document.getElementById('viewGuideChargeBtn');
            const editGuideChargeBtn = document.getElementById('editGuideChargeBtn');

            if (viewGuideChargeBtn) {
                viewGuideChargeBtn.addEventListener('click', () => this.viewGuideCharge());
            }
            if (editGuideChargeBtn) {
                editGuideChargeBtn.addEventListener('click', () => this.openGuideChargeModal());
            }

            // Service charge buttons
            const viewServiceChargeBtn = document.getElementById('viewServiceChargeBtn');
            const editServiceChargeBtn = document.getElementById('editServiceChargeBtn');

            if (viewServiceChargeBtn) {
                viewServiceChargeBtn.addEventListener('click', () => this.viewServiceCharge());
            }
            if (editServiceChargeBtn) {
                editServiceChargeBtn.addEventListener('click', () => this.openServiceChargeModal());
            }

            // Modal form submissions
            const driverChargeForm = document.getElementById('driverChargeForm');
            const guideChargeForm = document.getElementById('guideChargeForm');
            const serviceChargeForm = document.getElementById('serviceChargeForm');

            if (driverChargeForm) {
                driverChargeForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.saveDriverCharge();
                });
            }
            if (guideChargeForm) {
                guideChargeForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.saveGuideCharge();
                });
            }
            if (serviceChargeForm) {
                serviceChargeForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.saveServiceCharge();
                });
            }

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const modal = e.target.closest('.modal');
                    if (modal) {
                        this.closeModal(modal.id);
                    }
                });
            });

            // Modal overlays
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal.id);
                    }
                });
            });

            // Input validation
            const numberInputs = document.querySelectorAll('input[type="number"]');
            numberInputs.forEach(input => {
                input.addEventListener('input', (e) => {
                    this.validateInput(e.target);
                });
            });

            // Chart controls
            const chartTypeSelect = document.getElementById('chartTypeSelect');
            const timeRangeSelect = document.getElementById('timeRangeSelect');

            if (chartTypeSelect) {
                chartTypeSelect.addEventListener('change', (e) => {
                    this.changeChartType(e.target.value);
                });
            }

            if (timeRangeSelect) {
                timeRangeSelect.addEventListener('change', (e) => {
                    this.changeTimeRange(e.target.value);
                });
            }
        }

        async loadCurrentCharges() {
            try {
                console.log('Loading current service charges...');
                const response = await fetch(`${this.URL_ROOT}/moderator/getCurrentServiceCharges`);
                const data = await response.json();
                console.log('Received data from getCurrentServiceCharges:', data);

                if (data.success) {
                    this.currentCharges = data.charges;
                    console.log('Set currentCharges to:', this.currentCharges);
                    this.updateStatsDisplay();
                } else {
                    console.error('Error loading current charges:', data.message);
                    window.showNotification('Error loading current charges', 'error');
                }
            } catch (error) {
                console.error('Error loading current charges:', error);
                window.showNotification('Error loading current charges', 'error');
            }
        }

        async loadChargeHistory() {
            try {
                console.log('Loading charge history...');
                const response = await fetch(`${this.URL_ROOT}/moderator/getServiceChargeHistory`);
                const data = await response.json();

                if (data.success) {
                    this.chargeHistory = data.history;
                    this.renderChargeHistory();
                    // Note: switchToTab is now handled in loadData()
                } else {
                    console.error('Error loading charge history:', data.message);
                }
            } catch (error) {
                console.error('Error loading charge history:', error);
            }
        }

        updateStatsDisplay() {
            console.log('updateStatsDisplay called with charges:', this.currentCharges);
            console.log('Current charges object:', JSON.stringify(this.currentCharges, null, 2));

            // Update stat cards
            const currentDriverCharge = document.getElementById('currentDriverCharge');
            const currentGuideCharge = document.getElementById('currentGuideCharge');
            const currentServiceCharge = document.getElementById('currentServiceCharge');
            const lastUpdated = document.getElementById('lastUpdated');

            console.log('DOM elements found:', {
                currentDriverCharge: !!currentDriverCharge,
                currentGuideCharge: !!currentGuideCharge,
                currentServiceCharge: !!currentServiceCharge,
                lastUpdated: !!lastUpdated
            });

            if (currentDriverCharge) {
                const driverValue = this.currentCharges.driverBookingCharge || 0;
                currentDriverCharge.textContent = `LKR ${this.formatCurrency(driverValue)}`;
                console.log('Updated currentDriverCharge to:', currentDriverCharge.textContent);
            }

            if (currentGuideCharge) {
                const guideValue = this.currentCharges.guideBookingCharge || 0;
                currentGuideCharge.textContent = `LKR ${this.formatCurrency(guideValue)}`;
                console.log('Updated currentGuideCharge to:', currentGuideCharge.textContent);
            }

            if (currentServiceCharge) {
                const serviceValue = this.currentCharges.siteServiceCharge || 0;
                currentServiceCharge.textContent = `${serviceValue}%`;
                console.log('Updated currentServiceCharge to:', currentServiceCharge.textContent);
            }

            if (lastUpdated) {
                lastUpdated.textContent = this.currentCharges.lastUpdated ?
                    this.formatDate(this.currentCharges.lastUpdated) : 'Never';
                console.log('Updated lastUpdated to:', lastUpdated.textContent);
            }

            // Update individual charge displays in sections
            const driverChargeAmount = document.getElementById('driverChargeAmount');
            const guideChargeAmount = document.getElementById('guideChargeAmount');
            const serviceChargeAmount = document.getElementById('serviceChargeAmount');

            console.log('Individual display elements found:', {
                driverChargeAmount: !!driverChargeAmount,
                guideChargeAmount: !!guideChargeAmount,
                serviceChargeAmount: !!serviceChargeAmount
            });

            if (driverChargeAmount) {
                const driverValue = this.currentCharges.driverBookingCharge || 0;
                driverChargeAmount.textContent = `LKR ${this.formatCurrency(driverValue)}`;
                console.log('Updated driver charge amount to:', driverChargeAmount.textContent);
            }

            if (guideChargeAmount) {
                const guideValue = this.currentCharges.guideBookingCharge || 0;
                guideChargeAmount.textContent = `LKR ${this.formatCurrency(guideValue)}`;
                console.log('Updated guide charge amount to:', guideChargeAmount.textContent);
            }

            if (serviceChargeAmount) {
                const serviceValue = this.currentCharges.siteServiceCharge || 0;
                serviceChargeAmount.textContent = `${serviceValue}%`;
                console.log('Updated service charge amount to:', serviceChargeAmount.textContent);
            }

            // Update charge status badges
            this.updateChargeStatuses();
        }

        updateChargeStatuses() {
            const driverStatus = document.getElementById('driverChargeStatus');
            const guideStatus = document.getElementById('guideChargeStatus');
            const serviceStatus = document.getElementById('serviceChargeStatus');

            if (driverStatus) {
                driverStatus.className = 'status-value active';
                driverStatus.textContent = 'Active';
            }
            if (guideStatus) {
                guideStatus.className = 'status-value active';
                guideStatus.textContent = 'Active';
            }
            if (serviceStatus) {
                serviceStatus.className = 'status-value active';
                serviceStatus.textContent = 'Active';
            }
        }

        // Driver Charge Methods
        viewDriverCharge() {
            const detailsDiv = document.getElementById('chargeDetails');
            if (detailsDiv) {
                detailsDiv.innerHTML = `
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Charge Type:</span>
                        <span class="charge-detail-value">Driver Booking Charge</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Current Amount:</span>
                        <span class="charge-detail-value">LKR ${this.formatCurrency(this.currentCharges.driverBookingCharge)}</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Status:</span>
                        <span class="charge-detail-value">Active</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Last Updated:</span>
                        <span class="charge-detail-value">${this.currentCharges.lastUpdated ? this.formatDate(this.currentCharges.lastUpdated) : 'Never'}</span>
                    </div>
                `;
            }
            this.openModal('viewChargeModal');
        }

        openDriverChargeModal() {
            const input = document.getElementById('driverBookingCharge');
            const notes = document.getElementById('driverChargeNotes');
            if (input) {
                input.value = this.currentCharges.driverBookingCharge || '';
            }
            if (notes) {
                notes.value = '';
            }
            this.openModal('driverChargeModal');
        }

        async saveDriverCharge() {
            const input = document.getElementById('driverBookingCharge');
            const notes = document.getElementById('driverChargeNotes');

            if (!input) {
                console.error('Driver booking charge input not found');
                window.showNotification('Form error: Input field not found', 'error');
                return;
            }

            const charge = parseFloat(input.value) || 0;

            if (charge < 0) {
                window.showNotification('Driver booking charge cannot be negative', 'error');
                return;
            }

            try {
                this.setLoadingState('driverChargeModal', true);

                const response = await fetch(`${this.URL_ROOT}/moderator/setServiceCharges`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        driverBookingCharge: charge,
                        guideBookingCharge: this.currentCharges.guideBookingCharge,
                        siteServiceCharge: this.currentCharges.siteServiceCharge,
                        chargeNotes: notes ? notes.value.trim() : ''
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Driver booking charge updated successfully', 'success');
                    this.currentCharges.driverBookingCharge = charge;
                    this.currentCharges.lastUpdated = new Date().toISOString();
                    this.updateStatsDisplay();
                    this.loadChargeHistory();
                    this.closeModal('driverChargeModal');
                } else {
                    window.showNotification(data.message || 'Error updating driver charge', 'error');
                }
            } catch (error) {
                console.error('Error saving driver charge:', error);
                window.showNotification('Error updating driver charge', 'error');
            } finally {
                this.setLoadingState('driverChargeModal', false);
            }
        }

        // Guide Charge Methods
        viewGuideCharge() {
            const detailsDiv = document.getElementById('chargeDetails');
            if (detailsDiv) {
                detailsDiv.innerHTML = `
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Charge Type:</span>
                        <span class="charge-detail-value">Guide Booking Charge</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Current Amount:</span>
                        <span class="charge-detail-value">LKR ${this.formatCurrency(this.currentCharges.guideBookingCharge)}</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Status:</span>
                        <span class="charge-detail-value">Active</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Last Updated:</span>
                        <span class="charge-detail-value">${this.currentCharges.lastUpdated ? this.formatDate(this.currentCharges.lastUpdated) : 'Never'}</span>
                    </div>
                `;
            }
            this.openModal('viewChargeModal');
        }

        openGuideChargeModal() {
            const input = document.getElementById('guideBookingCharge');
            const notes = document.getElementById('guideChargeNotes');
            if (input) {
                input.value = this.currentCharges.guideBookingCharge || '';
            }
            if (notes) {
                notes.value = '';
            }
            this.openModal('guideChargeModal');
        }

        async saveGuideCharge() {
            const input = document.getElementById('guideBookingCharge');
            const notes = document.getElementById('guideChargeNotes');

            if (!input) {
                console.error('Guide booking charge input not found');
                window.showNotification('Form error: Input field not found', 'error');
                return;
            }

            const charge = parseFloat(input.value) || 0;

            if (charge < 0) {
                window.showNotification('Guide booking charge cannot be negative', 'error');
                return;
            }

            try {
                this.setLoadingState('guideChargeModal', true);

                const response = await fetch(`${this.URL_ROOT}/moderator/setServiceCharges`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        driverBookingCharge: this.currentCharges.driverBookingCharge,
                        guideBookingCharge: charge,
                        siteServiceCharge: this.currentCharges.siteServiceCharge,
                        chargeNotes: notes ? notes.value.trim() : ''
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Guide booking charge updated successfully', 'success');
                    this.currentCharges.guideBookingCharge = charge;
                    this.currentCharges.lastUpdated = new Date().toISOString();
                    this.updateStatsDisplay();
                    this.loadChargeHistory();
                    this.closeModal('guideChargeModal');
                } else {
                    window.showNotification(data.message || 'Error updating guide charge', 'error');
                }
            } catch (error) {
                console.error('Error saving guide charge:', error);
                window.showNotification('Error updating guide charge', 'error');
            } finally {
                this.setLoadingState('guideChargeModal', false);
            }
        }

        // Service Charge Methods
        viewServiceCharge() {
            const detailsDiv = document.getElementById('chargeDetails');
            if (detailsDiv) {
                detailsDiv.innerHTML = `
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Charge Type:</span>
                        <span class="charge-detail-value">Site Service Charge</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Current Percentage:</span>
                        <span class="charge-detail-value">${this.currentCharges.siteServiceCharge}%</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Status:</span>
                        <span class="charge-detail-value">Active</span>
                    </div>
                    <div class="charge-detail-item">
                        <span class="charge-detail-label">Last Updated:</span>
                        <span class="charge-detail-value">${this.currentCharges.lastUpdated ? this.formatDate(this.currentCharges.lastUpdated) : 'Never'}</span>
                    </div>
                `;
            }
            this.openModal('viewChargeModal');
        }

        openServiceChargeModal() {
            const input = document.getElementById('siteServiceCharge');
            const notes = document.getElementById('serviceChargeNotes');
            if (input) {
                input.value = this.currentCharges.siteServiceCharge || '';
            }
            if (notes) {
                notes.value = '';
            }
            this.openModal('serviceChargeModal');
        }

        async saveServiceCharge() {
            const input = document.getElementById('siteServiceCharge');
            const notes = document.getElementById('serviceChargeNotes');

            if (!input) {
                console.error('Site service charge input not found');
                window.showNotification('Form error: Input field not found', 'error');
                return;
            }

            const charge = parseFloat(input.value) || 0;

            if (charge < 0 || charge > 100) {
                window.showNotification('Site service charge must be between 0 and 100', 'error');
                return;
            }

            try {
                this.setLoadingState('serviceChargeModal', true);

                const response = await fetch(`${this.URL_ROOT}/moderator/setServiceCharges`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        driverBookingCharge: this.currentCharges.driverBookingCharge,
                        guideBookingCharge: this.currentCharges.guideBookingCharge,
                        siteServiceCharge: charge,
                        chargeNotes: notes ? notes.value.trim() : ''
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Site service charge updated successfully', 'success');
                    this.currentCharges.siteServiceCharge = charge;
                    this.currentCharges.lastUpdated = new Date().toISOString();
                    this.updateStatsDisplay();
                    this.loadChargeHistory();
                    this.closeModal('serviceChargeModal');
                } else {
                    window.showNotification(data.message || 'Error updating service charge', 'error');
                }
            } catch (error) {
                console.error('Error saving service charge:', error);
                window.showNotification('Error updating service charge', 'error');
            } finally {
                this.setLoadingState('serviceChargeModal', false);
            }
        }

        validateInput(input) {
            const value = parseFloat(input.value);
            const min = parseFloat(input.min) || 0;
            const max = parseFloat(input.max);

            if (input.value && (isNaN(value) || value < min || (max && value > max))) {
                input.classList.add('error');
            } else {
                input.classList.remove('error');
            }
        }

        renderChargeHistory() {
            const historyGrid = document.getElementById('chargeHistoryGrid');
            if (!historyGrid) return;

            if (this.chargeHistory.length === 0) {
                historyGrid.innerHTML = `
                    <tr class="no-history">
                        <td colspan="6">
                            <i class="fas fa-history"></i>
                            <p>No charge history yet</p>
                        </td>
                    </tr>
                `;
                return;
            }

            historyGrid.innerHTML = this.chargeHistory.map(entry => `
                <tr>
                    <td>${this.formatDate(entry.createdAt)}</td>
                    <td>LKR ${this.formatCurrency(entry.driverBookingCharge)}</td>
                    <td>LKR ${this.formatCurrency(entry.guideBookingCharge)}</td>
                    <td>${entry.siteServiceCharge}%</td>
                    <td>
                        <span class="status-badge ${entry.isActive ? 'active' : 'inactive'}">
                            ${entry.isActive ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn view" onclick="serviceChargeManager.viewChargeDetails('${entry.chargeId}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        viewChargeDetails(chargeId) {
            const charge = this.chargeHistory.find(c => c.chargeId == chargeId);
            if (!charge) {
                window.showNotification('Charge details not found', 'error');
                return;
            }

            const detailsDiv = document.getElementById('chargeDetails');
            if (detailsDiv) {
                detailsDiv.innerHTML = `
                    <div class="charge-detail-section">
                        <h4><i class="fas fa-info-circle"></i> Charge Configuration</h4>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Charge ID:</span>
                            <span class="charge-detail-value">#${charge.chargeId}</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Driver Booking Charge:</span>
                            <span class="charge-detail-value">LKR ${this.formatCurrency(charge.driverBookingCharge)}</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Guide Booking Charge:</span>
                            <span class="charge-detail-value">LKR ${this.formatCurrency(charge.guideBookingCharge)}</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Site Service Charge:</span>
                            <span class="charge-detail-value">${charge.siteServiceCharge}%</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Status:</span>
                            <span class="charge-detail-value">
                                <span class="status-badge ${charge.isActive ? 'active' : 'inactive'}">
                                    ${charge.isActive ? 'Active' : 'Inactive'}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="charge-detail-section">
                        <h4><i class="fas fa-user"></i> Moderator Information</h4>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Moderator:</span>
                            <span class="charge-detail-value">${charge.moderatorName || 'Unknown'}</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Email:</span>
                            <span class="charge-detail-value">${charge.moderatorEmail || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="charge-detail-section">
                        <h4><i class="fas fa-calendar"></i> Timeline</h4>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Created:</span>
                            <span class="charge-detail-value">${this.formatDate(charge.createdAt)}</span>
                        </div>
                        <div class="charge-detail-item">
                            <span class="charge-detail-label">Last Updated:</span>
                            <span class="charge-detail-value">${this.formatDate(charge.updatedAt)}</span>
                        </div>
                    </div>

                    ${charge.notes ? `
                    <div class="charge-detail-section">
                        <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                        <div class="charge-notes">
                            ${charge.notes}
                        </div>
                    </div>
                    ` : ''}
                `;
            }

            this.openModal('viewChargeModal');
        }

        switchToTab(targetId) {
            // Update navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            // Update sections
            document.querySelectorAll('.verification-section').forEach(sec => {
                sec.style.display = 'none';
            });

            const sectionElement = document.getElementById(targetId);
            if (sectionElement) {
                sectionElement.style.display = 'block';

                // Initialize chart when analytics tab is selected
                if (targetId === 'charge-analytics-section') {
                    setTimeout(() => {
                        this.initChart();
                    }, 100); // Small delay to ensure DOM is ready
                }
            }
        }

        filterChargeHistory(searchTerm = '') {
            const chargeHistoryFilter = document.getElementById('chargeHistoryFilter');
            const filterValue = chargeHistoryFilter ? chargeHistoryFilter.value : 'all';

            let filteredHistory = [...this.chargeHistory];

            if (filterValue !== 'all') {
                // Filter based on charge type
                if (filterValue === 'driver') {
                    filteredHistory = filteredHistory.filter(entry => entry.driverBookingCharge > 0);
                } else if (filterValue === 'guide') {
                    filteredHistory = filteredHistory.filter(entry => entry.guideBookingCharge > 0);
                } else if (filterValue === 'service') {
                    filteredHistory = filteredHistory.filter(entry => entry.siteServiceCharge > 0);
                }
            }

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredHistory = filteredHistory.filter(entry =>
                    this.formatDate(entry.createdAt).toLowerCase().includes(term) ||
                    entry.driverBookingCharge.toString().includes(term) ||
                    entry.guideBookingCharge.toString().includes(term) ||
                    entry.siteServiceCharge.toString().includes(term) ||
                    (entry.moderatorName && entry.moderatorName.toLowerCase().includes(term))
                );
            }

            this.renderFilteredChargeHistory(filteredHistory);
        }

        renderFilteredChargeHistory(filteredHistory) {
            const historyGrid = document.getElementById('chargeHistoryGrid');
            if (!historyGrid) return;

            if (filteredHistory.length === 0) {
                historyGrid.innerHTML = `
                    <tr class="no-history">
                        <td colspan="6">
                            <i class="fas fa-search"></i>
                            <p>No matching charge history found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            historyGrid.innerHTML = filteredHistory.map(entry => `
                <tr>
                    <td>${this.formatDate(entry.createdAt)}</td>
                    <td>LKR ${this.formatCurrency(entry.driverBookingCharge)}</td>
                    <td>LKR ${this.formatCurrency(entry.guideBookingCharge)}</td>
                    <td>${entry.siteServiceCharge}%</td>
                    <td>
                        <span class="status-badge ${entry.isActive ? 'active' : 'inactive'}">
                            ${entry.isActive ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn view" onclick="serviceChargeManager.viewChargeDetails('${entry.chargeId}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
            }
        }

        setLoadingState(modalId, loading) {
            const modal = document.getElementById(modalId);
            const form = modal ? modal.querySelector('form') : null;
            const submitBtn = modal ? modal.querySelector('button[type="submit"]') : null;

            if (loading) {
                if (form) form.classList.add('loading');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                }
            } else {
                if (form) form.classList.remove('loading');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                }
            }
        }

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-LK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        // Chart Analytics Methods
        initChart() {
            const ctx = document.getElementById('chargeEvolutionChart');
            if (!ctx) return;

            // Destroy existing chart if it exists
            if (this.chargeChart) {
                this.chargeChart.destroy();
            }

            this.chargeChart = new Chart(ctx, {
                type: this.chartType === 'area' ? 'line' : this.chartType,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Driver Charge (LKR)',
                        data: [],
                        borderColor: '#1976d2',
                        backgroundColor: this.chartType === 'area' ? 'rgba(25, 118, 210, 0.1)' : 'rgba(25, 118, 210, 0.8)',
                        fill: this.chartType === 'area',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Guide Charge (LKR)',
                        data: [],
                        borderColor: '#7b1fa2',
                        backgroundColor: this.chartType === 'area' ? 'rgba(123, 31, 162, 0.1)' : 'rgba(123, 31, 162, 0.8)',
                        fill: this.chartType === 'area',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Service Charge (%)',
                        data: [],
                        borderColor: '#f57c00',
                        backgroundColor: this.chartType === 'area' ? 'rgba(245, 124, 0, 0.1)' : 'rgba(245, 124, 0, 0.8)',
                        fill: this.chartType === 'area',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Service Charge Evolution Over Time',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 2) { // Service charge percentage
                                        label += context.parsed.y + '%';
                                    } else { // Driver/Guide charges
                                        label += 'LKR ' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Charge Amount (LKR)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Service Charge (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });

            this.updateChartData();
        }

        updateChartData() {
            if (!this.chargeChart || !this.chargeHistory.length) return;

            // Filter data based on time range
            let filteredHistory = this.filterHistoryByTimeRange();

            // Sort by date
            filteredHistory.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            // Prepare data for chart
            const labels = [];
            const driverData = [];
            const guideData = [];
            const serviceData = [];

            filteredHistory.forEach(entry => {
                const date = new Date(entry.createdAt);
                labels.push(date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                }));

                driverData.push(parseFloat(entry.driverBookingCharge) || 0);
                guideData.push(parseFloat(entry.guideBookingCharge) || 0);
                serviceData.push(parseFloat(entry.siteServiceCharge) || 0);
            });

            // Update chart data
            this.chargeChart.data.labels = labels;
            this.chargeChart.data.datasets[0].data = driverData;
            this.chargeChart.data.datasets[1].data = guideData;
            this.chargeChart.data.datasets[2].data = serviceData;

            this.chargeChart.update();

            // Update summary statistics
            this.updateSummaryStats(filteredHistory);
        }

        filterHistoryByTimeRange() {
            if (this.timeRange === 'all') return this.chargeHistory;

            const now = new Date();
            let cutoffDate;

            switch (this.timeRange) {
                case '6months':
                    cutoffDate = new Date(now.getTime() - (6 * 30 * 24 * 60 * 60 * 1000));
                    break;
                case '1year':
                    cutoffDate = new Date(now.getTime() - (365 * 24 * 60 * 60 * 1000));
                    break;
                case '2years':
                    cutoffDate = new Date(now.getTime() - (2 * 365 * 24 * 60 * 60 * 1000));
                    break;
                default:
                    return this.chargeHistory;
            }

            return this.chargeHistory.filter(entry => new Date(entry.created_at) >= cutoffDate);
        }

        updateSummaryStats(filteredHistory) {
            // Total changes
            document.getElementById('totalChanges').textContent = filteredHistory.length;

            // Average change frequency
            if (filteredHistory.length > 1) {
                const sortedHistory = filteredHistory.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                const firstDate = new Date(sortedHistory[0].created_at);
                const lastDate = new Date(sortedHistory[sortedHistory.length - 1].created_at);
                const daysDiff = Math.ceil((lastDate - firstDate) / (1000 * 60 * 60 * 24));
                const avgDays = daysDiff / (filteredHistory.length - 1);

                if (avgDays < 30) {
                    document.getElementById('avgChangeFrequency').textContent = `${Math.round(avgDays)} days`;
                } else if (avgDays < 365) {
                    document.getElementById('avgChangeFrequency').textContent = `${Math.round(avgDays / 30)} months`;
                } else {
                    document.getElementById('avgChangeFrequency').textContent = `${Math.round(avgDays / 365)} years`;
                }
            } else {
                document.getElementById('avgChangeFrequency').textContent = 'N/A';
            }

            // Current total charge
            const currentDriver = parseFloat(this.currentCharges.driverBookingCharge) || 0;
            const currentGuide = parseFloat(this.currentCharges.guideBookingCharge) || 0;
            document.getElementById('currentTotalCharge').textContent = `LKR ${(currentDriver + currentGuide).toLocaleString()}`;
        }

        changeChartType(newType) {
            this.chartType = newType;

            // Update dataset fill property based on chart type
            this.chargeChart.data.datasets.forEach(dataset => {
                if (newType === 'area') {
                    dataset.fill = true;
                    dataset.backgroundColor = dataset.borderColor.replace('rgb', 'rgba').replace(')', ', 0.1)');
                } else {
                    dataset.fill = false;
                    dataset.backgroundColor = dataset.borderColor;
                }
            });

            // Set chart type - area charts use line type with fill
            this.chargeChart.config.type = newType === 'area' ? 'line' : newType;
            this.chargeChart.update();
        }

        changeTimeRange(newRange) {
            this.timeRange = newRange;
            this.updateChartData();
        }    }

    // Initialize the manager
    window.ServiceChargeManager = ServiceChargeManager;
    window.serviceChargeManager = new ServiceChargeManager();

})();