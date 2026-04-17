(function(){
// Traveler Payments JavaScript
    if (window.TravelerPaymentsManager) {
        console.log('TravelerPaymentsManager already exists, cleaning up...');
        if (window.travelerPaymentsManager) {
            delete window.travelerPaymentsManager;
        }
        delete window.TravelerPaymentsManager;
    }

    // Traveler Payments Manager
    class TravelerPaymentsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentUser = null;
            this.payments = {
                completed: [],
                cancelled: [],
                refunded: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadPayments();
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

            // Completed payments search and filter
            const completedSearchInput = document.getElementById('completedSearchInput');
            if (completedSearchInput) {
                completedSearchInput.addEventListener('input', (e) => {
                    this.filterPayments('completed', e.target.value);
                });
            }

            const completedPaymentFilter = document.getElementById('completedPaymentFilter');
            if (completedPaymentFilter) {
                completedPaymentFilter.addEventListener('change', () => {
                    this.filterPayments('completed');
                });
            }

            // Cancelled payments search and filter
            const cancelledSearchInput = document.getElementById('cancelledSearchInput');
            if (cancelledSearchInput) {
                cancelledSearchInput.addEventListener('input', (e) => {
                    this.filterPayments('cancelled', e.target.value);
                });
            }

            const cancelledPaymentFilter = document.getElementById('cancelledPaymentFilter');
            if (cancelledPaymentFilter) {
                cancelledPaymentFilter.addEventListener('change', () => {
                    this.filterPayments('cancelled');
                });
            }

            // Refunded payments search and filter
            const refundedSearchInput = document.getElementById('refundedSearchInput');
            if (refundedSearchInput) {
                refundedSearchInput.addEventListener('input', (e) => {
                    this.filterPayments('refunded', e.target.value);
                });
            }

            const refundedPaymentFilter = document.getElementById('refundedPaymentFilter');
            if (refundedPaymentFilter) {
                refundedPaymentFilter.addEventListener('change', () => {
                    this.filterPayments('refunded');
                });
            }

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal('paymentDetailsModal'));
            });

            // Modal overlay
            const paymentModal = document.getElementById('paymentDetailsModal');
            if (paymentModal) {
                paymentModal.addEventListener('click', (e) => {
                    if (e.target === paymentModal) {
                        this.closeModal('paymentDetailsModal');
                    }
                });
            }

            // View button clicks
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const paymentId = button.dataset.paymentId;
                    if (paymentId) {
                        this.showPaymentDetails(paymentId);
                    }
                }
            });
        }

        async loadPayments() {
            try {
                console.log('Loading payments...');
                // Load completed, cancelled, and refunded payments
                const [completedResponse, cancelledResponse, refundedResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getCompletedPayments`),
                    fetch(`${this.URL_ROOT}/moderator/getCancelledPayments`),
                    fetch(`${this.URL_ROOT}/moderator/getRefundedPayments`)
                ]);

                const [completedData, cancelledData, refundedData] = await Promise.all([
                    completedResponse.json(),
                    cancelledResponse.json(),
                    refundedResponse.json()
                ]);

                if (completedData.success) {
                    this.payments.completed = completedData.payments;
                }

                if (cancelledData.success) {
                    this.payments.cancelled = cancelledData.payments;
                }

                if (refundedData.success) {
                    this.payments.refunded = refundedData.payments;
                }

                this.updateStats();
                this.renderPayments('completed');
                this.renderPayments('cancelled');
                this.renderPayments('refunded');
                this.switchToTab('completed-section');
            } catch (error) {
                console.error('Error loading payments:', error);
                window.showNotification('Error loading payments', 'error');
            }
        }

        updateStats() {
            const completedCount = this.payments.completed.length;
            const cancelledCount = this.payments.cancelled.length;
            const refundedCount = this.payments.refunded.length;
            const totalRevenue = this.payments.completed.reduce((sum, payment) => sum + (parseFloat(payment.amount) || 0), 0);

            document.getElementById('completedPaymentsCount').textContent = completedCount;
            document.getElementById('cancelledPaymentsCount').textContent = cancelledCount;
            document.getElementById('refundedPaymentsCount').textContent = refundedCount;
            document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toFixed(2);
        }

        filterPayments(section, searchTerm = '') {
            const paymentFilter = document.getElementById(`${section}PaymentFilter`);
            const filterValue = paymentFilter ? paymentFilter.value : 'all';

            let filteredPayments = [...this.payments[section]];

            if (filterValue !== 'all') {
                filteredPayments = filteredPayments.filter(payment => payment.paymentType === filterValue);
            }

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredPayments = filteredPayments.filter(payment =>
                    payment.tripId.toString().includes(term) ||
                    payment.travelerName.toLowerCase().includes(term) ||
                    payment.providerName.toLowerCase().includes(term) ||
                    payment.transactionId?.toLowerCase().includes(term)
                );
            }

            this.renderPayments(section, filteredPayments);
        }

        renderPayments(section, payments = null) {
            const targetGrid = section === 'completed' ? 'completedPaymentsGrid' :
                             section === 'cancelled' ? 'cancelledPaymentsGrid' : 'refundedPaymentsGrid';

            if (payments === null) {
                payments = this.payments[section];
            }

            const tbody = document.getElementById(targetGrid);

            if (payments.length === 0) {
                const sectionName = section === 'completed' ? 'completed' :
                                   section === 'cancelled' ? 'cancelled' : 'refunded';
                const icon = section === 'completed' ? 'check-circle' :
                            section === 'cancelled' ? 'times-circle' : 'undo-alt';
                const message = section === 'completed' ? 'yet' :
                               section === 'cancelled' ? 'yet' : 'yet';
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-${icon}"></i>
                            <p>No ${sectionName} payments ${message}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = payments.map(payment => this.createPaymentRow(payment, section)).join('');
        }

        createPaymentRow(payment, section) {
            const amount = section === 'refunded' ? parseFloat(payment.refundAmount) : parseFloat(payment.amount);
            const date = section === 'refunded' ? payment.refundDate : payment.paymentDate;
            const status = this.getPaymentStatus(payment, section);

            return `
                <tr class="account-row" data-payment-id="${payment.id}">
                    <td class="trip-id-cell">${payment.tripId}</td>
                    <td class="traveler-cell">
                        <div class="traveler-info">
                            <div class="traveler-name">${this.escapeHtml(payment.travelerName)}</div>
                            <div class="traveler-email">${this.escapeHtml(payment.travelerEmail)}</div>
                        </div>
                    </td>
                    <td class="provider-cell">
                        <div class="provider-info">
                            <div class="provider-name">${this.escapeHtml(payment.providerName)}</div>
                            <div class="provider-type">${this.escapeHtml(payment.providerType)}</div>
                        </div>
                    </td>
                    <td class="amount-cell">$${amount.toFixed(2)}</td>
                    <td class="date-cell">${this.formatDate(date)}</td>
                    <td class="status-cell">
                        <span class="status-badge ${status.class}">${status.text}</span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn-view" data-payment-id="${payment.id}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </td>
                </tr>
            `;
        }

        getPaymentStatus(payment, section) {
            if (section === 'completed') {
                if (payment.pDoneTraveller && payment.pDoneSite) {
                    return { text: 'Completed', class: 'status-completed' };
                } else if (payment.pDoneTraveller) {
                    return { text: 'Paid by Traveler', class: 'status-partial' };
                } else {
                    return { text: 'Pending Site Payment', class: 'status-pending' };
                }
            } else if (section === 'cancelled') {
                return { text: 'Cancelled', class: 'status-cancelled' };
            } else if (section === 'refunded') {
                return { text: 'Refunded', class: 'status-refunded' };
            }
            return { text: 'Unknown', class: 'status-unknown' };
        }

        showPaymentDetails(paymentId) {
            // Find payment across all sections
            let payment = null;
            let section = '';

            for (const [sec, payments] of Object.entries(this.payments)) {
                payment = payments.find(p => p.id == paymentId);
                if (payment) {
                    section = sec;
                    break;
                }
            }

            if (!payment) return;

            const modalContent = document.getElementById('paymentDetailsContent');

            if (section === 'completed') {
                modalContent.innerHTML = this.renderCompletedPaymentDetails(payment);
            } else if (section === 'cancelled') {
                modalContent.innerHTML = this.renderCancelledPaymentDetails(payment);
            } else if (section === 'refunded') {
                modalContent.innerHTML = this.renderRefundedPaymentDetails(payment);
            }

            this.openModal('paymentDetailsModal');
        }

        renderCompletedPaymentDetails(payment) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 3rem;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Payment #${payment.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-check-circle"></i>
                            Completed Payment
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Payment Information</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payment.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Total Amount:</label>
                                <span class="amount">$${parseFloat(payment.amount).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Transaction ID:</label>
                                <span class="transaction-id">${payment.transactionId || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Payment Date:</label>
                                <span>${this.formatDate(payment.paymentDate)}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-tasks"></i> Payment Status</h4>
                            <div class="info-item">
                                <label>Traveler Payment:</label>
                                <span class="${payment.pDoneTraveller ? 'completed' : 'pending'}">
                                    ${payment.pDoneTraveller ? '✓ Completed' : '○ Pending'} (${this.formatDate(payment.pDateTraveller)})
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Site Payment:</label>
                                <span class="${payment.pDoneSite ? 'completed' : 'pending'}">
                                    ${payment.pDoneSite ? '✓ Completed' : '○ Pending'} (${this.formatDate(payment.pDateSite)})
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        renderCancelledPaymentDetails(payment) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); display: flex; align-items: center; justify-content: center; color: #c62828; font-size: 3rem;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h3>Payment #${payment.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-times-circle"></i>
                            Cancelled Payment
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Cancellation Details</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payment.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Cancellation Date:</label>
                                <span>${this.formatDate(payment.paymentDate)}</span>
                            </div>
                            <div class="info-item">
                                <label>Status:</label>
                                <span class="status-cancelled">Cancelled</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-user"></i> Traveler Information</h4>
                            <div class="info-item">
                                <label>Name:</label>
                                <span>${this.escapeHtml(payment.travelerName)}</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${this.escapeHtml(payment.travelerEmail)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        renderRefundedPaymentDetails(payment) {
            return `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); display: flex; align-items: center; justify-content: center; color: #f57c00; font-size: 3rem;">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h3>Payment #${payment.id}</h3>
                        <div class="user-account-type">
                            <i class="fas fa-undo-alt"></i>
                            Refunded Payment
                        </div>
                    </div>
                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-info-circle"></i> Refund Details</h4>
                            <div class="info-item">
                                <label>Trip ID:</label>
                                <span>${payment.tripId}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Amount:</label>
                                <span class="amount">$${parseFloat(payment.refundAmount).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Date:</label>
                                <span>${this.formatDate(payment.refundDate)}</span>
                            </div>
                            <div class="info-item">
                                <label>Reason:</label>
                                <span>${this.escapeHtml(payment.refundReason || 'N/A')}</span>
                            </div>
                        </div>
                        <div class="info-group">
                            <h4><i class="fas fa-credit-card"></i> Transaction Information</h4>
                            <div class="info-item">
                                <label>Original Transaction:</label>
                                <span class="transaction-id">${payment.transactionId || 'N/A'}</span>
                            </div>
                            <div class="info-item">
                                <label>Refund Status:</label>
                                <span class="status-refunded">Completed</span>
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
    window.TravelerPaymentsManager = TravelerPaymentsManager;

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.travelerPaymentsManager = new TravelerPaymentsManager();
        });
    } else {
        window.travelerPaymentsManager = new TravelerPaymentsManager();
    }
})();
