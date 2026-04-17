(function(){
// Driver Payouts JavaScript
    if (window.DriverPayoutsManager) {
        console.log('DriverPayoutsManager already exists, cleaning up...');
        if (window.driverPayoutsManager) {
            delete window.driverPayoutsManager;
        }
        delete window.DriverPayoutsManager;
    }

    // Driver Payouts Manager
    class DriverPayoutsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentUser = null;
            this.payouts = {
                completed: [],
                pending: [],
                cancelled: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadPayouts();
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

            // Completed payouts search and filter
            const completedSearchInput = document.getElementById('completedSearchInput');
            if (completedSearchInput) {
                completedSearchInput.addEventListener('input', (e) => {
                    this.filterPayouts('completed', e.target.value);
                });
            }

            const completedPayoutFilter = document.getElementById('completedPayoutFilter');
            if (completedPayoutFilter) {
                completedPayoutFilter.addEventListener('change', () => {
                    this.filterPayouts('completed');
                });
            }

            // Pending payouts search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterPayouts('pending', e.target.value);
                });
            }

            const pendingPayoutFilter = document.getElementById('pendingPayoutFilter');
            if (pendingPayoutFilter) {
                pendingPayoutFilter.addEventListener('change', () => {
                    this.filterPayouts('pending');
                });
            }

            // Cancelled payouts search and filter
            const cancelledSearchInput = document.getElementById('cancelledSearchInput');
            if (cancelledSearchInput) {
                cancelledSearchInput.addEventListener('input', (e) => {
                    this.filterPayouts('cancelled', e.target.value);
                });
            }

            const cancelledPayoutFilter = document.getElementById('cancelledPayoutFilter');
            if (cancelledPayoutFilter) {
                cancelledPayoutFilter.addEventListener('change', () => {
                    this.filterPayouts('cancelled');
                });
            }

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal('payoutDetailsModal'));
            });

            // Modal overlay
            const payoutModal = document.getElementById('payoutDetailsModal');
            if (payoutModal) {
                payoutModal.addEventListener('click', (e) => {
                    if (e.target === payoutModal) {
                        this.closeModal('payoutDetailsModal');
                    }
                });
            }

            // View button clicks
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const payoutId = button.dataset.payoutId;
                    if (payoutId) {
                        this.showPayoutDetails(payoutId);
                    }
                }
            });
        }

        async loadPayouts() {
            try {
                console.log('Loading driver payouts...');
                // Load completed, pending, and cancelled payouts
                const [completedResponse, pendingResponse, cancelledResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getCompletedDriverPayouts`),
                    fetch(`${this.URL_ROOT}/moderator/getPendingDriverPayouts`),
                    fetch(`${this.URL_ROOT}/moderator/getCancelledDriverPayouts`)
                ]);

                const [completedData, pendingData, cancelledData] = await Promise.all([
                    completedResponse.json(),
                    pendingResponse.json(),
                    cancelledResponse.json()
                ]);

                if (completedData.success) {
                    this.payouts.completed = completedData.payouts;
                }

                if (pendingData.success) {
                    this.payouts.pending = pendingData.payouts;
                }

                if (cancelledData.success) {
                    this.payouts.cancelled = cancelledData.payouts;
                }

                this.updateStats();
                this.renderPayouts('completed');
                this.renderPayouts('pending');
                this.renderPayouts('cancelled');
                this.switchToTab('completed-section');
            } catch (error) {
                console.error('Error loading payouts:', error);
                window.showNotification('Error loading payouts', 'error');
            }
        }

        updateStats() {
            const completedCount = this.payouts.completed.length;
            const pendingCount = this.payouts.pending.length;
            const cancelledCount = this.payouts.cancelled.length;
            const totalPayoutAmount = this.payouts.completed.reduce((sum, payout) => sum + (parseFloat(payout.amount) || 0), 0);

            document.getElementById('completedPayoutsCount').textContent = completedCount;
            document.getElementById('pendingPayoutsCount').textContent = pendingCount;
            document.getElementById('cancelledPayoutsCount').textContent = cancelledCount;
            document.getElementById('totalPayoutAmount').textContent = '$' + totalPayoutAmount.toFixed(2);
        }

        filterPayouts(section, searchTerm = '') {
            const payoutFilter = document.getElementById(`${section}PayoutFilter`);
            const filterValue = payoutFilter ? payoutFilter.value : 'all';

            let filteredPayouts = [...this.payouts[section]];

            if (filterValue !== 'all') {
                filteredPayouts = filteredPayouts.filter(payout => payout.paymentType === filterValue);
            }

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredPayouts = filteredPayouts.filter(payout =>
                    payout.tripId.toString().includes(term) ||
                    payout.driverName.toLowerCase().includes(term) ||
                    payout.driverEmail.toLowerCase().includes(term) ||
                    payout.transactionId?.toLowerCase().includes(term)
                );
            }

            this.renderPayouts(section, filteredPayouts);
        }

        renderPayouts(section, payouts = null) {
            const targetGrid = section === 'completed' ? 'completedPayoutsGrid' :
                             section === 'pending' ? 'pendingPayoutsGrid' : 'cancelledPayoutsGrid';

            if (payouts === null) {
                payouts = this.payouts[section];
            }

            const tbody = document.getElementById(targetGrid);

            if (payouts.length === 0) {
                const sectionName = section === 'completed' ? 'completed' :
                                   section === 'pending' ? 'pending' : 'cancelled';
                const icon = section === 'completed' ? 'check-circle' :
                            section === 'pending' ? 'clock' : 'times-circle';
                const message = section === 'completed' ? 'yet' :
                               section === 'pending' ? 'yet' : 'yet';
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-${icon}"></i>
                            <p>No ${sectionName} payouts ${message}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = payouts.map(payout => this.createPayoutRow(payout, section)).join('');
        }

        createPayoutRow(payout, section) {
            const amount = section === 'cancelled' ? parseFloat(payout.refundAmount) : parseFloat(payout.amount);
            const date = section === 'cancelled' ? payout.refundDate : payout.paymentDate;
            const status = this.getPayoutStatus(payout, section);

            return `
                <tr class="account-row" data-payout-id="${payout.id}">
                    <td class="trip-id-cell">${payout.tripId}</td>
                    <td class="driver-cell">
                        <div class="driver-info">
                            <div class="driver-name">${this.escapeHtml(payout.driverName)}</div>
                            <div class="driver-email">${this.escapeHtml(payout.driverEmail)}</div>
                        </div>
                    </td>
                    <td class="amount-cell">$${amount.toFixed(2)}</td>
                    <td class="date-cell">${this.formatDate(date)}</td>
                    <td class="status-cell">
                        <span class="status-badge ${status.class}">${status.text}</span>
                    </td>
                    <td class="reason-cell">${this.escapeHtml(payout.reason || 'N/A')}</td>
                    <td class="actions-cell">
                        <button class="btn-view" data-payout-id="${payout.id}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </td>
                </tr>
            `;
        }

        getPayoutStatus(payout, section) {
            if (section === 'completed') {
                return { text: 'Completed', class: 'status-completed' };
            } else if (section === 'pending') {
                return { text: 'Pending', class: 'status-pending' };
            } else if (section === 'cancelled') {
                return { text: 'Cancelled', class: 'status-cancelled' };
            }
            return { text: 'Unknown', class: 'status-unknown' };
        }

        showPayoutDetails(payoutId) {
            // Find payout across all sections
            let payout = null;
            let section = '';

            for (const [sec, payouts] of Object.entries(this.payouts)) {
                payout = payouts.find(p => p.id == payoutId);
                if (payout) {
                    section = sec;
                    break;
                }
            }

            if (!payout) return;

            const modalContent = document.getElementById('payoutDetailsContent');

            if (section === 'completed') {
                modalContent.innerHTML = this.renderCompletedPayoutDetails(payout);
            } else if (section === 'pending') {
                modalContent.innerHTML = this.renderPendingPayoutDetails(payout);
            } else if (section === 'cancelled') {
                modalContent.innerHTML = this.renderCancelledPayoutDetails(payout);
            }

            this.openModal('payoutDetailsModal');
        }

        renderCompletedPayoutDetails(payout) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); display: flex; align-items: center; justify-content: center; color: #2e7d32; font-size: 3rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Payout #${payout.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-check-circle"></i>
                            Completed Payout
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Payout Information</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payout.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Payout Amount:</label>
                                <span class="amount">$${parseFloat(payout.amount).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Transaction ID:</label>
                                <span class="transaction-id">${payout.transactionId || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Payout Date:</label>
                                <span>${this.formatDate(payout.paymentDate)}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-user"></i> Driver Information</h4>
                            <div class="info-item">
                                <label>Name:</label>
                                <span>${this.escapeHtml(payout.driverName)}</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${this.escapeHtml(payout.driverEmail)}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span>${this.escapeHtml(payout.driverPhone || 'N/A')}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-tasks"></i> Payout Status</h4>
                            <div class="info-item">
                                <label>Site Payment:</label>
                                <span class="completed">
                                    ✓ Completed (${this.formatDate(payout.pDateSite)})
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Reason:</label>
                                <span>${this.escapeHtml(payout.reason || 'Trip completion')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        renderPendingPayoutDetails(payout) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe082 100%); display: flex; align-items: center; justify-content: center; color: #f57c00; font-size: 3rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Payout #${payout.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-clock"></i>
                            Pending Payout
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Payout Information</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payout.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Payout Amount:</label>
                                <span class="amount">$${parseFloat(payout.amount).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Transaction ID:</label>
                                <span class="transaction-id">${payout.transactionId || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Expected Date:</label>
                                <span>${this.formatDate(payout.paymentDate)}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-user"></i> Driver Information</h4>
                            <div class="info-item">
                                <label>Name:</label>
                                <span>${this.escapeHtml(payout.driverName)}</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${this.escapeHtml(payout.driverEmail)}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span>${this.escapeHtml(payout.driverPhone || 'N/A')}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-tasks"></i> Payout Status</h4>
                            <div class="info-item">
                                <label>Site Payment:</label>
                                <span class="pending">
                                    ○ Pending
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Reason:</label>
                                <span>${this.escapeHtml(payout.reason || 'Trip completion')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        renderCancelledPayoutDetails(payout) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); display: flex; align-items: center; justify-content: center; color: #c62828; font-size: 3rem;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h3>Payout #${payout.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-times-circle"></i>
                            Cancelled Payout
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Cancellation Details</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payout.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Amount:</label>
                                <span class="amount">$${parseFloat(payout.refundAmount).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Date:</label>
                                <span>${this.formatDate(payout.refundDate)}</span>
                            </div>
                            <div class="info-item">
                                <label>Reason:</label>
                                <span>${this.escapeHtml(payout.refundReason || 'Trip cancellation')}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-user"></i> Driver Information</h4>
                            <div class="info-item">
                                <label>Name:</label>
                                <span>${this.escapeHtml(payout.driverName)}</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${this.escapeHtml(payout.driverEmail)}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span>${this.escapeHtml(payout.driverPhone || 'N/A')}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-credit-card"></i> Transaction Information</h4>
                            <div class="info-item">
                                <label>Original Transaction:</label>
                                <span class="transaction-id">${payout.transactionId || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Status:</label>
                                <span class="status-cancelled">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
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
            }
        }

        openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('show');
            }
        }

        closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                this.currentUser = null;
            }
        }

        showSuccess(message) {
            // You can implement a toast notification here
            alert(message);
        }

        showError(message) {
            // You can implement a toast notification here
            alert('Error: ' + message);
        }

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialize the manager when DOM is ready
    window.DriverPayoutsManager = DriverPayoutsManager;

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.driverPayoutsManager = new DriverPayoutsManager();
        });
    } else {
        window.driverPayoutsManager = new DriverPayoutsManager();
    }
})();
