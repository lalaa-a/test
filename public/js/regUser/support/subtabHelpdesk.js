(function () {
	'use strict';

	const URL_ROOT = `${window.location.origin}/test`;
	const POLL_INTERVAL = 3000;

	const elements = {
		startBtn: document.getElementById('helpdeskStartBtn'),
		reconnectBtn: document.getElementById('helpdeskReconnectBtn'),
		statusText: document.getElementById('helpdeskChatStatusText'),
		messages: document.getElementById('helpdeskChatMessages'),
		input: document.getElementById('helpdeskChatInput'),
		sendBtn: document.getElementById('helpdeskSendBtn')
	};

	if (!elements.messages || !elements.input || !elements.sendBtn) {
		return;
	}

	let currentChatId = null;
	let pollTimer = null;
	let lastMessageId = 0;

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);
		return div.innerHTML;
	}

	function formatTime(timestamp) {
		const date = timestamp ? new Date(timestamp) : new Date();
		return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
	}

	function setStatus(text, mode) {
		if (!elements.statusText) {
			return;
		}

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

	function resetMessages() {
		elements.messages.innerHTML = `
			<div class="date-divider"><span>Today</span></div>
			<div class="message support-message">
				<div class="message-content">
					<p>Hello there. Welcome to Tripingoo Travel Support. How can we help you today?</p>
				</div>
				<span class="message-time">Now</span>
			</div>
		`;
	}

	function appendSupportMessage(text, senderName, timestamp) {
		const messageEl = document.createElement('div');
		messageEl.className = 'message support-message';
		messageEl.innerHTML = `
			<div class="message-content">
				<p class="sender-name">${escapeHtml(senderName || 'Support')}</p>
				<p>${escapeHtml(text)}</p>
			</div>
			<span class="message-time">${formatTime(timestamp)}</span>
		`;
		elements.messages.appendChild(messageEl);
		elements.messages.scrollTop = elements.messages.scrollHeight;
	}

	function appendUserMessage(text, timestamp, messageId) {
		const messageEl = document.createElement('div');
		messageEl.className = 'message user-message';

		if (messageId) {
			messageEl.dataset.messageId = String(messageId);
		}

		messageEl.innerHTML = `
			<div class="message-content">
				<p>${escapeHtml(text)}</p>
			</div>
			<span class="message-time">${formatTime(timestamp)}</span>
		`;

		if (messageId) {
			const deleteBtn = document.createElement('button');
			deleteBtn.type = 'button';
			deleteBtn.className = 'delete-message-btn';
			deleteBtn.textContent = 'Delete';
			messageEl.appendChild(deleteBtn);
		}

		elements.messages.appendChild(messageEl);
		elements.messages.scrollTop = elements.messages.scrollHeight;
		return messageEl;
	}

	function appendSystemMessage(text) {
		const messageEl = document.createElement('div');
		messageEl.className = 'message system-message';
		messageEl.innerHTML = `
			<div class="message-content system">
				<p>${escapeHtml(text)}</p>
			</div>
		`;
		elements.messages.appendChild(messageEl);
		elements.messages.scrollTop = elements.messages.scrollHeight;
	}

	function startPolling() {
		stopPolling();
		pollTimer = window.setInterval(pollMessages, POLL_INTERVAL);
	}

	function stopPolling() {
		if (pollTimer) {
			window.clearInterval(pollTimer);
			pollTimer = null;
		}
	}

	function updateMessages(messages) {
		if (!Array.isArray(messages) || messages.length === 0) {
			return;
		}

		const newMessages = messages.filter((msg) => Number(msg.id) > lastMessageId);
		if (newMessages.length === 0) {
			return;
		}

		newMessages.forEach((msg) => {
			const msgId = Number(msg.id);

			if (msg.sender_type === 'Moderator') {
				appendSupportMessage(msg.message, msg.sender_name, msg.created_at);
			}

			lastMessageId = Math.max(lastMessageId, msgId);
		});
	}

	function renderMessages(messages) {
		resetMessages();

		if (!Array.isArray(messages)) {
			return;
		}

		messages.forEach((msg) => {
			if (msg.sender_type === 'Moderator') {
				appendSupportMessage(msg.message, msg.sender_name, msg.created_at);
			} else {
				appendUserMessage(msg.message, msg.created_at, msg.id);
			}

			lastMessageId = Math.max(lastMessageId, Number(msg.id) || 0);
		});
	}

	async function pollMessages() {
		if (!currentChatId) {
			return;
		}

		try {
			const response = await fetch(`${URL_ROOT}/helpc/getMessages/${currentChatId}`);
			const data = await response.json();

			if (data.status === 'success') {
				updateMessages(data.messages || []);
			}
		} catch (error) {
			setStatus('Connection issue. Retrying...', 'error');
		}
	}

	async function startNewChat() {
		setStatus('Connecting...', 'pending');

		const response = await fetch(`${URL_ROOT}/helpc/startChat`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			}
		});

		const data = await response.json();
		if (data.status !== 'success' || !data.chat) {
			throw new Error(data.message || 'Unable to start chat');
		}

		currentChatId = Number(data.chat.id);
		lastMessageId = 0;
		resetMessages();
		setStatus(`Connected to chat #${currentChatId}`, 'connected');
		startPolling();
	}

	async function initializeChat() {
		setStatus('Checking existing chat...', 'pending');

		const response = await fetch(`${URL_ROOT}/helpc/getUserActiveChat`);
		const data = await response.json();

		if (data.status === 'success' && data.chat) {
			currentChatId = Number(data.chat.id);
			lastMessageId = 0;
			renderMessages(data.messages || []);
			setStatus(`Connected to chat #${currentChatId}`, 'connected');
			startPolling();
			return;
		}

		if (data.status === 'no_chat') {
			await startNewChat();
			return;
		}

		throw new Error(data.message || 'Unable to initialize chat');
	}

	async function sendMessage() {
		const text = elements.input.value.trim();
		if (!text) {
			return;
		}

		if (!currentChatId) {
			try {
				await initializeChat();
			} catch (error) {
				appendSystemMessage('Could not connect to chat. Please try again.');
				setStatus('Could not connect', 'error');
				return;
			}
		}

		const optimistic = appendUserMessage(text);
		elements.input.value = '';

		try {
			const response = await fetch(`${URL_ROOT}/helpc/sendMessage`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					chat_id: currentChatId,
					message: text
				})
			});

			const data = await response.json();
			if (data.status !== 'success') {
				throw new Error(data.message || 'Message send failed');
			}

			if (optimistic && data.message_id) {
				optimistic.dataset.messageId = String(data.message_id);
				const deleteBtn = document.createElement('button');
				deleteBtn.type = 'button';
				deleteBtn.className = 'delete-message-btn';
				deleteBtn.textContent = 'Delete';
				optimistic.appendChild(deleteBtn);
			}
		} catch (error) {
			appendSystemMessage('Failed to send message. Please try again.');
		}
	}

	elements.sendBtn.addEventListener('click', sendMessage);

	elements.input.addEventListener('keydown', (event) => {
		if (event.key === 'Enter') {
			event.preventDefault();
			sendMessage();
		}
	});

	if (elements.startBtn) {
		elements.startBtn.addEventListener('click', async () => {
			try {
				await initializeChat();
				elements.input.focus();
			} catch (error) {
				setStatus('Could not connect', 'error');
				appendSystemMessage('Unable to connect to support right now.');
			}
		});
	}

	if (elements.reconnectBtn) {
		elements.reconnectBtn.addEventListener('click', async () => {
			try {
				await initializeChat();
			} catch (error) {
				setStatus('Reconnect failed', 'error');
			}
		});
	}

	elements.messages.addEventListener('click', async (event) => {
		const button = event.target.closest('.delete-message-btn');
		if (!button) {
			return;
		}

		const messageNode = button.closest('.message.user-message');
		const messageId = messageNode ? messageNode.dataset.messageId : null;

		if (!messageId) {
			return;
		}

		if (!window.confirm('Delete this message?')) {
			return;
		}

		try {
			const response = await fetch(`${URL_ROOT}/helpc/deleteMessage`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ message_id: messageId })
			});

			const data = await response.json();
			if (data.status === 'success') {
				messageNode.remove();
			} else {
				appendSystemMessage(data.message || 'Could not delete message');
			}
		} catch (error) {
			appendSystemMessage('Could not delete message');
		}
	});

	window.addEventListener('beforeunload', stopPolling);

	// Auto-load existing chat on subtab open.
	initializeChat().catch(() => {
		setStatus('Click Start / Resume to connect', 'pending');
	});
})();
