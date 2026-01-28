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

    // Mark messages as read
    public function markAsRead($chatId, $readerType) {
        // Mark all messages from the "other" party as read
        $otherType = ($readerType === 'Moderator') ? 'User' : 'Moderator';
        $this->db->query('UPDATE help_messages SET is_read = 1 WHERE chat_id = :chat_id AND sender_type = :sender_type');
        $this->db->bind(':chat_id', $chatId);
        $this->db->bind(':sender_type', $otherType);
        return $this->db->execute();
    }
}
