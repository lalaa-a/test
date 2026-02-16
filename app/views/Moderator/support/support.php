<!-- Support Center Dashboard -->
<div class="support-center">
    <!-- Support Header -->
    <div class="support-header">
        <div class="support-title">
            <i class="fas fa-headset"></i>
            <h2>Support Center</h2>
        </div>
        <div class="support-stats">
            <div class="stat-pill open">
                <i class="fas fa-clock"></i>
                <span id="openChatsCount">0</span> Open
            </div>
            <div class="stat-pill assigned">
                <i class="fas fa-user-check"></i>
                <span id="myChatsCount">0</span> My Chats
            </div>
        </div>
    </div>

    <!-- Main Support Grid -->
    <div class="support-grid">
        <!-- Chat List Panel -->
        <div class="chat-list-panel">
            <div class="panel-header">
                <div class="panel-tabs">
                    <button class="panel-tab active" data-filter="all">
                        <i class="fas fa-inbox"></i> All
                    </button>
                    <button class="panel-tab" data-filter="open">
                        <i class="fas fa-folder-open"></i> Open
                    </button>
                    <button class="panel-tab" data-filter="mine">
                        <i class="fas fa-user"></i> Mine
                    </button>
                </div>
                <button class="refresh-btn" id="refreshChatsBtn" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            <div class="chat-list-container">
                <div class="chat-list" id="supportChatList">
                    <!-- Chat items will be dynamically loaded -->
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading chats...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Window Panel -->
        <div class="chat-window-panel">
            <!-- Empty State -->
            <div class="chat-empty-state" id="chatEmptyState">
                <div class="empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Select a conversation</h3>
                <p>Choose a chat from the list to start responding</p>
            </div>

            <!-- Active Chat Container (hidden by default) -->
            <div class="active-chat-container" id="activeChatContainer" style="display: none;">
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-user-info">
                        <div class="user-avatar-large" id="chatUserAvatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h3 id="chatUserName">User Name</h3>
                            <div class="user-meta">
                                <span class="user-type-badge" id="chatUserType">Traveller</span>
                                <span class="chat-id" id="chatIdDisplay">#Chat ID</span>
                            </div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="action-btn claim-btn" id="claimChatBtn" title="Claim this chat">
                            <i class="fas fa-hand-paper"></i>
                            <span>Claim</span>
                        </button>
                        <button class="action-btn close-btn" id="closeChatBtn" title="Close this chat">
                            <i class="fas fa-check-circle"></i>
                            <span>Close</span>
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div class="chat-messages-container" id="chatMessagesContainer">
                    <div class="messages-wrapper" id="chatMessages">
                        <!-- Messages will be loaded dynamically -->
                    </div>
                    
                    <!-- Typing Indicator -->
                    <div class="typing-indicator" id="typingIndicator" style="display: none;">
                        <div class="typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span>User is typing...</span>
                    </div>
                </div>

                <!-- Chat Input Area (hidden until claimed) -->
                <div class="chat-input-area" id="chatInputArea" style="display: none;">
                    <div class="input-container">
                        <textarea id="messageInput" placeholder="Type your message..." rows="1"></textarea>
                        <button class="send-btn" id="sendMessageBtn" title="Send message">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="input-actions">
                        <span class="char-count" id="charCount">0/500</span>
                    </div>
                </div>

                <!-- Claim Overlay (shown for unclaimed chats) -->
                <div class="claim-overlay" id="claimOverlay">
                    <div class="claim-content">
                        <i class="fas fa-lock"></i>
                        <h4>This chat is unclaimed</h4>
                        <p>Claim this chat to start responding to the user</p>
                        <button class="claim-action-btn" id="claimOverlayBtn">
                            <i class="fas fa-hand-paper"></i>
                            Claim Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>
