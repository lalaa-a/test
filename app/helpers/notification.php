<?php
/**
 * Notification helper
 *
 * Provides DB-backed notification read/write helpers with JSON fallback.
 */

if (!function_exists('notification_data_file_path')) {
    function notification_data_file_path() {
        return APP_ROOT . '/data/user_notifications.json';
    }
}

if (!function_exists('notification_db')) {
    function notification_db() {
        static $db = null;

        if ($db !== null) {
            return $db;
        }

        if (!class_exists('Database')) {
            return null;
        }

        try {
            $db = new Database();
        } catch (Throwable $e) {
            $db = null;
        }

        return $db;
    }
}

if (!function_exists('notification_db_tables_ready')) {
    function notification_db_tables_ready() {
        static $ready = null;

        if ($ready !== null) {
            return $ready;
        }

        $db = notification_db();
        if (!$db) {
            $ready = false;
            return false;
        }

        try {
            $db->query("SHOW TABLES LIKE 'notifications'");
            $notificationsTable = $db->single();

            $db->query("SHOW TABLES LIKE 'notification_recipients'");
            $recipientsTable = $db->single();

            $ready = !empty($notificationsTable) && !empty($recipientsTable);
        } catch (Throwable $e) {
            $ready = false;
        }

        return $ready;
    }
}

if (!function_exists('notification_map_db_rows')) {
    function notification_map_db_rows($rows) {
        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => isset($row->recipient_id) ? (int)$row->recipient_id : null,
                'notificationId' => isset($row->notification_id) ? (int)$row->notification_id : null,
                'userId' => isset($row->user_id) ? (int)$row->user_id : null,
                'type' => $row->type ?? 'info',
                'title' => $row->title ?? 'Notification',
                'message' => $row->message ?? '',
                'createdAt' => $row->created_at ?? date('Y-m-d H:i:s'),
                'isRead' => (isset($row->is_read) ? (int)$row->is_read : 0) === 1,
                'actionUrl' => $row->action_url ?? null
            ];
        }

        return $items;
    }
}

