-- dev/sql.sql
CREATE DATABASE IF NOT EXISTS tripingoo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tripingoo;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_type ENUM('driver', 'guide', 'tourist', 'admin', 'site_moderator', 'business_manager') NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    language VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(20) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    secondary_phone VARCHAR(25),
    address TEXT NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    driver_data JSON,
    guide_tourist_data JSON,
    remember_token VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;