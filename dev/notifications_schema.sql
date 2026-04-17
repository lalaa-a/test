-- Notification storage schema
-- Run this in the `tripingoo` database.
--
-- Data model overview:
-- 1) notifications: stores the notification content/body once.
-- 2) notification_recipients: stores who receives it + read state per user.
--
-- Why split into two tables?
-- - One notification can be sent to many users without duplicating title/message.
-- - Each user can have an independent read status (is_read, read_at).

CREATE TABLE IF NOT EXISTS notifications (
    -- Unique ID for the notification content record.
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- UI severity/type used for styling (info/success/warning/error).
    type ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',

    -- Short heading shown in the notification card.
    title VARCHAR(150) NOT NULL,

    -- Main message body.
    message TEXT NOT NULL,

    -- Optional structured metadata (JSON), for future expansion.
    -- Examples: {"tripId": 123, "deepLink": "/Driver/trips/123"}
    payload_json JSON NULL,

    -- Optional user/admin ID that created the notification.
    -- Keep nullable so system-generated notifications are allowed.
    created_by BIGINT UNSIGNED NULL,

    -- Creation timestamp of the content record.
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Speeds up "latest notifications" queries and cleanup jobs.
    KEY idx_notifications_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notification_recipients (
    -- Unique ID for this user-targeted notification instance.
    -- This ID is what your frontend should use as notificationId.
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- FK to notifications.id (the shared content row).
    notification_id BIGINT UNSIGNED NOT NULL,

    -- Recipient user ID (current app user table primary key).
    user_id BIGINT UNSIGNED NOT NULL,

    -- Optional click destination for this recipient.
    -- Can override payload deep links when needed.
    action_url VARCHAR(255) NULL,

    -- Read flag for this specific user-recipient row.
    -- 0 = unread, 1 = read.
    is_read TINYINT(1) NOT NULL DEFAULT 0,

    -- When user marked this notification as read.
    read_at DATETIME NULL,

    -- When recipient row was created (delivered).
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Auto-updated timestamp for any row change.
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Prevents duplicate assignment of same notification to same user.
    UNIQUE KEY uq_notification_user (notification_id, user_id),

    -- Main runtime index for dashboard queries:
    -- WHERE user_id = ? AND is_read = ? ORDER BY created_at DESC
    KEY idx_recipients_user_read_created (user_id, is_read, created_at),

    -- Helps joins and maintenance operations by notification_id.
    KEY idx_recipients_notification (notification_id),

    -- If notification content is deleted, recipient rows are deleted too.
    CONSTRAINT fk_recipients_notification
        FOREIGN KEY (notification_id)
        REFERENCES notifications (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example insert for one user
-- 1) Create shared notification content.
INSERT INTO notifications (type, title, message)
VALUES ('info', 'Trip reminder', 'Your next trip starts in 30 minutes.');

-- 2) Assign it to user id 1 as an unread recipient row.
--    LAST_INSERT_ID() is the notifications.id from step 1.
INSERT INTO notification_recipients (notification_id, user_id, action_url)
VALUES (LAST_INSERT_ID(), 1, '/Driver/trips');
