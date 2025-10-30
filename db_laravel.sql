-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 07:22 AM
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
-- Database: `db_laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disconnection_requests`
--

CREATE TABLE `disconnection_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `assigned_plumber_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','assigned','completed','cancelled') NOT NULL DEFAULT 'pending',
  `due_since` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lockout_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `user_agent`, `latitude`, `longitude`, `success`, `attempted_at`, `lockout_until`, `created_at`, `updated_at`) VALUES
(1, 'a@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', NULL, NULL, 0, '2025-10-28 20:12:17', NULL, '2025-10-28 20:12:17', '2025-10-28 20:12:17'),
(2, 'a@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', NULL, NULL, 0, '2025-10-28 20:12:24', NULL, '2025-10-28 20:12:24', '2025-10-28 20:12:24'),
(3, 'shan@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893330, 123.7231128, 0, '2025-10-28 20:16:40', NULL, '2025-10-28 20:16:40', '2025-10-28 20:16:40'),
(4, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893950, 123.7232801, 1, '2025-10-28 20:20:49', NULL, '2025-10-28 20:20:49', '2025-10-28 20:20:49'),
(5, '2025-0005', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893950, 123.7232801, 0, '2025-10-28 20:21:03', NULL, '2025-10-28 20:21:03', '2025-10-28 20:21:03'),
(6, '2025-0005', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893950, 123.7232801, 0, '2025-10-28 20:21:44', NULL, '2025-10-28 20:21:44', '2025-10-28 20:21:44'),
(7, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2892929, 123.7231039, 0, '2025-10-28 20:24:12', NULL, '2025-10-28 20:24:12', '2025-10-28 20:24:12'),
(8, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2892929, 123.7231039, 1, '2025-10-28 20:24:38', NULL, '2025-10-28 20:24:38', '2025-10-28 20:24:38'),
(9, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2892280, 123.7230749, 0, '2025-10-28 20:30:29', NULL, '2025-10-28 20:30:29', '2025-10-28 20:30:29'),
(10, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893992, 123.7232596, 1, '2025-10-28 20:30:43', NULL, '2025-10-28 20:30:43', '2025-10-28 20:30:43'),
(11, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893257, 123.7231745, 0, '2025-10-28 20:33:03', NULL, '2025-10-28 20:33:03', '2025-10-28 20:33:03'),
(12, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893257, 123.7231745, 0, '2025-10-28 20:33:10', NULL, '2025-10-28 20:33:10', '2025-10-28 20:33:10'),
(13, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893257, 123.7231745, 0, '2025-10-28 20:33:16', NULL, '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(14, '2025-0002', '127.0.0.1', NULL, 11.2893257, 123.7231745, 0, '2025-10-28 20:33:17', '2025-10-28 20:38:17', '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(15, 'macwas@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2892585, 123.7231417, 1, '2025-10-28 20:42:27', NULL, '2025-10-28 20:42:27', '2025-10-28 20:42:27'),
(16, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2892585, 123.7231417, 0, '2025-10-28 20:43:04', NULL, '2025-10-28 20:43:04', '2025-10-28 20:43:04'),
(17, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891000, 123.7230049, 0, '2025-10-28 20:55:28', NULL, '2025-10-28 20:55:28', '2025-10-28 20:55:28'),
(18, 'macwas@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891763, 123.7230534, 1, '2025-10-28 21:39:03', NULL, '2025-10-28 21:39:03', '2025-10-28 21:39:03'),
(19, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891011, 123.7230054, 1, '2025-10-28 21:40:41', NULL, '2025-10-28 21:40:41', '2025-10-28 21:40:41'),
(20, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891664, 123.7230506, 1, '2025-10-28 21:45:55', NULL, '2025-10-28 21:45:55', '2025-10-28 21:45:55'),
(21, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', NULL, NULL, 1, '2025-10-28 21:46:59', NULL, '2025-10-28 21:46:59', '2025-10-28 21:46:59'),
(22, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891664, 123.7230506, 1, '2025-10-28 21:48:56', NULL, '2025-10-28 21:48:56', '2025-10-28 21:48:56'),
(23, '2025-0002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2890939, 123.7230047, 0, '2025-10-28 21:49:59', NULL, '2025-10-28 21:49:59', '2025-10-28 21:49:59'),
(24, 'khamyrbautista@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891763, 123.7230534, 1, '2025-10-28 21:52:38', NULL, '2025-10-28 21:52:38', '2025-10-28 21:52:38'),
(25, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891619, 123.7230467, 1, '2025-10-28 21:56:34', NULL, '2025-10-28 21:56:34', '2025-10-28 21:56:34'),
(26, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891619, 123.7230467, 1, '2025-10-28 21:58:15', NULL, '2025-10-28 21:58:15', '2025-10-28 21:58:15'),
(27, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891704, 123.7230482, 1, '2025-10-28 22:01:05', NULL, '2025-10-28 22:01:05', '2025-10-28 22:01:05'),
(28, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891824, 123.7230663, 1, '2025-10-28 22:04:48', NULL, '2025-10-28 22:04:48', '2025-10-28 22:04:48'),
(29, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2891824, 123.7230663, 1, '2025-10-28 22:05:11', NULL, '2025-10-28 22:05:11', '2025-10-28 22:05:11'),
(30, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893535, 123.7232475, 1, '2025-10-28 22:06:39', NULL, '2025-10-28 22:06:39', '2025-10-28 22:06:39'),
(31, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893520, 123.7232418, 1, '2025-10-28 22:09:43', NULL, '2025-10-28 22:09:43', '2025-10-28 22:09:43'),
(33, 'jkr.grande@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 11.2893520, 123.7232418, 1, '2025-10-28 22:10:33', NULL, '2025-10-28 22:10:33', '2025-10-28 22:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `meter_readings`
--

CREATE TABLE `meter_readings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `plumber_id` bigint(20) UNSIGNED NOT NULL,
  `reading_date` date NOT NULL,
  `previous_reading` decimal(12,4) NOT NULL,
  `present_reading` decimal(12,4) NOT NULL,
  `used_cubic_meters` decimal(12,4) NOT NULL,
  `period` enum('mid','end') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2025_08_15_132954_add_portal_and_approved_to_users_table', 2),
(5, '2025_08_15_152552_add_portal_and_approved_to_users_table', 3),
(16, '0001_01_01_000000_create_users_table', 4),
(17, '0001_01_01_000001_create_cache_table', 4),
(18, '0001_01_01_000002_create_jobs_table', 4),
(19, '0001_01_01_000003_create_water_rates_table', 4),
(20, '0001_01_01_000004_create_water_connections_table', 4),
(21, '0001_01_01_000005_create_water_bills_table', 4),
(22, '0001_01_01_000006_create_payments_table', 4),
(23, '0001_01_01_000007_create_setup_requests_table', 4),
(24, '2025_09_04_000001_add_plain_password_to_users_table', 4),
(25, '2025_09_04_000002_create_meter_readings_table', 4),
(26, '2025_09_04_000003_add_late_fee_to_water_bills', 4),
(27, '2025_09_04_000004_create_disconnection_requests_table', 4),
(28, '2025_09_28_114514_add_customer_number_to_users_table', 5),
(29, '2025_09_28_115417_create_notifications_table', 6),
(30, '2025_10_26_051418_create_otp_verifications_table', 7),
(31, '2025_10_26_044036_create_sessions_table', 8),
(32, '2025_10_29_040024_create_login_attempts_table', 8),
(33, '2025_10_29_040238_create_login_attempts_table', 9),
(34, '2025_10_29_040315_create_sessions_table', 9),
(35, '2025_10_29_040420_add_user_agent_to_login_attempts_table', 10),
(36, '2025_10_29_041155_rename_successful_to_success_in_login_attempts_table', 11),
(37, '2025_10_29_120000_add_lat_lng_to_login_attempts_table', 12),
(38, '2025_10_29_045742_add_admin_created_to_users_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('f2614e03-0db7-4f84-b777-689375365ef5', 'App\\Notifications\\PlumberAssignedNotification', 'App\\Models\\User', 5, '{\"type\":\"plumber_assigned\",\"customer_id\":6,\"customer_name\":\"Test Customer\",\"customer_number\":\"2025-0003\",\"customer_address\":\"Customer Address, Test City\",\"customer_phone\":\"0987654321\",\"customer_email\":\"testcustomer2@example.com\",\"message\":\"You have been assigned to customer Test Customer (#2025-0003) for water connection setup.\",\"action_url\":\"\\/plumber\\/dashboard\"}', NULL, '2025-09-28 03:56:10', '2025-09-28 03:56:10');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'registration',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `user_id`, `otp_code`, `expires_at`, `is_used`, `type`, `created_at`, `updated_at`) VALUES
(1, 4, '645127', '2025-10-26 09:06:28', 1, 'login', '2025-10-26 01:05:47', '2025-10-26 01:06:28'),
(2, 4, '395245', '2025-10-26 01:16:28', 0, 'login', '2025-10-26 01:06:28', '2025-10-26 01:06:28'),
(9, 16, '781404', '2025-10-29 05:48:56', 1, 'login', '2025-10-28 21:46:28', '2025-10-28 21:48:56'),
(10, 16, '464125', '2025-10-29 05:49:31', 1, 'login', '2025-10-28 21:48:56', '2025-10-28 21:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('as@gmail.com', '$2y$12$0cx/aZ.8.v6qqV4i74SSHODNktlyj8U0i9eFRHi.D7yZWfk5JAFUK', '2025-09-29 00:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `water_bill_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `accountant_id` bigint(20) UNSIGNED NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setup_requests`
--

CREATE TABLE `setup_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_number` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `admin_created` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `role` enum('admin','plumber','accountant','customer') NOT NULL,
  `age` int(11) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `national_id` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `customer_number`, `first_name`, `last_name`, `email`, `email_verified_at`, `admin_created`, `password`, `plain_password`, `role`, `age`, `phone_number`, `photo`, `national_id`, `address`, `status`, `is_available`, `remember_token`, `created_at`, `updated_at`) VALUES
(4, '2025-0001', 'Admin', 'User', 'macwas@gmail.com', NULL, 0, '$2y$12$DteaXhwF.aSgZUzWCHrpBep4hZ/CiOF6s3W16D/N89zPEbL9B69f2', NULL, 'admin', 18, 'N/A', NULL, 'ADMIN-68d920db583b6', 'N/A', 'active', 1, 'rqTCJr9ac0vMqa94uYdd7Cz88CiIs6uasrFLQg8ynur8y7pIfJhMP4EDfQdL', '2025-09-28 03:49:47', '2025-09-28 03:49:47'),
(16, '2025-0002', 'Khamyr', 'Araño', 'khamyrbautista@gmail.com', '2025-10-28 21:49:31', 1, '$2y$12$4jXVjZQt7ojuGr0SZFt7t.sIrA18SX2XndYU51fTUqfDCBtp/aLpa', 'Deadmen13', 'customer', 23, '5465434634', NULL, 'AUTO-6901aa345e9d1', 'dsfadsfas', 'active', 0, NULL, '2025-10-28 21:46:28', '2025-10-28 21:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `water_bills`
--

CREATE TABLE `water_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `accountant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cubic_meters_used` decimal(10,2) NOT NULL,
  `rate_per_cubic_meter` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee_applied` tinyint(1) NOT NULL DEFAULT 0,
  `billing_month` date NOT NULL,
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `status` enum('unpaid','partially_paid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_receipt` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_connections`
--

CREATE TABLE `water_connections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `plumber_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `connection_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_rates`
--

CREATE TABLE `water_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rate_per_cubic_meter` decimal(10,2) NOT NULL,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `disconnection_requests`
--
ALTER TABLE `disconnection_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disconnection_requests_customer_id_foreign` (`customer_id`),
  ADD KEY `disconnection_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `disconnection_requests_assigned_plumber_id_foreign` (`assigned_plumber_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_attempts_email_index` (`email`);

--
-- Indexes for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meter_readings_customer_id_foreign` (`customer_id`),
  ADD KEY `meter_readings_plumber_id_foreign` (`plumber_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_verifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_water_bill_id_foreign` (`water_bill_id`),
  ADD KEY `payments_customer_id_foreign` (`customer_id`),
  ADD KEY `payments_accountant_id_foreign` (`accountant_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `setup_requests`
--
ALTER TABLE `setup_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setup_requests_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_national_id_unique` (`national_id`),
  ADD UNIQUE KEY `users_customer_number_unique` (`customer_number`);

--
-- Indexes for table `water_bills`
--
ALTER TABLE `water_bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `water_bills_customer_id_foreign` (`customer_id`),
  ADD KEY `water_bills_accountant_id_foreign` (`accountant_id`);

--
-- Indexes for table `water_connections`
--
ALTER TABLE `water_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `water_connections_customer_id_foreign` (`customer_id`),
  ADD KEY `water_connections_plumber_id_foreign` (`plumber_id`);

--
-- Indexes for table `water_rates`
--
ALTER TABLE `water_rates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `disconnection_requests`
--
ALTER TABLE `disconnection_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `meter_readings`
--
ALTER TABLE `meter_readings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setup_requests`
--
ALTER TABLE `setup_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `water_bills`
--
ALTER TABLE `water_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `water_connections`
--
ALTER TABLE `water_connections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `water_rates`
--
ALTER TABLE `water_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `disconnection_requests`
--
ALTER TABLE `disconnection_requests`
  ADD CONSTRAINT `disconnection_requests_assigned_plumber_id_foreign` FOREIGN KEY (`assigned_plumber_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `disconnection_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disconnection_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD CONSTRAINT `meter_readings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meter_readings_plumber_id_foreign` FOREIGN KEY (`plumber_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_accountant_id_foreign` FOREIGN KEY (`accountant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_water_bill_id_foreign` FOREIGN KEY (`water_bill_id`) REFERENCES `water_bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setup_requests`
--
ALTER TABLE `setup_requests`
  ADD CONSTRAINT `setup_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_bills`
--
ALTER TABLE `water_bills`
  ADD CONSTRAINT `water_bills_accountant_id_foreign` FOREIGN KEY (`accountant_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `water_bills_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_connections`
--
ALTER TABLE `water_connections`
  ADD CONSTRAINT `water_connections_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `water_connections_plumber_id_foreign` FOREIGN KEY (`plumber_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
