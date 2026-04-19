(function () {
    if (window.ModeratorManager) {
        if (window.moderatorManager) {
            delete window.moderatorManager;
        }
        delete window.ModeratorManager;
    }

    class ModeratorManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.ACTIVE_WINDOW_DAYS = 30;
            this.moderators = [];
            this.selectedModeratorId = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadModerators();
        }

        bindEvents() {
            document.querySelectorAll('.nav-tab-link').forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    document.querySelectorAll('.nav-tab-link').forEach((item) => {
                        item.classList.remove('active');
                    });
                    link.classList.add('active');
                    this.loadModerators();
                });
            });

            const searchInput = document.getElementById('pendingSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    this.renderModerators(this.getFilteredModerators());
                });
            }

            const statusFilter = document.getElementById('pendingAccountTypeFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', () => {
                    this.renderModerators(this.getFilteredModerators());
                });
            }

            const openAddModeratorBtn = document.getElementById('openAddModeratorModalBtn');
            if (openAddModeratorBtn) {
                openAddModeratorBtn.addEventListener('click', () => {
                    this.openAddModeratorModal();
                });
            }

            const closeAddModeratorBtn = document.getElementById('closeAddModeratorModalBtn');
            if (closeAddModeratorBtn) {
                closeAddModeratorBtn.addEventListener('click', () => {
                    this.closeAddModeratorModal();
                });
            }

            const cancelAddModeratorBtn = document.getElementById('cancelAddModeratorModalBtn');
            if (cancelAddModeratorBtn) {
                cancelAddModeratorBtn.addEventListener('click', () => {
                    this.closeAddModeratorModal();
                });
            }

            const moderatorQuickForm = document.getElementById('moderatorQuickForm');
            if (moderatorQuickForm) {
                moderatorQuickForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    await this.addModerator();
                });
            }

            document.addEventListener('click', (event) => {
                const viewButton = event.target.closest('.btn-view');
                if (viewButton) {
                    const moderatorId = viewButton.dataset.moderatorId;
                    if (moderatorId) {
                        this.openModeratorDetails(moderatorId);
                    }
                    return;
                }

                const closeButton = event.target.closest('[data-close-modal]');
                if (closeButton) {
                    this.closeModal(closeButton.dataset.closeModal);
                    return;
                }

                if (event.target.id === 'openUpdateModeratorConfirmBtn') {
                    this.openUpdateConfirm();
                    return;
                }

                if (event.target.id === 'openDeleteModeratorConfirmBtn') {
                    this.openDeleteConfirm();
                    return;
                }

                if (event.target.id === 'confirmUpdateModeratorBtn') {
                    this.updateModerator();
                    return;
                }

                if (event.target.id === 'confirmDeleteModeratorBtn') {
                    this.deleteModerator();
                }
            });

            document.querySelectorAll('.modal').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        this.closeModal(modal.id);
                    }
                });
            });

            const addModeratorModal = document.getElementById('addModeratorModal');
            if (addModeratorModal) {
                addModeratorModal.addEventListener('click', (event) => {
                    if (event.target === addModeratorModal) {
                        this.closeAddModeratorModal();
                    }
                });
            }
        }

        async loadModerators() {
            try {
                this.updateStatus('Loading moderator records...');
                this.setLoadingState(true);

                const response = await fetch(`${this.URL_ROOT}/Admin/getModerators`);
                const data = await response.json();

                if (!response.ok || !data.success || !Array.isArray(data.moderators)) {
                    throw new Error(data.message || 'Unable to load moderator records.');
                }

                this.moderators = data.moderators;
                this.updateStats();
                this.renderModerators(this.getFilteredModerators());
                this.updateStatus(`Showing ${this.moderators.length} moderator records.`);
            } catch (error) {
                console.error('Error loading moderators:', error);
                this.resetStats();
                this.renderEmptyState(error.message || 'Unable to load moderators right now.');
                this.updateStatus(error.message || 'Unable to load moderator records.');
            } finally {
                this.setLoadingState(false);
            }
        }

        async openModeratorDetails(moderatorId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/Admin/getModerator?id=${encodeURIComponent(moderatorId)}`);
                const data = await response.json();

                if (!response.ok || !data.success || !data.moderator) {
                    throw new Error(data.message || 'Unable to load moderator details.');
                }

                this.selectedModeratorId = String(data.moderator.id);
                this.populateModeratorForm(data.moderator);
                this.openModal('moderatorDetailsModal');
            } catch (error) {
                console.error('Error loading moderator details:', error);
                window.alert(error.message || 'Unable to load moderator details.');
            }
        }

        populateModeratorForm(moderator) {
            document.getElementById('moderatorId').value = moderator.id || '';
            document.getElementById('moderatorFullname').value = moderator.fullname || '';
            document.getElementById('moderatorEmail').value = moderator.email || '';
            document.getElementById('moderatorPhone').value = moderator.phone || '';
            document.getElementById('moderatorSecondaryPhone').value = moderator.secondary_phone || '';
            document.getElementById('moderatorAccountType').value = moderator.account_type || 'site_moderator';
            document.getElementById('moderatorLanguage').value = moderator.language || '';
            document.getElementById('moderatorGender').value = moderator.gender || '';
            document.getElementById('moderatorDob').value = moderator.dob || '';
            document.getElementById('moderatorAddress').value = moderator.address || '';

            document.getElementById('moderatorModalName').textContent = moderator.fullname || 'Moderator';
            document.getElementById('moderatorLastLogin').textContent = this.formatDateTime(moderator.last_login);
            document.getElementById('moderatorCreatedDate').textContent = this.formatDateTime(moderator.created_at);

            const status = this.getModeratorStatus(moderator);
            const statusElement = document.getElementById('moderatorModalStatus');
            statusElement.innerHTML = `<i class="fas ${status.icon}"></i>${status.text}`;
        }

        openUpdateConfirm() {
            const form = document.getElementById('moderatorDetailsForm');
            if (!form || !form.reportValidity()) {
                return;
            }

            this.openModal('updateModeratorConfirmModal');
        }

        openDeleteConfirm() {
            if (!this.selectedModeratorId) {
                return;
            }

            this.openModal('deleteModeratorConfirmModal');
        }

        async updateModerator() {
            try {
                const payload = this.getFormPayload();
                const response = await fetch(`${this.URL_ROOT}/Admin/updateModerator`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to update moderator.');
                }

                this.closeModal('updateModeratorConfirmModal');
                this.closeModal('moderatorDetailsModal');
                await this.loadModerators();
                window.alert(data.message || 'Moderator updated successfully.');
            } catch (error) {
                console.error('Error updating moderator:', error);
                window.alert(error.message || 'Failed to update moderator.');
            }
        }

        openAddModeratorModal() {
            this.clearAddModeratorFeedback();
            document.getElementById('moderatorQuickForm')?.reset();
            const modType = document.getElementById('modType');
            if (modType) {
                modType.value = 'site_moderator';
            }
            const modal = document.getElementById('addModeratorModal');
            if (modal) {
                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
            }
        }

        closeAddModeratorModal() {
            const modal = document.getElementById('addModeratorModal');
            if (modal) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
            this.clearAddModeratorFeedback();
        }

        async addModerator() {
            const form = document.getElementById('moderatorQuickForm');
            if (!form || !form.reportValidity()) {
                return;
            }

            const payload = {
                fullname: document.getElementById('modName')?.value.trim() || '',
                email: document.getElementById('modEmail')?.value.trim() || '',
                phone: document.getElementById('modPhone')?.value.trim() || '',
                secondary_phone: document.getElementById('modSecondaryPhone')?.value.trim() || '',
                language: document.getElementById('modLanguage')?.value || '',
                dob: document.getElementById('modDob')?.value || '',
                gender: document.getElementById('modGender')?.value || '',
                account_type: document.getElementById('modType')?.value || 'site_moderator',
                password: document.getElementById('modPassword')?.value || '',
                address: document.getElementById('modAddress')?.value.trim() || ''
            };

            try {
                this.showAddModeratorFeedback('Saving moderator...', 'success');

                const response = await fetch(`${this.URL_ROOT}/Admin/addModerator`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to add moderator.');
                }

                this.showAddModeratorFeedback(data.message || 'Moderator added successfully.', 'success');
                await this.loadModerators();

                setTimeout(() => {
                    this.closeAddModeratorModal();
                }, 700);
            } catch (error) {
                console.error('Error adding moderator:', error);
                this.showAddModeratorFeedback(error.message || 'Failed to add moderator.', 'error');
            }
        }

        showAddModeratorFeedback(message, type) {
            const feedback = document.getElementById('moderatorModalFeedback');
            if (!feedback) {
                return;
            }

            feedback.textContent = message;
            feedback.className = `feedback is-visible ${type}`;
        }

        clearAddModeratorFeedback() {
            const feedback = document.getElementById('moderatorModalFeedback');
            if (!feedback) {
                return;
            }

            feedback.textContent = '';
            feedback.className = 'feedback';
        }

        async deleteModerator() {
            try {
                const response = await fetch(`${this.URL_ROOT}/Admin/deleteModerator`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: this.selectedModeratorId
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to delete moderator.');
                }

                this.closeModal('deleteModeratorConfirmModal');
                this.closeModal('moderatorDetailsModal');
                this.selectedModeratorId = null;
                await this.loadModerators();
                window.alert(data.message || 'Moderator deleted successfully.');
            } catch (error) {
                console.error('Error deleting moderator:', error);
                window.alert(error.message || 'Failed to delete moderator.');
            }
        }

        getFormPayload() {
            return {
                id: document.getElementById('moderatorId').value,
                fullname: document.getElementById('moderatorFullname').value.trim(),
                email: document.getElementById('moderatorEmail').value.trim(),
                phone: document.getElementById('moderatorPhone').value.trim(),
                secondary_phone: document.getElementById('moderatorSecondaryPhone').value.trim(),
                account_type: document.getElementById('moderatorAccountType').value,
                language: document.getElementById('moderatorLanguage').value.trim(),
                gender: document.getElementById('moderatorGender').value,
                dob: document.getElementById('moderatorDob').value,
                address: document.getElementById('moderatorAddress').value.trim()
            };
        }

        getFilteredModerators() {
            const searchTerm = (document.getElementById('pendingSearchInput')?.value || '').trim().toLowerCase();
            const filterValue = document.getElementById('pendingAccountTypeFilter')?.value || 'all';

            return this.moderators.filter((moderator) => {
                const status = this.getModeratorStatus(moderator).key;
                const matchesFilter = this.matchesStatusFilter(status, filterValue);
                const matchesSearch = !searchTerm || [
                    moderator.fullname,
                    moderator.email,
                    moderator.phone,
                    moderator.secondary_phone
                ].some((value) => (value || '').toLowerCase().includes(searchTerm));

                return matchesFilter && matchesSearch;
            });
        }

        matchesStatusFilter(status, filterValue) {
            if (filterValue === 'all') {
                return true;
            }

            return status === filterValue;
        }

        renderModerators(moderators) {
            const tableBody = this.getTableBody();

            if (!tableBody) {
                return;
            }

            if (!moderators.length) {
                this.renderEmptyState('No moderators found for the current filters.');
                return;
            }

            tableBody.innerHTML = moderators.map((moderator) => {
                const status = this.getModeratorStatus(moderator);

                return `
                    <tr>
                        <td class="moderator-name">${this.escapeHtml(moderator.fullname || 'N/A')}</td>
                        <td class="moderator-email">
                            <a href="mailto:${this.escapeHtml(moderator.email || '')}">
                                ${this.escapeHtml(moderator.email || 'N/A')}
                            </a>
                        </td>
                        <td class="phone-cell">${this.escapeHtml(moderator.phone || moderator.secondary_phone || 'N/A')}</td>
                        <td class="status-cell">
                            <span class="status-badge ${status.className}">
                                <i class="fas ${status.icon}"></i>
                                ${status.text}
                            </span>
                        </td>
                        <td class="date-cell">${this.formatDateTime(moderator.last_login)}</td>
                        <td class="date-cell">${this.formatDateTime(moderator.created_at)}</td>
                        <td class="actions-cell">
                            <button class="btn-view" type="button" data-moderator-id="${this.escapeHtml(String(moderator.id || ''))}">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        renderEmptyState(message) {
            const tableBody = this.getTableBody();

            if (!tableBody) {
                return;
            }

            tableBody.innerHTML = `
                <tr class="no-accounts">
                    <td colspan="7">
                        <i class="fas fa-inbox"></i>
                        <p>${this.escapeHtml(message)}</p>
                    </td>
                </tr>
            `;
        }

        getModeratorStatus(moderator) {
            const isActive = this.isActiveModerator(moderator.last_login);

            return {
                key: isActive ? 'active' : 'inactive',
                text: isActive ? 'Active' : 'Inactive',
                className: isActive ? 'status-active' : 'status-inactive',
                icon: isActive ? 'fa-circle-check' : 'fa-clock'
            };
        }

        updateStats() {
            const stats = this.moderators.reduce((result, moderator) => {
                result.total += 1;

                if (this.isActiveModerator(moderator.last_login)) {
                    result.active += 1;
                } else {
                    result.inactive += 1;
                }

                if (this.isNewModerator(moderator.created_at)) {
                    result.new += 1;
                }

                return result;
            }, {
                total: 0,
                active: 0,
                new: 0,
                inactive: 0
            });

            this.updateStatValue('totalModeratorsCount', stats.total);
            this.updateStatValue('activeModeratorsCount', stats.active);
            this.updateStatValue('newModeratorsCount', stats.new);
            this.updateStatValue('inactiveModeratorsCount', stats.inactive);
        }

        resetStats() {
            this.updateStatValue('totalModeratorsCount', '0');
            this.updateStatValue('activeModeratorsCount', '0');
            this.updateStatValue('newModeratorsCount', '0');
            this.updateStatValue('inactiveModeratorsCount', '0');
        }

        updateStatValue(elementId, value) {
            const element = document.getElementById(elementId);

            if (element) {
                element.textContent = value;
            }
        }

        updateStatus(message) {
            const statusElement = document.getElementById('moderatorStatsStatus');

            if (statusElement) {
                statusElement.textContent = message;
            }
        }

        setLoadingState(isLoading) {
            document.querySelectorAll('.stat-card').forEach((card) => {
                card.classList.toggle('is-loading', isLoading);
            });
        }

        getTableBody() {
            return document.getElementById('moderatorsGrid');
        }

        openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
            }
        }

        isActiveModerator(lastLogin) {
            if (!lastLogin) {
                return false;
            }

            const lastLoginDate = new Date(lastLogin);

            if (Number.isNaN(lastLoginDate.getTime())) {
                return false;
            }

            const activeWindowInMs = this.ACTIVE_WINDOW_DAYS * 24 * 60 * 60 * 1000;
            return Date.now() - lastLoginDate.getTime() <= activeWindowInMs;
        }

        isNewModerator(createdAt) {
            if (!createdAt) {
                return false;
            }

            const createdAtDate = new Date(createdAt);

            if (Number.isNaN(createdAtDate.getTime())) {
                return false;
            }

            const newWindowInMs = this.ACTIVE_WINDOW_DAYS * 24 * 60 * 60 * 1000;
            return Date.now() - createdAtDate.getTime() <= newWindowInMs;
        }

        formatDateTime(value) {
            if (!value) {
                return 'Never';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return 'N/A';
            }

            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    window.ModeratorManager = ModeratorManager;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.moderatorManager = new ModeratorManager();
        });
    } else {
        window.moderatorManager = new ModeratorManager();
    }
})();
