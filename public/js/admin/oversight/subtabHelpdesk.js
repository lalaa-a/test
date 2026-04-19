(function () {
    'use strict';

    const URL_ROOT = `${window.location.origin}/test`;
    const POLL_INTERVAL = 3000;
    const MAX_MESSAGE_LENGTH = 500;
    const SEARCH_DEBOUNCE_MS = 250;

    const elements = {
        root: document.getElementById('moderatorHelpdeskRoot'),
        chatList: document.getElementById('supportChatList'),
        openChatsCount: document.getElementById('openChatsCount'),
        myChatsCount: document.getElementById('myChatsCount'),
        refreshBtn: document.getElementById('refreshChatsBtn'),
        chatEmptyState: document.getElementById('chatEmptyState'),
        activeChatContainer: document.getElementById('activeChatContainer'),
        chatUserAvatar: document.getElementById('chatUserAvatar'),
        chatUserName: document.getElementById('chatUserName'),
        chatUserType: document.getElementById('chatUserType'),
        chatIdDisplay: document.getElementById('chatIdDisplay'),
        chatMessages: document.getElementById('chatMessages'),
        chatMessagesContainer: document.getElementById('chatMessagesContainer'),
        messageInput: document.getElementById('messageInput'),
        sendMessageBtn: document.getElementById('sendMessageBtn'),
        charCount: document.getElementById('charCount'),
        chatInputArea: document.getElementById('chatInputArea'),
        claimChatBtn: document.getElementById('claimChatBtn'),
        closeChatBtn: document.getElementById('closeChatBtn'),
        deleteChatBtn: document.getElementById('deleteChatBtn'),
        claimOverlay: document.getElementById('claimOverlay'),
        claimOverlayBtn: document.getElementById('claimOverlayBtn'),
        toastContainer: document.getElementById('toastContainer'),
        filterTabs: document.querySelectorAll('.panel-tab'),
        userSearchInput: document.getElementById('userDirectorySearchInput'),
        userSearchResults: document.getElementById('userDirectoryResults')
    };

    if (!elements.chatList || !elements.chatMessages || !elements.messageInput || !elements.sendMessageBtn) {
        return;
    }

    const state = {
        viewerId: Number(elements.root ? elements.root.dataset.viewerId : 0),
        chatsList: [],
        currentFilter: 'all',
        currentChatId: null,
        currentChatStatus: null,
        isAdmin: false,
        lastChatListSignature: '',
        lastMessagesSignature: '',
        messagesRequestToken: 0,
        messagesLoadInFlightForChatId: null,
        chatListPollTimer: null,
        messagesPollTimer: null,
        searchDebounceTimer: null,
        searchRequestToken: 0
    };

    function init() {
        bindEvents();
        renderSearchHint();
        loadChatList();
        startChatListPolling();
    }

    function bindEvents() {
        elements.filterTabs.forEach((tab) => {
            tab.addEventListener('click', () => handleFilterChange(tab));
        });

        if (elements.refreshBtn) {
            elements.refreshBtn.addEventListener('click', () => {
                elements.refreshBtn.classList.add('loading');
                loadChatList().finally(() => {
                    elements.refreshBtn.classList.remove('loading');
                });
            });
        }

        elements.messageInput.addEventListener('input', handleInputChange);
        elements.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        elements.sendMessageBtn.addEventListener('click', sendMessage);

        if (elements.claimChatBtn) {
            elements.claimChatBtn.addEventListener('click', claimChat);
        }

        if (elements.claimOverlayBtn) {
            elements.claimOverlayBtn.addEventListener('click', claimChat);
        }

        if (elements.closeChatBtn) {
            elements.closeChatBtn.addEventListener('click', closeChat);
        }

        if (elements.deleteChatBtn) {
            elements.deleteChatBtn.addEventListener('click', deleteChat);
        }

        elements.chatMessages.addEventListener('click', (e) => {
            const btn = e.target.closest('.message-delete-btn');
            if (!btn) return;

            const messageId = Number(btn.dataset.messageId || 0);
            if (!messageId) return;

            deleteMessage(messageId);
        });

        if (elements.userSearchInput) {
            elements.userSearchInput.addEventListener('input', handleDirectorySearchInput);
        }
    }

    function handleFilterChange(tab) {
        elements.filterTabs.forEach((item) => item.classList.remove('active'));
        tab.classList.add('active');
        state.currentFilter = tab.dataset.filter || 'all';
        renderChatList();
    }

    function handleInputChange() {
        const length = elements.messageInput.value.length;

        if (elements.charCount) {
            elements.charCount.textContent = `${length}/${MAX_MESSAGE_LENGTH}`;
        }

        elements.messageInput.style.height = 'auto';
        elements.messageInput.style.height = `${Math.min(elements.messageInput.scrollHeight, 120)}px`;

        elements.sendMessageBtn.disabled = length === 0 || length > MAX_MESSAGE_LENGTH;
    }

    function handleDirectorySearchInput() {
        if (!elements.userSearchInput) return;

        const searchTerm = elements.userSearchInput.value.trim();

        if (state.searchDebounceTimer) {
            clearTimeout(state.searchDebounceTimer);
            state.searchDebounceTimer = null;
        }

        if (searchTerm === '') {
            renderSearchHint();
            return;
        }

        state.searchDebounceTimer = setTimeout(() => {
            searchUsers(searchTerm);
        }, SEARCH_DEBOUNCE_MS);
    }

    async function searchUsers(searchTerm) {
        const requestToken = ++state.searchRequestToken;
        renderSearchLoading();

        try {
            const response = await fetch(`${URL_ROOT}/helpc/searchUsersForModerator?q=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();

            if (requestToken !== state.searchRequestToken) {
                return;
            }

            if (data.status !== 'success') {
                throw new Error(data.message || 'Search failed');
            }

            renderSearchResults(data.users || []);
        } catch (error) {
            if (requestToken !== state.searchRequestToken) {
                return;
            }
            renderSearchError('Unable to search users right now.');
        }
    }

    function renderSearchHint() {
        if (!elements.userSearchResults) return;

        elements.userSearchResults.innerHTML = `
            <div class="directory-hint">
                Search by user ID or name to chat with admin, moderators, travellers, guides, or drivers.
            </div>
        `;
    }

    function renderSearchLoading() {
        if (!elements.userSearchResults) return;

        elements.userSearchResults.innerHTML = `
            <div class="directory-loading">
                <i class="fas fa-spinner fa-spin"></i> Searching users...
            </div>
        `;
    }

    function renderSearchError(message) {
        if (!elements.userSearchResults) return;

        elements.userSearchResults.innerHTML = `<div class="directory-empty">${escapeHtml(message)}</div>`;
    }

    function renderSearchResults(users) {
        if (!elements.userSearchResults) return;

        if (!Array.isArray(users) || users.length === 0) {
            elements.userSearchResults.innerHTML = '<div class="directory-empty">No matching users found.</div>';
            return;
        }

        elements.userSearchResults.innerHTML = users.map((user) => {
            const typeLabel = user.user_type || mapAccountType(user.account_type);
            return `
                <button type="button" class="directory-result-item" data-user-id="${Number(user.id)}">
                    <span class="directory-result-main">
                        <strong>${escapeHtml(user.fullname || 'Unknown User')}</strong>
                        <small>#${Number(user.id)} - ${escapeHtml(typeLabel)}</small>
                    </span>
                    <span class="type-badge ${normalizeTypeClass(typeLabel)}">${escapeHtml(typeLabel)}</span>
                </button>
            `;
        }).join('');

        elements.userSearchResults.querySelectorAll('.directory-result-item').forEach((item) => {
            item.addEventListener('click', () => {
                const targetUserId = Number(item.dataset.userId || 0);
                if (targetUserId > 0) {
                    startDirectChat(targetUserId);
                }
            });
        });
    }

    async function startDirectChat(targetUserId) {
        try {
            const response = await fetch(`${URL_ROOT}/helpc/startChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    target_type: 'user',
                    target_user_id: targetUserId
                })
            });

            const data = await response.json();
            if (data.status !== 'success' || !data.chat || !data.chat.id) {
                throw new Error(data.message || 'Could not start direct chat');
            }

            if (elements.userSearchInput) {
                elements.userSearchInput.value = '';
            }
            renderSearchHint();

            await loadChatList();
            await selectChat(Number(data.chat.id));
        } catch (error) {
            showToast(error.message || 'Failed to start direct chat', 'error');
        }
    }

    async function loadChatList() {
        try {
            const response = await fetch(`${URL_ROOT}/helpc/getChatsForModerator`);
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message || 'Unable to load chats');
            }

            const nextChats = data.chats || [];
            const nextSignature = getChatListSignature(nextChats);
            const changed = nextSignature !== state.lastChatListSignature;

            state.chatsList = nextChats;
            state.isAdmin = !!data.isAdmin;

            updateChatCounts();

            if (changed) {
                renderChatList();
                state.lastChatListSignature = nextSignature;
            }

            if (state.currentChatId) {
                const activeChat = getCurrentChat();
                if (activeChat) {
                    state.currentChatStatus = activeChat.status;
                    updateActionButtons(activeChat);
                } else {
                    resetActiveChatUI();
                }
            }
        } catch (error) {
            elements.chatList.innerHTML = `
                <div class="empty-list-state">
                    <i class="fas fa-wifi"></i>
                    <h4>Connection Error</h4>
                    <p>${escapeHtml(error.message || 'Unable to load chats')}</p>
                </div>
            `;
        }
    }

    function updateChatCounts() {
        const siteOpenCount = state.chatsList.filter((chat) => isSiteChat(chat) && chat.status === 'Open').length;
        const directCount = state.chatsList.filter((chat) => !isSiteChat(chat)).length;

        if (elements.openChatsCount) {
            elements.openChatsCount.textContent = String(siteOpenCount);
        }

        if (elements.myChatsCount) {
            elements.myChatsCount.textContent = String(directCount);
        }
    }

    function renderChatList() {
        let filteredChats = [...state.chatsList];

        if (state.currentFilter === 'open') {
            filteredChats = filteredChats.filter((chat) => isSiteChat(chat));
        } else if (state.currentFilter === 'mine') {
            filteredChats = filteredChats.filter((chat) => !isSiteChat(chat));
        }

        filteredChats.sort((a, b) => {
            const aTime = new Date(a.updated_at || a.created_at || 0).getTime();
            const bTime = new Date(b.updated_at || b.created_at || 0).getTime();
            return bTime - aTime;
        });

        if (filteredChats.length === 0) {
            const emptyText = state.currentFilter === 'open'
                ? 'No site support queue chats at the moment.'
                : state.currentFilter === 'mine'
                    ? 'No direct chats yet. Use search to start one.'
                    : 'No chats found.';

            elements.chatList.innerHTML = `
                <div class="empty-list-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No chats found</h4>
                    <p>${escapeHtml(emptyText)}</p>
                </div>
            `;
            return;
        }

        elements.chatList.innerHTML = filteredChats.map(createChatItemHTML).join('');
        elements.chatList.querySelectorAll('.chat-item').forEach((item) => {
            item.addEventListener('click', () => {
                const chatId = Number(item.dataset.chatId || 0);
                if (chatId > 0) {
                    selectChat(chatId);
                }
            });
        });
    }

    function createChatItemHTML(chat) {
        const chatId = Number(chat.id || 0);
        const active = state.currentChatId === chatId;
        const hasUnread = Number(chat.unread_count || 0) > 0;
        const site = isSiteChat(chat);
        const userTypeLabel = chat.user_type || 'User';
        const userTypeClass = normalizeTypeClass(userTypeLabel);
        const statusClass = site ? String(chat.status || 'Open').toLowerCase() : 'direct';
        const statusLabel = site ? (chat.status || 'Open') : 'Direct';
        const statusDotClass = site ? String(chat.status || 'Open').toLowerCase() : 'assigned';

        return `
            <div class="chat-item ${active ? 'active' : ''} ${hasUnread ? 'unread' : ''}" data-chat-id="${chatId}">
                <div class="chat-avatar ${userTypeClass}">
                    ${escapeHtml(getInitials(chat.user_name || 'User'))}
                    <span class="status-dot ${escapeHtml(statusDotClass)}"></span>
                </div>
                <div class="chat-info">
                    <div class="chat-info-header">
                        <span class="chat-name">${escapeHtml(chat.user_name || 'Unknown User')}</span>
                        <span class="chat-time">${escapeHtml(formatTimeAgo(chat.updated_at || chat.created_at))}</span>
                    </div>
                    <p class="chat-preview">${escapeHtml(truncate(chat.last_message || 'No messages yet', 50))}</p>
                    <div class="chat-meta">
                        <span class="type-badge ${escapeHtml(userTypeClass)}">${escapeHtml(userTypeLabel)}</span>
                        <span class="status-badge ${escapeHtml(statusClass)}">${escapeHtml(statusLabel)}</span>
                        ${hasUnread ? `<span class="unread-badge">${Number(chat.unread_count)}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    async function selectChat(chatId) {
        state.currentChatId = Number(chatId);
        state.lastMessagesSignature = '';

        const chat = getCurrentChat();
        if (!chat) return;

        state.currentChatStatus = chat.status;

        elements.chatEmptyState.style.display = 'none';
        elements.activeChatContainer.style.display = 'flex';

        const userTypeLabel = chat.user_type || 'User';
        const userTypeClass = normalizeTypeClass(userTypeLabel);

        elements.chatUserAvatar.innerHTML = escapeHtml(getInitials(chat.user_name || 'User'));
        elements.chatUserAvatar.className = `user-avatar-large ${userTypeClass}`;
        elements.chatUserName.textContent = chat.user_name || 'Unknown User';
        elements.chatUserType.textContent = userTypeLabel;
        elements.chatUserType.className = `user-type-badge ${userTypeClass}`;
        elements.chatIdDisplay.textContent = isSiteChat(chat) ? `#${Number(chat.id)}` : 'Direct chat';

        updateActionButtons(chat);
        handleInputChange();
        await loadMessages(chatId);
        startMessagePolling();
        renderChatList();
    }

    function updateActionButtons(chat) {
        const site = isSiteChat(chat);
        const closeLabel = elements.closeChatBtn ? elements.closeChatBtn.querySelector('span') : null;

        if (closeLabel) {
            closeLabel.textContent = site ? 'Close' : 'Archive';
        }

        if (site) {
            const isClaimed = chat.status === 'Assigned';
            const isMine = !!chat.is_mine;

            if (state.isAdmin) {
                setActionsVisibility({ claim: false, close: false, remove: false, input: false, overlay: false });
                return;
            }

            if (isClaimed && isMine) {
                setActionsVisibility({ claim: false, close: true, remove: true, input: true, overlay: false });
                return;
            }

            if (!isClaimed) {
                setActionsVisibility({ claim: true, close: false, remove: false, input: false, overlay: true });
                return;
            }

            setActionsVisibility({ claim: false, close: false, remove: false, input: false, overlay: false });
            return;
        }

        setActionsVisibility({ claim: false, close: true, remove: false, input: true, overlay: false });
    }

    function setActionsVisibility(config) {
        if (elements.claimChatBtn) elements.claimChatBtn.style.display = config.claim ? 'flex' : 'none';
        if (elements.closeChatBtn) elements.closeChatBtn.style.display = config.close ? 'flex' : 'none';
        if (elements.deleteChatBtn) elements.deleteChatBtn.style.display = config.remove ? 'flex' : 'none';
        if (elements.chatInputArea) elements.chatInputArea.style.display = config.input ? 'block' : 'none';
        if (elements.claimOverlay) elements.claimOverlay.style.display = config.overlay ? 'flex' : 'none';
    }

    async function loadMessages(chatId) {
        const parsedChatId = Number(chatId);
        if (state.messagesLoadInFlightForChatId === parsedChatId) return;

        const requestToken = ++state.messagesRequestToken;
        state.messagesLoadInFlightForChatId = parsedChatId;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/getMessages/${parsedChatId}`);
            const data = await response.json();

            if (requestToken !== state.messagesRequestToken || parsedChatId !== state.currentChatId) {
                return;
            }

            if (data.status === 'success') {
                const messages = data.messages || [];
                renderMessages(messages);

                const activeChat = getCurrentChat();
                const unreadCount = Number(activeChat ? activeChat.unread_count : 0);
                if (messages.length > 0 && unreadCount > 0) {
                    markMessagesAsRead(parsedChatId);
                }
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        } finally {
            if (state.messagesLoadInFlightForChatId === parsedChatId) {
                state.messagesLoadInFlightForChatId = null;
            }
        }
    }

    function renderMessages(messages) {
        const currentChat = getCurrentChat();
        if (!currentChat) return;

        const site = isSiteChat(currentChat);
        const signature = getMessagesSignature(messages, site, state.viewerId);

        if (signature === state.lastMessagesSignature) {
            return;
        }

        const shouldAutoScroll = isNearBottom();
        if (!Array.isArray(messages) || messages.length === 0) {
            elements.chatMessages.innerHTML = '<div class="date-divider"><span>Start of conversation</span></div>';
            state.lastMessagesSignature = signature;
            if (shouldAutoScroll) {
                scrollToBottom();
            }
            return;
        }

        let html = '';
        let lastDate = '';

        messages.forEach((msg) => {
            const msgDate = formatDate(msg.created_at);
            if (msgDate !== lastDate) {
                html += `<div class="date-divider"><span>${escapeHtml(msgDate)}</span></div>`;
                lastDate = msgDate;
            }

            const isOutgoing = Number(msg.sender_id || 0) === state.viewerId;
            const canDeleteCurrentChat = canDeleteInCurrentChat();
            const showDelete = canDeleteCurrentChat && (site || isOutgoing);
            const incomingInitial = getInitials(msg.sender_name || 'U');

            html += `
                <div class="message ${isOutgoing ? 'outgoing' : 'incoming'}">
                    <div class="message-avatar">${isOutgoing ? '<i class="fas fa-life-ring"></i>' : escapeHtml(incomingInitial)}</div>
                    <div class="message-content">
                        <p class="message-text">${escapeHtml(msg.message || '')}</p>
                        <span class="message-time">${escapeHtml(formatTime(msg.created_at))}</span>
                        ${showDelete ? `<button type="button" class="message-delete-btn" data-message-id="${Number(msg.id || 0)}">Delete</button>` : ''}
                    </div>
                </div>
            `;
        });

        elements.chatMessages.innerHTML = html;
        state.lastMessagesSignature = signature;

        if (shouldAutoScroll) {
            scrollToBottom();
        }
    }

    async function sendMessage() {
        const message = elements.messageInput.value.trim();
        if (!message || !state.currentChatId) {
            return;
        }

        elements.sendMessageBtn.disabled = true;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/sendMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: state.currentChatId,
                    message
                })
            });

            const parsed = await parseJsonResponse(response);
            const data = parsed.data || {};
            const isSuccess = isSendMessageSuccess(data, parsed.raw, response.ok);

            if (!isSuccess) {
                throw new Error((data && data.message) || 'Failed to send message');
            }

            elements.messageInput.value = '';
            elements.messageInput.style.height = 'auto';
            if (elements.charCount) {
                elements.charCount.textContent = `0/${MAX_MESSAGE_LENGTH}`;
            }

            state.lastMessagesSignature = '';
            await loadMessages(state.currentChatId);
        } catch (error) {
            showToast(error.message || 'Error sending message', 'error');
        } finally {
            elements.sendMessageBtn.disabled = false;
        }
    }

    async function claimChat() {
        const chat = getCurrentChat();
        if (!chat || !isSiteChat(chat)) {
            return;
        }

        try {
            const response = await fetch(`${URL_ROOT}/helpc/claimChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: state.currentChatId })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message || 'Failed to claim chat');
            }

            const index = state.chatsList.findIndex((item) => Number(item.id) === Number(state.currentChatId));
            if (index !== -1) {
                state.chatsList[index].status = 'Assigned';
                state.chatsList[index].is_mine = true;
            }

            updateChatCounts();
            renderChatList();

            const active = getCurrentChat();
            if (active) {
                updateActionButtons(active);
            }

            showToast('Chat claimed successfully', 'success');
        } catch (error) {
            showToast(error.message || 'Error claiming chat', 'error');
        }
    }

    async function closeChat() {
        const chat = getCurrentChat();
        if (!chat) return;

        const confirmText = isSiteChat(chat)
            ? 'Are you sure you want to close this site support chat?'
            : 'Archive this direct conversation?';

        if (!window.confirm(confirmText)) {
            return;
        }

        try {
            const response = await fetch(`${URL_ROOT}/helpc/closeChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: state.currentChatId })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message || 'Failed to close chat');
            }

            state.chatsList = state.chatsList.filter((item) => Number(item.id) !== Number(state.currentChatId));
            state.lastChatListSignature = getChatListSignature(state.chatsList);
            resetActiveChatUI();
            updateChatCounts();
            renderChatList();
            showToast('Chat closed', 'success');
        } catch (error) {
            showToast(error.message || 'Error closing chat', 'error');
        }
    }

    async function deleteChat() {
        const chat = getCurrentChat();
        if (!chat || !isSiteChat(chat) || !canDeleteInCurrentChat()) {
            return;
        }

        if (!window.confirm('Delete this chat and all messages?')) {
            return;
        }

        try {
            const response = await fetch(`${URL_ROOT}/helpc/deleteChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: state.currentChatId })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message || 'Could not delete chat');
            }

            state.chatsList = state.chatsList.filter((item) => Number(item.id) !== Number(state.currentChatId));
            state.lastChatListSignature = getChatListSignature(state.chatsList);
            resetActiveChatUI();
            updateChatCounts();
            renderChatList();
            showToast('Chat deleted', 'success');
        } catch (error) {
            showToast(error.message || 'Error deleting chat', 'error');
        }
    }

    async function deleteMessage(messageId) {
        if (!canDeleteInCurrentChat()) {
            return;
        }

        if (!window.confirm('Delete this message?')) {
            return;
        }

        try {
            const response = await fetch(`${URL_ROOT}/helpc/deleteMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message_id: Number(messageId) })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message || 'Could not delete message');
            }

            state.lastMessagesSignature = '';
            await loadMessages(state.currentChatId);
            showToast('Message deleted', 'success');
        } catch (error) {
            showToast(error.message || 'Error deleting message', 'error');
        }
    }

    async function markMessagesAsRead(chatId) {
        try {
            await fetch(`${URL_ROOT}/helpc/markAsRead`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: Number(chatId) })
            });

            const chat = state.chatsList.find((item) => Number(item.id) === Number(chatId));
            if (chat && Number(chat.unread_count || 0) > 0) {
                chat.unread_count = 0;
                renderChatList();
            }
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    function startChatListPolling() {
        stopChatListPolling();
        state.chatListPollTimer = window.setInterval(loadChatList, POLL_INTERVAL);
    }

    function stopChatListPolling() {
        if (state.chatListPollTimer) {
            clearInterval(state.chatListPollTimer);
            state.chatListPollTimer = null;
        }
    }

    function startMessagePolling() {
        stopMessagePolling();
        state.messagesPollTimer = window.setInterval(() => {
            if (state.currentChatId) {
                loadMessages(state.currentChatId);
            }
        }, POLL_INTERVAL);
    }

    function stopMessagePolling() {
        if (state.messagesPollTimer) {
            clearInterval(state.messagesPollTimer);
            state.messagesPollTimer = null;
        }
    }

    function resetActiveChatUI() {
        state.currentChatId = null;
        state.currentChatStatus = null;
        state.lastMessagesSignature = '';

        elements.activeChatContainer.style.display = 'none';
        elements.chatEmptyState.style.display = 'flex';
        elements.messageInput.value = '';
        elements.messageInput.style.height = 'auto';
        if (elements.charCount) {
            elements.charCount.textContent = `0/${MAX_MESSAGE_LENGTH}`;
        }
        elements.sendMessageBtn.disabled = true;

        stopMessagePolling();
    }

    function getCurrentChat() {
        if (!state.currentChatId) return null;
        return state.chatsList.find((chat) => Number(chat.id) === Number(state.currentChatId)) || null;
    }

    function isSiteChat(chat) {
        if (!chat) return false;
        if (typeof chat.is_site_chat !== 'undefined') {
            return !!chat.is_site_chat;
        }
        return String(chat.target_type || '').toLowerCase() === 'site';
    }

    function canDeleteInCurrentChat() {
        const chat = getCurrentChat();
        if (!chat) return false;

        if (isSiteChat(chat)) {
            return !!chat.is_mine;
        }

        return true;
    }

    function scrollToBottom() {
        elements.chatMessagesContainer.scrollTop = elements.chatMessagesContainer.scrollHeight;
    }

    function showToast(message, type = 'info') {
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }

        if (!elements.toastContainer) return;

        const iconMap = {
            success: 'fa-check',
            error: 'fa-times',
            info: 'fa-info'
        };

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${iconMap[type] || iconMap.info}"></i>
            </div>
            <span class="toast-message">${escapeHtml(message || '')}</span>
            <button class="toast-close" type="button" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        `;

        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => toast.remove());
        }

        elements.toastContainer.appendChild(toast);
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    function getChatListSignature(chats) {
        return JSON.stringify((chats || []).map((chat) => ({
            id: Number(chat.id || 0),
            status: chat.status || '',
            unread_count: Number(chat.unread_count || 0),
            is_mine: !!chat.is_mine,
            updated_at: chat.updated_at || '',
            last_message: chat.last_message || '',
            target_type: chat.target_type || '',
            user_name: chat.user_name || '',
            user_type: chat.user_type || ''
        })));
    }

    function getMessagesSignature(messages, siteChat, viewerId) {
        return JSON.stringify({
            siteChat,
            viewerId,
            messages: (messages || []).map((msg) => ({
                id: Number(msg.id || 0),
                sender_id: Number(msg.sender_id || 0),
                sender_type: msg.sender_type || '',
                message: msg.message || '',
                created_at: msg.created_at || ''
            }))
        });
    }

    function isNearBottom() {
        const threshold = 32;
        const container = elements.chatMessagesContainer;
        return (container.scrollHeight - container.scrollTop - container.clientHeight) <= threshold;
    }

    function truncate(text, length) {
        const value = String(text || '');
        if (value.length <= length) {
            return value;
        }
        return `${value.substring(0, length)}...`;
    }

    function formatTimeAgo(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
        return date.toLocaleDateString();
    }

    function formatDate(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        if (date.toDateString() === today.toDateString()) return 'Today';
        if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function formatTime(dateString) {
        if (!dateString) return '';
        return new Date(dateString).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getInitials(name) {
        const value = String(name || '').trim();
        if (!value) {
            return 'U';
        }

        const parts = value.split(/\s+/).filter(Boolean);
        if (parts.length === 1) {
            return parts[0].slice(0, 2).toUpperCase();
        }

        return `${parts[0].charAt(0)}${parts[1].charAt(0)}`.toUpperCase();
    }

    function normalizeTypeClass(typeLabel) {
        const value = String(typeLabel || '').toLowerCase();
        if (value === 'traveller' || value === 'tourist') return 'traveller';
        if (value === 'guide') return 'guide';
        if (value === 'driver') return 'driver';
        if (value === 'moderator' || value === 'site_moderator') return 'moderator';
        if (value === 'admin') return 'admin';
        return 'user';
    }

    function mapAccountType(accountType) {
        const value = String(accountType || '').toLowerCase();
        if (value === 'tourist') return 'Traveller';
        if (value === 'guide') return 'Guide';
        if (value === 'driver') return 'Driver';
        if (value === 'site_moderator') return 'Moderator';
        if (value === 'admin') return 'Admin';
        return 'User';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
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

    function isSendMessageSuccess(data, raw, responseOk) {
        if (data && data.status === 'success') return true;
        if (data && data.message_id) return true;

        const rawText = String(raw || '');
        if (/"status"\s*:\s*"success"/i.test(rawText)) return true;
        if (/"message_id"\s*:\s*\d+/i.test(rawText)) return true;

        return !!(responseOk && (!data || !data.status));
    }

    function tryParseJson(text) {
        try {
            return JSON.parse(text);
        } catch (error) {
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.addEventListener('beforeunload', () => {
        stopChatListPolling();
        stopMessagePolling();
    });
})();