// ===== Quick Problems Center - JavaScript =====

(function () {

    const URL_ROOT = document.querySelector('meta[name="url-root"]')?.content
        || window.location.origin + '/test';

    let currentFilter = 'all';
    let problemsData = [];

    // ---- Init ----
    loadProblems();

    // ---- Filter Tabs ----
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            loadProblems();
        });
    });

    // ---- Refresh ----
    const refreshBtn = document.getElementById('refreshProblemsBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            this.querySelector('i').style.transform = 'rotate(360deg)';
            loadProblems();
            setTimeout(() => {
                this.querySelector('i').style.transform = '';
            }, 600);
        });
    }

    // ---- Close Modal ----
    const closeModalBtn = document.getElementById('closeModalBtn');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    const modalOverlay = document.getElementById('problemModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    // ---- Load Problems ----
    async function loadProblems() {
        const listEl = document.getElementById('problemsList');

        // Show loading
        listEl.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Loading problems...</p>
            </div>
        `;

        try {
            const response = await fetch(`${URL_ROOT}/moderator/getProblems?filter=${currentFilter}`);
            const data = await response.json();

            if (data.success) {
                problemsData = data.problems;
                updateCounts(data.counts);
                renderProblems(data.problems);
            } else {
                showToast('Failed to load problems', 'error');
                listEl.innerHTML = '';
            }
        } catch (error) {
            console.error('Error loading problems:', error);
            showToast('Network error occurred', 'error');
            listEl.innerHTML = '';
        }
    }

    // ---- Update Counts ----
    function updateCounts(counts) {
        if (!counts) return;
        const pending = document.getElementById('pendingCount');
        const completed = document.getElementById('completedCount');
        const total = document.getElementById('totalCount');

        if (pending) pending.textContent = counts.pending || 0;
        if (completed) completed.textContent = counts.completed || 0;
        if (total) total.textContent = counts.total || 0;
    }

    // ---- Render Problems ----
    function renderProblems(problems) {
        const listEl = document.getElementById('problemsList');
        listEl.innerHTML = '';

        if (!problems || problems.length === 0) {
            listEl.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox fa-3x"></i>
                    <h3>No issues found</h3>
                    <p>There are no user problems to display right now.</p>
                </div>
            `;
            return;
        }

        problems.forEach(problem => {
            const card = createProblemCard(problem);
            listEl.appendChild(card);
        });
    }

    // ---- Create Problem Card ----
    function createProblemCard(problem) {
        problem.status = problem.status || 'pending';
        problem.createdAt = problem.createdAt || new Date().toISOString();

        const card = document.createElement('div');
        card.className = `problem-card status-${problem.status}`;
        card.setAttribute('data-problem-id', problem.problemId);

        const avatarTypeClass = getAvatarTypeClass(problem.account_type);
        const initial = problem.fullname ? problem.fullname.charAt(0).toUpperCase() : '?';
        const userTypeLabel = formatAccountType(problem.account_type);
        const subjectLabel = formatSubject(problem.subject);
        const timeAgo = getTimeAgo(problem.createdAt);

        card.innerHTML = `
            <div class="problem-user-avatar ${avatarTypeClass}">${initial}</div>
            <div class="problem-card-content">
                <div class="problem-card-top">
                    <span class="problem-user-name">${escapeHtml(problem.fullname)}</span>
                    <span class="problem-user-type ${avatarTypeClass}">${userTypeLabel}</span>
                </div>
                <div class="problem-subject-badge">
                    <i class="fas fa-tag"></i> ${subjectLabel}
                </div>
                <div class="problem-message-preview">${escapeHtml(problem.message)}</div>
                <div class="problem-card-meta">
                    <span class="problem-meta-item">
                        <i class="fas fa-envelope"></i> ${escapeHtml(problem.email)}
                    </span>
                    <span class="problem-meta-item">
                        <i class="fas fa-phone"></i> ${escapeHtml(problem.phone)}
                    </span>
                    <span class="problem-meta-item">
                        <i class="fas fa-clock"></i> ${timeAgo}
                    </span>
                </div>
            </div>
            <div class="problem-card-actions">
                <span class="problem-status-badge status-${problem.status}">
                    ${problem.status === 'completed' ? '<i class="fas fa-check"></i> ' : '<i class="fas fa-clock"></i> '}
                    ${problem.status.replace('_', ' ')}
                </span>
                ${problem.status !== 'completed'
                ? `<button class="complete-btn" data-id="${problem.problemId}" onclick="event.stopPropagation();">
                        <i class="fas fa-check-circle"></i> Complete
                       </button>`
                : `<span class="completed-label">
                        <i class="fas fa-user-check"></i> ${problem.completedByName ? escapeHtml(problem.completedByName) : 'Moderator'}
                       </span>`
            }
                <button class="delete-btn" data-id="${problem.problemId}" style="margin-left: 8px; background: none; border: none; color: #ff6b6b; cursor: pointer;" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Card click -> open modal
        card.addEventListener('click', function () {
            openModal(problem);
        });

        // Complete button click
        const completeBtn = card.querySelector('.complete-btn');
        if (completeBtn) {
            completeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                markComplete(problem.problemId);
            });
        }

        const deleteBtn = card.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                deleteProblem(problem.problemId);
            });
        }

        return card;
    }

    // ---- Open Modal ----
    function openModal(problem) {
        problem.status = problem.status || 'pending';
        problem.createdAt = problem.createdAt || new Date().toISOString();

        const modal = document.getElementById('problemModal');
        const body = document.getElementById('modalBody');
        const footer = document.getElementById('modalFooter');

        const avatarTypeClass = getAvatarTypeClass(problem.account_type);
        const initial = problem.fullname ? problem.fullname.charAt(0).toUpperCase() : '?';
        const userTypeLabel = formatAccountType(problem.account_type);
        const subjectLabel = formatSubject(problem.subject);
        const formattedDate = new Date(problem.createdAt).toLocaleString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        body.innerHTML = `
            <div class="modal-user-section">
                <div class="modal-user-avatar ${avatarTypeClass}">${initial}</div>
                <div class="modal-user-details">
                    <div class="modal-user-name">${escapeHtml(problem.fullname)}</div>
                    <div class="modal-user-contact">
                        <div class="modal-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:${escapeHtml(problem.email)}">${escapeHtml(problem.email)}</a>
                        </div>
                        <div class="modal-contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:${escapeHtml(problem.phone)}">${escapeHtml(problem.phone)}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-problem-section">
                <div class="modal-section-label">Subject</div>
                <div class="modal-subject">${subjectLabel}</div>

                <div class="modal-section-label">Message</div>
                <div class="modal-message">${escapeHtml(problem.message)}</div>
            </div>

            <div class="modal-meta-row">
                <div class="modal-meta-badge">
                    <i class="fas fa-user-tag"></i> ${userTypeLabel}
                </div>
                <div class="modal-meta-badge">
                    <i class="fas fa-calendar"></i> ${formattedDate}
                </div>
                <div class="modal-meta-badge">
                    <i class="fas fa-info-circle"></i> ${problem.status.replace('_', ' ')}
                </div>
                ${problem.status === 'completed' && problem.completedByName
                ? `<div class="modal-meta-badge">
                        <i class="fas fa-user-check"></i> Fixed by: ${escapeHtml(problem.completedByName)}
                       </div>`
                : ''
            }
                ${problem.status === 'completed' && problem.completedAt
                ? `<div class="modal-meta-badge">
                        <i class="fas fa-check-double"></i> ${new Date(problem.completedAt).toLocaleString('en-US', {
                    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                })}
                       </div>`
                : ''
            }
            </div>
        `;

        if (problem.status !== 'completed') {
            footer.innerHTML = `
                <button class="modal-btn modal-btn-delete" id="modalDeleteBtn" data-id="${problem.problemId}" style="background-color: #ff6b6b; color: white;">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="modal-btn modal-btn-close" onclick="document.getElementById('problemModal').style.display='none';">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="modal-btn modal-btn-complete" id="modalCompleteBtn" data-id="${problem.problemId}">
                    <i class="fas fa-check-circle"></i> Mark as Completed
                </button>
            `;

            const modalCompleteBtn = document.getElementById('modalCompleteBtn');
            if (modalCompleteBtn) {
                modalCompleteBtn.addEventListener('click', function () {
                    markComplete(this.dataset.id);
                });
            }
        } else {
            footer.innerHTML = `
                <button class="modal-btn modal-btn-delete" id="modalDeleteBtn" data-id="${problem.problemId}" style="background-color: #ff6b6b; color: white;">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="modal-btn modal-btn-close" onclick="document.getElementById('problemModal').style.display='none';">
                    <i class="fas fa-times"></i> Close
                </button>
            `;
        }

        const modalDeleteBtn = document.getElementById('modalDeleteBtn');
        if (modalDeleteBtn) {
            modalDeleteBtn.addEventListener('click', function () {
                deleteProblem(this.dataset.id);
            });
        }

        modal.style.display = 'flex';
    }

    // ---- Close Modal ----
    function closeModal() {
        const modal = document.getElementById('problemModal');
        if (modal) modal.style.display = 'none';
    }

    // ---- Mark Complete ----
    async function markComplete(problemId) {
        if (!confirm('✅ Are you sure you want to mark this issue as completed?')) return;

        try {
            const response = await fetch(`${URL_ROOT}/moderator/completeProblem`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ problemId: problemId })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Problem marked as completed!', 'success');
                closeModal();
                loadProblems();
            } else {
                showToast(data.message || 'Failed to complete problem', 'error');
            }
        } catch (error) {
            console.error('Error completing problem:', error);
            showToast('Network error occurred', 'error');
        }
    }

    // ---- Delete Problem ----
    async function deleteProblem(problemId) {
        if (!confirm('🗑️ Are you sure you want to delete this problem? This action cannot be undone.')) return;

        try {
            const response = await fetch(`${URL_ROOT}/moderator/deleteProblem`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ problemId: problemId })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Problem deleted successfully!', 'success');
                closeModal();
                loadProblems();
            } else {
                showToast(data.message || 'Failed to delete problem', 'error');
            }
        } catch (error) {
            console.error('Error deleting problem:', error);
            showToast('Network error occurred', 'error');
        }
    }

    // ---- Helper: Format Account Type ----
    function formatAccountType(type) {
        const types = {
            'tourist': 'Traveller',
            'driver': 'Driver',
            'guide': 'Guide',
            'admin': 'Admin',
            'site_moderator': 'Moderator',
            'business_manager': 'Business Manager'
        };
        return types[type] || type || 'Unknown';
    }

    // ---- Helper: Format Subject ----
    function formatSubject(subject) {
        const subjects = {
            'booking': 'Booking Issues',
            'payment': 'Payment Problems',
            'account': 'Account Help',
            'complaint': 'File a Complaint',
            'other': 'Other'
        };
        return subjects[subject] || subject || 'General';
    }

    // ---- Helper: Avatar Type Class ----
    function getAvatarTypeClass(type) {
        if (type === 'tourist') return 'type-tourist';
        if (type === 'driver') return 'type-driver';
        if (type === 'guide') return 'type-guide';
        return 'type-default';
    }

    // ---- Helper: Time Ago ----
    function getTimeAgo(dateStr) {
        const now = new Date();
        const date = new Date(dateStr);
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHrs = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHrs < 24) return `${diffHrs}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    // ---- Helper: Escape HTML ----
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ---- Toast Notification ----
    function showToast(message, type = 'info') {
        const container = document.getElementById('qpToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `qp-toast ${type}`;

        const icon = type === 'success' ? 'fa-check-circle'
            : type === 'error' ? 'fa-exclamation-circle'
                : 'fa-info-circle';

        toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'toastOut 0.4s ease forwards';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

})();
