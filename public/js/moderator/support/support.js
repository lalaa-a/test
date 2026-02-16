/**
 * Support Center JavaScript
 * Real-time chat functionality for moderators
 */

(function () {
    'use strict';

    // Configuration
    const URL_ROOT = 'http://localhost/test';
    const POLL_INTERVAL = 3000; // Poll every 3 seconds
    const MAX_MESSAGE_LENGTH = 500;

    // State
    let currentChatId = null;
    let currentChatStatus = null;
    let chatsList = [];
    let currentFilter = 'all';
    let pollIntervals = {
        chatList: null,
        messages: null
    };
    let isAdmin = false; // Will be set from session

    // DOM Elements
    const elements = {
        // Chat List
        chatList: document.getElementById('supportChatList'),
        openChatsCount: document.getElementById('openChatsCount'),
        myChatsCount: document.getElementById('myChatsCount'),
        refreshBtn: document.getElementById('refreshChatsBtn'),

        // Chat Window
        chatEmptyState: document.getElementById('chatEmptyState'),
        activeChatContainer: document.getElementById('activeChatContainer'),
        chatUserAvatar: document.getElementById('chatUserAvatar'),
        chatUserName: document.getElementById('chatUserName'),
        chatUserType: document.getElementById('chatUserType'),
        chatIdDisplay: document.getElementById('chatIdDisplay'),
        chatMessages: document.getElementById('chatMessages'),
        chatMessagesContainer: document.getElementById('chatMessagesContainer'),
        typingIndicator: document.getElementById('typingIndicator'),

        // Input
        chatInputArea: document.getElementById('chatInputArea'),
        messageInput: document.getElementById('messageInput'),
        sendMessageBtn: document.getElementById('sendMessageBtn'),
        charCount: document.getElementById('charCount'),

        // Actions
        claimChatBtn: document.getElementById('claimChatBtn'),
        closeChatBtn: document.getElementById('closeChatBtn'),
        claimOverlay: document.getElementById('claimOverlay'),
        claimOverlayBtn: document.getElementById('claimOverlayBtn'),

        // Toast
        toastContainer: document.getElementById('toastContainer')
    };

    // Initialize
    function init() {
        loadChatList();
        bindEvents();
        startChatListPolling();
    }

    // Bind Events
    function bindEvents() {
        // Filter tabs
        document.querySelectorAll('.panel-tab').forEach(tab => {
            tab.addEventListener('click', () => handleFilterChange(tab));
        });

        // Refresh button
        if (elements.refreshBtn) {
            elements.refreshBtn.addEventListener('click', () => {
                elements.refreshBtn.classList.add('loading');
                loadChatList().finally(() => {
                    elements.refreshBtn.classList.remove('loading');
                });
            });
        }

        // Message input
        if (elements.messageInput) {
            elements.messageInput.addEventListener('input', handleInputChange);
            elements.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        // Send button
        if (elements.sendMessageBtn) {
            elements.sendMessageBtn.addEventListener('click', sendMessage);
        }

        // Claim buttons
        if (elements.claimChatBtn) {
            elements.claimChatBtn.addEventListener('click', claimChat);
        }
        if (elements.claimOverlayBtn) {
            elements.claimOverlayBtn.addEventListener('click', claimChat);
        }

        // Close chat button
        if (elements.closeChatBtn) {
            elements.closeChatBtn.addEventListener('click', closeChat);
        }
    }

    // Handle Filter Change
    function handleFilterChange(tab) {
        document.querySelectorAll('.panel-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        currentFilter = tab.dataset.filter;
        renderChatList();
    }

    // Handle Input Change
    function handleInputChange() {
        const length = elements.messageInput.value.length;
        elements.charCount.textContent = `${length}/${MAX_MESSAGE_LENGTH}`;

        // Auto-resize textarea
        elements.messageInput.style.height = 'auto';
        elements.messageInput.style.height = Math.min(elements.messageInput.scrollHeight, 120) + 'px';

        // Disable send if empty or too long
        elements.sendMessageBtn.disabled = length === 0 || length > MAX_MESSAGE_LENGTH;
    }

    // Load Chat List
    async function loadChatList() {
        try {
            const response = await fetch(`${URL_ROOT}/helpc/getChatsForModerator`);
            const data = await response.json();

            if (data.status === 'success') {
                chatsList = data.chats || [];
                isAdmin = data.isAdmin || false;
                updateChatCounts();
                renderChatList();
            } else if (data.status === 'error') {
                // Handle errors like "not logged in"
                if (elements.chatList) {
                    elements.chatList.innerHTML = `
                        <div class="empty-list-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <h4>${data.message || 'Unable to load chats'}</h4>
                            <p>Please log in as a moderator to access the support center.</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error loading chat list:', error);
            if (elements.chatList) {
                elements.chatList.innerHTML = `
                    <div class="empty-list-state">
                        <i class="fas fa-wifi"></i>
                        <h4>Connection Error</h4>
                        <p>Unable to connect to the server. Please try again.</p>
                    </div>
                `;
            }
        }
    }

    // Update Chat Counts
    function updateChatCounts() {
        const openCount = chatsList.filter(c => c.status === 'Open').length;
        const myCount = chatsList.filter(c => c.status === 'Assigned' && c.is_mine).length;

        if (elements.openChatsCount) elements.openChatsCount.textContent = openCount;
        if (elements.myChatsCount) elements.myChatsCount.textContent = myCount;
    }

    // Render Chat List
    function renderChatList() {
        if (!elements.chatList) return;

        let filteredChats = chatsList;

        if (currentFilter === 'open') {
            filteredChats = chatsList.filter(c => c.status === 'Open');
        } else if (currentFilter === 'mine') {
            filteredChats = chatsList.filter(c => c.status === 'Assigned' && c.is_mine);
        }

        if (filteredChats.length === 0) {
            elements.chatList.innerHTML = `
                <div class="empty-list-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No chats found</h4>
                    <p>${currentFilter === 'mine' ? 'You haven\'t claimed any chats yet' : 'No open support chats at the moment'}</p>
                </div>
            `;
            return;
        }

        elements.chatList.innerHTML = filteredChats.map(chat => createChatItemHTML(chat)).join('');

        // Bind click events
        elements.chatList.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', () => selectChat(parseInt(item.dataset.chatId)));
        });
    }

    // Create Chat Item HTML
    function createChatItemHTML(chat) {
        const userType = chat.user_type.toLowerCase();
        const initial = chat.user_name ? chat.user_name.charAt(0).toUpperCase() : '?';
        const timeAgo = formatTimeAgo(chat.updated_at || chat.created_at);
        const preview = chat.last_message || 'No messages yet';
        const isActive = currentChatId === parseInt(chat.id);
        const hasUnread = parseInt(chat.unread_count || 0) > 0;

        return `
            <div class="chat-item ${isActive ? 'active' : ''} ${hasUnread ? 'unread' : ''}" data-chat-id="${chat.id}">
                <div class="chat-avatar ${userType}">
                    ${initial}
                    <span class="status-dot ${chat.status.toLowerCase()}"></span>
                </div>
                <div class="chat-info">
                    <div class="chat-info-header">
                        <span class="chat-name">${escapeHtml(chat.user_name || 'Unknown User')}</span>
                        <span class="chat-time">${timeAgo}</span>
                    </div>
                    <p class="chat-preview">${escapeHtml(truncate(preview, 40))}</p>
                    <div class="chat-meta">
                        <span class="type-badge ${userType}">${chat.user_type}</span>
                        <span class="status-badge ${chat.status.toLowerCase()}">${chat.status}</span>
                        ${hasUnread ? `<span class="unread-badge">${chat.unread_count}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Select Chat
    async function selectChat(chatId) {
        currentChatId = chatId;
        const chat = chatsList.find(c => parseInt(c.id) === chatId);

        if (!chat) return;

        currentChatStatus = chat.status;

        // Update UI
        elements.chatEmptyState.style.display = 'none';
        elements.activeChatContainer.style.display = 'flex';

        // Update header
        const userType = chat.user_type.toLowerCase();
        elements.chatUserAvatar.innerHTML = `<i class="fas fa-user"></i>`;
        elements.chatUserAvatar.className = `user-avatar-large ${userType}`;
        elements.chatUserName.textContent = chat.user_name || 'Unknown User';
        elements.chatUserType.textContent = chat.user_type;
        elements.chatUserType.className = `user-type-badge ${userType}`;
        elements.chatIdDisplay.textContent = `#${chatId}`;

        // Update action buttons based on status and role
        updateActionButtons(chat);

        // Load messages
        await loadMessages(chatId);

        // Start message polling
        startMessagePolling();

        // Update chat list to show active state
        renderChatList();
    }

    // Update Action Buttons
    function updateActionButtons(chat) {
        const isClaimed = chat.status === 'Assigned';
        const isMine = chat.is_mine;

        // Admin view - no messaging
        if (isAdmin) {
            elements.claimChatBtn.style.display = 'none';
            elements.closeChatBtn.style.display = 'none';
            elements.chatInputArea.style.display = 'none';
            elements.claimOverlay.style.display = 'none';
            return;
        }

        if (isClaimed && isMine) {
            // Chat is claimed by current moderator
            elements.claimChatBtn.style.display = 'none';
            elements.closeChatBtn.style.display = 'flex';
            elements.chatInputArea.style.display = 'block';
            elements.claimOverlay.style.display = 'none';
        } else if (!isClaimed) {
            // Chat is open - show claim overlay
            elements.claimChatBtn.style.display = 'flex';
            elements.closeChatBtn.style.display = 'none';
            elements.chatInputArea.style.display = 'none';
            elements.claimOverlay.style.display = 'flex';
        } else {
            // Claimed by another moderator (shouldn't see this)
            elements.claimChatBtn.style.display = 'none';
            elements.closeChatBtn.style.display = 'none';
            elements.chatInputArea.style.display = 'none';
            elements.claimOverlay.style.display = 'none';
        }
    }

    // Load Messages
    async function loadMessages(chatId) {
        try {
            const response = await fetch(`${URL_ROOT}/helpc/getMessages/${chatId}`);
            const data = await response.json();

            if (data.status === 'success') {
                renderMessages(data.messages || []);

                // Mark as read
                if (data.messages && data.messages.length > 0) {
                    markMessagesAsRead(chatId);
                }
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    // Render Messages
    function renderMessages(messages) {
        if (!elements.chatMessages) return;

        if (messages.length === 0) {
            elements.chatMessages.innerHTML = `
                <div class="date-divider"><span>Start of conversation</span></div>
            `;
            return;
        }

        let html = '';
        let lastDate = '';

        messages.forEach(msg => {
            const msgDate = formatDate(msg.created_at);
            if (msgDate !== lastDate) {
                html += `<div class="date-divider"><span>${msgDate}</span></div>`;
                lastDate = msgDate;
            }

            const isOutgoing = msg.sender_type === 'Moderator';
            const initial = msg.sender_name ? msg.sender_name.charAt(0).toUpperCase() : '?';
            const time = formatTime(msg.created_at);

            html += `
                <div class="message ${isOutgoing ? 'outgoing' : 'incoming'}">
                    <div class="message-avatar">${isOutgoing ? '<i class="fas fa-headset"></i>' : initial}</div>
                    <div class="message-content">
                        <p class="message-text">${escapeHtml(msg.message)}</p>
                        <span class="message-time">${time}</span>
                    </div>
                </div>
            `;
        });

        elements.chatMessages.innerHTML = html;
        scrollToBottom();
    }

    // Scroll to Bottom
    function scrollToBottom() {
        if (elements.chatMessagesContainer) {
            elements.chatMessagesContainer.scrollTop = elements.chatMessagesContainer.scrollHeight;
        }
    }

    // Send Message
    async function sendMessage() {
        const message = elements.messageInput.value.trim();
        if (!message || !currentChatId || isAdmin) return;

        elements.sendMessageBtn.disabled = true;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/sendMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: currentChatId,
                    message: message
                })
            });
            const data = await response.json();

            if (data.status === 'success') {
                elements.messageInput.value = '';
                elements.charCount.textContent = `0/${MAX_MESSAGE_LENGTH}`;
                elements.messageInput.style.height = 'auto';
                await loadMessages(currentChatId);
                showToast('Message sent successfully', 'success');
            } else {
                showToast(data.message || 'Failed to send message', 'error');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            showToast('Error sending message', 'error');
        } finally {
            elements.sendMessageBtn.disabled = false;
        }
    }

    // Claim Chat
    async function claimChat() {
        if (!currentChatId || isAdmin) return;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/claimChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: currentChatId })
            });
            const data = await response.json();

            if (data.status === 'success') {
                showToast('Chat claimed successfully! You can now respond.', 'success');

                // Update local state
                currentChatStatus = 'Assigned';

                // Update the chat in the list
                const chatIndex = chatsList.findIndex(c => parseInt(c.id) === currentChatId);
                if (chatIndex !== -1) {
                    chatsList[chatIndex].status = 'Assigned';
                    chatsList[chatIndex].is_mine = true;
                }

                // Refresh UI
                updateChatCounts();
                renderChatList();

                const chat = chatsList.find(c => parseInt(c.id) === currentChatId);
                if (chat) updateActionButtons(chat);
            } else {
                showToast(data.message || 'Failed to claim chat', 'error');
            }
        } catch (error) {
            console.error('Error claiming chat:', error);
            showToast('Error claiming chat', 'error');
        }
    }

    // Close Chat
    async function closeChat() {
        if (!currentChatId || isAdmin) return;

        if (!confirm('Are you sure you want to close this chat?')) return;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/closeChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: currentChatId })
            });
            const data = await response.json();

            if (data.status === 'success') {
                showToast('Chat closed successfully', 'success');

                // Remove from list
                chatsList = chatsList.filter(c => parseInt(c.id) !== currentChatId);

                // Reset UI
                currentChatId = null;
                currentChatStatus = null;
                elements.activeChatContainer.style.display = 'none';
                elements.chatEmptyState.style.display = 'flex';

                // Refresh
                updateChatCounts();
                renderChatList();
                stopMessagePolling();
            } else {
                showToast(data.message || 'Failed to close chat', 'error');
            }
        } catch (error) {
            console.error('Error closing chat:', error);
            showToast('Error closing chat', 'error');
        }
    }

    // Mark Messages as Read
    async function markMessagesAsRead(chatId) {
        try {
            await fetch(`${URL_ROOT}/helpc/markAsRead`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_id: chatId })
            });

            // Update local count
            const chat = chatsList.find(c => parseInt(c.id) === chatId);
            if (chat) chat.unread_count = 0;
            renderChatList();
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    // Polling Functions
    function startChatListPolling() {
        if (pollIntervals.chatList) clearInterval(pollIntervals.chatList);
        pollIntervals.chatList = setInterval(loadChatList, POLL_INTERVAL);
    }

    function startMessagePolling() {
        if (pollIntervals.messages) clearInterval(pollIntervals.messages);
        pollIntervals.messages = setInterval(() => {
            if (currentChatId) loadMessages(currentChatId);
        }, POLL_INTERVAL);
    }

    function stopMessagePolling() {
        if (pollIntervals.messages) {
            clearInterval(pollIntervals.messages);
            pollIntervals.messages = null;
        }
    }

    // Toast Notification
    function showToast(message, type = 'info') {
        if (!elements.toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const iconMap = {
            success: 'fa-check',
            error: 'fa-times',
            info: 'fa-info'
        };

        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${iconMap[type]}"></i>
            </div>
            <span class="toast-message">${escapeHtml(message)}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        elements.toastContainer.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Utility Functions
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function truncate(text, length) {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
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
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatTime(dateString) {
        if (!dateString) return '';
        return new Date(dateString).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (pollIntervals.chatList) clearInterval(pollIntervals.chatList);
        if (pollIntervals.messages) clearInterval(pollIntervals.messages);
    });

})();
