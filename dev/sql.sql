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
    verified INT NOT NULL DEFAULT 0,
    driver_data JSON,
    guide_tourist_data JSON,
    remember_token VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--driver_data JSON is like below,
{
    "id_back": "/signup/nic/nic_back_driver_6967861e26a29.png",
    "id_front": "/signup/nic/nic_front_driver_6967861e2687c.png",
    "license_back": "/drivers/36/licenses/driver_license_licenseBack_1769139004.png",
    "license_front": "/signup/license/license_front_6967861e26429.png",
    "license_number": "64205423jbhh",
    "license_expire_date": "2026-01-15"
}

guide_tourist_data JSON is like below,
{
    "nic_back": "img/signup/nic_back_68fb05daaf416.jpg",
    "nic_front": "img/signup/nic_front_68fb05daaf18c.jpg",
    "nic_passport": "200216901548"
}