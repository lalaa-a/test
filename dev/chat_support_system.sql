
CREATE DATABASE IF NOT EXISTS chat_support_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;


--roles
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB;

--users
CREATE TABLE users (
    user_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
)ENGINE=InnoDB;

--chts

CREATE TABLE chats (
    chat_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    status ENUM('open', 'assigned', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)ENGINE=InnoDB;

-- Modaratorsla assign krna eka
-- One chat can have only onr moderator

CREATE TABLE moderator_assignments (
    assignment_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL UNIQUE,
    moderator_id BIGINT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(chat_id),
    FOREIGN KEY (moderator_id) REFERENCES users(user_id)
)ENGINE=InnoDB;


-- Messages

CREATE TABLE messages (
    message_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL,
    sender_id BIGINT NOT NULL,
    message_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(chat_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
)ENGINE=InnoDB;


-- indexce what to do performance

CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_chats_user ON chats(user_id);
CREATE INDEX idx_chats_status ON chats(status);
CREATE INDEX idx_messages_chat ON messages(chat_id);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_assignments_moderator ON moderator_assignments(moderator_id);


--triggering and constant logic

DELIMITER $$

-- Only moderators can be assigned
CREATE TRIGGER trg_check_moderator_role
BEFORE INSERT ON moderator_assignments
FOR EACH ROW
BEGIN
    IF (SELECT r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = NEW.moderator_id) <> 'MODERATOR'
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only moderators can be assigned to chats';
    END IF;
END$$

-- Admins cannot ssend msg
CREATE TRIGGER trg_prevent_admin_messages
BEFORE INSERT ON messages
FOR EACH ROW
BEGIN
    IF (SELECT r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = NEW.sender_id) = 'ADMIN'
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Admins cannot send messages';
    END IF;
END$$

DELIMITER ;

