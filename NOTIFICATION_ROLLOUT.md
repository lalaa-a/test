# Notification Rollout Across All Dashboards

This guide explains how to enable the same notification unit used in the driver dashboard for all other dashboards.

## 1) Prerequisites

1. Run SQL schema:
   - `dev/notifications_schema.sql`
2. Ensure helper is loaded:
   - `app/bootloader.php` includes `helpers/notification.php`.
3. Ensure assets exist:
   - CSS: `public/css/notification/notification-unit.css`
   - JS: `public/js/notification/notification-unit.js`
   - Panel partial: `app/views/Notification/panel.php`

## 2) What each dashboard needs

For each dashboard view file:

1. Add notification data in PHP at the top:

```php
<?php
$loggedInUser = getLoggedInUser();
$notificationItems = get_notifications_for_user($loggedInUser['id'] ?? null, 20);
$unreadCount = count_unread_notifications($notificationItems);
?>
```

2. Ensure CSS is loaded in `<head>`:

```php
<link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/notification/notification-unit.css">
```

3. Ensure header button IDs are exactly:
- Button id: `notificationsBtn`
- Badge id: `notificationBadge`

4. Replace hardcoded badge values with PHP unread count:

```php
<span class="notification-badge" id="notificationBadge" style="display: <?php echo $unreadCount > 0 ? 'flex' : 'none'; ?>;"><?php echo (int)$unreadCount; ?></span>
```

5. Include notification panel once in body:

```php
<?php require APP_ROOT . '/views/Notification/panel.php'; ?>
```

6. Before closing `</body>`, expose payload + API URLs:

```php
<script>
window.dashboardNotifications = <?php echo json_encode($notificationItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
window.driverDashboardNotifications = window.dashboardNotifications;
window.driverDashboardNotificationApi = {
   listUrl: '<?php echo URL_ROOT; ?>/Notification/listItems',
   markReadUrl: '<?php echo URL_ROOT; ?>/Notification/markRead',
   markAllReadUrl: '<?php echo URL_ROOT; ?>/Notification/markAllRead',
   refreshIntervalMs: 15000
};
</script>
<script src="<?php echo URL_ROOT; ?>/public/js/notification/notification-unit.js"></script>
```

7. Remove old demo notification JS blocks that do:
- `alert('...notifications...')`
- fake timeout badge updates
- direct `notificationBadge.textContent = '3'`

## 3) Target dashboards to update

Current template dashboards with old placeholder behavior:

1. `app/views/UserTemplates/travellerDash.php`
2. `app/views/UserTemplates/guideDash.php`
3. `app/views/UserTemplates/moderatorDash.php`

Other dashboard variants to align (if actively used):

1. `app/views/Admin/adminDash.php`
2. `app/views/Admin/adminDash1.php`
3. `app/views/BuisManager/buisDash.php`
4. `app/views/Guide/guideDash.php`
5. `app/views/Driver/driverDash.php`
6. `app/views/SiteModerator/SiteModeratorDash.php`

## 4) Data flow after rollout

1. Dashboard loads and calls `get_notifications_for_user(userId, 20)`.
2. Helper reads DB (`notifications` + `notification_recipients`).
3. Unit renders bell count and panel list.
4. Clicking "Mark as read" calls `Notification/markRead`.
5. Clicking "Mark all as read" calls `Notification/markAllRead`.
6. Any dashboard that uses the same endpoints sees consistent read state.

Controller endpoints summary:

1. `GET /Notification/listItems`
2. `POST /Notification/markRead`
3. `POST /Notification/markAllRead`

## 5) Common mistakes to avoid

1. Using wrong IDs (`notificationsBtn`, `notificationBadge` must match).
2. Keeping old fake JS badge simulation enabled.
3. Sending notifications.id to API instead of notification_recipients.id.
4. Forgetting to include `panel.php` in the dashboard markup.

## 6) Optional improvement (recommended)

Create a reusable partial (for example `app/views/Notification/init.php`) that prints:
1. the top PHP fetch block,
2. the API URL script object,
3. the unit script include.

Then each dashboard only includes the partial once, reducing duplication.
