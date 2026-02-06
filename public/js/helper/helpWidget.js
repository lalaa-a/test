/**
 * Help Widget JavaScript
 * Used by travellerDash, guideDash, driverDash
 */
(function () {
    const helpBtn = document.getElementById('helpBtn');
    const helpPopup = document.getElementById('helpOptionsPopup');
    const openChatBtn = document.getElementById('openChatBtn');
    const chatWidget = document.getElementById('chatWidget');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatInput = document.getElementById('chatInput');
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatMessages = document.getElementById('chatMessages');

    let popupOpen = false;

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
        });
    }

    // Close chat widget
    if (closeChatBtn && chatWidget) {
        closeChatBtn.addEventListener('click', function () {
            chatWidget.classList.remove('active');
        });
    }
    

    // Send chat message
    function sendChatMessage() {
        const text = chatInput.value.trim();
        if (text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'message user-message';
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            msgDiv.innerHTML = `
                <div class="message-content"><p>${text}</p></div>
                <span class="message-time">${time}</span>
            `;
            chatMessages.appendChild(msgDiv);
            chatInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Simulate reply
            setTimeout(function () {
                const replyDiv = document.createElement('div');
                replyDiv.className = 'message support-message';
                replyDiv.innerHTML = `
                    <div class="message-content"><p>Thanks for your message! Our team will respond shortly.</p></div>
                    <span class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                `;
                chatMessages.appendChild(replyDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 1500);
        }
    }

    chatSendBtn && chatSendBtn.addEventListener('click', sendChatMessage);
    chatInput && chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendChatMessage();
    });
})();
