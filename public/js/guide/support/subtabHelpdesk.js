(function () {
	'use strict';

	const URL_ROOT = `${window.location.origin}/test`;
	const POLL_INTERVAL = 3000;
	const INPUT_MAX_HEIGHT = 110;

	const elements = {
		root: document.getElementById('helpdeskSubtabRoot'),
		targetList: document.getElementById('supportTargetList'),
		filterTabs: document.querySelectorAll('.target-filter-tab'),
		reconnectBtn: document.getElementById('helpdeskReconnectBtn'),
		statusText: document.getElementById('helpdeskChatStatusText'),
		targetAvatar: document.getElementById('helpdeskTargetAvatar'),
		targetName: document.getElementById('helpdeskTargetName'),
		messages: document.getElementById('helpdeskChatMessages'),
		input: document.getElementById('helpdeskChatInput'),
		sendBtn: document.getElementById('helpdeskSendBtn')
	};

	if (!elements.root || !elements.targetList || !elements.messages || !elements.input || !elements.sendBtn) {
		return;
	}

	const state = {
		viewerId: Number(elements.root.dataset.viewerId || 0),
		viewerSenderType: mapAccountType(elements.root.dataset.viewerType || ''),
		filter: 'all',
		targets: [],
		selectedTarget: null,
		currentChatId: null,
		pollTimer: null,
		isLoadingMessages: false,
		isSending: false,
		messagesRequestToken: 0,
		lastMessagesSignature: ''
	};

	function mapAccountType(accountType) {
		const value = String(accountType || '').toLowerCase();
		const map = {
			tourist: 'Traveller',
			guide: 'Guide',
			driver: 'Driver',
			site_moderator: 'Moderator',
			admin: 'Admin'
		};
		return map[value] || '';
	}

	function showNotification(message, type = 'info') {
		if (typeof window.showNotification === 'function') {
			window.showNotification(message, type);
		}
	}

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	function formatMessageText(text) {
		return escapeHtml(text).replace(/\n/g, '<br>');
	}

	function formatTime(timestamp) {
		const date = timestamp ? new Date(timestamp) : new Date();
		return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
	}

	function formatDateLabel(timestamp) {
		const date = timestamp ? new Date(timestamp) : new Date();
		const now = new Date();
		const yesterday = new Date(now);
		yesterday.setDate(yesterday.getDate() - 1);

		if (date.toDateString() === now.toDateString()) return 'Today';
		if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';

		return date.toLocaleDateString('en-US', {
			month: 'short',
			day: 'numeric',
			year: 'numeric'
		});
	}

	function getInitials(name, fallback = 'TS') {
		const cleaned = String(name || '').trim();
		if (!cleaned) return fallback;

		const parts = cleaned.split(/\s+/).filter(Boolean);
		if (parts.length === 1) {
			return parts[0].slice(0, 2).toUpperCase();
		}

		return `${parts[0].charAt(0)}${parts[1].charAt(0)}`.toUpperCase();
	}

	function getTargetKey(target) {
		if (!target) return '';
		return `${target.targetType}:${target.targetUserId || 0}`;
	}

	function setStatus(text, mode) {
		if (!elements.statusText) return;

		elements.statusText.textContent = text;
		elements.statusText.classList.remove('is-connected', 'is-error', 'is-pending');

		if (mode === 'connected') {
			elements.statusText.classList.add('is-connected');
		} else if (mode === 'error') {
			elements.statusText.classList.add('is-error');
		} else {
			elements.statusText.classList.add('is-pending');
		}
	}

	function setComposerEnabled(enabled) {
		elements.input.disabled = !enabled;
		if (!enabled) {
			elements.sendBtn.disabled = true;
			return;
		}
		syncSendButtonState();
	}

	function syncSendButtonState() {
		const text = elements.input.value.trim();
		elements.sendBtn.disabled = elements.input.disabled || state.isSending || text.length === 0;
	}

	function autoResizeInput() {
		elements.input.style.height = 'auto';
		elements.input.style.height = `${Math.min(elements.input.scrollHeight, INPUT_MAX_HEIGHT)}px`;
	}

	async function requestJson(url, options = {}) {
		const response = await fetch(url, options);
		const parsed = await parseJsonResponse(response);

		return {
			response,
			data: parsed.data || {},
			raw: parsed.raw || ''
		};
	}

	async function parseJsonResponse(response) {
		const raw = await response.text();

		if (!raw) {
			return { data: {}, raw: '' };
		}

		const direct = tryParseJson(raw);
		if (direct) {
			return { data: direct, raw };
		}

		const extracted = extractFirstJsonObject(raw);
		if (extracted) {
			return { data: extracted, raw };
		}

		return { data: {}, raw };
	}

	function tryParseJson(text) {
		try {
			return JSON.parse(text);
		} catch (e) {
			return null;
		}
	}

	function extractFirstJsonObject(text) {
		for (let start = 0; start < text.length; start++) {
			if (text[start] !== '{') continue;

			let depth = 0;
			let inString = false;
			let escaped = false;

			for (let i = start; i < text.length; i++) {
				const ch = text[i];

				if (inString) {
					if (escaped) {
						escaped = false;
						continue;
					}
					if (ch === '\\') {
						escaped = true;
						continue;
					}
					if (ch === '"') {
						inString = false;
					}
					continue;
				}

				if (ch === '"') {
					inString = true;
					continue;
				}

				if (ch === '{') depth++;

				if (ch === '}') {
					depth--;
					if (depth === 0) {
						const candidate = text.slice(start, i + 1);
						const parsed = tryParseJson(candidate);
						if (parsed) {
							return parsed;
						}
						break;
					}
				}
			}
		}

		return null;
	}

	function isSendMessageSuccess(data, raw, responseOk) {
		if (data && data.status === 'success') return true;
		if (data && data.message_id) return true;

		const rawText = String(raw || '');
		if (/"status"\s*:\s*"success"/i.test(rawText)) return true;
		if (/"message_id"\s*:\s*\d+/i.test(rawText)) return true;

		if (responseOk && (!data || !data.status)) return true;
		return false;
	}

	function createSiteTarget() {
		return {
			targetType: 'Site',
			targetUserId: null,
			name: 'Tripingoo Site Support',
			subtitle: 'General account and trip assistance',
			avatar: 'TS'
		};
	}

	function mapContactToTarget(contact, targetType) {
		const id = Number(contact.id || 0);
		const name = String(contact.name || contact.fullname || `${targetType} #${id || '?'}`);

		return {
			targetType,
			targetUserId: id,
			name,
			subtitle: 'Traveller connected to your guide requests',
			avatar: getInitials(name, 'TR')
		};
	}

	function renderTargetsLoading() {
		elements.targetList.innerHTML = `
			<div class="target-loading">
				<i class="fas fa-spinner fa-spin"></i>
				<span>Loading contacts...</span>
			</div>
		`;
	}

	function renderTargetList() {
		const selectedKey = getTargetKey(state.selectedTarget);
		const filter = state.filter;

		const filtered = state.targets.filter((target) => {
			if (filter === 'site') return target.targetType === 'Site';
			if (filter === 'travellers') return target.targetType === 'Traveller';
			return true;
		});

		if (filtered.length === 0) {
			elements.targetList.innerHTML = `
				<div class="target-empty">
					<span>No contacts available for this filter.</span>
				</div>
			`;
			return;
		}

		elements.targetList.innerHTML = filtered.map((target) => {
			const key = getTargetKey(target);
			const isActive = key === selectedKey;
			const typeClass = target.targetType.toLowerCase();

			return `
				<button type="button" class="target-item ${isActive ? 'active' : ''}" data-key="${escapeHtml(key)}">
					<span class="target-item-avatar">${escapeHtml(target.avatar)}</span>
					<span class="target-item-body">
						<strong>${escapeHtml(target.name)}</strong>
						<small>${escapeHtml(target.subtitle)}</small>
					</span>
					<span class="target-type-badge ${typeClass}">${escapeHtml(target.targetType)}</span>
				</button>
			`;
		}).join('');

		elements.targetList.querySelectorAll('.target-item').forEach((button) => {
			button.addEventListener('click', () => {
				const key = button.dataset.key || '';
				selectTargetByKey(key);
			});
		});
	}

	function updateChatHeader() {
		if (!state.selectedTarget) {
			elements.targetAvatar.textContent = 'TS';
			elements.targetName.textContent = 'Tripingoo Site Support';
			return;
		}

		elements.targetAvatar.textContent = state.selectedTarget.avatar;
		elements.targetName.textContent = state.selectedTarget.name;
	}

	function renderWelcomeState() {
		elements.messages.innerHTML = `
			<div class="empty-chat-state">
				<i class="fas fa-comments"></i>
				<h4>Select a conversation</h4>
				<p>Choose Site Support or a traveller to start messaging.</p>
			</div>
		`;
	}

	function renderNoMessagesState(targetName) {
		elements.messages.innerHTML = `
			<div class="empty-chat-state">
				<i class="fas fa-paper-plane"></i>
				<h4>No messages yet</h4>
				<p>Start a new conversation with ${escapeHtml(targetName || 'support')}.</p>
			</div>
		`;
	}

	function renderSystemMessage(text) {
		const message = `
			<article class="helpdesk-message system">
				<div class="helpdesk-message-content">
					<p class="helpdesk-message-text">${formatMessageText(text)}</p>
				</div>
			</article>
		`;

		if (elements.messages.querySelector('.empty-chat-state')) {
			elements.messages.innerHTML = '';
		}

		elements.messages.insertAdjacentHTML('beforeend', message);
		scrollToBottom();
	}

	function isNearBottom() {
		const threshold = 32;
		return (elements.messages.scrollHeight - elements.messages.scrollTop - elements.messages.clientHeight) <= threshold;
	}

	function scrollToBottom() {
		elements.messages.scrollTop = elements.messages.scrollHeight;
	}

	function getMessagesSignature(messages) {
		return JSON.stringify((messages || []).map((msg) => ({
			id: Number(msg.id || 0),
			sender_id: Number(msg.sender_id || 0),
			sender_type: msg.sender_type || '',
			message: msg.message || '',
			created_at: msg.created_at || ''
		})));
	}

	function isOutgoingMessage(message) {
		const senderId = Number(message.sender_id || 0);
		if (state.viewerId && senderId) {
			return state.viewerId === senderId;
		}

		return String(message.sender_type || '') === String(state.viewerSenderType || 'Guide');
	}

	function createMessageHTML(message) {
		const outgoing = isOutgoingMessage(message);
		const messageId = Number(message.id || 0);
		const senderName = message.sender_name || (state.selectedTarget ? state.selectedTarget.name : 'Support');
		const deleteAction = outgoing && messageId
			? `<button type="button" class="delete-message-btn" data-message-id="${messageId}">Delete</button>`
			: '';

		return `
			<article class="helpdesk-message ${outgoing ? 'outgoing' : 'incoming'}" data-message-id="${messageId || ''}">
				<div class="helpdesk-message-content">
					${outgoing ? '' : `<p class="helpdesk-sender-name">${escapeHtml(senderName)}</p>`}
					<p class="helpdesk-message-text">${formatMessageText(message.message || '')}</p>
				</div>
				<div class="helpdesk-message-meta">
					<span>${formatTime(message.created_at)}</span>
					${deleteAction}
				</div>
			</article>
		`;
	}

	function renderMessages(messages) {
		const list = Array.isArray(messages) ? messages : [];
		const signature = getMessagesSignature(list);

		if (signature === state.lastMessagesSignature) {
			return;
		}

		if (list.length === 0) {
			renderNoMessagesState(state.selectedTarget ? state.selectedTarget.name : 'support');
			state.lastMessagesSignature = signature;
			return;
		}

		const shouldAutoScroll = isNearBottom();
		let html = '';
		let lastDateLabel = '';

		list.forEach((message) => {
			const currentDate = formatDateLabel(message.created_at);

			if (currentDate !== lastDateLabel) {
				html += `<div class="chat-date-divider"><span>${escapeHtml(currentDate)}</span></div>`;
				lastDateLabel = currentDate;
			}

			html += createMessageHTML(message);
		});

		elements.messages.innerHTML = html;
		state.lastMessagesSignature = signature;

		if (shouldAutoScroll) {
			scrollToBottom();
		}
	}

	function appendOptimisticMessage(text) {
		const html = `
			<article class="helpdesk-message outgoing" data-message-id="">
				<div class="helpdesk-message-content">
					<p class="helpdesk-message-text">${formatMessageText(text)}</p>
				</div>
				<div class="helpdesk-message-meta">
					<span>${formatTime(new Date().toISOString())}</span>
				</div>
			</article>
		`;

		if (elements.messages.querySelector('.empty-chat-state')) {
			elements.messages.innerHTML = '';
		}

		elements.messages.insertAdjacentHTML('beforeend', html);
		scrollToBottom();

		const allOutgoing = elements.messages.querySelectorAll('.helpdesk-message.outgoing');
		return allOutgoing[allOutgoing.length - 1] || null;
	}

	function attachDeleteButton(messageNode, messageId) {
		if (!messageNode || !messageId) return;

		const meta = messageNode.querySelector('.helpdesk-message-meta');
		if (!meta || meta.querySelector('.delete-message-btn')) return;

		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'delete-message-btn';
		button.dataset.messageId = String(messageId);
		button.textContent = 'Delete';
		meta.appendChild(button);

		messageNode.dataset.messageId = String(messageId);
	}

	function buildTargetQuery(target) {
		const params = new URLSearchParams();
		params.set('target_type', String(target.targetType || 'Site').toLowerCase());

		if (target.targetUserId) {
			params.set('target_user_id', String(target.targetUserId));
		}

		return `?${params.toString()}`;
	}

	function buildTargetPayload(target) {
		const payload = {
			target_type: String(target.targetType || 'Site').toLowerCase()
		};

		if (target.targetUserId) {
			payload.target_user_id = target.targetUserId;
		}

		return payload;
	}

	async function loadTargets() {
		renderTargetsLoading();

		const siteTarget = createSiteTarget();
		let travellers = [];

		try {
			const result = await requestJson(`${URL_ROOT}/helpc/getGuideSupportContacts`);
			const data = result.data || {};

			if (data.status !== 'success') {
				throw new Error(data.message || 'Could not load contacts');
			}

			travellers = (data.travellers || []).map((contact) => mapContactToTarget(contact, 'Traveller'));
		} catch (error) {
			showNotification('Could not load traveller contacts. Site support is still available.', 'warning');
		}

		state.targets = [siteTarget, ...travellers];
		renderTargetList();
		await selectTargetByKey(getTargetKey(siteTarget));
	}

	async function selectTargetByKey(key) {
		const target = state.targets.find((item) => getTargetKey(item) === key);
		if (!target) return;

		state.selectedTarget = target;
		state.currentChatId = null;
		state.lastMessagesSignature = '';

		updateChatHeader();
		renderTargetList();
		renderNoMessagesState(target.name);
		setComposerEnabled(false);

		await loadConversationForSelectedTarget(false);
	}

	async function loadConversationForSelectedTarget(isManualRefresh) {
		if (!state.selectedTarget) return;

		stopPolling();
		setStatus('Loading conversation...', 'pending');

		try {
			const result = await requestJson(`${URL_ROOT}/helpc/getUserActiveChat${buildTargetQuery(state.selectedTarget)}`);
			const data = result.data || {};

			if (data.status === 'success' && data.chat) {
				state.currentChatId = Number(data.chat.id) || null;
				renderMessages(data.messages || []);
				setComposerEnabled(true);
				setStatus(`Connected with ${state.selectedTarget.name}`, 'connected');
				startPolling();
				markMessagesAsRead();
				return;
			}

			if (data.status === 'no_chat') {
				state.currentChatId = null;
				renderNoMessagesState(state.selectedTarget.name);
				setComposerEnabled(true);
				setStatus(`Start a new conversation with ${state.selectedTarget.name}`, 'pending');
				return;
			}

			throw new Error(data.message || 'Unable to load conversation');
		} catch (error) {
			setStatus('Could not load conversation', 'error');
			setComposerEnabled(false);
			renderSystemMessage('Unable to load this conversation right now.');

			if (isManualRefresh) {
				showNotification('Could not refresh the conversation', 'error');
			}
		}
	}

	async function ensureConversationExists() {
		if (state.currentChatId || !state.selectedTarget) {
			return true;
		}

		const payload = buildTargetPayload(state.selectedTarget);
		const result = await requestJson(`${URL_ROOT}/helpc/startChat`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(payload)
		});

		const data = result.data || {};
		const chat = data.chat || null;
		const success = (data.status === 'success' && chat && chat.id) || (chat && chat.id);

		if (!success) {
			throw new Error(data.message || 'Could not start this chat');
		}

		state.currentChatId = Number(chat.id) || null;
		startPolling();
		return true;
	}

	async function pollMessages(silent = true) {
		if (!state.currentChatId || state.isLoadingMessages) {
			return;
		}

		const requestToken = ++state.messagesRequestToken;
		state.isLoadingMessages = true;

		try {
			const result = await requestJson(`${URL_ROOT}/helpc/getMessages/${state.currentChatId}`);
			const data = result.data || {};

			if (requestToken !== state.messagesRequestToken) {
				return;
			}

			if (data.status === 'success') {
				renderMessages(data.messages || []);
				markMessagesAsRead();
				return;
			}

			if (!silent) {
				throw new Error(data.message || 'Could not load messages');
			}
		} catch (error) {
			if (!silent) {
				setStatus('Connection issue. Retrying...', 'error');
			}
		} finally {
			if (requestToken === state.messagesRequestToken) {
				state.isLoadingMessages = false;
			}
		}
	}

	async function markMessagesAsRead() {
		if (!state.currentChatId) return;

		try {
			await requestJson(`${URL_ROOT}/helpc/markAsRead`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ chat_id: state.currentChatId })
			});
		} catch (error) {
			// Intentionally ignored for silent UX.
		}
	}

	function startPolling() {
		stopPolling();
		state.pollTimer = window.setInterval(() => {
			pollMessages(true);
		}, POLL_INTERVAL);
	}

	function stopPolling() {
		if (state.pollTimer) {
			window.clearInterval(state.pollTimer);
			state.pollTimer = null;
		}
	}

	async function sendMessage() {
		if (state.isSending || !state.selectedTarget) {
			return;
		}

		const text = elements.input.value.trim();
		if (!text) {
			syncSendButtonState();
			return;
		}

		state.isSending = true;
		syncSendButtonState();

		const optimistic = appendOptimisticMessage(text);
		elements.input.value = '';
		autoResizeInput();
		syncSendButtonState();

		try {
			await ensureConversationExists();

			const result = await requestJson(`${URL_ROOT}/helpc/sendMessage`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					chat_id: state.currentChatId,
					message: text
				})
			});

			const data = result.data || {};
			const success = isSendMessageSuccess(data, result.raw, result.response.ok);

			if (!success) {
				throw new Error(data.message || 'Failed to send message');
			}

			if (optimistic && data.message_id) {
				attachDeleteButton(optimistic, data.message_id);
			}

			state.lastMessagesSignature = '';
			await pollMessages(true);
			setStatus(`Connected with ${state.selectedTarget.name}`, 'connected');
		} catch (error) {
			if (optimistic && optimistic.parentElement) {
				optimistic.remove();
			}

			renderSystemMessage(error.message || 'Failed to send message. Please try again.');
			showNotification(error.message || 'Failed to send message', 'error');
		} finally {
			state.isSending = false;
			syncSendButtonState();
		}
	}

	async function deleteMessage(messageId, messageNode) {
		try {
			const result = await requestJson(`${URL_ROOT}/helpc/deleteMessage`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ message_id: messageId })
			});

			const data = result.data || {};
			if (data.status === 'success') {
				if (messageNode && messageNode.parentElement) {
					messageNode.remove();
				}
				state.lastMessagesSignature = '';
			} else {
				showNotification(data.message || 'Could not delete message', 'error');
			}
		} catch (error) {
			showNotification('Could not delete message', 'error');
		}
	}

	function bindEvents() {
		elements.filterTabs.forEach((tab) => {
			tab.addEventListener('click', () => {
				elements.filterTabs.forEach((btn) => btn.classList.remove('active'));
				tab.classList.add('active');
				state.filter = tab.dataset.filter || 'all';
				renderTargetList();
			});
		});

		if (elements.reconnectBtn) {
			elements.reconnectBtn.addEventListener('click', async () => {
				if (!state.selectedTarget) {
					return;
				}
				await loadConversationForSelectedTarget(true);
			});
		}

		elements.input.addEventListener('input', () => {
			autoResizeInput();
			syncSendButtonState();
		});

		elements.input.addEventListener('keydown', (event) => {
			if (event.key === 'Enter' && !event.shiftKey) {
				event.preventDefault();
				sendMessage();
			}
		});

		elements.sendBtn.addEventListener('click', sendMessage);

		elements.messages.addEventListener('click', (event) => {
			const button = event.target.closest('.delete-message-btn');
			if (!button) return;

			const messageId = Number(button.dataset.messageId || 0);
			if (!messageId) return;

			const messageNode = button.closest('.helpdesk-message.outgoing');
			if (!messageNode) return;

			if (!window.confirm('Delete this message?')) {
				return;
			}

			deleteMessage(messageId, messageNode);
		});
	}

	async function init() {
		bindEvents();
		renderWelcomeState();
		setStatus('Loading support contacts...', 'pending');
		setComposerEnabled(false);
		autoResizeInput();
		await loadTargets();
	}

	window.addEventListener('beforeunload', stopPolling);

	init().catch(() => {
		setStatus('Could not initialize help desk', 'error');
		renderSystemMessage('Help desk is temporarily unavailable. Please try again.');
	});
})();
