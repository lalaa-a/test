(function () {
	'use strict';

	const URL_ROOT = `${window.location.origin}/test`;

	const form = document.getElementById('complaintFeedbackForm');
	const subjectInput = document.getElementById('problemSubject');
	const messageInput = document.getElementById('problemMessage');
	const submitBtn = document.getElementById('submitProblemBtn');
	const notice = document.getElementById('problemFormNotice');
	const list = document.getElementById('userProblemsList');
	const filterButtons = document.querySelectorAll('.history-filter-btn');

	if (!form || !list) {
		return;
	}

	let currentFilter = 'all';

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	function formatDate(dateStr) {
		const date = new Date(dateStr);
		return date.toLocaleString([], {
			year: 'numeric',
			month: 'short',
			day: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
	}

	function renderNotice(message, type) {
		if (!notice) {
			return;
		}

		notice.textContent = message || '';
		notice.classList.remove('is-success', 'is-error');

		if (type === 'success') {
			notice.classList.add('is-success');
		} else if (type === 'error') {
			notice.classList.add('is-error');
		}
	}

	function statusMeta(status) {
		const normalized = String(status || '').toLowerCase();
		if (normalized === 'completed') {
			return { label: 'Completed', className: 'completed' };
		}
		if (normalized === 'in_progress') {
			return { label: 'In Progress', className: 'in-progress' };
		}
		return { label: 'Pending', className: 'pending' };
	}

	function renderProblems(problems) {
		if (!Array.isArray(problems) || problems.length === 0) {
			list.innerHTML = `
				<div class="empty-state">
					<i class="fas fa-inbox"></i>
					<p>No submissions for this filter.</p>
				</div>
			`;
			return;
		}

		list.innerHTML = problems.map((problem) => {
			const status = statusMeta(problem.status);
			const completedByText = problem.completedByName
				? `<span class="resolver">Handled by ${escapeHtml(problem.completedByName)}</span>`
				: '';

			return `
				<article class="problem-card">
					<div class="problem-card-head">
						<h4>${escapeHtml(problem.subject)}</h4>
						<span class="status-badge ${status.className}">${status.label}</span>
					</div>
					<p class="problem-message">${escapeHtml(problem.message)}</p>
					<div class="problem-meta">
						<span><i class="fas fa-clock"></i> ${formatDate(problem.createdAt)}</span>
						${completedByText}
					</div>
				</article>
			`;
		}).join('');
	}

	async function loadProblems() {
		list.innerHTML = `
			<div class="loading-state">
				<i class="fas fa-spinner fa-spin"></i>
				<span>Loading your submissions...</span>
			</div>
		`;

		try {
			const response = await fetch(`${URL_ROOT}/RegUser/getUserProblemsByUserId?filter=${encodeURIComponent(currentFilter)}`);
			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || 'Failed to load submissions');
			}

			renderProblems(data.problems || []);
		} catch (error) {
			list.innerHTML = `
				<div class="empty-state error">
					<i class="fas fa-triangle-exclamation"></i>
					<p>Failed to load submissions. Please try again.</p>
				</div>
			`;
		}
	}

	async function submitProblem(event) {
		event.preventDefault();

		const subject = subjectInput ? subjectInput.value.trim() : '';
		const message = messageInput ? messageInput.value.trim() : '';

		if (!subject || !message) {
			renderNotice('Subject and message are required.', 'error');
			return;
		}

		const originalHtml = submitBtn ? submitBtn.innerHTML : '';
		if (submitBtn) {
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
		}

		renderNotice('');

		try {
			const response = await fetch(`${URL_ROOT}/RegUser/submitProblem`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ subject, message })
			});

			const data = await response.json();
			if (!data.success) {
				throw new Error(data.message || 'Submission failed');
			}

			renderNotice(data.message || 'Submitted successfully.', 'success');
			form.reset();
			currentFilter = 'all';
			filterButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.filter === 'all'));
			await loadProblems();
		} catch (error) {
			renderNotice(error.message || 'Could not submit your request.', 'error');
		} finally {
			if (submitBtn) {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalHtml;
			}
		}
	}

	form.addEventListener('submit', submitProblem);

	filterButtons.forEach((button) => {
		button.addEventListener('click', () => {
			currentFilter = button.dataset.filter || 'all';
			filterButtons.forEach((btn) => btn.classList.toggle('active', btn === button));
			loadProblems();
		});
	});

	loadProblems();
})();
