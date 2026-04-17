<?php

class Notification extends Controller {

    public function index() {
        $this->listItems();
    }

    public function listItems() {
        $this->sendJsonHeader();

        if (!$this->ensureLoggedIn()) {
            return;
        }

        $loggedUser = getLoggedInUser();
        $userId = (int)($loggedUser['id'] ?? 0);
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        $items = get_notifications_for_user($userId, $limit);

        $this->sendJson(200, [
            'success' => true,
            'notifications' => $items,
            'unreadCount' => count_unread_notifications($items)
        ]);
    }

    public function markRead() {
        $this->sendJsonHeader();

        if (!$this->ensureMethod('POST')) {
            return;
        }

        if (!$this->ensureLoggedIn()) {
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $recipientId = (int)($payload['notificationId'] ?? ($_POST['notificationId'] ?? 0));

        if ($recipientId <= 0) {
            $this->sendJson(422, [
                'success' => false,
                'message' => 'notificationId is required'
            ]);
            return;
        }

        $loggedUser = getLoggedInUser();
        $userId = (int)($loggedUser['id'] ?? 0);

        $result = mark_notification_as_read($userId, $recipientId);
        if (empty($result['success'])) {
            $this->sendJson(500, [
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ]);
            return;
        }

        $this->sendJson(200, [
            'success' => true,
            'updated' => (int)($result['updated'] ?? 0),
            'source' => $result['source'] ?? 'unknown'
        ]);
    }

    public function markAllRead() {
        $this->sendJsonHeader();

        if (!$this->ensureMethod('POST')) {
            return;
        }

        if (!$this->ensureLoggedIn()) {
            return;
        }

        $loggedUser = getLoggedInUser();
        $userId = (int)($loggedUser['id'] ?? 0);

        $result = mark_all_notifications_as_read($userId);
        if (empty($result['success'])) {
            $this->sendJson(500, [
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ]);
            return;
        }

        $this->sendJson(200, [
            'success' => true,
            'updated' => (int)($result['updated'] ?? 0),
            'source' => $result['source'] ?? 'unknown'
        ]);
    }

    private function ensureLoggedIn() {
        if (isLoggedIn()) {
            return true;
        }

        $this->sendJson(401, [
            'success' => false,
            'message' => 'Unauthorized'
        ]);

        return false;
    }

    private function ensureMethod($method) {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === strtoupper($method)) {
            return true;
        }

        $this->sendJson(405, [
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        return false;
    }

    private function sendJsonHeader() {
        header('Content-Type: application/json; charset=utf-8');
    }

    private function sendJson($statusCode, $body) {
        http_response_code((int)$statusCode);
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
