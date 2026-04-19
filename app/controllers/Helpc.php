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

    private function normalizeTargetType($targetType) {
        $value = strtolower(trim((string)$targetType));

        if ($value === '' || $value === 'site' || $value === 'support' || $value === 'moderator') {
            return 'Site';
        }

        if ($value === 'driver') {
            return 'Driver';
        }

        if ($value === 'guide') {
            return 'Guide';
        }

        if ($value === 'traveller' || $value === 'traveler') {
            return 'Traveller';
        }

        if ($value === 'user') {
            return 'User';
        }

        return null;
    }

    private function buildChatSubject($targetType, $targetUserId = null) {
        if ($targetType === 'Site') {
            return 'SITE';
        }

        return strtoupper($targetType) . ':' . (int)$targetUserId;
    }

    private function parseChatSubject($subject) {
        $normalized = strtoupper(trim((string)$subject));

        if ($normalized === '' || $normalized === 'SITE') {
            return [
                'targetType' => 'Site',
                'targetUserId' => null
            ];
        }

        if (preg_match('/^(DRIVER|GUIDE|TRAVELLER|USER):(\d+)$/', $normalized, $matches) === 1) {
            return [
                'targetType' => ucfirst(strtolower($matches[1])),
                'targetUserId' => (int)$matches[2]
            ];
        }

        return [
            'targetType' => 'Site',
            'targetUserId' => null
        ];
    }

    private function isSiteSupportChat($chat) {
        if (!$chat) {
            return false;
        }

        $subject = $this->parseChatSubject($chat->subject ?? null);
        return $subject['targetType'] === 'Site';
    }

    private function isDirectUserChat($chat) {
        if (!$chat) {
            return false;
        }

        $subject = $this->parseChatSubject($chat->subject ?? null);
        return $subject['targetType'] === 'User' && (int)$subject['targetUserId'] > 0;
    }

    private function isDirectUserChatParticipant($chat, $userId) {
        if (!$this->isDirectUserChat($chat)) {
            return false;
        }

        $subject = $this->parseChatSubject($chat->subject ?? null);
        $ownerId = (int)($chat->user_id ?? 0);
        $targetId = (int)($subject['targetUserId'] ?? 0);

        return (int)$userId === $ownerId || (int)$userId === $targetId;
    }

    private function isProviderParticipant($chat, $userId, $userType) {
        if (!$chat || !$userId || !$userType) {
            return false;
        }

        $subject = $this->parseChatSubject($chat->subject ?? null);
        if ($subject['targetType'] === 'Site' || !$subject['targetUserId']) {
            return false;
        }

        return $subject['targetType'] === $userType && (int)$subject['targetUserId'] === (int)$userId;
    }

    private function canUserOpenTargetedChat($accountType, $userId, $targetType, $targetUserId, &$errorMessage = null) {
        if ($targetType === 'Site') {
            return true;
        }

        if (!$targetUserId) {
            $errorMessage = $targetType . ' ID is required';
            return false;
        }

        if ($targetType === 'Driver' || $targetType === 'Guide') {
            if ($accountType !== 'tourist') {
                $errorMessage = 'Only travellers can start chats with providers';
                return false;
            }

            if (!$this->helpChatModel->canTravellerContactProvider($userId, $targetType, $targetUserId)) {
                $errorMessage = 'You can only message providers linked to your trips';
                return false;
            }

            return true;
        }

        if ($targetType === 'Traveller') {
            if ($accountType !== 'driver' && $accountType !== 'guide') {
                $errorMessage = 'Only guides and drivers can start chats with travellers';
                return false;
            }

            $canContact = $accountType === 'driver'
                ? $this->helpChatModel->canDriverContactTraveller($userId, $targetUserId)
                : $this->helpChatModel->canGuideContactTraveller($userId, $targetUserId);

            if (!$canContact) {
                $errorMessage = 'You can only message travellers who requested your service';
                return false;
            }

            return true;
        }

        if ($targetType === 'User') {
            if ($accountType !== 'site_moderator' && $accountType !== 'admin') {
                $errorMessage = 'Only moderators and admins can start direct user chats';
                return false;
            }

            if ((int)$targetUserId === (int)$userId) {
                $errorMessage = 'You cannot start a chat with yourself';
                return false;
            }

            $targetUser = $this->helpChatModel->getUserBasicInfo((int)$targetUserId);
            if (!$targetUser) {
                $errorMessage = 'Target user not found';
                return false;
            }

            return true;
        }

        $errorMessage = 'Invalid target type';
        return false;
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
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = [];
            }

            // Get user info from session
            $userId = $_SESSION['user_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$userId || !$accountType) {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                return;
            }

            $userType = $this->mapAccountType($accountType);
            $targetType = $this->normalizeTargetType($input['target_type'] ?? 'site');

            if (!$targetType) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid target type']);
                return;
            }

            $targetUserId = isset($input['target_user_id']) ? (int)$input['target_user_id'] : null;

            $errorMessage = null;
            if (!$this->canUserOpenTargetedChat($accountType, $userId, $targetType, $targetUserId, $errorMessage)) {
                echo json_encode(['status' => 'error', 'message' => $errorMessage ?: 'Access denied']);
                return;
            }

            $chat = null;

            if ($targetType === 'Site') {
                $targetUserId = null;
                $subject = $this->buildChatSubject($targetType, $targetUserId);

                $chat = $this->helpChatModel->getActiveChat($userId, $userType, $subject);
                if (!$chat) {
                    $chatId = $this->helpChatModel->createChat($userId, $userType, $subject);
                    $chat = $this->helpChatModel->getChatById($chatId);
                }
            } elseif ($targetType === 'User' && ($accountType === 'site_moderator' || $accountType === 'admin')) {
                $subject = $this->buildChatSubject($targetType, $targetUserId);

                $chat = $this->helpChatModel->getDirectUserChat($userId, $targetUserId);
                if (!$chat) {
                    $chatId = $this->helpChatModel->createChat($userId, $userType, $subject);
                    $chat = $this->helpChatModel->getChatById($chatId);
                }
            } elseif ($targetType === 'Traveller' && ($accountType === 'driver' || $accountType === 'guide')) {
                // Reuse traveller-owned chat thread so both traveller and provider share one conversation.
                $providerType = $accountType === 'driver' ? 'Driver' : 'Guide';
                $subject = $this->buildChatSubject($providerType, $userId);
                $chat = $this->helpChatModel->getActiveChat($targetUserId, 'Traveller', $subject);

                if (!$chat) {
                    $chatId = $this->helpChatModel->createChat($targetUserId, 'Traveller', $subject);
                    $chat = $this->helpChatModel->getChatById($chatId);
                }
            } else {
                $subject = $this->buildChatSubject($targetType, $targetUserId);

                $chat = $this->helpChatModel->getActiveChat($userId, $userType, $subject);
                if (!$chat) {
                    $chatId = $this->helpChatModel->createChat($userId, $userType, $subject);
                    $chat = $this->helpChatModel->getChatById($chatId);
                }
            }

            echo json_encode([
                'status' => 'success',
                'chat' => $chat,
                'target' => [
                    'target_type' => $targetType,
                    'target_user_id' => $targetUserId
                ]
            ]);
        }
    }

    public function getTravellerSupportContacts() {
        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        if ($accountType !== 'tourist') {
            echo json_encode([
                'status' => 'success',
                'drivers' => [],
                'guides' => []
            ]);
            return;
        }

        $drivers = $this->helpChatModel->getTravellerDriverContacts($userId);
        $guides = $this->helpChatModel->getTravellerGuideContacts($userId);

        echo json_encode([
            'status' => 'success',
            'drivers' => $drivers,
            'guides' => $guides
        ]);
    }

    public function getDriverSupportContacts() {
        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        if ($accountType !== 'driver') {
            echo json_encode([
                'status' => 'success',
                'travellers' => []
            ]);
            return;
        }

        $travellers = $this->helpChatModel->getDriverTravellerContacts($userId);

        echo json_encode([
            'status' => 'success',
            'travellers' => $travellers
        ]);
    }

    public function getGuideSupportContacts() {
        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        if ($accountType !== 'guide') {
            echo json_encode([
                'status' => 'success',
                'travellers' => []
            ]);
            return;
        }

        $travellers = $this->helpChatModel->getGuideTravellerContacts($userId);

        echo json_encode([
            'status' => 'success',
            'travellers' => $travellers
        ]);
    }

    public function searchUsersForModerator() {
        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        if (!$this->isModerator($accountType) && !$this->isAdmin($accountType)) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $query = trim((string)($_GET['q'] ?? ''));
        if ($query === '') {
            echo json_encode([
                'status' => 'success',
                'users' => []
            ]);
            return;
        }

        $users = $this->helpChatModel->searchUsersForModerator($query, (int)$userId, 20);
        $mappedUsers = [];

        foreach ($users as $user) {
            $mappedUsers[] = [
                'id' => (int)$user->id,
                'fullname' => $user->fullname,
                'account_type' => $user->account_type,
                'user_type' => $this->mapAccountType($user->account_type),
                'email' => $user->email,
                'profile_photo' => $user->profile_photo
            ];
        }

        echo json_encode([
            'status' => 'success',
            'users' => $mappedUsers
        ]);
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
            $chat = $this->helpChatModel->getChatById($chatId);

            if (!$chat) {
                echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                return;
            }

            $isSiteSupportChat = $this->isSiteSupportChat($chat);
            $isDirectUserChat = $this->isDirectUserChat($chat);

            if ($this->isModerator($accountType)) {
                if ($isSiteSupportChat) {
                    if ($chat->status === 'Open') {
                        echo json_encode(['status' => 'error', 'message' => 'Please claim this chat first']);
                        return;
                    }
                    if ($chat->assigned_moderator_id && $chat->assigned_moderator_id != $senderId) {
                        echo json_encode(['status' => 'error', 'message' => 'This chat is assigned to another moderator']);
                        return;
                    }
                } elseif ($isDirectUserChat) {
                    if (!$this->isDirectUserChatParticipant($chat, $senderId)) {
                        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                        return;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Unsupported chat type for moderators']);
                    return;
                }
            } elseif ($this->isAdmin($accountType)) {
                // Admins remain view-only for site support queue, but can reply in direct USER chats.
                if ($isSiteSupportChat) {
                    echo json_encode(['status' => 'error', 'message' => 'Admins cannot send messages in site support chats']);
                    return;
                }

                if (!$isDirectUserChat || !$this->isDirectUserChatParticipant($chat, $senderId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } else {
                $isOwner = (int)$chat->user_id === (int)$senderId;
                $isProviderParticipant = $this->isProviderParticipant($chat, $senderId, $senderType);

                if (!$isOwner && !$isProviderParticipant) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            }

            $messageId = $this->helpMessageModel->addMessage($chatId, $senderId, $senderType, $message);
            if ($messageId) {
                echo json_encode(['status' => 'success', 'message_id' => $messageId]);
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
            // Admin can see all site queue chats plus direct USER chats they participate in.
            $siteChats = $this->helpChatModel->getAllChats();
            $directChats = $this->helpChatModel->getDirectUserChatsForViewer((int)$userId);

            $chatMap = [];
            foreach (array_merge($siteChats, $directChats) as $chat) {
                $chatMap[(int)$chat->id] = $chat;
            }

            $chats = array_values($chatMap);
        } else {
            // Moderators see open chats + their assigned chats
            $chats = $this->helpChatModel->getChatsForModerator($userId);
        }

        // Enrich chat data with user info
        $enrichedChats = [];
        foreach ($chats as $chat) {
            $chatData = (array) $chat;

            $subjectMeta = $this->parseChatSubject($chat->subject ?? null);
            $chatData['target_type'] = $subjectMeta['targetType'];
            $chatData['target_user_id'] = $subjectMeta['targetUserId'];
            $chatData['is_site_chat'] = ($subjectMeta['targetType'] === 'Site');

            if ($subjectMeta['targetType'] === 'User' && (int)$subjectMeta['targetUserId'] > 0) {
                $ownerId = (int)$chat->user_id;
                $targetId = (int)$subjectMeta['targetUserId'];
                $counterpartId = ($ownerId === (int)$userId) ? $targetId : $ownerId;

                $counterpart = $this->helpChatModel->getUserBasicInfo($counterpartId);
                if ($counterpart) {
                    $chatData['user_name'] = $counterpart->fullname;
                    $chatData['user_type'] = $this->mapAccountType($counterpart->account_type);
                    $chatData['user_account_type'] = $counterpart->account_type;
                    $chatData['user_id'] = (int)$counterpart->id;
                } else {
                    $chatData['user_name'] = 'Unknown User';
                    $chatData['user_type'] = 'User';
                    $chatData['user_account_type'] = null;
                    $chatData['user_id'] = $counterpartId;
                }

                $chatData['is_mine'] = $this->isDirectUserChatParticipant($chat, $userId);
            } else {
                // Site support queue chat
                $chatWithInfo = $this->helpChatModel->getChatWithUserInfo($chat->id);
                $chatData['user_name'] = $chatWithInfo ? $chatWithInfo->user_name : 'Unknown User';
                $chatData['user_type'] = $chat->user_type;
                $chatData['user_account_type'] = null;
                $chatData['user_id'] = (int)$chat->user_id;
                $chatData['is_mine'] = ($chat->assigned_moderator_id == $userId);
            }

            // Get unread count for current viewer
            $chatData['unread_count'] = $this->helpMessageModel->getUnreadCountForViewer($chat->id, (int)$userId);
            
            // Get last message preview
            $lastMessage = $this->helpMessageModel->getLastMessage($chat->id);
            $chatData['last_message'] = $lastMessage ? $lastMessage->message : null;
            $chatData['updated_at'] = $lastMessage ? $lastMessage->created_at : $chat->created_at;
            
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
        $chat = $this->helpChatModel->getChatById($chatId);

        if (!$chat) {
            echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
            return;
        }

        // Check access
        if ($this->isModerator($accountType)) {
            if ($this->isSiteSupportChat($chat)) {
                if (!$this->helpChatModel->canModeratorAccessChat($chatId, $userId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } elseif ($this->isDirectUserChat($chat)) {
                if (!$this->isDirectUserChatParticipant($chat, $userId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        } elseif (!$this->isAdmin($accountType)) {
            $userType = $this->mapAccountType($accountType);
            $isOwner = (int)$chat->user_id === (int)$userId;
            $isProviderParticipant = $this->isProviderParticipant($chat, $userId, $userType);

            // Regular users can see their own chats and provider-targeted chats assigned to them.
            if (!$isOwner && !$isProviderParticipant) {
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

            if (!$this->isSiteSupportChat($chat)) {
                echo json_encode(['status' => 'error', 'message' => 'Only site support chats can be claimed']);
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

            if (!$this->isModerator($accountType) && !$this->isAdmin($accountType)) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }

            // Check ownership
            $chat = $this->helpChatModel->getChatById($chatId);
            if (!$chat) {
                echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                return;
            }

            if ($this->isSiteSupportChat($chat)) {
                if (!$this->isModerator($accountType)) {
                    echo json_encode(['status' => 'error', 'message' => 'Admins cannot close site support chats']);
                    return;
                }

                if ($chat->assigned_moderator_id != $moderatorId) {
                    echo json_encode(['status' => 'error', 'message' => 'You can only close your own chats']);
                    return;
                }
            } elseif ($this->isDirectUserChat($chat)) {
                if (!$this->isDirectUserChatParticipant($chat, $moderatorId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unsupported chat type']);
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
            $userId = $_SESSION['user_id'] ?? null;
            $accountType = $_SESSION['user_account_type'] ?? null;

            if (!$chatId || !$userId || !$accountType) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
                return;
            }

            $chat = $this->helpChatModel->getChatById($chatId);
            if (!$chat) {
                echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
                return;
            }

            if ($this->isModerator($accountType)) {
                if ($this->isSiteSupportChat($chat)) {
                    if (!$this->helpChatModel->canModeratorAccessChat($chatId, $userId)) {
                        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                        return;
                    }
                } elseif ($this->isDirectUserChat($chat)) {
                    if (!$this->isDirectUserChatParticipant($chat, $userId)) {
                        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                        return;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } elseif (!$this->isAdmin($accountType)) {
                $userType = $this->mapAccountType($accountType);
                $isOwner = (int)$chat->user_id === (int)$userId;
                $isProviderParticipant = $this->isProviderParticipant($chat, $userId, $userType);

                if (!$isOwner && !$isProviderParticipant) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            }

            $userType = $this->mapAccountType($accountType);

            if ($this->helpMessageModel->markAsRead($chatId, $userType, (int)$userId)) {
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
        $targetType = $this->normalizeTargetType($_GET['target_type'] ?? 'site');

        if (!$targetType) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid target type']);
            return;
        }

        $targetUserId = isset($_GET['target_user_id']) ? (int)$_GET['target_user_id'] : null;

        $errorMessage = null;
        if (!$this->canUserOpenTargetedChat($accountType, $userId, $targetType, $targetUserId, $errorMessage)) {
            echo json_encode(['status' => 'error', 'message' => $errorMessage ?: 'Access denied']);
            return;
        }

        if ($targetType === 'Site') {
            $targetUserId = null;
            $subject = $this->buildChatSubject($targetType, $targetUserId);
            $chat = $this->helpChatModel->getActiveChat($userId, $userType, $subject);
        } elseif ($targetType === 'User' && ($accountType === 'site_moderator' || $accountType === 'admin')) {
            $chat = $this->helpChatModel->getDirectUserChat($userId, $targetUserId);
        } elseif ($targetType === 'Traveller' && ($accountType === 'driver' || $accountType === 'guide')) {
            // Driver/Guide opens the same traveller-owned chat thread keyed by PROVIDER:<providerId>.
            $providerType = $accountType === 'driver' ? 'Driver' : 'Guide';
            $subject = $this->buildChatSubject($providerType, $userId);
            $chat = $this->helpChatModel->getActiveChat($targetUserId, 'Traveller', $subject);
        } else {
            $subject = $this->buildChatSubject($targetType, $targetUserId);
            $chat = $this->helpChatModel->getActiveChat($userId, $userType, $subject);
        }
        
        if ($chat) {
            // Get messages
            $messages = $this->helpMessageModel->getMessagesWithSenderInfo($chat->id);
            echo json_encode([
                'status' => 'success', 
                'chat' => $chat,
                'messages' => $messages,
                'target' => [
                    'target_type' => $targetType,
                    'target_user_id' => $targetUserId
                ]
            ]);
        } else {
            echo json_encode(['status' => 'no_chat']);
        }
    }

    // Delete a single message
    // - Moderator: can delete messages inside chats assigned to them
    // - Regular user: can delete only their own messages inside their own chat
    public function deleteMessage() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $messageId = $data['message_id'] ?? null;

        if (!$messageId) {
            echo json_encode(['status' => 'error', 'message' => 'Message ID required']);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $message = $this->helpMessageModel->getMessageById($messageId);
        if (!$message) {
            echo json_encode(['status' => 'error', 'message' => 'Message not found']);
            return;
        }

        $chat = $this->helpChatModel->getChatById($message->chat_id);
        if (!$chat) {
            echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
            return;
        }

        $userType = $this->mapAccountType($accountType);
        $requireOwnMessage = true;

        if ($this->isModerator($accountType)) {
            if ($this->isSiteSupportChat($chat)) {
                // Site support moderation queue: assigned moderator can remove any message in the thread.
                if ($chat->status !== 'Assigned' || $chat->assigned_moderator_id != $userId) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
                $requireOwnMessage = false;
            } elseif ($this->isDirectUserChat($chat)) {
                if (!$this->isDirectUserChatParticipant($chat, $userId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                    return;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        } elseif ($this->isAdmin($accountType)) {
            if ($this->isSiteSupportChat($chat)) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }

            if (!$this->isDirectUserChat($chat) || !$this->isDirectUserChatParticipant($chat, $userId)) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        } else {
            $isOwner = (int)$chat->user_id === (int)$userId;
            $isProviderParticipant = $this->isProviderParticipant($chat, $userId, $userType);

            if (!$isOwner && !$isProviderParticipant) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        }

        if ($requireOwnMessage) {
            if ($message->sender_id != $userId || $message->sender_type !== $userType) {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                return;
            }
        }

        if ($this->helpMessageModel->deleteMessage($messageId)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not delete message']);
        }
    }

    // Delete a chat (hard delete)
    // Moderator only: can delete chats assigned to them.
    public function deleteChat() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $chatId = $data['chat_id'] ?? null;

        if (!$chatId) {
            echo json_encode(['status' => 'error', 'message' => 'Chat ID required']);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $accountType = $_SESSION['user_account_type'] ?? null;

        if (!$userId || !$accountType) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        if (!$this->isModerator($accountType)) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $chat = $this->helpChatModel->getChatById($chatId);
        if (!$chat) {
            echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
            return;
        }

        if (!$this->isSiteSupportChat($chat)) {
            echo json_encode(['status' => 'error', 'message' => 'Only site support chats can be deleted by moderators']);
            return;
        }

        if ($chat->status !== 'Assigned' || $chat->assigned_moderator_id != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        // Delete messages first (no FK cascade assumed)
        $this->helpMessageModel->deleteMessagesByChatId($chatId);

        if ($this->helpChatModel->deleteChat($chatId)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not delete chat']);
        }
    }
}