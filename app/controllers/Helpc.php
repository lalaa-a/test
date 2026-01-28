<?php

class Helpc extends Controller {
    private $helpChatModel;
    private $helpMessageModel;

    public function __construct() {
        // Load Models
        $this->helpChatModel = $this->model('HelpChat');
        $this->helpMessageModel = $this->model('HelpMessage');
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
            // Assuming user is logged in and we have their ID and Type in session
            $userId = $_SESSION['user_id'] ?? 1; // Fallback for dev
            $userType = $_SESSION['user_type'] ?? 'Traveller'; 

            // Check if active chat exists
            $chat = $this->helpChatModel->getActiveChat($userId, $userType);
            
            if (!$chat) {
                $chatId = $this->helpChatModel->createChat($userId, $userType);
                $chat = $this->helpChatModel->getChatById($chatId);
            }

            echo json_encode(['status' => 'success', 'chat' => $chat]);
        }
    }

    // Send a message
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $chatId = $data['chat_id'];
            $message = $data['message'];
            // Get sender info from session
            $senderId = $_SESSION['user_id'] ?? 1; 
            $senderType = $_SESSION['user_type'] ?? 'Traveller'; 

            // Exclusivity Check for Moderators
            if ($senderType === 'Moderator') {
                $chat = $this->helpChatModel->getChatById($chatId);
                if ($chat->assigned_moderator_id && $chat->assigned_moderator_id != $senderId) {
                    echo json_encode(['status' => 'error', 'message' => 'This chat is assigned to another moderator.']);
                    return;
                }
            }

            if ($this->helpMessageModel->addMessage($chatId, $senderId, $senderType, $message)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    // Get all open chats
    public function getOpenChats() {
        $chats = $this->helpChatModel->getOpenChats();
        echo json_encode(['status' => 'success', 'chats' => $chats]);
    }

    // Get messages for a chat
    public function getMessages($chatId) {
        $messages = $this->helpMessageModel->getMessagesByChatId($chatId);
        echo json_encode(['status' => 'success', 'messages' => $messages]);
    }
    
    // Moderator: Claim a chat
    public function claimChat() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $chatId = $data['chat_id'];
            $moderatorId = $_SESSION['user_id']; 
            
            if ($this->helpChatModel->assignChat($chatId, $moderatorId)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not claim chat']);
            }
        }
    }
}
