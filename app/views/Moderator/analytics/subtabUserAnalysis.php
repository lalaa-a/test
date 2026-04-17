<!-- User Analysis Content -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">User Analysis</h1>
            <p class="page-subtitle">Comprehensive analysis of user base, verification status, and registration trends</p>
        </div>
        <div class="header-controls">
            <select id="userAnalysisTimeRange" class="filter-select">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days">Last 90 Days</option>
                <option value="1year">Last Year</option>
                <option value="all" selected>All Time</option>
            </select>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalUsersCount">0</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon driver">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalDriversCount">0</div>
            <div class="stat-label">Total Drivers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon guide">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalGuidesCount">0</div>
            <div class="stat-label">Total Guides</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon tourist">
            <i class="fas fa-suitcase"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="totalTouristsCount">0</div>
            <div class="stat-label">Total Tourists</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon verified">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="verifiedUsersCount">0</div>
            <div class="stat-label">Verified Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="pendingVerificationsCount">0</div>
            <div class="stat-label">Pending Verifications</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#overview-section" class="nav-link active" data-section="overview">
        <i class="fas fa-chart-pie"></i>
        Overview
    </a>
    <a href="#verification-section" class="nav-link" data-section="verification">
        <i class="fas fa-user-check"></i>
        Verification Status
    </a>
    <a href="#registration-section" class="nav-link" data-section="registration">
        <i class="fas fa-calendar-alt"></i>
        Registration Trends
    </a>
    <a href="#license-section" class="nav-link" data-section="license">
        <i class="fas fa-id-card"></i>
        Tourist Licenses
    </a>
</div>

<!-- Analysis Sections -->
<div class="analysis-sections">
    <!-- Overview Section -->
    <div class="analysis-section" id="overview-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-chart-pie"></i>
                User Base Overview
            </h2>
            <p>Distribution of users by account type and verification status</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>User Distribution</h3>
                    <p>Breakdown by account type</p>
                </div>
                <div class="chart-body">
                    <canvas id="userDistributionChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Verification Overview</h3>
                    <p>Verified vs unverified users</p>
                </div>
                <div class="chart-body">
                    <canvas id="verificationOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Status Section -->
    <div class="analysis-section" id="verification-section" style="display: none;">
        <div class="section-header">
            <h2>
                <i class="fas fa-user-check"></i>
                Verification Status Breakdown
            </h2>
            <p>Detailed verification status for drivers and guides</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Driver Verification Status</h3>
                    <p>Approved, pending, and rejected drivers</p>
                </div>
                <div class="chart-body">
                    <canvas id="driverVerificationChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Guide Verification Status</h3>
                    <p>Approved, pending, and rejected guides</p>
                </div>
                <div class="chart-body">
                    <canvas id="guideVerificationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-breakdown">
            <div class="breakdown-card">
                <h4>Account Verification Statistics</h4>
                <div class="breakdown-stats">
                    <div class="breakdown-item">
                        <span class="breakdown-label">Verified Drivers:</span>
                        <span class="breakdown-value" id="verifiedDriversCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Verified Guides:</span>
                        <span class="breakdown-value" id="verifiedGuidesCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Pending Drivers:</span>
                        <span class="breakdown-value" id="pendingDriversCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Pending Guides:</span>
                        <span class="breakdown-value" id="pendingGuidesCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Rejected Drivers:</span>
                        <span class="breakdown-value" id="rejectedDriversCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Rejected Guides:</span>
                        <span class="breakdown-value" id="rejectedGuidesCount">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Trends Section -->
    <div class="analysis-section" id="registration-section" style="display: none;">
        <div class="section-header">
            <h2>
                <i class="fas fa-calendar-alt"></i>
                User Registration Trends
            </h2>
            <p>User registration patterns over time</p>
        </div>

        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>Registration Timeline</h3>
                <p>Daily user registrations by account type</p>
            </div>
            <div class="chart-body">
                <canvas id="registrationTrendChart"></canvas>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Growth</h3>
                    <p>User registration growth by month</p>
                </div>
                <div class="chart-body">
                    <canvas id="monthlyGrowthChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Peak Registration Days</h3>
                    <p>Most active registration days</p>
                </div>
                <div class="chart-body">
                    <canvas id="peakDaysChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tourist License Section -->
    <div class="analysis-section" id="license-section" style="display: none;">
        <div class="section-header">
            <h2>
                <i class="fas fa-id-card"></i>
                Tourist License Verification
            </h2>
            <p>Tourist license status for drivers and guides</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Driver License Status</h3>
                    <p>Tourist license verification for drivers</p>
                </div>
                <div class="chart-body">
                    <canvas id="driverLicenseChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Guide License Status</h3>
                    <p>Tourist license verification for guides</p>
                </div>
                <div class="chart-body">
                    <canvas id="guideLicenseChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-breakdown">
            <div class="breakdown-card">
                <h4>Tourist License Statistics</h4>
                <div class="breakdown-stats">
                    <div class="breakdown-item">
                        <span class="breakdown-label">Drivers with Valid License:</span>
                        <span class="breakdown-value" id="driversWithLicenseCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Guides with Valid License:</span>
                        <span class="breakdown-value" id="guidesWithLicenseCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Pending Driver Licenses:</span>
                        <span class="breakdown-value" id="pendingDriverLicensesCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Pending Guide Licenses:</span>
                        <span class="breakdown-value" id="pendingGuideLicensesCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Rejected Driver Licenses:</span>
                        <span class="breakdown-value" id="rejectedDriverLicensesCount">0</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Rejected Guide Licenses:</span>
                        <span class="breakdown-value" id="rejectedGuideLicensesCount">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>