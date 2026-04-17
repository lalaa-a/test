<div class="notification-unit" id="driverNotificationUnit" aria-hidden="true">
    <div class="notification-panel" id="driverNotificationPanel" role="dialog" aria-label="Notifications" aria-modal="false">
        <div class="notification-panel-header">
            <h3 class="notification-panel-title">Notifications</h3>
            <div class="notification-panel-actions">
                <button type="button" class="notification-link-btn" id="notificationMarkAllBtn">Mark all as read</button>
                <button type="button" class="notification-close-btn" id="notificationCloseBtn" aria-label="Close notifications">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="notification-panel-body" id="notificationListContainer"></div>

        <div class="notification-empty-state" id="notificationEmptyState" style="display:none;">
            <i class="fas fa-bell-slash"></i>
            <p>No notifications right now</p>
        </div>
    </div>
</div>