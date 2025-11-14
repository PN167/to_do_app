-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 14, 2025 at 12:53 AM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
                         `id` int(11) NOT NULL,
                         `user_id` int(11) NOT NULL,
                         `title` varchar(255) NOT NULL,
                         `description` text DEFAULT NULL,
                         `status` enum('not_started','in_progress','completed') DEFAULT 'not_started',
                         `priority` enum('low','medium','high') DEFAULT 'medium',
                         `due_date` datetime DEFAULT NULL,
                         `created` datetime DEFAULT NULL,
                         `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `status`, `priority`, `due_date`, `created`, `modified`) VALUES
                                                                                                                           (6, 3, 'test', 'this is a test', 'not_started', 'medium', NULL, '2025-11-13 07:15:50', '2025-11-13 07:15:50'),
                                                                                                                           (7, 4, 'test', 'test', 'not_started', 'medium', NULL, '2025-11-14 00:14:41', '2025-11-14 00:14:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `email` varchar(255) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `nonce` varchar(255) DEFAULT NULL,
                         `nonce_expiry` datetime DEFAULT NULL,
                         `created` datetime DEFAULT NULL,
                         `modified` datetime DEFAULT NULL,
                         `api_token` varchar(255) DEFAULT NULL,
                         `api_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `api_token`, `api_token_expires`) VALUES
                                                                                                                                      (1, 'test@example.com', 'secret-password', NULL, NULL, '2025-11-13 17:16:20', '2025-11-13 17:16:20', NULL, NULL),
                                                                                                                                      (2, 'test@example.com', '$2y$10$E4eIAXQXbIgNHOW0jJ6sXOZc9KXrXbhPU5GJ8E5GhXBKW1YMCxCCm', NULL, NULL, '2025-11-13 17:39:08', '2025-11-13 17:39:08', NULL, NULL),
                                                                                                                                      (3, 'john.ng1607@gmail.com', '$2y$12$Hb/NBcAFyfLJ9JUQuyP6vO1mTHTSfzahQcXeK/g6AAgh0SFJ.Sao6', NULL, NULL, '2025-11-13 06:45:41', '2025-11-13 06:45:41', NULL, NULL),
                                                                                                                                      (4, 'test@gmail.com', '$2y$12$SXmDKnmQal5qTcMEWhd.U.BOgdoaTHKNhKHt9Givh5sJIm3d7Mw02', NULL, NULL, '2025-11-13 07:32:53', '2025-11-13 07:32:53', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
    ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
  ADD KEY `idx_api_token` (`api_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
    ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
