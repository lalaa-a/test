<section
	class="support-subtab helpdesk-subtab"
	id="helpdeskSubtabRoot"
	data-viewer-id="<?php echo (int)($_SESSION['user_id'] ?? 0); ?>"
	data-viewer-type="<?php echo htmlspecialchars($_SESSION['user_account_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
>
	<div class="subtab-header">
		<h2><i class="fas fa-life-ring"></i> Help Desk</h2>
		<p>Message site support or chat with travellers who sent requests to you.</p>
	</div>

	<div class="helpdesk-layout">
		<aside class="support-target-panel">
			<div class="target-panel-head">
				<h3>Conversations</h3>
				<p>Choose who you want to message</p>
			</div>

			<div class="target-filter-tabs" role="tablist" aria-label="Filter support contacts">
				<button type="button" class="target-filter-tab active" data-filter="all">All</button>
				<button type="button" class="target-filter-tab" data-filter="site">Site</button>
				<button type="button" class="target-filter-tab" data-filter="travellers">Travellers</button>
			</div>

			<div class="support-target-list" id="supportTargetList" aria-live="polite">
				<div class="target-loading">
					<i class="fas fa-spinner fa-spin"></i>
					<span>Loading contacts...</span>
				</div>
			</div>
		</aside>

		<div class="helpdesk-chat-shell">
			<div class="helpdesk-chat-header">
				<div class="helpdesk-target-meta">
					<span class="target-avatar" id="helpdeskTargetAvatar">TS</span>
					<div class="target-copy">
						<h3 id="helpdeskTargetName">Tripingoo Site Support</h3>
						<p id="helpdeskChatStatusText">Select a conversation to start</p>
					</div>
				</div>

				<div class="helpdesk-chat-actions">
					<button type="button" id="helpdeskReconnectBtn" class="helpdesk-btn ghost" title="Reconnect conversation">
						<i class="fas fa-rotate"></i>
						<span>Refresh</span>
					</button>
				</div>
			</div>

			<div class="helpdesk-chat-messages" id="helpdeskChatMessages" aria-live="polite">
				<div class="empty-chat-state">
					<i class="fas fa-comments"></i>
					<h4>No conversation selected</h4>
					<p>Choose Site Support or a traveller to begin chatting.</p>
				</div>
			</div>

			<div class="helpdesk-chat-input-wrap">
				<textarea
					id="helpdeskChatInput"
					class="helpdesk-chat-input"
					placeholder="Type your message..."
					rows="1"
					disabled
				></textarea>

				<button type="button" id="helpdeskSendBtn" class="helpdesk-send-btn" title="Send" disabled>
					<i class="fas fa-paper-plane"></i>
				</button>
			</div>
		</div>
	</div>
</section>
