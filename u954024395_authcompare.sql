-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 02, 2026 at 09:08 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u954024395_authcompare`
--

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

CREATE TABLE `attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `attempt_no` tinyint(3) UNSIGNED NOT NULL,
  `success` tinyint(1) NOT NULL,
  `time_ms` int(10) UNSIGNED NOT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`id`, `session_id`, `attempt_no`, `success`, `time_ms`, `error_code`, `created_at`) VALUES
(1, 1, 1, 1, 5773, NULL, '2025-10-20 21:11:30'),
(2, 2, 1, 0, 5140, NULL, '2025-10-20 21:12:00'),
(3, 2, 2, 1, 5251, NULL, '2025-10-20 21:12:06'),
(4, 3, 1, 1, 27816, NULL, '2025-10-23 09:39:58'),
(5, 4, 1, 1, 40276, NULL, '2025-10-23 12:53:42'),
(6, 5, 1, 1, 15754, NULL, '2025-10-23 13:11:26'),
(7, 6, 1, 1, 13596, NULL, '2025-10-23 13:31:59'),
(8, 7, 1, 1, 7876, NULL, '2025-10-23 13:39:16'),
(9, 8, 1, 1, 17277, NULL, '2025-10-23 14:01:30'),
(10, 9, 1, 1, 9435, NULL, '2025-10-23 14:11:06'),
(11, 11, 1, 0, 39106, NULL, '2025-10-23 14:36:39'),
(12, 11, 2, 0, 25040, NULL, '2025-10-23 14:37:04'),
(13, 13, 1, 1, 38720, NULL, '2025-10-23 14:46:49'),
(14, 15, 1, 0, 7375, NULL, '2025-10-23 15:04:44'),
(15, 15, 2, 0, 4496, NULL, '2025-10-23 15:05:01'),
(16, 16, 1, 1, 6785, NULL, '2025-10-23 15:21:41'),
(17, 17, 1, 1, 26546, NULL, '2025-10-23 16:33:26'),
(18, 18, 1, 1, 11885, NULL, '2025-10-23 18:01:25'),
(19, 19, 1, 1, 18784, NULL, '2025-10-23 20:46:10'),
(20, 20, 1, 1, 12920, NULL, '2025-10-23 20:48:12'),
(21, 21, 1, 1, 11368, NULL, '2025-10-25 09:21:00'),
(22, 23, 1, 1, 19362, NULL, '2025-10-25 10:19:31'),
(23, 24, 1, 1, 17111, NULL, '2025-10-25 10:20:43'),
(24, 25, 1, 1, 14215, NULL, '2025-10-25 16:25:11'),
(25, 26, 1, 1, 15005, NULL, '2025-10-25 16:31:03'),
(26, 27, 1, 0, 18871, NULL, '2025-10-27 09:12:14'),
(27, 27, 2, 1, 23308, NULL, '2025-10-27 09:13:19'),
(28, 28, 1, 0, 19687, NULL, '2025-10-27 09:15:27'),
(29, 28, 2, 0, 10485, NULL, '2025-10-27 09:16:26'),
(30, 28, 3, 0, 7223, NULL, '2025-10-27 09:17:01'),
(31, 12, 1, 1, 5950, NULL, '2025-10-27 09:25:05'),
(32, 11, 3, 0, 13252, NULL, '2025-10-27 09:25:43'),
(33, 30, 1, 1, 4761, NULL, '2025-10-27 10:16:44'),
(34, 32, 1, 0, 9761, NULL, '2025-10-27 10:41:10'),
(35, 32, 2, 1, 13896, NULL, '2025-10-27 10:41:48'),
(36, 14, 1, 0, 25026, NULL, '2025-10-27 10:54:02'),
(37, 33, 1, 0, 12026, NULL, '2025-10-27 10:54:17'),
(38, 33, 2, 1, 18247, NULL, '2025-10-27 10:54:36'),
(39, 14, 2, 0, 21733, NULL, '2025-10-27 10:54:43'),
(40, 14, 3, 0, 14984, NULL, '2025-10-27 10:55:34'),
(41, 10, 1, 0, 52050, NULL, '2025-10-27 10:58:29'),
(42, 10, 2, 0, 50737, NULL, '2025-10-27 10:59:22'),
(43, 10, 3, 1, 8615, NULL, '2025-10-27 10:59:31'),
(44, 34, 1, 1, 11063, NULL, '2025-10-27 11:07:11'),
(45, 35, 1, 1, 4615, NULL, '2025-10-27 11:38:14'),
(46, 37, 1, 0, 7461, NULL, '2025-11-30 19:41:50'),
(47, 37, 2, 0, 5568, NULL, '2025-11-30 19:41:56'),
(48, 37, 3, 0, 10145, NULL, '2025-11-30 19:42:07'),
(49, 36, 1, 1, 6414, NULL, '2025-11-30 19:45:59');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `id` int(10) UNSIGNED NOT NULL,
  `participant_id` int(10) UNSIGNED NOT NULL,
  `experiment_id` int(10) UNSIGNED NOT NULL,
  `type` enum('password','pattern') NOT NULL,
  `verifier_hash` varchar(255) NOT NULL,
  `metrics_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`id`, `participant_id`, `experiment_id`, `type`, `verifier_hash`, `metrics_json`, `created_at`) VALUES
(1, 2, 1, 'password', '$argon2id$v=19$m=65536,t=4,p=1$Vno1Nzg3ZVJ3andZcGZZQg$mdaBdQzieHoVJx/To1DGsEeAIlsIkNjqX7NXXlb1f/w', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":17590}', '2025-10-20 21:11:24'),
(2, 1, 2, 'pattern', '40e52055c05550e19f46378bf34a907755bafb60b045588440b179d1676774e7', '{\"nodes\":5,\"create_time_ms\":6796}', '2025-10-20 21:11:55'),
(3, 21, 21, 'pattern', '41202490ed05cbc40fdefc275bee0d53b9177f8f58ff3440f74b65f37d15b68a', '{\"nodes\":4,\"create_time_ms\":12197}', '2025-10-23 09:39:28'),
(4, 16, 17, 'password', '$argon2id$v=19$m=65536,t=4,p=1$MW1pODlIZkNlUlFoNWRRcg$xNGVYgLpu6om99a57K5JzG77BpwZ5XVUHA9qDgyY6rM', '{\"len\":8,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":36610}', '2025-10-23 12:53:00'),
(5, 34, 35, 'pattern', '95f86b8021faf130d23996e4faea014a5a8ca7e23d74ebe1b9241e12d6ddc492', '{\"nodes\":8,\"create_time_ms\":57823}', '2025-10-23 13:11:09'),
(6, 14, 15, 'password', '$argon2id$v=19$m=65536,t=4,p=1$eUVzSlg5anVxeXVjemFWSA$VothrHGSptaDoDK2eNvVc+CiNvMWCY3gEBeeol7KBEg', '{\"len\":13,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":24802}', '2025-10-23 13:31:45'),
(7, 32, 33, 'pattern', '1f382b91ee9f8fab7f97a6072763077575534fb21ce5db21e26f39ed0976b4ea', '{\"nodes\":7,\"create_time_ms\":12060}', '2025-10-23 13:38:58'),
(8, 24, 24, 'pattern', '5859d60486e7737e9b2db31b0750010d01fc0922cc234b2d611b66c3557c04be', '{\"nodes\":9,\"create_time_ms\":40496}', '2025-10-23 14:01:13'),
(9, 6, 6, 'password', '$argon2id$v=19$m=65536,t=4,p=1$b0RYUnprck1SNldTUmpzVw$5h3TChnORNj6K9nZFICp8Za17LCyM/U3UZmag7Tslag', '{\"len\":12,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":150511}', '2025-10-23 14:10:56'),
(10, 27, 28, 'pattern', 'a4efddfbcf1b3d047230e111edce568a61d3549bf4281606260ca8b8db9de6f8', '{\"nodes\":9,\"create_time_ms\":29217}', '2025-10-23 14:35:59'),
(11, 15, 16, 'password', '$argon2id$v=19$m=65536,t=4,p=1$UUE0WjAxd09pT2lGQ3JOeA$o3/jaAY4Z4Mn0W4DfY2kECNZz63AQzD12dyGtbzozqc', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":43893}', '2025-10-23 14:46:10'),
(12, 13, 14, 'password', '$argon2id$v=19$m=65536,t=4,p=1$dWJLZWZyeDNzaW95ZjhUeA$kG8xSdLh042bKv74QcBXIdnwqPUujvezYDVfWJPgXik', '{\"len\":12,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":21570}', '2025-10-23 15:02:54'),
(13, 31, 32, 'pattern', '1f382b91ee9f8fab7f97a6072763077575534fb21ce5db21e26f39ed0976b4ea', '{\"nodes\":7,\"create_time_ms\":9642}', '2025-10-23 15:04:36'),
(14, 33, 34, 'pattern', 'ba1ec0bcd857d3942fb6c6581ddd8670e4be918bc6739ddf213426ebef084c52', '{\"nodes\":7,\"create_time_ms\":6009}', '2025-10-23 15:21:26'),
(15, 17, 18, 'password', '$argon2id$v=19$m=65536,t=4,p=1$UWhPc2UuN1Fzek1zaFpULw$tCnDgNx90ymPxSrcsksiCOtfWn07mLzMSE6iKT4JnFA', '{\"len\":17,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":57190}', '2025-10-23 16:28:49'),
(16, 7, 7, 'password', '$argon2id$v=19$m=65536,t=4,p=1$VWhQb2ZxbGwxRU56T2Y0Mg$EUnytD1F3d9+YkgDULlKgrhHERE03Zk9vmDXIlPH0uc', '{\"len\":12,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":31727}', '2025-10-23 18:01:12'),
(17, 20, 20, 'password', '$argon2id$v=19$m=65536,t=4,p=1$a0ZqLnR2N0Vud3JPSG52TA$YhCDhJYPEknYnGZUesMQSQFhNmJ0NvvwVpz2jULYenM', '{\"len\":15,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":48967}', '2025-10-23 20:45:50'),
(18, 38, 39, 'pattern', '2efec8afb043fc240a2a75484c58f0bd41cf9a5a366e6e16c7d7ce6d9313d8b1', '{\"nodes\":5,\"create_time_ms\":52812}', '2025-10-23 20:47:58'),
(19, 18, 19, 'password', '$argon2id$v=19$m=65536,t=4,p=1$bUNKdlprcVFIL2xFbWJiMQ$pf4F114OEr9PNsBBucHU9Dm675jdlbOEc84+IEjaF/U', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":31960}', '2025-10-25 09:20:48'),
(20, 11, 11, 'password', '$argon2id$v=19$m=65536,t=4,p=1$RC5NelB3eWxBenhwS1k4Qg$/FbrLfBjPHSov2lSj9FHFBcqsCyDP2QrG02086htkgk', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":33436}', '2025-10-25 10:19:11'),
(21, 29, 30, 'pattern', '0bf9b1a082894eaad6b7177156a93091d090a5254d9c524b9d101101087c2e83', '{\"nodes\":4,\"create_time_ms\":30859}', '2025-10-25 10:20:25'),
(22, 3, 3, 'password', '$argon2id$v=19$m=65536,t=4,p=1$cXB3QjFrY0ZwS2tScWVhWg$H79TgKEG1HXZ7yMYFUyPcUDxmwnQ8zGUcbzrlASmEMw', '{\"len\":8,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":56146}', '2025-10-25 16:24:55'),
(23, 22, 23, 'pattern', '1b2a65fe5959264b4bd2a1cab416d29ac8fbbc7dbf25c1462d6e9ef099534629', '{\"nodes\":6,\"create_time_ms\":10091}', '2025-10-25 16:30:47'),
(24, 8, 8, 'password', '$argon2id$v=19$m=65536,t=4,p=1$MHovQXZvU2RoVkM4ZWo4aw$BEkz2kPoq0J32HnyLoor2RiiZkNiaRwNsNFAuIg3nRo', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":46312}', '2025-10-27 09:11:54'),
(25, 26, 26, 'pattern', '4d0068af2eb0a4c30a4c279748ed393d61941d519f00bfffcef935a8c131f758', '{\"nodes\":4,\"create_time_ms\":28584}', '2025-10-27 09:14:41'),
(26, 9, 9, 'password', '$argon2id$v=19$m=65536,t=4,p=1$Q2ZGMTRJTE9zd20yYXc0NA$tNHn342oJtvajMcDCRxPUKrzjWZtOpZk05IbnRqLPnY', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":21909}', '2025-10-27 09:24:58'),
(27, 19, 20, 'password', '$argon2id$v=19$m=65536,t=4,p=1$TzhrQ3o4OE9vVVlXbC50TA$5bv3hHUtdH8ZyLOLy4R3jiKoH0JUH4iayqlvk6/6Ylc', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":19418}', '2025-10-27 10:16:38'),
(28, 23, 40, 'pattern', '1f382b91ee9f8fab7f97a6072763077575534fb21ce5db21e26f39ed0976b4ea', '{\"nodes\":7,\"create_time_ms\":5622}', '2025-10-27 10:40:13'),
(29, 10, 10, 'password', '$argon2id$v=19$m=65536,t=4,p=1$U2hLNTI0YWtkMDZHRXNMSQ$ZMDaARI3i38FGQ0i2a/Nj8xwGB0iAlF3HkI8dIkb2GA', '{\"len\":8,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":24293}', '2025-10-27 10:54:05'),
(30, 12, 12, 'password', '$argon2id$v=19$m=65536,t=4,p=1$RTJnaWpCVGhpeEF0aEs5Sw$YrMxgyX4nxGjjIz+mQ5qIhf8qwi74vn/vFJfWMA0gnI', '{\"len\":11,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":42516}', '2025-10-27 10:57:31'),
(31, 28, 29, 'pattern', '62e405a8b32287a9f01385dd180149a076732e51545e07ba39e66e9da71256f7', '{\"nodes\":4,\"create_time_ms\":17126}', '2025-10-27 11:06:57'),
(32, 5, 5, 'password', '$argon2id$v=19$m=65536,t=4,p=1$MHo5Z2ZkQzBNR3lKbTU4Ug$rkVnjEx+qK/5U76giy468M2qMwK/J1r7+cV8BSssre0', '{\"len\":9,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":false,\"create_time_ms\":23441}', '2025-10-27 11:38:09'),
(33, 42, 46, 'pattern', '40e52055c05550e19f46378bf34a907755bafb60b045588440b179d1676774e7', '{\"nodes\":5,\"create_time_ms\":810395}', '2025-11-30 19:41:42'),
(34, 43, 45, 'password', '$argon2id$v=19$m=65536,t=4,p=1$V1NuejlRRnZ0a293N2VVYg$SCFwTaMOkGy3+b1fvfvcwAUQo8c3jYhz7PRf+DZQABA', '{\"len\":16,\"has_upper\":true,\"has_lower\":true,\"has_digit\":true,\"has_symbol\":true,\"create_time_ms\":48329}', '2025-11-30 19:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `experiments`
--

CREATE TABLE `experiments` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `modality` enum('password','pattern','ab') NOT NULL,
  `policy_json` longtext DEFAULT NULL,
  `schedule_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experiments`
--

INSERT INTO `experiments` (`id`, `title`, `modality`, `policy_json`, `schedule_json`, `created_at`, `updated_at`) VALUES
(1, 'Pilot Test 1 - Password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-20 21:10:07', '2025-10-20 21:10:07'),
(2, 'Pilot Test 1 - Pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-20 21:10:28', '2025-10-20 21:10:28'),
(3, 'Password And Pattern Experiment', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-21 18:45:17', '2025-10-21 18:45:17'),
(4, 'prof test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:29:28', '2025-10-22 12:29:28'),
(5, 'prof test pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:34:01', '2025-10-22 12:34:01'),
(6, 'Kyari Test Password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:37:07', '2025-10-22 12:37:07'),
(7, 'Kyari test pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:37:30', '2025-10-22 12:37:30'),
(8, 'Alhassan test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:37:55', '2025-10-22 12:37:55'),
(9, 'Alhassan test Pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:38:09', '2025-10-22 12:38:09'),
(10, 'Bello test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:38:31', '2025-10-22 12:38:31'),
(11, 'Bello test pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:38:43', '2025-10-22 12:38:43'),
(12, 'Ramat test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:39:06', '2025-10-22 12:39:06'),
(13, 'Ramat test  pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:39:27', '2025-10-22 12:39:27'),
(14, 'sakinat Test Password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:39:51', '2025-10-22 12:39:51'),
(15, 'Sakina test Pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:40:05', '2025-10-22 12:40:05'),
(16, 'Mojirade Test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:40:32', '2025-10-22 12:40:32'),
(17, 'MojiradeTest pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:40:53', '2025-10-22 12:40:53'),
(18, 'Hannah test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:42:01', '2025-10-22 12:42:01'),
(19, 'Hannah test Pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:42:21', '2025-10-22 12:42:21'),
(20, 'Lawal  test password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 12:49:46', '2025-10-22 12:49:46'),
(21, 'PT password and pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:36:26', '2025-10-22 15:36:26'),
(22, 'PT prof test Pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:37:44', '2025-10-22 15:37:44'),
(23, 'pt prof testt pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:39:09', '2025-10-22 15:39:09'),
(24, 'PT Kyari test1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:41:00', '2025-10-22 15:41:00'),
(25, 'PT Kyari test12pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:41:15', '2025-10-22 15:41:15'),
(26, 'PT Alhassan test1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:41:56', '2025-10-22 15:41:56'),
(27, 'PT Alhassan test 2 pattern', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:42:11', '2025-10-22 15:42:11'),
(28, 'PT Alhassan test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:42:40', '2025-10-22 15:42:40'),
(29, 'PT  Bello test 1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:43:39', '2025-10-22 15:43:39'),
(30, 'PT  Bello test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:44:09', '2025-10-22 15:44:09'),
(31, 'PT Ramat test patten', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:45:23', '2025-10-22 15:45:23'),
(32, 'PT Sakina test 1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:46:33', '2025-10-22 15:46:33'),
(33, 'PT Sakina test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:46:54', '2025-10-22 15:46:54'),
(34, 'PT Mojirade test 1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:47:45', '2025-10-22 15:47:45'),
(35, 'PT Mojirade test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:48:00', '2025-10-22 15:48:00'),
(36, 'PT Hannah test 1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:48:18', '2025-10-22 15:48:18'),
(37, 'PT Hannah  test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:48:47', '2025-10-22 15:48:47'),
(38, 'PT Lawal test 1 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:49:17', '2025-10-22 15:49:17'),
(39, 'PT Lawal test 2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:49:40', '2025-10-22 15:49:40'),
(40, 'PT prof test2 pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-22 15:55:50', '2025-10-22 15:55:50'),
(41, 'PTEos', 'password', '{\"password\":{\"min_length\":16,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":true},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-25 16:29:23', '2025-10-25 16:29:23'),
(42, 'Sahiri', 'pattern', '{\"password\":{\"min_length\":8187551631,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-27 10:43:42', '2025-10-27 10:43:42'),
(43, 'Alh', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-27 10:55:54', '2025-10-27 10:55:54'),
(44, 'Computer studies', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-10-27 10:57:02', '2025-10-27 10:57:02'),
(45, 'WorkScreenshots_Password', 'password', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-11-30 19:03:46', '2025-11-30 19:03:46'),
(46, 'WorkScreenshots_Pattern', 'pattern', '{\"password\":{\"min_length\":8,\"require_upper\":true,\"require_lower\":true,\"require_digit\":true,\"require_symbol\":false},\"pattern\":{\"grid\":3,\"min_nodes\":4,\"allow_cross\":false}}', NULL, '2025-11-30 19:04:11', '2025-11-30 19:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(16) NOT NULL,
  `experiment_id` int(10) UNSIGNED NOT NULL,
  `status` enum('invited','joined','completed') DEFAULT 'invited',
  `joined_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `code`, `experiment_id`, `status`, `joined_at`, `created_at`) VALUES
