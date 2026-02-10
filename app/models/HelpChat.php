<?php

class HelpChat {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get active chat for a user
    public function getActiveChat($userId, $userType) {
        $this->db->query('SELECT * FROM help_chats WHERE user_id = :user_id AND user_type = :user_type AND status != "Closed" ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':user_type', $userType);
        return $this->db->single();
    }

    // Create a new chat
    public function createChat($userId, $userType) {
        $this->db->query('INSERT INTO help_chats (user_id, user_type, status, created_at) VALUES (:user_id, :user_type, "Open", NOW())');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':user_type', $userType);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    // Get chat by ID
    public function getChatById($chatId) {
        $this->db->query('SELECT * FROM help_chats WHERE id = :id');
        $this->db->bind(':id', $chatId);
        return $this->db->single();
    }

    // Get all open chats (for moderators)
    public function getOpenChats() {
        $this->db->query('SELECT * FROM help_chats WHERE status IN ("Open", "Assigned") ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Get chats assigned to a specific moderator
    public function getMyChats($moderatorId) {
        $this->db->query('SELECT * FROM help_chats WHERE assigned_moderator_id = :mod_id AND status = "Assigned" ORDER BY updated_at DESC');
        $this->db->bind(':mod_id', $moderatorId);
        return $this->db->resultSet();
    }

    // Assign chat to moderator (Exclusive Assignment)
    public function assignChat($chatId, $moderatorId) {
        // Only assign if not already assigned to someone else
        $this->db->query('UPDATE help_chats SET assigned_moderator_id = :mod_id, status = "Assigned", updated_at = NOW() WHERE id = :id AND (assigned_moderator_id IS NULL OR assigned_moderator_id = :mod_id)');
        $this->db->bind(':mod_id', $moderatorId);
        $this->db->bind(':id', $chatId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // Close a chat
    public function closeChat($chatId) {
        $this->db->query('UPDATE help_chats SET status = "Closed", updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $chatId);
        return $this->db->execute();
    }

    // Get chats visible to a specific moderator (open chats + their assigned chats)
    public function getChatsForModerator($moderatorId) {
        $this->db->query('SELECT * FROM help_chats 
            WHERE (status = "Open") 
            OR (status = "Assigned" AND assigned_moderator_id = :mod_id) 
            ORDER BY 
                CASE WHEN status = "Assigned" AND assigned_moderator_id = :mod_id2 THEN 0 ELSE 1 END,
                created_at DESC');
        $this->db->bind(':mod_id', $moderatorId);
        $this->db->bind(':mod_id2', $moderatorId);
        return $this->db->resultSet();
    }

    // Get all chats for admin (view-only)
    public function getAllChats() {
        $this->db->query('SELECT * FROM help_chats WHERE status IN ("Open", "Assigned") ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Get chat with user info for display
    public function getChatWithUserInfo($chatId) {
        $chat = $this->getChatById($chatId);
        if (!$chat) return null;

        $userId = $chat->user_id;

        // All users are in the 'users' table with 'id' as the primary key
        $this->db->query("SELECT fullname FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        $chat->user_name = $user ? $user->fullname : 'Unknown User';
        return $chat;
    }

    // Check if moderator can access this chat
    public function canModeratorAccessChat($chatId, $moderatorId) {
        $chat = $this->getChatById($chatId);
        if (!$chat) return false;
        
        // Open chats are accessible to all moderators
        if ($chat->status === 'Open') return true;
        
        // Assigned chats only to the assigned moderator
        return $chat->assigned_moderator_id == $moderatorId;
    }
}
