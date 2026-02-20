-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 02:55 PM
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
-- Database: `subhomoy_alfa_network`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `news_date` text DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `name`, `news_date`, `photo`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Achievement 1', '2026-01-13', '1769438374_1.png', NULL, 1, '2026-01-26 09:09:34', '2026-01-26 09:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` enum('ma','s') DEFAULT 's' COMMENT 'ma = master admin, b = branch, s = staff',
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `remember_token` varchar(250) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `type`, `mobile`, `email`, `password`, `image`, `remember_token`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Master Admin', 'ma', '6289339520', 'admin@gmail.com', '$2y$10$TVEJg4pHvC3qabShMnfSUOmlbakklezJQROWP13wmgAy14XDVp3vq', '1769233854360_F_1525361933_wrAhkKnIAuYmw9suastDjy6ZDuPLld64.jpg', NULL, 1, NULL, '2026-01-24 00:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `heading1` varchar(250) DEFAULT NULL,
  `heading2` varchar(250) DEFAULT NULL,
  `section` int(11) NOT NULL DEFAULT 0,
  `banner_text` varchar(250) DEFAULT NULL,
  `banner_text2` varchar(250) DEFAULT NULL,
  `banner_link` varchar(250) DEFAULT NULL,
  `banner_image` varchar(250) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Agriculture', 1, NULL, '2026-02-09 09:04:03', '2026-02-09 09:04:03'),
(2, 'Finance', 1, NULL, '2026-02-09 09:04:18', '2026-02-09 09:04:39'),
(3, 'IT', 1, NULL, '2026-02-09 09:04:24', '2026-02-09 09:04:24');

-- --------------------------------------------------------

--
-- Table structure for table `committee_categories`
--

CREATE TABLE `committee_categories` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `short_description` longtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee_categories`
--

