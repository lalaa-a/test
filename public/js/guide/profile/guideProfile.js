(function() {

    if (window.GuideProfileManager) {
        console.log('GuideProfileManager already exists, cleaning up...');
        if (window.guideProfileManager) {
            delete window.guideProfileManager;
        }
        delete window.GuideProfileManager;
    }

// Guide Profile JavaScript
class GuideProfileManager {
    constructor() {
        this.URL_ROOT = 'http://localhost/test';
        this.UP_ROOT = 'http://localhost/test/public/uploads';
        this.currentSection = null;
        this.currentData = null; // Store current profile data
        this.init();
    }

    init() {
        this.initializeElements();
        this.attachEventListeners();
        this.initializeFormValidation();
        this.initializeFileUploads();
        this.loadProfileData();
    }

    initializeElements() {
        // Profile Header Elements
        this.profileAvatar = document.getElementById('profileAvatar');
        this.changePhotoBtn = document.getElementById('changePhotoBtn');
        this.photoInput = document.getElementById('photoInput');
        this.driverName = document.getElementById('driverName');
        this.verificationStatus = document.getElementById('verificationStatus');
        this.currentEmailDisplay = document.getElementById('currentEmailDisplay');
        this.changeEmailBtn = document.getElementById('changeEmailBtn');

        // Cover Photos Gallery Elements
        this.editCoverPhotosBtn = document.getElementById('editCoverPhotosBtn');
        this.cancelCoverPhotosBtn = document.getElementById('cancelCoverPhotosBtn');
        this.saveCoverPhotosBtn = document.getElementById('saveCoverPhotosBtn');
        this.coverGalleryView = document.getElementById('coverGalleryView');
        this.coverGalleryEdit = document.getElementById('coverGalleryEdit');
        this.viewAllPhotosBtn = document.getElementById('viewAllPhotosBtn');
        this.photoCountText = document.getElementById('photoCountText');
        
        // Gallery containers
        this.mainPhotoContainer = document.getElementById('mainPhotoContainer');
        this.smallPhoto1Container = document.getElementById('smallPhoto1Container');
        this.smallPhoto2Container = document.getElementById('smallPhoto2Container');
        this.smallPhoto3Container = document.getElementById('smallPhoto3Container');
        this.smallPhoto4Container = document.getElementById('smallPhoto4Container');
        
        // Store uploaded photos
        this.uploadedPhotos = [];
        this.deletedPhotos = []; // Track photos to be deleted

        // Personal Info Elements
        this.personalInfoView = document.getElementById('personalInfoView');
        this.personalForm = document.getElementById('personalForm');
        this.editPersonalBtn = document.getElementById('editPersonalBtn');
        this.cancelPersonalBtn = document.getElementById('cancelPersonalBtn');
        this.savePersonalBtn = document.getElementById('savePersonalBtn');

        // Personal Info Display Elements
        this.displayFullName = document.getElementById('displayFullName');
        this.displayPhone = document.getElementById('displayPhone');
        this.displaySecondaryPhone = document.getElementById('displaySecondaryPhone');
        this.displayDateOfBirth = document.getElementById('displayDateOfBirth');
        this.displayAddress = document.getElementById('displayAddress');
        this.displayBio = document.getElementById('displayBio');
        this.displayInstagram = document.getElementById('displayInstagram');
        this.displayFacebook = document.getElementById('displayFacebook');
        this.displayLanguages = document.getElementById('displayLanguages');
        this.languagesChipContainer = document.getElementById('languagesChipContainer');
        this.languagesChipsDisplay = document.getElementById('languagesChips');
        this.languagesInput = document.getElementById('languagesInput');
        this.languagesHiddenInput = document.getElementById('languages');
        
        // Store language chips
        this.languageChips = [];

        // Tourist License Elements
        this.touristLicenseVerified = document.getElementById('touristLicenseVerified');
        this.touristLicenseReview = document.getElementById('touristLicenseReview');
        this.touristLicenseEdit = document.getElementById('touristLicenseEdit');
        this.editTouristLicenseBtn = document.getElementById('editTouristLicenseBtn');
        this.cancelTouristLicenseBtn = document.getElementById('cancelTouristLicenseBtn');
        this.saveTouristLicenseBtn = document.getElementById('saveTouristLicenseBtn');
        this.updateTouristLicenseBtn = document.getElementById('updateTouristLicenseBtn');
        this.touristLicenseStatus = document.getElementById('touristLicenseStatus');
        this.verifiedTouristLicenseNumber = document.getElementById('verifiedTouristLicenseNumber');
        this.verifiedTouristLicenseExpiry = document.getElementById('verifiedTouristLicenseExpiry');

        // Email Modal Elements
        this.emailChangeModal = document.getElementById('emailChangeModal');
        this.emailChangeForm = document.getElementById('emailChangeForm');
        this.sendVerificationBtn = document.getElementById('sendVerificationBtn');
    }

    attachEventListeners() {
        // Edit buttons - using event delegation
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const section = e.target.closest('.profile-section');
                this.toggleEditMode(section);
            });
        });

        // Cancel buttons
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                this.cancelEdit();
            });
        });

        // Save buttons
        document.querySelectorAll('.btn-save').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.saveProfile(e.target.closest('.profile-section'));
            });
        });

        // Form submit handlers (prevent page refresh)
        document.querySelectorAll('.profile-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveProfile(e.target.closest('.profile-section'));
            });
        });

        // Email change button
        if (this.changeEmailBtn) {
            this.changeEmailBtn.addEventListener('click', () => {
                this.showEmailChangeModal();
            });
        }

        // Modal close
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => {
                this.closeModal();
            });
        });

        // Click outside modal
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal();
                }
            });
        });

        // Photo gallery upload buttons (handle these first)
        document.querySelectorAll('.photo-upload-slot .btn-upload-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const targetId = btn.getAttribute('data-target');
                if (targetId) {
                    const input = document.getElementById(targetId);
                    if (input) input.click();
                }
            });
        });

        // Tourist license upload buttons
        const uploadTouristFrontBtn = document.getElementById('uploadTouristLicenseFrontBtn');
        const uploadTouristBackBtn = document.getElementById('uploadTouristLicenseBackBtn');
        const touristFrontInput = document.getElementById('touristLicenseFrontInput');
        const touristBackInput = document.getElementById('touristLicenseBackInput');
        
        if (uploadTouristFrontBtn && touristFrontInput) {
            uploadTouristFrontBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                touristFrontInput.click();
            });
        }
        
        if (uploadTouristBackBtn && touristBackInput) {
            uploadTouristBackBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                touristBackInput.click();
            });
        }

        // Photo remove buttons
        document.querySelectorAll('.btn-remove-photo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const slot = btn.getAttribute('data-slot');
                this.removePhoto(slot);
            });
        });

        // View all photos button
        if (this.viewAllPhotosBtn) {
            this.viewAllPhotosBtn.addEventListener('click', () => {
                this.showAllPhotosModal();
            });
        }

        // Photo change button
        if (this.changePhotoBtn) {
            this.changePhotoBtn.addEventListener('click', () => {
                if (this.photoInput) {
                    this.photoInput.click();
                }
            });
        }

        // Send verification button (email change)
        if (this.sendVerificationBtn) {
            this.sendVerificationBtn.addEventListener('click', () => {
                this.sendEmailVerification();
            });
        }

        // Language chip input
        if (this.languagesInput) {
            this.languagesInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    this.addLanguageChip();
                }
            });
            
            this.languagesInput.addEventListener('blur', () => {
                // Add chip on blur if there's text
                if (this.languagesInput.value.trim()) {
                    this.addLanguageChip();
                }
            });
        }

        // Edit cover photos button
        if (this.editCoverPhotosBtn) {
            this.editCoverPhotosBtn.addEventListener('click', () => {
                this.toggleCoverPhotosEdit();
            });
        }

        // Cancel cover photos button
        if (this.cancelCoverPhotosBtn) {
            this.cancelCoverPhotosBtn.addEventListener('click', () => {
                this.toggleCoverPhotosEdit();
            });
        }

        // Save cover photos button
        if (this.saveCoverPhotosBtn) {
            this.saveCoverPhotosBtn.addEventListener('click', () => {
                this.saveCoverPhotos();
            });
        }
    }

    initializeFormValidation() {
        // Real-time validation
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', () => {
                this.validateField(field);
            });

            field.addEventListener('input', () => {
                if (field.classList.contains('invalid')) {
                    this.validateField(field);
                }
            });
        });
    }

    initializeFileUploads() {
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e.target);
            });
        });
    }

    async loadProfileData() {
        try {
            console.log('Loading profile data...');
            
            const response = await fetch(`${this.URL_ROOT}/Guide/retrieveBasicGuideInfo`);
            const data = await response.json();

            if (data.success && data.driverInfo) {
                const driverInfo = data.driverInfo;
                
                // Store current data for state management
                this.currentData = driverInfo;
                
                this.populateFormData(driverInfo);
                this.populateDisplayData(driverInfo);
                this.initializeViewStates();
                
                // Load cover photos
                await this.loadCoverPhotos();
            } else {
                console.error('Failed to load profile data:', data.message);
                window.showNotification('Failed to load profile data. Please refresh the page.', 'error');
            }
        } catch (error) {
            console.error('Error loading profile data:', error);
            window.showNotification('An error occurred while loading profile data.', 'error');
        }
    }

    async loadCoverPhotos() {
        try {
            const response = await fetch(`${this.URL_ROOT}/ProfileController/getCoverPhotos`);
            const data = await response.json();
            
            if (data.success && data.photos) {
                // Convert database photos to uploadedPhotos array
                this.uploadedPhotos = [];
                this.deletedPhotos = []; // Reset deleted photos
                
                data.photos.forEach(photo => {
                    const photoUrl = `${this.UP_ROOT}/${photo.photo_path}`;
                    this.uploadedPhotos[photo.photo_order] = {
                        url: photoUrl,
                        id: photo.id,
                        path: photo.photo_path,
                        fromDatabase: true
                    };
                });
                
                // Update gallery view
                this.updateGalleryView();
                
                // Update edit mode thumbnails
                this.populateEditModeThumbnails();
            }
        } catch (error) {
            console.error('Error loading cover photos:', error);
        }
    }
    
    populateEditModeThumbnails() {
        // Populate edit mode with existing photos
        for (let i = 0; i < 10; i++) {
            const slot = i + 1;
            const photo = this.uploadedPhotos[i];
            const preview = document.getElementById(`uploadPreview${slot}`);
            const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
            
            if (photo && preview) {
                // Show existing photo
                const photoUrl = typeof photo === 'string' ? photo : photo.url;
                preview.innerHTML = `<img src="${photoUrl}" alt="Photo ${slot}">`;
                if (removeBtn) removeBtn.style.display = 'flex';
            } else if (preview) {
                // Show placeholder
                const isMain = slot === 1;
                preview.innerHTML = `
                    <div class="upload-placeholder">
                        <i class="fas fa-${isMain ? 'cloud-upload-alt' : 'image'}"></i>
                        <p>${isMain ? 'Main Photo' : 'Photo ' + slot}</p>
                        ${isMain ? '<span class="upload-hint">Recommended: 1200x800px</span>' : ''}
                    </div>
                `;
                if (removeBtn) removeBtn.style.display = 'none';
            }
        }
    }

    populateDisplayData(data) {
        // Populate personal info display elements
        if (this.displayFullName) this.displayFullName.textContent = data.fullName || '-';
        if (this.displayPhone) this.displayPhone.textContent = data.phone || '-';
        if (this.displaySecondaryPhone) this.displaySecondaryPhone.textContent = data.secondaryPhone || '-';
        if (this.displayDateOfBirth) this.displayDateOfBirth.textContent = data.dateOfBirth ? new Date(data.dateOfBirth).toLocaleDateString() : '-';
        if (this.displayAddress) this.displayAddress.textContent = data.address || '-';
        if (this.displayBio) this.displayBio.textContent = data.bio || '-';
        
        // Social media links
        if (this.displayInstagram) {
            const ig = data.instagram ? String(data.instagram).replace(/^@/, '') : '';
            this.displayInstagram.innerHTML = ig ? `<a href="https://instagram.com/${encodeURIComponent(ig)}" target="_blank" rel="noopener noreferrer">@${ig}</a>` : '-';
        }
        if (this.displayFacebook) {
            const fb = data.facebook ? String(data.facebook).replace(/^@/, '') : '';
            this.displayFacebook.innerHTML = fb ? `<a href="https://facebook.com/${encodeURIComponent(fb)}" target="_blank" rel="noopener noreferrer">@${fb}</a>` : '-';
        }

        // Languages
        if (this.displayLanguages) {
            const languages = data.languages ? String(data.languages) : '';
            if (languages && languages !== '-') {
                // Split languages and create tags
                const languageArray = languages.split(',').map(lang => lang.trim()).filter(lang => lang.length > 0);
                if (languageArray.length > 0) {
                    this.displayLanguages.innerHTML = languageArray.map(lang => 
                        `<span class="language-tag"><i class="fas fa-language"></i> ${lang}</span>`
                    ).join('');
                } else {
                    this.displayLanguages.innerHTML = '<span class="no-data">-</span>';
                }
            } else {
                this.displayLanguages.innerHTML = '<span class="no-data">-</span>';
            }
        }

        // Update current email display
        if (this.currentEmailDisplay && data.email) {
            this.currentEmailDisplay.textContent = data.email;
        }

        // Update profile header
        if (this.driverName && data.fullName) {
            this.driverName.textContent = data.fullName;
        }

        // Update profile avatar
        if (this.profileAvatar && data.profilePhoto) {
            // Remove existing avatar and reload the new one
            this.profileAvatar.innerHTML = '';
            const img = document.createElement('img');
            img.src = `${this.UP_ROOT}/${data.profilePhoto}`;
            img.alt = 'Profile Photo';
            this.profileAvatar.appendChild(img);
        }

        // Update experience years
        if (data.userCreatedAt) {
            const createdDate = new Date(data.userCreatedAt);
            const currentDate = new Date();
            const yearsDiff = currentDate.getFullYear() - createdDate.getFullYear();
            const experienceYearsEl = document.getElementById('experienceYears');
            if (experienceYearsEl) {
                experienceYearsEl.textContent = yearsDiff > 0 ? yearsDiff : 1; // Minimum 1 year
            }
        }

        // Update rating
        if (data.averageRating !== undefined && data.averageRating !== null) {
            const ratingEl = document.getElementById('rating');
            if (ratingEl) {
                const rating = parseFloat(data.averageRating);
                ratingEl.textContent = rating.toFixed(1);
            }
        }

        // Update verification status
        if (this.verificationStatus) {
            const isTouristLicenseVerified = data.tLicenseStatus === true || data.tLicenseStatus === '1' || data.tLicenseStatus === 1;

            if (isTouristLicenseVerified) {
                this.verificationStatus.innerHTML = '<span class="status-badge status-verified"><i class="fas fa-shield-alt"></i> Verified Guide</span>';
            } else {
                this.verificationStatus.innerHTML = '<span class="status-badge status-unverified"><i class="fas fa-exclamation-triangle"></i> Unverified</span>';
            }
        }

        // Update tourist license photos
        if (data.touristLicenseFrontPhoto) {
            const frontImg = document.getElementById('verifiedTouristLicenseFront');
            if (frontImg) {
                frontImg.src = `${this.UP_ROOT}/${data.touristLicenseFrontPhoto}`;
                frontImg.style.display = 'block';
            }
        }
        if (data.touristLicenseBackPhoto) {
            const backImg = document.getElementById('verifiedTouristLicenseBack');
            if (backImg) {
                backImg.src = `${this.UP_ROOT}/${data.touristLicenseBackPhoto}`;
                backImg.style.display = 'block';
            }
        }

        // Update tourist license status badge
        if (this.touristLicenseStatus) {
            const isTouristLicenseSubmitted = data.tLicenseSubmitted === true || data.tLicenseSubmitted === '1' || data.tLicenseSubmitted === 1;
            const isTouristLicenseVerified = data.tLicenseStatus === true || data.tLicenseStatus === '1' || data.tLicenseStatus === 1;

            if (isTouristLicenseVerified) {
                this.touristLicenseStatus.innerHTML = '<span class="status-badge status-valid">Valid</span>';
            } else if (isTouristLicenseSubmitted) {
                this.touristLicenseStatus.innerHTML = '<span class="status-badge status-pending">Under Review</span>';
            } else {
                this.touristLicenseStatus.innerHTML = '<span class="status-badge status-pending">Not Submitted</span>';
            }
        }

        // Update verified tourist license info
        if (this.verifiedTouristLicenseNumber) {
            this.verifiedTouristLicenseNumber.textContent = data.touristLicenseNumber || '-';
        }
        if (this.verifiedTouristLicenseExpiry) {
            this.verifiedTouristLicenseExpiry.textContent = data.touristLicenseExpiry ? new Date(data.touristLicenseExpiry).toLocaleDateString() : '-';
        }
    }

    initializeViewStates() {
        // Ensure personal information shows view mode initially
        if (this.personalInfoView && this.personalForm && this.editPersonalBtn) {
            this.personalInfoView.style.display = 'block';
            this.personalForm.style.display = 'none';
            this.editPersonalBtn.style.display = 'inline-flex';
            if (this.cancelPersonalBtn) this.cancelPersonalBtn.style.display = 'none';
            if (this.savePersonalBtn) this.savePersonalBtn.style.display = 'none';
        }

        // Tourist license state depends on submission status
        this.initializeTouristLicenseState();

        // Update tourist license submitted date
        const submittedDate = document.getElementById('touristLicenseSubmittedDate');
        if (submittedDate) {
            submittedDate.textContent = new Date().toLocaleDateString();
        }
    }

    initializeTouristLicenseState() {
        if (!this.touristLicenseVerified || !this.touristLicenseReview || !this.touristLicenseEdit || !this.editTouristLicenseBtn) return;

        // Get current data to check submission status
        const isTouristLicenseSubmitted = this.currentData?.tLicenseSubmitted === true ||
                                         this.currentData?.tLicenseSubmitted === '1' ||
                                         this.currentData?.tLicenseSubmitted === 1;
        const isTouristLicenseVerified = this.currentData?.tLicenseStatus === true ||
                                        this.currentData?.tLicenseStatus === '1' ||
                                        this.currentData?.tLicenseStatus === 1;

        if (isTouristLicenseVerified) {
            // Show verified state
            this.touristLicenseVerified.style.display = 'block';
            this.touristLicenseReview.style.display = 'none';
            this.touristLicenseEdit.style.display = 'none';
            this.editTouristLicenseBtn.style.display = 'inline-flex';
            if (this.cancelTouristLicenseBtn) this.cancelTouristLicenseBtn.style.display = 'none';
            if (this.saveTouristLicenseBtn) this.saveTouristLicenseBtn.style.display = 'none';
            if (this.updateTouristLicenseBtn) this.updateTouristLicenseBtn.style.display = 'none';
        } else if (isTouristLicenseSubmitted) {
            // Show under review state
            this.touristLicenseVerified.style.display = 'none';
            this.touristLicenseReview.style.display = 'block';
            this.touristLicenseEdit.style.display = 'none';
            this.editTouristLicenseBtn.style.display = 'none';
            if (this.cancelTouristLicenseBtn) this.cancelTouristLicenseBtn.style.display = 'none';
            if (this.saveTouristLicenseBtn) this.saveTouristLicenseBtn.style.display = 'none';
            if (this.updateTouristLicenseBtn) this.updateTouristLicenseBtn.style.display = 'none';
        } else {
            // Not submitted - show submit form
            this.touristLicenseVerified.style.display = 'none';
            this.touristLicenseReview.style.display = 'none';
            this.touristLicenseEdit.style.display = 'block';
            this.editTouristLicenseBtn.style.display = 'none';
            if (this.cancelTouristLicenseBtn) this.cancelTouristLicenseBtn.style.display = 'none';
            if (this.saveTouristLicenseBtn) this.saveTouristLicenseBtn.style.display = 'inline-flex';
            if (this.updateTouristLicenseBtn) this.updateTouristLicenseBtn.style.display = 'none';
        }
    }



    populateFormData(data) {
        Object.keys(data).forEach(key => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = data[key];
                } else {
                    field.value = data[key];
                }
            }
        });
    }

    toggleEditMode(section) {
        const sectionId = section.querySelector('h2').textContent.trim();

        if (sectionId === 'Personal Information') {
            this.togglePersonalInfo();
        } else if (sectionId === 'Tourist Guide License') {
            this.toggleLicenseEdit(sectionId);
        }
    }

    addLanguageChip() {
        if (!this.languagesInput) return;
        
        let value = this.languagesInput.value.trim().replace(/,+$/g, ''); // Remove trailing commas
        if (!value) return;
        
        // Check if already exists
        if (this.languageChips.includes(value)) {
            window.showNotification('Language already added', 'warning');
            this.languagesInput.value = '';
            return;
        }
        
        // Add to array
        this.languageChips.push(value);
        
        // Create chip element
        const chip = document.createElement('div');
        chip.className = 'language-chip';
        chip.innerHTML = `
            <span>${value}</span>
            <button type="button" class="chip-remove" data-language="${value}">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add remove listener
        chip.querySelector('.chip-remove').addEventListener('click', (e) => {
            const lang = e.currentTarget.getAttribute('data-language');
            this.removeLanguageChip(lang);
        });
        
        // Add to display
        this.languagesChipsDisplay.appendChild(chip);
        
        // Update hidden input
        this.updateLanguagesHiddenInput();
        
        // Clear input
        this.languagesInput.value = '';
    }
    
    removeLanguageChip(language) {
        // Remove from array
        this.languageChips = this.languageChips.filter(lang => lang !== language);
        
        // Remove from display
        const chips = this.languagesChipsDisplay.querySelectorAll('.language-chip');
        chips.forEach(chip => {
            const chipLang = chip.querySelector('.chip-remove').getAttribute('data-language');
            if (chipLang === language) {
                chip.remove();
            }
        });
        
        // Update hidden input
        this.updateLanguagesHiddenInput();
    }
    
    updateLanguagesHiddenInput() {
        if (this.languagesHiddenInput) {
            this.languagesHiddenInput.value = this.languageChips.join(',');
        }
    }
    
    populateLanguageChips(languagesString) {
        // Clear existing chips
        this.languageChips = [];
        if (this.languagesChipsDisplay) {
            this.languagesChipsDisplay.innerHTML = '';
        }
        
        if (!languagesString) return;
        
        // Parse comma-separated string
        const languages = languagesString.split(',').map(lang => lang.trim()).filter(lang => lang.length > 0);
        
        // Add each language as a chip
        languages.forEach(lang => {
            this.languageChips.push(lang);
            
            const chip = document.createElement('div');
            chip.className = 'language-chip';
            chip.innerHTML = `
                <span>${lang}</span>
                <button type="button" class="chip-remove" data-language="${lang}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            chip.querySelector('.chip-remove').addEventListener('click', (e) => {
                const language = e.currentTarget.getAttribute('data-language');
                this.removeLanguageChip(language);
            });
            
            this.languagesChipsDisplay.appendChild(chip);
        });
        
        // Update hidden input
        this.updateLanguagesHiddenInput();
    }

    togglePersonalInfo() {
        if (!this.editPersonalBtn || !this.cancelPersonalBtn || !this.savePersonalBtn) return;

        const isEditing = this.editPersonalBtn.style.display === 'none';

        if (isEditing) {
            // Switch to view mode
            this.personalInfoView.style.display = 'block';
            this.personalForm.style.display = 'none';
            this.editPersonalBtn.style.display = 'inline-flex';
            this.cancelPersonalBtn.style.display = 'none';
            this.savePersonalBtn.style.display = 'none';
            this.currentSection = null;
        } else {
            // Switch to edit mode
            this.personalInfoView.style.display = 'none';
            this.personalForm.style.display = 'block';
            this.editPersonalBtn.style.display = 'none';
            this.cancelPersonalBtn.style.display = 'inline-flex';
            this.savePersonalBtn.style.display = 'inline-flex';
            this.currentSection = this.editPersonalBtn.closest('.profile-section');
            
            // Populate language chips from current data
            if (this.currentData && this.currentData.languages) {
                this.populateLanguageChips(this.currentData.languages);
            }
        }
    }

    toggleLicenseEdit(sectionId) {
        
        const isDriverLicense = false; // Guides don't have driver license
        const prefix = isDriverLicense ? 'driver' : 'tourist';
        
        // Check if the other license section is currently in edit mode
        const otherPrefix = isDriverLicense ? 'tourist' : 'driver';
        const otherEditDiv = document.getElementById(`${otherPrefix}LicenseEdit`);
        const otherReviewDiv = document.getElementById(`${otherPrefix}LicenseReview`);
        const otherVerifiedDiv = document.getElementById(`${otherPrefix}LicenseVerified`) || document.getElementById(`${otherPrefix}LicenseView`);
        
        // If the other section is in edit mode, cancel it first
        if (otherEditDiv && otherEditDiv.style.display === 'block') {
            // Hide edit mode for the other section
            otherEditDiv.style.display = 'none';
            
            // Show the appropriate view mode for the other section
            if (otherReviewDiv && otherReviewDiv.style.display === 'block') {
                // Keep review mode
            } else if (otherVerifiedDiv) {
                otherVerifiedDiv.style.display = 'block';
            }
            
            // Hide other section's buttons
            const otherEditBtn = document.getElementById(`edit${isDriverLicense ? 'Tourist' : 'Driver'}LicenseBtn`);
            const otherCancelBtn = document.getElementById(`cancel${isDriverLicense ? 'Tourist' : 'Driver'}LicenseBtn`);
            const otherSaveBtn = document.getElementById(`update${isDriverLicense ? 'Tourist' : 'Driver'}LicenseBtn`);
            
            if (otherEditBtn) otherEditBtn.style.display = 'inline-flex';
            if (otherCancelBtn) otherCancelBtn.style.display = 'none';
            if (otherSaveBtn) otherSaveBtn.style.display = 'none';
            
            // Reset currentSection if it was the other section
            if (this.currentSection && this.currentSection !== otherEditBtn?.closest('.profile-section')) {
                // Keep currentSection as is
            } else {
                this.currentSection = null;
            }
        }
        
        // Correctly get the verified container (IDs differ between driver/tourist)
        const verifiedDiv = document.getElementById(`${prefix}LicenseView`) || document.getElementById(`${prefix}LicenseVerified`);
        const reviewDiv = document.getElementById(`${prefix}LicenseReview`);
        const editDiv = document.getElementById(`${prefix}LicenseEdit`);
        const editBtn = document.getElementById(`edit${isDriverLicense ? 'Driver' : 'Tourist'}LicenseBtn`);
        const cancelBtn = document.getElementById(`cancel${isDriverLicense ? 'Driver' : 'Tourist'}LicenseBtn`);
        const saveBtn = document.getElementById(`update${isDriverLicense ? 'Driver' : 'Tourist'}LicenseBtn`);
        // Header status element (only tourist for guides)
        const statusEl = document.getElementById('touristLicenseStatus');

        if (!editBtn) return;

        // Don't allow editing if under review
        if (reviewDiv && reviewDiv.style.display === 'block') {
            return;
        }

        const isEditing = editBtn.style.display === 'none';

        if (isEditing) {
            // Switch to view mode
            if (verifiedDiv) verifiedDiv.style.display = 'block';
            editDiv.style.display = 'none';
            editBtn.style.display = 'inline-flex';
            if (cancelBtn) cancelBtn.style.display = 'none';
            if (saveBtn) saveBtn.style.display = 'none';
            if (statusEl) statusEl.style.display = '';
            this.currentSection = null;
        } else {
            // Switch to edit mode
            if (verifiedDiv) verifiedDiv.style.display = 'none';
            editDiv.style.display = 'block';
            editBtn.style.display = 'none';
            if (cancelBtn) cancelBtn.style.display = 'inline-flex';
            if (saveBtn) saveBtn.style.display = 'inline-flex';
            if (statusEl) statusEl.style.display = 'none';
            this.currentSection = editBtn.closest('.profile-section');
            
            // Populate existing photos in the form
            this.populateExistingLicensePhotos(isDriverLicense);
            
            // Populate form fields with existing data
            this.populateLicenseFormData(isDriverLicense);
        }
    }

    populateLicenseFormData(isDriverLicense) {
        if (isDriverLicense) {
            // Guides don't have driver license
            return;
        }
        
        // Populate tourist license form fields
        const touristLicenseNumberField = document.getElementById('touristLicenseNumber');
        const touristLicenseExpiryField = document.getElementById('touristLicenseExpiry');
        
        if (touristLicenseNumberField && this.currentData && this.currentData.touristLicenseNumber) {
            touristLicenseNumberField.value = this.currentData.touristLicenseNumber;
        }
        
        if (touristLicenseExpiryField && this.currentData && this.currentData.touristLicenseExpiry) {
            touristLicenseExpiryField.value = this.currentData.touristLicenseExpiry;
        }
    }

    populateExistingLicensePhotos(isDriverLicense) {
        if (isDriverLicense) {
            // Guides don't have driver license
            return;
        } else {
            // Populate tourist license photos
            if (this.currentData && this.currentData.touristLicenseFrontPhoto) {
                const frontContainer = document.getElementById('touristLicenseFrontContainer');
                if (frontContainer) {
                    const img = document.createElement('img');
                    img.src = `${this.UP_ROOT}/${this.currentData.touristLicenseFrontPhoto}`;
                    img.alt = 'Current Front Tourist License';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    frontContainer.innerHTML = '';
                    frontContainer.appendChild(img);
                }
            }
            
            if (this.currentData && this.currentData.touristLicenseBackPhoto) {
                const backContainer = document.getElementById('touristLicenseBackContainer');
                if (backContainer) {
                    const img = document.createElement('img');
                    img.src = `${this.UP_ROOT}/${this.currentData.touristLicenseBackPhoto}`;
                    img.alt = 'Current Back Tourist License';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    backContainer.innerHTML = '';
                    backContainer.appendChild(img);
                }
            }
        }
    }

    toggleCoverPhotosEdit() {
        if (!this.coverGalleryView || !this.coverGalleryEdit) return;

        const isEditing = this.coverGalleryView.style.display === 'none';

        if (isEditing) {
            // Switch to view mode
            this.coverGalleryView.style.display = 'block';
            this.coverGalleryEdit.style.display = 'none';
            this.updateGalleryView();
        } else {
            // Switch to edit mode
            this.coverGalleryView.style.display = 'none';
            this.coverGalleryEdit.style.display = 'block';
        }
    }

    updateGalleryView() {
        // Helper to get photo URL
        const getPhotoUrl = (photo) => {
            if (!photo) return null;
            return typeof photo === 'string' ? photo : photo.url;
        };
        
        // Update the main gallery view with uploaded photos
        const containers = [
            this.mainPhotoContainer,
            this.smallPhoto1Container,
            this.smallPhoto2Container,
            this.smallPhoto3Container,
            this.smallPhoto4Container
        ];

        containers.forEach((container, index) => {
            if (!container) return;

            const photoUrl = getPhotoUrl(this.uploadedPhotos[index]);
            
            // Special handling for container 4 (has the view all button)
            if (index === 4) {
                // Find or create image/placeholder wrapper
                let contentWrapper = container.querySelector('.photo-content-wrapper');
                if (!contentWrapper) {
                    contentWrapper = document.createElement('div');
                    contentWrapper.className = 'photo-content-wrapper';
                    contentWrapper.style.cssText = 'width: 100%; height: 100%; position: absolute; top: 0; left: 0;';
                    container.insertBefore(contentWrapper, container.firstChild);
                }
                
                if (photoUrl) {
                    contentWrapper.innerHTML = `<img src="${photoUrl}" alt="Gallery photo ${index + 1}">`;
                } else {
                    contentWrapper.innerHTML = `
                        <div class="photo-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    `;
                }
            } else {
                // Normal handling for other containers
                if (photoUrl) {
                    container.innerHTML = `<img src="${photoUrl}" alt="Gallery photo ${index + 1}">`;
                } else {
                    if (index === 0) {
                        container.innerHTML = `
                            <div class="photo-placeholder">
                                <i class="fas fa-image"></i>
                                <p>No photos uploaded</p>
                            </div>
                        `;
                    } else {
                        container.innerHTML = `
                            <div class="photo-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        `;
                    }
                }
            }
        });

        // Update "View all photos" button
        if (this.viewAllPhotosBtn && this.photoCountText) {
            const totalPhotos = this.uploadedPhotos.filter(p => p).length;
            console.log('Updating view all button. Total photos:', totalPhotos);
            console.log('Button element:', this.viewAllPhotosBtn);
            
            if (totalPhotos > 0) {
                this.viewAllPhotosBtn.style.display = 'flex';
                if (totalPhotos > 4) {
                    this.photoCountText.textContent = `+${totalPhotos - 4} more`;
                } else {
                    this.photoCountText.textContent = `View all photos`;
                }
                console.log('Button shown with text:', this.photoCountText.textContent);
            } else {
                this.viewAllPhotosBtn.style.display = 'none';
                console.log('Button hidden - no photos');
            }
        } else {
            console.error('View all button or text element not found!');
            console.log('viewAllPhotosBtn:', this.viewAllPhotosBtn);
            console.log('photoCountText:', this.photoCountText);
        }
    }
    
    populateEditModeThumbnails() {
        // Populate edit mode with existing photos
        for (let i = 0; i < 10; i++) {
            const slot = i + 1;
            const photo = this.uploadedPhotos[i];
            const preview = document.getElementById(`uploadPreview${slot}`);
            const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
            
            if (photo && preview) {
                // Show existing photo
                const photoUrl = typeof photo === 'string' ? photo : photo.url;
                preview.innerHTML = `<img src="${photoUrl}" alt="Photo ${slot}">`;
                if (removeBtn) removeBtn.style.display = 'flex';
            } else if (preview) {
                // Show placeholder
                const isMain = slot === 1;
                preview.innerHTML = `
                    <div class="upload-placeholder">
                        <i class="fas fa-${isMain ? 'cloud-upload-alt' : 'image'}"></i>
                        <p>${isMain ? 'Main Photo' : 'Photo ' + slot}</p>
                        ${isMain ? '<span class="upload-hint">Recommended: 1200x800px</span>' : ''}
                    </div>
                `;
                if (removeBtn) removeBtn.style.display = 'none';
            }
        }
    }

    removePhoto(slot) {
        const slotIndex = parseInt(slot) - 1;
        const photo = this.uploadedPhotos[slotIndex];
        
        // If photo is from database, mark it for deletion
        if (photo && photo.fromDatabase && photo.id) {
            this.deletedPhotos.push(photo.id);
            console.log('Marked photo for deletion:', photo.id);
        }
        
        // Remove from uploaded photos array
        this.uploadedPhotos[slotIndex] = null;
        
        // Clear the preview
        const preview = document.getElementById(`uploadPreview${slot}`);
        const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slot}"]`);
        const fileInput = document.getElementById(`photoUpload${slot}`);
        
        if (preview) {
            const isMain = slot === '1';
            preview.innerHTML = `
                <div class="upload-placeholder">
                    <i class="fas fa-${isMain ? 'cloud-upload-alt' : 'image'}"></i>
                    <p>${isMain ? 'Main Photo' : 'Photo ' + slot}</p>
                    ${isMain ? '<span class="upload-hint">Recommended: 1200x800px</span>' : ''}
                </div>
            `;
        }
        
        if (removeBtn) {
            removeBtn.style.display = 'none';
        }
        
        // Clear file input
        if (fileInput) {
            fileInput.value = '';
        }
        
        window.showNotification('Photo will be removed when you save changes', 'info');
    }

    showAllPhotosModal() {
        // Get all photos with URLs
        const photos = this.uploadedPhotos.filter(p => p).map(p => {
            return typeof p === 'string' ? p : p.url;
        });
        
        if (photos.length === 0) {
            window.showNotification('No photos to display', 'info');
            return;
        }
        
        let currentIndex = 0;
        
        // Create modal to show photos one by one
        const modal = document.createElement('div');
        modal.className = 'photos-lightbox-modal';
        modal.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close">&times;</button>
                <button class="lightbox-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="lightbox-image-container">
                    <img src="${photos[0]}" alt="Photo 1" class="lightbox-image">
                    <div class="lightbox-counter">1 / ${photos.length}</div>
                </div>
                <button class="lightbox-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const imgElement = modal.querySelector('.lightbox-image');
        const counter = modal.querySelector('.lightbox-counter');
        const prevBtn = modal.querySelector('.lightbox-prev');
        const nextBtn = modal.querySelector('.lightbox-next');
        const closeBtn = modal.querySelector('.lightbox-close');
        
        // Update image
        const updateImage = () => {
            imgElement.src = photos[currentIndex];
            imgElement.alt = `Photo ${currentIndex + 1}`;
            counter.textContent = `${currentIndex + 1} / ${photos.length}`;
            
            // Disable buttons at boundaries
            prevBtn.style.opacity = currentIndex === 0 ? '0.3' : '1';
            prevBtn.style.cursor = currentIndex === 0 ? 'not-allowed' : 'pointer';
            nextBtn.style.opacity = currentIndex === photos.length - 1 ? '0.3' : '1';
            nextBtn.style.cursor = currentIndex === photos.length - 1 ? 'not-allowed' : 'pointer';
        };
        
        // Navigation handlers
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex > 0) {
                currentIndex--;
                updateImage();
            }
        });
        
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex < photos.length - 1) {
                currentIndex++;
                updateImage();
            }
        });
        
        // Keyboard navigation
        const handleKeyPress = (e) => {
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                updateImage();
            } else if (e.key === 'ArrowRight' && currentIndex < photos.length - 1) {
                currentIndex++;
                updateImage();
            } else if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', handleKeyPress);
            }
        };
        document.addEventListener('keydown', handleKeyPress);
        
        // Close modal handlers
        closeBtn.addEventListener('click', () => {
            modal.remove();
            document.removeEventListener('keydown', handleKeyPress);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                document.removeEventListener('keydown', handleKeyPress);
            }
        });
        
        updateImage();
        
        // Add lightbox styles dynamically
        if (!document.getElementById('lightboxModalStyles')) {
            const style = document.createElement('style');
            style.id = 'lightboxModalStyles';
            style.textContent = `
                .photos-lightbox-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.95);
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: fadeIn 0.3s ease;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                .lightbox-content {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 60px 100px;
                }
                
                .lightbox-close {
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: rgba(255, 255, 255, 0.9);
                    border: none;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    font-size: 2rem;
                    color: #333;
                    cursor: pointer;
                    z-index: 10001;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }
                
                .lightbox-close:hover {
                    background: white;
                    transform: scale(1.1);
                }
                
                .lightbox-image-container {
                    position: relative;
                    max-width: 90%;
                    max-height: 90%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .lightbox-image {
                    max-width: 100%;
                    max-height: 80vh;
                    object-fit: contain;
                    border-radius: 8px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
                }
                
                .lightbox-counter {
                    position: absolute;
                    bottom: -40px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(255, 255, 255, 0.9);
                    color: #333;
                    padding: 10px 20px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 0.9rem;
                }
                
                .lightbox-prev,
                .lightbox-next {
                    position: absolute;
                    background: rgba(255, 255, 255, 0.9);
                    border: none;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    font-size: 1.5rem;
                    color: #333;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10001;
                }
                
                .lightbox-prev:hover,
                .lightbox-next:hover {
                    background: white;
                    transform: scale(1.1);
                }
                
                .lightbox-prev {
                    left: 30px;
                }
                
                .lightbox-next {
                    right: 30px;
                }
                
                @media (max-width: 768px) {
                    .lightbox-content {
                        padding: 60px 20px;
                    }
                    
                    .lightbox-prev {
                        left: 10px;
                    }
                    
                    .lightbox-next {
                        right: 10px;
                    }
                    
                    .lightbox-prev,
                    .lightbox-next {
                        width: 40px;
                        height: 40px;
                        font-size: 1.2rem;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    async saveCoverPhotos() {
        try {
            // Show loading notification
            window.showNotification('Saving changes...', 'info');
            
            const formData = new FormData();
            let fileCount = 0;
            
            // Get all file inputs and append NEW files to FormData
            for (let i = 1; i <= 10; i++) {
                const fileInput = document.getElementById(`photoUpload${i}`);
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    formData.append(`photo${i}`, fileInput.files[0]);
                    fileCount++;
                    console.log(`Added photo ${i}:`, fileInput.files[0].name);
                }
            }
            
            // Add deleted photo IDs
            if (this.deletedPhotos.length > 0) {
                formData.append('deletedPhotos', JSON.stringify(this.deletedPhotos));
                console.log('Photos to delete:', this.deletedPhotos);
            }
            
            console.log('Total files to upload:', fileCount);
            console.log('Total photos to delete:', this.deletedPhotos.length);
            
            // Allow save if there are changes (uploads OR deletions)
            if (fileCount === 0 && this.deletedPhotos.length === 0) {
                window.showNotification('No changes to save', 'info');
                return;
            }
            
            const response = await fetch(`${this.URL_ROOT}/ProfileController/saveCoverPhotos`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                let message = [];
                if (data.uploaded > 0) message.push(`${data.uploaded} photo(s) uploaded`);
                if (data.deleted > 0) message.push(`${data.deleted} photo(s) deleted`);
                
                window.showNotification(message.join(', ') + '!', 'success');
                
                // Reload cover photos from server
                await this.loadCoverPhotos();
                
                // Update gallery view
                this.updateGalleryView();
                
                // Switch back to view mode
                this.toggleCoverPhotosEdit();
                
                // Clear file inputs and deleted photos array
                for (let i = 1; i <= 10; i++) {
                    const fileInput = document.getElementById(`photoUpload${i}`);
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }
                this.deletedPhotos = [];
            } else {
                window.showNotification(data.message || 'Failed to save changes', 'error');
            }
            
        } catch (error) {
            console.error('Error saving cover photos:', error);
            window.showNotification('An error occurred while saving changes', 'error');
        }
    }

    cancelEdit() {
        if (this.currentSection) {
            const sectionId = this.currentSection.querySelector('h2').textContent.trim();

            if (sectionId === 'Personal Information') {
                this.togglePersonalInfo();
                // Reset form data
                this.loadProfileData();
            } else if (sectionId === 'Tourist Guide License') {
                this.toggleLicenseEdit(sectionId);
                // Reset form data
                this.loadProfileData();
            }

            this.currentSection = null;
        }
    }

    async saveProfile(section) {
        if (!section) return;
        
        const sectionId = section.querySelector('h2')?.textContent.trim();
        
        // Handle personal information save
        if (sectionId === 'Personal Information') {
            const form = document.getElementById('personalForm');
            if (!form) return;

            // Validate all fields
            const fields = form.querySelectorAll('input, select, textarea');
            let isValid = true;

            fields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                window.showNotification('Please fix the errors before saving.', 'error');
                return;
            }

            // Collect form data
            const formData = new FormData(form);
            console.log('Saving personal info...', Object.fromEntries(formData));

            // Show loading state
            window.showNotification('Updating profile...', 'info');

            try {
                // Make API call to update personal info
                const response = await fetch(`${this.URL_ROOT}/Guide/editGuidePersonalInfo`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Reload profile data to get updated information from server
                    await this.loadProfileData();

                    // Explicitly switch to view mode after successful save
                    if (this.personalInfoView && this.personalForm && this.editPersonalBtn) {
                        this.personalInfoView.style.display = 'block';
                        this.personalForm.style.display = 'none';
                        this.editPersonalBtn.style.display = 'inline-flex';
                        if (this.cancelPersonalBtn) this.cancelPersonalBtn.style.display = 'none';
                        if (this.savePersonalBtn) this.savePersonalBtn.style.display = 'none';
                        this.currentSection = null;
                    }

                    // Show success message
                    window.showNotification('Profile updated successfully!', 'success');

                } else {
                    // Show error message from server
                    window.showNotification(result.message || 'Failed to update profile. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                window.showNotification('An error occurred while updating your profile. Please try again.', 'error');
            }

            return;
        }

        // Handle license saves
        const form = section.querySelector('.profile-form');
        if (!form) return;

        // Validate all fields in the section
        const fields = form.querySelectorAll('input, select, textarea');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            window.showNotification('Please fix the errors before saving.', 'error');
            return;
        }

        // Collect form data
        const formData = new FormData(form);

        // Check if this is the tourist license form
        if (form.id === 'touristLicenseForm') {
            this.handleTouristLicenseSubmission(formData);
            return;
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Remove existing error
        this.clearFieldError(field);

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required.';
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
        }

        // Phone validation
        if (field.name === 'phone' && value) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number.';
            }
        }

        // Languages validation
        if (field.name === 'languages' && value) {
            // Check if the input contains any spaces (not allowed between languages)
            if (/\s/.test(value)) {
                isValid = false;
                errorMessage = 'Languages must be separated by commas only. No spaces allowed (e.g., English,Sinhala,Tamil).';
            } else {
                // Check if languages are comma-separated without spaces
                const languages = value.split(',').filter(lang => lang.length > 0);

                if (languages.length === 0) {
                    isValid = false;
                    errorMessage = 'Please enter at least one language.';
                } else if (value.includes(',,')) {
                    // Check for double commas
                    isValid = false;
                    errorMessage = 'Please ensure languages are properly separated by single commas.';
                } else if (value.startsWith(',') || value.endsWith(',')) {
                    // Check for leading/trailing commas
                    isValid = false;
                    errorMessage = 'Languages cannot start or end with commas.';
                }
            }
        }

        // Date validation
        if (field.type === 'date' && value) {
            const date = new Date(value);
            const today = new Date();
            if (date > today) {
                isValid = false;
                errorMessage = 'Date cannot be in the future.';
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            const num = parseInt(value);
            const min = field.min ? parseInt(field.min) : null;
            const max = field.max ? parseInt(field.max) : null;

            if (min !== null && num < min) {
                isValid = false;
                errorMessage = `Value must be at least ${min}.`;
            }
            if (max !== null && num > max) {
                isValid = false;
                errorMessage = `Value must be at most ${max}.`;
            }
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('invalid');

        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            errorElement.style.cssText = `
                color: #dc3545;
                font-size: 0.8rem;
                margin-top: 5px;
                display: flex;
                align-items: center;
                gap: 5px;
            `;
            field.parentNode.appendChild(errorElement);
        }

        errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    }

    clearFieldError(field) {
        field.classList.remove('invalid');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            window.showNotification('Please select a valid image file (JPEG, JPG, or PNG).', 'error');
            input.value = '';
            return;
        }

        // Validate file size (5MB max)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            window.showNotification('File size must be less than 5MB.', 'error');
            input.value = '';
            return;
        }

        // Handle cover photo uploads from gallery
        if (input.id.startsWith('photoUpload')) {
            this.showCoverPhotoPreview(input, file);
            return;
        }

        // Handle profile photo uploads
        if (input.id === 'photoInput') {
            this.handleProfilePhotoUpload(file);
            return;
        }

        // Show preview for images
        if (file.type.startsWith('image/')) {
            this.showImagePreview(input, file);
        }

        // Show file name
        const fileName = file.name;
        const uploadBtn = input.previousElementSibling;
        if (uploadBtn && uploadBtn.classList.contains('btn-upload-photo')) {
            uploadBtn.innerHTML = `<i class="fas fa-check"></i> ${fileName}`;
        }

        console.log('File selected:', fileName);
    }

    showImagePreview(input, file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const container = input.closest('.license-photo-item, .avatar-upload');
            const placeholder = container ? container.querySelector('.photo-placeholder') : null;

            if (placeholder) {
                placeholder.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
            }
            
            // Update the upload button text to show file name
            const uploadBtn = container ? container.querySelector('.btn-upload-photo') : null;
            if (uploadBtn) {
                const shortName = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
                uploadBtn.innerHTML = `<i class="fas fa-check-circle"></i> ${shortName}`;
                uploadBtn.style.background = 'var(--primary)';
            }
            
            window.showNotification('Photo selected successfully', 'success');
        };
        reader.readAsDataURL(file);
    }

    showCoverPhotoPreview(input, file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Get the slot number from input id (e.g., "photoUpload1" -> 1)
            const slotNumber = input.id.replace('photoUpload', '');
            const slotIndex = parseInt(slotNumber) - 1;
            
            // Store the photo data (mark as new upload, not from database)
            this.uploadedPhotos[slotIndex] = {
                url: e.target.result,
                fromDatabase: false,
                isNewUpload: true
            };
            
            // Update preview
            const preview = document.getElementById(`uploadPreview${slotNumber}`);
            const removeBtn = document.querySelector(`.btn-remove-photo[data-slot="${slotNumber}"]`);
            
            if (preview) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo ${slotNumber}">`;
            }
            
            // Show remove button
            if (removeBtn) {
                removeBtn.style.display = 'flex';
            }
            
            window.showNotification('Photo selected for upload', 'success');
        };
        reader.readAsDataURL(file);
    }

    async handleProfilePhotoUpload(file) {
        try {
            const formData = new FormData();
            formData.append('profilePhoto', file);

            const response = await fetch(`${this.URL_ROOT}/ProfileController/changeProfilePhoto`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Update the profile avatar with the new photo
                const avatarImg = document.querySelector('.profile-avatar img');
                if (avatarImg) {
                    avatarImg.src = result.photoPath;
                }

                // Refresh profile data if needed
                await this.loadProfileData();

                window.showNotification('Profile photo updated successfully', 'success');
            } else {
                throw new Error(result.message || 'Failed to update profile photo');
            }
        } catch (error) {
            console.error('Profile photo upload error:', error);
            window.showNotification('Failed to update profile photo: ' + error.message, 'error');
        }
    }

    showEmailChangeModal() {
        if (this.emailChangeModal) {
            // Populate current email from the display
            const currentEmailField = document.getElementById('currentEmail');
            if (currentEmailField && this.currentEmailDisplay) {
                currentEmailField.value = this.currentEmailDisplay.textContent;
            }

            // Attach event listeners
            this.attachEmailModalListeners();

            this.emailChangeModal.classList.add('show');
        }
    }

    closeModal() {
        if (this.emailChangeModal) {
            this.emailChangeModal.classList.remove('show');
        }
    }

    sendEmailVerification() {
        if (!this.emailChangeForm) return;

        const formData = new FormData(this.emailChangeForm);
        const data = Object.fromEntries(formData);

        // Validate form
        if (!this.validateEmailChangeForm(data)) {
            return;
        }

        // Disable button and show loading
        const sendBtn = document.getElementById('sendVerificationBtn');
        const originalText = sendBtn.textContent;
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending...';

        // Make AJAX call to send OTP
        fetch(`${this.URL_ROOT}/ProfileController/sendEmailChangeOTP`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Show OTP verification form
                this.showOTPVerificationForm(data.newEmail);
                window.showNotification('Verification code sent to your new email address!', 'success');
            } else {
                window.showNotification(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error sending verification code:', error);
            window.showNotification('An error occurred while sending the verification code.', 'error');
        })
        .finally(() => {
            // Re-enable button
            sendBtn.disabled = false;
            sendBtn.textContent = originalText;
        });
    }

    validateEmailChangeForm(data) {
        // Check if new emails match
        if (data.newEmail !== data.confirmNewEmail) {
            window.showNotification('New email addresses do not match.', 'error');
            return false;
        }

        // Check if new email is different from current
        if (data.newEmail === data.currentEmail) {
            window.showNotification('New email must be different from current email.', 'error');
            return false;
        }

        // Check password is provided
        if (!data.password) {
            window.showNotification('Please enter your current password.', 'error');
            return false;
        }

        return true;
    }

    showOTPVerificationForm(newEmail) {
        const modalBody = this.emailChangeModal.querySelector('.modal-body');
        const modalActions = this.emailChangeModal.querySelector('.modal-actions');
        const modalHeader = this.emailChangeModal.querySelector('.modal-header h3');

        // Update header
        modalHeader.textContent = 'Verify New Email Address';

        // Replace form content with OTP input
        modalBody.innerHTML = `
            <div class="otp-verification">
                <p>A verification code has been sent to <strong>${newEmail}</strong></p>
                <p>Please enter the 6-digit code below:</p>
                <div class="form-group">
                    <label for="emailOTP">Verification Code</label>
                    <input type="text" id="emailOTP" name="otp" maxlength="6" required>
                    <div class="form-hint">Enter the 6-digit code sent to your new email address.</div>
                </div>
                <div class="otp-timer">
                    <p>Code expires in: <span id="otpTimer">10:00</span></p>
                    <button type="button" id="resendOTPBtn" class="btn-link" style="display: none;">Resend Code</button>
                </div>
            </div>
        `;

        // Update modal actions
        modalActions.innerHTML = `
            <button class="btn-cancel" id="cancelOTPBtn">Cancel</button>
            <button type="button" class="btn-save" id="verifyOTPBtn">Verify & Update Email</button>
        `;

        // Add event listeners
        const otpInput = document.getElementById('emailOTP');
        const verifyBtn = document.getElementById('verifyOTPBtn');
        const cancelBtn = document.getElementById('cancelOTPBtn');
        const resendBtn = document.getElementById('resendOTPBtn');

        // Auto-focus OTP input
        otpInput.focus();

        // Start countdown timer
        this.startOTPTimer();

        // Verify OTP button
        verifyBtn.addEventListener('click', () => {
            this.verifyEmailOTP(newEmail);
        });

        // Cancel button
        cancelBtn.addEventListener('click', () => {
            this.resetEmailChangeModal();
        });

        // Resend button
        resendBtn.addEventListener('click', () => {
            this.resendEmailOTP(newEmail);
        });

        // Allow Enter key to submit
        otpInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.verifyEmailOTP(newEmail);
            }
        });
    }

    startOTPTimer() {
        let timeLeft = 10 * 60; // 10 minutes
        const timerElement = document.getElementById('otpTimer');
        const resendBtn = document.getElementById('resendOTPBtn');

        const timer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(timer);
                timerElement.textContent = 'Expired';
                resendBtn.style.display = 'inline-block';
            }
        }, 1000);
    }

    verifyEmailOTP(newEmail) {
        const otpInput = document.getElementById('emailOTP');
        const otp = otpInput.value.trim();

        if (!otp || otp.length !== 6) {
            window.showNotification('Please enter a valid 6-digit verification code.', 'error');
            return;
        }

        // Disable button and show loading
        const verifyBtn = document.getElementById('verifyOTPBtn');
        const originalText = verifyBtn.textContent;
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';

        // Make AJAX call to verify OTP
        fetch(`${this.URL_ROOT}/ProfileController/verifyEmailChangeOTP`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ otp: otp })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Update the displayed email
                if (this.currentEmailDisplay) {
                    this.currentEmailDisplay.textContent = newEmail;
                }

                // Update current data
                if (this.currentData) {
                    this.currentData.email = newEmail;
                }

                window.showNotification('Email address updated successfully!', 'success');
                this.closeModal();
                this.resetEmailChangeModal();
            } else {
                window.showNotification(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            window.showNotification('An error occurred while verifying the code.', 'error');
        })
        .finally(() => {
            // Re-enable button
            verifyBtn.disabled = false;
            verifyBtn.textContent = originalText;
        });
    }

    resendEmailOTP(newEmail) {
        // Get form data again
        const currentEmail = document.getElementById('currentEmail').value;
        const password = document.getElementById('password').value;

        const data = {
            newEmail: newEmail,
            confirmNewEmail: newEmail, // Same as new email
            currentEmail: currentEmail,
            password: password
        };

        // Disable resend button
        const resendBtn = document.getElementById('resendOTPBtn');
        resendBtn.disabled = true;
        resendBtn.textContent = 'Sending...';

        // Make AJAX call to resend OTP
        fetch(`${this.URL_ROOT}/ProfileController/sendEmailChangeOTP`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                window.showNotification('Verification code resent successfully!', 'success');
                // Restart timer
                this.startOTPTimer();
                resendBtn.style.display = 'none';
            } else {
                window.showNotification(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error resending verification code:', error);
            window.showNotification('An error occurred while resending the code.', 'error');
        })
        .finally(() => {
            // Re-enable resend button
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend Code';
        });
    }

    resetEmailChangeModal() {
        const modalBody = this.emailChangeModal.querySelector('.modal-body');
        const modalActions = this.emailChangeModal.querySelector('.modal-actions');
        const modalHeader = this.emailChangeModal.querySelector('.modal-header h3');

        // Reset header
        modalHeader.textContent = 'Change Email Address';

        // Reset form content
        modalBody.innerHTML = `
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
        `;

        // Reset modal actions
        modalActions.innerHTML = `
            <button class="btn-cancel">Cancel</button>
            <button type="button" class="btn-save" id="sendVerificationBtn">Send Verification Code</button>
        `;

        // Re-attach event listeners
        this.attachEmailModalListeners();
    }

    attachEmailModalListeners() {
        // Send verification button
        const sendBtn = document.getElementById('sendVerificationBtn');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => {
                this.sendEmailVerification();
            });
        }

        // Cancel button
        const cancelBtn = this.emailChangeModal.querySelector('.btn-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.closeModal();
            });
        }
    }

    async handleTouristLicenseSubmission(formData) {
        console.log('Processing tourist license...', Object.fromEntries(formData));

        // Add the uploaded files to the form data
        const frontInput = document.getElementById('touristLicenseFrontInput');
        const backInput = document.getElementById('touristLicenseBackInput');

        if (frontInput && frontInput.files[0]) {
            formData.append('tLicensePhotoFront', frontInput.files[0]);
        }
        if (backInput && backInput.files[0]) {
            formData.append('tLicensePhotoBack', backInput.files[0]);
        }

        // Determine if this is a first submission or an edit
        const isAlreadySubmitted = this.currentData?.tLicenseSubmitted === true ||
                                   this.currentData?.tLicenseSubmitted === '1' ||
                                   this.currentData?.tLicenseSubmitted === 1;

        const apiUrl = isAlreadySubmitted
            ? `${this.URL_ROOT}/ProfileController/editTouristLicense`
            : `${this.URL_ROOT}/ProfileController/submitTLicense`;

        const actionMessage = isAlreadySubmitted ? 'Updating tourist license...' : 'Submitting tourist license...';
        const successMessage = isAlreadySubmitted ? 'Tourist license updated successfully!' : 'Tourist license submitted for review!';

        // Show loading notification
        window.showNotification(actionMessage, 'info');

        try {
            // Make API call to submit/update tourist license
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                window.showNotification(successMessage, 'success');

                // Switch to under review state
                const verifiedDiv = document.getElementById('touristLicenseVerified');
                const reviewDiv = document.getElementById('touristLicenseReview');
                const editDiv = document.getElementById('touristLicenseEdit');
                const editBtn = document.getElementById('editTouristLicenseBtn');
                const cancelBtn = document.getElementById('cancelTouristLicenseBtn');
                const saveBtn = document.getElementById('saveTouristLicenseBtn');
                const updateBtn = document.getElementById('updateTouristLicenseBtn');
                const statusElement = document.getElementById('touristLicenseStatus');

                if (verifiedDiv) verifiedDiv.style.display = 'none';
                if (editDiv) editDiv.style.display = 'none';
                if (reviewDiv) reviewDiv.style.display = 'block';
                if (editBtn) editBtn.style.display = 'none';
                if (cancelBtn) cancelBtn.style.display = 'none';
                if (saveBtn) saveBtn.style.display = 'none';
                if (updateBtn) updateBtn.style.display = 'none';

                // Update status badge
                if (statusElement) {
                    statusElement.innerHTML = '<span class="status-badge status-pending">Under Review</span>';
                }

                // Update submitted date
                const submittedDate = document.getElementById('touristLicenseSubmittedDate');
                if (submittedDate) {
                    submittedDate.textContent = new Date().toLocaleString();
                }

                // Reset current section
                this.currentSection = null;

                // Reload profile data to get updated status from server
                await this.loadProfileData();

            } else {
                // Show error message from server
                window.showNotification(result.message || 'Failed to process tourist license. Please try again.', 'error');
            }

        } catch (error) {
            console.error('Error processing tourist license:', error);
            window.showNotification('An error occurred while processing tourist license. Please try again.', 'error');
        }
    }

    completeTouristLicenseVerification(formData) {
        // Switch to verified state
        const verifiedDiv = document.getElementById('touristLicenseVerified');
        const reviewDiv = document.getElementById('touristLicenseReview');
        const editBtn = document.getElementById('editTouristLicenseBtn');
        const statusElement = document.getElementById('touristLicenseStatus');

        reviewDiv.style.display = 'none';
        verifiedDiv.style.display = 'block';
        editBtn.style.display = 'inline-flex'; // Show edit button again

        // Update verified license info with submitted data
        const licenseNumber = formData.get('touristLicenseNumber');
        const licenseExpiry = formData.get('touristLicenseExpiry');

        const verifiedNumber = document.getElementById('verifiedTouristLicenseNumber');
        const verifiedExpiry = document.getElementById('verifiedTouristLicenseExpiry');

        if (verifiedNumber && licenseNumber) {
            verifiedNumber.textContent = licenseNumber;
        }
        if (verifiedExpiry && licenseExpiry) {
            verifiedExpiry.textContent = new Date(licenseExpiry).toLocaleDateString();
        }

        // Update license status
        if (statusElement) {
            statusElement.innerHTML = '<span class="status-badge status-valid">Valid</span>';
        }

        // Update overall verification status in header
        const verificationStatus = document.getElementById('verificationStatus');
        if (verificationStatus) {
            verificationStatus.innerHTML = '<span class="status-badge status-verified"><i class="fas fa-shield-alt"></i> Verified Driver</span>';
        }

        // Show verification complete message
        window.showNotification('Tourist license verified successfully!', 'success');

        // Reset current section
        this.currentSection = null;
    }
}

window.GuideProfileManager = GuideProfileManager;
window.guideProfileManager = new GuideProfileManager();

// Add field validation styles to CSS dynamically
const style = document.createElement('style');
style.textContent = `
    .field-error {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
    }
`;
document.head.appendChild(style);

})();