CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    
    role ENUM('Admin', 'HR', 'Care-Taker','Mobile_Rider', 'Supervisor', 'Officer', 'Client') NOT NULL,
    
    status ENUM('Active', 'Inactive', 'Suspended', 'Retired') DEFAULT 'Active',
    
    profile_photo VARCHAR(255), -- Path to profile image
    address TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);