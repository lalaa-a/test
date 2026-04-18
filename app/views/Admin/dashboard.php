<section class="admin-dashboard-home">
    <div class="dashboard-overview">
        <div class="dashboard-overview-copy">
            <p class="dashboard-kicker">Admin Dashboard</p>
            <p class="dashboard-description">
                Quick access to the latest activity across verification, support, transactions, and trip operations.
            </p>
        </div>
    </div>

    <div class="summary-cards-grid">
        <a href="<?php echo URL_ROOT; ?>/admin/verification" class="summary-card summary-card-verification">
            <div class="summary-card-top">
                <div class="summary-card-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <span class="summary-card-tag">Verification</span>
            </div>
            <div class="summary-card-value" id="summaryPendingVerifications">--</div>
            <p class="summary-card-title">Pending Verifications</p>
            <p class="summary-card-meta" id="summaryPendingVerificationsMeta">Loading account, license, and vehicle queues...</p>
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/oversight" class="summary-card summary-card-support">
            <div class="summary-card-top">
                <div class="summary-card-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <span class="summary-card-tag">Support</span>
            </div>
            <div class="summary-card-value" id="summaryOpenSupportChats">--</div>
            <p class="summary-card-title">Open Support Chats</p>
            <p class="summary-card-meta" id="summaryOpenSupportChatsMeta">Loading helpdesk conversations...</p>
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/transaction" class="summary-card summary-card-payouts">
            <div class="summary-card-top">
                <div class="summary-card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span class="summary-card-tag">Transaction</span>
            </div>
            <div class="summary-card-value" id="summaryPendingPayouts">--</div>
            <p class="summary-card-title">Pending Payouts</p>
            <p class="summary-card-meta" id="summaryPendingPayoutsMeta">Loading driver and guide payouts...</p>
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/tripInfo" class="summary-card summary-card-trips">
            <div class="summary-card-top">
                <div class="summary-card-icon">
                    <i class="fas fa-route"></i>
                </div>
                <span class="summary-card-tag">Trip Info</span>
            </div>
            <div class="summary-card-value" id="summaryOngoingTrips">--</div>
            <p class="summary-card-title">Ongoing Trips</p>
            <p class="summary-card-meta" id="summaryOngoingTripsMeta">Loading trips currently in progress...</p>
        </a>
    </div>

    <section class="platform-overview-section">
        <div class="platform-overview-header">
            <div>
                <p class="platform-overview-kicker">Platform Overview</p>
                <p class="platform-overview-description">
                    User growth, verification, revenue, and vehicle status.
                </p>
            </div>

            <label class="platform-overview-filter">
                <span>Time Range</span>
                <select id="dashboardOverviewTimeRange">
                    <option value="30days">Last 30 days</option>
                    <option value="90days" selected>Last 90 days</option>
                    <option value="365days">Last 365 days</option>
                </select>
            </label>
        </div>

        <div class="overview-metrics-grid">
            <div class="overview-metric-card">
                <span class="overview-metric-label">Total Users</span>
                <strong class="overview-metric-value" id="overviewTotalUsers">--</strong>
                <span class="overview-metric-meta" id="overviewTotalUsersMeta">Loading platform user base...</span>
            </div>

            <div class="overview-metric-card">
                <span class="overview-metric-label">Verified Accounts</span>
                <strong class="overview-metric-value" id="overviewVerifiedAccounts">--</strong>
                <span class="overview-metric-meta" id="overviewVerifiedAccountsMeta">Loading approval totals...</span>
            </div>

            <div class="overview-metric-card">
                <span class="overview-metric-label">Total Vehicles</span>
                <strong class="overview-metric-value" id="overviewTotalVehicles">--</strong>
                <span class="overview-metric-meta" id="overviewTotalVehiclesMeta">Loading fleet size...</span>
            </div>

            <div class="overview-metric-card">
                <span class="overview-metric-label">Site Profit</span>
                <strong class="overview-metric-value" id="overviewSiteProfit">--</strong>
                <span class="overview-metric-meta" id="overviewSiteProfitMeta">Loading earnings snapshot...</span>
            </div>
        </div>

        <div class="overview-chart-grid">
            <article class="overview-chart-card overview-chart-card-wide">
                <div class="overview-chart-header">
                    <div>
                        <p class="overview-chart-kicker">User Growth</p>
                        <h4>Registration Trend</h4>
                    </div>
                    <span class="overview-chart-caption">Drivers, guides, and tourists over time</span>
                </div>
                <div class="overview-chart-canvas-wrap">
                    <canvas id="overviewRegistrationTrendChart"></canvas>
                </div>
            </article>

            <article class="overview-chart-card">
                <div class="overview-chart-header">
                    <div>
                        <p class="overview-chart-kicker">Verification</p>
                        <h4>Overall Verification Mix</h4>
                    </div>
                    <span class="overview-chart-caption">Current account status balance</span>
                </div>
                <div class="overview-chart-canvas-wrap overview-chart-canvas-wrap-compact">
                    <canvas id="overviewVerificationMixChart"></canvas>
                </div>
            </article>

            <article class="overview-chart-card overview-chart-card-wide">
                <div class="overview-chart-header">
                    <div>
                        <p class="overview-chart-kicker">Revenue</p>
                        <h4>Revenue Trend</h4>
                    </div>
                    <span class="overview-chart-caption">Total revenue and site profit movement</span>
                </div>
                <div class="overview-chart-canvas-wrap">
                    <canvas id="overviewRevenueTrendChart"></canvas>
                </div>
            </article>

            <article class="overview-chart-card">
                <div class="overview-chart-header">
                    <div>
                        <p class="overview-chart-kicker">Vehicles</p>
                        <h4>Vehicle Status Breakdown</h4>
                    </div>
                    <span class="overview-chart-caption">Approved, pending, and rejected vehicles</span>
                </div>
                <div class="overview-chart-canvas-wrap overview-chart-canvas-wrap-compact">
                    <canvas id="overviewVehicleStatusChart"></canvas>
                </div>
            </article>
        </div>
    </section>


</section>