if (!function_exists('notification_get_db_items_for_user')) {
    function notification_get_db_items_for_user($userId, $limit = 10) {
        if (!notification_db_tables_ready()) {
            return null;
        }

        $uid = (int)$userId;
        $safeLimit = max(1, min(100, (int)$limit));
        $db = notification_db();
        if (!$db) {
            return null;
        }

        try {
            $db->query(
                "SELECT
                    nr.id AS recipient_id,
                    nr.notification_id,
                    nr.user_id,
                    nr.is_read,
                    nr.action_url,
                    n.type,
                    n.title,
                    n.message,
                    COALESCE(n.created_at, nr.created_at) AS created_at
                FROM notification_recipients nr
                INNER JOIN notifications n ON n.id = nr.notification_id
                WHERE nr.user_id = :uid
                ORDER BY COALESCE(n.created_at, nr.created_at) DESC
                LIMIT {$safeLimit}"
            );
            $db->bind(':uid', $uid);

            $rows = $db->resultSet();
            return notification_map_db_rows($rows);
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('default_notifications_for_user')) {
    function default_notifications_for_user($userId = null) {
        $uid = (int)($userId ?? 0);

        return [
            [
                'id' => 'n-' . $uid . '-1',
                'userId' => $uid,
                'type' => 'info',
                'title' => 'Welcome to your dashboard',
                'message' => 'Your driver dashboard is ready. Check recent trips and updates.',
                'createdAt' => date('Y-m-d H:i:s', strtotime('-20 minutes')),
                'isRead' => false,
                'actionUrl' => null
            ],
            [
                'id' => 'n-' . $uid . '-2',
                'userId' => $uid,
                'type' => 'success',
                'title' => 'Profile synced',
                'message' => 'Your profile information was refreshed successfully.',
                'createdAt' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'isRead' => true,
                'actionUrl' => null
            ]
        ];
    }
}

if (!function_exists('get_notifications_for_user')) {
    function get_notifications_for_user($userId = null, $limit = 10) {
        $uid = (int)($userId ?? 0);
        $limit = max(1, (int)$limit);

        $dbItems = notification_get_db_items_for_user($uid, $limit);
        if (is_array($dbItems)) {
            return array_slice($dbItems, 0, $limit);
        }

        $filePath = notification_data_file_path();

        if (!file_exists($filePath)) {
            return array_slice(default_notifications_for_user($uid), 0, $limit);
        }

        $raw = file_get_contents($filePath);
        if ($raw === false || trim($raw) === '') {
            return array_slice(default_notifications_for_user($uid), 0, $limit);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return array_slice(default_notifications_for_user($uid), 0, $limit);
        }

        $filtered = array_filter($decoded, function ($item) use ($uid) {
            if (!is_array($item)) {
                return false;
            }

            if (!isset($item['userId'])) {
                return true;
            }

            return (int)$item['userId'] === $uid;
        });

        usort($filtered, function ($a, $b) {
            $at = strtotime($a['createdAt'] ?? '1970-01-01 00:00:00');
            $bt = strtotime($b['createdAt'] ?? '1970-01-01 00:00:00');
            return $bt <=> $at;
        });

        if (empty($filtered)) {
            return array_slice(default_notifications_for_user($uid), 0, $limit);
        }

        return array_slice(array_values($filtered), 0, $limit);
    }
}

if (!function_exists('count_unread_notifications')) {
    function count_unread_notifications($notifications) {
        if (!is_array($notifications)) {
            return 0;
        }

        $count = 0;
        foreach ($notifications as $notification) {
            if (empty($notification['isRead'])) {
                $count++;
            }
        }

        return $count;
    }
}

if (!function_exists('mark_notification_as_read')) {
    function mark_notification_as_read($userId, $recipientId) {
        $uid = (int)$userId;
        $rid = (int)$recipientId;

        if ($uid <= 0 || $rid <= 0) {
            return ['success' => false, 'updated' => 0, 'source' => 'invalid'];
        }

        if (notification_db_tables_ready()) {
            $db = notification_db();
            if ($db) {
                try {
                    $db->query(
                        'UPDATE notification_recipients
                         SET is_read = 1, read_at = NOW(), updated_at = NOW()
                         WHERE id = :rid AND user_id = :uid AND is_read = 0'
                    );
                    $db->bind(':rid', $rid);
                    $db->bind(':uid', $uid);
                    $db->execute();

                    return ['success' => true, 'updated' => (int)$db->rowCount(), 'source' => 'db'];
                } catch (Throwable $e) {
                    return ['success' => false, 'updated' => 0, 'source' => 'db'];
                }
            }
        }

        $filePath = notification_data_file_path();
        if (!file_exists($filePath)) {
            return ['success' => false, 'updated' => 0, 'source' => 'file'];
        }

        $raw = file_get_contents($filePath);
        $decoded = json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            return ['success' => false, 'updated' => 0, 'source' => 'file'];
        }

        $updated = 0;
        foreach ($decoded as &$item) {
            if (!is_array($item)) {
                continue;
            }
            if ((int)($item['id'] ?? 0) === $rid && (int)($item['userId'] ?? 0) === $uid) {
                if (empty($item['isRead'])) {
                    $item['isRead'] = true;
                    $updated++;
                }
                break;
            }
        }
        unset($item);

        if ($updated > 0) {
            file_put_contents($filePath, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return ['success' => true, 'updated' => $updated, 'source' => 'file'];
    }
}

if (!function_exists('mark_all_notifications_as_read')) {
    function mark_all_notifications_as_read($userId) {
        $uid = (int)$userId;

        if ($uid <= 0) {
            return ['success' => false, 'updated' => 0, 'source' => 'invalid'];
        }

        if (notification_db_tables_ready()) {
            $db = notification_db();
            if ($db) {
                try {
                    $db->query(
                        'UPDATE notification_recipients
                         SET is_read = 1, read_at = NOW(), updated_at = NOW()
                         WHERE user_id = :uid AND is_read = 0'
                    );
                    $db->bind(':uid', $uid);
                    $db->execute();

                    return ['success' => true, 'updated' => (int)$db->rowCount(), 'source' => 'db'];
                } catch (Throwable $e) {
                    return ['success' => false, 'updated' => 0, 'source' => 'db'];
                }
            }
        }

        $filePath = notification_data_file_path();
        if (!file_exists($filePath)) {
            return ['success' => false, 'updated' => 0, 'source' => 'file'];
        }

        $raw = file_get_contents($filePath);
        $decoded = json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            return ['success' => false, 'updated' => 0, 'source' => 'file'];
        }

        $updated = 0;
        foreach ($decoded as &$item) {
            if (!is_array($item)) {
                continue;
            }
            if ((int)($item['userId'] ?? 0) !== $uid) {
                continue;
            }
            if (empty($item['isRead'])) {
                $item['isRead'] = true;
                $updated++;
            }
        }
        unset($item);

        if ($updated > 0) {
            file_put_contents($filePath, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return ['success' => true, 'updated' => $updated, 'source' => 'file'];
    }
}
