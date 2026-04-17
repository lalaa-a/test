<section class="support-subtab helpdesk-subtab">
	<div class="subtab-header">
		<h2><i class="fas fa-comments"></i> Helpdesk - Chat With Us</h2>
		<p>Connect with our support team in real time for trip or account help.</p>
	</div>

	<div class="helpdesk-chat-shell">
		<div class="helpdesk-chat-header">
			<div class="helpdesk-agent-meta">
				<span class="agent-avatar"><i class="fas fa-headset"></i></span>
				<div>
					<h3>Travel Support</h3>
					<p id="helpdeskChatStatusText">Ready to connect</p>
				</div>
			</div>
			<div class="helpdesk-chat-actions">
				<button type="button" id="helpdeskStartBtn" class="helpdesk-btn primary">
					<i class="fas fa-plug"></i> Start / Resume
				</button>
				<button type="button" id="helpdeskReconnectBtn" class="helpdesk-btn ghost" title="Reconnect">
					<i class="fas fa-rotate"></i>
				</button>
			</div>
		</div>

		<div class="helpdesk-chat-messages" id="helpdeskChatMessages" aria-live="polite">
			<div class="date-divider"><span>Today</span></div>
			<div class="message support-message">
				<div class="message-content">
					<p>Hello there. Welcome to Tripingoo Travel Support. Click Start / Resume to continue your chat.</p>
				</div>
				<span class="message-time">Now</span>
			</div>
		</div>

		<div class="helpdesk-chat-input-wrap">
			<input
				type="text"
				id="helpdeskChatInput"
				class="helpdesk-chat-input"
				placeholder="Type your message..."
				autocomplete="off"
			>
			<button type="button" id="helpdeskSendBtn" class="helpdesk-send-btn" title="Send">
				<i class="fas fa-paper-plane"></i>
			</button>
		</div>
	</div>
</section>
