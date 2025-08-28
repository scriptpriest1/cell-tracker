-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 27, 2025 at 11:53 AM
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
-- Database: `cell_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) NOT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cells`
--

CREATE TABLE `cells` (
  `id` bigint(20) NOT NULL,
  `cell_name` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `church_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cells`
--

INSERT INTO `cells` (`id`, `cell_name`, `date_created`, `church_id`) VALUES
(1, 'Zion', '2025-07-29 06:11:10', 2),
(8, 'Heaven', '2025-07-31 10:52:48', 2),
(10, 'Auxano', '2025-07-31 11:12:45', 2),
(11, 'Dynamic', '2025-07-31 11:14:25', 1),
(28, 'The Way', '2025-08-01 16:37:11', 2),
(31, 'Marvelous', '2025-08-01 19:48:31', 2),
(32, 'Youths', '2025-08-01 19:55:54', 1),
(33, 'Glorious', '2025-08-01 23:25:02', 1),
(34, 'Light', '2025-08-01 23:25:54', 1),
(35, 'Men', '2025-08-04 00:25:58', 3),
(36, 'Haven', '2025-08-04 03:43:59', 3),
(38, 'Grace', '2024-06-15 00:47:58', 3),
(39, 'Glory', '2025-08-05 02:11:31', 3),
(40, 'Light', '2025-08-05 02:12:25', 3),
(41, 'Dominion', '2025-08-05 02:18:22', 3),
(43, 'Victory', '2025-08-05 02:26:18', 3),
(46, 'Destiny', '2025-08-05 02:33:14', 3),
(48, 'Gracious Ladies', '2025-08-05 02:39:01', 3),
(56, 'Marvelous Light', '2025-08-05 03:14:15', 3),
(57, 'Star', '2025-08-05 22:50:11', 2),
(58, 'Diamond', '2025-08-05 22:58:04', 2),
(59, 'Treasure', '2025-08-05 23:18:02', 2),
(60, 'Dynamic', '2025-08-05 23:28:38', 2),
(61, 'Messiah', '2025-08-07 15:16:50', 2),
(62, 'Supreme', '2025-08-09 14:56:57', 2),
(63, 'Favour', '2025-08-09 14:57:06', 2),
(64, 'Pace Setters', '2025-08-10 06:09:04', 2),
(65, 'Miracle', '2025-08-11 19:39:02', 2),
(66, 'Haven', '2025-08-14 05:09:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cell_members`
--

CREATE TABLE `cell_members` (
  `id` bigint(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob_month` varchar(50) NOT NULL,
  `dob_day` varchar(2) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `residential_address` varchar(100) NOT NULL,
  `foundation_sch_status` varchar(50) NOT NULL,
  `delg_in_cell` varchar(100) NOT NULL,
  `dept_in_church` varchar(100) NOT NULL,
  `date_joined_ministry` varchar(50) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `cell_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell_members`
--

INSERT INTO `cell_members` (`id`, `title`, `first_name`, `last_name`, `phone_number`, `email`, `dob_month`, `dob_day`, `occupation`, `residential_address`, `foundation_sch_status`, `delg_in_cell`, `dept_in_church`, `date_joined_ministry`, `date_added`, `cell_id`) VALUES
(19, 'sister', 'Joy', 'Obisike', '08167484342', 'joychidera97@gmail.com', 'dec', '4', 'Student', 'First bus-stop, Irete, Imo, Nigeria.', 'graduated', 'Secretary', 'Media', '', '2025-08-15 09:30:35', 28),
(20, 'brother', 'Michael', 'Chimereze', '08109085121', 'official.michaelchimereze@gmail.com', 'jul', '3', 'Web developer', 'Ubah, Orogwe, Owerri-west', 'graduated', 'Cell leader', 'Choir, Media', '', '2025-08-15 09:31:28', 28),
(21, 'sister', 'Grace', 'Nduchi', '09078246177', 'gracendu@gmail.com', 'aug', '14', 'Seamstress', 'Orogwe', 'graduated', 'Bible study class teacher', 'Ushering', '2018-06-01', '2025-08-15 09:34:29', 32),
(22, 'sister', 'Chinwendu', 'Chimereze', '07040332022', 'chinwenduchimereze@gmail.com', 'feb', '7', 'Church staff', 'Ubah, Orogwe, Owerri-west', 'graduated', 'Cell leader', 'Choir', '2020-04-15', '2025-08-15 09:37:36', 32),
(23, 'brother', 'Sampson', 'Ejiogu', '09154567876', '', 'feb', '2', 'Taxi driver', '', 'not-enrolled', '', '', '', '2025-08-15 15:40:36', 11),
(24, 'pastor', 'Saviour', 'Ndubuisi', '09078246177', 'pnd@gmail.com', 'jan', '20', 'Painter', 'Ihiagwa', 'graduated', 'Cell leader', 'PCF', '', '2024-06-15 14:45:16', 35);

-- --------------------------------------------------------

--
-- Table structure for table `cell_reports`
--

CREATE TABLE `cell_reports` (
  `id` bigint(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `week` int(11) DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `offering` decimal(10,0) NOT NULL,
  `date_generated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `editable` tinyint(1) NOT NULL,
  `cell_report_draft_id` bigint(20) DEFAULT NULL,
  `cell_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cell_report_attendees`
--

CREATE TABLE `cell_report_attendees` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `first_timer` tinyint(1) NOT NULL,
  `new_convert` tinyint(1) NOT NULL,
  `cell_member_id` bigint(20) DEFAULT NULL,
  `cell_report_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell_report_attendees`
--

INSERT INTO `cell_report_attendees` (`id`, `name`, `first_timer`, `new_convert`, `cell_member_id`, `cell_report_id`) VALUES
(5, 'Favour', 1, 0, NULL, NULL),
(6, 'John', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cell_report_drafts`
--

CREATE TABLE `cell_report_drafts` (
  `id` bigint(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `week` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_generated` timestamp NOT NULL DEFAULT current_timestamp(),
  `cell_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell_report_drafts`
--

INSERT INTO `cell_report_drafts` (`id`, `type`, `week`, `description`, `status`, `date_generated`, `cell_id`) VALUES
(143, 'meeting', 2, 'Bible Study Class 1', 'pending', '2024-07-08 03:09:03', 32),
(144, 'meeting', 1, 'Prayer and Planning', 'pending', '2024-07-01 03:15:12', 32),
(145, 'meeting', 3, 'Bible Study Class 2', 'pending', '2024-07-17 03:15:24', 32),
(146, 'meeting', 4, 'Cell Outreach', 'pending', '2024-07-31 03:15:32', 32),
(147, 'meeting', 1, 'Prayer and Planning', 'pending', '2024-08-01 03:15:49', 32),
(148, 'meeting', 4, 'Cell Outreach', 'pending', '2025-08-23 20:39:33', 32),
(149, 'meeting', 4, 'Cell Outreach', 'pending', '2025-08-23 20:41:13', 35),
(150, 'meeting', 4, 'Cell Outreach', 'pending', '2025-08-24 20:52:21', 35),
(151, 'meeting', 4, 'Cell Outreach', 'pending', '2025-08-26 13:58:09', 32);

-- --------------------------------------------------------

--
-- Table structure for table `churches`
--

CREATE TABLE `churches` (
  `id` bigint(20) NOT NULL,
  `church_name` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `churches`
--

INSERT INTO `churches` (`id`, `church_name`, `date_created`) VALUES
(1, 'Orogwe', '2025-07-25 15:23:47'),
(2, 'Ohii', '2025-07-25 15:23:53'),
(3, 'Eziobodo', '2025-07-31 12:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `date_created`) VALUES
(1, 'Central Church', '2025-07-29 06:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `cell_id` bigint(20) DEFAULT NULL,
  `church_id` bigint(20) DEFAULT NULL,
  `group_id` bigint(20) DEFAULT NULL,
  `cell_role` varchar(50) NOT NULL,
  `church_role` varchar(50) NOT NULL,
  `group_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `user_login`, `password`, `phone_number`, `date_created`, `cell_id`, `church_id`, `group_id`, `cell_role`, `church_role`, `group_role`) VALUES
(2, 'Chinwendu', 'Chimereze', 'me@wendy.com', 'password', '', '2025-07-25 15:53:41', 32, 1, NULL, 'leader', 'pfcc', ''),
(4, 'Michael', 'King', 'sparrowdeck@gmail.com', 'sparrow', '', '2025-07-30 11:21:30', 28, 2, NULL, 'leader', '', ''),
(8, 'Ugo', 'Amadi', 'ugoamadi@gmail.com', 'password', '', '2025-07-31 12:20:33', 35, 3, NULL, 'executive', '', ''),
(19, 'Saviour', 'Ndubuisi', 'pnd@gmail.com', '$2y$10$hJCgtuBJH78ztX597sTmceQSOU.mm9rV/1jRAX.esOK', '09077656776', '2025-08-05 02:16:13', 35, NULL, NULL, 'leader', '', ''),
(20, 'Hezel', 'Macauley', 'hezelm@gmail.com', 'password', '09077656776', '2025-08-05 23:18:02', 59, NULL, NULL, 'leader', '', ''),
(22, 'Favour', 'Dominic', 'favdominic@gmail.com', 'password', '08045828913', '2025-08-07 15:16:50', 61, NULL, NULL, 'executive', '', ''),
(24, 'Grace', 'Nduchi', 'gracendu@gmail.com', '$2y$10$CGBbzCeZ0eet/oFQIdBYXeVZgySLMJbW0mI7AD/X3e0', '09078246177', '2025-08-08 15:37:48', 32, NULL, NULL, 'executive', '', ''),
(25, 'Favour', 'Nwoko', 'favenwoko@gmail', '$2y$10$XcTq3RAjWrNgruYMLseOfek1zMOErNU8dp/AZRTYmYi', '09078246177', '2025-08-10 06:13:31', 1, NULL, NULL, 'executive', '', ''),
(27, 'Ugochukwu', 'Kingsley', 'ugokingsley@gmail.com', '$2y$10$zbQ9jT07bFC70/x7rtjXvunPbdtkQtWiSfevBk0bnS6', '09085275325', '2025-08-15 15:35:28', 11, NULL, NULL, 'leader', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cells`
--
ALTER TABLE `cells`
  ADD PRIMARY KEY (`id`),
  ADD KEY `church_id` (`church_id`);

--
-- Indexes for table `cell_members`
--
ALTER TABLE `cell_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cell_id` (`cell_id`);

--
-- Indexes for table `cell_reports`
--
ALTER TABLE `cell_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cell_report_draft_id` (`cell_report_draft_id`),
  ADD KEY `cell_id` (`cell_id`);

--
-- Indexes for table `cell_report_attendees`
--
ALTER TABLE `cell_report_attendees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cell_member_id` (`cell_member_id`),
  ADD KEY `cell_report_id` (`cell_report_id`);

--
-- Indexes for table `cell_report_drafts`
--
ALTER TABLE `cell_report_drafts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cell_id` (`cell_id`);

--
-- Indexes for table `churches`
--
ALTER TABLE `churches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`user_login`),
  ADD KEY `users_ibfk_1` (`church_id`),
  ADD KEY `cell_id` (`cell_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cells`
--
ALTER TABLE `cells`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `cell_members`
--
ALTER TABLE `cell_members`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `cell_reports`
--
ALTER TABLE `cell_reports`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cell_report_attendees`
--
ALTER TABLE `cell_report_attendees`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cell_report_drafts`
--
ALTER TABLE `cell_report_drafts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `churches`
--
ALTER TABLE `churches`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cells`
--
ALTER TABLE `cells`
  ADD CONSTRAINT `cells_ibfk_1` FOREIGN KEY (`church_id`) REFERENCES `churches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cell_members`
--
ALTER TABLE `cell_members`
  ADD CONSTRAINT `cell_members_ibfk_1` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cell_reports`
--
ALTER TABLE `cell_reports`
  ADD CONSTRAINT `cell_reports_ibfk_1` FOREIGN KEY (`cell_report_draft_id`) REFERENCES `cell_report_drafts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cell_reports_ibfk_2` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`);

--
-- Constraints for table `cell_report_attendees`
--
ALTER TABLE `cell_report_attendees`
  ADD CONSTRAINT `cell_report_attendees_ibfk_1` FOREIGN KEY (`cell_member_id`) REFERENCES `cell_members` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cell_report_attendees_ibfk_2` FOREIGN KEY (`cell_report_id`) REFERENCES `cell_reports` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cell_report_drafts`
--
ALTER TABLE `cell_report_drafts`
  ADD CONSTRAINT `cell_report_drafts_ibfk_1` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`church_id`) REFERENCES `churches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