INSERT INTO `committee_categories` (`id`, `name`, `short_description`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Finance', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:33:18', '2026-02-20 07:47:53'),
(2, 'Membership', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:33:25', '2026-02-20 07:47:57'),
(3, 'Learning', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:34:09', '2026-02-20 07:48:02'),
(4, 'Social', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:34:17', '2026-02-20 07:48:06'),
(5, 'Engagement', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:34:24', '2026-02-20 07:48:11'),
(6, 'Woman', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, NULL, '2026-02-20 03:34:30', '2026-02-20 07:48:15');

-- --------------------------------------------------------

--
-- Table structure for table `cores`
--

CREATE TABLE `cores` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `photo` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cores`
--

INSERT INTO `cores` (`id`, `name`, `points`, `photo`, `description`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Core 1', 0, '1770826187_1.png', 'Short description goes here', NULL, 1, '2026-02-11 10:39:17', '2026-02-11 10:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `core_members`
--

CREATE TABLE `core_members` (
  `id` int(11) NOT NULL,
  `core_id` int(11) NOT NULL DEFAULT 0,
  `member_id` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `core_members`
--

INSERT INTO `core_members` (`id`, `core_id`, `member_id`, `status`, `created_at`, `updated_at`) VALUES
(5, 1, 5, 1, '2026-02-12 04:34:06', NULL),
(6, 1, 6, 1, '2026-02-12 04:34:06', NULL),
(7, 1, 4, 1, '2026-02-12 04:34:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `venue` text DEFAULT NULL,
  `event_date` text DEFAULT NULL,
  `seat_capacity` int(11) NOT NULL DEFAULT 0,
  `photo` text DEFAULT NULL,
  `video` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `venue`, `event_date`, `seat_capacity`, `photo`, `video`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Event 1', '<p>Event 1 Event 1</p>', 'Kolkata', '2026-01-31', 0, '1769521815_164170133620_venue_image.jpg', '1769521815_draw-video.mp4', 1, NULL, '2026-01-27 08:20:15', '2026-02-12 22:57:49');

-- --------------------------------------------------------

--
-- Table structure for table `event_questions`
--

CREATE TABLE `event_questions` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL DEFAULT 0,
  `event_question` longtext DEFAULT NULL,
  `event_answer_type` enum('INPUTBOX','TEXTAREA','RADIO','CHECKBOX','DROPDOWN','FILE') DEFAULT NULL,
  `event_answer_options` longtext DEFAULT NULL,
  `is_bydefault_show` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_questions`
--

INSERT INTO `event_questions` (`id`, `event_id`, `event_question`, `event_answer_type`, `event_answer_options`, `is_bydefault_show`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Are you attending or not ?', 'DROPDOWN', 'YES,NO', 1, 1, '2026-02-13 05:48:37', '2026-02-13 01:06:08'),
(2, 1, 'Any Questions/Remarks ?', 'INPUTBOX', NULL, 1, 1, '2026-02-13 05:48:38', '2026-02-13 01:06:08');

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(250) DEFAULT NULL,
  `site_phone` varchar(255) DEFAULT NULL,
  `site_mail` varchar(255) DEFAULT NULL,
  `system_email` varchar(250) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `timing` longtext DEFAULT NULL,
  `site_logo` varchar(255) DEFAULT NULL,
  `site_footer_logo` varchar(250) DEFAULT NULL,
  `site_favicon` varchar(250) DEFAULT NULL,
  `theme_color` varchar(250) DEFAULT NULL,
  `font_color` varchar(250) DEFAULT NULL,
  `twitter_profile` varchar(250) DEFAULT NULL,
  `facebook_profile` varchar(250) DEFAULT NULL,
  `instagram_profile` varchar(250) DEFAULT NULL,
  `linkedin_profile` varchar(250) DEFAULT NULL,
  `youtube_profile` varchar(250) DEFAULT NULL,
  `sms_authentication_key` varchar(250) DEFAULT NULL,
  `sms_sender_id` varchar(250) DEFAULT NULL,
  `sms_base_url` varchar(250) DEFAULT NULL,
  `from_email` varchar(250) DEFAULT NULL,
  `from_name` varchar(250) DEFAULT NULL,
  `smtp_host` varchar(250) DEFAULT NULL,
  `smtp_username` varchar(250) DEFAULT NULL,
  `smtp_password` varchar(250) DEFAULT NULL,
  `smtp_port` varchar(250) DEFAULT NULL,
  `email_template_forgot_password` longtext DEFAULT NULL,
  `email_template_change_password` longtext DEFAULT NULL,
  `email_template_failed_login` longtext DEFAULT NULL,
  `meta_title` longtext DEFAULT NULL,
  `meta_description` longtext DEFAULT NULL,
  `meta_keywords` longtext DEFAULT NULL,
  `document_size` int(11) NOT NULL DEFAULT 0,
  `photo_size` int(11) NOT NULL DEFAULT 0,
  `video_size` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `published` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `site_name`, `site_phone`, `site_mail`, `system_email`, `site_url`, `description`, `timing`, `site_logo`, `site_footer_logo`, `site_favicon`, `theme_color`, `font_color`, `twitter_profile`, `facebook_profile`, `instagram_profile`, `linkedin_profile`, `youtube_profile`, `sms_authentication_key`, `sms_sender_id`, `sms_base_url`, `from_email`, `from_name`, `smtp_host`, `smtp_username`, `smtp_password`, `smtp_port`, `email_template_forgot_password`, `email_template_change_password`, `email_template_failed_login`, `meta_title`, `meta_description`, `meta_keywords`, `document_size`, `photo_size`, `video_size`, `created_at`, `updated_at`, `published`) VALUES
(1, 'ALFA NETWORK', '+916289339520', 'subhomoysamanta1989@gmail.com', 'subhomoysamanta1989@gmail.com', 'http://localhost/a07fc4e3bc138dfdd94c397cf364e48b/', 'Test address', NULL, '1770457454ALFA-Logo-Yellow-scaled-2048x754.png', '1770457454ALFA-Logo-Yellow-scaled-2048x754.png', '1770457454ALFA-Logo-Yellow-scaled-2048x754.png', '#f1d30e', '#ffffff', NULL, NULL, NULL, '#', NULL, NULL, NULL, NULL, NULL, 'ALFA NETWORK', NULL, NULL, NULL, '587', NULL, NULL, NULL, NULL, NULL, NULL, 5000, 200, 10000, '0000-00-00 00:00:00', '2026-02-07 04:15:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE `industries` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `industries`
--

INSERT INTO `industries` (`id`, `name`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Agriculture', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(2, 'Apparel', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(3, 'Artist', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(4, 'Automobile', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(5, 'Beauty & Personal Care', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(6, 'Branding', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(7, 'Career Counselling', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(8, 'Chartered Accountant', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(9, 'Company Secretary', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(10, 'Construction', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(11, 'Digital Marketing', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(12, 'Education', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(13, 'EdTech', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(14, 'Electrical', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(15, 'Energy', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(16, 'Export', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(17, 'Fashion', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(18, 'Finance & Investment', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(19, 'Food & Beverages', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(20, 'Gems & Jewellery', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(21, 'Gifting', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(22, 'Healthcare', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(23, 'Home & Lifestyle', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(24, 'Homemaker', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(25, 'Hospitality', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(26, 'Interior Design', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(27, 'Investments', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(28, 'Lawyer', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(29, 'Logistics', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(30, 'Machinery', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(31, 'App Member Profile Details', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(32, 'Manufacturing', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(33, 'Marketing', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(34, 'NGO', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(35, 'Packaging', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(36, 'Plastics', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(37, 'Professional Services', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(38, 'Real Estate', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(39, 'Retail', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(40, 'Services', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(41, 'Startup', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(42, 'Steel & Metals', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(43, 'Tea Estates', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(44, 'Technology', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(45, 'Textiles', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(46, 'Travel', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(47, 'Wellness', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(48, 'Others', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`id`, `name`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adventure Trekking', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(2, 'Angel Investment', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(3, 'Art Collecting', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(4, 'Artificial Intelligence', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(5, 'Basketball', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(6, 'Capital Market', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(7, 'Chess', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(8, 'Cricket', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(9, 'Fitness', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(10, 'Football', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(11, 'Formula 1', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(12, 'Geopolitics', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(13, 'Golf', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(14, 'Lawn Tennis', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(15, 'Mahjong', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(16, 'Morning Runs', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(17, 'Music', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(18, 'Music Concerts', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(19, 'Nature', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(20, 'Nutrition', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(21, 'Photography', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(22, 'Pickleball', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(23, 'Poker', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(24, 'Reading', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(25, 'Squash', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(26, 'Table Tennis', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(27, 'Travel', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(28, 'Yoga', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21'),
(29, 'Others', 1, NULL, '2026-02-20 07:40:21', '2026-02-20 07:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `magazines`
--

CREATE TABLE `magazines` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `news_date` text DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `mag_file` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `magazines`
--

INSERT INTO `magazines` (`id`, `name`, `news_date`, `photo`, `description`, `mag_file`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Magazine 1', '2026-01-10', '1769438031_8129_500_320.png', 'test', '1769438031_sample.pdf', NULL, 1, '2026-01-26 09:03:51', '2026-01-26 09:03:51');

-- --------------------------------------------------------

--
-- Table structure for table `medias`
--

CREATE TABLE `medias` (
  `id` int(11) NOT NULL,
  `institute_id` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `media_file` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medias`
--

INSERT INTO `medias` (`id`, `institute_id`, `category_id`, `media_file`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(13, 2, 5, 'uploads/media/1769690286_697b54ae16d47.jpg', NULL, 1, '2026-01-29 12:38:06', '2026-01-29 13:33:45'),
(14, 2, 5, 'uploads/media/1769690286_697b54ae16f1b.png', NULL, 1, '2026-01-29 12:38:06', '2026-01-29 13:33:48'),
(15, 2, 5, 'uploads/media/1769690286_697b54ae17010.jpg', NULL, 1, '2026-01-29 12:38:06', '2026-01-29 13:33:50'),
(16, 2, 5, 'uploads/media/1769690286_697b54ae170eb.png', NULL, 1, '2026-01-29 12:38:06', '2026-01-29 13:33:52'),
(17, 2, 5, 'uploads/media/1769690286_697b54ae17221.jpg', '2026-01-29 13:43:10', 3, '2026-01-29 12:38:06', '2026-01-29 08:13:10'),
(18, 2, 5, 'uploads/media/1769690286_697b54ae1735d.png', '2026-01-29 13:41:46', 3, '2026-01-29 12:38:06', '2026-01-29 08:11:46'),
(19, 2, 5, 'uploads/media/1769690286_697b54ae1746a.jpg', NULL, 1, '2026-01-29 12:38:06', '2026-01-29 13:33:59'),
(20, 2, 5, 'uploads/media/1769694202_697b63fa0bfd0.png', NULL, 1, '2026-01-29 13:43:22', NULL),
(21, 2, 5, 'uploads/media/1769694202_697b63fa0c1ee.jpg', NULL, 1, '2026-01-29 13:43:22', NULL),
(22, 2, 5, 'uploads/media/1769694202_697b63fa0c2d2.jpg', NULL, 1, '2026-01-29 13:43:22', NULL),
(23, 1, 3, 'uploads/media/1769694270_697b643ec1b01.png', NULL, 1, '2026-01-29 13:44:30', NULL),
(24, 1, 3, 'uploads/media/1769694270_697b643ec1cf5.jpg', NULL, 1, '2026-01-29 13:44:30', NULL),
(25, 1, 3, 'uploads/media/1769694270_697b643ec1dce.png', NULL, 1, '2026-01-29 13:44:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_title` varchar(250) DEFAULT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `short_description` longtext DEFAULT NULL,
  `long_description` longtext DEFAULT NULL,
  `page_banner_image` varchar(250) DEFAULT NULL,
  `meta_title` longtext DEFAULT NULL,
  `meta_description` longtext DEFAULT NULL,
  `meta_keywords` longtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_title`, `slug`, `short_description`, `long_description`, `page_banner_image`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Privacy Policy', 'privacy-policy', NULL, '<p>What is Lorem Ipsum?</p><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p>Why do we use it?</p><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br>&nbsp;</p><p>Where does it come from?</p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p>Where can I get some?</p><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', NULL, NULL, NULL, NULL, 1, '2026-01-26 09:44:02', '2026-01-26 09:44:02'),
(2, 'Terms and Conditions', 'terms-and-conditions', NULL, '<p>What is Lorem Ipsum?</p><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p>Why do we use it?</p><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br>&nbsp;</p><p>Where does it come from?</p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p>Where can I get some?</p><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', NULL, NULL, NULL, NULL, 1, '2026-01-26 09:44:41', '2026-01-26 09:44:41'),
(3, 'About Us', 'about-us', NULL, '<p>What is Lorem Ipsum?</p><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p>Why do we use it?</p><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br>&nbsp;</p><p>Where does it come from?</p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p>Where can I get some?</p><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', NULL, NULL, NULL, NULL, 1, '2026-01-26 09:44:55', '2026-01-26 09:44:55'),
(4, 'Rule book', 'rule-book', NULL, '<p>What is Lorem Ipsum?</p><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p>Why do we use it?</p><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br>&nbsp;</p><p>Where does it come from?</p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p>Where can I get some?</p><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', NULL, NULL, NULL, NULL, 1, '2026-02-09 13:39:39', '2026-02-09 13:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `name` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `category_id`, `name`, `short_description`, `logo`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Privilege 1', 'Privilege 1 Privilege 1', '1770648707_164170132023_venue_image.jpg', NULL, 1, '2026-02-09 09:21:30', '2026-02-09 09:21:47'),
(3, 2, 'Privilege 2', 'Privilege 2 Privilege 2', '1770649035_164170133620_venue_image.jpg', NULL, 1, '2026-02-09 09:26:51', '2026-02-09 09:27:57'),
(4, 3, 'Privilege 3', 'Privilege 3 Privilege 3', '1770649054_164170130626_venue_image.jpg', NULL, 1, '2026-02-09 09:27:34', '2026-02-09 09:27:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '1=>normal member',
  `committee_category_id` text DEFAULT NULL,
  `committee_member_type` text DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT current_timestamp(),
  `phone` varchar(250) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `photo` varchar(250) DEFAULT NULL,
  `company_name` text DEFAULT NULL,
  `designation` text DEFAULT NULL,
  `dob` text DEFAULT NULL,
  `doj` text DEFAULT NULL,
  `doa` text DEFAULT NULL,
  `core_id` text DEFAULT NULL,
  `spouse_name` text DEFAULT NULL,
  `profession` text DEFAULT NULL,
  `alumni` text DEFAULT NULL,
  `industry_id` text DEFAULT NULL,
  `interest_id` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=>inactive,1=>active,3=>deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `type`, `committee_category_id`, `committee_member_type`, `name`, `email`, `email_verified_at`, `phone`, `password`, `remember_token`, `photo`, `company_name`, `designation`, `dob`, `doj`, `doa`, `core_id`, `spouse_name`, `profession`, `alumni`, `industry_id`, `interest_id`, `address`, `points`, `created_at`, `updated_at`, `status`) VALUES
(4, 1, '3', '1', 'Member 1', 'mem1@yopmail.com', '2026-02-09 14:02:16', '9999999999', NULL, NULL, '1770646137_1688301381netaji.jpg', 'Company Name', 'CEO', '1990-02-21', '2018', '0200-01-01', '1', 'Spouse Name', 'Profession', 'Alumni', '[\"31\",\"2\",\"6\",\"8\"]', '[\"5\",\"3\",\"7\",\"9\"]', 'Address', 0, '2026-02-09 08:32:16', '2026-02-20 13:49:11', 1),
(5, 1, '5', '0', 'Member 2', 'mem2@yopmail.com', '2026-02-09 14:06:47', '8888888888', NULL, NULL, '1770646007_1.png', 'Company Name', 'Accountant', NULL, '2021', '0200-01-01', '1', 'Spouse Name', 'Profession', 'Hobby', '[\"31\",\"2\",\"6\",\"8\"]', '[\"5\",\"3\",\"7\",\"9\"]', 'Address', 0, '2026-02-09 08:36:47', '2026-02-20 13:49:10', 1),
(6, 1, '2', '1', 'Member 3', 'mem3@yopmail.com', '2026-02-10 13:20:47', '7878787878', NULL, NULL, '1770729647_1688301381netaji.jpg', 'Company Name', 'Senior Developer', '1990-01-01', '2010', NULL, '1', NULL, 'Profession', 'Hobby', '[\"31\",\"2\",\"6\",\"8\"]', '[\"5\",\"3\",\"7\",\"9\"]', 'Address', 25, '2026-02-10 07:50:47', '2026-02-20 13:49:03', 1),
(8, 1, NULL, NULL, 'Member 4', 'mem4@yopmail.com', '2026-02-10 13:20:47', '7878787879', NULL, NULL, '1770729647_1688301381netaji.jpg', 'Company Name', 'Senior Developer', '1990-01-01', '2017', '0200-01-01', '1', 'Spouse Name', 'Profession', 'Hobby', '[\"31\",\"2\",\"6\",\"8\"]', '[\"3\",\"5\",\"7\",\"8\",\"9\"]', 'Address', 25, '2026-02-10 07:50:47', '2026-02-20 08:21:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_accesses`
--

CREATE TABLE `user_accesses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` varchar(250) DEFAULT '[]',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_accesses`
--

INSERT INTO `user_accesses` (`id`, `user_id`, `module_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\",\"17\",\"18\",\"19\",\"20\",\"21\",\"25\",\"26\",\"27\",\"28\",\"29\",\"30\",\"31\",\"32\",\"33\",\"34\",\"35\"]', 1, '2023-08-03 06:27:18', '2025-07-21 10:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `activity_id` int(11) NOT NULL,
  `user_email` varchar(250) DEFAULT NULL,
  `user_name` varchar(250) DEFAULT NULL,
  `user_type` enum('ADMIN','USER') DEFAULT NULL,
  `ip_address` varchar(250) DEFAULT NULL,
  `activity_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=>failed login,1=>success login,2=>logout',
  `activity_details` longtext DEFAULT NULL,
  `platform_type` enum('WEB','MOBILE','ANDROID','IOS') NOT NULL DEFAULT 'WEB',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`activity_id`, `user_email`, `user_name`, `user_type`, `ip_address`, `activity_type`, `activity_details`, `platform_type`, `created_at`, `updated_at`) VALUES
(3, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-24 05:51:58', '2026-01-24 05:51:58'),
(4, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-24 05:54:43', '2026-01-24 05:54:43'),
(5, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-24 05:55:38', '2026-01-24 05:55:38'),
(6, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-24 05:55:44', '2026-01-24 05:55:44'),
(7, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-24 05:55:47', '2026-01-24 05:55:47'),
(8, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-24 06:18:52', '2026-01-24 06:18:52'),
(9, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-26 03:25:50', '2026-01-26 03:25:50'),
(10, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-26 09:17:32', '2026-01-26 09:17:32'),
(11, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-26 13:40:34', '2026-01-26 13:40:34'),
(12, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-26 14:41:32', '2026-01-26 14:41:32'),
(13, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-27 13:44:31', '2026-01-27 13:44:31'),
(14, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-27 14:00:12', '2026-01-27 14:00:12'),
(15, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-28 13:07:18', '2026-01-28 13:07:18'),
(16, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-28 13:46:46', '2026-01-28 13:46:46'),
(17, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-29 12:23:24', '2026-01-29 12:23:24'),
(18, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-01-29 12:23:24', '2026-01-29 12:23:24'),
(19, 'pratimt@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-01-29 13:49:33', '2026-01-29 13:49:33'),
(20, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-07 09:41:47', '2026-02-07 09:41:47'),
(21, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-07 09:45:31', '2026-02-07 09:45:31'),
(22, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-07 09:47:17', '2026-02-07 09:47:17'),
(23, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-07 09:47:23', '2026-02-07 09:47:23'),
(24, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-09 13:37:51', '2026-02-09 13:37:51'),
(25, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-09 15:11:04', '2026-02-09 15:11:04'),
(26, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-10 13:18:06', '2026-02-10 13:18:06'),
(27, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-10 14:37:46', '2026-02-10 14:37:46'),
(28, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-11 16:07:55', '2026-02-11 16:07:55'),
(29, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-11 17:18:13', '2026-02-11 17:18:13'),
(30, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-12 03:49:56', '2026-02-12 03:49:56'),
(31, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-12 04:49:56', '2026-02-12 04:49:56'),
(32, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-12 05:54:17', '2026-02-12 05:54:17'),
(33, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-13 04:19:38', '2026-02-13 04:19:38'),
(34, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-13 08:01:42', '2026-02-13 08:01:42'),
(35, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-19 17:17:01', '2026-02-19 17:17:01'),
(36, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-19 17:17:09', '2026-02-19 17:17:09'),
(37, 'pratimt@gmail.com', 'Super Admin', 'ADMIN', '::1', 0, 'Invalid Email Or Password !!!', 'WEB', '2026-02-20 08:46:48', '2026-02-20 08:46:48'),
(38, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-20 08:46:55', '2026-02-20 08:46:55'),
(39, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 1, 'Login Success !!!', 'WEB', '2026-02-20 13:06:56', '2026-02-20 13:06:56'),
(40, 'admin@gmail.com', 'Master Admin', 'ADMIN', '::1', 2, 'You Are Successfully Logged Out !!!', 'WEB', '2026-02-20 13:55:32', '2026-02-20 13:55:32');

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL DEFAULT 0,
  `credited_points` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`id`, `member_id`, `credited_points`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 6, 10, 1, NULL, '2026-02-10 13:42:20', NULL),
(2, 6, 15, 1, NULL, '2026-02-10 13:42:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_reg_events`
--

CREATE TABLE `user_reg_events` (
  `id` int(11) NOT NULL,
  `userid` varchar(255) NOT NULL,
  `eventid` int(11) NOT NULL,
  `is_spouse` int(11) NOT NULL,
  `note` longtext NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `qrcode` varchar(250) DEFAULT NULL,
  `entry_timestamp` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `is_regret` tinyint(1) NOT NULL DEFAULT 0,
  `regret_reason` longtext DEFAULT NULL,
  `regret_timestamp` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_reg_event_answers`
--

CREATE TABLE `user_reg_event_answers` (
  `id` int(11) NOT NULL,
  `userid` varchar(250) DEFAULT NULL,
  `eventid` int(11) NOT NULL DEFAULT 0,
  `event_question_id` int(11) DEFAULT 0,
  `event_answer_type` enum('INPUTBOX','TEXTAREA','RADIO','CHECKBOX','DROPDOWN','FILE') DEFAULT NULL,
  `event_answer` longtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `committee_categories`
--
ALTER TABLE `committee_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cores`
--
ALTER TABLE `cores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_members`
--
ALTER TABLE `core_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_questions`
--
ALTER TABLE `event_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `industries`
--
ALTER TABLE `industries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `magazines`
--
ALTER TABLE `magazines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_accesses`
--
ALTER TABLE `user_accesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_reg_events`
--
ALTER TABLE `user_reg_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_reg_event_answers`
--
ALTER TABLE `user_reg_event_answers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `committee_categories`
--
ALTER TABLE `committee_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cores`
--
ALTER TABLE `cores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `core_members`
--
ALTER TABLE `core_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_questions`
--
ALTER TABLE `event_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `industries`
--
ALTER TABLE `industries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `interests`
--
ALTER TABLE `interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `magazines`
--
ALTER TABLE `magazines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medias`
--
ALTER TABLE `medias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_accesses`
--
ALTER TABLE `user_accesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_reg_events`
--
ALTER TABLE `user_reg_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_reg_event_answers`
--
ALTER TABLE `user_reg_event_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
