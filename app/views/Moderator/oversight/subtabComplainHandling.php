<!-- Complain Handling Content -->

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Complain Handling</h1>
            <p class="page-subtitle">Review and manage user complaints and feedback</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="pendingComplaintsCount">0</div>
            <div class="stat-label">Pending Complaints</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon in-progress">
            <i class="fas fa-cog"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="inProgressComplaintsCount">0</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number" id="completedComplaintsCount">0</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="section-nav">
    <a href="#pending-section" class="nav-link active" data-section="pending">
        <i class="fas fa-clock"></i>
        Pending
    </a>
    <a href="#in-progress-section" class="nav-link" data-section="in_progress">
        <i class="fas fa-cog"></i>
        In Progress
    </a>
    <a href="#completed-section" class="nav-link" data-section="completed">
        <i class="fas fa-check-circle"></i>
        Completed
    </a>
</div>

<!-- Complain Sections -->
<div class="complain-sections">
    <!-- Pending Complaints Section -->
    <div class="complain-section" id="pending-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Complaints
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="pendingSearchInput" placeholder="Search pending complaints..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="pendingSubjectFilter" class="filter-select">
                                <option value="all">All Subjects</option>
                                <option value="booking">Booking Issue</option>
                                <option value="payment">Payment Problem</option>
                                <option value="trip">Trip Experience</option>
                                <option value="guide_driver">Guide / Driver Concern</option>
                                <option value="account">Account Help</option>
                                <option value="feature">Feature Suggestion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="complaints-table-container" id="pendingComplaintsContainer">
            <table class="complaints-table" id="pendingComplaintsTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Submitted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pendingComplaintsGrid">
                    <tr class="no-complaints">
                        <td colspan="5">
                            <i class="fas fa-inbox"></i>
                            <p>No pending complaints</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- In Progress Complaints Section -->
    <div class="complain-section" id="in-progress-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-cog"></i>
                    In Progress Complaints
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="inProgressSearchInput" placeholder="Search in progress complaints..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="inProgressSubjectFilter" class="filter-select">
                                <option value="all">All Subjects</option>
                                <option value="booking">Booking Issue</option>
                                <option value="payment">Payment Problem</option>
                                <option value="trip">Trip Experience</option>
                                <option value="guide_driver">Guide / Driver Concern</option>
                                <option value="account">Account Help</option>
                                <option value="feature">Feature Suggestion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="complaints-table-container" id="inProgressComplaintsContainer">
            <table class="complaints-table" id="inProgressComplaintsTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Handled By</th>
                        <th>Started Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inProgressComplaintsGrid">
                    <tr class="no-complaints">
                        <td colspan="6">
                            <i class="fas fa-cog"></i>
                            <p>No complaints in progress</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Complaints Section -->
    <div class="complain-section" id="completed-section">
        <div class="section-header">
            <div class="section-header-content">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Completed Complaints
                </h2>
                <div class="section-controls">
                    <div class="search-filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completedSearchInput" placeholder="Search completed complaints..." class="search-input">
                        </div>
                        <div class="filter-dropdown">
                            <select id="completedSubjectFilter" class="filter-select">
                                <option value="all">All Subjects</option>
                                <option value="booking">Booking Issue</option>
                                <option value="payment">Payment Problem</option>
                                <option value="trip">Trip Experience</option>
                                <option value="guide_driver">Guide / Driver Concern</option>
                                <option value="account">Account Help</option>
                                <option value="feature">Feature Suggestion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="complaints-table-container" id="completedComplaintsContainer">
            <table class="complaints-table" id="completedComplaintsTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Handled By</th>
                        <th>Completed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="completedComplaintsGrid">
                    <tr class="no-complaints">
                        <td colspan="6">
                            <i class="fas fa-check-circle"></i>
                            <p>No completed complaints yet</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Complaint Details Modal -->
<div id="complaintDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Complaint Details</h3>
            <button class="modal-close" onclick="closeComplaintModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="complaintDetailsContent">
            <!-- Complaint details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeComplaintModal()">Close</button>
            <button class="btn btn-primary" id="startHandlingBtn" onclick="complainHandlingManager.startHandling()">Start Handling</button>
            <button class="btn btn-success" id="markCompletedBtn" onclick="complainHandlingManager.markCompleted()">Mark Completed</button>
        </div>
    </div>
</div>