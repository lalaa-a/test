(function(){
// Tourist License Verification JavaScript
    if (window.LicenseVerificationManager) {
        console.log('LicenseVerificationManager already exists, cleaning up...');
        if (window.licenseVerificationManager) {
            delete window.licenseVerificationManager;
        }
        delete window.LicenseVerificationManager;
    }

    // License Verification Manager
    class LicenseVerificationManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.currentLicense = null;
            this.pendingVerifyLicenseId = null;
            this.pendingRejectLicenseId = null;
            this.pendingRevokeLicenseVerificationId = null;
            this.pendingRevokeLicenseRejectionId = null;
            this.licenses = {
                pending: [],
                verified: [],
                rejected: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadLicenses();
        }

        bindEvents() {
            // Pending section search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterLicenses('pending', e.target.value);
                });
            }

            const pendingLicenseTypeFilter = document.getElementById('pendingLicenseTypeFilter');
            if (pendingLicenseTypeFilter) {
                pendingLicenseTypeFilter.addEventListener('change', () => {
                    this.filterLicenses('pending');
                });
            }

            // Verified section search and filter
            const verifiedSearchInput = document.getElementById('verifiedSearchInput');
            if (verifiedSearchInput) {
                verifiedSearchInput.addEventListener('input', (e) => {
                    this.filterLicenses('verified', e.target.value);
                });
            }

            const verifiedLicenseTypeFilter = document.getElementById('verifiedLicenseTypeFilter');
            if (verifiedLicenseTypeFilter) {
                verifiedLicenseTypeFilter.addEventListener('change', () => {
                    this.filterLicenses('verified');
                });
            }

            // Rejected section search and filter
            const rejectedSearchInput = document.getElementById('rejectedSearchInput');
            if (rejectedSearchInput) {
                rejectedSearchInput.addEventListener('input', (e) => {
                    this.filterLicenses('rejected', e.target.value);
                });
            }

            const rejectedLicenseTypeFilter = document.getElementById('rejectedLicenseTypeFilter');
            if (rejectedLicenseTypeFilter) {
                rejectedLicenseTypeFilter.addEventListener('change', () => {
                    this.filterLicenses('rejected');
                });
            }

            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    this.switchToTab(targetId);
                });
            });

            // Modal events
            this.bindModalEvents();
        }

        bindModalEvents() {
            // License details modal events
            const closeBtn = document.querySelector('#licenseDetailsModal .modal-footer .btn-secondary');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(document.getElementById('licenseDetailsModal')));
            }

            // Verification modal events
            const cancelVerifyLicenseBtn = document.getElementById('cancelVerifyLicenseBtn');
            const confirmVerifyLicenseBtn = document.getElementById('confirmVerifyLicenseBtn');

            if (cancelVerifyLicenseBtn) {
                cancelVerifyLicenseBtn.addEventListener('click', () => this.closeVerifyLicenseConfirmModal());
            }

            if (confirmVerifyLicenseBtn) {
                confirmVerifyLicenseBtn.addEventListener('click', () => this.confirmVerifyLicense());
            }

            // Rejection modal events
            const cancelRejectLicenseBtn = document.getElementById('cancelRejectLicenseBtn');
            const confirmRejectLicenseBtn = document.getElementById('confirmRejectLicenseBtn');

            if (cancelRejectLicenseBtn) {
                cancelRejectLicenseBtn.addEventListener('click', () => this.closeRejectLicenseConfirmModal());
            }

            if (confirmRejectLicenseBtn) {
                confirmRejectLicenseBtn.addEventListener('click', () => this.confirmRejectLicense());
            }

            // Revoke verification modal events
            const cancelRevokeLicenseVerificationBtn = document.getElementById('cancelRevokeLicenseVerificationBtn');
            const confirmRevokeLicenseVerificationBtn = document.getElementById('confirmRevokeLicenseVerificationBtn');

            if (cancelRevokeLicenseVerificationBtn) {
                cancelRevokeLicenseVerificationBtn.addEventListener('click', () => this.closeRevokeLicenseVerificationModal());
            }

            if (confirmRevokeLicenseVerificationBtn) {
                confirmRevokeLicenseVerificationBtn.addEventListener('click', () => this.confirmRevokeLicenseVerification());
            }

            // Revoke rejection modal events
            const cancelRevokeLicenseRejectionBtn = document.getElementById('cancelRevokeLicenseRejectionBtn');
            const confirmRevokeLicenseRejectionBtn = document.getElementById('confirmRevokeLicenseRejectionBtn');

            if (cancelRevokeLicenseRejectionBtn) {
                cancelRevokeLicenseRejectionBtn.addEventListener('click', () => this.closeRevokeLicenseRejectionModal());
            }

            if (confirmRevokeLicenseRejectionBtn) {
                confirmRevokeLicenseRejectionBtn.addEventListener('click', () => this.confirmRevokeLicenseRejection());
            }
        }

        async loadLicenses() {
            try {
                const [pendingResponse, verifiedResponse, rejectedResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/moderator/getPendingLicenses`),
                    fetch(`${this.URL_ROOT}/moderator/getVerifiedLicenses`),
                    fetch(`${this.URL_ROOT}/moderator/getRejectedLicenses`)
                ]);

                const [pendingData, verifiedData, rejectedData] = await Promise.all([
                    pendingResponse.json(),
                    verifiedResponse.json(),
                    rejectedResponse.json()
                ]);

                if (pendingData.success) {
                    this.licenses.pending = pendingData.licenses;
                }

                if (verifiedData.success) {
                    this.licenses.verified = verifiedData.licenses;
                }

                if (rejectedData.success) {
                    this.licenses.rejected = rejectedData.licenses;
                }

                this.renderLicenses('pending');
                this.renderLicenses('verified');
                this.renderLicenses('rejected');
                this.updateStats();
                this.switchToTab('pending-section');
            } catch (error) {
                console.error('Error loading licenses:', error);
                window.showNotification('Error loading licenses', 'error');
            }
        }

        updateStats() {
            const pendingGuides = this.licenses.pending.filter(license => license.account_type === 'guide').length;
            const pendingDrivers = this.licenses.pending.filter(license => license.account_type === 'driver').length;
            const verifiedGuides = this.licenses.verified.filter(license => license.account_type === 'guide').length;
            const verifiedDrivers = this.licenses.verified.filter(license => license.account_type === 'driver').length;
            const rejectedLicenses = this.licenses.rejected.length;

            document.getElementById('pendingGuidesCount').textContent = pendingGuides;
            document.getElementById('pendingDriversCount').textContent = pendingDrivers;
            document.getElementById('verifiedGuidesCount').textContent = verifiedGuides;
            document.getElementById('verifiedDriversCount').textContent = verifiedDrivers;
            document.getElementById('rejectedLicensesCount').textContent = rejectedLicenses;
        }

        filterLicenses(section, searchTerm = '') {
            const licenseType = document.getElementById(`${section}LicenseTypeFilter`).value;

            let filteredLicenses = [...this.licenses[section]];

            if (searchTerm) {
                filteredLicenses = filteredLicenses.filter(license =>
                    license.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    license.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    (license.license_number && license.license_number.toLowerCase().includes(searchTerm.toLowerCase()))
                );
            }

            if (licenseType !== 'all') {
                filteredLicenses = filteredLicenses.filter(license => license.account_type === licenseType);
            }

            this.renderFilteredLicenses(section, filteredLicenses);
        }

        renderLicenses(section) {
            const container = document.getElementById(`${section}LicensesGrid`);
            if (!container) return;

            if (this.licenses[section].length === 0) {
                container.innerHTML = `
                    <tr class="no-licenses">
                        <td colspan="8">
                            <i class="fas fa-inbox"></i>
                            <p>No ${section} licenses ${section === 'verified' ? 'yet' : 'to verify'}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = this.licenses[section].map(license => this.createLicenseRow(license, section)).join('');
        }

        renderFilteredLicenses(section, filteredLicenses) {
            const container = document.getElementById(`${section}LicensesGrid`);
            if (!container) return;

            if (filteredLicenses.length === 0) {
                container.innerHTML = `
                    <tr class="no-licenses">
                        <td colspan="8">
                            <i class="fas fa-search"></i>
                            <p>No licenses found matching your search</p>
                        </td>
                    </tr>
                `;
                return;
            }

            container.innerHTML = filteredLicenses.map(license => this.createLicenseRow(license, section)).join('');
        }

        createLicenseRow(license, section) {
            const profilePhoto = license.profile_photo ? `${this.UP_ROOT}${license.profile_photo}` : '/test/public/img/default-avatar.png';
            const licenseTypeIcon = license.account_type === 'guide' ? 'fas fa-map-marked-alt' : 'fas fa-car';
            const licenseTypeLabel = license.account_type === 'guide' ? 'Guide' : 'Driver';
            const dateField = section === 'verified' ? license.verified_at : section === 'rejected' ? license.rejected_at : license.created_at;

            return `
                <tr>
                    <td class="profile-cell">
                        <img src="${profilePhoto}" alt="Profile" class="license-avatar-small" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
                    </td>
                    <td>${license.name}</td>
                    <td>${license.email}</td>
                    <td>
                        <span class="license-type-badge ${license.account_type}">
                            <i class="${licenseTypeIcon}"></i>
                            ${licenseTypeLabel}
                        </span>
                    </td>
                    <td>${license.license_number || 'N/A'}</td>
                    <td>${license.license_expire_date ? new Date(license.license_expire_date).toLocaleDateString() : 'N/A'}</td>
                    <td>${new Date(dateField).toLocaleDateString()}</td>
                    <td class="actions-cell">
                        <button class="btn btn-view" data-license-id="${license.id}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                        ${section === 'pending' ? `
                            <button class="btn-verify-small" data-action="verify" data-license-id="${license.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-reject-small" data-action="reject" data-license-id="${license.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : `
                            <button class="btn-revoke-small" data-action="revoke" data-license-id="${license.id}">
                                <i class="fas fa-undo"></i>
                            </button>
                        `}
                    </td>
                </tr>
            `;
        }

        async showLicenseDetails(licenseId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/getLicenseDetails/${licenseId}`);
                if (!response.ok) {
                    throw new Error('Failed to load license details');
                }

                const data = await response.json();
                if (data.success) {
                    this.currentLicense = data.license;
                    this.renderLicenseDetails(data.license);

                    const modal = document.getElementById('licenseDetailsModal');
                    modal.classList.add('show');
                } else {
                    window.showNotification('Error loading license details', 'error');
                }
            } catch (error) {
                console.error('Error loading license details:', error);
                window.showNotification('Error loading license details', 'error');
            }
        }

        renderLicenseDetails(license) {
            const profilePhoto = license.profile_photo ? `${this.UP_ROOT}${license.profile_photo}` : '/test/public/img/default-avatar.png';
            const licenseFront = license.license_photo_front ? `${this.UP_ROOT}${license.license_photo_front}` : null;
            const licenseBack = license.license_photo_back ? `${this.UP_ROOT}${license.license_photo_back}` : null;
            const licenseTypeIcon = license.account_type === 'guide' ? 'fas fa-map-marked-alt' : 'fas fa-car';
            const licenseTypeLabel = license.account_type === 'guide' ? 'Guide' : 'Driver';

            document.getElementById('licenseDetailsContent').innerHTML = `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <img src="${profilePhoto}" alt="Profile" class="user-profile-photo" onclick="licenseVerificationManager.viewPhoto('${profilePhoto}')" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
                        <h3>${license.name}</h3>
                        <p class="user-account-type">
                            <i class="${licenseTypeIcon}"></i>
                            ${licenseTypeLabel}
                        </p>
                    </div>
                    <div class="user-info-section">
                        ${license.rejectionReason ? `
                        <div class="info-group rejection-reason-group">
                            <h4><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h4>
                            <div class="rejection-reason-content">
                                ${license.rejectionReason}
                            </div>
                        </div>
                        ` : ''}
                        <div class="info-group">
                            <h4>Basic Information</h4>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${license.email}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span>${license.phone || 'Not provided'}</span>
                            </div>
                            <div class="info-item">
                                <label>License Number:</label>
                                <span>${license.license_number || 'Not provided'}</span>
                            </div>
                            <div class="info-item">
                                <label>License Expiry:</label>
                                <span>${license.license_expire_date ? new Date(license.license_expire_date).toLocaleDateString() : 'Not provided'}</span>
                            </div>
                            <div class="info-item">
                                <label>Applied Date:</label>
                                <span>${new Date(license.submitted_at).toLocaleDateString()}</span>
                            </div>
                            ${(licenseFront || licenseBack) ? `
                            <div class="documents-section">
                                <label>Tourist License Documents:</label>
                                <div class="documents-grid">
                                    ${licenseFront ? `
                                        <div class="document-item">
                                            <img src="${licenseFront}" alt="License Front" class="document-photo" onclick="licenseVerificationManager.viewPhoto('${licenseFront}')">
                                        </div>
                                    ` : ''}
                                    ${licenseBack ? `
                                        <div class="document-item">
                                            <img src="${licenseBack}" alt="License Back" class="document-photo" onclick="licenseVerificationManager.viewPhoto('${licenseBack}')">
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            // Update modal buttons based on status
            const verifyBtn = document.getElementById('verifyLicenseBtn');
            const rejectBtn = document.getElementById('rejectLicenseBtn');

            if (license.status === 'approved') {
                verifyBtn.style.display = 'none';
                rejectBtn.textContent = 'Revoke Verification';
                rejectBtn.className = 'btn btn-warning';
                rejectBtn.onclick = () => this.revokeLicenseVerification(license.userId);
            } else if (license.status === 'rejected') {
                verifyBtn.style.display = 'none';
                rejectBtn.textContent = 'Revoke Rejection';
                rejectBtn.className = 'btn btn-warning';
                rejectBtn.onclick = () => this.revokeLicenseRejection(license.userId);
            } else {
                // pending status
                verifyBtn.style.display = 'inline-block';
                rejectBtn.textContent = 'Reject License';
                rejectBtn.className = 'btn btn-danger';
                rejectBtn.onclick = () => this.rejectLicense(license.userId);
            }
        }

        viewPhoto(photoUrl) {
            const modal = document.getElementById('photoViewerModal');
            const img = document.getElementById('photoViewerImage');
            if (modal && img) {
                img.src = photoUrl;
                modal.style.display = 'block';

                // Adjust image size for large images
                img.onload = () => {
                    this.adjustImageSize(img);
                };
            }
        }

        adjustImageSize(img) {
            const maxWidth = window.innerWidth * 0.8;
            const maxHeight = window.innerHeight * 0.8;

            let { naturalWidth: width, naturalHeight: height } = img;

            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width *= ratio;
                height *= ratio;
            }

            img.style.width = `${width}px`;
            img.style.height = `${height}px`;
        }

        async verifyLicense(licenseId = null) {
            const id = licenseId || this.currentLicense?.id;
            if (!id) return;

            this.pendingVerifyLicenseId = id;
            this.showVerifyLicenseConfirmModal();
        }

        showVerifyLicenseConfirmModal() {
            const modal = document.getElementById('verifyLicenseConfirmModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeVerifyLicenseConfirmModal() {
            const modal = document.getElementById('verifyLicenseConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingVerifyLicenseId = null;
            }
        }

        async confirmVerifyLicense() {
            if (this.pendingVerifyLicenseId) {
                const licenseId = this.pendingVerifyLicenseId;
                this.closeVerifyLicenseConfirmModal();
                this.pendingVerifyLicenseId = null;
                await this.performVerifyLicense(licenseId);
            } else {
                window.showNotification('No license selected for verification', 'error');
            }
        }

        async performVerifyLicense(licenseId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/verifyLicense/${licenseId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('License verified successfully', 'success');
                    this.closeModal(document.getElementById('licenseDetailsModal'));
                    await this.loadLicenses();
                } else {
                    throw new Error(data.message || 'Failed to verify license');
                }
            } catch (error) {
                console.error('Error verifying license:', error);
                window.showNotification(error.message || 'Error verifying license', 'error');
            }
        }

        async rejectLicense(licenseId = null) {
            const id = licenseId || this.currentLicense?.id;
            if (!id) return;

            this.pendingRejectLicenseId = id;
            this.showRejectLicenseConfirmModal();
        }

        showRejectLicenseConfirmModal() {
            const modal = document.getElementById('rejectLicenseConfirmModal');
            if (modal) {
                // Clear previous reason
                const reasonTextarea = document.getElementById('licenseRejectionReason');
                if (reasonTextarea) {
                    reasonTextarea.value = '';
                }
                modal.classList.add('show');
            }
        }

        closeRejectLicenseConfirmModal() {
            const modal = document.getElementById('rejectLicenseConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRejectLicenseId = null;
            }
        }

        async confirmRejectLicense() {
            const reasonTextarea = document.getElementById('licenseRejectionReason');
            const reason = reasonTextarea ? reasonTextarea.value.trim() : '';

            if (!reason) {
                window.showNotification('Please provide a rejection reason', 'error');
                reasonTextarea?.focus();
                return;
            }

            if (this.pendingRejectLicenseId) {
                const licenseId = this.pendingRejectLicenseId;
                this.closeRejectLicenseConfirmModal();
                this.pendingRejectLicenseId = null;
                await this.performRejectLicense(licenseId, reason);
            } else {
                window.showNotification('No license selected for rejection', 'error');
            }
        }

        async performRejectLicense(licenseId, reason) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/rejectLicense/${licenseId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('License rejected successfully', 'success');
                    this.closeModal(document.getElementById('licenseDetailsModal'));
                    await this.loadLicenses();
                } else {
                    throw new Error(data.message || 'Failed to reject license');
                }
            } catch (error) {
                console.error('Error rejecting license:', error);
                window.showNotification(error.message || 'Error rejecting license', 'error');
            }
        }

        revokeLicenseVerification(licenseId = null) {
            const id = licenseId || this.currentLicense?.id;
            if (!id) return;

            this.pendingRevokeLicenseVerificationId = id;
            this.showRevokeLicenseVerificationModal();
        }

        showRevokeLicenseVerificationModal() {
            const modal = document.getElementById('revokeLicenseVerificationModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeLicenseVerificationModal() {
            const modal = document.getElementById('revokeLicenseVerificationModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeLicenseVerificationId = null;
            }
        }

        async confirmRevokeLicenseVerification() {
            if (this.pendingRevokeLicenseVerificationId) {
                const licenseId = this.pendingRevokeLicenseVerificationId;
                this.closeRevokeLicenseVerificationModal();
                this.pendingRevokeLicenseVerificationId = null;
                await this.performRevokeLicenseVerification(licenseId);
            } else {
                window.showNotification('No license selected for verification revoke', 'error');
            }
        }

        async performRevokeLicenseVerification(licenseId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeLicenseVerification/${licenseId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('License verification revoked successfully', 'success');
                    this.closeModal(document.getElementById('licenseDetailsModal'));
                    await this.loadLicenses();
                } else {
                    throw new Error(data.message || 'Failed to revoke license verification');
                }
            } catch (error) {
                console.error('Error revoking license verification:', error);
                window.showNotification(error.message || 'Error revoking license verification', 'error');
            }
        }

        revokeLicenseRejection(licenseId = null) {
            const id = licenseId || this.currentLicense?.id;
            if (!id) return;

            this.pendingRevokeLicenseRejectionId = id;
            this.showRevokeLicenseRejectionModal();
        }

        showRevokeLicenseRejectionModal() {
            const modal = document.getElementById('revokeLicenseRejectionModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeLicenseRejectionModal() {
            const modal = document.getElementById('revokeLicenseRejectionModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeLicenseRejectionId = null;
            }
        }

        async confirmRevokeLicenseRejection() {
            if (this.pendingRevokeLicenseRejectionId) {
                const licenseId = this.pendingRevokeLicenseRejectionId;
                this.closeRevokeLicenseRejectionModal();
                this.pendingRevokeLicenseRejectionId = null;
                await this.performRevokeLicenseRejection(licenseId);
            } else {
                window.showNotification('No license selected for rejection revoke', 'error');
            }
        }

        async performRevokeLicenseRejection(licenseId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeLicenseRejection/${licenseId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('License rejection revoked successfully', 'success');
                    this.closeModal(document.getElementById('licenseDetailsModal'));
                    await this.loadLicenses();
                } else {
                    throw new Error(data.message || 'Failed to revoke license rejection');
                }
            } catch (error) {
                console.error('Error revoking license rejection:', error);
                window.showNotification(error.message || 'Error revoking license rejection', 'error');
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
                this.currentLicense = null;
            }
        }

        updateActiveNavLink(targetId) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`.nav-link[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        switchToTab(targetId) {
            // Hide all sections
            document.querySelectorAll('.verification-section').forEach(sec => sec.style.display = 'none');
            // Show target section
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            // Update active nav link
            this.updateActiveNavLink(targetId);
        }
    }

    // Event delegation for dynamic elements
    document.addEventListener('click', function(e) {
        // View license button
        if (e.target.closest('.btn-view')) {
            const button = e.target.closest('.btn-view');
            const licenseId = button.getAttribute('data-license-id');
            if (licenseId && window.licenseVerificationManager) {
                window.licenseVerificationManager.showLicenseDetails(licenseId);
            }
        }

        // Verify license button
        if (e.target.closest('.btn-verify-small')) {
            const button = e.target.closest('.btn-verify-small');
            const licenseId = button.getAttribute('data-license-id');
            if (licenseId && window.licenseVerificationManager) {
                window.licenseVerificationManager.verifyLicense(licenseId);
            }
        }

        // Reject license button
        if (e.target.closest('.btn-reject-small')) {
            const button = e.target.closest('.btn-reject-small');
            const licenseId = button.getAttribute('data-license-id');
            if (licenseId && window.licenseVerificationManager) {
                window.licenseVerificationManager.rejectLicense(licenseId);
            }
        }

        // Revoke license button
        if (e.target.closest('.btn-revoke-small')) {
            const button = e.target.closest('.btn-revoke-small');
            const licenseId = button.getAttribute('data-license-id');
            if (licenseId && window.licenseVerificationManager) {
                const license = window.licenseVerificationManager.licenses.verified.find(l => l.id == licenseId) ||
                               window.licenseVerificationManager.licenses.rejected.find(l => l.id == licenseId);
                if (license) {
                    if (license.status === 'approved') {
                        window.licenseVerificationManager.revokeLicenseVerification(licenseId);
                    } else if (license.status === 'rejected') {
                        window.licenseVerificationManager.revokeLicenseRejection(licenseId);
                    }
                }
            }
        }
    });

    // Global functions for modal close buttons
    window.closeLicenseModal = function() {
        if (window.licenseVerificationManager) {
            window.licenseVerificationManager.closeModal(document.getElementById('licenseDetailsModal'));
        }
    };

    window.closePhotoViewer = function() {
        const modal = document.getElementById('photoViewerModal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    // Initialize the manager
    window.LicenseVerificationManager = LicenseVerificationManager;
    window.licenseVerificationManager = new LicenseVerificationManager();

})();
