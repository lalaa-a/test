<!-- Help Desk Dashboard -->
<div class="support-center helpdesk-center" id="moderatorHelpdeskRoot" data-viewer-id="<?php echo (int)($_SESSION['user_id'] ?? 0); ?>">
    <!-- Help Desk Header -->
    <div class="support-header">
        <div class="support-title">
            <div class="support-title-icon" aria-hidden="true">
                <i class="fas fa-life-ring"></i>
            </div>
            <div class="support-title-copy">
                <h2>Help Desk</h2>
                <p>Handle site queue chats and direct conversations with any platform user.</p>
            </div>
        </div>
        <div class="support-stats">
            <div class="support-stat-card open">
                <div class="support-stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="support-stat-content">
                    <span id="openChatsCount">0</span>
                    <small>Open Chats</small>
                </div>
            </div>
            <div class="support-stat-card assigned">
                <div class="support-stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="support-stat-content">
                    <span id="myChatsCount">0</span>
                    <small>Direct Chats</small>
                </div>
            </div>
        </div>
    </div>

    <div class="helpdesk-workspace">
        <!-- Main Help Desk Grid -->
        <div class="support-grid">
            <!-- Chat List Panel -->
            <div class="chat-list-panel">
                <div class="panel-header">
                    <div class="panel-tabs">
                        <button class="panel-tab active" data-filter="all">
                            <i class="fas fa-inbox"></i> All
                        </button>
                        <button class="panel-tab" data-filter="open">
                            <i class="fas fa-life-ring"></i> Site Queue
                        </button>
                        <button class="panel-tab" data-filter="mine">
                            <i class="fas fa-user-group"></i> Direct
                        </button>
                    </div>
                </div>

                <div class="directory-panel">
                    <label for="userDirectorySearchInput" class="directory-label">Start Direct Chat</label>
                    <div class="directory-search-wrap">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input
                            id="userDirectorySearchInput"
                            type="text"
                            placeholder="Search by user ID or name"
                            autocomplete="off"
                        />
                    </div>
                    <div id="userDirectoryResults" class="directory-results" aria-live="polite">
                        <div class="directory-hint">Search by user ID or name to chat with admin, moderators, travellers, guides, or drivers.</div>
                    </div>
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
                                    <span class="chat-id" id="chatIdDisplay"></span>
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
                            <button class="action-btn delete-btn" id="deleteChatBtn" title="Delete this chat" style="display: none;">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
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
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>