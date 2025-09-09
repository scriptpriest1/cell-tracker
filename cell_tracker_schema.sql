-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 10:06 PM
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
(28, 'The Way', '2025-08-01 16:37:11', 2),
(32, 'Youths', '2025-08-01 19:55:54', 1);

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
(26, 'sister', 'Favour', 'Uyork', '09128958294', 'favouruyork@gmail.com', 'jul', '9', 'Church staff', 'Irete', 'graduated', '', 'Choir', '', '2025-08-29 00:30:57', 28),
(27, 'sister', 'Chinwendu', 'Chimereze', '08165515314', '', 'feb', '7', 'Church staff', 'Orogwe', 'graduated', 'Cell leader', 'Choir', '', '2025-08-29 00:59:19', 32),
(28, 'brother', 'David', 'Israel', '09032331337', 'davidisrael@gmail.com', '', '', '', 'Irete', 'not-enrolled', '', '', '', '2025-08-29 16:04:32', 28),
(29, 'brother', 'Elijah', 'Davidson', '07063812100', '', '', '', '', 'Ohii', 'not-enrolled', '', '', '', '2025-08-29 16:04:45', 28),
(30, 'brother', 'Steven', 'Richard', '', '', '', '', '', 'Ohii', 'not-enrolled', '', '', '', '2025-08-29 16:05:06', 28),
(31, 'brother', 'Kelly', 'Amzo', '', '', '', '', '', 'Orogwe', 'not-enrolled', '', '', '', '2025-08-29 16:05:18', 28),
(33, 'brother', 'Great', 'Chimereze', '0706381950', 'greatking549@gmail.com', 'may', '9', 'Student', 'Orogwe', 'graduated', '', 'Choir, Media', '2019-04-01', '2025-09-01 15:07:52', 32),
(34, 'brother', 'Steven', 'Obiagu', '07063819501', 'stevagu@gmail.com', '', '', '', 'Orogwe', 'enrolled', '', '', '', '2025-09-01 16:37:28', 32);

-- --------------------------------------------------------

--
-- Table structure for table `cell_reports`
--

CREATE TABLE `cell_reports` (
  `id` bigint(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `week` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `attendance` int(11) DEFAULT NULL,
  `first_timers` int(11) DEFAULT NULL,
  `new_converts` int(11) DEFAULT NULL,
  `outreach_kind` varchar(100) DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `offering` decimal(10,0) NOT NULL,
  `date_generated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expiry_date` timestamp NULL DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
  `expiry_date` timestamp NULL DEFAULT NULL,
  `cell_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell_report_drafts`
--

INSERT INTO `cell_report_drafts` (`id`, `type`, `week`, `description`, `status`, `date_generated`, `expiry_date`, `cell_id`) VALUES
(313, 'meeting', 1, 'Prayer and Planning', 'pending', '2025-08-31 23:00:00', '2025-09-07 22:59:59', 32),
(314, 'meeting', 1, 'Prayer and Planning', 'pending', '2025-08-31 23:00:00', '2025-09-07 22:59:59', 28);

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
  `password` varchar(255) NOT NULL,
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
(2, 'Chinwendu', 'Chimereze', 'me@wendy.com', '$2y$10$yiFsgAhpWbuRPNldLRpB3eAUWhUY43gi3Ds1mh9sB7VUzoeABB08G', '', '2025-07-25 15:53:41', 32, 1, NULL, 'leader', 'pfcc', ''),
(4, 'Michael', 'King', 'sparrowdeck@gmail.com', '$2y$10$Zd1JPi/dwP9CR6RclIkqeulkj/ZxvruaspS8izSRo9MCA0voToZwS', '', '2025-07-30 11:21:30', 28, 2, NULL, 'leader', '', '');

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `cell_members`
--
ALTER TABLE `cell_members`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `cell_reports`
--
ALTER TABLE `cell_reports`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `cell_report_attendees`
--
ALTER TABLE `cell_report_attendees`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `cell_report_drafts`
--
ALTER TABLE `cell_report_drafts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
