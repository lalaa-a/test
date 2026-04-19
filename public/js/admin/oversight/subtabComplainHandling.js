(function(){
// Complain Handling JavaScript
    if (window.ComplainHandlingManager) {
        console.log('ComplainHandlingManager already exists, cleaning up...');
        if (window.complainHandlingManager) {
            delete window.complainHandlingManager;
        }
        delete window.ComplainHandlingManager;
    }

    // Complain Handling Manager
    class ComplainHandlingManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.currentComplaint = null;
            this.complaints = {
                pending: [],
                in_progress: [],
                completed: []
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadComplaints();
        }

        bindEvents() {
            // Pending section search and filter
            const pendingSearchInput = document.getElementById('pendingSearchInput');
            if (pendingSearchInput) {
                pendingSearchInput.addEventListener('input', (e) => {
                    this.filterComplaints('pending', e.target.value);
                });
            }

            const pendingSubjectFilter = document.getElementById('pendingSubjectFilter');
            if (pendingSubjectFilter) {
                pendingSubjectFilter.addEventListener('change', () => {
                    this.filterComplaints('pending');
                });
            }

            // In Progress section search and filter
            const inProgressSearchInput = document.getElementById('inProgressSearchInput');
            if (inProgressSearchInput) {
                inProgressSearchInput.addEventListener('input', (e) => {
                    this.filterComplaints('in_progress', e.target.value);
                });
            }

            const inProgressSubjectFilter = document.getElementById('inProgressSubjectFilter');
            if (inProgressSubjectFilter) {
                inProgressSubjectFilter.addEventListener('change', () => {
                    this.filterComplaints('in_progress');
                });
            }

            // Completed section search and filter
            const completedSearchInput = document.getElementById('completedSearchInput');
            if (completedSearchInput) {
                completedSearchInput.addEventListener('input', (e) => {
                    this.filterComplaints('completed', e.target.value);
                });
            }

            const completedSubjectFilter = document.getElementById('completedSubjectFilter');
            if (completedSubjectFilter) {
                completedSubjectFilter.addEventListener('change', () => {
                    this.filterComplaints('completed');
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
            const closeBtn = document.querySelector('#complaintDetailsModal .modal-footer .btn-secondary');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(document.getElementById('complaintDetailsModal')));
            }

            // ESC key to close modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal(document.getElementById('complaintDetailsModal'));
                }
            });

            // Action buttons (View, Start Handling, Mark Completed)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btn-view')) {
                    const button = e.target.closest('.btn-view');
                    const complaintId = button.dataset.complaintId;
                    if (complaintId) {
                        this.showComplaintDetails(complaintId);
                    }
                }

                if (e.target.closest('.btn-start')) {
                    const button = e.target.closest('.btn-start');
                    const complaintId = button.dataset.complaintId;
                    if (complaintId) {
                        this.startHandling(complaintId);
                    }
                }

                if (e.target.closest('.btn-complete')) {
                    const button = e.target.closest('.btn-complete');
                    const complaintId = button.dataset.complaintId;
                    if (complaintId) {
                        this.markCompleted(complaintId);
                    }
                }
            });
        }

        async loadComplaints() {
            try {
                const response = await fetch(`${this.URL_ROOT}/admin/getAllComplaints`);
                const data = await response.json();

                if (data.success) {
                    this.complaints = data.complaints;
                    this.updateStats();
                    this.renderComplaints();
                } else {
                    console.error('Failed to load complaints:', data.message);
                }
            } catch (error) {
                console.error('Error loading complaints:', error);
            }
        }

        updateStats() {
            const pendingCount = this.complaints.pending ? this.complaints.pending.length : 0;
            const inProgressCount = this.complaints.in_progress ? this.complaints.in_progress.length : 0;
            const completedCount = this.complaints.completed ? this.complaints.completed.length : 0;

            document.getElementById('pendingComplaintsCount').textContent = pendingCount;
            document.getElementById('inProgressComplaintsCount').textContent = inProgressCount;
            document.getElementById('completedComplaintsCount').textContent = completedCount;
        }

        renderComplaints() {
            this.renderComplaintsSection('pending', this.complaints.pending || []);
            this.renderComplaintsSection('in_progress', this.complaints.in_progress || []);
            this.renderComplaintsSection('completed', this.complaints.completed || []);
        }

        renderComplaintsSection(status, complaints) {
            const containerId = status === 'in_progress' ? 'inProgressComplaintsGrid' : `${status}ComplaintsGrid`;
            const container = document.getElementById(containerId);

            if (!container) return;

            if (!complaints || complaints.length === 0) {
                const noComplaintsHtml = status === 'in_progress' ?
                    `<tr class="no-complaints">
                        <td colspan="6">
                            <i class="fas fa-cog"></i>
                            <p>No complaints in progress</p>
                        </td>
                    </tr>` :
                    `<tr class="no-complaints">
                        <td colspan="${status === 'pending' ? '5' : '6'}">
                            <i class="fas fa-${status === 'completed' ? 'check-circle' : 'inbox'}"></i>
                            <p>No ${status === 'completed' ? 'completed' : status === 'pending' ? 'pending' : 'in progress'} complaints</p>
                        </td>
                    </tr>`;

                container.innerHTML = noComplaintsHtml;
                return;
            }

            container.innerHTML = complaints.map(complaint => this.renderComplaintRow(complaint, status)).join('');
        }

        renderComplaintRow(complaint, status) {
            const subjectLabels = {
                'booking': 'Booking Issue',
                'payment': 'Payment Problem',
                'trip': 'Trip Experience',
                'guide_driver': 'Guide / Driver Concern',
                'account': 'Account Help',
                'feature': 'Feature Suggestion',
                'other': 'Other'
            };

            const subjectLabel = subjectLabels[complaint.subject] || complaint.subject;

            if (status === 'pending') {
                return `
                    <tr>
                        <td>
                            <div class="complaint-user">
                                <img src="${complaint.userAvatar || '/test/public/img/default-avatar.png'}" alt="User" class="user-avatar">
                                <div class="user-info">
                                    <p class="user-name">${this.escapeHtml(complaint.userName)}</p>
                                    <p class="user-email">${this.escapeHtml(complaint.userEmail)}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="complaint-subject">${this.escapeHtml(subjectLabel)}</p>
                        </td>
                        <td>
                            <p class="complaint-message">${this.escapeHtml(complaint.message)}</p>
                        </td>
                        <td>
                            <span class="complaint-date">${this.formatDate(complaint.createdAt)}</span>
                        </td>
                        <td>
                            <div class="complaint-actions">
                                <button class="btn-view" data-complaint-id="${complaint.problemId}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-start" data-complaint-id="${complaint.problemId}">
                                    <i class="fas fa-play"></i> Start
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            } else if (status === 'in_progress') {
                return `
                    <tr>
                        <td>
                            <div class="complaint-user">
                                <img src="${complaint.userAvatar || '/test/public/img/default-avatar.png'}" alt="User" class="user-avatar">
                                <div class="user-info">
                                    <p class="user-name">${this.escapeHtml(complaint.userName)}</p>
                                    <p class="user-email">${this.escapeHtml(complaint.userEmail)}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="complaint-subject">${this.escapeHtml(subjectLabel)}</p>
                        </td>
                        <td>
                            <p class="complaint-message">${this.escapeHtml(complaint.message)}</p>
                        </td>
                        <td>
                            <span class="complaint-handler">${this.escapeHtml(complaint.handlerName || 'Unknown')}</span>
                        </td>
                        <td>
                            <span class="complaint-date">${this.formatDate(complaint.updatedAt)}</span>
                        </td>
                        <td>
                            <div class="complaint-actions">
                                <button class="btn-view" data-complaint-id="${complaint.problemId}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-complete" data-complaint-id="${complaint.problemId}">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            } else if (status === 'completed') {
                return `
                    <tr>
                        <td>
                            <div class="complaint-user">
                                <img src="${complaint.userAvatar || '/test/public/img/default-avatar.png'}" alt="User" class="user-avatar">
                                <div class="user-info">
                                    <p class="user-name">${this.escapeHtml(complaint.userName)}</p>
                                    <p class="user-email">${this.escapeHtml(complaint.userEmail)}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="complaint-subject">${this.escapeHtml(subjectLabel)}</p>
                        </td>
                        <td>
                            <p class="complaint-message">${this.escapeHtml(complaint.message)}</p>
                        </td>
                        <td>
                            <span class="complaint-handler">${this.escapeHtml(complaint.handlerName || 'Unknown')}</span>
                        </td>
                        <td>
                            <span class="complaint-date">${this.formatDate(complaint.completedAt)}</span>
                        </td>
                        <td>
                            <div class="complaint-actions">
                                <button class="btn-view" data-complaint-id="${complaint.problemId}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        filterComplaints(status, searchTerm = '') {
            const subjectFilter = document.getElementById(`${status === 'in_progress' ? 'inProgress' : status}SubjectFilter`).value;
            const originalComplaints = this.complaints[status] || [];

            let filtered = originalComplaints;

            // Apply search filter
            if (searchTerm) {
                filtered = filtered.filter(complaint =>
                    complaint.userName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    complaint.userEmail.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    complaint.message.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    complaint.subject.toLowerCase().includes(searchTerm.toLowerCase())
                );
            }

            // Apply subject filter
            if (subjectFilter && subjectFilter !== 'all') {
                filtered = filtered.filter(complaint => complaint.subject === subjectFilter);
            }

            this.renderComplaintsSection(status, filtered);
        }

        switchToTab(targetId) {
            // Hide all sections
            document.querySelectorAll('.complain-section').forEach(section => {
                section.style.display = 'none';
            });

            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Show target section
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }

            // Add active class to clicked nav link
            const activeLink = document.querySelector(`[href="#${targetId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        async showComplaintDetails(complaintId) {
            try {
                const response = await fetch(`${this.URL_ROOT}/admin/getComplaintDetails?complaintId=${complaintId}`);
                const data = await response.json();

                if (data.success) {
                    this.currentComplaint = data.complaint;
                    this.renderComplaintModal(data.complaint);
                    this.openModal(document.getElementById('complaintDetailsModal'));
                } else {
                    alert('Failed to load complaint details: ' + data.message);
                }
            } catch (error) {
                console.error('Error loading complaint details:', error);
                alert('Error loading complaint details');
            }
        }

        renderComplaintModal(complaint) {
            const subjectLabels = {
                'booking': 'Booking Issue',
                'payment': 'Payment Problem',
                'trip': 'Trip Experience',
                'guide_driver': 'Guide / Driver Concern',
                'account': 'Account Help',
                'feature': 'Feature Suggestion',
                'other': 'Other'
            };

            const statusLabels = {
                'pending': 'Pending',
                'in_progress': 'In Progress',
                'completed': 'Completed'
            };

            const modalBody = document.getElementById('complaintDetailsContent');
            modalBody.innerHTML = `
                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">User Information</div>
                    <div class="complaint-detail-value">
                        <strong>${this.escapeHtml(complaint.userName)}</strong><br>
                        ${this.escapeHtml(complaint.userEmail)}
                    </div>
                </div>

                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Subject</div>
                    <div class="complaint-detail-value">${this.escapeHtml(subjectLabels[complaint.subject] || complaint.subject)}</div>
                </div>

                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Status</div>
                    <div class="complaint-detail-value">${statusLabels[complaint.status] || complaint.status}</div>
                </div>

                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Submitted Date</div>
                    <div class="complaint-detail-value">${this.formatDate(complaint.createdAt)}</div>
                </div>

                ${complaint.status !== 'pending' ? `
                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Handled By</div>
                    <div class="complaint-detail-value">${this.escapeHtml(complaint.handlerName || 'Unknown')}</div>
                </div>
                ` : ''}

                ${complaint.status === 'completed' ? `
                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Completed Date</div>
                    <div class="complaint-detail-value">${this.formatDate(complaint.completedAt)}</div>
                </div>
                ` : ''}

                <div class="complaint-detail-item">
                    <div class="complaint-detail-label">Message</div>
                    <div class="complaint-detail-value complaint-message-full">${this.escapeHtml(complaint.message)}</div>
                </div>
            `;

            // Update modal footer buttons based on status
            const startBtn = document.getElementById('startHandlingBtn');
            const completeBtn = document.getElementById('markCompletedBtn');

            if (complaint.status === 'pending') {
                startBtn.style.display = 'inline-block';
                completeBtn.style.display = 'none';
            } else if (complaint.status === 'in_progress') {
                startBtn.style.display = 'none';
                completeBtn.style.display = 'inline-block';
            } else {
                startBtn.style.display = 'none';
                completeBtn.style.display = 'none';
            }
        }

        async startHandling(complaintId = null) {
            const id = complaintId || (this.currentComplaint ? this.currentComplaint.problemId : null);
            if (!id) return;

            try {
                const response = await fetch(`${this.URL_ROOT}/admin/startComplaintHandling`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ complaintId: id })
                });

                const data = await response.json();

                if (data.success) {
                    this.closeModal(document.getElementById('complaintDetailsModal'));
                    this.loadComplaints(); // Refresh the data
                    alert('Complaint handling started successfully');
                } else {
                    alert('Failed to start handling: ' + data.message);
                }
            } catch (error) {
                console.error('Error starting complaint handling:', error);
                alert('Error starting complaint handling');
            }
        }

        async markCompleted(complaintId = null) {
            const id = complaintId || (this.currentComplaint ? this.currentComplaint.problemId : null);
            if (!id) return;

            try {
                const response = await fetch(`${this.URL_ROOT}/admin/markComplaintCompleted`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ complaintId: id })
                });

                const data = await response.json();

                if (data.success) {
                    this.closeModal(document.getElementById('complaintDetailsModal'));
                    this.loadComplaints(); // Refresh the data
                    alert('Complaint marked as completed successfully');
                } else {
                    alert('Failed to mark as completed: ' + data.message);
                }
            } catch (error) {
                console.error('Error marking complaint as completed:', error);
                alert('Error marking complaint as completed');
            }
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
            this.currentComplaint = null;
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : String(text);
            return div.innerHTML;
        }

        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString([], {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    // Initialize the manager
    window.ComplainHandlingManager = ComplainHandlingManager;
    window.complainHandlingManager = new ComplainHandlingManager();

    // Global functions for modal buttons
    window.closeComplaintModal = function() {
        if (window.complainHandlingManager) {
            window.complainHandlingManager.closeModal(document.getElementById('complaintDetailsModal'));
        }
    };
})();