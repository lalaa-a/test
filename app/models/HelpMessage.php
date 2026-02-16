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
        return $this->db->execute();
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

    // Get last message for a chat
    public function getLastMessage($chatId) {
        $this->db->query('SELECT * FROM help_messages WHERE chat_id = :chat_id ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':chat_id', $chatId);
        return $this->db->single();
    }

    // Mark messages as read
    public function markAsRead($chatId, $readerType) {
        // Mark all messages from the "other" party as read
        if ($readerType === 'Moderator') {
            // Moderator reading - mark user messages as read
            $this->db->query('UPDATE help_messages SET is_read = 1 
                WHERE chat_id = :chat_id 
                AND sender_type IN ("Traveller", "Guide", "Driver")');
        } else {
            // User reading - mark moderator messages as read
            $this->db->query('UPDATE help_messages SET is_read = 1 
                WHERE chat_id = :chat_id 
                AND sender_type = "Moderator"');
        }
        $this->db->bind(':chat_id', $chatId);
        return $this->db->execute();
    }
}

