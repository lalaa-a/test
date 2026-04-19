<?php
$adminName = $_SESSION['user_fullname'] ?? 'Administrator';
$adminEmail = $_SESSION['user_email'] ?? 'admin@example.com';
$adminRole = $_SESSION['user_account_type'] ?? 'Admin';
$adminPhoto = $_SESSION['user_profile_photo'] ?? '';
$avatarInitial = strtoupper(substr(trim($adminName), 0, 1));
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-section">
            <div class="profile-avatar" id="profileAvatar">
                <?php if (!empty($adminPhoto)) : ?>
                    <img src="<?php echo URL_ROOT . '/public/uploads' . $adminPhoto; ?>" alt="Admin Profile">
                <?php else : ?>
                    <span class="profile-avatar-initial"><?php echo htmlspecialchars($avatarInitial); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-info">
            <h1 id="driverName"><?php echo htmlspecialchars($adminName); ?></h1>
            <p class="driver-role">Platform administration account</p>

            <div class="email-settings-header">
                <div class="current-email-display">
                    <label>Current Email</label>
                    <div class="email-display-box">
                        <i class="fas fa-envelope"></i>
                        <span id="currentEmailDisplay"><?php echo htmlspecialchars($adminEmail); ?></span>
                        <button class="btn-change-email-header" id="changeEmailBtn">
                            <i class="fas fa-edit"></i> Change
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-user"></i> Admin Information</h2>
            </div>
            <div class="section-content">
                <div id="personalInfoView" class="personal-info-display">
                    <div class="info-row">
                        <div class="info-item">
                            <label>Full Name</label>
                            <span id="displayFullName"><?php echo htmlspecialchars($adminName); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email Address</label>
                            <span id="displayEmail"><?php echo htmlspecialchars($adminEmail); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <label>Account Role</label>
                            <span id="displayRole"><?php echo htmlspecialchars($adminRole); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Account Status</label>
                            <span id="displayStatus">Active</span>
                        </div>
                    </div>
                </div>

                <form id="personalForm" class="profile-form" style="display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullName">Full Name *</label>
                            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($adminName); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($adminEmail); ?>" required>
                        </div>
                    </div>
                </form>

                <div class="section-actions">
                    <button class="btn-edit" id="editPersonalBtn">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-cancel" id="cancelPersonalBtn" style="display: none;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button class="btn-save" id="savePersonalBtn" style="display: none;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div id="emailChangeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Email Address</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="emailChangeForm">
                    <div class="form-group">
                        <label for="currentEmail">Current Email</label>
                        <input type="email" id="currentEmail" name="currentEmail" value="<?php echo htmlspecialchars($adminEmail); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="newEmail">New Email Address</label>
                        <input type="email" id="newEmail" name="newEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewEmail">Confirm New Email</label>
                        <input type="email" id="confirmNewEmail" name="confirmNewEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Current Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="modal-actions">
                        <button class="btn-cancel" type="button">Cancel</button>
                        <button class="btn-save" type="button" id="sendVerificationBtn">Send Verification Code</button>
                    </div>
                </form>

                <div id="emailOtpSection" class="otp-verification" style="display: none;">
                    <p>A verification code has been sent to <strong id="otpEmailTarget">-</strong></p>

                    <div class="form-group">
                        <label for="emailOTP">Verification Code</label>
                        <input type="text" id="emailOTP" name="emailOTP" maxlength="6" placeholder="Enter 6-digit code">
                    </div>

                    <div class="modal-actions">
                        <button class="btn-cancel" type="button" id="cancelOtpBtn">Cancel</button>
                        <button class="btn-cancel" type="button" id="resendOtpBtn">Resend Code</button>
                        <button class="btn-save" type="button" id="verifyOtpBtn">Verify Email</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
