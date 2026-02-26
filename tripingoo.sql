-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 05:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tripingoo`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_verifications`
--

CREATE TABLE `account_verifications` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewedBy` int(11) DEFAULT NULL,
  `reviewedAt` timestamp NULL DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `rejectionReason` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_verifications`
--

INSERT INTO `account_verifications` (`id`, `userId`, `status`, `reviewedBy`, `reviewedAt`, `expiryDate`, `rejectionReason`, `createdAt`, `updatedAt`) VALUES
(2, 12, 'approved', 25, '2026-01-29 12:11:15', NULL, NULL, '2026-01-28 03:54:12', '2026-01-29 12:11:15'),
(3, 36, 'approved', 25, '2026-01-27 20:01:56', NULL, NULL, '2026-01-25 12:40:21', '2026-01-27 20:01:56'),
(4, 17, 'pending', NULL, NULL, NULL, NULL, '2026-01-29 12:18:02', '2026-02-09 08:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `commission_rates`
--

CREATE TABLE `commission_rates` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL COMMENT 'guide or driver',
  `rate` decimal(5,2) NOT NULL DEFAULT 15.00,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commission_rates`
--

INSERT INTO `commission_rates` (`id`, `role`, `rate`, `updated_at`, `updated_by`) VALUES
(1, 'guide', 15.00, '2026-02-21 19:04:52', 11),
(2, 'driver', 13.00, '2026-02-21 19:10:33', 26);

-- --------------------------------------------------------

--
-- Table structure for table `commission_rate_history`
--

CREATE TABLE `commission_rate_history` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `old_rate` decimal(5,2) NOT NULL,
  `new_rate` decimal(5,2) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `change_date` datetime DEFAULT current_timestamp(),
  `effective_from` date NOT NULL,
  `reason` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commission_rate_history`
--

INSERT INTO `commission_rate_history` (`id`, `role`, `old_rate`, `new_rate`, `changed_by`, `change_date`, `effective_from`, `reason`) VALUES
(1, 'guide', 15.00, 18.00, 11, '2026-02-21 19:04:52', '2026-05-01', 'Rate revision'),
(2, 'driver', 12.00, 14.00, 11, '2026-02-21 19:04:52', '2026-05-01', 'Rate revision'),
(3, 'driver', 12.00, 13.00, 26, '2026-02-21 19:10:33', '2026-03-21', 'ffd');

-- --------------------------------------------------------

--
-- Table structure for table `cover_photos`
--

CREATE TABLE `cover_photos` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `photo_order` tinyint(4) DEFAULT 0,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cover_photos`
--

INSERT INTO `cover_photos` (`id`, `userId`, `photo_path`, `photo_order`, `uploaded_at`, `is_active`) VALUES
(72, 17, '/drivers/17/cover_photos/cover_1769693209_5.png', 4, '2026-01-29 13:26:49', 1),
(71, 17, '/drivers/17/cover_photos/cover_1769693209_4.jpg', 3, '2026-01-29 13:26:49', 1),
(70, 17, '/drivers/17/cover_photos/cover_1769693152_3.png', 2, '2026-01-29 13:25:52', 1),
(69, 17, '/drivers/17/cover_photos/cover_1769687529_2.png', 1, '2026-01-29 11:52:09', 1),
(68, 17, '/drivers/17/cover_photos/cover_1769687529_1.png', 0, '2026-01-29 11:52:09', 1),
(67, 36, '/drivers/36/cover_photos/cover_1769347802_5.png', 4, '2026-01-25 13:30:02', 1),
(66, 36, '/drivers/36/cover_photos/cover_1769347169_4.jpg', 3, '2026-01-25 13:19:29', 1),
(65, 36, '/drivers/36/cover_photos/cover_1769347169_3.png', 2, '2026-01-25 13:19:29', 1),
(63, 36, '/drivers/36/cover_photos/cover_1769347081_1.png', 0, '2026-01-25 13:18:01', 1),
(64, 36, '/drivers/36/cover_photos/cover_1769347169_2.png', 1, '2026-01-25 13:19:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `created_trips`
--

CREATE TABLE `created_trips` (
  `tripId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `tripTitle` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `status` enum('pending','scheduled','completed','ongoing') DEFAULT 'pending',
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp(),
  `numberOfPeople` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `created_trips`
--

INSERT INTO `created_trips` (`tripId`, `userId`, `tripTitle`, `description`, `startDate`, `endDate`, `status`, `createdAt`, `updatedAt`, `numberOfPeople`) VALUES
(2, 28, 'lalinda ravishan', 'sccsc', '2025-11-25', '2025-11-26', 'completed', '2025-11-23 14:49:59', '2025-11-25 19:48:56', 1),
(5, 6, 'cdscds', 'sdcdsc', '2025-11-26', '2025-11-28', 'completed', '2025-11-24 17:26:59', '2025-11-24 17:26:59', 1),
(8, 7, 'xz xz ', 'zczczc', '2025-11-26', '2025-11-28', 'completed', '2025-11-25 03:42:36', '2025-11-25 03:42:36', 1),
(9, 9, 'test1', 'cddc', '2025-11-27', '2025-11-29', 'completed', '2025-11-25 04:56:13', '2025-11-25 04:56:13', 1),
(10, 18, 'test2', 'jjakdka', '2025-11-26', '2026-11-28', 'ongoing', '2025-11-25 05:24:29', '2025-11-25 05:24:29', 1),
(18, 10, 'yahangala', 'This is a trip to yahangala', '2026-01-27', '2027-11-30', 'ongoing', '2025-11-25 19:38:34', '2025-11-25 19:38:34', 1),
(20, 10, 'lalindas trip', 'mage trip eka', '2025-12-23', '2026-12-26', 'ongoing', '2025-12-02 05:05:19', '2025-12-02 05:05:19', 1),
(21, 28, 'mage trip eka', 'dacdsc', '2026-12-30', '2027-01-08', 'pending', '2025-12-06 15:54:03', '2025-12-23 19:00:41', 1),
(23, 28, 'hambanthota', 'this is my annual trip to hambanthota', '2027-03-11', '2027-03-12', 'pending', '2026-03-09 19:08:54', '2026-02-10 02:40:56', 2),
(24, 28, 'batch trip', 'this is the annual batch trip of ucsc 21 batch', '2027-01-14', '2027-01-28', 'pending', '2026-01-12 10:54:53', '2026-01-12 10:55:11', 1),
(25, 36, 'trip to nuwara eliya', 'cdcsdc', '2027-01-20', '2027-01-28', 'pending', '2026-01-17 04:41:59', '2026-01-17 04:41:59', 1),
(29, 28, 'trip ucsc', 'cdknskcns', '2027-02-10', '2027-02-12', 'pending', '2026-02-09 07:15:55', '2026-02-10 02:34:30', 2);

-- --------------------------------------------------------

--
-- Table structure for table `driver_unavailable_dates`
--

CREATE TABLE `driver_unavailable_dates` (
  `id` int(11) NOT NULL,
  `driverId` int(11) NOT NULL,
  `unavailableDate` date NOT NULL,
  `reason` enum('personal','booked') DEFAULT NULL,
  `personalReason` text DEFAULT NULL,
  `tripId` int(11) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_unavailable_dates`
--

INSERT INTO `driver_unavailable_dates` (`id`, `driverId`, `unavailableDate`, `reason`, `personalReason`, `tripId`, `createdAt`, `updatedAt`) VALUES
(9, 36, '2026-02-16', 'personal', 'dccdc', NULL, '2026-02-08 16:15:58', '2026-02-08 16:15:58'),
(10, 36, '2026-02-24', 'personal', 'bknknkj', NULL, '2026-02-08 16:35:18', '2026-02-08 16:35:18'),
(11, 36, '2026-02-11', 'personal', 'nknknk', NULL, '2026-02-08 16:39:22', '2026-02-08 16:39:22');

-- --------------------------------------------------------

--
-- Stand-in structure for view `driver_vehicles_view`
-- (See below for the actual view)
--
CREATE TABLE `driver_vehicles_view` (
`driverId` int(11)
,`accountType` enum('driver','guide','tourist','admin','site_moderator','business_manager')
,`driverName` varchar(255)
,`driverLanguage` varchar(50)
,`driverDob` date
,`driverGender` varchar(20)
,`driverPhone` varchar(25)
,`driverSecondaryPhone` varchar(25)
,`driverAddress` text
,`driverEmail` varchar(255)
,`driverProfilePhoto` varchar(255)
,`driverVerified` int(11)
,`driverData` longtext
,`driverLastLogin` timestamp
,`driverCreatedAt` timestamp
,`driverUpdatedAt` timestamp
,`vehicleId` int(11)
,`make` varchar(50)
,`model` varchar(50)
,`year` int(11)
,`color` varchar(30)
,`licensePlate` varchar(20)
,`seatingCapacity` int(11)
,`childSeats` int(11)
,`fuelEfficiency` decimal(5,2)
,`vehicleDescription` text
,`frontViewPhoto` varchar(255)
,`backViewPhoto` varchar(255)
,`sideViewPhoto` varchar(255)
,`interiorPhoto1` varchar(255)
,`interiorPhoto2` varchar(255)
,`interiorPhoto3` varchar(255)
,`vehicleStatus` tinyint(1)
,`vehicleAvailability` tinyint(1)
,`vehicleApproved` tinyint(1)
,`vehicleCreatedAt` timestamp
,`vehicleUpdatedAt` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 4.0,
  `total_reviews` int(11) DEFAULT 0,
  `price_per_day` decimal(8,2) DEFAULT 50.00,
  `image_url` varchar(500) DEFAULT NULL,
  `is_licensed` tinyint(1) DEFAULT 1,
  `is_trending` tinyint(1) DEFAULT 0,
  `is_tourist_guide` tinyint(1) DEFAULT 0,
  `badge_type` enum('top-rated','most-booked','none') DEFAULT 'none',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `email`, `phone`, `description`, `specialization`, `rating`, `total_reviews`, `price_per_day`, `image_url`, `is_licensed`, `is_trending`, `is_tourist_guide`, `badge_type`, `created_at`, `updated_at`) VALUES
(1, 'Saman Perera', 'saman@guides.lk', '+94771234567', 'Expert cultural tour guide with 15 years experience. Specialized in ancient temples and historical sites across Sri Lanka.', 'Cultural Tours', 4.8, 156, 75.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 1, 1, 'top-rated', '2025-10-17 10:50:58', '2025-10-17 10:50:58'),
(2, 'Ruwan Silva', 'ruwan@guides.lk', '+94772345678', 'Wildlife safari expert and nature enthusiast. Perfect guide for national parks and wildlife photography tours.', 'Wildlife Safari', 4.9, 203, 85.00, '/test/public/components/driver/images/driver-jane-smith-123.png', 1, 1, 1, 'most-booked', '2025-10-17 10:50:58', '2025-10-17 10:50:58'),
(3, 'Nimal Fernando', 'nimal@guides.lk', '+94773456789', 'Hill country specialist with deep knowledge of tea plantations, scenic routes, and mountain adventures.', 'Hill Country', 4.7, 89, 70.00, '/test/public/components/driver/images/driver-john-doe-112.png', 1, 0, 1, 'none', '2025-10-17 10:50:58', '2025-10-17 10:50:58'),
(4, 'Chaminda Rathnayake', 'chaminda@guides.lk', '+94774567890', 'Coastal tour expert specializing in southern beaches, fishing villages, and marine life experiences.', 'Beach Tours', 4.6, 112, 65.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 0, 1, 'none', '2025-10-17 10:51:15', '2025-10-17 10:51:15'),
(5, 'Kasun Mendis', 'kasun@guides.lk', '+94775678901', 'All-island tour guide with expertise in comprehensive Sri Lankan cultural and natural heritage tours.', 'Cultural Tours', 4.8, 176, 80.00, '/test/public/components/driver/images/driver-jane-smith-123.png', 1, 1, 1, 'top-rated', '2025-10-17 10:51:15', '2025-10-17 10:51:15'),
(6, 'Lakshan Perera', 'lakshan@guides.lk', '+94776789012', 'Adventure sports specialist and mountain guide. Expert in hiking, rock climbing, and extreme sports tours.', 'Adventure Sports', 4.7, 134, 90.00, '/test/public/components/driver/images/driver-john-doe-112.png', 1, 0, 1, 'none', '2025-10-17 10:51:15', '2025-10-17 10:51:15'),
(7, 'Tharanga Wickramasinghe', 'tharanga@guides.lk', '+94777890123', 'Licensed historical tour guide specializing in ancient sites and archaeological wonders.', 'Historical Sites', 4.9, 124, 75.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 0, 0, 'none', '2025-10-17 10:51:36', '2025-10-23 03:23:27'),
(8, 'Kumari Jayawardene', 'kumari@guides.lk', '+94778901234', 'Expert cultural guide with deep knowledge of Sri Lankan heritage and traditions.', 'Cultural Tours', 4.9, 98, 70.00, '/test/public/components/driver/images/driver-jane-smith-123.png', 1, 0, 0, 'none', '2025-10-17 10:51:36', '2025-10-23 03:23:00'),
(9, 'Ravi Gunasekara', 'ravi@guides.lk', '+94779012345', 'Wildlife expert and nature guide with extensive knowledge of Sri Lankan fauna and flora.', 'Wildlife Safari', 5.0, 210, 95.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 1, 0, 'top-rated', '2025-10-17 10:51:36', '2025-10-23 03:22:22'),
(10, 'vihanga', 'tec5aa2donsolutions@gmail.com', '+94777512854', 'Good driver with 2 years of experience', 'Ancient Runes', 4.0, 0, 50.00, 'img/signup/profile_68f7bc0507b26.png', 1, 0, 0, 'none', '2025-10-21 16:59:49', '2025-10-21 16:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `guide_locations`
--

CREATE TABLE `guide_locations` (
  `id` int(11) NOT NULL,
  `guideId` int(11) NOT NULL,
  `spotId` int(11) NOT NULL,
  `baseCharge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `chargeType` enum('per_person','whole_trip') NOT NULL DEFAULT 'per_person',
  `minGroupSize` int(11) NOT NULL DEFAULT 1,
  `maxGroupSize` int(11) NOT NULL DEFAULT 20,
  `description` text DEFAULT NULL,
  `photoPath` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_locations`
--

INSERT INTO `guide_locations` (`id`, `guideId`, `spotId`, `baseCharge`, `chargeType`, `minGroupSize`, `maxGroupSize`, `description`, `photoPath`, `isActive`, `created_at`, `updated_at`) VALUES
(5, 17, 22, 2000.00, 'per_person', 5, 20, 'mfdmclfd,vl', '/travelSpots/21/photo1_spot_697ed3e9cca406.61633021.jpg', 1, '2026-02-01 11:30:35', '2026-02-21 03:12:40'),
(6, 3, 1, 3000.00, 'per_person', 1, 20, NULL, NULL, 0, '2026-02-21 03:09:58', '2026-02-21 03:09:58'),
(7, 14, 2, 2500.00, 'per_person', 1, 20, NULL, NULL, 0, '2026-02-21 03:10:54', '2026-02-21 03:10:54'),
(8, 16, 21, 3500.00, 'per_person', 1, 20, NULL, NULL, 0, '2026-02-21 03:11:25', '2026-02-21 03:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `payoutID` int(11) NOT NULL,
  `TransactionID` text NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `tripID` int(11) DEFAULT NULL,
  `service_type` text DEFAULT NULL,
  `earnings` float DEFAULT NULL,
  `commission` float DEFAULT NULL,
  `net_payout` float DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `actions` enum('Process Payout','View Detail','','') DEFAULT NULL,
  `payout_date` date DEFAULT curdate(),
  `payout_time` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payouts`
--

INSERT INTO `payouts` (`payoutID`, `TransactionID`, `userID`, `tripID`, `service_type`, `earnings`, `commission`, `net_payout`, `status`, `actions`, `payout_date`, `payout_time`) VALUES
(0, '', 2, 1, 'driver', 8000, 1200, 6800, 'Completed', '', '2026-02-04', '08:16:52'),
(0, '', 3, 1, 'guide', 3000, 450, 2550, 'Completed', '', '2026-01-04', '08:16:52'),
(0, '', 16, 2, 'guide', 4000, 600, 3400, 'Completed', '', '2026-01-04', '08:16:52'),
(0, '', 14, 2, 'guide', 5000, 750, 4250, 'Completed', '', '2026-01-04', '08:16:52'),
(0, '', 17, 2, 'guide', 1000, 150, 850, 'Completed', '', '2026-01-04', '08:16:52'),
(0, '', 12, 2, 'driver', 4000, 600, 3400, 'Completed', '', '2026-01-04', '08:16:52'),
(0, '', 36, 2, 'driver', 1000, 150, 850, 'Completed', '', '2026-01-04', '08:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `profile_details`
--

CREATE TABLE `profile_details` (
  `profileId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `languages` varchar(500) DEFAULT NULL,
  `instaAccount` varchar(255) DEFAULT NULL,
  `facebookAccount` varchar(255) DEFAULT NULL,
  `dlVerified` tinyint(1) DEFAULT 0,
  `tlSubmitted` tinyint(1) DEFAULT 0,
  `tlVerified` tinyint(1) DEFAULT 0,
  `tLicenseNumber` varchar(100) DEFAULT NULL,
  `tLicenseExpiryDate` date DEFAULT NULL,
  `tLicensePhotoFront` varchar(255) DEFAULT NULL,
  `tLicensePhotoBack` varchar(255) DEFAULT NULL,
  `averageRating` decimal(3,2) DEFAULT 0.00,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profile_details`
--

INSERT INTO `profile_details` (`profileId`, `userId`, `bio`, `languages`, `instaAccount`, `facebookAccount`, `dlVerified`, `tlSubmitted`, `tlVerified`, `tLicenseNumber`, `tLicenseExpiryDate`, `tLicensePhotoFront`, `tLicensePhotoBack`, `averageRating`, `createdAt`, `updatedAt`) VALUES
(1, 36, 'this is is lee  hhfthfhgkgddsfsfs', 'polish,jio,koloa,moka,bijja,joka,mokoa', '@lalaa_a', 'lalaravidvdcd', 1, 1, 1, '56562648', '2026-01-08', '/drivers/36/licenses/tourist_license_tLicensePhotoFront_1769348057.png', '/drivers/36/licenses/tourist_license_tLicensePhotoBack_1769348057.png', 0.00, '2026-01-20 17:04:40', '2026-02-09 08:14:12'),
(2, 12, 'hnn,nnkjnkjbhkb', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 0.00, '2026-01-28 03:48:59', '2026-01-28 03:48:59'),
(3, 17, 'cdsmldsmv', 'pakaya,english', '', '', 0, 1, 1, '56562645', '2026-02-28', '/drivers/17/licenses/tourist_license_tLicensePhotoFront_1769691433.png', '/drivers/17/licenses/tourist_license_tLicensePhotoBack_1769691433.png', 4.00, '2026-01-29 12:16:16', '2026-02-02 10:04:16'),
(4, 37, 'Welcome to my profile in tripingoo!', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 0.00, '2026-02-03 17:53:33', '2026-02-03 17:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `refund_requests`
--

CREATE TABLE `refund_requests` (
  `id` int(11) NOT NULL,
  `traveller_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `booking_amount` decimal(10,2) NOT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `requested_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending',
  `reviewed_by` varchar(100) DEFAULT NULL,
  `reviewed_date` datetime DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refund_requests`
--

INSERT INTO `refund_requests` (`id`, `traveller_id`, `trip_id`, `booking_amount`, `refund_amount`, `requested_date`, `status`, `reviewed_by`, `reviewed_date`, `reason`, `created_at`, `updated_at`) VALUES
(1, 28, 29, 5000.00, NULL, '2026-02-21 19:40:52', 'rejected', '26', '2026-02-21 20:21:13', 'private reason', '2026-02-21 19:40:52', '2026-02-21 20:21:13'),
(2, 28, 24, 8000.00, NULL, '2026-02-21 19:42:01', 'approved', '26', '2026-02-21 20:18:45', 'gffghh', '2026-02-21 19:42:01', '2026-02-21 20:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `tlicense_verifications`
--

CREATE TABLE `tlicense_verifications` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewedBy` int(11) DEFAULT NULL,
  `reviewedAt` timestamp NULL DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `rejectionReason` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tlicense_verifications`
--

INSERT INTO `tlicense_verifications` (`id`, `userId`, `status`, `reviewedBy`, `reviewedAt`, `expiryDate`, `rejectionReason`, `createdAt`, `updatedAt`) VALUES
(1, 36, 'approved', 25, '2026-02-09 08:14:12', '2026-01-08', NULL, '2026-01-25 15:53:36', '2026-02-09 08:14:12'),
(3, 17, 'approved', 25, '2026-01-29 13:23:44', '2026-02-28', NULL, '2026-01-29 12:57:13', '2026-01-29 13:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionID` int(11) NOT NULL,
  `tripID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `type` text DEFAULT NULL,
  `transactionDate` date DEFAULT curdate(),
  `transactionTime` time DEFAULT curtime(),
  `transaction_status` enum('Pending','Completed','Cancellation Request') DEFAULT NULL,
  `actions` enum('View Details','Process','Process Refund') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionID`, `tripID`, `userID`, `amount`, `type`, `transactionDate`, `transactionTime`, `transaction_status`, `actions`) VALUES
(75, 1, 2, 8000, 'Driver Payment', '2026-02-04', '08:16:52', 'Completed', 'Process'),
(76, 1, 3, 24000, 'Guide Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(77, 2, 12, 12000, 'Driver Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(78, 2, 16, 4000, 'Guide Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(75, 1, 2, 8000, 'Driver Payment', '2026-02-04', '08:16:52', 'Completed', 'Process'),
(76, 1, 3, 24000, 'Guide Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(77, 2, 12, 12000, 'Driver Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(78, 2, 16, 4000, 'Guide Payment', '2026-02-04', '08:16:52', 'Pending', 'Process'),
(1, 1, 3, 3000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(2, 1, 3, 3000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(3, 1, 3, 3000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(4, 1, 3, 3000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(5, 2, 16, 4000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(6, 2, 16, 4000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(7, 2, 16, 4000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(8, 2, 14, 5000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(9, 2, 14, 5000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(10, 2, 17, 1000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(11, 2, 17, 1000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(12, 2, 17, 1000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(13, 2, 17, 1000, 'Guide Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(14, 1, 2, 3000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(15, 1, 2, 3000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(16, 1, 2, 3000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(17, 1, 2, 3000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(18, 2, 12, 4000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(19, 2, 12, 4000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(20, 2, 12, 4000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(21, 2, 12, 5000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(22, 2, 12, 5000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(23, 2, 12, 1000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(24, 2, 36, 1000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(25, 2, 36, 1000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process'),
(26, 2, 36, 1000, 'Driver Payment', '2026-01-04', '08:16:52', 'Completed', 'Process');

-- --------------------------------------------------------

--
-- Table structure for table `traveller_side_d_requests`
--

CREATE TABLE `traveller_side_d_requests` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT 'Tourist who created the request',
  `tripId` int(11) NOT NULL COMMENT 'Reference to the trip',
  `eventId` int(11) NOT NULL COMMENT 'Reference to the trip event',
  `travelSpotId` int(11) NOT NULL COMMENT 'Reference to travel spot',
  `driverId` int(11) DEFAULT NULL COMMENT 'Selected driver (null if not selected)',
  `status` enum('notSelected','pending','requested','accepted','rejected','cancelled','completed') DEFAULT 'notSelected',
  `driverFullName` varchar(255) DEFAULT NULL,
  `driverProfilePhoto` varchar(255) DEFAULT NULL,
  `driverAverageRating` decimal(3,2) DEFAULT NULL,
  `driverBio` text DEFAULT NULL,
  `vehicleType` varchar(100) DEFAULT NULL,
  `vehicleNumber` varchar(100) DEFAULT NULL,
  `totalCharge` decimal(10,2) DEFAULT NULL COMMENT 'Total charge for the driver service',
  `requestedAt` timestamp NULL DEFAULT NULL COMMENT 'When request sent to driver',
  `respondedAt` timestamp NULL DEFAULT NULL COMMENT 'When driver responded',
  `acceptedAt` timestamp NULL DEFAULT NULL COMMENT 'When driver accepted',
  `completedAt` timestamp NULL DEFAULT NULL COMMENT 'When driver service completed',
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `traveller_side_d_requests`
--

INSERT INTO `traveller_side_d_requests` (`id`, `userId`, `tripId`, `eventId`, `travelSpotId`, `driverId`, `status`, `driverFullName`, `driverProfilePhoto`, `driverAverageRating`, `driverBio`, `vehicleType`, `vehicleNumber`, `totalCharge`, `requestedAt`, `respondedAt`, `acceptedAt`, `completedAt`, `createdAt`, `updatedAt`) VALUES
(0, 28, 2, 95, 1, 2, 'completed', NULL, NULL, NULL, NULL, NULL, NULL, 3000.00, NULL, NULL, NULL, NULL, '2026-02-21 16:03:36', '2026-02-21 16:10:48'),
(0, 6, 5, 96, 1, 12, 'completed', NULL, NULL, NULL, NULL, NULL, NULL, 4500.00, NULL, NULL, NULL, NULL, '2026-02-21 16:03:36', '2026-02-21 16:10:48'),
(0, 7, 8, 97, 1, 36, 'completed', NULL, NULL, NULL, NULL, NULL, NULL, 7000.00, NULL, NULL, NULL, NULL, '2026-02-21 16:03:36', '2026-02-21 16:10:48');

-- --------------------------------------------------------

--
-- Table structure for table `traveller_side_g_requests`
--

CREATE TABLE `traveller_side_g_requests` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL COMMENT 'Tourist who created the request',
  `tripId` int(11) NOT NULL COMMENT 'Reference to the trip',
  `eventId` int(11) NOT NULL COMMENT 'Reference to the trip event',
  `travelSpotId` int(11) NOT NULL COMMENT 'Reference to travel spot',
  `guideId` int(11) DEFAULT NULL COMMENT 'Selected guide (null if not selected)',
  `status` enum('notSelected','pending','requested','accepted','rejected','cancelled','completed') DEFAULT 'notSelected',
  `guideFullName` varchar(255) DEFAULT NULL,
  `guideProfilePhoto` varchar(255) DEFAULT NULL,
  `guideAverageRating` decimal(3,2) DEFAULT NULL,
  `guideBio` text DEFAULT NULL,
  `totalCharge` decimal(10,2) DEFAULT NULL COMMENT 'Total charge for the guide service',
  `requestedAt` timestamp NULL DEFAULT NULL COMMENT 'When request sent to guide',
  `respondedAt` timestamp NULL DEFAULT NULL COMMENT 'When guide responded',
  `acceptedAt` timestamp NULL DEFAULT NULL COMMENT 'When guide accepted',
  `completedAt` timestamp NULL DEFAULT NULL COMMENT 'When guide service completed',
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `traveller_side_g_requests`
--

INSERT INTO `traveller_side_g_requests` (`id`, `userId`, `tripId`, `eventId`, `travelSpotId`, `guideId`, `status`, `guideFullName`, `guideProfilePhoto`, `guideAverageRating`, `guideBio`, `totalCharge`, `requestedAt`, `respondedAt`, `acceptedAt`, `completedAt`, `createdAt`, `updatedAt`) VALUES
(26, 28, 23, 76, 21, 17, 'pending', 'vihanga', 'http://localhost/test/public/uploads/signup/profile/profile_697b49dc27b7a.jpg', 4.00, '\n                                    mfdmclfd,vl                                ', 66.00, NULL, NULL, NULL, NULL, '2026-02-04 20:22:18', '2026-02-05 05:24:25'),
(30, 28, 23, 81, 21, 17, 'pending', 'vihanga', 'http://localhost/test/public/uploads/signup/profile/profile_697b49dc27b7a.jpg', 4.00, '\n                                    mfdmclfd,vl                                ', 66.00, NULL, NULL, NULL, NULL, '2026-02-05 05:23:46', '2026-02-05 05:23:46'),
(35, 28, 23, 91, 21, 17, 'pending', 'vihanga', 'http://localhost/test/public/uploads/signup/profile/profile_697b49dc27b7a.jpg', 4.00, '\n                                    mfdmclfd,vl                                ', 2000.00, NULL, NULL, NULL, NULL, '2026-02-08 20:57:20', '2026-02-10 02:42:33'),
(37, 28, 29, 94, 21, 17, 'pending', 'vihanga', 'http://localhost/test/public/uploads/signup/profile/profile_697b49dc27b7a.jpg', 4.00, '\n                                    mfdmclfd,vl                                ', 2000.00, NULL, NULL, NULL, NULL, '2026-02-10 02:32:55', '2026-02-10 02:34:03'),
(43, 28, 2, 95, 1, NULL, 'completed', NULL, NULL, NULL, NULL, 3000.00, NULL, NULL, NULL, NULL, '2026-02-21 15:58:39', '2026-02-21 16:13:09'),
(44, 6, 5, 96, 1, NULL, 'completed', NULL, NULL, NULL, NULL, 2500.00, NULL, NULL, NULL, NULL, '2026-02-21 15:59:06', '2026-02-21 16:13:09'),
(45, 7, 8, 97, 1, NULL, 'completed', NULL, NULL, NULL, NULL, 4000.00, NULL, NULL, NULL, NULL, '2026-02-21 15:59:06', '2026-02-21 16:13:09');

-- --------------------------------------------------------

--
-- Table structure for table `travelspots_mainfilters`
--

CREATE TABLE `travelspots_mainfilters` (
  `mainFilterId` int(11) NOT NULL,
  `mainFilterName` varchar(255) NOT NULL,
  `moderatorId` int(11) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travelspots_mainfilters`
--

INSERT INTO `travelspots_mainfilters` (`mainFilterId`, `mainFilterName`, `moderatorId`, `createdAt`, `updatedAt`) VALUES
(3, 'adventure3', 28, '2025-12-11 13:53:48', '2025-12-11 13:58:07'),
(4, 'culture', 28, '2025-12-11 13:54:11', '2025-12-11 13:54:11'),
(5, 'outdoor', 28, '2025-12-11 13:56:02', '2025-12-11 13:56:02'),
(6, 'ancient runes', 28, '2025-12-21 14:14:22', '2025-12-21 14:14:22'),
(7, 'treking', 28, '2025-12-26 16:31:26', '2025-12-26 16:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `travelspots_subfilters`
--

CREATE TABLE `travelspots_subfilters` (
  `subFilterId` int(11) NOT NULL,
  `subFilterName` varchar(255) NOT NULL,
  `mainFilterId` int(11) NOT NULL,
  `moderatorId` int(11) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travelspots_subfilters`
--

INSERT INTO `travelspots_subfilters` (`subFilterId`, `subFilterName`, `mainFilterId`, `moderatorId`, `createdAt`, `updatedAt`) VALUES
(4, 'test12', 3, 28, '2025-12-11 13:53:56', '2025-12-11 13:58:01'),
(5, 'test2', 4, 28, '2025-12-11 13:54:18', '2025-12-11 13:54:18'),
(6, 'test4', 5, 28, '2025-12-11 13:56:08', '2025-12-11 13:56:08'),
(7, 'artifacts', 6, 28, '2025-12-21 14:29:37', '2025-12-21 14:29:37'),
(8, 'parana badu', 6, 25, '2026-02-01 04:17:12', '2026-02-01 04:17:12');

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots`
--

CREATE TABLE `travel_spots` (
  `spotId` int(11) NOT NULL,
  `spotName` varchar(200) NOT NULL,
  `overview` text DEFAULT NULL,
  `province` enum('Western','Central','Southern','Northern','Eastern','North Western','North Central','Uva','Sabaragamuwa') DEFAULT NULL,
  `district` enum('Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya','Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar','Mullaitivu','Vavuniya','Ampara','Batticaloa','Trincomalee','Kurunegala','Puttalam','Anuradhapura','Polonnaruwa','Badulla','Monaragala','Ratnapura','Kegalle') DEFAULT NULL,
  `bestTimeFrom` enum('January','February','March','April','May','June','July','August','September','October','November','December') DEFAULT NULL,
  `bestTimeTo` enum('January','February','March','April','May','June','July','August','September','October','November','December') DEFAULT NULL,
  `visitingDurationMin` int(11) DEFAULT NULL,
  `visitingDurationMax` int(11) DEFAULT NULL,
  `ticketPriceLocal` decimal(10,2) DEFAULT NULL,
  `ticketPriceForeigner` decimal(10,2) DEFAULT NULL,
  `openingHours` text DEFAULT NULL,
  `ticketDetails` text DEFAULT NULL,
  `parkingDetails` text DEFAULT NULL,
  `accessibility` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `travelerTips` text DEFAULT NULL,
  `averageRating` decimal(3,2) DEFAULT 0.00,
  `totalReviews` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots`
--

INSERT INTO `travel_spots` (`spotId`, `spotName`, `overview`, `province`, `district`, `bestTimeFrom`, `bestTimeTo`, `visitingDurationMin`, `visitingDurationMax`, `ticketPriceLocal`, `ticketPriceForeigner`, `openingHours`, `ticketDetails`, `parkingDetails`, `accessibility`, `facilities`, `travelerTips`, `averageRating`, `totalReviews`) VALUES
(1, 'Azure Peaks Sanctuary', 'A high-altitude nature reserve famous for migratory birds.', '', '', 'October', 'March', 120, 240, 5.00, 25.00, '06:00 - 18:00', 'Kids under 5 free', 'On-site gravel lot', 'Limited accessibility', 'Toilets, Cafe', 'Bring a jacket', 4.80, 1240),
(2, 'Old Port Citadel', 'A 17th-century coastal fortress and museum.', '', '', 'December', 'May', 60, 120, 2.50, 15.00, '09:00 - 17:00', 'Includes museum entry', 'Street parking', 'Fully accessible', 'Restrooms, Gift Shop', 'Wear walking shoes', 4.50, 890),
(21, 'bambarakanda falls dh', 'this is the highest waterfall in srilanka', 'Central', 'Jaffna', 'February', 'April', NULL, 2, 10.00, 10.00, 'anytime ', 'ld;c,dls;c', 'cnsdnc sc ', 'jsdcmlsdcn', 'csndkcnkjsnckj', 'cjnsdkjcnjksdc', 0.00, 0),
(22, 'wangedigala', 'this is a hicking place', 'Southern', 'Gampaha', 'February', 'May', NULL, 10, 10.00, 10.00, 'cjscldscm', 'csdjclmlksdcm', 'csdlclsmclmsdlc', 'csdjcksdckjdsnjc', 'cskdjnkcnsdjlcn ', 'csdclsmclcosdjcpojsc', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots_contributions`
--

CREATE TABLE `travel_spots_contributions` (
  `contributionId` int(11) NOT NULL,
  `spotId` int(11) DEFAULT NULL,
  `moderatorId` int(11) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots_contributions`
--

INSERT INTO `travel_spots_contributions` (`contributionId`, `spotId`, `moderatorId`, `createdAt`, `updatedAt`) VALUES
(11, 21, 25, '2026-01-27 10:34:56', '2026-01-27 10:34:56'),
(12, 22, 25, '2026-02-04 12:19:11', '2026-02-04 12:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots_itinerary`
--

CREATE TABLE `travel_spots_itinerary` (
  `pointId` int(11) NOT NULL,
  `spotId` int(11) DEFAULT NULL,
  `pointName` varchar(200) DEFAULT NULL,
  `pointOrder` int(11) DEFAULT 0,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots_itinerary`
--

INSERT INTO `travel_spots_itinerary` (`pointId`, `spotId`, `pointName`, `pointOrder`, `latitude`, `longitude`, `createdAt`, `updatedAt`) VALUES
(36, 21, 'Yakkalamulla', 0, 6.10642640, 80.34831450, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(37, 22, 'Wangedigala Camp Site', 0, 6.76209900, 80.83130100, '2026-02-04 12:19:11', '2026-02-04 12:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots_nearbyspots`
--

CREATE TABLE `travel_spots_nearbyspots` (
  `id` int(11) NOT NULL,
  `sourceSpotId` int(11) DEFAULT NULL,
  `nearbySpotId` int(11) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots_nearbyspots`
--

INSERT INTO `travel_spots_nearbyspots` (`id`, `sourceSpotId`, `nearbySpotId`, `createdAt`, `updatedAt`) VALUES
(34, 21, 2, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(35, 22, 1, '2026-02-04 12:19:11', '2026-02-04 12:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots_photos`
--

CREATE TABLE `travel_spots_photos` (
  `photoId` int(11) NOT NULL,
  `spotId` int(11) DEFAULT NULL,
  `photoPath` varchar(500) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots_photos`
--

INSERT INTO `travel_spots_photos` (`photoId`, `spotId`, `photoPath`, `createdAt`, `updatedAt`) VALUES
(98, 21, '/travelSpots/21/photo1_spot_697ed3e9cca406.61633021.jpg', '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(99, 21, '/travelSpots/21/photo2_spot_697ed3e9cd2c20.00707071.jpg', '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(100, 21, '/travelSpots/21/photo3_spot_697ed3e9cda6b1.15029115.jpg', '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(101, 21, '/travelSpots/21/photo4_spot_697ed3e9cef802.78470364.png', '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(102, 22, '/travelSpots/22/photo1_spot_6983393f814953.87045149.jpg', '2026-02-04 12:19:11', '2026-02-04 12:19:11'),
(103, 22, '/travelSpots/22/photo2_spot_6983393f8696a5.77015885.jpg', '2026-02-04 12:19:11', '2026-02-04 12:19:11'),
(104, 22, '/travelSpots/22/photo3_spot_6983393f87c520.51097645.png', '2026-02-04 12:19:11', '2026-02-04 12:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `travel_spots_subfilters`
--

CREATE TABLE `travel_spots_subfilters` (
  `id` int(11) NOT NULL,
  `spotId` int(11) DEFAULT NULL,
  `subFilterId` int(11) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_spots_subfilters`
--

INSERT INTO `travel_spots_subfilters` (`id`, `spotId`, `subFilterId`, `createdAt`, `updatedAt`) VALUES
(46, 21, 4, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(47, 21, 5, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(48, 21, 7, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(49, 21, 8, '2026-02-01 04:17:45', '2026-02-01 04:17:45'),
(50, 22, 8, '2026-02-04 12:19:11', '2026-02-04 12:19:11');

-- --------------------------------------------------------

--
-- Stand-in structure for view `travel_spot_card_data`
-- (See below for the actual view)
--
CREATE TABLE `travel_spot_card_data` (
`mainFilterId` int(11)
,`mainFilterName` varchar(255)
,`subFilterId` int(11)
,`subFilterName` varchar(255)
,`spotId` int(11)
,`spotName` varchar(200)
,`overview` text
,`averageRating` decimal(3,2)
,`totalReviews` int(11)
,`district` enum('Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya','Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar','Mullaitivu','Vavuniya','Ampara','Batticaloa','Trincomalee','Kurunegala','Puttalam','Anuradhapura','Polonnaruwa','Badulla','Monaragala','Ratnapura','Kegalle')
,`province` enum('Western','Central','Southern','Northern','Eastern','North Western','North Central','Uva','Sabaragamuwa')
,`photoPath` varchar(500)
,`status` varchar(30)
);

-- --------------------------------------------------------

--
-- Table structure for table `trip_events`
--

CREATE TABLE `trip_events` (
  `eventId` int(11) NOT NULL,
  `tripId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `eventDate` date NOT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `eventType` enum('travelSpot','location') NOT NULL,
  `eventStatus` enum('start','intermediate','end') NOT NULL,
  `travelSpotId` int(11) DEFAULT NULL,
  `locationName` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_events`
--

INSERT INTO `trip_events` (`eventId`, `tripId`, `userId`, `eventDate`, `startTime`, `endTime`, `eventType`, `eventStatus`, `travelSpotId`, `locationName`, `latitude`, `longitude`, `description`, `created_at`, `updated_at`) VALUES
(60, 23, 28, '2026-03-11', '09:00:00', '09:15:00', 'location', 'start', NULL, 'Colombo Bandaranaike International Airport', 7.18015430, 79.88424950, 'Airport arrival', '2026-02-04 18:03:12', '2026-02-08 17:00:10'),
(61, 23, 28, '2026-03-11', '09:15:00', '10:00:00', 'location', 'intermediate', NULL, 'Shangri-La Colombo', 6.92853540, 79.84443620, 'Shangila hotel for take breakfast', '2026-02-04 18:04:40', '2026-02-08 17:00:22'),
(62, 23, 28, '2026-03-11', '10:00:00', '11:00:00', 'location', 'intermediate', NULL, 'Galle Face', 6.92857750, 79.84508000, 'Chilling in galle face beach', '2026-02-04 18:05:25', '2026-02-08 17:00:28'),
(67, 23, 28, '2026-03-12', '00:00:00', '13:00:00', 'location', 'intermediate', NULL, 'Pattipola', 6.85850220, 80.83085870, 'xalmclskdcmlk', '2026-02-04 19:38:18', '2026-02-08 17:01:13'),
(76, 23, 28, '2026-03-11', '11:00:00', '12:00:00', 'travelSpot', 'intermediate', 21, NULL, NULL, NULL, NULL, '2026-02-04 20:22:18', '2026-02-08 17:00:36'),
(79, 23, 28, '2026-03-12', '13:00:00', '14:00:00', 'location', 'end', NULL, 'Galle', 6.03289480, 80.21679120, 'jndcmsdlckm', '2026-02-05 05:10:04', '2026-02-08 21:04:13'),
(81, 23, 28, '2026-03-12', '14:00:00', '15:00:00', 'travelSpot', 'intermediate', 21, NULL, NULL, NULL, NULL, '2026-02-05 05:23:46', '2026-02-08 17:01:06'),
(88, 23, 28, '2026-03-12', '16:00:00', '17:00:00', 'location', 'start', NULL, 'Galle Dutch Fort', 6.03046440, 80.21502370, 'schksdjcldsdcd', '2026-02-05 16:49:46', '2026-02-08 17:01:02'),
(89, 23, 28, '2026-03-12', '17:00:00', '18:00:00', 'location', 'end', NULL, 'Halawatha', 7.88714980, 79.82126890, 'snkcnkjsanckjas', '2026-02-05 16:50:24', '2026-02-08 17:00:53'),
(91, 23, 28, '2026-03-11', '12:00:00', '13:00:00', 'travelSpot', 'intermediate', 21, NULL, NULL, NULL, NULL, '2026-02-08 20:57:20', '2026-02-08 20:57:20'),
(92, 29, 28, '2026-02-10', '09:00:00', '09:00:00', 'location', 'start', NULL, 'Colombo Bandaranaike International Airport', 7.18015430, 79.88424950, 'this is the start point', '2026-02-09 07:17:38', '2026-02-09 07:19:25'),
(94, 29, 28, '2026-02-10', '09:00:00', '11:00:00', 'travelSpot', 'end', 21, NULL, NULL, NULL, NULL, '2026-02-10 02:32:55', '2026-02-10 02:34:03'),
(95, 2, 28, '0000-00-00', NULL, NULL, 'travelSpot', '', 1, NULL, NULL, NULL, NULL, '2026-02-21 15:52:01', '2026-02-21 15:52:01'),
(96, 5, 6, '0000-00-00', NULL, NULL, 'travelSpot', '', 1, NULL, NULL, NULL, NULL, '2026-02-21 15:52:01', '2026-02-21 15:52:01'),
(97, 8, 7, '0000-00-00', NULL, NULL, 'travelSpot', '', 1, NULL, NULL, NULL, NULL, '2026-02-21 15:52:01', '2026-02-21 15:52:01');

--
-- Triggers `trip_events`
--
DELIMITER $$
CREATE TRIGGER `trg_trip_events_validate_before_insert` BEFORE INSERT ON `trip_events` FOR EACH ROW BEGIN
    IF NEW.eventType = 'travelSpot' THEN
        IF NEW.travelSpotId IS NULL OR NEW.locationName IS NOT NULL OR NEW.latitude IS NOT NULL OR NEW.longitude IS NOT NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=travelSpot, travelSpotId must be set and location fields must be NULL';
        END IF;
    ELSEIF NEW.eventType = 'location' THEN
        IF NEW.travelSpotId IS NOT NULL OR NEW.locationName IS NULL OR NEW.latitude IS NULL OR NEW.longitude IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=location, location fields must be set and travelSpotId must be NULL';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_trip_events_validate_before_update` BEFORE UPDATE ON `trip_events` FOR EACH ROW BEGIN
    IF NEW.eventType = 'travelSpot' THEN
        IF NEW.travelSpotId IS NULL OR NEW.locationName IS NOT NULL OR NEW.latitude IS NOT NULL OR NEW.longitude IS NOT NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=travelSpot, travelSpotId must be set and location fields must be NULL';
        END IF;
    ELSEIF NEW.eventType = 'location' THEN
        IF NEW.travelSpotId IS NOT NULL OR NEW.locationName IS NULL OR NEW.latitude IS NULL OR NEW.longitude IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=location, location fields must be set and travelSpotId must be NULL';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_type` enum('driver','guide','tourist','admin','site_moderator','business_manager') NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `language` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `secondary_phone` varchar(25) DEFAULT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `driver_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`driver_data`)),
  `guide_tourist_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`guide_tourist_data`)),
  `currency_code` varchar(10) NOT NULL DEFAULT 'USD',
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_type`, `fullname`, `language`, `dob`, `gender`, `phone`, `secondary_phone`, `address`, `email`, `password`, `profile_photo`, `driver_data`, `guide_tourist_data`, `currency_code`, `remember_token`, `last_login`, `created_at`, `updated_at`, `verified`) VALUES
(1, 'admin', 'chiran', 'sinhala', '2024-01-15', 'male', '+94777512854', NULL, '221b baker street.', 'lala@gmail.com', '$2y$10$edRYNY579Vu8rtfUhxsu9OblXQv9UrwYi1JxDq9w0RG672r.x.E66', NULL, NULL, NULL, 'USD', NULL, '2026-02-21 04:26:43', '2025-10-19 12:51:36', '2026-02-21 04:26:43', 0),
(2, 'driver', 'lalinda', 'Spanish', '2025-10-13', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lalinda@gmail.com', '$2y$10$oO2Voo8nRcLk0gD.9Jav8OAIrnWJ86V.lHRUIJ5fyD91PUFuxLyui', 'img/signup/sample4.png', '{\"id_back\": \"img/signup/id_back_68f4e2cca06b7.jpg\", \"id_front\": \"img/signup/id_front_68f4e2cc9fe0a.jpg\", \"insurance\": \"img/signup/insurance_68f4e2cc9f508.jpg\", \"vehicle_doc\": \"img/signup/vehicle_doc_68f4e2cc9eebc.png\", \"license_back\": \"img/signup/license_back_68f4e2cc9e910.png\", \"license_front\": \"img/signup/license_front_68f4e2cc9e204.jpg\", \"license_number\": \"64205423\", \"vehicle_number\": \"54234\", \"license_expire_date\": \"2025-10-01\"}', NULL, 'USD', NULL, '2025-10-24 06:06:40', '2025-10-19 13:08:28', '2026-02-03 07:45:41', 0),
(3, 'guide', 'abhijeeth', 'Sinhala', '2025-10-14', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'abba@gmail.com', '$2y$10$XT0XbLq.UmlxLiQL/4Fq/eifnVA6fw.BrPDdRry/d1uddFgd0RKvC', 'img/signup/sample4.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f545bd1e745.png\", \"nic_front\": \"img/signup/nic_front_68f545bd1e607.png\", \"nic_passport\": \"56415614654\"}', 'USD', NULL, '2025-10-24 06:09:07', '2025-10-19 20:10:37', '2026-02-03 07:45:41', 0),
(6, 'tourist', 'ransara', 'Sinhala', '2025-10-23', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'ransara1@gmail.com', '$2y$10$ZU5NaKSP9s6OlywLP9pRhu0Mmvsf6rK2t6D67dH/Tpo4sZW0gyQgC', 'img/signup/sample4.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f5d71f3da2c.png\", \"nic_front\": \"img/signup/nic_front_68f5d71f3d844.png\", \"nic_passport\": \"200216901549\"}', 'USD', NULL, '2026-02-21 04:28:19', '2025-10-20 06:30:55', '2026-02-21 04:28:19', 0),
(7, 'tourist', 'nsdnsjd', 'Sinhala', '2025-10-23', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'pehasn@gmail.com', '$2y$10$vwNHqQ16r.Wy9G.rT1j75.3bAv/VFjZ8e.tU6/yenUz0a.TRNSnf6', 'img/signup/profile_68f5d760c260e.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f5d760c2db3.png\", \"nic_front\": \"img/signup/nic_front_68f5d760c29eb.png\", \"nic_passport\": \"24001074\"}', 'USD', NULL, '2025-10-22 07:48:08', '2025-10-20 06:32:00', '2026-02-03 07:45:41', 0),
(8, 'tourist', 'ssvdsfbg', 'Sinhala', '2025-10-01', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'tecadonscolutions@gmail.com', '$2y$10$OpOH6q/sWfjghyrLoF0JweXPAMtMDHQHrhGLSlIDZv/JtQsV9aR8S', 'img/signup/profile_68f5d7d46f1a4.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f5d7d470147.png\", \"nic_front\": \"img/signup/nic_front_68f5d7d46f9b9.png\", \"nic_passport\": \"200216901548\"}', 'USD', NULL, NULL, '2025-10-20 06:33:56', '2026-02-03 07:45:41', 0),
(9, 'tourist', 'fmjfnjkdf', 'English', '2025-10-28', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'tecaddsonsolutions@gmail.com', '$2y$10$KYSx3pwM3joArsOIi5aEme/D4eWtYp07f8QwahVdx39sNSJ9ZQ7cW', 'img/signup/profile_68f5d81a12e31.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68f5d81a1341b.jpg\", \"nic_front\": \"img/signup/nic_front_68f5d81a131b5.png\", \"nic_passport\": \"206256146\"}', 'USD', NULL, NULL, '2025-10-20 06:35:06', '2026-02-03 07:45:41', 0),
(10, 'tourist', 'lalinda ravishan', 'Sinhala', '2025-10-29', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'koka@gmail.com', '$2y$10$HGgf9.CQYy8wd3W2xglq4.3G4qN.CXJATeKOIC3A5uPuVVyQXUw3.', 'img/signup/profile_68f5d8580b6d8.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f5d8580bd3e.png\", \"nic_front\": \"img/signup/nic_front_68f5d8580ba35.png\", \"nic_passport\": \"24001074\"}', 'USD', NULL, '2025-10-20 16:28:40', '2025-10-20 06:36:08', '2026-02-03 07:45:41', 0),
(11, 'business_manager', 'lalinda', 'english', '2025-10-01', 'male', '+94782498755', '', '221b baker street london', 'lalindabus@gamail.com', '$2y$10$KndQ./z/n200mNDR2ivRieKec.a6rkFHKWjjef7L3CtyVUKDYYSmK', 'img/signup/sample4.png', NULL, NULL, 'USD', NULL, '2025-10-23 14:36:41', '2025-10-20 09:02:34', '2026-02-03 07:45:41', 0),
(12, 'driver', 'lalinda', 'English', '2025-10-07', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'teecadonsolutions@gmail.com', '$2y$10$OvbFKu1uTWn8AL1E2357cOtBq1F.ZwyBWVD4WR7OZQKgpBuOD2hl6', 'img/signup/profile_68f7b5bd03af1.png', '{\"id_back\": \"img/signup/id_back_68f7b5bd06eac.png\", \"id_front\": \"img/signup/id_front_68f7b5bd06403.png\", \"insurance\": \"img/signup/insurance_68f7b5bd0574f.png\", \"vehicle_doc\": \"img/signup/vehicle_doc_68f7b5bd04b1a.png\", \"license_back\": \"img/signup/license_back_68f7b5bd041c6.png\", \"license_front\": \"img/signup/license_front_68f7b5bd03dea.png\", \"license_number\": \"125105210\", \"vehicle_number\": \"621615621\", \"license_expire_date\": \"2025-10-06\"}', NULL, 'USD', NULL, '2026-01-14 08:42:19', '2025-10-21 16:33:01', '2026-02-03 07:45:41', 1),
(14, 'guide', 'vihanga', 'English', '2025-10-29', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'tecaadonsolutions@gmail.com', '$2y$10$DXjtEX1r93G2C77etIZ5.eRFcCvCK7kYCevTat6AMEsk55SBElEPq', 'img/signup/profile_68f7ba4d33510.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f7ba4d34cda.png\", \"nic_front\": \"img/signup/nic_front_68f7ba4d349f1.png\", \"nic_passport\": \"110210\"}', 'USD', NULL, '2025-10-22 15:38:03', '2025-10-21 16:52:29', '2026-02-03 07:45:41', 0),
(16, 'guide', 'vihanga', 'English', '2025-10-29', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'tecaa2donsolutions@gmail.com', '$2y$10$OEuFTDQsKbCwbgM.KFDWmOSGeDyyxiFLFGA2uVx/LkbirDHqphaJa', 'img/signup/profile_68f7ba7bc402d.png', NULL, '{\"nic_back\": \"img/signup/nic_back_68f7ba7bc5e26.png\", \"nic_front\": \"img/signup/nic_front_68f7ba7bc586b.png\", \"nic_passport\": \"110210\"}', 'USD', NULL, NULL, '2025-10-21 16:53:16', '2026-02-03 07:45:41', 0),
(17, 'guide', 'vihanga', 'English', '2005-10-12', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'tec5aa2donsolutions@gmail.com', '$2y$10$Qgc.3TEGFIohZ.nDy9bqbu2DIll7d7McIHlVQDFbqywktbXt1L5qC', '/signup/profile/profile_697b49dc27b7a.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68f7bc0509a83.png\", \"nic_front\": \"img/signup/nic_front_68f7bc0509293.png\", \"nic_passport\": \"110210\"}', 'USD', NULL, '2026-02-01 16:50:44', '2025-10-21 16:59:49', '2026-02-09 08:12:10', 0),
(18, 'tourist', 'Abhijeeth', 'Sinhala', '2003-09-08', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'kandauda91@gmail.com', '$2y$10$F2bXJKaS4Gkp8.jhBxDfee6Cf7hBNEBXASJ0wzHQa/YEBDVVNQS02', 'img/signup/profile_68f8730dc6d46.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68f872f020a6d.jpg\", \"nic_front\": \"img/signup/nic_front_68f872f0206f6.jpg\", \"nic_passport\": \"200325211329\"}', 'USD', NULL, '2025-10-22 06:00:36', '2025-10-22 06:00:16', '2026-02-03 07:45:41', 0),
(19, 'site_moderator', 'lalinda', 'Japanese', '2025-10-17', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lalindasite@gmail.com', '$2y$10$4A1FLWpr.MKwaP0Zkp6jp.m04PZp.j7boxVpnbMY1LDMORLXRYsVa', 'img/signup/sample4.png', NULL, NULL, 'USD', NULL, '2025-10-24 02:57:08', '2025-10-22 15:41:48', '2026-02-03 07:45:41', 0),
(20, 'tourist', 'lalinda ravishan', 'Sinhala', '2025-09-30', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lwravishan@gmail.com', '$2y$10$DZS.vjlOVjHpNI0hFx2sbuG5JiWQnSYL1RuoCWZVwUFH4NxtfjsQ2', 'img/signup/profile_68fa04c417020.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68fa04c4183a1.jpg\", \"nic_front\": \"img/signup/nic_front_68fa04c41793a.jpg\", \"nic_passport\": \"200216901548\"}', 'USD', NULL, '2025-10-23 10:35:09', '2025-10-23 10:34:44', '2026-02-03 07:45:41', 0),
(22, 'site_moderator', 'fssfsf', 'Hindi', '2025-10-09', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'fsfsf@gamil.com', '$2y$10$1kxZk17a1H2XgAbucr7u1OANonXrzlICTOIztDGwuhzlxetYuD4Iq', NULL, NULL, NULL, 'USD', NULL, NULL, '2025-10-23 12:42:35', '2026-02-03 07:45:41', 0),
(23, 'site_moderator', 'lalinda', 'German', '2025-10-18', 'Female', '+94777512854', '', 'dsdfdf', 'lala124@gmail.com', '$2y$10$TRXql8Vr5EfmDCNnEFlvKu8KyosXXl560wprakoErKADbEpdhIenG', NULL, NULL, NULL, 'USD', NULL, NULL, '2025-10-23 12:49:39', '2026-02-03 07:45:41', 0),
(24, 'site_moderator', 'kasun', 'Japanese', '2002-02-23', 'Other', '+94777512854', '+94777512854', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'kasun@gmail.com', '$2y$10$bkVFsskL7bL/qpqmbhiNt.2VJ30jvn5MpVH97UJ3bRJKuwEBgX32.', NULL, NULL, NULL, 'USD', NULL, NULL, '2025-10-23 12:58:45', '2026-02-03 07:45:41', 0),
(25, 'site_moderator', 'lalinda', 'English', '2000-12-31', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lalinda1@gmail.com', '$2y$10$qVkPTOrf7LGtwyL8DpkxCOnUQVCnJKYbPRwKb5N5f8TdaF3XqFAEy', '', NULL, NULL, 'USD', NULL, '2026-02-09 08:01:04', '2025-10-24 03:27:41', '2026-02-09 08:01:04', 0),
(26, 'business_manager', 'lalinda', 'English', '1998-02-24', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lalinda2@gmail.com', '$2y$10$dp6SIougunU2GigEBvvD6.VVLchRD5pTc5ByXv8hvunu0r/71zH2C', '', NULL, NULL, 'USD', NULL, '2026-02-21 12:30:18', '2025-10-24 03:32:31', '2026-02-21 12:30:18', 0),
(27, 'tourist', 'ransara', 'English', '2025-09-23', 'Male', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'ransara12@gmail.com', '$2y$10$roSmeGfVroKVuQszGqD33eT8Xetakbpki6FjKgLcjcwLqpHyrzlUq', 'img/signup/profile_68fb05daaef3d.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68fb05daaf416.jpg\", \"nic_front\": \"img/signup/nic_front_68fb05daaf18c.jpg\", \"nic_passport\": \"200216901548\"}', 'USD', NULL, '2025-10-24 05:06:47', '2025-10-24 04:51:38', '2026-02-03 07:45:41', 0),
(28, 'tourist', 'Mahinda Rajapakse', 'English', '2025-10-08', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'sewmini@gmail.com', '$2y$10$k1ghQeTlxYw/nYFGXOFPHeaFV.ZGopXithkOxAqmAJmOq4Ru1Wru2', 'img/signup/profile_68fb160cbfa89.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_68fb160cbfd68.jpg\", \"nic_front\": \"img/signup/nic_front_68fb160cbfc30.jpg\", \"nic_passport\": \"200216901548\"}', 'LKR', NULL, '2026-02-09 05:01:26', '2025-10-24 06:00:45', '2026-02-09 05:01:26', 0),
(30, 'tourist', 'chiran', 'English', '2025-12-08', 'Female', '+94777512854', '', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'chirandd@gmail.com', '$2y$10$oMPKxU3yKVzjdFxF3W6CDOO8YmiNuCTMQE35lT5unD7wpBZ8Ngkv6', 'img/signup/profile_6952645a89de1.jpg', NULL, '{\"nic_back\": \"img/signup/nic_back_6952645a8aa5d.jpg\", \"nic_front\": \"img/signup/nic_front_6952645a8a4be.jpg\", \"nic_passport\": \"200216901548\"}', 'USD', NULL, NULL, '2025-12-29 11:22:03', '2026-02-03 07:45:41', 0),
(36, 'driver', 'lalinda ravishan', 'Sinhala', '2026-01-01', 'Female', '+94777512854', '+94777512854', 'Imaduwa road , Beranagoda,Yakkalamulla, Galle', 'lalinda.ravishan@aiesec.net', '$2y$10$a9qawQZAJ1NxWTJXaWeJZOaG56WK/kCV6AtZ97twFqMeLEA3CG73G', '/signup/profile/profile_69761c3d68b82.jpg', '{\"id_back\": \"/signup/nic/nic_back_driver_6967861e26a29.png\", \"id_front\": \"/signup/nic/nic_front_driver_6967861e2687c.png\", \"license_back\": \"/drivers/36/licenses/driver_license_licenseBack_1769347788.png\", \"nic_passport\": \"200216901548\", \"license_front\": \"/drivers/36/licenses/driver_license_licenseFront_1769347788.png\", \"license_number\": \"64205423jbhh\", \"license_expire_date\": \"2026-01-15\"}', NULL, 'USD', NULL, '2026-02-09 08:10:46', '2024-01-14 12:03:42', '2026-02-09 08:10:46', 1),
(37, 'tourist', 'Pasan', 'English', '2026-03-03', 'Male', '+94782498755', '+94782498755', 'dvsvmldskv', '2023cs218@stu.ucsc.cmb.ac.lk', '$2y$10$0aX21PJ2NuzclckyDw7tf.r4S5NG4oTF2NesHe0Cnt2hwJ6X0wBey', '/signup/profile/profile_6982361cee85d.png', NULL, '{\"nic_back\": \"/signup/nic/nic_back_6982361cef150.png\", \"nic_front\": \"/signup/nic/nic_front_6982361ceedb2.png\", \"nic_passport\": \"200216901548\"}', 'SEK', NULL, '2026-02-03 17:58:10', '2026-02-03 17:53:33', '2026-02-03 17:58:10', 0);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert_verification` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    -- Only create verification record for drivers and guides
    IF NEW.account_type IN ('driver', 'guide') THEN
        INSERT INTO account_verifications (
            userId, 
            status, 
            createdAt, 
            updatedAt
        ) VALUES (
            NEW.id,
            'pending',
            NEW.created_at,
            NEW.created_at
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `create_user_profile` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO profile_details (
        userId,  
        bio,
        languages,
        instaAccount, 
        facebookAccount, 
        dlVerified,
        tlSubmitted,
        tlVerified,
        tLicenseNumber,
        tLicenseExpiryDate,
        tLicensePhotoFront,
        tLicensePhotoBack,
        averageRating
    ) VALUES (
        NEW.id, 
        'Welcome to my profile in tripingoo!',
        NULL,
        NULL, 
        NULL, 
        FALSE,
        FALSE,
        FALSE,
        NULL,
        NULL,
        NULL,
        NULL,
        0.00
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_remember_tokens`
--

CREATE TABLE `user_remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_verifications_view`
-- (See below for the actual view)
--
CREATE TABLE `user_verifications_view` (
`userId` int(11)
,`accountType` enum('driver','guide','tourist','admin','site_moderator','business_manager')
,`fullName` varchar(255)
,`language` varchar(50)
,`dob` date
,`gender` varchar(20)
,`phone` varchar(25)
,`secondaryPhone` varchar(25)
,`address` text
,`email` varchar(255)
,`profilePhoto` varchar(255)
,`verified` int(11)
,`driverData` longtext
,`guideTouristData` longtext
,`lastLogin` timestamp
,`accountCreatedAt` timestamp
,`accountUpdatedAt` timestamp
,`verificationId` int(11)
,`verificationStatus` enum('pending','approved','rejected')
,`reviewedBy` int(11)
,`reviewedAt` timestamp
,`expiryDate` date
,`rejectionReason` text
,`verificationCreatedAt` timestamp
,`verificationUpdatedAt` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicleId` int(11) NOT NULL,
  `driverId` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `licensePlate` varchar(20) NOT NULL,
  `seatingCapacity` int(11) DEFAULT 4,
  `childSeats` int(11) NOT NULL DEFAULT 0,
  `fuelEfficiency` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `frontViewPhoto` varchar(255) DEFAULT NULL,
  `backViewPhoto` varchar(255) DEFAULT NULL,
  `sideViewPhoto` varchar(255) DEFAULT NULL,
  `interiorPhoto1` varchar(255) DEFAULT NULL,
  `interiorPhoto2` varchar(255) DEFAULT NULL,
  `interiorPhoto3` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `availability` tinyint(1) DEFAULT 1,
  `isApproved` tinyint(1) DEFAULT 0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicleId`, `driverId`, `make`, `model`, `year`, `color`, `licensePlate`, `seatingCapacity`, `childSeats`, `fuelEfficiency`, `description`, `frontViewPhoto`, `backViewPhoto`, `sideViewPhoto`, `interiorPhoto1`, `interiorPhoto2`, `interiorPhoto3`, `status`, `availability`, `isApproved`, `createdAt`, `updatedAt`) VALUES
(8, 36, 'taylor', 'jffcy', 2014, 'jhbhj', 'vhvhgv', 5, 0, 7.00, 'ytftydtshfyj', '/drivers/36/vehicles/vehicle_front_69743dbb545a2.png', '/drivers/36/vehicles/vehicle_back_69743dbb54ce1.png', '/drivers/36/vehicles/vehicle_side_69743dbb55231.png', '/drivers/36/vehicles/vehicle_interior1_69743dbb563fc.png', '/drivers/36/vehicles/vehicle_interior2_69743dbb5728f.png', '/drivers/36/vehicles/vehicle_interior3_69743dbb57cef.png', 1, 0, 1, '2026-01-24 03:34:19', '2026-02-09 08:11:31'),
(9, 36, 'ashok', 'leyland', 2015, 'white', 'abc-156', 5, 0, 4.90, 'thislsdcmsdkl dsmclsmcd', '/drivers/36/vehicles/vehicle_front_697448593a6ab.png', '/drivers/36/vehicles/vehicle_back_697448593bae0.png', '/drivers/36/vehicles/vehicle_side_697448593c7b7.jpg', '/drivers/36/vehicles/vehicle_interior1_697448593dc2d.jpg', '/drivers/36/vehicles/vehicle_interior2_697448593eeea.png', '/drivers/36/vehicles/vehicle_interior3_69744859400f3.jpg', 0, 0, 0, '2026-01-24 04:19:37', '2026-02-01 10:58:20'),
(10, 36, 'nissan ', 'gtx', 2015, 'white', 'nkjdn-123', 5, 1, 15.00, 'sdklcmklsdmc', '/drivers/36/vehicles/vehicle_front_69744edd1b168.jpg', '/drivers/36/vehicles/vehicle_back_69744edd1b51a.jpg', '/drivers/36/vehicles/vehicle_side_69744edd1c113.jpeg', '/drivers/36/vehicles/vehicle_interior1_69744edd1d39d.png', '/drivers/36/vehicles/vehicle_interior2_69744edd1e50c.png', '/drivers/36/vehicles/vehicle_interior3_69744edd1feab.png', 1, 1, 1, '2026-01-24 04:47:25', '2026-02-09 08:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_pricing`
--

CREATE TABLE `vehicle_pricing` (
  `pricingId` int(11) NOT NULL,
  `vehicleId` int(11) NOT NULL,
  `driverId` int(11) NOT NULL,
  `vehicleChargePerKm` decimal(10,2) NOT NULL DEFAULT 0.00,
  `driverChargePerKm` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vehicleChargePerDay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `driverChargePerDay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimumKm` decimal(5,2) DEFAULT 0.00,
  `minimumDays` decimal(3,1) DEFAULT 1.0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_pricing`
--

INSERT INTO `vehicle_pricing` (`pricingId`, `vehicleId`, `driverId`, `vehicleChargePerKm`, `driverChargePerKm`, `vehicleChargePerDay`, `driverChargePerDay`, `minimumKm`, `minimumDays`, `createdAt`, `updatedAt`) VALUES
(10, 10, 36, 50.00, 19.00, 45.00, 43.00, 78.00, 20.0, '2026-02-07 10:02:22', '2026-02-07 10:42:27'),
(11, 8, 36, 10.00, 4.00, 5552.00, 4.00, 0.00, 1.0, '2026-02-07 10:40:37', '2026-02-07 10:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_verifications`
--

CREATE TABLE `vehicle_verifications` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `vehicleId` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewedBy` int(11) DEFAULT NULL,
  `reviewedAt` timestamp NULL DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `rejectionReason` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_verifications`
--

INSERT INTO `vehicle_verifications` (`id`, `userId`, `vehicleId`, `status`, `reviewedBy`, `reviewedAt`, `expiryDate`, `rejectionReason`, `createdAt`, `updatedAt`) VALUES
(1, 36, 8, 'approved', 25, '2026-02-05 13:46:08', NULL, NULL, '2026-01-25 20:39:26', '2026-02-05 13:46:08'),
(2, 36, 9, 'rejected', 25, '2026-01-26 10:39:19', NULL, 'you suck', '2026-01-26 03:20:59', '2026-01-26 10:39:19'),
(3, 36, 10, 'approved', 25, '2026-01-26 10:48:26', NULL, NULL, '2026-01-26 07:51:43', '2026-01-26 10:48:26');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_mainfilters_with_subfilters`
-- (See below for the actual view)
--
CREATE TABLE `view_mainfilters_with_subfilters` (
`mainFilterId` int(11)
,`mainFilterName` varchar(255)
,`subFilterId` int(11)
,`subFilterName` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_user_complete_profiles`
-- (See below for the actual view)
--
CREATE TABLE `vw_user_complete_profiles` (
`userId` int(11)
,`account_type` enum('driver','guide','tourist','admin','site_moderator','business_manager')
,`fullname` varchar(255)
,`dob` date
,`gender` varchar(20)
,`phone` varchar(25)
,`secondary_phone` varchar(25)
,`address` text
,`email` varchar(255)
,`profile_photo` varchar(255)
,`driver_data` longtext
,`guide_tourist_data` longtext
,`user_created_at` timestamp
,`user_updated_at` timestamp
,`profileId` int(11)
,`bio` text
,`languages` varchar(500)
,`instaAccount` varchar(255)
,`facebookAccount` varchar(255)
,`dlVerified` tinyint(1)
,`tlSubmitted` tinyint(1)
,`tlVerified` tinyint(1)
,`tLicenseNumber` varchar(100)
,`tLicenseExpiryDate` date
,`tLicensePhotoFront` varchar(255)
,`tLicensePhotoBack` varchar(255)
,`averageRating` decimal(3,2)
,`profile_created_at` timestamp
,`profile_updated_at` timestamp
,`age` bigint(21)
,`verification_status_text` varchar(12)
,`social_links_status` varchar(15)
);

-- --------------------------------------------------------

--
-- Structure for view `driver_vehicles_view`
--
DROP TABLE IF EXISTS `driver_vehicles_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `driver_vehicles_view`  AS SELECT `u`.`id` AS `driverId`, `u`.`account_type` AS `accountType`, `u`.`fullname` AS `driverName`, `u`.`language` AS `driverLanguage`, `u`.`dob` AS `driverDob`, `u`.`gender` AS `driverGender`, `u`.`phone` AS `driverPhone`, `u`.`secondary_phone` AS `driverSecondaryPhone`, `u`.`address` AS `driverAddress`, `u`.`email` AS `driverEmail`, `u`.`profile_photo` AS `driverProfilePhoto`, `u`.`verified` AS `driverVerified`, `u`.`driver_data` AS `driverData`, `u`.`last_login` AS `driverLastLogin`, `u`.`created_at` AS `driverCreatedAt`, `u`.`updated_at` AS `driverUpdatedAt`, `v`.`vehicleId` AS `vehicleId`, `v`.`make` AS `make`, `v`.`model` AS `model`, `v`.`year` AS `year`, `v`.`color` AS `color`, `v`.`licensePlate` AS `licensePlate`, `v`.`seatingCapacity` AS `seatingCapacity`, `v`.`childSeats` AS `childSeats`, `v`.`fuelEfficiency` AS `fuelEfficiency`, `v`.`description` AS `vehicleDescription`, `v`.`frontViewPhoto` AS `frontViewPhoto`, `v`.`backViewPhoto` AS `backViewPhoto`, `v`.`sideViewPhoto` AS `sideViewPhoto`, `v`.`interiorPhoto1` AS `interiorPhoto1`, `v`.`interiorPhoto2` AS `interiorPhoto2`, `v`.`interiorPhoto3` AS `interiorPhoto3`, `v`.`status` AS `vehicleStatus`, `v`.`availability` AS `vehicleAvailability`, `v`.`isApproved` AS `vehicleApproved`, `v`.`createdAt` AS `vehicleCreatedAt`, `v`.`updatedAt` AS `vehicleUpdatedAt` FROM (`users` `u` join `vehicles` `v` on(`u`.`id` = `v`.`driverId`)) WHERE `u`.`account_type` = 'driver' ;

-- --------------------------------------------------------

--
-- Structure for view `travel_spot_card_data`
--
DROP TABLE IF EXISTS `travel_spot_card_data`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `travel_spot_card_data`  AS SELECT `tmf`.`mainFilterId` AS `mainFilterId`, `tmf`.`mainFilterName` AS `mainFilterName`, `tsf`.`subFilterId` AS `subFilterId`, `tsf`.`subFilterName` AS `subFilterName`, `ts`.`spotId` AS `spotId`, `ts`.`spotName` AS `spotName`, `ts`.`overview` AS `overview`, `ts`.`averageRating` AS `averageRating`, `ts`.`totalReviews` AS `totalReviews`, `ts`.`district` AS `district`, `ts`.`province` AS `province`, `tsp`.`photoPath` AS `photoPath`, CASE WHEN `tsf`.`subFilterId` is null THEN 'MAIN_FILTER_WITHOUT_SUBFILTERS' WHEN `ts`.`spotId` is null THEN 'SUBFILTER_WITHOUT_SPOTS' ELSE 'COMPLETE_CHAIN' END AS `status` FROM ((((`travelspots_mainfilters` `tmf` left join `travelspots_subfilters` `tsf` on(`tmf`.`mainFilterId` = `tsf`.`mainFilterId`)) left join `travel_spots_subfilters` `tss` on(`tsf`.`subFilterId` = `tss`.`subFilterId`)) left join `travel_spots` `ts` on(`tss`.`spotId` = `ts`.`spotId`)) left join `travel_spots_photos` `tsp` on(`tss`.`spotId` = `tsp`.`spotId`)) ORDER BY `tmf`.`mainFilterName` ASC, CASE WHEN `tsf`.`subFilterName` is null THEN 1 ELSE 0 END ASC, `tsf`.`subFilterName` ASC, `ts`.`spotName` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `user_verifications_view`
--
DROP TABLE IF EXISTS `user_verifications_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_verifications_view`  AS SELECT `u`.`id` AS `userId`, `u`.`account_type` AS `accountType`, `u`.`fullname` AS `fullName`, `u`.`language` AS `language`, `u`.`dob` AS `dob`, `u`.`gender` AS `gender`, `u`.`phone` AS `phone`, `u`.`secondary_phone` AS `secondaryPhone`, `u`.`address` AS `address`, `u`.`email` AS `email`, `u`.`profile_photo` AS `profilePhoto`, `u`.`verified` AS `verified`, `u`.`driver_data` AS `driverData`, `u`.`guide_tourist_data` AS `guideTouristData`, `u`.`last_login` AS `lastLogin`, `u`.`created_at` AS `accountCreatedAt`, `u`.`updated_at` AS `accountUpdatedAt`, `av`.`id` AS `verificationId`, `av`.`status` AS `verificationStatus`, `av`.`reviewedBy` AS `reviewedBy`, `av`.`reviewedAt` AS `reviewedAt`, `av`.`expiryDate` AS `expiryDate`, `av`.`rejectionReason` AS `rejectionReason`, `av`.`createdAt` AS `verificationCreatedAt`, `av`.`updatedAt` AS `verificationUpdatedAt` FROM (`users` `u` left join `account_verifications` `av` on(`u`.`id` = `av`.`userId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_mainfilters_with_subfilters`
--
DROP TABLE IF EXISTS `view_mainfilters_with_subfilters`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_mainfilters_with_subfilters`  AS SELECT `mf`.`mainFilterId` AS `mainFilterId`, `mf`.`mainFilterName` AS `mainFilterName`, `sf`.`subFilterId` AS `subFilterId`, `sf`.`subFilterName` AS `subFilterName` FROM (`travelspots_mainfilters` `mf` left join `travelspots_subfilters` `sf` on(`mf`.`mainFilterId` = `sf`.`mainFilterId`)) ORDER BY `mf`.`mainFilterName` ASC, `sf`.`subFilterName` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_user_complete_profiles`
--
DROP TABLE IF EXISTS `vw_user_complete_profiles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_user_complete_profiles`  AS SELECT `u`.`id` AS `userId`, `u`.`account_type` AS `account_type`, `u`.`fullname` AS `fullname`, `u`.`dob` AS `dob`, `u`.`gender` AS `gender`, `u`.`phone` AS `phone`, `u`.`secondary_phone` AS `secondary_phone`, `u`.`address` AS `address`, `u`.`email` AS `email`, `u`.`profile_photo` AS `profile_photo`, `u`.`driver_data` AS `driver_data`, `u`.`guide_tourist_data` AS `guide_tourist_data`, `u`.`created_at` AS `user_created_at`, `u`.`updated_at` AS `user_updated_at`, `pd`.`profileId` AS `profileId`, `pd`.`bio` AS `bio`, `pd`.`languages` AS `languages`, `pd`.`instaAccount` AS `instaAccount`, `pd`.`facebookAccount` AS `facebookAccount`, `pd`.`dlVerified` AS `dlVerified`, `pd`.`tlSubmitted` AS `tlSubmitted`, `pd`.`tlVerified` AS `tlVerified`, `pd`.`tLicenseNumber` AS `tLicenseNumber`, `pd`.`tLicenseExpiryDate` AS `tLicenseExpiryDate`, `pd`.`tLicensePhotoFront` AS `tLicensePhotoFront`, `pd`.`tLicensePhotoBack` AS `tLicensePhotoBack`, `pd`.`averageRating` AS `averageRating`, `pd`.`createdAt` AS `profile_created_at`, `pd`.`updatedAt` AS `profile_updated_at`, timestampdiff(YEAR,`u`.`dob`,curdate()) AS `age`, CASE WHEN `pd`.`dlVerified` = 1 THEN 'Verified' ELSE 'Not Verified' END AS `verification_status_text`, CASE WHEN `pd`.`instaAccount` is not null AND `pd`.`facebookAccount` is not null THEN 'Both' WHEN `pd`.`instaAccount` is not null THEN 'Instagram Only' WHEN `pd`.`facebookAccount` is not null THEN 'Facebook Only' ELSE 'No Social Links' END AS `social_links_status` FROM (`users` `u` left join `profile_details` `pd` on(`u`.`id` = `pd`.`userId`)) ORDER BY `u`.`fullname` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_verifications`
--
ALTER TABLE `account_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_reviewedBy` (`reviewedBy`),
  ADD KEY `idx_createdAt` (`createdAt`),
  ADD KEY `idx_status_userId` (`status`,`userId`),
  ADD KEY `idx_reviewedAt` (`reviewedAt`);

--
-- Indexes for table `commission_rates`
--
ALTER TABLE `commission_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role` (`role`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `commission_rate_history`
--
ALTER TABLE `commission_rate_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_role_date` (`role`,`effective_from`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `cover_photos`
--
ALTER TABLE `cover_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_order` (`userId`,`photo_order`);

--
-- Indexes for table `created_trips`
--
ALTER TABLE `created_trips`
  ADD PRIMARY KEY (`tripId`),
  ADD KEY `user_id` (`userId`);

--
-- Indexes for table `driver_unavailable_dates`
--
ALTER TABLE `driver_unavailable_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tripId` (`tripId`),
  ADD KEY `idx_driver_id` (`driverId`),
  ADD KEY `idx_date` (`unavailableDate`),
  ADD KEY `idx_reason` (`reason`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_licensed` (`is_licensed`),
  ADD KEY `idx_trending` (`is_trending`),
  ADD KEY `idx_tourist` (`is_tourist_guide`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `guide_locations`
--
ALTER TABLE `guide_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guideId` (`guideId`),
  ADD KEY `spotId` (`spotId`);

--
-- Indexes for table `profile_details`
--
ALTER TABLE `profile_details`
  ADD PRIMARY KEY (`profileId`),
  ADD UNIQUE KEY `userId` (`userId`);

--
-- Indexes for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `traveller_id` (`traveller_id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `tlicense_verifications`
--
ALTER TABLE `tlicense_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_reviewedBy` (`reviewedBy`),
  ADD KEY `idx_createdAt` (`createdAt`),
  ADD KEY `idx_status_userId` (`status`,`userId`),
  ADD KEY `idx_reviewedAt` (`reviewedAt`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `traveller_side_d_requests`
--
ALTER TABLE `traveller_side_d_requests`
  ADD KEY `tripId` (`tripId`),
  ADD KEY `driverId` (`driverId`);

--
-- Indexes for table `traveller_side_g_requests`
--
ALTER TABLE `traveller_side_g_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_tripId` (`tripId`),
  ADD KEY `idx_eventId` (`eventId`),
  ADD KEY `idx_travelSpotId` (`travelSpotId`),
  ADD KEY `idx_guideId` (`guideId`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_createdAt` (`createdAt`),
  ADD KEY `idx_status_trip` (`status`,`tripId`),
  ADD KEY `idx_user_status` (`userId`,`status`),
  ADD KEY `idx_event_guide` (`eventId`,`guideId`);

--
-- Indexes for table `travelspots_mainfilters`
--
ALTER TABLE `travelspots_mainfilters`
  ADD PRIMARY KEY (`mainFilterId`),
  ADD KEY `moderatorId` (`moderatorId`);

--
-- Indexes for table `travelspots_subfilters`
--
ALTER TABLE `travelspots_subfilters`
  ADD PRIMARY KEY (`subFilterId`),
  ADD KEY `moderatorId` (`moderatorId`),
  ADD KEY `mainFilterId` (`mainFilterId`);

--
-- Indexes for table `travel_spots`
--
ALTER TABLE `travel_spots`
  ADD PRIMARY KEY (`spotId`),
  ADD UNIQUE KEY `spotName` (`spotName`);

--
-- Indexes for table `travel_spots_contributions`
--
ALTER TABLE `travel_spots_contributions`
  ADD PRIMARY KEY (`contributionId`),
  ADD KEY `spotId` (`spotId`),
  ADD KEY `moderatorId` (`moderatorId`);

--
-- Indexes for table `travel_spots_itinerary`
--
ALTER TABLE `travel_spots_itinerary`
  ADD PRIMARY KEY (`pointId`),
  ADD UNIQUE KEY `unique_coordinates` (`spotId`,`latitude`,`longitude`),
  ADD UNIQUE KEY `unique_point_name` (`pointName`);

--
-- Indexes for table `travel_spots_nearbyspots`
--
ALTER TABLE `travel_spots_nearbyspots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sourceSpotId` (`sourceSpotId`),
  ADD KEY `nearbySpotId` (`nearbySpotId`);

--
-- Indexes for table `travel_spots_photos`
--
ALTER TABLE `travel_spots_photos`
  ADD PRIMARY KEY (`photoId`),
  ADD KEY `spotId` (`spotId`);

--
-- Indexes for table `travel_spots_subfilters`
--
ALTER TABLE `travel_spots_subfilters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_spot_subfilter` (`spotId`,`subFilterId`),
  ADD KEY `subFilterId` (`subFilterId`);

--
-- Indexes for table `trip_events`
--
ALTER TABLE `trip_events`
  ADD PRIMARY KEY (`eventId`),
  ADD KEY `travelSpotId` (`travelSpotId`),
  ADD KEY `idx_trip_events_tripId` (`tripId`),
  ADD KEY `idx_trip_events_userId` (`userId`),
  ADD KEY `idx_trip_events_date` (`eventDate`),
  ADD KEY `idx_trip_events_type` (`eventType`),
  ADD KEY `idx_trip_events_coords` (`latitude`,`longitude`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicleId`),
  ADD UNIQUE KEY `licensePlate` (`licensePlate`),
  ADD KEY `driverId` (`driverId`);

--
-- Indexes for table `vehicle_pricing`
--
ALTER TABLE `vehicle_pricing`
  ADD PRIMARY KEY (`pricingId`),
  ADD UNIQUE KEY `unique_vehicle_pricing` (`vehicleId`),
  ADD KEY `driverId` (`driverId`),
  ADD KEY `idx_vehicle_id` (`vehicleId`);

--
-- Indexes for table `vehicle_verifications`
--
ALTER TABLE `vehicle_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_reviewedBy` (`reviewedBy`),
  ADD KEY `idx_createdAt` (`createdAt`),
  ADD KEY `idx_status_userId` (`status`,`userId`),
  ADD KEY `idx_reviewedAt` (`reviewedAt`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_verifications`
--
ALTER TABLE `account_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `commission_rates`
--
ALTER TABLE `commission_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `commission_rate_history`
--
ALTER TABLE `commission_rate_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cover_photos`
--
ALTER TABLE `cover_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `created_trips`
--
ALTER TABLE `created_trips`
  MODIFY `tripId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `driver_unavailable_dates`
--
ALTER TABLE `driver_unavailable_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guide_locations`
--
ALTER TABLE `guide_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `profile_details`
--
ALTER TABLE `profile_details`
  MODIFY `profileId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `refund_requests`
--
ALTER TABLE `refund_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tlicense_verifications`
--
ALTER TABLE `tlicense_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `traveller_side_g_requests`
--
ALTER TABLE `traveller_side_g_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `travelspots_mainfilters`
--
ALTER TABLE `travelspots_mainfilters`
  MODIFY `mainFilterId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `travelspots_subfilters`
--
ALTER TABLE `travelspots_subfilters`
  MODIFY `subFilterId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `travel_spots`
--
ALTER TABLE `travel_spots`
  MODIFY `spotId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `travel_spots_contributions`
--
ALTER TABLE `travel_spots_contributions`
  MODIFY `contributionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `travel_spots_itinerary`
--
ALTER TABLE `travel_spots_itinerary`
  MODIFY `pointId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `travel_spots_nearbyspots`
--
ALTER TABLE `travel_spots_nearbyspots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `travel_spots_photos`
--
ALTER TABLE `travel_spots_photos`
  MODIFY `photoId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `travel_spots_subfilters`
--
ALTER TABLE `travel_spots_subfilters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `trip_events`
--
ALTER TABLE `trip_events`
  MODIFY `eventId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user_remember_tokens`
--
ALTER TABLE `user_remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicle_pricing`
--
ALTER TABLE `vehicle_pricing`
  MODIFY `pricingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle_verifications`
--
ALTER TABLE `vehicle_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_verifications`
--
ALTER TABLE `account_verifications`
  ADD CONSTRAINT `account_verifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_verifications_ibfk_2` FOREIGN KEY (`reviewedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commission_rates`
--
ALTER TABLE `commission_rates`
  ADD CONSTRAINT `commission_rates_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commission_rate_history`
--
ALTER TABLE `commission_rate_history`
  ADD CONSTRAINT `commission_rate_history_ibfk_1` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `created_trips`
--
ALTER TABLE `created_trips`
  ADD CONSTRAINT `created_trips_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `driver_unavailable_dates`
--
ALTER TABLE `driver_unavailable_dates`
  ADD CONSTRAINT `driver_unavailable_dates_ibfk_1` FOREIGN KEY (`driverId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_unavailable_dates_ibfk_2` FOREIGN KEY (`tripId`) REFERENCES `created_trips` (`tripId`) ON DELETE SET NULL;

--
-- Constraints for table `guide_locations`
--
ALTER TABLE `guide_locations`
  ADD CONSTRAINT `guide_locations_ibfk_1` FOREIGN KEY (`guideId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_locations_ibfk_2` FOREIGN KEY (`spotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE;

--
-- Constraints for table `profile_details`
--
ALTER TABLE `profile_details`
  ADD CONSTRAINT `profile_details_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD CONSTRAINT `refund_requests_ibfk_1` FOREIGN KEY (`traveller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `refund_requests_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `created_trips` (`tripId`);

--
-- Constraints for table `tlicense_verifications`
--
ALTER TABLE `tlicense_verifications`
  ADD CONSTRAINT `tlicense_verifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tlicense_verifications_ibfk_2` FOREIGN KEY (`reviewedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- Constraints for table `traveller_side_d_requests`
--
ALTER TABLE `traveller_side_d_requests`
  ADD CONSTRAINT `traveller_side_d_requests_ibfk_1` FOREIGN KEY (`tripId`) REFERENCES `created_trips` (`tripId`),
  ADD CONSTRAINT `traveller_side_d_requests_ibfk_2` FOREIGN KEY (`driverId`) REFERENCES `users` (`id`);

--
-- Constraints for table `traveller_side_g_requests`
--
ALTER TABLE `traveller_side_g_requests`
  ADD CONSTRAINT `traveller_side_g_requests_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `traveller_side_g_requests_ibfk_2` FOREIGN KEY (`tripId`) REFERENCES `created_trips` (`tripId`) ON DELETE CASCADE,
  ADD CONSTRAINT `traveller_side_g_requests_ibfk_3` FOREIGN KEY (`eventId`) REFERENCES `trip_events` (`eventId`) ON DELETE CASCADE,
  ADD CONSTRAINT `traveller_side_g_requests_ibfk_4` FOREIGN KEY (`travelSpotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE,
  ADD CONSTRAINT `traveller_side_g_requests_ibfk_5` FOREIGN KEY (`guideId`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `travelspots_mainfilters`
--
ALTER TABLE `travelspots_mainfilters`
  ADD CONSTRAINT `travelspots_mainfilters_ibfk_1` FOREIGN KEY (`moderatorId`) REFERENCES `users` (`id`);

--
-- Constraints for table `travelspots_subfilters`
--
ALTER TABLE `travelspots_subfilters`
  ADD CONSTRAINT `travelspots_subfilters_ibfk_1` FOREIGN KEY (`moderatorId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `travelspots_subfilters_ibfk_2` FOREIGN KEY (`mainFilterId`) REFERENCES `travelspots_mainfilters` (`mainFilterId`) ON DELETE CASCADE;

--
-- Constraints for table `travel_spots_contributions`
--
ALTER TABLE `travel_spots_contributions`
  ADD CONSTRAINT `travel_spots_contributions_ibfk_1` FOREIGN KEY (`spotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE,
  ADD CONSTRAINT `travel_spots_contributions_ibfk_2` FOREIGN KEY (`moderatorId`) REFERENCES `users` (`id`);

--
-- Constraints for table `travel_spots_itinerary`
--
ALTER TABLE `travel_spots_itinerary`
  ADD CONSTRAINT `travel_spots_itinerary_ibfk_1` FOREIGN KEY (`spotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE;

--
-- Constraints for table `travel_spots_nearbyspots`
--
ALTER TABLE `travel_spots_nearbyspots`
  ADD CONSTRAINT `travel_spots_nearbyspots_ibfk_1` FOREIGN KEY (`sourceSpotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE,
  ADD CONSTRAINT `travel_spots_nearbyspots_ibfk_2` FOREIGN KEY (`nearbySpotId`) REFERENCES `travel_spots` (`spotId`);

--
-- Constraints for table `travel_spots_photos`
--
ALTER TABLE `travel_spots_photos`
  ADD CONSTRAINT `travel_spots_photos_ibfk_1` FOREIGN KEY (`spotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE;

--
-- Constraints for table `travel_spots_subfilters`
--
ALTER TABLE `travel_spots_subfilters`
  ADD CONSTRAINT `travel_spots_subfilters_ibfk_1` FOREIGN KEY (`spotId`) REFERENCES `travel_spots` (`spotId`) ON DELETE CASCADE,
  ADD CONSTRAINT `travel_spots_subfilters_ibfk_2` FOREIGN KEY (`subFilterId`) REFERENCES `travelspots_subfilters` (`subFilterId`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`driverId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_pricing`
--
ALTER TABLE `vehicle_pricing`
  ADD CONSTRAINT `vehicle_pricing_ibfk_1` FOREIGN KEY (`vehicleId`) REFERENCES `vehicles` (`vehicleId`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_pricing_ibfk_2` FOREIGN KEY (`driverId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_verifications`
--
ALTER TABLE `vehicle_verifications`
  ADD CONSTRAINT `vehicle_verifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_verifications_ibfk_2` FOREIGN KEY (`reviewedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
