<?php

class HelpChat {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function normalizeSubject($subject) {
        $normalized = strtoupper(trim((string)$subject));
        return $normalized === '' ? 'SITE' : $normalized;
    }

    private function siteChatFilterSql($alias = '') {
        $prefix = '';
        if ($alias !== '') {
            $prefix = rtrim($alias, '.') . '.';
        }

        return '(' . $prefix . 'subject IS NULL OR ' . $prefix . 'subject = "" OR UPPER(' . $prefix . 'subject) = "SITE")';
    }

    private function tableExists($tableName) {
        $this->db->query('SHOW TABLES LIKE :tableName');
        $this->db->bind(':tableName', (string)$tableName);
        return (bool)$this->db->single();
    }

    private function getDriverRequestContactTables() {
        $tables = [];

        if ($this->tableExists('traveller_side_t_requests')) {
            $tables[] = 'traveller_side_t_requests';
        }

        if ($this->tableExists('traveller_side_d_requests')) {
            $tables[] = 'traveller_side_d_requests';
        }

        return $tables;
    }

    public function getUserBasicInfo($userId) {
        $this->db->query('SELECT id, fullname, account_type, profile_photo, email
            FROM users
            WHERE id = :id
            LIMIT 1');
        $this->db->bind(':id', (int)$userId);
        return $this->db->single();
    }

    public function searchUsersForModerator($query, $excludeUserId = null, $limit = 20) {
        $term = trim((string)$query);
        if ($term === '') {
            return [];
        }

        $safeLimit = (int)$limit;
        if ($safeLimit < 1) {
            $safeLimit = 20;
        }
        if ($safeLimit > 50) {
            $safeLimit = 50;
        }

        $hasExactId = ctype_digit($term);
        $exactId = $hasExactId ? (int)$term : 0;
        $pattern = '%' . $term . '%';

        $querySql = 'SELECT id, fullname, account_type, profile_photo, email
            FROM users
            WHERE 1 = 1';

        if ($excludeUserId !== null) {
            $querySql .= ' AND id != :exclude_id';
        }

        $querySql .= ' AND (
                fullname LIKE :search_pattern
                OR email LIKE :search_pattern
                OR account_type LIKE :search_pattern';

        if ($hasExactId) {
            $querySql .= ' OR id = :exact_id';
        }

        $querySql .= ' )
            ORDER BY
                CASE WHEN id = :rank_exact_id THEN 0 ELSE 1 END,
                fullname ASC
            LIMIT ' . $safeLimit;

        $this->db->query($querySql);
        $this->db->bind(':search_pattern', $pattern);
        $this->db->bind(':rank_exact_id', $exactId);

        if ($excludeUserId !== null) {
            $this->db->bind(':exclude_id', (int)$excludeUserId);
        }

        if ($hasExactId) {
            $this->db->bind(':exact_id', $exactId);
        }

        return $this->db->resultSet();
    }

    public function getDirectUserChat($userId, $targetUserId) {
        $userId = (int)$userId;
        $targetUserId = (int)$targetUserId;

        if ($userId <= 0 || $targetUserId <= 0 || $userId === $targetUserId) {
            return null;
        }

        $subjectToTarget = 'USER:' . $targetUserId;
        $subjectToUser = 'USER:' . $userId;

        $this->db->query('SELECT *
            FROM help_chats
            WHERE status != "Closed"
              AND (
                    (user_id = :user_id AND (CASE WHEN subject IS NULL OR subject = "" THEN "SITE" ELSE UPPER(subject) END) = :subject_to_target)
                    OR
                    (user_id = :target_user_id AND (CASE WHEN subject IS NULL OR subject = "" THEN "SITE" ELSE UPPER(subject) END) = :subject_to_user)
                  )
            ORDER BY created_at DESC
            LIMIT 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':target_user_id', $targetUserId);
        $this->db->bind(':subject_to_target', $subjectToTarget);
        $this->db->bind(':subject_to_user', $subjectToUser);

        return $this->db->single();
    }

    // Get active chat for a user
    public function getActiveChat($userId, $userType, $subject = 'SITE') {
        $this->db->query('SELECT * FROM help_chats
            WHERE user_id = :user_id
              AND user_type = :user_type
              AND status != "Closed"
              AND (CASE WHEN subject IS NULL OR subject = "" THEN "SITE" ELSE UPPER(subject) END) = :subject
            ORDER BY created_at DESC
            LIMIT 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':user_type', $userType);
        $this->db->bind(':subject', $this->normalizeSubject($subject));
        return $this->db->single();
    }

    // Create a new chat
    public function createChat($userId, $userType, $subject = 'SITE') {
        $this->db->query('INSERT INTO help_chats (user_id, user_type, subject, status, created_at)
            VALUES (:user_id, :user_type, :subject, "Open", NOW())');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':user_type', $userType);
        $this->db->bind(':subject', $this->normalizeSubject($subject));
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
                $this->db->query('SELECT * FROM help_chats
                        WHERE status IN ("Open", "Assigned")
                            AND ' . $this->siteChatFilterSql() . '
                        ORDER BY created_at DESC');
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
        $subjectCase = '(CASE WHEN subject IS NULL OR subject = "" THEN "SITE" ELSE UPPER(subject) END)';
        $directSubject = 'USER:' . (int)$moderatorId;

        $this->db->query('SELECT * FROM help_chats
            WHERE (
                    ((status = "Open") AND ' . $this->siteChatFilterSql() . ')
                    OR
                    ((status = "Assigned" AND assigned_moderator_id = :mod_id) AND ' . $this->siteChatFilterSql() . ')
                    OR
                    (
                        status != "Closed"
                        AND (
                            (user_id = :mod_id2 AND ' . $subjectCase . ' LIKE "USER:%")
                            OR (' . $subjectCase . ' = :direct_subject)
                        )
                    )
                  )
            ORDER BY
                CASE
                    WHEN status = "Assigned" AND assigned_moderator_id = :mod_id3 THEN 0
                    WHEN (' . $subjectCase . ' = :direct_subject2 OR (user_id = :mod_id4 AND ' . $subjectCase . ' LIKE "USER:%")) THEN 1
                    WHEN status = "Open" AND ' . $this->siteChatFilterSql() . ' THEN 2
                    ELSE 3
                END,
                created_at DESC');
        $this->db->bind(':mod_id', (int)$moderatorId);
        $this->db->bind(':mod_id2', (int)$moderatorId);
        $this->db->bind(':mod_id3', (int)$moderatorId);
        $this->db->bind(':mod_id4', (int)$moderatorId);
        $this->db->bind(':direct_subject', $directSubject);
        $this->db->bind(':direct_subject2', $directSubject);
        return $this->db->resultSet();
    }

    // Get direct USER chats where the given viewer is either the owner or target participant.
    public function getDirectUserChatsForViewer($viewerId) {
        $subjectCase = '(CASE WHEN subject IS NULL OR subject = "" THEN "SITE" ELSE UPPER(subject) END)';
        $directSubject = 'USER:' . (int)$viewerId;

        $this->db->query('SELECT * FROM help_chats
            WHERE status != "Closed"
              AND (
                    (user_id = :viewer_id_owner AND ' . $subjectCase . ' LIKE "USER:%")
                    OR (' . $subjectCase . ' = :direct_subject)
                  )
            ORDER BY created_at DESC');
        $this->db->bind(':viewer_id_owner', (int)$viewerId);
        $this->db->bind(':direct_subject', $directSubject);
        return $this->db->resultSet();
    }

    // Get all chats for admin (view-only)
    public function getAllChats() {
                $this->db->query('SELECT * FROM help_chats
                        WHERE status IN ("Open", "Assigned")
                            AND ' . $this->siteChatFilterSql() . '
                        ORDER BY created_at DESC');
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

    public function getTravellerDriverContacts($travellerId) {
        $this->db->query('SELECT
                u.id,
                u.fullname AS name,
                u.profile_photo,
                MAX(COALESCE(tsdr.updatedAt, tsdr.createdAt)) AS lastInteraction
            FROM traveller_side_d_requests tsdr
            INNER JOIN users u
                ON u.id = tsdr.driverId
               AND u.account_type = "driver"
            WHERE tsdr.rqUserId = :traveller_id
              AND tsdr.requestStatus IN ("pending", "requested", "accepted", "completed")
            GROUP BY u.id, u.fullname, u.profile_photo
            ORDER BY lastInteraction DESC');
        $this->db->bind(':traveller_id', $travellerId);
        return $this->db->resultSet();
    }

    public function getTravellerGuideContacts($travellerId) {
        $this->db->query('SELECT
                u.id,
                u.fullname AS name,
                u.profile_photo,
                MAX(COALESCE(tsgr.updatedAt, tsgr.createdAt)) AS lastInteraction
            FROM traveller_side_g_requests tsgr
            INNER JOIN users u
                ON u.id = tsgr.guideId
               AND u.account_type = "guide"
            WHERE tsgr.userId = :traveller_id
              AND tsgr.guideId IS NOT NULL
              AND tsgr.status IN ("pending", "requested", "accepted", "completed")
            GROUP BY u.id, u.fullname, u.profile_photo
            ORDER BY lastInteraction DESC');
        $this->db->bind(':traveller_id', $travellerId);
        return $this->db->resultSet();
    }

    public function getDriverTravellerContacts($driverId) {
        $tables = $this->getDriverRequestContactTables();
        if (count($tables) === 0) {
            return [];
        }

        $segments = [];
        foreach ($tables as $table) {
            $segments[] = 'SELECT rqUserId AS travellerId, COALESCE(updatedAt, createdAt) AS lastInteraction
                          FROM ' . $table . '
                          WHERE driverId = :driver_id
                            AND requestStatus IN ("pending", "requested", "accepted", "completed")';
        }

        $query = 'SELECT
                    u.id,
                    u.fullname AS name,
                    u.profile_photo,
                    MAX(req.lastInteraction) AS lastInteraction
                  FROM (' . implode(' UNION ALL ', $segments) . ') req
                  INNER JOIN users u
                    ON u.id = req.travellerId
                   AND u.account_type = "tourist"
                  GROUP BY u.id, u.fullname, u.profile_photo
                  ORDER BY lastInteraction DESC';

        $this->db->query($query);
        $this->db->bind(':driver_id', $driverId);
        return $this->db->resultSet();
    }

    public function getGuideTravellerContacts($guideId) {
        if (!$this->tableExists('traveller_side_g_requests')) {
            return [];
        }

        $this->db->query('SELECT
                u.id,
                u.fullname AS name,
                u.profile_photo,
                MAX(COALESCE(tsgr.updatedAt, tsgr.requestedAt, tsgr.createdAt)) AS lastInteraction
            FROM traveller_side_g_requests tsgr
            INNER JOIN users u
                ON u.id = tsgr.userId
               AND u.account_type = "tourist"
            WHERE tsgr.guideId = :guide_id
              AND tsgr.status IN ("pending", "requested", "accepted", "completed")
            GROUP BY u.id, u.fullname, u.profile_photo
            ORDER BY lastInteraction DESC');
        $this->db->bind(':guide_id', (int)$guideId);
        return $this->db->resultSet();
    }

    public function canTravellerContactProvider($travellerId, $targetType, $targetUserId) {
        $target = strtoupper(trim((string)$targetType));

        if ($target === 'DRIVER') {
            $this->db->query('SELECT requestId
                FROM traveller_side_d_requests
                WHERE rqUserId = :traveller_id
                  AND driverId = :provider_id
                  AND requestStatus IN ("pending", "requested", "accepted", "completed")
                LIMIT 1');
        } elseif ($target === 'GUIDE') {
            $this->db->query('SELECT id
                FROM traveller_side_g_requests
                WHERE userId = :traveller_id
                  AND guideId = :provider_id
                  AND status IN ("pending", "requested", "accepted", "completed")
                LIMIT 1');
        } else {
            return false;
        }

        $this->db->bind(':traveller_id', $travellerId);
        $this->db->bind(':provider_id', $targetUserId);

        return (bool)$this->db->single();
    }

    public function canDriverContactTraveller($driverId, $travellerId) {
        $tables = $this->getDriverRequestContactTables();
        if (count($tables) === 0) {
            return false;
        }

        $segments = [];
        foreach ($tables as $table) {
            $segments[] = 'SELECT rqUserId AS travellerId
                          FROM ' . $table . '
                          WHERE driverId = :driver_id
                            AND rqUserId = :traveller_id
                            AND requestStatus IN ("pending", "requested", "accepted", "completed")';
        }

        $query = 'SELECT travellerId FROM (' . implode(' UNION ALL ', $segments) . ') contact_rows LIMIT 1';

        $this->db->query($query);
        $this->db->bind(':driver_id', (int)$driverId);
        $this->db->bind(':traveller_id', (int)$travellerId);

        return (bool)$this->db->single();
    }

    public function canGuideContactTraveller($guideId, $travellerId) {
        if (!$this->tableExists('traveller_side_g_requests')) {
            return false;
        }

        $this->db->query('SELECT id
            FROM traveller_side_g_requests
            WHERE guideId = :guide_id
              AND userId = :traveller_id
              AND status IN ("pending", "requested", "accepted", "completed")
            LIMIT 1');
        $this->db->bind(':guide_id', (int)$guideId);
        $this->db->bind(':traveller_id', (int)$travellerId);

        return (bool)$this->db->single();
    }

    // Delete a chat by ID (hard delete)
    public function deleteChat($chatId) {
        $this->db->query('DELETE FROM help_chats WHERE id = :id');
        $this->db->bind(':id', $chatId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}