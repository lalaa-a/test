

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-section">
                <div class="profile-avatar" id="profileAvatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="avatar-upload">
                    <button class="btn-upload" id="changePhotoBtn">
                        <i class="fas fa-camera"></i> Change Photo
                    </button>
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                </div>
            </div>
            <div class="profile-info">
                <h1 id="driverName">Guide Name</h1>
                <p class="driver-role">Professional Tour Guide</p>
                <div class="verification-status" id="verificationStatus">
                    <span class="status-badge status-unverified">
                        <i class="fas fa-shield-alt"></i> Unverified
                    </span>
                </div>
                <div class="driver-stats">
                    <div class="stat-item">
                        <span class="stat-number" id="totalTours">0</span>
                        <span class="stat-label">Tours</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="experienceYears">0</span>
                        <span class="stat-label">Years Exp.</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="rating">0.0</span>
                        <span class="stat-label">Rating</span>
                    </div>
                </div>
                <div class="email-settings-header">
                    <div class="current-email-display">
                        <label>Current Email</label>
                        <div class="email-display-box">
                            <i class="fas fa-envelope"></i>
                            <span id="currentEmailDisplay">-</span>
                            <button class="btn-change-email-header" id="changeEmailBtn">
                                <i class="fas fa-edit"></i> Change
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Cover Photos Gallery Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h2><i class="fas fa-images"></i> Cover Photos Gallery</h2>
                    <button class="btn-edit" id="editCoverPhotosBtn">
                        <i class="fas fa-edit"></i> Manage Photos
                    </button>
                </div>
                <div class="section-content">
                    <!-- Photo Gallery View Mode -->
                    <div id="coverGalleryView" class="cover-gallery-view">
                        <div class="gallery-grid">
                            <!-- Main Photo (Left) -->
                            <div class="gallery-main-photo" id="mainPhotoContainer">
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>No photos uploaded</p>
                                </div>
                            </div>
                            
                            <!-- Small Photos Grid (Right) -->
                            <div class="gallery-small-photos">
                                <div class="small-photo" id="smallPhoto1Container">
                                    <div class="photo-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                </div>
                                <div class="small-photo" id="smallPhoto2Container">
                                    <div class="photo-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                </div>
                                <div class="small-photo" id="smallPhoto3Container">
                                    <div class="photo-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                </div>
                                <div class="small-photo small-photo-overlay" id="smallPhoto4Container">
                                    <div class="photo-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <button class="view-all-photos-btn" id="viewAllPhotosBtn" style="display: none;">
                                        <i class="fas fa-th"></i>
                                        <span id="photoCountText">View all photos</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div id="coverGalleryEdit" class="cover-gallery-edit" style="display: none;">
                        <div class="upload-instructions">
                            <p><i class="fas fa-info-circle"></i> Upload up to 10 photos to showcase your services. The first photo will be featured as the main image.</p>
                        </div>
                        
                        <div class="photo-upload-grid">
                            <!-- Photo Upload Slots -->
                            <div class="photo-upload-slot" data-slot="1">
                                <div class="upload-preview" id="uploadPreview1">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Main Photo</p>
                                        <span class="upload-hint">Recommended: 1200x800px</span>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload1" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload1">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="1" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="2">
                                <div class="upload-preview" id="uploadPreview2">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 2</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload2" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload2">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="2" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="3">
                                <div class="upload-preview" id="uploadPreview3">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 3</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload3" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload3">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="3" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="4">
                                <div class="upload-preview" id="uploadPreview4">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 4</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload4" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload4">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="4" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="5">
                                <div class="upload-preview" id="uploadPreview5">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 5</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload5" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload5">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="5" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="6">
                                <div class="upload-preview" id="uploadPreview6">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 6</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload6" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload6">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="6" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="7">
                                <div class="upload-preview" id="uploadPreview7">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 7</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload7" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload7">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="7" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="8">
                                <div class="upload-preview" id="uploadPreview8">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 8</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload8" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload8">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="8" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="9">
                                <div class="upload-preview" id="uploadPreview9">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 9</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload9" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload9">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="9" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="photo-upload-slot" data-slot="10">
                                <div class="upload-preview" id="uploadPreview10">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Photo 10</p>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload10" accept="image/*" style="display: none;">
                                <button class="btn-upload-photo" data-target="photoUpload10">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button class="btn-remove-photo" data-slot="10" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="section-actions">
                            <button class="btn-cancel" id="cancelCoverPhotosBtn">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button class="btn-save" id="saveCoverPhotosBtn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                </div>
                <div class="section-content">
                    <!-- View mode -->
                    <div id="personalInfoView" class="personal-info-display">
                        <div class="info-row">
                            <div class="info-item">
                                <label>Full Name</label>
                                <span id="displayFullName">-</span>
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <span id="displayPhone">-</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <label>Secondary Phone</label>
                                <span id="displaySecondaryPhone">-</span>
                            </div>
                            <div class="info-item">
                                <label>Date of Birth</label>
                                <span id="displayDateOfBirth">-</span>
                            </div>
                        </div>
                        <div class="info-full-row">
                            <label>Bio & Experience</label>
                            <span id="displayBio">-</span>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <label>Instagram</label>
                                <span id="displayInstagram">-</span>
                            </div>
                            <div class="info-item">
                                <label>Facebook</label>
                                <span id="displayFacebook">-</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <label>Address</label>
                                <span id="displayAddress">-</span>
                            </div>
                            <div class="info-item">
                                <label>Languages</label>
                                <span id="displayLanguages">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit mode -->
                    <form id="personalForm" class="profile-form" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name *</label>
                                <input type="text" id="fullName" name="fullName" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="secondaryPhone">Secondary Phone</label>
                                <input type="tel" id="secondaryPhone" name="secondaryPhone">
                            </div>
                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth *</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="bio">Bio & Experience</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about your guiding experience, specialties, and what makes you a great tour guide..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="instagram">Instagram Username</label>
                                <input type="text" id="instagram" name="instagram" placeholder="yourhandle (no @)">
                            </div>
                            <div class="form-group">
                                <label for="facebook">Facebook Username</label>
                                <input type="text" id="facebook" name="facebook" placeholder="yourprofile (no @)">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="address">Address *</label>
                                <textarea id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="languages">Languages</label>
                                <div class="chip-input-container" id="languagesChipContainer">
                                    <div class="chips-display" id="languagesChips"></div>
                                    <input type="text" id="languagesInput" class="chip-input" placeholder="Type a language and press Enter">
                                </div>
                                <input type="hidden" id="languages" name="languages">
                                <small class="form-hint">Type a language and press Enter or comma to add</small>
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

            <!-- Tourist Guide License Section -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-certificate"></i> Tourist Guide License</h2>
                        <div class="header-actions">
                            <div class="license-status" id="touristLicenseStatus">
                                <span class="status-badge status-pending">Not Submitted</span>
                            </div>
                        </div>
                    </div>
                <div class="section-content">
                    <!-- Verified view -->
                    <div id="touristLicenseVerified" class="license-verified" style="display: none;">
                        <div class="verified-license">
                            <div class="verified-header">
                                <i class="fas fa-check-circle verified-icon"></i>
                                <h3>License Verified</h3>
                            </div>
                            <!-- Verified photos row -->
                            <div class="verified-photos">
                                <div class="verified-photo-item">
                                    <label class="photo-label">Front of Tourist License</label>
                                    <div class="verified-photo">
                                        <img id="verifiedTouristLicenseFront" src="" alt="Verified Tourist License Front" style="display: none;">
                                        <div class="license-placeholder">
                                            <i class="fas fa-certificate"></i>
                                            <p>License verified by moderator</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="verified-photo-item">
                                    <label class="photo-label">Back of Tourist License</label>
                                    <div class="verified-photo">
                                        <img id="verifiedTouristLicenseBack" src="" alt="Verified Tourist License Back" style="display: none;">
                                        <div class="license-placeholder">
                                            <i class="fas fa-certificate"></i>
                                            <p>License verified by moderator</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Verified details -->
                            <div class="verified-details-row">
                                <div class="verified-info">
                                    <p><strong>License Number:</strong> <span id="verifiedTouristLicenseNumber">-</span></p>
                                    <p><strong>Valid Until:</strong> <span id="verifiedTouristLicenseExpiry">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Under Review view -->
                    <div id="touristLicenseReview" class="license-pending" style="display: none;">
                        <div class="pending-license">
                            <div class="pending-header">
                                <i class="fas fa-clock pending-icon"></i>
                                <h3>License Under Review</h3>
                            </div>
                            <div class="pending-details">
                                <p>Your tourist guide license has been submitted and is currently under review by our moderators. The verification process typically takes 2-3 minutes.</p>
                                <div class="pending-info">
                                    <p><strong>Submitted:</strong> <span id="touristLicenseSubmittedDate">-</span></p>
                                    <p><strong>Status:</strong> <span class="status-badge status-pending">Under Review</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit view -->
                    <div id="touristLicenseEdit" class="license-info" style="display: none;">
                        <div class="license-photos">
                            <div class="license-photo-item">
                                <label class="photo-label">Front of Tourist License</label>
                                <div class="photo-placeholder" id="touristLicenseFrontContainer">
                                    <i class="fas fa-certificate"></i>
                                    <p>No front photo uploaded</p>
                                </div>
                                <button class="btn-upload-photo" id="uploadTouristLicenseFrontBtn">
                                    <i class="fas fa-camera"></i> Upload Front
                                </button>
                                <input type="file" id="touristLicenseFrontInput" accept="image/*" style="display: none;">
                            </div>
                            <div class="license-photo-item">
                                <label class="photo-label">Back of Tourist License</label>
                                <div class="photo-placeholder" id="touristLicenseBackContainer">
                                    <i class="fas fa-certificate"></i>
                                    <p>No back photo uploaded</p>
                                </div>
                                <button class="btn-upload-photo" id="uploadTouristLicenseBackBtn">
                                    <i class="fas fa-camera"></i> Upload Back
                                </button>
                                <input type="file" id="touristLicenseBackInput" accept="image/*" style="display: none;">
                            </div>
                        </div>
                        <div class="license-details">
                            <form id="touristLicenseForm" class="profile-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="touristLicenseNumber">License Number *</label>
                                        <input type="text" id="touristLicenseNumber" name="touristLicenseNumber" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="touristLicenseExpiry">Expiry Date *</label>
                                        <input type="date" id="touristLicenseExpiry" name="touristLicenseExpiry" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="section-actions">
                        <button class="btn-edit" id="editTouristLicenseBtn" style="display: none;">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        
                        <button class="btn-cancel" id="cancelTouristLicenseBtn" style="display: none;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn-save" id="updateTouristLicenseBtn" style="display: none;">
                            <i class="fas fa-save"></i> Update License
                        </button>

                        <button class="btn-save" id="saveTouristLicenseBtn" style="display: none;">
                            <i class="fas fa-save"></i> Submit License
                        </button>
                    </div>
                </div>
            </div>
            </div>
    </div>

    <!-- Email Change Modal -->
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
                        <input type="email" id="currentEmail" name="currentEmail" readonly>
                    </div>
                    <div class="form-group">
                        <label for="newEmail">New Email Address</label>
                        <input type="email" id="newEmail" name="newEmail" required>
                        <div class="form-hint">A verification code will be sent to this email address.</div>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewEmail">Confirm New Email</label>
                        <input type="email" id="confirmNewEmail" name="confirmNewEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Current Password</label>
                        <input type="password" id="password" name="password" required>
                        <div class="form-hint">Enter your current password to confirm this change.</div>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel">Cancel</button>
                <button type="button" class="btn-save" id="sendVerificationBtn">Send Verification Code</button>
            </div>
        </div>
    </div>


