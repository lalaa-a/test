

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `support_problems` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` enum('booking','payment','account','complaint','other') NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `support_problems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_support_problems_user` (`user_id`),
  ADD KEY `idx_support_problems_status` (`status`),
  ADD KEY `idx_support_problems_resolved_by` (`resolved_by`);

R TABLE `support_problems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `support_problems`
  ADD CONSTRAINT `support_problems_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_problems_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

