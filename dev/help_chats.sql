
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `help_chats` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_type` varchar(50) NOT NULL DEFAULT 'Traveller' COMMENT 'Traveller, Guide, Driver',
  `status` enum('Open','Assigned','Closed') NOT NULL DEFAULT 'Open',
  `assigned_moderator_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `help_chats` (`id`, `user_id`, `user_type`, `status`, `assigned_moderator_id`, `created_at`, `updated_at`) VALUES
(1, 2, 'Traveller', 'Closed', 19, '2026-02-07 12:49:45', '2026-02-09 04:55:55'),
(2, 2, 'Driver', 'Closed', 19, '2026-02-07 12:56:54', '2026-02-09 04:55:59'),
(3, 19, 'Moderator', 'Closed', 19, '2026-02-09 04:53:28', '2026-02-09 04:56:03'),
(4, 19, 'Moderator', 'Closed', 19, '2026-02-09 04:57:06', '2026-02-09 05:03:50'),
(5, 2, 'Driver', 'Closed', 19, '2026-02-09 04:57:36', '2026-02-09 05:15:39'),
(6, 19, 'Moderator', 'Closed', 19, '2026-02-09 08:21:38', '2026-02-10 08:03:48'),
(7, 2, 'Driver', 'Closed', 19, '2026-02-09 08:21:57', '2026-02-10 11:13:41'),
(8, 7, 'Traveller', 'Closed', 19, '2026-02-10 07:51:53', '2026-02-10 11:13:37'),
(9, 19, 'Moderator', 'Closed', 19, '2026-02-10 08:19:54', '2026-02-10 11:13:25'),
(10, 17, 'Guide', 'Closed', 19, '2026-02-10 10:17:14', '2026-02-10 11:13:33'),
(11, 7, 'Traveller', 'Closed', 19, '2026-02-10 11:14:26', '2026-02-11 11:07:24'),
(12, 26, 'Business_manager', 'Closed', 19, '2026-02-11 08:08:36', '2026-02-11 08:26:45'),
(13, 19, 'Moderator', 'Closed', 19, '2026-02-11 08:23:07', '2026-02-11 08:26:52'),
(14, 2, 'Driver', 'Closed', 19, '2026-02-11 08:24:24', '2026-02-11 08:27:01'),
(15, 19, 'Moderator', 'Closed', 19, '2026-02-11 09:30:28', '2026-02-11 11:07:20'),
(16, 2, 'Driver', 'Closed', 19, '2026-02-11 09:30:47', '2026-02-11 11:54:23'),
(17, 19, 'Moderator', 'Closed', 19, '2026-02-11 11:25:51', '2026-02-11 11:54:17'),
(18, 7, 'Traveller', 'Closed', 19, '2026-02-11 11:45:19', '2026-02-11 11:54:19'),
(19, 7, 'Traveller', 'Closed', 19, '2026-02-11 11:57:11', '2026-02-15 19:38:32'),
(20, 2, 'Driver', 'Closed', 19, '2026-02-14 11:41:51', '2026-02-17 09:29:13'),
(21, 6, 'Traveller', 'Closed', 19, '2026-02-15 19:22:01', '2026-02-17 09:29:01'),
(22, 12, 'Driver', 'Closed', 19, '2026-02-15 21:08:34', '2026-02-17 09:29:18'),
(23, 7, 'Traveller', 'Closed', 19, '2026-02-17 04:56:26', '2026-02-17 09:29:24'),
(24, 2, 'Driver', 'Closed', 19, '2026-02-17 09:31:50', '2026-04-10 05:43:49'),
(25, 19, 'Moderator', 'Closed', 19, '2026-04-10 05:36:30', '2026-04-10 05:43:53'),
(26, 7, 'Traveller', 'Closed', 19, '2026-04-10 05:47:02', '2026-04-15 06:10:32'),
(27, 2, 'Driver', 'Closed', 19, '2026-04-10 06:09:54', '2026-04-15 05:37:43'),
(29, 2, 'Driver', 'Closed', 19, '2026-04-15 06:16:18', '2026-04-15 06:18:48'),
(30, 2, 'Driver', 'Closed', 19, '2026-04-15 06:19:00', '2026-04-15 06:23:11'),
(31, 12, 'Driver', 'Closed', 19, '2026-04-15 06:22:16', '2026-04-15 06:41:32'),
(33, 2, 'Driver', 'Closed', 19, '2026-04-15 07:09:20', '2026-04-15 07:29:55');


ALTER TABLE `help_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_moderator` (`assigned_moderator_id`);


ALTER TABLE `help_chats`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