(1, 'WVMZEUCR', 2, 'completed', '2025-10-20 21:11:48', '2025-10-20 21:10:36'),
(2, 'DUJL8JXD', 1, 'completed', '2025-10-20 21:10:53', '2025-10-20 21:10:41'),
(3, '8KU8C4YV', 3, 'completed', '2025-10-25 16:23:59', '2025-10-21 18:46:43'),
(4, 'V4VBRFGH', 4, 'invited', NULL, '2025-10-22 12:30:35'),
(5, 'LZWTA2LL', 5, 'completed', '2025-10-27 11:37:45', '2025-10-22 12:52:59'),
(6, '7RABVPND', 6, 'completed', '2025-10-23 14:08:24', '2025-10-22 12:53:22'),
(7, '97YW59XJ', 7, 'completed', '2025-10-23 18:00:37', '2025-10-22 12:53:32'),
(8, 'TPDKTCCC', 8, 'completed', '2025-10-27 09:10:02', '2025-10-22 12:53:41'),
(9, 'QHTAEEKY', 9, 'completed', '2025-10-23 14:37:37', '2025-10-22 12:53:49'),
(10, 'AWZ5SU55', 10, 'completed', '2025-10-27 10:52:33', '2025-10-22 12:54:18'),
(11, 'UZTKSTC8', 11, 'completed', '2025-10-25 10:18:13', '2025-10-22 12:54:26'),
(12, 'XSFELW2A', 12, 'completed', '2025-10-23 14:13:42', '2025-10-22 12:54:33'),
(13, 'YGHFV392', 14, 'completed', '2025-10-23 15:02:32', '2025-10-22 12:54:50'),
(14, 'HXCNDJUT', 15, 'completed', '2025-10-23 13:31:06', '2025-10-22 12:54:57'),
(15, 'WL5457N4', 16, 'completed', '2025-10-23 14:45:25', '2025-10-22 12:55:04'),
(16, '9AF97W2A', 17, 'completed', '2025-10-23 12:50:20', '2025-10-22 12:55:11'),
(17, '5YCEMYW7', 18, 'completed', '2025-10-23 16:27:50', '2025-10-22 12:55:19'),
(18, 'XNA9Q6F6', 19, 'completed', '2025-10-25 09:20:15', '2025-10-22 12:55:29'),
(19, '6VACTBBB', 20, 'completed', '2025-10-27 10:16:18', '2025-10-22 12:55:50'),
(20, 'ZUVX3JSK', 20, 'completed', '2025-10-23 20:45:00', '2025-10-22 12:55:58'),
(21, 'AXB9MFNW', 21, 'completed', '2025-10-23 09:27:35', '2025-10-22 15:51:36'),
(22, '3ZS9878A', 23, 'completed', '2025-10-25 16:30:11', '2025-10-22 15:56:23'),
(23, 'HHFH3TE9', 40, 'completed', '2025-10-27 10:39:01', '2025-10-22 15:56:35'),
(24, '5QHL2362', 24, 'completed', '2025-10-23 13:59:49', '2025-10-22 15:57:00'),
(25, 'W5K34K5W', 25, 'joined', '2025-10-27 10:14:37', '2025-10-22 15:57:09'),
(26, 'E9VNGYH3', 26, 'completed', '2025-10-27 09:14:10', '2025-10-22 15:57:32'),
(27, 'P53AE293', 28, 'completed', '2025-10-23 14:34:19', '2025-10-22 15:59:29'),
(28, 'CUC7DT5M', 29, 'completed', '2025-10-27 11:06:39', '2025-10-22 15:59:43'),
(29, 'J5WRDG7K', 30, 'completed', '2025-10-25 10:19:53', '2025-10-22 15:59:48'),
(30, '2JKXTFL7', 31, 'invited', NULL, '2025-10-22 15:59:57'),
(31, '4RGWL632', 32, 'joined', '2025-10-23 15:04:26', '2025-10-22 16:00:16'),
(32, 'SYKW4T48', 33, 'completed', '2025-10-23 13:38:32', '2025-10-22 16:00:41'),
(33, 'Y2VYY2X3', 34, 'completed', '2025-10-23 15:21:07', '2025-10-22 16:00:57'),
(34, 'RB7EXYPF', 35, 'completed', '2025-10-23 13:09:03', '2025-10-22 16:01:13'),
(35, '4SFPS7YH', 36, 'invited', NULL, '2025-10-22 16:01:21'),
(36, '482MKQPX', 37, 'joined', '2025-10-25 09:21:27', '2025-10-22 16:01:29'),
(37, 'HUMSNP7X', 38, 'joined', '2025-10-27 10:16:54', '2025-10-22 16:01:41'),
(38, 'WKBKYNKD', 39, 'completed', '2025-10-23 20:46:44', '2025-10-22 16:01:51'),
(39, 'FBYWV9NN', 40, 'invited', NULL, '2025-10-23 15:22:30'),
(40, 'E49TREXS', 32, 'invited', NULL, '2025-10-23 15:22:40'),
(41, 'QM86P6YY', 42, 'invited', NULL, '2025-10-27 10:44:23'),
(42, 'L9MGGLY5', 46, 'completed', '2025-11-30 19:26:33', '2025-11-30 19:25:51'),
(43, '9HPD8MQ7', 45, 'completed', '2025-11-30 19:26:30', '2025-11-30 19:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `participant_id` int(10) UNSIGNED NOT NULL,
  `experiment_id` int(10) UNSIGNED NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `participant_id`, `experiment_id`, `started_at`, `ended_at`) VALUES
