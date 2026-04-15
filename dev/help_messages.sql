-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 15, 2026 at 10:03 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `help_messages`
--

CREATE TABLE `help_messages` (
  `id` bigint(20) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `sender_type` varchar(50) NOT NULL COMMENT 'Traveller, Guide, Driver, Moderator',
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `help_messages`
--

INSERT INTO `help_messages` (`id`, `chat_id`, `sender_id`, `sender_type`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 2, 'Traveller', 'hello', 1, '2026-02-07 12:49:50'),
(2, 2, 2, 'Driver', 'hello', 1, '2026-02-07 12:56:58'),
(3, 2, 2, 'Driver', 'Hello, I need help', 1, '2026-02-08 09:54:40'),
(4, 2, 2, 'Driver', 'hi', 1, '2026-02-08 09:57:56'),
(5, 2, 2, 'Driver', 'haloooooooo', 1, '2026-02-08 09:58:41'),
(6, 2, 2, 'Driver', 'gii', 1, '2026-02-08 10:22:56'),
(7, 2, 2, 'Driver', 'mn', 1, '2026-02-08 10:29:08'),
(8, 2, 2, 'Driver', 'j', 1, '2026-02-08 10:29:34'),
(9, 2, 2, 'Driver', 'sdfghj', 1, '2026-02-08 10:38:37'),
(10, 1, 19, 'Moderator', 'hello', 1, '2026-02-09 04:52:54'),
(11, 2, 2, 'Driver', 'today', 1, '2026-02-09 04:53:59'),
(12, 2, 19, 'Moderator', 'hello', 0, '2026-02-09 04:54:53'),
(13, 2, 19, 'Moderator', 'hh', 0, '2026-02-09 04:55:42'),
(14, 2, 19, 'Moderator', 'jj', 0, '2026-02-09 04:56:23'),
(15, 5, 2, 'Driver', 'hi', 1, '2026-02-09 04:57:38'),
(16, 5, 19, 'Moderator', 'udaya', 0, '2026-02-09 05:14:25'),
(17, 5, 19, 'Moderator', 'hi', 0, '2026-02-09 05:14:44'),
(18, 5, 19, 'Moderator', 'jj', 0, '2026-02-09 05:14:57'),
(19, 7, 2, 'Driver', 'hello', 1, '2026-02-09 08:22:00'),
(20, 7, 19, 'Moderator', 'ok', 0, '2026-02-09 08:22:25'),
(21, 7, 19, 'Moderator', 'yoy[', 0, '2026-02-09 08:22:54'),
(22, 7, 2, 'Driver', 'hi', 1, '2026-02-10 07:46:28'),
(23, 7, 19, 'Moderator', 'kohomda machn', 0, '2026-02-10 07:47:06'),
(24, 7, 19, 'Moderator', 'hodai bn', 0, '2026-02-10 07:47:15'),
(25, 8, 7, 'Traveller', 'hi', 1, '2026-02-10 07:51:55'),
(26, 8, 19, 'Moderator', 'ok', 1, '2026-02-10 07:57:58'),
(27, 8, 19, 'Moderator', 'some times we can see', 1, '2026-02-10 07:58:07'),
(28, 8, 19, 'Moderator', 'hi', 1, '2026-02-10 07:59:59'),
(29, 8, 19, 'Moderator', 'hi', 1, '2026-02-10 08:00:07'),
(30, 8, 19, 'Moderator', 'ok', 1, '2026-02-10 08:00:25'),
(31, 6, 19, 'Moderator', 's', 0, '2026-02-10 08:02:22'),
(32, 6, 19, 'Moderator', 'ds', 0, '2026-02-10 08:02:33'),
(33, 7, 19, 'Moderator', 'sdds', 0, '2026-02-10 08:02:53'),
(34, 6, 19, 'Moderator', 'dsdsds', 0, '2026-02-10 08:03:40'),
(35, 6, 19, 'Moderator', 'sdsdsdsdsdsdsd', 0, '2026-02-10 08:03:51'),
(36, 8, 19, 'Moderator', 'helo', 1, '2026-02-10 08:04:00'),
(37, 8, 7, 'Traveller', 'how are', 1, '2026-02-10 08:04:30'),
(38, 8, 19, 'Moderator', 'asda', 1, '2026-02-10 08:06:13'),
(39, 8, 19, 'Moderator', 'fjdf', 1, '2026-02-10 08:06:24'),
(40, 8, 19, 'Moderator', 'sd', 1, '2026-02-10 08:06:31'),
(41, 8, 19, 'Moderator', 'jjkkkkkkk', 1, '2026-02-10 08:06:51'),
(42, 7, 19, 'Moderator', 'sasasasasasasas', 0, '2026-02-10 08:07:15'),
(43, 8, 7, 'Traveller', 'hiii', 1, '2026-02-10 08:20:23'),
(44, 8, 19, 'Moderator', 'hjkdd', 1, '2026-02-10 08:20:50'),
(45, 8, 19, 'Moderator', 'cfcf', 1, '2026-02-10 08:22:07'),
(46, 8, 7, 'Traveller', 'gi', 1, '2026-02-10 08:22:59'),
(47, 8, 7, 'Traveller', 'gq', 1, '2026-02-10 08:23:06'),
(48, 7, 2, 'Driver', 'df', 1, '2026-02-10 08:24:55'),
(49, 8, 19, 'Moderator', 'hi', 0, '2026-02-10 09:58:30'),
(50, 8, 19, 'Moderator', 'hello kohomda', 0, '2026-02-10 09:58:40'),
(51, 8, 19, 'Moderator', 'hodai mn', 0, '2026-02-10 09:58:53'),
(52, 8, 19, 'Moderator', 'awlk nh', 0, '2026-02-10 09:58:57'),
(53, 8, 7, 'Traveller', 'fdfghjk', 1, '2026-02-10 10:01:16'),
(54, 10, 17, 'Guide', 'fdfd', 1, '2026-02-10 10:17:17'),
(55, 8, 19, 'Moderator', 'sasa\'', 0, '2026-02-10 11:13:48'),
(56, 11, 7, 'Traveller', 'fdfdf', 1, '2026-02-10 11:14:28'),
(57, 12, 26, 'Business_manager', 'ef', 0, '2026-02-11 08:08:38'),
(58, 12, 19, 'Moderator', 'hello', 0, '2026-02-11 08:09:22'),
(59, 11, 7, 'Traveller', 'dfdfdfdfdfdfdfdf', 1, '2026-02-11 08:09:48'),
(60, 11, 19, 'Moderator', 'hello', 0, '2026-02-11 08:10:51'),
(61, 11, 19, 'Moderator', 'kohomda', 0, '2026-02-11 08:11:06'),
(62, 14, 2, 'Driver', 'ho', 1, '2026-02-11 08:24:27'),
(63, 14, 19, 'Moderator', 'hooo', 0, '2026-02-11 08:25:14'),
(64, 14, 19, 'Moderator', 'goood', 0, '2026-02-11 08:25:19'),
(65, 14, 19, 'Moderator', 'hkjh', 0, '2026-02-11 08:26:31'),
(66, 16, 2, 'Driver', 'ds', 1, '2026-02-11 09:30:49'),
(67, 16, 19, 'Moderator', 'kohomda', 1, '2026-02-11 09:31:17'),
(68, 16, 19, 'Moderator', 'hodai', 1, '2026-02-11 09:31:28'),
(69, 16, 19, 'Moderator', 'ehemda', 1, '2026-02-11 09:31:37'),
(70, 16, 19, 'Moderator', 'ow', 1, '2026-02-11 09:31:42'),
(71, 16, 2, 'Driver', 'hi', 1, '2026-02-11 09:36:51'),
(72, 16, 19, 'Moderator', 'kohomdaaa', 1, '2026-02-11 09:37:26'),
(73, 16, 19, 'Moderator', 'hodai', 1, '2026-02-11 09:37:32'),
(74, 16, 19, 'Moderator', 'ss', 1, '2026-02-11 09:37:45'),
(75, 16, 2, 'Driver', 'hello', 1, '2026-02-11 09:56:43'),
(76, 16, 2, 'Driver', 'kohomda', 1, '2026-02-11 09:56:56'),
(77, 16, 19, 'Moderator', 'hodaid', 1, '2026-02-11 09:57:23'),
(78, 16, 19, 'Moderator', 'awlk nh', 1, '2026-02-11 09:57:36'),
(79, 16, 2, 'Driver', 'kohomda', 1, '2026-02-11 09:58:25'),
(80, 16, 19, 'Moderator', 'np', 1, '2026-02-11 09:58:47'),
(81, 16, 19, 'Moderator', 'hm', 1, '2026-02-11 09:58:53'),
(82, 16, 19, 'Moderator', 'same here', 1, '2026-02-11 09:59:01'),
(83, 16, 2, 'Driver', 'gugugu', 1, '2026-02-11 10:09:08'),
(84, 16, 19, 'Moderator', 'ucsc', 1, '2026-02-11 10:09:41'),
(85, 16, 19, 'Moderator', 'uoc', 1, '2026-02-11 10:10:20'),
(86, 16, 19, 'Moderator', 'hu]', 1, '2026-02-11 10:11:58'),
(87, 16, 19, 'Moderator', 'ghjk', 1, '2026-02-11 10:12:23'),
(88, 16, 19, 'Moderator', 'zz', 1, '2026-02-11 10:15:11'),
(89, 16, 2, 'Driver', 'saasasasasasasaasasasa', 1, '2026-02-11 10:16:42'),
(90, 16, 19, 'Moderator', 'xcvbnm,l;loikjuhg', 1, '2026-02-11 10:17:13'),
(91, 16, 19, 'Moderator', 'sedrtyuijhgf', 1, '2026-02-11 10:17:20'),
(92, 16, 19, 'Moderator', 's', 1, '2026-02-11 10:17:24'),
(93, 16, 2, 'Driver', 'jkkj', 1, '2026-02-11 10:24:07'),
(94, 16, 2, 'Driver', 'kjkk', 1, '2026-02-11 10:24:12'),
(95, 16, 19, 'Moderator', 'manju', 1, '2026-02-11 10:24:53'),
(96, 16, 19, 'Moderator', 'cmplc', 1, '2026-02-11 10:24:59'),
(97, 16, 2, 'Driver', 'hi', 1, '2026-02-11 10:46:54'),
(98, 16, 2, 'Driver', 'hello', 1, '2026-02-11 10:47:01'),
(99, 16, 19, 'Moderator', 'ok', 0, '2026-02-11 10:47:26'),
(100, 16, 19, 'Moderator', 'heee', 0, '2026-02-11 10:47:34'),
(101, 16, 19, 'Moderator', 'how sad', 0, '2026-02-11 10:47:45'),
(102, 16, 19, 'Moderator', 'jj', 0, '2026-02-11 10:47:50'),
(103, 16, 19, 'Moderator', 'ghjk', 0, '2026-02-11 10:48:17'),
(104, 16, 19, 'Moderator', 'nnn', 0, '2026-02-11 11:07:00'),
(105, 16, 19, 'Moderator', 'jiiij', 0, '2026-02-11 11:11:29'),
(106, 16, 19, 'Moderator', 'ko', 0, '2026-02-11 11:11:38'),
(107, 17, 19, 'Moderator', 'test123', 0, '2026-02-11 11:29:57'),
(108, 17, 19, 'Moderator', 'helo', 0, '2026-02-11 11:35:23'),
(109, 17, 19, 'Moderator', 'hi', 0, '2026-02-11 11:35:42'),
(110, 16, 2, 'Driver', 'jo', 1, '2026-02-11 11:39:19'),
(111, 16, 19, 'Moderator', 'kohomd', 0, '2026-02-11 11:39:29'),
(112, 16, 2, 'Driver', 'hari gihind', 1, '2026-02-11 11:39:38'),
(113, 16, 19, 'Moderator', 'OWWW', 0, '2026-02-11 11:39:45'),
(114, 16, 2, 'Driver', 'mar\\', 1, '2026-02-11 11:39:52'),
(115, 16, 19, 'Moderator', 'heeee', 0, '2026-02-11 11:39:59'),
(116, 16, 2, 'Driver', 'yoyo', 1, '2026-02-11 11:40:05'),
(117, 17, 19, 'Moderator', 'hi', 0, '2026-02-11 11:41:55'),
(118, 17, 19, 'Moderator', 'asa', 0, '2026-02-11 11:42:46'),
(119, 16, 19, 'Moderator', 'hello', 0, '2026-02-11 11:42:59'),
(120, 16, 2, 'Driver', 'helllo', 1, '2026-02-11 11:43:24'),
(121, 17, 19, 'Moderator', 'sdsds', 0, '2026-02-11 11:44:08'),
(122, 16, 2, 'Driver', 'sddsdsdsdsdsdsdsdd', 1, '2026-02-11 11:44:40'),
(123, 18, 7, 'Traveller', 'assasas', 1, '2026-02-11 11:45:22'),
(124, 18, 19, 'Moderator', 'fds', 0, '2026-02-11 11:45:40'),
(125, 18, 7, 'Traveller', 'sssssss', 1, '2026-02-11 11:45:46'),
(126, 18, 7, 'Traveller', 'dsdsdssd', 0, '2026-02-11 11:56:25'),
(127, 19, 7, 'Traveller', 'asasasasas', 1, '2026-02-11 11:57:14'),
(128, 20, 2, 'Driver', 'dsd', 1, '2026-02-14 12:05:59'),
(129, 19, 7, 'Traveller', 'sdss', 1, '2026-02-14 12:06:28'),
(130, 20, 2, 'Driver', 'hoelo', 1, '2026-02-15 18:42:53'),
(131, 19, 7, 'Traveller', 'xccxcxcxcxcxcx', 1, '2026-02-15 19:18:50'),
(132, 19, 7, 'Traveller', 'sd', 1, '2026-02-15 19:19:09'),
(133, 21, 6, 'Traveller', 'hi', 1, '2026-02-15 19:22:04'),
(134, 21, 6, 'Traveller', 'sesesess', 1, '2026-02-15 19:57:39'),
(135, 22, 12, 'Driver', 'chiraaaaaaan', 1, '2026-02-15 21:08:39'),
(136, 22, 12, 'Driver', 'wijesekara', 1, '2026-02-16 08:08:35'),
(137, 22, 12, 'Driver', 'ertyui', 1, '2026-02-16 09:39:37'),
(138, 22, 12, 'Driver', 'asasaas', 1, '2026-02-16 13:24:43'),
(139, 20, 2, 'Driver', 'dgd', 1, '2026-02-17 08:08:21'),
(140, 20, 2, 'Driver', 'asgfgsa', 1, '2026-02-17 08:13:30'),
(141, 24, 2, 'Driver', 'welcome!', 1, '2026-02-17 09:31:55'),
(142, 24, 2, 'Driver', 'hii', 1, '2026-04-07 14:00:49'),
(143, 24, 19, 'Moderator', 'kohomda', 0, '2026-04-07 14:02:18'),
(144, 24, 19, 'Moderator', 'mokkd awla bn', 0, '2026-04-07 14:02:21'),
(145, 24, 2, 'Driver', 'mukuth na sudda', 1, '2026-04-07 14:02:35'),
(146, 24, 2, 'Driver', 'lma elama', 1, '2026-04-07 14:02:42'),
(147, 24, 19, 'Moderator', 'ok ok', 0, '2026-04-10 05:34:25'),
(148, 24, 19, 'Moderator', 'today', 0, '2026-04-10 05:38:23'),
(149, 26, 7, 'Traveller', 'good moring', 1, '2026-04-10 05:47:08'),
(150, 26, 19, 'Moderator', 'how can help you', 0, '2026-04-10 05:47:28'),
(151, 26, 19, 'Moderator', 'yes np', 0, '2026-04-10 06:08:47'),
(152, 27, 2, 'Driver', 'hi', 1, '2026-04-10 06:09:56'),
(153, 27, 19, 'Moderator', 'kojomda', 0, '2026-04-10 06:10:02'),
(154, 27, 2, 'Driver', 'hodadaii', 1, '2026-04-10 06:10:07'),
(155, 27, 19, 'Moderator', 'imhiiiiii', 0, '2026-04-10 06:10:13'),
(156, 27, 19, 'Moderator', 'ok', 0, '2026-04-10 06:13:20'),
(157, 27, 2, 'Driver', 'some', 1, '2026-04-10 06:13:29'),
(158, 27, 19, 'Moderator', 'ok', 0, '2026-04-10 06:16:33'),
(159, 27, 19, 'Moderator', 'gi', 0, '2026-04-10 06:41:18'),
(160, 27, 2, 'Driver', 'hello anuda', 0, '2026-04-15 03:02:00'),
(163, 29, 19, 'Moderator', 'ok', 0, '2026-04-15 06:17:10'),
(165, 29, 19, 'Moderator', 'hello', 0, '2026-04-15 06:17:49'),
(166, 29, 2, 'Driver', 'adssas', 0, '2026-04-15 06:17:57'),
(167, 30, 2, 'Driver', '123', 0, '2026-04-15 06:19:04'),
(168, 30, 2, 'Driver', 'jk', 0, '2026-04-15 06:19:35'),
(169, 31, 12, 'Driver', 'hello', 1, '2026-04-15 06:22:18'),
(170, 31, 19, 'Moderator', '..', 0, '2026-04-15 06:25:45'),
(171, 31, 19, 'Moderator', 'sesewe', 0, '2026-04-15 06:32:53'),
(172, 31, 19, 'Moderator', 'ewew', 0, '2026-04-15 06:32:58'),
(181, 31, 19, 'Moderator', 'sasm', 0, '2026-04-15 06:41:22'),
(184, 33, 2, 'Driver', 'hiii', 1, '2026-04-15 07:09:36'),
(186, 33, 2, 'Driver', 'hihihi\'', 1, '2026-04-15 07:09:53'),
(187, 33, 19, 'Moderator', '-0-0-', 0, '2026-04-15 07:10:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `help_messages`
--
ALTER TABLE `help_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat` (`chat_id`),
  ADD KEY `idx_sender` (`sender_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `help_messages`
--
ALTER TABLE `help_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `help_messages`
--
ALTER TABLE `help_messages`
  ADD CONSTRAINT `help_messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `help_chats` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
