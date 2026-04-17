(function () {
    class NotificationUnit {
        constructor(options = {}) {
            this.notifications = Array.isArray(options.notifications) ? options.notifications : [];
            this.api = {
                listUrl: options.listUrl || null,
                markReadUrl: options.markReadUrl || null,
                markAllReadUrl: options.markAllReadUrl || null
            };
            this.requestInFlight = false;
            this.pollingInFlight = false;
            this.refreshIntervalMs = Number(options.refreshIntervalMs || 15000);
            this.pollTimer = null;
            this.hasSyncedOnce = false;

            this.root = document.getElementById('driverNotificationUnit');
            this.panel = document.getElementById('driverNotificationPanel');
            this.listContainer = document.getElementById('notificationListContainer');
            this.emptyState = document.getElementById('notificationEmptyState');
            this.markAllBtn = document.getElementById('notificationMarkAllBtn');
            this.closeBtn = document.getElementById('notificationCloseBtn');

            this.triggerBtn = document.getElementById(options.triggerButtonId || 'notificationsBtn');
            this.badge = document.getElementById(options.badgeId || 'notificationBadge');

            if (!this.root || !this.panel || !this.listContainer || !this.triggerBtn || !this.badge) {
                return;
            }

            this.bindEvents();
            this.render();
            this.updateBadge();
            this.startAutoRefresh();
        }

        bindEvents() {
            this.triggerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggle();
            });

            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', () => this.close());
            }

            if (this.markAllBtn) {
                this.markAllBtn.addEventListener('click', () => this.handleMarkAllRead());
            }

            if (this.listContainer) {
                this.listContainer.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-action="mark-read"]');
                    if (!button) {
                        return;
                    }

                    const notificationId = Number(button.dataset.id || 0);
                    if (!notificationId) {
                        return;
                    }

                    this.handleMarkSingleRead(notificationId);
                });
            }

            document.addEventListener('click', (e) => {
                if (!this.root.classList.contains('open')) {
                    return;
                }

                if (!this.root.contains(e.target) && !this.triggerBtn.contains(e.target)) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.close();
                }
            });
        }

        toggle() {
            if (this.root.classList.contains('open')) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            this.root.classList.add('open');
            this.root.setAttribute('aria-hidden', 'false');
        }

        close() {
            this.root.classList.remove('open');
            this.root.setAttribute('aria-hidden', 'true');
        }

        updateBadge() {
            const unread = this.notifications.filter((n) => !n.isRead).length;
            this.badge.textContent = String(unread);
            this.badge.style.display = unread > 0 ? 'flex' : 'none';

            if (this.markAllBtn) {
                this.markAllBtn.disabled = unread === 0;
            }
        }

        render() {
            if (!this.notifications.length) {
                this.listContainer.innerHTML = '';
                if (this.emptyState) {
                    this.emptyState.style.display = 'block';
                }
                return;
            }

            if (this.emptyState) {
                this.emptyState.style.display = 'none';
            }

            this.listContainer.innerHTML = this.notifications.map((n) => {
                const title = this.escapeHtml(n.title || 'Notification');
                const message = this.escapeHtml(n.message || '');
                const time = this.formatTime(n.createdAt);
                const unreadClass = n.isRead ? '' : ' unread';
                const itemId = Number(n.id || 0);
                const actionMarkup = n.isRead
                    ? '<span class="notification-item-status">Read</span>'
                    : `<button type="button" class="notification-item-read-btn" data-action="mark-read" data-id="${itemId}">Mark as read</button>`;

                return `
                    <article class="notification-item${unreadClass}">
                        <div class="notification-item-head">
                            <h4 class="notification-item-title">${title}</h4>
                            <span class="notification-item-time">${time}</span>
                        </div>
                        <p class="notification-item-message">${message}</p>
                        <div class="notification-item-actions">
                            ${actionMarkup}
                        </div>
                    </article>
                `;
            }).join('');
        }

        async handleMarkSingleRead(notificationId) {
            const target = this.notifications.find((item) => Number(item.id) === Number(notificationId));
            if (!target || target.isRead || this.requestInFlight) {
                return;
            }

            target.isRead = true;
            this.render();
            this.updateBadge();

            const saved = await this.postJson(this.api.markReadUrl, { notificationId });
            if (saved) {
                return;
            }

            target.isRead = false;
            this.render();
            this.updateBadge();
            this.notify('Could not update notification right now.', 'error');
        }

        async handleMarkAllRead() {
            const unreadIds = this.notifications
                .filter((item) => !item.isRead)
                .map((item) => Number(item.id));

            if (!unreadIds.length || this.requestInFlight) {
                return;
            }

            this.notifications = this.notifications.map((item) => ({ ...item, isRead: true }));
            this.render();
            this.updateBadge();

            const saved = await this.postJson(this.api.markAllReadUrl, {});
            if (saved) {
                return;
            }

            this.notifications = this.notifications.map((item) => ({
                ...item,
                isRead: unreadIds.includes(Number(item.id)) ? false : item.isRead
            }));
            this.render();
            this.updateBadge();
            this.notify('Could not mark all notifications as read.', 'error');
        }

        async postJson(url, payload) {
            if (!url) {
                return false;
            }

            this.requestInFlight = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload || {})
                });

                if (!response.ok) {
                    return false;
                }

                const data = await response.json();
                return Boolean(data && data.success);
            } catch (error) {
                return false;
            } finally {
                this.requestInFlight = false;
            }
        }

        notify(message, type) {
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type || 'info');
            }
        }

        startAutoRefresh() {
            if (!this.api.listUrl || this.refreshIntervalMs < 5000) {
                return;
            }

            this.pollTimer = window.setInterval(() => {
                this.refreshFromServer();
            }, this.refreshIntervalMs);
        }

        async refreshFromServer() {
            if (!this.api.listUrl || this.pollingInFlight) {
                return;
            }

            this.pollingInFlight = true;
            try {
                const response = await fetch(this.api.listUrl, { method: 'GET' });
                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (!data || !data.success || !Array.isArray(data.notifications)) {
                    return;
                }

                const oldUnread = this.notifications.filter((n) => !n.isRead).length;
                const oldIds = new Set(this.notifications.map((n) => Number(n.id)));

                this.notifications = data.notifications;
                const newUnread = this.notifications.filter((n) => !n.isRead).length;

                this.render();
                this.updateBadge();

                if (this.hasSyncedOnce) {
                    const hasBrandNewItem = this.notifications.some((n) => !oldIds.has(Number(n.id)));
                    if (hasBrandNewItem || newUnread > oldUnread) {
                        this.notify('You have new notifications.', 'info');
                    }
                }

                this.hasSyncedOnce = true;
            } catch (error) {
                // Keep silent on polling failures to avoid noisy UX.
            } finally {
                this.pollingInFlight = false;
            }
        }

        formatTime(value) {
            if (!value) {
                return 'now';
            }

            const timestamp = new Date(value.replace(' ', 'T'));
            if (Number.isNaN(timestamp.getTime())) {
                return 'now';
            }

            const diffMs = Date.now() - timestamp.getTime();
            const mins = Math.floor(diffMs / 60000);
            if (mins < 1) {
                return 'just now';
            }
            if (mins < 60) {
                return `${mins}m ago`;
            }

            const hours = Math.floor(mins / 60);
            if (hours < 24) {
                return `${hours}h ago`;
            }

            const days = Math.floor(hours / 24);
            return `${days}d ago`;
        }

        escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const payload = Array.isArray(window.driverDashboardNotifications)
            ? window.driverDashboardNotifications
            : [];
        const api = window.driverDashboardNotificationApi || {};

        window.driverNotificationUnit = new NotificationUnit({
            notifications: payload,
            triggerButtonId: 'notificationsBtn',
            badgeId: 'notificationBadge',
            listUrl: api.listUrl || null,
            markReadUrl: api.markReadUrl || null,
            markAllReadUrl: api.markAllReadUrl || null,
            refreshIntervalMs: Number(api.refreshIntervalMs || 15000)
        });
    });
})();
