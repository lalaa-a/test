<!-- Compact Navigation Bar -->
<nav class="content-nav">
    <ul class="nav-tabs">
        <li class="nav-tab">
            <button class="nav-tab-link active" data-tab="subtabModeratorInfo">
                <i class="fa-solid fa-user-shield"></i>
                <span class="nav-tab-text">Moderator Accounts</span>
            </button>
        </li>
    </ul>
</nav>

<div id="content-subtab-loader" class="content-subtab-loader">
    <div class="verification-container">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">Moderator Accounts</h1>
                    <p class="page-subtitle" id="moderatorStatsStatus">Loading moderator records...</p>
                </div>
                <div class="header-actions">
                    <button id="openAddModeratorModalBtn" class="btn btn-primary btn-add-moderator" type="button">
                        <i class="fa-solid fa-plus"></i>
                        Add Moderator
                    </button>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="totalModeratorsCount">0</div>
                    <div class="stat-label">Total Moderators</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="activeModeratorsCount">0</div>
                    <div class="stat-label">Active Moderators</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon refunded">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="newModeratorsCount">0</div>
                    <div class="stat-label">New Moderators</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="inactiveModeratorsCount">0</div>
                    <div class="stat-label">Inactive Moderators</div>
                </div>
            </div>
        </div>

        <div class="verification-section" id="pending-section">
            <div class="section-header">
                <div class="section-header-content">
                    <h2>
                        <i class="fas fa-users"></i>
                        Moderators
                    </h2>
                    <div class="section-controls">
                        <div class="search-filter-section">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="pendingSearchInput" placeholder="Search moderators..." class="search-input">
                            </div>
                            <div class="filter-dropdown">
                                <select id="pendingAccountTypeFilter" class="filter-select">
                                    <option value="all">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accounts-table-container" id="pendingAccountsContainer">
                <table class="accounts-table" id="pendingAccountsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="moderatorsGrid">
                        <tr class="no-accounts">
                            <td colspan="7">
                                <i class="fas fa-inbox"></i>
                                <p>No moderators found</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="moderatorDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Moderator Details</h3>
            <button class="modal-close" type="button" data-close-modal="moderatorDetailsModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="moderatorDetailsForm">
                <input type="hidden" id="moderatorId" name="id">
                <input type="hidden" id="moderatorAccountType" name="account_type">

                <div class="user-details-grid moderator-modal-grid">
                    <div class="user-profile-section">
                        <div class="user-profile-photo moderator-avatar-placeholder">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 id="moderatorModalName">Moderator</h3>
                        <div class="user-account-type" id="moderatorModalStatus">
                            <i class="fas fa-circle-check"></i>
                            Active
                        </div>
                    </div>

                    <div class="user-info-section">
                        <div class="info-group">
                            <h4><i class="fas fa-id-card"></i> Basic Information</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="moderatorFullname">Full Name <span class="required">*</span></label>
                                    <input type="text" id="moderatorFullname" name="fullname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="moderatorEmail">Email <span class="required">*</span></label>
                                    <input type="email" id="moderatorEmail" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="moderatorPhone">Phone <span class="required">*</span></label>
                                    <input type="text" id="moderatorPhone" name="phone" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="moderatorSecondaryPhone">Secondary Phone</label>
                                    <input type="text" id="moderatorSecondaryPhone" name="secondary_phone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="info-group">
                            <h4><i class="fas fa-user-gear"></i> Personal Details</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="moderatorLanguage">Language <span class="required">*</span></label>
                                    <input type="text" id="moderatorLanguage" name="language" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="moderatorGender">Gender <span class="required">*</span></label>
                                    <select id="moderatorGender" name="gender" class="form-control" required>
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="moderatorDob">Date of Birth <span class="required">*</span></label>
                                    <input type="date" id="moderatorDob" name="dob" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="info-group">
                            <h4><i class="fas fa-location-dot"></i> Address</h4>
                            <div class="form-group">
                                <label for="moderatorAddress">Address <span class="required">*</span></label>
                                <textarea id="moderatorAddress" name="address" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="info-group">
                            <h4><i class="fas fa-clock-rotate-left"></i> Activity</h4>
                            <div class="info-item">
                                <label>Last Login</label>
                                <span id="moderatorLastLogin">Never</span>
                            </div>
                            <div class="info-item">
                                <label>Created Date</label>
                                <span id="moderatorCreatedDate">N/A</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-close-modal="moderatorDetailsModal">Close</button>
            <button class="btn btn-primary" id="openUpdateModeratorConfirmBtn" type="button">Save Changes</button>
            <button class="btn btn-danger" id="openDeleteModeratorConfirmBtn" type="button">Delete Moderator</button>
        </div>
    </div>
