const URL_ROOT_MOD = 'http://localhost/test'; // Adjust or inject
let currentChatId = null;
let adminPollInterval = null;
let openChatsList = [];

document.addEventListener('DOMContentLoaded', () => {
    // Initial Load
    loadOpenChats();
    // Poll for new open chats
    setInterval(loadOpenChats, 5000);

    // Handle Input
    const input = document.getElementById('adminChatInput');
    if (input) {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendAdminMessage();
        });
    }
});

function loadOpenChats() {
    fetch(`${URL_ROOT_MOD}/helpc/getOpenChats`)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                openChatsList = data.chats;
                renderChatList(data.chats);
            }
        })
        .catch(err => console.error(err));
}

function renderChatList(chats) {
    const listContainer = document.getElementById('openChatsList');
    listContainer.innerHTML = '';

    if (chats.length === 0) {
        listContainer.innerHTML = '<div class="p-3 text-center text-muted">No open chats</div>';
        return;
    }

    chats.forEach(chat => {
        const item = document.createElement('div');
        item.className = 'chat-list-item ' + (currentChatId === chat.id ? 'active' : '');
        item.style.padding = '10px';
        item.style.borderBottom = '1px solid #eee';
        item.style.cursor = 'pointer';
        item.style.backgroundColor = currentChatId === chat.id ? '#e9ecef' : 'white';

        // Format time
        const time = new Date(chat.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        item.innerHTML = `
            <div style="font-weight: bold;">${chat.user_type} #${chat.user_id}</div>
            <div style="font-size: 0.8rem; color: #888;">${time}</div>
            <div style="font-size: 0.8rem; color: ${chat.status === 'Assigned' ? 'green' : 'orange'};">
                ${chat.status} ${chat.assigned_moderator_id ? '(Assigned)' : ''}
            </div>
        `;

        item.onclick = () => selectChat(chat.id, chat.status, chat.assigned_moderator_id);
        listContainer.appendChild(item);
    });
}

function selectChat(chatId, status, assignedId) {
    currentChatId = chatId;

    // Highlight in list
    renderChatList(openChatsList);

    // Update Header
    document.getElementById('activeChatHeader').textContent = `Chat #${chatId} (${status})`;

    const inputArea = document.getElementById('adminChatInputArea');
    const claimOverlay = document.getElementById('claimChatOverlay');

    // Reset Views
    inputArea.style.display = 'none';
    claimOverlay.style.display = 'none';

    if (status === 'Open') {
        claimOverlay.style.display = 'flex';
    } else if (status === 'Assigned') {
        inputArea.style.display = 'block';
        claimOverlay.style.display = 'none';
    }

    // Load Messages
    loadChatMessages(chatId);

    // Clear and restart message polling
    if (adminPollInterval) clearInterval(adminPollInterval);
    adminPollInterval = setInterval(() => loadChatMessages(chatId), 3000);
}

function loadChatMessages(chatId) {
    if (!chatId) return;

    fetch(`${URL_ROOT_MOD}/helpc/getMessages/${chatId}`)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                renderChatMessages(data.messages);
            }
        });
}

function renderChatMessages(messages) {
    const container = document.getElementById('adminChatMessages');
    container.innerHTML = '';

    // DEBUG: Log all messages to console for debugging
    console.log('=== Rendering Messages ===');
    console.log('Total messages:', messages.length);

    messages.forEach(msg => {
        // DEBUG: Log each message details
        console.log(`MSG #${msg.id}: sender_id=${msg.sender_id}, sender_type="${msg.sender_type}", sender_name="${msg.sender_name}", message="${msg.message}"`);

        const isMe = msg.sender_type === 'Moderator';
        const align = isMe ? 'flex-end' : 'flex-start';
        const bg = isMe ? '#006A71' : 'white';
        const color = isMe ? 'white' : 'black';
        const borderColor = isMe ? '#006A71' : '#ddd';

        const msgDiv = document.createElement('div');
        msgDiv.style.display = 'flex';
        msgDiv.style.justifyContent = align;
        msgDiv.style.marginBottom = '10px';

        // Add sender type badge for clarity (especially helpful when names are the same)
        const senderBadge = isMe ?
            '<span style="font-size: 0.65rem; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; margin-bottom: 4px; display: inline-block;">💬 You (Moderator)</span>' :
            `<span style="font-size: 0.65rem; background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px; margin-bottom: 4px; display: inline-block;">👤 ${msg.sender_name} (${msg.sender_type})</span>`;

        msgDiv.innerHTML = `
            <div style="background: ${bg}; color: ${color}; padding: 8px 12px; border-radius: 8px; max-width: 70%; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid ${borderColor};">
                ${senderBadge}
                <div style="font-size: 0.95rem;">${msg.message}</div>
                <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 4px; text-align: right;">
                    ${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>
        `;
        container.appendChild(msgDiv);
    });

    console.log('=== End Messages ===');
    container.scrollTop = container.scrollHeight;
}

function claimChat() {
    if (!currentChatId) return;

    fetch(`${URL_ROOT_MOD}/helpc/claimChat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ chat_id: currentChatId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                // Refresh list and re-select
                loadOpenChats();
                setTimeout(() => {
                    // Find chat in new list and select it
                    const chat = openChatsList.find(c => c.id === currentChatId);
                    if (chat) {
                        chat.status = 'Assigned'; // Optimistic update
                        selectChat(chat.id, 'Assigned', chat.assigned_moderator_id);
                    }
                }, 500); // Wait for list refresh
            } else {
                alert('Failed to claim chat: ' + (data.message || 'Unknown error'));
            }
        });
}

function sendAdminMessage() {
    const input = document.getElementById('adminChatInput');
    const msg = input.value.trim();
    if (!msg || !currentChatId) return;

    fetch(`${URL_ROOT_MOD}/helpc/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            chat_id: currentChatId,
            message: msg
        })
    })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                input.value = '';
                loadChatMessages(currentChatId);
            }
        });
}
