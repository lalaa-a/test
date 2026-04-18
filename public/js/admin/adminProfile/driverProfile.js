(function() {
    if (window.DriverProfileManager) {
        if (window.driverProfileManager) {
            delete window.driverProfileManager;
        }
        delete window.DriverProfileManager;
    }

    class DriverProfileManager {
        constructor() {
            this.currentSection = null;
            this.URL_ROOT = this.getUrlRoot();
            this.init();
        }

        getUrlRoot() {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const appSegment = pathParts[0] ? `/${pathParts[0]}` : '';
            return `${window.location.origin}${appSegment}`;
        }

        init() {
            this.initializeElements();
            this.attachEventListeners();
            this.initializeViewStates();
        }

        initializeElements() {
            this.personalInfoView = document.getElementById('personalInfoView');
            this.personalForm = document.getElementById('personalForm');
            this.editPersonalBtn = document.getElementById('editPersonalBtn');
            this.cancelPersonalBtn = document.getElementById('cancelPersonalBtn');
            this.savePersonalBtn = document.getElementById('savePersonalBtn');
            this.changeEmailBtn = document.getElementById('changeEmailBtn');
            this.emailChangeModal = document.getElementById('emailChangeModal');
            this.emailChangeForm = document.getElementById('emailChangeForm');
            this.sendVerificationBtn = document.getElementById('sendVerificationBtn');
            this.emailOtpSection = document.getElementById('emailOtpSection');
            this.emailOTPInput = document.getElementById('emailOTP');
            this.otpEmailTarget = document.getElementById('otpEmailTarget');
            this.verifyOtpBtn = document.getElementById('verifyOtpBtn');
            this.resendOtpBtn = document.getElementById('resendOtpBtn');
            this.cancelOtpBtn = document.getElementById('cancelOtpBtn');
            this.currentEmailInput = document.getElementById('currentEmail');
            this.newEmailInput = document.getElementById('newEmail');
            this.confirmNewEmailInput = document.getElementById('confirmNewEmail');
            this.passwordInput = document.getElementById('password');
            this.currentEmailDisplay = document.getElementById('currentEmailDisplay');
            this.displayFullName = document.getElementById('displayFullName');
            this.displayEmail = document.getElementById('displayEmail');
        }

        attachEventListeners() {
            if (this.editPersonalBtn) {
                this.editPersonalBtn.addEventListener('click', () => this.togglePersonalInfo());
            }

            if (this.cancelPersonalBtn) {
                this.cancelPersonalBtn.addEventListener('click', () => this.cancelEdit());
            }

            if (this.savePersonalBtn) {
                this.savePersonalBtn.addEventListener('click', () => this.savePersonalInfo());
            }

            if (this.personalForm) {
                this.personalForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.savePersonalInfo();
                });
            }

            if (this.changeEmailBtn) {
                this.changeEmailBtn.addEventListener('click', () => {
                    this.resetEmailChangeModal();
                    this.showModal(this.emailChangeModal);
                });
            }

            if (this.sendVerificationBtn) {
                this.sendVerificationBtn.addEventListener('click', () => this.sendEmailVerification());
            }

            if (this.verifyOtpBtn) {
                this.verifyOtpBtn.addEventListener('click', () => this.verifyEmailOTP());
            }

            if (this.resendOtpBtn) {
                this.resendOtpBtn.addEventListener('click', () => this.sendEmailVerification(true));
            }

            if (this.cancelOtpBtn) {
                this.cancelOtpBtn.addEventListener('click', () => this.resetEmailChangeModal());
            }

            document.querySelectorAll('.modal-close').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = button.closest('.modal');
                    this.hideModal(modal);
                    if (modal === this.emailChangeModal) {
                        this.resetEmailChangeModal();
                    }
                });
            });

            document.querySelectorAll('.modal').forEach((modal) => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.hideModal(modal);
                        if (modal === this.emailChangeModal) {
                            this.resetEmailChangeModal();
                        }
                    }
                });
            });

            document.querySelectorAll('.modal .btn-cancel').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = button.closest('.modal');
                    if (modal) {
                        this.hideModal(modal);
                        if (modal === this.emailChangeModal) {
                            this.resetEmailChangeModal();
                        }
                    }
                });
            });

            if (this.emailOTPInput) {
                this.emailOTPInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.verifyEmailOTP();
                    }
                });
            }
        }

        initializeViewStates() {
            if (this.personalInfoView && this.personalForm) {
                this.personalInfoView.style.display = 'block';
                this.personalForm.style.display = 'none';
            }

            if (this.editPersonalBtn) {
                this.editPersonalBtn.style.display = 'inline-flex';
            }

            if (this.cancelPersonalBtn) {
                this.cancelPersonalBtn.style.display = 'none';
            }

            if (this.savePersonalBtn) {
                this.savePersonalBtn.style.display = 'none';
            }
        }

        togglePersonalInfo() {
            const isEditing = this.personalForm && this.personalForm.style.display !== 'none';

            if (isEditing) {
                this.personalInfoView.style.display = 'block';
                this.personalForm.style.display = 'none';
                this.editPersonalBtn.style.display = 'inline-flex';
                this.cancelPersonalBtn.style.display = 'none';
                this.savePersonalBtn.style.display = 'none';
                this.currentSection = null;
                return;
            }

            this.personalInfoView.style.display = 'none';
            this.personalForm.style.display = 'block';
            this.editPersonalBtn.style.display = 'none';
            this.cancelPersonalBtn.style.display = 'inline-flex';
            this.savePersonalBtn.style.display = 'inline-flex';
            this.currentSection = 'personal';
        }

        cancelEdit() {
            if (this.currentSection === 'personal') {
                this.togglePersonalInfo();
            }
        }

        savePersonalInfo() {
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');

            if (this.displayFullName && fullNameInput) {
                this.displayFullName.textContent = fullNameInput.value || '-';
            }

            if (this.displayEmail && emailInput) {
                this.displayEmail.textContent = emailInput.value || '-';
            }

            const currentEmailDisplay = document.getElementById('currentEmailDisplay');
            if (currentEmailDisplay && emailInput) {
                currentEmailDisplay.textContent = emailInput.value || '-';
            }

            this.togglePersonalInfo();
            this.notify('Profile details updated in the page UI.', 'success');
        }

        showModal(modal) {
            if (modal) {
                modal.classList.add('show');
            }
        }

        hideModal(modal) {
            if (modal) {
                modal.classList.remove('show');
            }
        }

        async sendEmailVerification(isResend = false) {
            const payload = {
                currentEmail: this.currentEmailInput ? this.currentEmailInput.value.trim() : '',
                newEmail: this.newEmailInput ? this.newEmailInput.value.trim() : '',
                confirmNewEmail: this.confirmNewEmailInput ? this.confirmNewEmailInput.value.trim() : '',
                password: this.passwordInput ? this.passwordInput.value : ''
            };

            if (!payload.currentEmail || !payload.newEmail || !payload.confirmNewEmail || !payload.password) {
                this.notify('All fields are required.', 'error');
                return;
            }

            if (payload.newEmail !== payload.confirmNewEmail) {
                this.notify('New email addresses do not match.', 'error');
                return;
            }

            if (payload.currentEmail === payload.newEmail) {
                this.notify('New email must be different from the current email.', 'error');
                return;
            }

            const button = isResend ? this.resendOtpBtn : this.sendVerificationBtn;
            const originalText = button ? button.textContent : '';

            if (button) {
                button.disabled = true;
                button.textContent = isResend ? 'Sending...' : 'Sending...';
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/admin/sendEmailChangeOTP`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!result.success) {
                    this.notify(result.message || 'Failed to send verification code.', 'error');
                    return;
                }

                if (this.otpEmailTarget) {
                    this.otpEmailTarget.textContent = payload.newEmail;
                }

                if (this.emailChangeForm) {
                    this.emailChangeForm.style.display = 'none';
                }

                if (this.emailOtpSection) {
                    this.emailOtpSection.style.display = 'block';
                }

                if (this.emailOTPInput) {
                    this.emailOTPInput.value = '';
                    this.emailOTPInput.focus();
                }

                this.notify(result.message || 'Verification code sent successfully.', 'success');
            } catch (error) {
                this.notify('An error occurred while sending the verification code.', 'error');
            } finally {
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            }
        }

        async verifyEmailOTP() {
            const otp = this.emailOTPInput ? this.emailOTPInput.value.trim() : '';

            if (!otp) {
                this.notify('Please enter the verification code.', 'error');
                return;
            }

            if (this.verifyOtpBtn) {
                this.verifyOtpBtn.disabled = true;
                this.verifyOtpBtn.textContent = 'Verifying...';
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/admin/verifyEmailChangeOTP`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ otp })
                });

                const result = await response.json();

                if (!result.success) {
                    this.notify(result.message || 'Failed to verify OTP.', 'error');
                    return;
                }

                const newEmail = result.new_email || (this.newEmailInput ? this.newEmailInput.value.trim() : '');

                if (this.currentEmailDisplay) {
                    this.currentEmailDisplay.textContent = newEmail;
                }

                if (this.displayEmail) {
                    this.displayEmail.textContent = newEmail;
                }

                if (this.currentEmailInput) {
                    this.currentEmailInput.value = newEmail;
                }

                const mainEmailInput = document.getElementById('email');
                if (mainEmailInput) {
                    mainEmailInput.value = newEmail;
                }

                this.notify(result.message || 'Email updated successfully.', 'success');
                this.hideModal(this.emailChangeModal);
                this.resetEmailChangeModal();
            } catch (error) {
                this.notify('An error occurred while verifying the code.', 'error');
            } finally {
                if (this.verifyOtpBtn) {
                    this.verifyOtpBtn.disabled = false;
                    this.verifyOtpBtn.textContent = 'Verify Email';
                }
            }
        }

        resetEmailChangeModal() {
            if (this.emailChangeForm) {
                this.emailChangeForm.style.display = 'block';
            }

            if (this.emailOtpSection) {
                this.emailOtpSection.style.display = 'none';
            }

            if (this.currentEmailInput && this.currentEmailDisplay) {
                this.currentEmailInput.value = this.currentEmailDisplay.textContent.trim();
            }

            if (this.newEmailInput) {
                this.newEmailInput.value = '';
            }

            if (this.confirmNewEmailInput) {
                this.confirmNewEmailInput.value = '';
            }

            if (this.passwordInput) {
                this.passwordInput.value = '';
            }

            if (this.emailOTPInput) {
                this.emailOTPInput.value = '';
            }
        }

        notify(message, type) {
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            }
        }
    }

    window.DriverProfileManager = DriverProfileManager;
    window.driverProfileManager = new DriverProfileManager();
})();
