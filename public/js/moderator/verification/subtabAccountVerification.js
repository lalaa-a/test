
(function(){
// Account Verification JavaScript
    if (window.AccountVerificationManager) {
        console.log('AccountVerificationManager already exists, cleaning up...');
        if (window.accountVerificationManager) {
            delete window.accountVerificationManager;
        }
        delete window.AccountVerificationManager;
    }

    // Account Verification Manager
    class AccountVerificationManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.currentUser = null;
            this.pendingVerifyUserId = null;
            this.pendingRejectUserId = null;
            this.pendingRevokeVerificationUserId = null;
            this.pendingRevokeRejectionUserId = null;
            this.accounts = {
                pending: [],
                verified: [],
                rejected: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadAccounts();
        }

        bindEvents() {
            // Pending section search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterAccounts('pending', e.target.value);
                });
            }

            const pendingAccountTypeFilter = document.getElementById('pendingAccountTypeFilter');
            if (pendingAccountTypeFilter) {
                pendingAccountTypeFilter.addEventListener('change', () => {
                    this.filterAccounts('pending');
                });
            }

            // Verified section search and filter
            const verifiedSearchInput = document.getElementById('verifiedSearchInput');
            if (verifiedSearchInput) {
                verifiedSearchInput.addEventListener('input', (e) => {
                    this.filterAccounts('verified', e.target.value);
                });
            }

            const verifiedAccountTypeFilter = document.getElementById('verifiedAccountTypeFilter');
            if (verifiedAccountTypeFilter) {
                verifiedAccountTypeFilter.addEventListener('change', () => {
                    this.filterAccounts('verified');
                });
            }

            // Rejected section search and filter
            const rejectedSearchInput = document.getElementById('rejectedSearchInput');
            if (rejectedSearchInput) {
                rejectedSearchInput.addEventListener('input', (e) => {
                    this.filterAccounts('rejected', e.target.value);
                });
            }

            const rejectedAccountTypeFilter = document.getElementById('rejectedAccountTypeFilter');
            if (rejectedAccountTypeFilter) {
                rejectedAccountTypeFilter.addEventListener('change', () => {
                    this.filterAccounts('rejected');
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

            // Modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => this.closeModal(btn.closest('.modal')));
            });

            // Modal overlays
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modal);
                    }
                });
            });

            // Modal footer close button
            const closeBtn = document.querySelector('#userDetailsModal .modal-footer .btn-secondary');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(document.getElementById('userDetailsModal')));
            }

            // Photo viewer modal events
            const photoViewerModal = document.getElementById('photoViewerModal');
            const photoViewerClose = document.querySelector('.photo-viewer-close');

            if (photoViewerModal) {
                photoViewerModal.addEventListener('click', (e) => {
                    if (e.target === photoViewerModal) {
                        this.closePhotoViewer();
                    }
                });
            }

            if (photoViewerClose) {
                photoViewerClose.addEventListener('click', () => this.closePhotoViewer());
            }

            // ESC key to close photo viewer
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closePhotoViewer();
                }
            });

            // Verification confirmation modal events
            const cancelVerifyBtn = document.getElementById('cancelVerifyBtn');
            const confirmVerifyBtn = document.getElementById('confirmVerifyBtn');

            if (cancelVerifyBtn) {
                cancelVerifyBtn.addEventListener('click', () => this.closeVerifyConfirmModal());
            }

            if (confirmVerifyBtn) {
                confirmVerifyBtn.addEventListener('click', () => this.confirmVerifyAccount());
            }

            // Rejection confirmation modal events
            const cancelRejectBtn = document.getElementById('cancelRejectBtn');
            const confirmRejectBtn = document.getElementById('confirmRejectBtn');

            if (cancelRejectBtn) {
                cancelRejectBtn.addEventListener('click', () => this.closeRejectConfirmModal());
            }

            if (confirmRejectBtn) {
                confirmRejectBtn.addEventListener('click', () => this.confirmRejectAccount());
            }

            // Revoke verification modal events
            const cancelRevokeVerificationBtn = document.getElementById('cancelRevokeVerificationBtn');
            const confirmRevokeVerificationBtn = document.getElementById('confirmRevokeVerificationBtn');

            if (cancelRevokeVerificationBtn) {
                cancelRevokeVerificationBtn.addEventListener('click', () => this.closeRevokeVerificationModal());
            }

            if (confirmRevokeVerificationBtn) {
                confirmRevokeVerificationBtn.addEventListener('click', () => this.confirmRevokeVerification());
            }

            // Revoke rejection modal events
            const cancelRevokeRejectionBtn = document.getElementById('cancelRevokeRejectionBtn');
            const confirmRevokeRejectionBtn = document.getElementById('confirmRevokeRejectionBtn');

            if (cancelRevokeRejectionBtn) {
                cancelRevokeRejectionBtn.addEventListener('click', () => this.closeRevokeRejectionModal());
            }

            if (confirmRevokeRejectionBtn) {
                confirmRevokeRejectionBtn.addEventListener('click', () => this.confirmRevokeRejection());
            }

            // Account row clicks (View button)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const accountId = button.dataset.accountId;
                    if (accountId) {
                        this.showUserDetails(accountId);
                    }
                }
            });

            // Action buttons (Verify, Reject, Revoke)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-verify-small')) {
                    const button = e.target.closest('.btn-verify-small');
                    const accountId = button.dataset.accountId;
                    if (accountId) {
                        this.verifyAccount(accountId);
                    }
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-reject-small')) {
                    const button = e.target.closest('.btn-reject-small');
                    const accountId = button.dataset.accountId;
                    if (accountId) {
                        this.rejectAccount(accountId);
                    }
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-revoke-small')) {
                    const button = e.target.closest('.btn-revoke-small');
                    const accountId = button.dataset.accountId;
                    const revokeType = button.dataset.revokeType;
                    if (accountId) {
                        if (revokeType === 'verified') {
                            this.revokeVerification(accountId);
                        } else if (revokeType === 'rejected') {
                            this.revokeRejection(accountId);
                        }
                    }
                }
            });
        }

        async loadAccounts() {
            try {
                console.log('Loading accounts...');
                // Load pending, verified, and rejected accounts
                const [pendingResponse, verifiedResponse, rejectedResponse] = await Promise.all([
                    fetch(`${this.URL_ROOT}/Moderator/getAccounts/pending`),
                    fetch(`${this.URL_ROOT}/Moderator/getAccounts/verified`),
                    fetch(`${this.URL_ROOT}/Moderator/getAccounts/rejected`)
                ]);

                const [pendingData, verifiedData, rejectedData] = await Promise.all([
                    pendingResponse.json(),
                    verifiedResponse.json(),
                    rejectedResponse.json()
                ]);

                if (pendingData.success) {
                    this.accounts.pending = pendingData.accounts;
                }

                if (verifiedData.success) {
                    this.accounts.verified = verifiedData.accounts;
                }

                if (rejectedData.success) {
                    this.accounts.rejected = rejectedData.accounts;
                }

                this.updateStats();
                this.renderAccounts('pending');
                this.renderAccounts('verified');
                this.renderAccounts('rejected');
                this.switchToTab('pending-section');
            } catch (error) {
                console.error('Error loading accounts:', error);
                window.showNotification('Error loading accounts', 'error');
            }
        }

        updateStats() {
            const pendingGuides = this.accounts.pending.filter(acc => acc.account_type === 'guide').length;
            const pendingDrivers = this.accounts.pending.filter(acc => acc.account_type === 'driver').length;
            const verifiedGuides = this.accounts.verified.filter(acc => acc.account_type === 'guide').length;
            const verifiedDrivers = this.accounts.verified.filter(acc => acc.account_type === 'driver').length;
            const rejectedAccounts = this.accounts.rejected.length;

            document.getElementById('pendingGuidesCount').textContent = pendingGuides;
            document.getElementById('pendingDriversCount').textContent = pendingDrivers;
            document.getElementById('verifiedGuidesCount').textContent = verifiedGuides;
            document.getElementById('verifiedDriversCount').textContent = verifiedDrivers;
            document.getElementById('rejectedAccountsCount').textContent = rejectedAccounts;
        }

        filterAccounts(section, searchTerm = '') {
            const accountType = document.getElementById(`${section}AccountTypeFilter`).value;

            let filteredAccounts = [...this.accounts[section]];

            if (accountType !== 'all') {
                filteredAccounts = filteredAccounts.filter(acc => acc.account_type === accountType);
            }

            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredAccounts = filteredAccounts.filter(acc =>
                    acc.name.toLowerCase().includes(term) ||
                    acc.email.toLowerCase().includes(term) ||
                    acc.nic.toLowerCase().includes(term)
                );
            }

            this.renderAccounts(section, filteredAccounts);
        }

        renderAccounts(section, accounts = null) {
            const targetGrid = section === 'pending' ? 'pendingAccountsGrid' : 
                             section === 'verified' ? 'verifiedAccountsGrid' : 'rejectedAccountsGrid';

            if (accounts === null) {
                accounts = this.accounts[section];
            }

            const tbody = document.getElementById(targetGrid);

            if (accounts.length === 0) {
                const sectionName = section === 'pending' ? 'pending' : 
                                   section === 'verified' ? 'verified' : 'rejected';
                const icon = section === 'pending' ? 'inbox' : 
                            section === 'verified' ? 'check-circle' : 'user-times';
                const message = section === 'pending' ? 'to verify' : 
                               section === 'verified' ? 'yet' : 'yet';
                tbody.innerHTML = `
                    <tr class="no-accounts">
                        <td colspan="7">
                            <i class="fas fa-${icon}"></i>
                            <p>No ${sectionName} accounts ${message}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = accounts.map(account => this.createAccountRow(account, section)).join('');
        }

        createAccountRow(account, section) {
            const profilePhoto = account.profile_photo ? `${this.UP_ROOT}${account.profile_photo}` : `${this.URL_ROOT}/public/img/default-avatar.png`;
            const accountTypeIcon = account.account_type === 'guide' ? 'fas fa-map-marked-alt' : 'fas fa-car';
            const accountTypeLabel = account.account_type === 'guide' ? 'Guide' : 'Driver';
            const dateField = section === 'pending' ? account.created_at : (account.verification_created_at || account.created_at);
            const dateLabel = section === 'pending' ? 'Applied' : section === 'verified' ? 'Verified' : 'Rejected';

            return `
                <tr class="account-row" data-account-id="${account.id}">
                    <td class="profile-cell">
                        <img src="${profilePhoto}" alt="Profile" class="account-avatar-small" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
                    </td>
                    <td class="name-cell">${account.name}</td>
                    <td class="email-cell">${account.email}</td>
                    <td class="type-cell">
                        <span class="account-type-badge">
                            <i class="${accountTypeIcon}"></i>
                            ${accountTypeLabel}
                        </span>
                    </td>
                    <td class="nic-cell">${account.nic}</td>
                    <td class="date-cell">${new Date(dateField).toLocaleDateString()}</td>
                    <td class="actions-cell">
                        <button class="btn btn-view" data-account-id="${account.id}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                        ${section === 'pending' ? `
                            <button class="btn-verify-small" data-action="verify" data-account-id="${account.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-reject-small" data-action="reject" data-account-id="${account.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : `
                            <button class="btn-revoke-small" data-action="revoke" data-revoke-type="${section}" data-account-id="${account.id}">
                                <i class="fas fa-undo"></i>
                            </button>
                        `}
                    </td>
                </tr>
            `;
        }

        closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
                this.currentUser = null;
            }
        }

        closePhotoViewer() {
            const modal = document.getElementById('photoViewerModal');
            if (modal) {
                modal.style.display = 'none';
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

        async showUserDetails(userId) {
            try {
                console.log('Loading user details for ID:', userId);
                const response = await fetch(`${this.URL_ROOT}/moderator/getUserDetails/${userId}`);
                const data = await response.json();

                if (data.success) {
                    this.currentUser = data.user;
                    this.renderUserDetails(data.user);
                    const modal = document.getElementById('userDetailsModal');
                    if (modal) {
                        modal.style.display = 'flex';
                    }
                } else {
                    window.showNotification('Error loading user details', 'error');
                }
            } catch (error) {
                console.error('Error loading user details:', error);
                window.showNotification('Error loading user details', 'error');
            }
        }

        renderUserDetails(user) {
            const profilePhoto = user.profile_photo ? `${this.UP_ROOT}${user.profile_photo}` : '/test/public/img/default-avatar.png';
            const nicFront = user.nic_front ? `${this.UP_ROOT}${user.nic_front}` : null;
            const nicBack = user.nic_back ? `${this.UP_ROOT}${user.nic_back}` : null;
            const licenseFront = user.license_front ? `${this.UP_ROOT}${user.license_front}` : null;
            const licenseBack = user.license_back ? `${this.UP_ROOT}${user.license_back}` : null;
            const accountTypeIcon = user.account_type === 'guide' ? 'fas fa-map-marked-alt' : 'fas fa-car';
            const accountTypeLabel = user.account_type === 'guide' ? 'Guide' : 'Driver';

            document.getElementById('userDetailsContent').innerHTML = `
                <div class="user-details-grid">
                    <div class="user-profile-section">
                        <img src="${profilePhoto}" alt="Profile" class="user-profile-photo" onclick="accountVerificationManager.viewPhoto('${profilePhoto}')" onerror="this.src='${this.URL_ROOT}/public/img/default-avatar.png'">
                        <h3>${user.name}</h3>
                        <p class="user-account-type">
                            <i class="${accountTypeIcon}"></i>
                            ${accountTypeLabel}
                        </p>
                    </div>
                    <div class="user-info-section">
                        ${(user.rejectionReason || user.rejection_reason) ? `
                        <div class="info-group rejection-reason-group">
                            <h4><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h4>
                            <div class="rejection-reason-content">
                                ${user.rejectionReason || user.rejection_reason}
                            </div>
                        </div>
                        ` : ''}
                        <div class="info-group">
                            <h4>Basic Information</h4>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${user.email}</span>
                            </div>
                            <div class="info-item">
                                <label>NIC:</label>
                                <span>${user.nic_passport || 'Not provided'}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span>${user.phone || 'Not provided'}</span>
                            </div>
                            ${user.secondary_phone ? `
                            <div class="info-item">
                                <label>Secondary Phone:</label>
                                <span>${user.secondary_phone}</span>
                            </div>
                            ` : ''}
                            <div class="info-item">
                                <label>Address:</label>
                                <span>${user.address || 'Not provided'}</span>
                            </div>
                            <div class="info-item">
                                <label>Applied Date:</label>
                                <span>${new Date(user.created_at).toLocaleDateString()}</span>
                            </div>
                            ${(nicFront || nicBack) ? `
                            <div class="documents-section">
                                <label>NIC Documents:</label>
                                <div class="documents-grid">
                                    ${nicFront ? `
                                        <div class="document-item">
                                            <img src="${nicFront}" alt="NIC Front" class="document-photo" onclick="accountVerificationManager.viewPhoto('${nicFront}')">
                                        </div>
                                    ` : ''}
                                    ${nicBack ? `
                                        <div class="document-item">
                                            <img src="${nicBack}" alt="NIC Back" class="document-photo" onclick="accountVerificationManager.viewPhoto('${nicBack}')">
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            ` : ''}
                        </div>

                        ${user.account_type === 'guide' ? `
                            <div class="info-group">
                                <h4>Guide Information</h4>
                                <p class="guide-note">Guide specific information will be displayed here.</p>
                            </div>
                        ` : `
                            <div class="info-group">
                                <h4>Driver Information</h4>
                                ${user.license_number ? `
                                <div class="info-item">
                                    <label>License Number:</label>
                                    <span>${user.license_number}</span>
                                </div>
                                ` : ''}
                                ${user.license_expire_date ? `
                                <div class="info-item">
                                    <label>License Expiry:</label>
                                    <span>${new Date(user.license_expire_date).toLocaleDateString()}</span>
                                </div>
                                ` : ''}
                                ${(licenseFront || licenseBack) ? `
                                <div class="documents-section">
                                    <label>License Documents:</label>
                                    <div class="documents-grid">
                                        ${licenseFront ? `
                                            <div class="document-item">
                                                <img src="${licenseFront}" alt="License Front" class="document-photo" onclick="accountVerificationManager.viewPhoto('${licenseFront}')">
                                            </div>
                                        ` : ''}
                                        ${licenseBack ? `
                                            <div class="document-item">
                                                <img src="${licenseBack}" alt="License Back" class="document-photo" onclick="accountVerificationManager.viewPhoto('${licenseBack}')">
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        `}
                    </div>
                </div>
            `;

            // Update modal buttons based on status
            const verifyBtn = document.getElementById('verifyBtn');
            const rejectBtn = document.getElementById('rejectBtn');

            if (user.status === 'approved') {
                verifyBtn.style.display = 'none';
                rejectBtn.textContent = 'Revoke Verification';
                rejectBtn.className = 'btn btn-warning';
                rejectBtn.onclick = () => this.revokeVerification(user.id);
            } else if (user.status === 'rejected') {
                verifyBtn.style.display = 'none';
                rejectBtn.textContent = 'Revoke Rejection';
                rejectBtn.className = 'btn btn-warning';
                rejectBtn.onclick = () => this.revokeRejection(user.id);
            } else {
                // pending status
                verifyBtn.style.display = 'inline-block';
                rejectBtn.textContent = 'Reject';
                rejectBtn.className = 'btn btn-danger';
                rejectBtn.onclick = () => this.rejectAccount(user.id);
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

        async verifyAccount(userId = null) {
            const id = userId || this.currentUser?.id;
            if (!id) return;

            this.pendingVerifyUserId = id;
            this.showVerifyConfirmModal();
        }

        showVerifyConfirmModal() {
            const modal = document.getElementById('verifyConfirmModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeVerifyConfirmModal() {
            const modal = document.getElementById('verifyConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingVerifyUserId = null;
            }
        }

        async confirmVerifyAccount() {
            if (this.pendingVerifyUserId) {
                const userId = this.pendingVerifyUserId;
                this.closeVerifyConfirmModal();
                this.pendingVerifyUserId = null;
                await this.performVerifyAccount(userId);
            } else {
                window.showNotification('No account selected for verification', 'error');
            }
        }

        async performVerifyAccount(userId) {
            try {
                console.log('Verifying account:', userId);
                const response = await fetch(`${this.URL_ROOT}/moderator/verifyAccount/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Account verified successfully', 'success');
                    this.loadAccounts();
                    this.closeModal(document.getElementById('userDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to verify account');
                }
            } catch (error) {
                console.error('Error verifying account:', error);
                window.showNotification(error.message || 'Error verifying account', 'error');
            }
        }

        async rejectAccount(userId = null) {
            const id = userId || this.currentUser?.id;
            if (!id) return;

            this.pendingRejectUserId = id;
            this.showRejectConfirmModal();
        }

        showRejectConfirmModal() {
            const modal = document.getElementById('rejectConfirmModal');
            if (modal) {
                // Clear previous reason
                const reasonTextarea = document.getElementById('rejectionReason');
                if (reasonTextarea) {
                    reasonTextarea.value = '';
                }
                modal.classList.add('show');
            }
        }

        closeRejectConfirmModal() {
            const modal = document.getElementById('rejectConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRejectUserId = null;
            }
        }

        async confirmRejectAccount() {
            const reasonTextarea = document.getElementById('rejectionReason');
            const reason = reasonTextarea ? reasonTextarea.value.trim() : '';

            if (!reason) {
                window.showNotification('Please provide a rejection reason', 'error');
                reasonTextarea?.focus();
                return;
            }

            if (this.pendingRejectUserId) {
                const userId = this.pendingRejectUserId;
                this.closeRejectConfirmModal();
                this.pendingRejectUserId = null;
                await this.performRejectAccount(userId, reason);
            } else {
                window.showNotification('No account selected for rejection', 'error');
            }
        }

        async performRejectAccount(userId, reason) {
            try {
                console.log('Rejecting account:', userId, 'Reason:', reason);
                const response = await fetch(`${this.URL_ROOT}/moderator/rejectAccount/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Account rejected', 'success');
                    this.loadAccounts();
                    this.closeModal(document.getElementById('userDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to reject account');
                }
            } catch (error) {
                console.error('Error rejecting account:', error);
                window.showNotification(error.message || 'Error rejecting account', 'error');
            }
        }

        // Revoke modal methods
        showRevokeVerificationModal(userId) {
            this.pendingRevokeVerificationUserId = userId;
            const modal = document.getElementById('revokeVerificationModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeVerificationModal() {
            const modal = document.getElementById('revokeVerificationModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeVerificationUserId = null;
            }
        }

        async confirmRevokeVerification() {
            if (this.pendingRevokeVerificationUserId) {
                const userId = this.pendingRevokeVerificationUserId;
                this.closeRevokeVerificationModal();
                this.pendingRevokeVerificationUserId = null;
                await this.performRevokeVerification(userId);
            } else {
                window.showNotification('No account selected for verification revoke', 'error');
            }
        }

        showRevokeRejectionModal(userId) {
            this.pendingRevokeRejectionUserId = userId;
            const modal = document.getElementById('revokeRejectionModal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeRevokeRejectionModal() {
            const modal = document.getElementById('revokeRejectionModal');
            if (modal) {
                modal.classList.remove('show');
                this.pendingRevokeRejectionUserId = null;
            }
        }

        async confirmRevokeRejection() {
            if (this.pendingRevokeRejectionUserId) {
                const userId = this.pendingRevokeRejectionUserId;
                this.closeRevokeRejectionModal();
                this.pendingRevokeRejectionUserId = null;
                await this.performRevokeRejection(userId);
            } else {
                window.showNotification('No account selected for rejection revoke', 'error');
            }
        }

        async revokeVerification(userId) {
            this.showRevokeVerificationModal(userId);
        }

        async performRevokeVerification(userId) {
            try {
                console.log('Revoking verification for account:', userId);
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeVerification/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Verification revoked', 'success');
                    this.loadAccounts();
                    this.closeModal(document.getElementById('userDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to revoke verification');
                }
            } catch (error) {
                console.error('Error revoking verification:', error);
                window.showNotification(error.message || 'Error revoking verification', 'error');
            }
        }

        async revokeRejection(userId) {
            this.showRevokeRejectionModal(userId);
        }

        async performRevokeRejection(userId) {
            try {
                console.log('Revoking rejection for account:', userId);
                const response = await fetch(`${this.URL_ROOT}/moderator/revokeRejection/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.showNotification('Rejection revoked', 'success');
                    this.loadAccounts();
                    this.closeModal(document.getElementById('userDetailsModal'));
                } else {
                    throw new Error(data.message || 'Failed to revoke rejection');
                }
            } catch (error) {
                console.error('Error revoking rejection:', error);
                window.showNotification(error.message || 'Error revoking rejection', 'error');
            }
        }
    }

    window.AccountVerificationManager = AccountVerificationManager;
    window.accountVerificationManager = new AccountVerificationManager();

    // Global functions for onclick handlers
    function closeUserModal() {
        const modal = document.getElementById('userDetailsModal');
        if (modal) {
            modal.style.display = 'none';
            if (window.accountVerificationManager) {
                window.accountVerificationManager.currentUser = null;
            }
        }
    }

    function closePhotoViewer() {
        accountVerificationManager.closePhotoViewer();
    }

})();
