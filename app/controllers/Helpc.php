<?php

class Helpc extends Controller {
    private $helpChatModel;
    private $helpMessageModel;

    public function __construct() {
        // Load Models
        $this->helpChatModel = $this->model('HelpChat');
        $this->helpMessageModel = $this->model('HelpMessage');
    }

    // Map account types to display types
    private function mapAccountType($accountType) {
        $typeMap = [
            'tourist' => 'Traveller',
            'guide' => 'Guide',
            'driver' => 'Driver',
            'site_moderator' => 'Moderator',
            'admin' => 'Admin'
        ];
        return $typeMap[$accountType] ?? ucfirst($accountType);
    }

    // Check if user is a moderator
    private function isModerator($accountType) {
        return $accountType === 'site_moderator';
    }

    // Check if user is an admin
    private function isAdmin($accountType) {
        return $accountType === 'admin';
    }

    public function index() {
        // Main Help Page
        $data = [
            'title' => 'Help Center'
        ];
        $this->view('UserTemplates/help', $data);
    }

    // Initialize or continue a chat
    public function startChat() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get user info from session
            $userId = $_SESSION['user_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$userId || !$accountType) {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                return;
            }

            $userType = $this->mapAccountType($accountType);

            // Check if active chat exists
            $chat = $this->helpChatModel->getActiveChat($userId, $userType);
            
            if (!$chat) {
                $chatId = $this->helpChatModel->createChat($userId, $userType);
                $chat = $this->helpChatModel->getChatById($chatId);
            }

