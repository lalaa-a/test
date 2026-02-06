

<!-- Chat Widget -->
<div class="chat-widget" id="chatWidget">
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="agent-avatar">
                <i class="fas fa-headset"></i>
                <span class="status-dot"></span>
            </div>
            <div class="agent-details">
                <h3>Travel Support</h3>
                <p>Online | Typically replies in minutes</p>
            </div>
        </div>
        <div class="chat-header-actions">
            <i class="fas fa-times" id="closeChatBtn"></i>
        </div>
    </div>
    <div class="chat-body">
        <div class="chat-messages" id="chatMessages">
            <div class="date-divider"><span>Today</span></div>
            <div class="message support-message">
                <div class="message-content">
                    <p>Hello there! 👋 Welcome to Tripingoo Travel Support. How can we help you today?</p>
                </div>
                <span class="message-time">Just now</span>
            </div>
        </div>
        <div class="chat-input-area">
            <div class="input-wrapper">
                <input type="text" id="chatInput" placeholder="Type your message...">
                <button class="send-btn" id="chatSendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Help & Chat Widget JS -->
<script src="<?php echo URL_ROOT . '/public/js/helper/helpWidget.js' ?>"></script>