(1, 2, 1, '2025-10-20 21:10:53', '2025-10-20 21:11:30'),
(2, 1, 2, '2025-10-20 21:11:47', '2025-10-20 21:12:06'),
(3, 21, 21, '2025-10-23 09:27:35', '2025-10-23 09:39:58'),
(4, 16, 17, '2025-10-23 12:50:20', '2025-10-23 12:53:42'),
(5, 34, 35, '2025-10-23 13:09:03', '2025-10-23 13:11:26'),
(6, 14, 15, '2025-10-23 13:31:06', '2025-10-23 13:31:59'),
(7, 32, 33, '2025-10-23 13:38:32', '2025-10-23 13:39:16'),
(8, 24, 24, '2025-10-23 13:59:49', '2025-10-23 14:01:30'),
(9, 6, 6, '2025-10-23 14:08:24', '2025-10-23 14:11:06'),
(10, 12, 12, '2025-10-23 14:13:42', '2025-10-27 10:59:31'),
(11, 27, 28, '2025-10-23 14:34:19', '2025-10-27 09:25:43'),
(12, 9, 9, '2025-10-23 14:37:37', '2025-10-27 09:25:05'),
(13, 15, 16, '2025-10-23 14:45:25', '2025-10-23 14:46:49'),
(14, 13, 14, '2025-10-23 15:02:32', '2025-10-27 10:55:34'),
(15, 31, 32, '2025-10-23 15:04:26', NULL),
(16, 33, 34, '2025-10-23 15:21:07', '2025-10-23 15:21:41'),
(17, 17, 18, '2025-10-23 16:27:50', '2025-10-23 16:33:26'),
(18, 7, 7, '2025-10-23 18:00:37', '2025-10-23 18:01:25'),
(19, 20, 20, '2025-10-23 20:45:00', '2025-10-23 20:46:10'),
(20, 38, 39, '2025-10-23 20:46:44', '2025-10-23 20:48:12'),
(21, 18, 19, '2025-10-25 09:20:15', '2025-10-25 09:21:00'),
(22, 36, 37, '2025-10-25 09:21:27', NULL),
(23, 11, 11, '2025-10-25 10:18:13', '2025-10-25 10:19:31'),
(24, 29, 30, '2025-10-25 10:19:53', '2025-10-25 10:20:43'),
(25, 3, 3, '2025-10-25 16:23:59', '2025-10-25 16:25:11'),
(26, 22, 23, '2025-10-25 16:30:11', '2025-10-25 16:31:03'),
(27, 8, 8, '2025-10-27 09:10:02', '2025-10-27 09:13:19'),
(28, 26, 26, '2025-10-27 09:14:10', '2025-10-27 09:17:01'),
(29, 25, 25, '2025-10-27 10:14:37', NULL),
(30, 19, 20, '2025-10-27 10:16:18', '2025-10-27 10:16:44'),
(31, 37, 38, '2025-10-27 10:16:54', NULL),
(32, 23, 40, '2025-10-27 10:39:01', '2025-10-27 10:41:48'),
(33, 10, 10, '2025-10-27 10:52:33', '2025-10-27 10:54:36'),
(34, 28, 29, '2025-10-27 11:06:39', '2025-10-27 11:07:11'),
(35, 5, 5, '2025-10-27 11:37:45', '2025-10-27 11:38:14'),
(36, 43, 45, '2025-11-30 19:26:30', '2025-11-30 19:45:59'),
(37, 42, 46, '2025-11-30 19:26:33', '2025-11-30 19:42:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attempts`
--
ALTER TABLE `attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_attempt` (`session_id`,`attempt_no`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_pe` (`participant_id`,`experiment_id`),
  ADD KEY `fk_credentials_experiment` (`experiment_id`);

--
-- Indexes for table `experiments`
--
ALTER TABLE `experiments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_participants_experiment` (`experiment_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessions_experiment` (`experiment_id`),
  ADD KEY `idx_participant_experiment` (`participant_id`,`experiment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `experiments`
--
ALTER TABLE `experiments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempts_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `credentials`
--
ALTER TABLE `credentials`
  ADD CONSTRAINT `fk_credentials_experiment` FOREIGN KEY (`experiment_id`) REFERENCES `experiments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_credentials_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_participants_experiment` FOREIGN KEY (`experiment_id`) REFERENCES `experiments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_experiment` FOREIGN KEY (`experiment_id`) REFERENCES `experiments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sessions_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
