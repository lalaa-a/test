<!-- Service Charge Management -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Service Charge Management</h1>
            <p class="page-subtitle">Set booking charges and service fees for drivers and guides</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon driver">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="currentDriverCharge">LKR 0.00</div>
            <div class="stat-label">Driver Booking Charge</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon guide">
            <i class="fas fa-user-friends"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="currentGuideCharge">LKR 0.00</div>
            <div class="stat-label">Guide Booking Charge</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon service">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="currentServiceCharge">0.00%</div>
            <div class="stat-label">Site Service Charge</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon active">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="lastUpdated">Never</div>
            <div class="stat-label">Last Updated</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#add-charges-section" class="nav-link active" data-section="add-charges">
        <i class="fas fa-plus-circle"></i>
        Add Charges
    </a>
    <a href="#charge-history-section" class="nav-link" data-section="charge-history">
        <i class="fas fa-history"></i>
        Charge History
    </a>
    <a href="#charge-analytics-section" class="nav-link" data-section="charge-analytics">
        <i class="fas fa-chart-line"></i>
        Charge Analytics
    </a>
</div>

<!-- Service Charge Sections -->
<div class="verification-sections">
    <!-- Add Charges Section -->
    <div class="verification-section" id="add-charges-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-plus-circle"></i>
                    Add Charges
                </h2>
            </div>
        </div>

        <div class="charge-sections">
            <!-- Driver Booking Charge Section -->
            <div class="charge-section">
                <div class="section-header">
                    <h2><i class="fas fa-car"></i> Driver Booking Charge</h2>
                    <p class="section-description">Fixed amount charged per driver booking (kept by the site)</p>
                </div>
                <div class="charge-card" id="driverChargeCard">
                    <div class="charge-card-content">
                        <div class="charge-info">
                            <div class="charge-amount">
                                <span class="amount-label">Current Charge:</span>
                                <span class="amount-value" id="driverChargeAmount">LKR 0.00</span>
                            </div>
                            <div class="charge-status">
                                <span class="status-label">Status:</span>
                                <span class="status-value active">Active</span>
                            </div>
                        </div>
                        <div class="charge-actions">
                            <button class="btn-view-charge" onclick="serviceChargeManager.viewDriverCharge()">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn-edit-charge" onclick="serviceChargeManager.openDriverChargeModal()">
                                <i class="fas fa-edit"></i> Set Charge
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guide Booking Charge Section -->
            <div class="charge-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-friends"></i> Guide Booking Charge</h2>
                    <p class="section-description">Fixed amount charged per guide booking (kept by the site)</p>
                </div>
                <div class="charge-card" id="guideChargeCard">
                    <div class="charge-card-content">
                        <div class="charge-info">
                            <div class="charge-amount">
                                <span class="amount-label">Current Charge:</span>
                                <span class="amount-value" id="guideChargeAmount">LKR 0.00</span>
                            </div>
                            <div class="charge-status">
                                <span class="status-label">Status:</span>
                                <span class="status-value active">Active</span>
                            </div>
                        </div>
                        <div class="charge-actions">
                            <button class="btn-view-charge" onclick="serviceChargeManager.viewGuideCharge()">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn-edit-charge" onclick="serviceChargeManager.openGuideChargeModal()">
                                <i class="fas fa-edit"></i> Set Charge
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Site Service Charge Section -->
            <div class="charge-section">
                <div class="section-header">
                    <h2><i class="fas fa-percentage"></i> Site Service Charge</h2>
                    <p class="section-description">Percentage charged on total booking amount (kept by the site)</p>
                </div>
                <div class="charge-card" id="serviceChargeCard">
                    <div class="charge-card-content">
                        <div class="charge-info">
                            <div class="charge-amount">
                                <span class="amount-label">Current Charge:</span>
                                <span class="amount-value" id="serviceChargeAmount">0.00%</span>
                            </div>
                            <div class="charge-status">
                                <span class="status-label">Status:</span>
                                <span class="status-value active">Active</span>
                            </div>
                        </div>
                        <div class="charge-actions">
                            <button class="btn-view-charge" onclick="serviceChargeManager.viewServiceCharge()">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn-edit-charge" onclick="serviceChargeManager.openServiceChargeModal()">
                                <i class="fas fa-edit"></i> Set Charge
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charge History Section -->
    <div class="verification-section" id="charge-history-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-history"></i>
                    Charge History
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="chargeHistorySearchInput" placeholder="Search history..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="chargeHistoryFilter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="driver">Driver Charges</option>
                                <option value="guide">Guide Charges</option>
                                <option value="service">Service Charges</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="history-container">
            <div class="history-table-container">
                <table class="history-table" id="chargeHistoryTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Driver Charge</th>
                            <th>Guide Charge</th>
                            <th>Service Charge</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="chargeHistoryGrid">
                        <tr class="no-history">
                            <td colspan="6">
                                <i class="fas fa-history"></i>
                                <p>No charge history yet</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charge Analytics Section -->
    <div class="verification-section" id="charge-analytics-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-chart-line"></i>
                    Charge Analytics
                </h2>
                <div class="section-controls">
                    <div class="chart-controls">
                        <select id="chartTypeSelect" class="filter-select">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="area">Area Chart</option>
                        </select>
                        <select id="timeRangeSelect" class="filter-select">
                            <option value="all">All Time</option>
                            <option value="6months">Last 6 Months</option>
                            <option value="1year">Last Year</option>
                            <option value="2years">Last 2 Years</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-container">
            <!-- Chart Container -->
            <div class="chart-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Service Charge Evolution</h3>
                        <p class="chart-description">Track how driver, guide, and site service charges have changed over time</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="chargeEvolutionChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="summary-stats">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value" id="totalChanges">0</div>
                        <div class="summary-label">Total Changes</div>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value" id="avgChangeFrequency">-</div>
                        <div class="summary-label">Avg. Change Frequency</div>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value" id="currentTotalCharge">LKR 0.00</div>
                        <div class="summary-label">Current Total Charge</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Driver Charge Modal -->
