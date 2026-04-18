<?php
$adminName = $_SESSION['user_fullname'] ?? 'Administrator';
$adminEmail = $_SESSION['user_email'] ?? 'admin@example.com';
$adminRole = $_SESSION['user_account_type'] ?? 'Admin';
$adminPhoto = $_SESSION['user_profile_photo'] ?? '';
$memberSince = isset($_SESSION['user_login_time']) ? date('M d, Y', $_SESSION['user_login_time']) : date('M d, Y');
$avatarInitial = strtoupper(substr(trim($adminName), 0, 1));
?>

<div class="profile-container admin-profile-page">
    <div class="profile-header">
        <div class="profile-avatar-section">
            <div class="profile-avatar">
                <?php if (!empty($adminPhoto)) : ?>
                    <img src="<?php echo URL_ROOT . '/public/uploads' . $adminPhoto; ?>" alt="Admin Profile">
                <?php else : ?>
                    <span class="profile-avatar-initial"><?php echo $avatarInitial; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-info">
            <h1 id="adminName"><?php echo htmlspecialchars($adminName); ?></h1>
            <p class="driver-role">Platform administration account</p>
        </div>

        <div class="email-settings-header admin-header-panel">
            <div class="current-email-display">
                <label>Admin Email</label>
                <div class="email-display-box">
                    <i class="fas fa-envelope"></i>
                    <span id="currentEmailDisplay"><?php echo htmlspecialchars($adminEmail); ?></span>
                </div>
            </div>

            <button type="button" class="btn-change-email-header" id="openPasswordModalBtn">
                <i class="fas fa-lock"></i>
                Change Password
            </button>
        </div>
    </div>

    <div class="profile-nav">
        <button class="nav-tab active" type="button" data-tab="account">
            <i class="fas fa-user-shield"></i>
            Account
        </button>
        <button class="nav-tab" type="button" data-tab="security">
            <i class="fas fa-lock"></i>
            Security
        </button>
        <button class="nav-tab" type="button" data-tab="activity">
            <i class="fas fa-clock-rotate-left"></i>
            Activity
        </button>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="account-tab">
            <div class="profile-section">
                <div class="section-header">
                    <h2><i class="fas fa-id-badge"></i> Account Overview</h2>
                </div>
                <div class="section-content">
                    <div class="admin-detail-grid">
                        <div class="admin-detail-card">
                            <label>Full Name</label>
                            <div class="detail-value"><?php echo htmlspecialchars($adminName); ?></div>
                        </div>
                        <div class="admin-detail-card">
                            <label>Email Address</label>
                            <div class="detail-value"><?php echo htmlspecialchars($adminEmail); ?></div>
                        </div>
                        <div class="admin-detail-card">
                            <label>Role</label>
                            <div class="detail-value"><?php echo htmlspecialchars($adminRole); ?></div>
                        </div>
                        <div class="admin-detail-card">
                            <label>Member Since</label>
                            <div class="detail-value"><?php echo htmlspecialchars($memberSince); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="security-tab">
            <div class="profile-sections-row">
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-key"></i> Password</h2>
                    </div>
                    <div class="section-content">
                        <div class="admin-note-card">
                            <h3>Change account password</h3>
                            <p>Use a strong password to protect access to platform settings, moderator accounts, and financial tools.</p>
                            <button type="button" class="btn-save" id="openPasswordModalInlineBtn">Update Password</button>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-lock"></i> Access Protection</h2>
                    </div>
                    <div class="section-content">
                        <div class="admin-check-list">
                            <div class="admin-check-item">
                                <strong>Primary email</strong>
                                <span>Used for sign-in and admin alerts.</span>
                            </div>
                            <div class="admin-check-item">
                                <strong>Session review</strong>
                                <span>Track account access across admin devices.</span>
                            </div>
                            <div class="admin-check-item">
                                <strong>Security review</strong>
                                <span>Recommended before changing key system settings.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="activity-tab">
            <div class="profile-section">
                <div class="section-header">
                    <h2><i class="fas fa-list-check"></i> Recent Account Activity</h2>
                </div>
                <div class="section-content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-right-to-bracket"></i></div>
                            <div class="activity-copy">
                                <strong>Last login</strong>
                                <span>Latest admin access activity will appear here.</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-users-gear"></i></div>
                            <div class="activity-copy">
                                <strong>Moderator management</strong>
                                <span>Track moderator account changes and approvals.</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-file-shield"></i></div>
                            <div class="activity-copy">
                                <strong>Security events</strong>
                                <span>Password updates and future security actions can be listed here.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="passwordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Password</h3>
            <button type="button" class="modal-close" data-close-modal="passwordModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="passwordForm" class="profile-form">
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" placeholder="Re-enter new password">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" data-close-modal="passwordModal">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
