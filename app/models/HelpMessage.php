<?php

class HelpMessage {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Add a new message
    public function addMessage($chatId, $senderId, $senderType, $message) {
        $this->db->query('INSERT INTO help_messages (chat_id, sender_id, sender_type, message, created_at) VALUES (:chat_id, :sender_id, :sender_type, :message, NOW())');
        $this->db->bind(':chat_id', $chatId);
        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':sender_type', $senderType);
        $this->db->bind(':message', $message);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    // Get a single message by ID
    public function getMessageById($messageId) {
        $this->db->query('SELECT * FROM help_messages WHERE id = :id');
        $this->db->bind(':id', $messageId);
        return $this->db->single();
    }

    // Get messages for a chat
    public function getMessagesByChatId($chatId) {
        $this->db->query('SELECT * FROM help_messages WHERE chat_id = :chat_id ORDER BY created_at ASC');
        $this->db->bind(':chat_id', $chatId);
        return $this->db->resultSet();
    }

    // Get messages with sender information
    public function getMessagesWithSenderInfo($chatId) {
        $messages = $this->getMessagesByChatId($chatId);
        $enrichedMessages = [];

        foreach ($messages as $msg) {
            $msgData = (object) [
                'id' => $msg->id,
                'chat_id' => $msg->chat_id,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at,
                'sender_name' => $this->getSenderName($msg->sender_id, $msg->sender_type)
            ];
            $enrichedMessages[] = $msgData;
        }

        return $enrichedMessages;
    }

    // Get sender name based on type
    private function getSenderName($senderId, $senderType) {
        // All users are in the 'users' table with 'id' as the primary key
        // The senderType is just for display (Traveller, Guide, Driver, Moderator, Admin)
        $this->db->query("SELECT fullname FROM users WHERE id = :sender_id");
        $this->db->bind(':sender_id', $senderId);
        $result = $this->db->single();
        
        return $result ? $result->fullname : 'Unknown';
    }

    // Get unread message count for moderator (messages from users)
    public function getUnreadCountForModerator($chatId) {
        $this->db->query('SELECT COUNT(*) as count FROM help_messages 
            WHERE chat_id = :chat_id 
            AND is_read = 0 
            AND sender_type IN ("Traveller", "Guide", "Driver")');
        $this->db->bind(':chat_id', $chatId);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    // Get unread message count for any viewer by excluding their own sender_id.
    public function getUnreadCountForViewer($chatId, $viewerId) {
        $this->db->query('SELECT COUNT(*) as count FROM help_messages
            WHERE chat_id = :chat_id
              AND is_read = 0
              AND sender_id != :viewer_id');
        $this->db->bind(':chat_id', (int)$chatId);
        $this->db->bind(':viewer_id', (int)$viewerId);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    public function getUnreadCountForViewerAcrossChats($chatIds, $viewerId) {
        if (!is_array($chatIds) || count($chatIds) === 0) {
            return 0;
        }

        $normalizedIds = [];
        foreach ($chatIds as $chatId) {
            $id = (int)$chatId;
            if ($id > 0) {
                $normalizedIds[$id] = $id;
            }
        }

        if (count($normalizedIds) === 0) {
            return 0;
        }

        $placeholders = [];
        foreach (array_values($normalizedIds) as $index => $chatId) {
            $key = ':chat_id_' . $index;
            $placeholders[] = $key;
        }

        $this->db->query('SELECT COUNT(*) as count FROM help_messages
            WHERE is_read = 0
              AND sender_id != :viewer_id
              AND chat_id IN (' . implode(', ', $placeholders) . ')');
        $this->db->bind(':viewer_id', (int)$viewerId);

        $index = 0;
        foreach (array_values($normalizedIds) as $chatId) {
            $this->db->bind(':chat_id_' . $index, (int)$chatId);
            $index++;
        }

        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    // Get last message for a chat
    public function getLastMessage($chatId) {
        $this->db->query('SELECT * FROM help_messages WHERE chat_id = :chat_id ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':chat_id', $chatId);
        return $this->db->single();
    }

    // Mark messages as read
    public function markAsRead($chatId, $readerType, $readerId = null) {
        if ($readerId !== null) {
            // Prefer sender_id-based read updates to support peers with same sender_type (e.g., Moderator <-> Moderator).
            $this->db->query('UPDATE help_messages SET is_read = 1
                WHERE chat_id = :chat_id
                  AND sender_id != :reader_id');
            $this->db->bind(':reader_id', (int)$readerId);
        } else {
            // Backward-compatible type-based fallback.
            $this->db->query('UPDATE help_messages SET is_read = 1
                WHERE chat_id = :chat_id
                  AND sender_type != :reader_type');
            $this->db->bind(':reader_type', $readerType);
        }

        $this->db->bind(':chat_id', (int)$chatId);
        return $this->db->execute();
    }

    // Delete a single message by ID
    public function deleteMessage($messageId) {
        $this->db->query('DELETE FROM help_messages WHERE id = :id');
        $this->db->bind(':id', $messageId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // Delete all messages in a chat
    public function deleteMessagesByChatId($chatId) {
        $this->db->query('DELETE FROM help_messages WHERE chat_id = :chat_id');
        $this->db->bind(':chat_id', $chatId);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