<div id="driverChargeModal" class="modal">
    <div class="modal-content charge-modal">
        <div class="modal-header">
            <h3><i class="fas fa-car"></i> Set Driver Booking Charge</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="driverChargeForm" class="charge-form">
                <div class="form-section">
                    <h4><i class="fas fa-car"></i> Driver Booking Charge</h4>
                    <p class="form-hint">Fixed amount charged per driver booking (kept by the site)</p>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="driverBookingCharge">Driver Booking Charge (LKR) *</label>
                            <input type="number" id="driverBookingCharge" name="driverBookingCharge" min="0" step="0.01" placeholder="e.g., 500.00" required>
                            <small class="form-hint">Amount in Sri Lankan Rupees</small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-sticky-note"></i> Notes (Optional)</h4>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="driverChargeNotes">Notes</label>
                            <textarea id="driverChargeNotes" name="driverChargeNotes" rows="3" placeholder="Add any additional notes about this charge..." maxlength="500"></textarea>
                            <small class="form-hint">Maximum 500 characters</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="serviceChargeManager.closeModal('driverChargeModal')">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Driver Charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Guide Charge Modal -->
<div id="guideChargeModal" class="modal">
    <div class="modal-content charge-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-friends"></i> Set Guide Booking Charge</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="guideChargeForm" class="charge-form">
                <div class="form-section">
                    <h4><i class="fas fa-user-friends"></i> Guide Booking Charge</h4>
                    <p class="form-hint">Fixed amount charged per guide booking (kept by the site)</p>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="guideBookingCharge">Guide Booking Charge (LKR) *</label>
                            <input type="number" id="guideBookingCharge" name="guideBookingCharge" min="0" step="0.01" placeholder="e.g., 300.00" required>
                            <small class="form-hint">Amount in Sri Lankan Rupees</small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-sticky-note"></i> Notes (Optional)</h4>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="guideChargeNotes">Notes</label>
                            <textarea id="guideChargeNotes" name="guideChargeNotes" rows="3" placeholder="Add any additional notes about this charge..." maxlength="500"></textarea>
                            <small class="form-hint">Maximum 500 characters</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="serviceChargeManager.closeModal('guideChargeModal')">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Guide Charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Service Charge Modal -->
<div id="serviceChargeModal" class="modal">
    <div class="modal-content charge-modal">
        <div class="modal-header">
            <h3><i class="fas fa-percentage"></i> Set Site Service Charge</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="serviceChargeForm" class="charge-form">
                <div class="form-section">
                    <h4><i class="fas fa-percentage"></i> Site Service Charge</h4>
                    <p class="form-hint">Percentage charged on total booking amount (kept by the site)</p>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="siteServiceCharge">Site Service Charge (%) *</label>
                            <input type="number" id="siteServiceCharge" name="siteServiceCharge" min="0" max="100" step="0.01" placeholder="e.g., 5.00" required>
                            <small class="form-hint">Percentage (0-100)</small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-sticky-note"></i> Notes (Optional)</h4>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="serviceChargeNotes">Notes</label>
                            <textarea id="serviceChargeNotes" name="serviceChargeNotes" rows="3" placeholder="Add any additional notes about this charge..." maxlength="500"></textarea>
                            <small class="form-hint">Maximum 500 characters</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="serviceChargeManager.closeModal('serviceChargeModal')">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Service Charge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Charge Details Modal -->
<div id="viewChargeModal" class="modal">
    <div class="modal-content charge-modal">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Charge Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="charge-details" id="chargeDetails">
                <!-- Charge details will be populated by JavaScript -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="serviceChargeManager.closeModal('viewChargeModal')">Close</button>
        </div>
    </div>
</div>
