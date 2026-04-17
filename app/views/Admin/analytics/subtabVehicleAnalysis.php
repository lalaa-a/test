<!-- Vehicle Analysis Content -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Vehicle Analysis</h1>
            <p class="page-subtitle">Comprehensive analysis of vehicle verification status and driver vehicle management</p>
        </div>
        <div class="header-controls">
            <select id="vehicleAnalysisTimeRange" class="filter-select">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days" selected>Last 90 Days</option>
                <option value="1year">Last Year</option>
                <option value="all">All Time</option>
            </select>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalVehiclesCount">0</div>
            <div class="stat-label">Total Vehicles</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon verified">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="verifiedVehiclesCount">0</div>
            <div class="stat-label">Verified Vehicles</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="pendingVehiclesCount">0</div>
            <div class="stat-label">Pending Verification</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rejected">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="rejectedVehiclesCount">0</div>
            <div class="stat-label">Rejected Vehicles</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#overview-section" class="nav-link active" data-section="overview">
        <i class="fas fa-chart-pie"></i>
        Overview
    </a>
    <a href="#bookings-section" class="nav-link" data-section="bookings">
        <i class="fas fa-car"></i>
        Vehicle Models
    </a>
    <a href="#compliance-section" class="nav-link" data-section="compliance">
        <i class="fas fa-shield-alt"></i>
        Compliance
    </a>
</div>

<!-- Analysis Sections -->
<div class="analysis-sections">
    <!-- Overview Section -->
    <div class="analysis-section" id="overview-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-chart-pie"></i>
                Vehicle Overview
            </h2>
            <p>General statistics and trends for vehicle verification</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Verification Status Distribution</h3>
                    <p>Breakdown of vehicle verification statuses</p>
                </div>
                <div class="chart-body">
                    <canvas id="vehicleStatusChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Verification Trends</h3>
                    <p>Vehicle verification submissions over time</p>
                </div>
                <div class="chart-body">
                    <canvas id="vehicleTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Verification Activity</h3>
                    <p>Verification activity by month</p>
                </div>
                <div class="chart-body">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Driver Vehicle Ownership</h3>
                    <p>Vehicles per driver distribution</p>
                </div>
                <div class="chart-body">
                    <canvas id="driverOwnershipChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Models Section -->
    <div class="analysis-section" id="bookings-section" style="display: none;">
        <div class="section-header">
            <h2>
                <i class="fas fa-car"></i>
                Vehicle Models Analysis
            </h2>
            <p>Analysis of vehicle model distribution in the fleet</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Vehicle Model Distribution</h3>
                    <p>Distribution of vehicles by model type</p>
                </div>
                <div class="chart-body">
                    <canvas id="vehicleTypeChart" width="400" height="300"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Booking Trends</h3>
                    <p>Vehicle booking patterns over time</p>
                </div>
                <div class="chart-body">
                    <canvas id="bookingTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-breakdown">
            <div class="breakdown-card">
                <h4>Fleet Statistics</h4>
                <div class="breakdown-stats">
                    <div class="breakdown-item">
                        <span class="breakdown-label">Total Vehicles:</span>
                        <span class="breakdown-value" id="totalBookingsCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Available Vehicles:</span>
                        <span class="breakdown-value" id="activeBookingsCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Approved Vehicles:</span>
                        <span class="breakdown-value" id="completedTripsCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Average Seating:</span>
                        <span class="breakdown-value" id="avgTripDistance">0 seats</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Section -->
    <div class="analysis-section" id="compliance-section" style="display: none;">
        <div class="section-header">
            <h2>
                <i class="fas fa-shield-alt"></i>
                Vehicle Compliance
            </h2>
            <p>Vehicle compliance status and expiry tracking</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Verification Status</h3>
                    <p>Vehicle verification breakdown</p>
                </div>
                <div class="chart-body">
                    <canvas id="complianceChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Verification Trends</h3>
                    <p>Verification activity over time</p>
                </div>
                <div class="chart-body">
                    <canvas id="verificationTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Details Modal -->
<div id="vehicleDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Vehicle Details</h3>
            <button class="modal-close" onclick="closeVehicleModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="vehicleDetailsContent">
            <!-- Vehicle details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeVehicleModal()">Close</button>
        </div>
    </div>
</div>