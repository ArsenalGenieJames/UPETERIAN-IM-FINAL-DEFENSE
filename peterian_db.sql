-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 03:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peterian_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `user_id`, `post_id`, `content`, `timestamp`) VALUES
(7, 3, 7, 'choy choy uy ', '2024-11-23 12:32:14'),
(8, 3, 7, 'qwewqewq', '2024-11-25 13:07:26'),
(89, 3, 21, 'qwe', '2024-11-29 16:02:28'),
(90, 3, 22, 'qwewqe', '2024-11-29 16:02:33'),
(91, 3, 22, 'qwe', '2024-11-29 16:03:10'),
(92, 3, 23, 'qeqwewq', '2024-12-01 13:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

CREATE TABLE `feed` (
  `feed_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendships`
--

CREATE TABLE `friendships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('requested','accepted') DEFAULT 'requested'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friendships`
--

INSERT INTO `friendships` (`id`, `user_id`, `friend_id`, `status`) VALUES
(1, 4, 3, ''),
(2, 3, 4, ''),
(3, 3, 1, ''),
(4, 3, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `user_id`, `post_id`) VALUES
(35, 2, NULL),
(36, 2, NULL),
(25, 2, 7),
(27, 3, NULL),
(28, 3, NULL),
(30, 3, NULL),
(31, 3, NULL),
(32, 3, NULL),
(34, 3, NULL),
(38, 3, 7);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `type` enum('like','comment','follow') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `read_status` enum('unread','read') DEFAULT 'unread',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `from_user_id`, `type`, `related_id`, `message`, `read_status`, `timestamp`) VALUES
(1, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:26:46'),
(2, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:26:58'),
(3, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:27:38'),
(4, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:29:40'),
(5, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:29:46'),
(6, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:29:53'),
(7, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:31:50'),
(8, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:31:59'),
(9, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:32:03'),
(10, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:32:49'),
(11, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:33:06'),
(12, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:33:59'),
(13, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:34:06'),
(14, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:34:12'),
(15, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:34:31'),
(16, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:34:33'),
(17, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:34:45'),
(18, 3, 3, 'like', NULL, 'User 3 liked your post.', 'read', '2024-11-27 15:34:47'),
(19, 3, 3, '', NULL, 'User 3 unliked your post.', 'read', '2024-11-27 15:34:57'),
(20, 3, 3, 'comment', NULL, 'User  3 commented on your post.', 'read', '2024-11-27 16:51:10'),
(21, 3, 3, 'comment', NULL, 'User  3 commented on your post.', 'read', '2024-11-27 16:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `post_type` enum('text','image','video') DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `visibility` enum('public','friends_only') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `content`, `post_type`, `media_url`, `timestamp`, `visibility`) VALUES
(1, NULL, 'geniejamesarsenal', NULL, NULL, '2024-11-20 14:46:14', NULL),
(2, NULL, 'qwewqe', NULL, NULL, '2024-11-20 14:46:39', NULL),
(7, 2, 'ici days of the wilson ', 'video', 'uploads/posts/467647302_8928702437153129_6728492217147289341_n.mp4', '2024-11-22 15:13:00', NULL),
(20, 3, 'hi\r\n', 'text', NULL, '2024-11-29 09:14:18', NULL),
(21, 3, 'hapit najud front end na kulang \r\n', 'text', NULL, '2024-11-29 11:44:07', NULL),
(22, 3, 'oten', 'text', NULL, '2024-11-29 15:53:40', NULL),
(23, 3, 'hi guys ', 'text', NULL, '2024-12-01 13:05:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profileupdates`
--

CREATE TABLE `profileupdates` (
  `update_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `update_type` enum('name_change','profile_picture_change','bio_change') DEFAULT NULL,
  `update_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `friend_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `Name`, `Email`, `Address`, `username`, `password`, `profile_picture`, `bio`, `registration_date`, `friend_count`) VALUES
(1, 'geniejamesarsenal', 'geniejamesarsenal.202300349@gmail.com', NULL, NULL, '$2y$10$PAo20TVA2tBzvn7jMbzkPOYOrwNpOVas2QYUV2sND9zGsChZinJ9q', NULL, NULL, '2024-11-19 12:22:52', 0),
(2, 'geniejamesarsenal', 'arsenalgeniejames@gmail.com', 'Prk 1 Tambacan Iligan City', 'genie', '$2y$10$Bt5I55EYiTKIlfw87BzxxeNK5sIr1uL7nL.A3SZMF2mJC/0B170nW', 'uploads/profile_pictures/414666659_1336913343660164_4260487093164628197_n.jpg', NULL, '2024-11-19 12:29:28', 0),
(3, 'admin', 'admin@gmail.com', 'iligan city', 'admin', '$2y$10$dl27fR6ePSV4STgEE5rL7eRrrIosNFK2p.Tghv9pDNyxTmkSHlo7i', 'uploads/profile_pictures/profile_6749e3575d8bc.png', NULL, '2024-11-20 13:33:42', 0),
(4, 'ryan', 'ryanbalisi@gmail.com', 'luinab', 'ry', '$2y$10$5b/0p4Dz6FCddc1dAqEuyOFZSUqN/FK8RIdrsbOJyNl57QGSuIjF.', 'uploads/profile_pictures/302510198_461852989198980_5651353224387478655_n.png', NULL, '2024-11-27 12:08:50', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`feed_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `friendships`
--
ALTER TABLE `friendships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `from_user_id` (`from_user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profileupdates`
--
ALTER TABLE `profileupdates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `feed`
--
ALTER TABLE `feed`
  MODIFY `feed_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friendships`
--
ALTER TABLE `friendships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `profileupdates`
--
ALTER TABLE `profileupdates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `feed`
--
ALTER TABLE `feed`
  ADD CONSTRAINT `feed_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `feed_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `friendships`
--
ALTER TABLE `friendships`
  ADD CONSTRAINT `friendships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friendships_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `profileupdates`
--
ALTER TABLE `profileupdates`
  ADD CONSTRAINT `profileupdates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `shares_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
