/**
 * Help Widget JavaScript
 * Real-time chat with moderators
 * Used by travellerDash, guideDash, driverDash
 */
(function () {
    'use strict';

    // Configuration
    const URL_ROOT = 'http://localhost/test';
    const POLL_INTERVAL = 3000;

    // DOM Elements
    const helpBtn = document.getElementById('helpBtn');
    const helpPopup = document.getElementById('helpOptionsPopup');
    const openChatBtn = document.getElementById('openChatBtn');
    const chatWidget = document.getElementById('chatWidget');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatInput = document.getElementById('chatInput');
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatMessages = document.getElementById('chatMessages');

    // State
    let popupOpen = false;
    let currentChatId = null;
    let pollInterval = null;
    let lastMessageId = 0;

    // Toggle help popup (only when floating help button exists)
    if (helpBtn && helpPopup) {
        helpBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            // Close chat widget if open
            if (chatWidget) chatWidget.classList.remove('active');
            popupOpen = !popupOpen;
            helpPopup.classList.toggle('active', popupOpen);
        });

        // Close popup when clicking outside
        document.addEventListener('click', function (e) {
            if (!helpBtn.contains(e.target) && !helpPopup.contains(e.target)) {
                helpPopup.classList.remove('active');
                popupOpen = false;
            }
        });
    }

    // Open chat widget from popup button
    if (openChatBtn && chatWidget) {
        openChatBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (helpPopup) helpPopup.classList.remove('active');
            popupOpen = false;
            chatWidget.classList.add('active');
            if (chatInput) chatInput.focus();

            // Initialize chat
            initializeChat();
        });
    }

    // Close chat widget
    if (closeChatBtn && chatWidget) {
        closeChatBtn.addEventListener('click', function () {
            chatWidget.classList.remove('active');
            stopPolling();
        });
    }

    // Initialize chat - get or create chat session
    async function initializeChat() {
        try {
            // First try to get existing active chat
            const response = await fetch(`${URL_ROOT}/helpc/getUserActiveChat`);
            const data = await response.json();

            if (data.status === 'success' && data.chat) {
                // Existing chat found
                currentChatId = data.chat.id;
                renderMessages(data.messages || []);
                startPolling();
            } else if (data.status === 'no_chat') {
                // No active chat, need to start one
                await startNewChat();
            }
        } catch (error) {
            console.error('Error initializing chat:', error);
            showSystemMessage('Unable to connect to support. Please try again.');
        }
    }

    // Start a new chat
    async function startNewChat() {
        try {
            const response = await fetch(`${URL_ROOT}/helpc/startChat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();

            if (data.status === 'success' && data.chat) {
                currentChatId = data.chat.id;
                clearMessages();
                addWelcomeMessage();
                startPolling();
            } else {
                showSystemMessage('Unable to start chat. Please try again.');
            }
        } catch (error) {
            console.error('Error starting chat:', error);
            showSystemMessage('Unable to start chat. Please try again.');
        }
    }

    // Send chat message
    async function sendChatMessage() {
        const text = chatInput.value.trim();
        if (!text || !currentChatId) return;

        // Optimistically show the message
        addUserMessage(text);
        chatInput.value = '';

        try {
            const response = await fetch(`${URL_ROOT}/helpc/sendMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: currentChatId,
                    message: text
                })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                showSystemMessage('Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            showSystemMessage('Failed to send message. Please try again.');
        }
    }

    // Poll for new messages
    async function pollMessages() {
        if (!currentChatId) return;

        try {
            const response = await fetch(`${URL_ROOT}/helpc/getMessages/${currentChatId}`);
            const data = await response.json();

            if (data.status === 'success') {
                updateMessages(data.messages || []);
            }
        } catch (error) {
            console.error('Error polling messages:', error);
        }
    }

    // Update messages with new ones
    function updateMessages(messages) {
        if (!messages.length) return;

        // Find new messages
        const newMessages = messages.filter(m => parseInt(m.id) > lastMessageId);

        newMessages.forEach(msg => {
            // Skip if it's our own message (already shown optimistically)
            if (msg.sender_type !== 'Moderator') {
                lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
                return;
            }

            addModeratorMessage(msg.message, msg.sender_name, msg.created_at);
            lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
        });
    }

    // Render messages from server
    function renderMessages(messages) {
        clearMessages();
        addWelcomeMessage();

        messages.forEach(msg => {
            if (msg.sender_type === 'Moderator') {
                addModeratorMessage(msg.message, msg.sender_name, msg.created_at);
            } else {
                addUserMessage(msg.message, msg.created_at);
            }
            lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
        });
    }

    // Clear messages
    function clearMessages() {
        if (chatMessages) {
            chatMessages.innerHTML = '<div class="date-divider"><span>Today</span></div>';
        }
    }

    // Add welcome message
    function addWelcomeMessage() {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message support-message';
        msgDiv.innerHTML = `
            <div class="message-content">
                <p>Hello there! 👋 Welcome to Tripingoo Travel Support. How can we help you today?</p>
            </div>
            <span class="message-time">Just now</span>
        `;
        chatMessages.appendChild(msgDiv);
    }

    // Add user message
    function addUserMessage(text, timestamp = null) {
        const time = timestamp ? formatTime(timestamp) : new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message user-message';
        msgDiv.innerHTML = `
            <div class="message-content"><p>${escapeHtml(text)}</p></div>
            <span class="message-time">${time}</span>
        `;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add moderator message
    function addModeratorMessage(text, senderName = 'Support', timestamp = null) {
        const time = timestamp ? formatTime(timestamp) : new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message support-message';
        msgDiv.innerHTML = `
            <div class="message-content">
                <p class="sender-name">${escapeHtml(senderName)}</p>
                <p>${escapeHtml(text)}</p>
            </div>
            <span class="message-time">${time}</span>
        `;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Play notification sound if available
        playNotificationSound();
    }

    // Show system message
    function showSystemMessage(text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message system-message';
        msgDiv.innerHTML = `
            <div class="message-content system"><p>${escapeHtml(text)}</p></div>
        `;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Start polling for messages
    function startPolling() {
        stopPolling();
        pollInterval = setInterval(pollMessages, POLL_INTERVAL);
    }

    // Stop polling
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Format time from timestamp
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Play notification sound
    function playNotificationSound() {
        try {
            // Simple beep using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 440;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.1;

            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            // Audio not supported, ignore
        }
    }

    // Bind events
    if (chatSendBtn) {
        chatSendBtn.addEventListener('click', sendChatMessage);
    }

    if (chatInput) {
        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendChatMessage();
            }
        });
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', stopPolling);
})();

