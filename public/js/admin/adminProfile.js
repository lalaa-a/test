(function () {
    if (window.AdminProfileManager) {
        if (window.adminProfileManager) {
            delete window.adminProfileManager;
        }
        delete window.AdminProfileManager;
    }

    class AdminProfileManager {
        constructor() {
            this.init();
        }

        init() {
            this.initializeElements();
            this.attachEventListeners();
        }

        initializeElements() {
            this.navTabs = Array.from(document.querySelectorAll('.nav-tab'));
            this.tabPanes = Array.from(document.querySelectorAll('.tab-pane'));
            this.passwordModal = document.getElementById('passwordModal');
            this.passwordForm = document.getElementById('passwordForm');
            this.openPasswordModalBtn = document.getElementById('openPasswordModalBtn');
            this.openPasswordModalInlineBtn = document.getElementById('openPasswordModalInlineBtn');
            this.closeModalButtons = Array.from(document.querySelectorAll('[data-close-modal]'));
        }

        attachEventListeners() {
            this.navTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    this.activateTab(tab.dataset.tab);
                });
            });

            if (this.openPasswordModalBtn) {
                this.openPasswordModalBtn.addEventListener('click', () => this.openModal(this.passwordModal));
            }

            if (this.openPasswordModalInlineBtn) {
                this.openPasswordModalInlineBtn.addEventListener('click', () => this.openModal(this.passwordModal));
            }

            this.closeModalButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-close-modal');
                    this.closeModal(document.getElementById(modalId));
                });
            });

            if (this.passwordModal) {
                this.passwordModal.addEventListener('click', (event) => {
                    if (event.target === this.passwordModal) {
                        this.closeModal(this.passwordModal);
                    }
                });
            }

            if (this.passwordForm) {
                this.passwordForm.addEventListener('submit', (event) => {
                    event.preventDefault();
                    this.closeModal(this.passwordModal);

                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Password change UI is ready. Backend connection can be added later.', 'info');
                    }
                });
            }
        }

        activateTab(tabName) {
            this.navTabs.forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.tab === tabName);
            });

            this.tabPanes.forEach((pane) => {
                pane.classList.toggle('active', pane.id === `${tabName}-tab`);
            });
        }

        openModal(modal) {
            if (modal) {
                modal.classList.add('show');
            }
        }

        closeModal(modal) {
            if (modal) {
                modal.classList.remove('show');
            }
        }
    }

    window.AdminProfileManager = AdminProfileManager;
    window.adminProfileManager = new AdminProfileManager();
})();