            echo json_encode(['status' => 'success', 'chat' => $chat]);
        }
    }

    // Send a message (for both users and moderators)
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $chatId = $data['chat_id'] ?? null;
            $message = trim($data['message'] ?? '');

            if (!$chatId || !$message) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                return;
            }

            // Get sender info from session
            $senderId = $_SESSION['user_id'] ?? null; 
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$senderId || !$accountType) {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                return;
            }

            $senderType = $this->mapAccountType($accountType);

            // Prevent Admin from sending messages
            if ($this->isAdmin($accountType)) {
                echo json_encode(['status' => 'error', 'message' => 'Admins cannot send messages']);
                return;
            }

            // Exclusivity Check for Moderators
            if ($this->isModerator($accountType)) {
                $chat = $this->helpChatModel->getChatById($chatId);
                if (!$chat) {
                    echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                    return;
                }
                if ($chat->status === 'Open') {
                    echo json_encode(['status' => 'error', 'message' => 'Please claim this chat first']);
                    return;
                }
                if ($chat->assigned_moderator_id && $chat->assigned_moderator_id != $senderId) {
                    echo json_encode(['status' => 'error', 'message' => 'This chat is assigned to another moderator']);
                    return;
                }
            }

            if ($this->helpMessageModel->addMessage($chatId, $senderId, $senderType, $message)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
            }
        }
    }

    // Get all open chats (legacy)
    public function getOpenChats() {
        $chats = $this->helpChatModel->getOpenChats();
        echo json_encode(['status' => 'success', 'chats' => $chats]);
    }

    // Get chats for moderator dashboard (with user info and filtering)
    public function getChatsForModerator() {
        $accountType = $_SESSION['user_account_type'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $isAdmin = $this->isAdmin($accountType);
        $isModerator = $this->isModerator($accountType);

        if (!$isAdmin && !$isModerator) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }
        
        // Get chats based on role
        if ($isAdmin) {
            // Admin can see all chats (view-only)
            $chats = $this->helpChatModel->getAllChats();
        } else {
            // Moderators see open chats + their assigned chats
            $chats = $this->helpChatModel->getChatsForModerator($userId);
        }

        // Enrich chat data with user info
        $enrichedChats = [];
        foreach ($chats as $chat) {
            $chatData = (array) $chat;
            
            // Get user name
            $chatWithInfo = $this->helpChatModel->getChatWithUserInfo($chat->id);
            $chatData['user_name'] = $chatWithInfo ? $chatWithInfo->user_name : 'Unknown User';
            
            // Get unread count for moderator
            $chatData['unread_count'] = $this->helpMessageModel->getUnreadCountForModerator($chat->id);
            
            // Get last message preview
            $lastMessage = $this->helpMessageModel->getLastMessage($chat->id);
            $chatData['last_message'] = $lastMessage ? $lastMessage->message : null;
            $chatData['updated_at'] = $lastMessage ? $lastMessage->created_at : $chat->created_at;
            
            // Check if this is the current moderator's chat
            $chatData['is_mine'] = ($chat->assigned_moderator_id == $userId);
            
            $enrichedChats[] = $chatData;
        }

        echo json_encode([
            'status' => 'success', 
            'chats' => $enrichedChats,
            'isAdmin' => $isAdmin
        ]);
    }

    // Get messages for a chat (with sender names)
    public function getMessages($chatId = null) {
        if (!$chatId) {
            echo json_encode(['status' => 'error', 'message' => 'Chat ID required']);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        // Check access
        if ($this->isModerator($accountType)) {
            if (!$this->helpChatModel->canModeratorAccessChat($chatId, $userId)) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        } elseif (!$this->isAdmin($accountType)) {
            // Regular user - can only see their own chat
            $chat = $this->helpChatModel->getChatById($chatId);
            if (!$chat || $chat->user_id != $userId) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        }

        $messages = $this->helpMessageModel->getMessagesWithSenderInfo($chatId);
        echo json_encode(['status' => 'success', 'messages' => $messages]);
    }
    
    // Moderator: Claim a chat
    public function claimChat() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $chatId = $data['chat_id'] ?? null;
            $moderatorId = $_SESSION['user_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$chatId || !$moderatorId) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                return;
            }

            if (!$this->isModerator($accountType)) {
                echo json_encode(['status' => 'error', 'message' => 'Only moderators can claim chats']);
                return;
            }

            // Check if chat is already claimed
            $chat = $this->helpChatModel->getChatById($chatId);
            if (!$chat) {
                echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                return;
            }

            if ($chat->status === 'Assigned' && $chat->assigned_moderator_id != $moderatorId) {
                echo json_encode(['status' => 'error', 'message' => 'Chat already claimed by another moderator']);
                return;
            }

            if ($this->helpChatModel->assignChat($chatId, $moderatorId)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not claim chat']);
            }
        }
    }

    // Moderator: Close a chat
    public function closeChat() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $chatId = $data['chat_id'] ?? null;
            $moderatorId = $_SESSION['user_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$chatId || !$moderatorId) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                return;
            }

            if (!$this->isModerator($accountType)) {
                echo json_encode(['status' => 'error', 'message' => 'Only moderators can close chats']);
                return;
            }

            // Check ownership
            $chat = $this->helpChatModel->getChatById($chatId);
            if (!$chat) {
                echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                return;
            }

            if ($chat->assigned_moderator_id != $moderatorId) {
                echo json_encode(['status' => 'error', 'message' => 'You can only close your own chats']);
                return;
            }

            if ($this->helpChatModel->closeChat($chatId)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not close chat']);
            }
        }
    }

    // Mark messages as read
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $chatId = $data['chat_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$chatId) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                return;
            }

            $userType = $this->mapAccountType($accountType);

            if ($this->helpMessageModel->markAsRead($chatId, $userType)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not mark as read']);
            }
        }
    }

    // Get user's active chat (for user side)
    public function getUserActiveChat() {
        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $userType = $this->mapAccountType($accountType);

        $chat = $this->helpChatModel->getActiveChat($userId, $userType);
        
        if ($chat) {
            // Get messages
            $messages = $this->helpMessageModel->getMessagesWithSenderInfo($chat->id);
            echo json_encode([
                'status' => 'success', 
                'chat' => $chat,
                'messages' => $messages
            ]);
        } else {
            echo json_encode(['status' => 'no_chat']);
        }
    }
}
