-- Notifications table for admin dashboard
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    notification_type ENUM('payment', 'verification', 'help', 'cancellation', 'general') DEFAULT 'general',
    user_id INT,
    status ENUM('unread', 'read', 'resolved') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add sample notifications
INSERT INTO notifications (title, content, notification_type, user_id, status, created_at) VALUES
('Payment Issue', 'chiran reports payment went through but no confirmation received for itinerary ITN-001.', 'payment', 1, 'unread', NOW() - INTERVAL 2 HOUR),
('Profile Update Help', 'sewmini needs assistance updating her guide profile information and certification documents.', 'help', 1, 'unread', NOW() - INTERVAL 5 HOUR),
('Driver Verification', 'chiran submitted new vehicle registration documents for verification as a driver.', 'verification', 1, 'unread', NOW() - INTERVAL 1 DAY),
('Itinerary Cancellation', 'akila requests cancellation of itinerary ITN-005 due to personal emergency.', 'cancellation', 1, 'unread', NOW() - INTERVAL 1 DAY),
('New Driver Registration', 'New driver kasun has completed registration and awaits admin verification.', 'verification', 1, 'unread', NOW() - INTERVAL 3 HOUR),
('Booking Inquiry', 'User lalinda inquires about availability for a 7-day tour package in February.', 'general', 1, 'unread', NOW() - INTERVAL 6 HOUR);