</div>

<div id="updateModeratorConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-check"></i> Confirm Moderator Update</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-floppy-disk"></i>
                <p>Are you sure you want to save these moderator changes?</p>
                <p class="confirm-warning">This will update the moderator record immediately.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelUpdateModeratorBtn" type="button">Cancel</button>
            <button class="btn btn-primary" id="confirmUpdateModeratorBtn" type="button">Save Changes</button>
        </div>
    </div>
</div>

<div id="deleteModeratorConfirmModal" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-times"></i> Confirm Moderator Delete</h3>
        </div>
        <div class="modal-body">
            <div class="confirm-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to delete this moderator?</p>
                <p class="confirm-warning">This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelDeleteModeratorBtn" type="button">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteModeratorBtn" type="button">Delete Moderator</button>
        </div>
    </div>
</div>

<div id="addModeratorModal" class="admin-modal" aria-hidden="true">
    <div class="admin-modal-content moderator-modal-content">
        <div class="admin-modal-header moderator-modal-header">
            <h3>
                <i class="fa-solid fa-plus"></i>
                Add New Moderator
            </h3>
            <button id="closeAddModeratorModalBtn" class="admin-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>

        <form id="moderatorQuickForm" class="moderator-modal-form">
            <input type="hidden" id="modType" value="site_moderator">

            <div id="moderatorModalFeedback" class="feedback" aria-live="polite"></div>

            <section class="admin-modal-section">
                <div class="section-title">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Moderator Information</span>
                </div>

                <div class="modal-form-grid">
                    <div class="modal-form-group">
                        <label for="modName">Full Name *</label>
                        <input type="text" id="modName" placeholder="e.g., Jane Perera" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="modEmail">Email Address *</label>
                        <input type="email" id="modEmail" placeholder="e.g., jane@example.com" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="modPhone">Phone Number *</label>
                        <input type="text" id="modPhone" placeholder="e.g., 0771234567" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="modSecondaryPhone">Secondary Phone</label>
                        <input type="text" id="modSecondaryPhone" placeholder="Optional">
                    </div>
                    <div class="modal-form-group">
                        <label for="modLanguage">Language *</label>
                        <select id="modLanguage" required>
                            <option value="">Select language</option>
                            <option value="English">English</option>
                            <option value="Sinhala">Sinhala</option>
                            <option value="Tamil">Tamil</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label for="modDob">Date of Birth *</label>
                        <input type="date" id="modDob" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="modGender">Gender *</label>
                        <select id="modGender" required>
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="admin-modal-section">
                <div class="section-title">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Account Access</span>
                </div>

                <div class="modal-form-grid">
                    <div class="modal-form-group">
                        <label for="modPassword">Password *</label>
                        <input type="password" id="modPassword" placeholder="Minimum 6 characters" minlength="6" required>
                    </div>
                    <div class="modal-form-group modal-form-group-full">
                        <label for="modAddress">Address *</label>
                        <textarea id="modAddress" rows="4" placeholder="Enter moderator address" required></textarea>
                    </div>
                </div>
            </section>

            <div class="modal-actions">
                <button id="cancelAddModeratorModalBtn" class="btn-secondary" type="button">Cancel</button>
                <button class="btn-primary" type="submit">Add Moderator</button>
            </div>
        </form>
    </div>
</div>
