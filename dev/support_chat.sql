               
-- Table: help_chats
-- store  chat session between users and. moderators


CREATE TABLE IF NOT EXISTS help_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('Traveller', 'Guide', 'Driver') NOT NULL,
    assigned_moderator_id INT DEFAULT NULL,
    status ENUM('Open', 'Assigned', 'Closed') NOT NULL DEFAULT 'Open',
    subject VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    
    INDEX idx_user (user_id, user_type),
    INDEX idx_status (status),
    INDEX idx_moderator (assigned_moderator_id),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- individual chats karana tika store krnnwa

CREATE TABLE IF NOT EXISTS help_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    sender_type ENUM('Traveller', 'Guide', 'Driver', 'Moderator', 'Admin') NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (chat_id) REFERENCES help_chats(id) ON DELETE CASCADE,
    INDEX idx_chat_messages (chat_id, created_at ASC),
    INDEX idx_sender (sender_id, sender_type),
    INDEX idx_unread (chat_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Trigger: Admin has read only steps 

DELIMITER $$

CREATE TRIGGER trg_prevent_admin_messages
BEFORE INSERT ON help_messages
FOR EACH ROW
BEGIN
    IF NEW.sender_type = 'Admin' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Admins cannot send messages in support chats';
    END IF;
END$$

DELIMITER ;



-- Update help chat eka after new msg comming
-
DELIMITER $$

CREATE TRIGGER trg_update_chat_timestamp
AFTER INSERT ON help_messages
FOR EACH ROW
BEGIN
    UPDATE help_chats 
    SET updated_at = NOW() 
    WHERE id = NEW.chat_id;
END$$

DELIMITER ;


-- meken chat summery ekak.driver/guide/traveller 

CREATE OR REPLACE VIEW view_chat_summaries AS
SELECT 
    hc.id,
    hc.user_id,
    hc.user_type,
    hc.assigned_moderator_id,
    hc.status,
    hc.subject,
    hc.created_at,
    hc.updated_at,
    CASE 
        WHEN hc.user_type = 'Traveller' THEN (SELECT fullname FROM users WHERE user_id = hc.user_id)
        WHEN hc.user_type = 'Guide' THEN (SELECT fullname FROM guides WHERE guide_id = hc.user_id)
        WHEN hc.user_type = 'Driver' THEN (SELECT fullname FROM drivers WHERE driver_id = hc.user_id)
        ELSE 'Unknown'
    END AS user_name,
    (SELECT COUNT(*) FROM help_messages hm WHERE hm.chat_id = hc.id AND hm.is_read = 0 AND hm.sender_type != 'Moderator') AS unread_count,
    (SELECT message FROM help_messages hm WHERE hm.chat_id = hc.id ORDER BY hm.created_at DESC LIMIT 1) AS last_message,
    (SELECT created_at FROM help_messages hm WHERE hm.chat_id = hc.id ORDER BY hm.created_at DESC LIMIT 1) AS last_message_time
FROM help_chats hc
ORDER BY hc.updated_at DESC;

