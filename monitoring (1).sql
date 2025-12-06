-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 08:26 AM
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
-- Database: `monitoring`
--
CREATE DATABASE IF NOT EXISTS `monitoring` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `monitoring`;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_aktuator`
--

DROP TABLE IF EXISTS `tbl_aktuator`;
CREATE TABLE `tbl_aktuator` (
  `id_aktuator` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `nama_aktuator` varchar(100) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_aktuator`
--

INSERT INTO `tbl_aktuator` (`id_aktuator`, `id_sensor`, `nama_aktuator`, `timestamp`) VALUES
(1, 1, 'Pendingin/Pemanas', '2025-12-06 02:26:12'),
(2, 2, 'Kompresor/Valve', '2025-12-06 02:28:41'),
(3, 3, 'Alarm Sirine', '2025-12-06 02:30:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kontrol`
--

DROP TABLE IF EXISTS `tbl_kontrol`;
CREATE TABLE `tbl_kontrol` (
  `id_kontrol` int(11) NOT NULL,
  `id_aktuator` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `keadaan` enum('ON','OFF') NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_kontrol`
--

INSERT INTO `tbl_kontrol` (`id_kontrol`, `id_aktuator`, `id_sensor`, `keadaan`, `timestamp`) VALUES
(1, 1, 1, 'ON', '2025-12-06 02:47:52'),
(2, 2, 2, 'ON', '2025-12-06 02:28:43'),
(3, 3, 3, 'OFF', '2025-12-06 02:43:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log_anomali`
--

DROP TABLE IF EXISTS `tbl_log_anomali`;
CREATE TABLE `tbl_log_anomali` (
  `id_anomali` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `aksi` varchar(200) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_log_anomali`
--

INSERT INTO `tbl_log_anomali` (`id_anomali`, `id_sensor`, `aksi`, `waktu`) VALUES
(1, 1, 'Suhu Rendah (14.29 °C)', '2025-12-05 19:27:59'),
(2, 1, 'Suhu Rendah (14.83 °C)', '2025-12-05 19:29:24'),
(3, 1, 'Suhu Tinggi (36.02 °C)', '2025-12-05 19:31:04'),
(4, 2, 'Tekanan Tinggi (1156 hPa)', '2025-12-05 19:31:10'),
(5, 2, 'Tekanan Tinggi (1168 hPa)', '2025-12-05 19:31:37'),
(6, 2, 'Tekanan Rendah (873 hPa)', '2025-12-05 19:31:59'),
(7, 2, 'Tekanan Rendah (930 hPa)', '2025-12-05 19:32:13'),
(8, 1, 'Suhu Tinggi (28.3 °C)', '2025-12-05 19:32:45'),
(9, 2, 'Tekanan Rendah (913 hPa)', '2025-12-05 19:32:48'),
(10, 2, 'Tekanan Tinggi (1182 hPa)', '2025-12-05 19:32:51'),
(11, 1, 'Suhu Tinggi (31.29 °C)', '2025-12-05 19:32:54'),
(12, 1, 'Suhu Tinggi (33.85 °C)', '2025-12-05 19:41:41'),
(13, 2, 'Tekanan Tinggi (1138 hPa)', '2025-12-05 19:41:44'),
(14, 2, 'Tekanan Tinggi (1149 hPa)', '2025-12-05 19:42:39'),
(15, 1, 'Suhu Rendah (12.97 °C)', '2025-12-05 19:42:43'),
(16, 1, 'Suhu Rendah (11.02 °C)', '2025-12-05 19:42:59'),
(17, 2, 'Tekanan Tinggi (1188 hPa)', '2025-12-05 19:43:07'),
(18, 2, 'Tekanan Rendah (941 hPa)', '2025-12-05 19:43:33'),
(19, 1, 'Suhu Rendah (14.12 °C)', '2025-12-05 19:43:43'),
(20, 2, 'Tekanan Tinggi (1154 hPa)', '2025-12-05 19:46:50'),
(21, 2, 'Tekanan Rendah (927 hPa)', '2025-12-05 19:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log_sensor`
--

DROP TABLE IF EXISTS `tbl_log_sensor`;
CREATE TABLE `tbl_log_sensor` (
  `id_log_sensor` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pengukuran` decimal(10,2) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_log_sensor`
--

INSERT INTO `tbl_log_sensor` (`id_log_sensor`, `id_sensor`, `id_user`, `pengukuran`, `timestamp`) VALUES
(1, 1, 1, 13.70, '2025-12-06 02:26:12'),
(2, 1, 1, 11.26, '2025-12-06 02:26:16'),
(3, 1, 1, 11.31, '2025-12-06 02:26:19'),
(4, 1, 1, 11.37, '2025-12-06 02:26:23'),
(5, 1, 1, 11.35, '2025-12-06 02:26:27'),
(6, 1, 1, 11.25, '2025-12-06 02:26:31'),
(7, 1, 1, 11.27, '2025-12-06 02:26:35'),
(8, 1, 1, 11.39, '2025-12-06 02:26:39'),
(9, 1, 1, 11.21, '2025-12-06 02:26:43'),
(10, 1, 1, 11.33, '2025-12-06 02:26:47'),
(11, 1, 1, 11.35, '2025-12-06 02:26:51'),
(12, 1, 1, 11.38, '2025-12-06 02:26:55'),
(13, 1, 1, 11.40, '2025-12-06 02:26:59'),
(14, 1, 1, 11.35, '2025-12-06 02:27:03'),
(15, 1, 1, 11.28, '2025-12-06 02:27:07'),
(16, 1, 1, 11.25, '2025-12-06 02:27:11'),
(17, 1, 1, 11.34, '2025-12-06 02:27:15'),
(18, 1, 1, 11.37, '2025-12-06 02:27:19'),
(19, 1, 1, 11.36, '2025-12-06 02:27:23'),
(20, 1, 1, 11.23, '2025-12-06 02:27:27'),
(21, 1, 1, 11.22, '2025-12-06 02:27:31'),
(22, 1, 1, 11.35, '2025-12-06 02:27:35'),
(23, 1, 1, 11.36, '2025-12-06 02:27:39'),
(24, 1, 1, 11.28, '2025-12-06 02:27:43'),
(25, 1, 1, 11.34, '2025-12-06 02:27:47'),
(26, 1, 1, 11.26, '2025-12-06 02:27:51'),
(27, 1, 1, 11.24, '2025-12-06 02:27:55'),
(28, 1, 1, 8.35, '2025-12-06 02:27:59'),
(29, 1, 1, 11.40, '2025-12-06 02:28:03'),
(30, 1, 1, 20.32, '2025-12-06 02:28:07'),
(31, 1, 1, 20.32, '2025-12-06 02:28:11'),
(32, 1, 1, 20.04, '2025-12-06 02:28:14'),
(33, 1, 1, 20.20, '2025-12-06 02:28:17'),
(34, 1, 1, 20.41, '2025-12-06 02:28:20'),
(35, 1, 1, 20.10, '2025-12-06 02:28:23'),
(36, 1, 1, 20.13, '2025-12-06 02:28:26'),
(37, 1, 1, 20.08, '2025-12-06 02:28:31'),
(38, 2, 1, 1000.00, '2025-12-06 02:28:41'),
(39, 1, 1, 20.44, '2025-12-06 02:28:45'),
(40, 2, 1, 1000.00, '2025-12-06 02:28:45'),
(41, 1, 1, 20.27, '2025-12-06 02:28:48'),
(42, 2, 1, 1003.00, '2025-12-06 02:28:48'),
(43, 1, 1, 20.06, '2025-12-06 02:28:51'),
(44, 2, 1, 1001.00, '2025-12-06 02:28:51'),
(45, 1, 1, 20.23, '2025-12-06 02:28:54'),
(46, 2, 1, 998.00, '2025-12-06 02:28:54'),
(47, 2, 1, 999.00, '2025-12-06 02:28:57'),
(48, 1, 1, 20.14, '2025-12-06 02:28:57'),
(49, 1, 1, 20.30, '2025-12-06 02:29:00'),
(50, 2, 1, 997.00, '2025-12-06 02:29:00'),
(51, 1, 1, 20.17, '2025-12-06 02:29:03'),
(52, 2, 1, 996.00, '2025-12-06 02:29:03'),
(53, 1, 1, 20.01, '2025-12-06 02:29:21'),
(54, 2, 1, 996.00, '2025-12-06 02:29:21'),
(55, 1, 1, 14.83, '2025-12-06 02:29:24'),
(56, 2, 1, 998.00, '2025-12-06 02:29:24'),
(57, 1, 1, 20.05, '2025-12-06 02:29:27'),
(58, 2, 1, 997.00, '2025-12-06 02:29:27'),
(59, 1, 1, 20.15, '2025-12-06 02:29:30'),
(60, 2, 1, 998.00, '2025-12-06 02:29:30'),
(61, 2, 1, 998.00, '2025-12-06 02:30:38'),
(62, 1, 1, 20.32, '2025-12-06 02:30:38'),
(63, 1, 1, 20.29, '2025-12-06 02:30:41'),
(64, 2, 1, 998.00, '2025-12-06 02:30:41'),
(65, 2, 1, 1000.00, '2025-12-06 02:30:44'),
(66, 1, 1, 20.44, '2025-12-06 02:30:44'),
(67, 1, 1, 20.26, '2025-12-06 02:30:47'),
(68, 2, 1, 1002.00, '2025-12-06 02:30:47'),
(69, 2, 2, 1003.00, '2025-12-06 02:31:04'),
(70, 1, 2, 36.02, '2025-12-06 02:31:04'),
(71, 2, 2, 1003.00, '2025-12-06 02:31:07'),
(72, 1, 2, 25.99, '2025-12-06 02:31:07'),
(73, 2, 2, 1156.00, '2025-12-06 02:31:10'),
(74, 1, 2, 25.80, '2025-12-06 02:31:10'),
(75, 1, 2, 25.77, '2025-12-06 02:31:13'),
(76, 2, 2, 1098.00, '2025-12-06 02:31:13'),
(77, 1, 2, 25.65, '2025-12-06 02:31:37'),
(78, 2, 2, 1168.00, '2025-12-06 02:31:37'),
(79, 2, 2, 1099.00, '2025-12-06 02:31:40'),
(80, 1, 2, 25.43, '2025-12-06 02:31:40'),
(81, 2, 2, 1097.00, '2025-12-06 02:31:43'),
(82, 1, 2, 25.34, '2025-12-06 02:31:43'),
(83, 1, 2, 25.22, '2025-12-06 02:31:46'),
(84, 2, 2, 1096.00, '2025-12-06 02:31:46'),
(85, 1, 2, 24.83, '2025-12-06 02:31:49'),
(86, 2, 2, 1098.00, '2025-12-06 02:31:49'),
(87, 1, 2, 24.86, '2025-12-06 02:31:52'),
(88, 2, 2, 1098.00, '2025-12-06 02:31:52'),
(89, 2, 2, 1096.00, '2025-12-06 02:31:55'),
(90, 1, 2, 24.92, '2025-12-06 02:31:55'),
(91, 1, 2, 25.11, '2025-12-06 02:31:59'),
(92, 2, 2, 873.00, '2025-12-06 02:31:59'),
(93, 1, 2, 24.84, '2025-12-06 02:32:02'),
(94, 2, 2, 951.00, '2025-12-06 02:32:02'),
(95, 1, 2, 24.51, '2025-12-06 02:32:05'),
(96, 2, 2, 951.00, '2025-12-06 02:32:05'),
(97, 1, 1, 24.58, '2025-12-06 02:32:10'),
(98, 2, 1, 951.00, '2025-12-06 02:32:10'),
(99, 2, 1, 930.00, '2025-12-06 02:32:13'),
(100, 1, 1, 24.58, '2025-12-06 02:32:13'),
(101, 1, 1, 28.30, '2025-12-06 02:32:45'),
(102, 2, 1, 952.00, '2025-12-06 02:32:45'),
(103, 1, 1, 25.77, '2025-12-06 02:32:48'),
(104, 2, 1, 913.00, '2025-12-06 02:32:48'),
(105, 1, 1, 25.65, '2025-12-06 02:32:51'),
(106, 2, 1, 1182.00, '2025-12-06 02:32:51'),
(107, 1, 1, 31.29, '2025-12-06 02:32:54'),
(108, 2, 1, 1100.00, '2025-12-06 02:32:54'),
(109, 1, 1, 25.61, '2025-12-06 02:32:57'),
(110, 2, 1, 1100.00, '2025-12-06 02:32:57'),
(111, 1, 1, 25.61, '2025-12-06 02:33:00'),
(112, 2, 1, 1100.00, '2025-12-06 02:33:00'),
(113, 2, 1, 1098.00, '2025-12-06 02:33:03'),
(114, 1, 1, 25.69, '2025-12-06 02:33:03'),
(115, 2, 1, 1098.00, '2025-12-06 02:33:06'),
(116, 1, 1, 25.44, '2025-12-06 02:33:06'),
(117, 1, 1, 25.77, '2025-12-06 02:33:09'),
(118, 2, 1, 1096.00, '2025-12-06 02:33:09'),
(119, 1, 1, 25.97, '2025-12-06 02:33:12'),
(120, 2, 1, 1097.00, '2025-12-06 02:33:12'),
(121, 1, 1, 26.00, '2025-12-06 02:33:15'),
(122, 2, 1, 1097.00, '2025-12-06 02:33:15'),
(123, 1, 1, 25.71, '2025-12-06 02:33:18'),
(124, 2, 1, 1095.00, '2025-12-06 02:33:18'),
(125, 1, 1, 25.62, '2025-12-06 02:33:21'),
(126, 2, 1, 1092.00, '2025-12-06 02:33:21'),
(127, 1, 1, 25.28, '2025-12-06 02:33:24'),
(128, 2, 1, 1093.00, '2025-12-06 02:33:24'),
(129, 1, 1, 24.94, '2025-12-06 02:33:27'),
(130, 2, 1, 1092.00, '2025-12-06 02:33:27'),
(131, 1, 1, 25.23, '2025-12-06 02:33:30'),
(132, 2, 1, 1092.00, '2025-12-06 02:33:30'),
(133, 1, 1, 24.86, '2025-12-06 02:33:33'),
(134, 2, 1, 1094.00, '2025-12-06 02:33:33'),
(135, 1, 1, 24.86, '2025-12-06 02:33:36'),
(136, 2, 1, 1095.00, '2025-12-06 02:33:36'),
(137, 2, 1, 1092.00, '2025-12-06 02:33:40'),
(138, 1, 1, 25.08, '2025-12-06 02:33:40'),
(139, 1, 1, 25.19, '2025-12-06 02:33:43'),
(140, 2, 1, 1091.00, '2025-12-06 02:33:43'),
(141, 1, 2, 25.01, '2025-12-06 02:41:38'),
(142, 2, 2, 1091.00, '2025-12-06 02:41:38'),
(143, 1, 2, 33.85, '2025-12-06 02:41:41'),
(144, 2, 2, 1090.00, '2025-12-06 02:41:41'),
(145, 1, 2, 25.94, '2025-12-06 02:41:44'),
(146, 2, 2, 1138.00, '2025-12-06 02:41:44'),
(147, 1, 2, 25.79, '2025-12-06 02:41:47'),
(148, 2, 2, 1099.00, '2025-12-06 02:41:47'),
(149, 1, 2, 25.71, '2025-12-06 02:41:50'),
(150, 2, 2, 1098.00, '2025-12-06 02:41:50'),
(151, 1, 2, 25.50, '2025-12-06 02:41:53'),
(152, 2, 2, 1098.00, '2025-12-06 02:41:53'),
(153, 1, 2, 25.29, '2025-12-06 02:41:56'),
(154, 2, 2, 1100.00, '2025-12-06 02:41:56'),
(155, 1, 2, 25.06, '2025-12-06 02:42:00'),
(156, 2, 2, 1098.00, '2025-12-06 02:42:00'),
(157, 2, 2, 1097.00, '2025-12-06 02:42:04'),
(158, 1, 2, 25.02, '2025-12-06 02:42:04'),
(159, 2, 2, 1097.00, '2025-12-06 02:42:08'),
(160, 1, 2, 25.17, '2025-12-06 02:42:08'),
(161, 1, 2, 25.10, '2025-12-06 02:42:12'),
(162, 2, 2, 1097.00, '2025-12-06 02:42:12'),
(163, 1, 2, 25.16, '2025-12-06 02:42:15'),
(164, 2, 2, 1100.00, '2025-12-06 02:42:15'),
(165, 1, 2, 24.94, '2025-12-06 02:42:19'),
(166, 2, 2, 1097.00, '2025-12-06 02:42:19'),
(167, 1, 2, 24.59, '2025-12-06 02:42:23'),
(168, 2, 2, 1098.00, '2025-12-06 02:42:23'),
(169, 1, 2, 24.75, '2025-12-06 02:42:27'),
(170, 2, 2, 1097.00, '2025-12-06 02:42:27'),
(171, 1, 2, 24.47, '2025-12-06 02:42:31'),
(172, 2, 2, 1096.00, '2025-12-06 02:42:31'),
(173, 1, 2, 24.86, '2025-12-06 02:42:35'),
(174, 2, 2, 1095.00, '2025-12-06 02:42:35'),
(175, 1, 2, 24.94, '2025-12-06 02:42:39'),
(176, 2, 2, 1149.00, '2025-12-06 02:42:39'),
(177, 1, 2, 12.97, '2025-12-06 02:42:43'),
(178, 2, 2, 1100.00, '2025-12-06 02:42:43'),
(179, 1, 2, 20.28, '2025-12-06 02:42:47'),
(180, 2, 2, 1099.00, '2025-12-06 02:42:47'),
(181, 1, 2, 20.13, '2025-12-06 02:42:51'),
(182, 2, 2, 1100.00, '2025-12-06 02:42:51'),
(183, 1, 2, 20.20, '2025-12-06 02:42:55'),
(184, 2, 2, 1099.00, '2025-12-06 02:42:55'),
(185, 1, 2, 11.02, '2025-12-06 02:42:59'),
(186, 2, 2, 1097.00, '2025-12-06 02:42:59'),
(187, 1, 2, 20.34, '2025-12-06 02:43:03'),
(188, 2, 2, 1099.00, '2025-12-06 02:43:03'),
(189, 2, 2, 1188.00, '2025-12-06 02:43:07'),
(190, 1, 2, 20.35, '2025-12-06 02:43:07'),
(191, 1, 2, 20.34, '2025-12-06 02:43:11'),
(192, 2, 2, 1098.00, '2025-12-06 02:43:11'),
(193, 1, 2, 20.12, '2025-12-06 02:43:15'),
(194, 2, 2, 1096.00, '2025-12-06 02:43:15'),
(195, 1, 2, 20.17, '2025-12-06 02:43:19'),
(196, 2, 2, 1094.00, '2025-12-06 02:43:19'),
(197, 1, 2, 20.14, '2025-12-06 02:43:23'),
(198, 2, 2, 1092.00, '2025-12-06 02:43:23'),
(199, 1, 2, 20.49, '2025-12-06 02:43:24'),
(200, 2, 2, 1090.00, '2025-12-06 02:43:24'),
(201, 2, 2, 1090.00, '2025-12-06 02:43:27'),
(202, 1, 2, 20.17, '2025-12-06 02:43:27'),
(203, 1, 2, 20.49, '2025-12-06 02:43:30'),
(204, 2, 2, 1088.00, '2025-12-06 02:43:30'),
(205, 1, 2, 20.54, '2025-12-06 02:43:33'),
(206, 2, 2, 941.00, '2025-12-06 02:43:33'),
(207, 1, 2, 20.59, '2025-12-06 02:43:36'),
(208, 2, 2, 951.00, '2025-12-06 02:43:36'),
(209, 1, 2, 20.74, '2025-12-06 02:43:39'),
(210, 2, 2, 951.00, '2025-12-06 02:43:39'),
(211, 1, 1, 14.12, '2025-12-06 02:43:43'),
(212, 2, 1, 953.00, '2025-12-06 02:43:43'),
(213, 1, 1, 20.30, '2025-12-06 02:43:46'),
(214, 2, 1, 953.00, '2025-12-06 02:43:46'),
(215, 1, 1, 20.49, '2025-12-06 02:46:47'),
(216, 2, 1, 952.00, '2025-12-06 02:46:47'),
(217, 1, 1, 20.47, '2025-12-06 02:46:50'),
(218, 2, 1, 1154.00, '2025-12-06 02:46:50'),
(219, 1, 1, 20.21, '2025-12-06 02:46:53'),
(220, 2, 1, 1099.00, '2025-12-06 02:46:53'),
(221, 2, 1, 1099.00, '2025-12-06 02:46:56'),
(222, 1, 1, 20.29, '2025-12-06 02:46:56'),
(223, 1, 1, 20.47, '2025-12-06 02:46:59'),
(224, 2, 1, 927.00, '2025-12-06 02:46:59'),
(225, 2, 1, 950.00, '2025-12-06 02:47:02'),
(226, 1, 1, 20.32, '2025-12-06 02:47:02'),
(227, 1, 1, 20.51, '2025-12-06 02:47:13'),
(228, 2, 1, 953.00, '2025-12-06 02:47:13'),
(229, 1, 1, 20.74, '2025-12-06 02:47:16'),
(230, 2, 1, 952.00, '2025-12-06 02:47:16'),
(231, 2, 1, 954.00, '2025-12-06 02:47:19'),
(232, 1, 1, 20.48, '2025-12-06 02:47:19'),
(233, 1, 1, 20.84, '2025-12-06 02:47:22'),
(234, 2, 1, 956.00, '2025-12-06 02:47:22'),
(235, 1, 1, 21.02, '2025-12-06 02:47:25'),
(236, 2, 1, 958.00, '2025-12-06 02:47:25'),
(237, 2, 1, 958.00, '2025-12-06 02:47:41'),
(238, 2, 1, 959.00, '2025-12-06 02:47:45'),
(239, 2, 1, 957.00, '2025-12-06 02:47:48'),
(240, 1, 1, 20.90, '2025-12-06 02:50:13'),
(241, 2, 1, 957.00, '2025-12-06 02:50:13'),
(242, 2, 1, 960.00, '2025-12-06 02:50:16'),
(243, 1, 1, 20.60, '2025-12-06 02:50:16'),
(244, 1, 1, 20.26, '2025-12-06 02:50:19'),
(245, 2, 1, 962.00, '2025-12-06 02:50:19'),
(246, 1, 1, 20.10, '2025-12-06 02:50:22'),
(247, 2, 1, 964.00, '2025-12-06 02:50:22'),
(248, 1, 1, 20.09, '2025-12-06 02:50:25'),
(249, 2, 1, 963.00, '2025-12-06 02:50:25'),
(250, 1, 1, 20.32, '2025-12-06 02:50:28'),
(251, 2, 1, 961.00, '2025-12-06 02:50:28'),
(252, 1, 1, 20.36, '2025-12-06 02:50:31'),
(253, 2, 1, 964.00, '2025-12-06 02:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log_user`
--

DROP TABLE IF EXISTS `tbl_log_user`;
CREATE TABLE `tbl_log_user` (
  `id_log_user` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `aksi_user` varchar(200) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_log_user`
--

INSERT INTO `tbl_log_user` (`id_log_user`, `id_user`, `aksi_user`, `timestamp`) VALUES
(1, 1, 'Login', '2025-11-29 18:48:07'),
(3, 1, 'Tambah Parameter', '2025-11-29 18:48:07'),
(4, 1, 'Mengedit data user: Aw', '2025-11-30 22:50:47'),
(5, 1, 'Mengedit data user: Aw', '2025-11-30 22:51:07'),
(6, 1, 'Mengedit data user: Rama', '2025-11-30 22:51:33'),
(7, 1, 'Login ke Sistem', '2025-11-30 23:02:57'),
(8, 1, 'Logout', '2025-11-30 23:04:38'),
(11, 1, 'Login ke Sistem', '2025-11-30 23:05:57'),
(12, 1, 'Logout', '2025-11-30 23:09:10'),
(13, 1, 'Login ke Sistem', '2025-11-30 23:09:15'),
(14, 1, 'Logout', '2025-11-30 23:09:33'),
(19, 1, 'Login ke Sistem', '2025-12-01 00:15:11'),
(20, 1, 'Logout', '2025-12-01 00:16:20'),
(23, 1, 'Login ke Sistem', '2025-12-05 21:17:07'),
(24, 1, 'Mengedit data user: Awi', '2025-12-05 21:26:16'),
(25, 1, 'Tambah User: azzzzz', '2025-12-05 21:49:13'),
(26, 1, 'Edit User: awijelek', '2025-12-05 21:49:24'),
(27, 1, 'Hapus User: awijelek', '2025-12-05 21:59:29'),
(28, 1, 'Tambah User: araaa', '2025-12-05 22:00:33'),
(29, 1, 'Menambah perangkat baru: Buzzer-1', '2025-12-05 22:28:32'),
(30, 1, 'Mengubah status aktuator ID 6 menjadi ON', '2025-12-05 22:28:48'),
(31, 1, 'Mengubah status aktuator ID 6 menjadi OFF', '2025-12-05 22:28:49'),
(32, 1, 'Menghapus perangkat: LED-2', '2025-12-05 22:57:49'),
(33, 1, 'Menambah perangkat baru: BMP-3 (GKU-2)', '2025-12-05 22:58:09'),
(34, 1, 'Mengubah status ID 7 ke ON', '2025-12-05 22:58:19'),
(35, 1, 'Menghapus perangkat: Buzzer', '2025-12-05 23:05:53'),
(36, 1, 'Menghapus perangkat: BMP-3', '2025-12-05 23:05:57'),
(37, 1, 'Menambah perangkat baru: DHT-3 (GKU-1)', '2025-12-05 23:06:15'),
(38, 1, 'MELAKUKAN RESET SISTEM (SEMUA DATA SENSOR DIHAPUS)', '2025-12-05 23:14:50'),
(39, 1, 'Menambah perangkat baru: DHT-1 (GKU)', '2025-12-05 23:14:58'),
(40, 1, 'Menambah perangkat baru: BMP-1 (GKU-2)', '2025-12-05 23:15:13'),
(41, 1, 'Mengubah status ID 1 ke ON', '2025-12-05 23:20:24'),
(42, 1, 'Mengubah status ID 2 ke ON', '2025-12-05 23:20:25'),
(43, 1, 'Mengubah status ID 2 ke OFF', '2025-12-05 23:38:28'),
(44, 1, 'Mengubah status ID 1 ke OFF', '2025-12-05 23:38:29'),
(45, 1, 'Mengubah status ID 1 ke ON', '2025-12-05 23:38:43'),
(46, 1, 'Mengubah status ID 2 ke ON', '2025-12-05 23:44:52'),
(47, 1, 'Mengubah status ID 1 ke OFF', '2025-12-06 00:20:03'),
(48, 1, 'Mengubah status ID 2 ke OFF', '2025-12-06 00:20:17'),
(49, 1, 'Menambah perangkat: DHT-2 (GKU)', '2025-12-06 00:20:33'),
(50, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 00:20:35'),
(51, 1, 'Tambah Alat: Buzzer-1', '2025-12-06 00:33:39'),
(52, 1, 'Mengubah status ID 5 ke ON', '2025-12-06 00:34:04'),
(53, 1, 'Hapus Alat: BMP-2', '2025-12-06 00:38:51'),
(54, 1, 'Mengubah status ID 6 ke ON', '2025-12-06 00:39:05'),
(55, 1, 'RESET SYSTEM TOTAL', '2025-12-06 00:50:18'),
(56, 1, 'Tambah Alat: DHT-1', '2025-12-06 00:50:41'),
(57, 1, 'Mengubah status ID 1 ke ON', '2025-12-06 00:50:49'),
(58, 1, 'Tambah Alat: BMP-1', '2025-12-06 00:51:10'),
(59, 1, 'Mengubah status ID 2 ke ON', '2025-12-06 00:51:13'),
(60, 1, 'Logout', '2025-12-06 01:22:21'),
(61, 2, 'Login ke Sistem', '2025-12-06 01:22:31'),
(62, 2, 'Logout', '2025-12-06 01:22:48'),
(63, 1, 'Login ke Sistem', '2025-12-06 01:22:51'),
(64, 1, 'Tambah Alat: DHT-2', '2025-12-06 02:24:22'),
(65, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 02:24:24'),
(66, 1, 'RESET SYSTEM TOTAL', '2025-12-06 02:26:04'),
(67, 1, 'Tambah Alat: DHT-1', '2025-12-06 02:26:12'),
(68, 1, 'Mengubah status ID 1 ke ON', '2025-12-06 02:26:14'),
(69, 1, 'Tambah Alat: BMP-1', '2025-12-06 02:28:41'),
(70, 1, 'Mengubah status ID 2 ke ON', '2025-12-06 02:28:43'),
(71, 1, 'Tambah Alat: Buzzer-1', '2025-12-06 02:30:34'),
(72, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 02:30:42'),
(73, 1, 'Logout', '2025-12-06 02:30:56'),
(74, 2, 'Login ke Sistem', '2025-12-06 02:31:04'),
(75, 2, 'Logout', '2025-12-06 02:32:05'),
(76, 1, 'Login ke Sistem', '2025-12-06 02:32:10'),
(77, 1, 'Mengubah status ID 3 ke OFF', '2025-12-06 02:32:16'),
(78, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 02:34:10'),
(79, 1, 'Mengubah status ID 3 ke OFF', '2025-12-06 02:34:14'),
(80, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 02:40:37'),
(81, 1, 'Mengubah status ID 3 ke OFF', '2025-12-06 02:41:15'),
(82, 1, 'Mengubah status ID 3 ke ON', '2025-12-06 02:41:20'),
(83, 1, 'Logout', '2025-12-06 02:41:31'),
(84, 2, 'Login ke Sistem', '2025-12-06 02:41:38'),
(85, 2, 'Logout', '2025-12-06 02:43:39'),
(86, 1, 'Login ke Sistem', '2025-12-06 02:43:43'),
(87, 1, 'Mengubah status ID 3 ke OFF', '2025-12-06 02:43:48'),
(88, 1, 'Mengubah status ID 1 ke OFF', '2025-12-06 02:47:40'),
(89, 1, 'Mengubah status ID 1 ke ON', '2025-12-06 02:47:52');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_panduan`
--

DROP TABLE IF EXISTS `tbl_panduan`;
CREATE TABLE `tbl_panduan` (
  `id_panduan` int(11) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `isi` text NOT NULL,
  `versi_sistem` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_panduan`
--

INSERT INTO `tbl_panduan` (`id_panduan`, `judul`, `isi`, `versi_sistem`) VALUES
(1, 'Tentang Sistem', 'Sistem Monitoring IoT ini dirancang untuk memantau suhu dan kelembapan secara real-time. Dibangun menggunakan arsitektur MVC sederhana untuk efisiensi dan kemudahan pengembangan.', '1.0.0'),
(2, 'Teknologi', 'Backend: PHP Native (PDO)\nFrontend: HTML5, CSS3 (Custom), Javascript\nDatabase: MySQL / MariaDB\nIoT: ESP32 via REST API', '1.0.0'),
(3, 'Tim Pengembang', 'Nama: Awi Masfufah\r\nNIM: 122490031\r\nProdi: Rekayasa Instrumentasi & Automasi\r\nKampus: Institut Teknologi Sumatera', '-'),
(4, 'Panduan Penggunaan', '1. Login menggunakan akun yang terdaftar.\n2. Buka menu \"Dashboard\" untuk melihat grafik real-time.\n3. Jika ada nilai bahaya, cek menu \"Anomali\".\n4. Gunakan menu \"Kontrol\" untuk menyalakan/mematikan alat.', '-');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_parameter`
--

DROP TABLE IF EXISTS `tbl_parameter`;
CREATE TABLE `tbl_parameter` (
  `id_parameter` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `offset` float DEFAULT 0,
  `skala` float DEFAULT 1,
  `satuan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_parameter`
--

INSERT INTO `tbl_parameter` (`id_parameter`, `id_user`, `id_sensor`, `offset`, `skala`, `satuan`) VALUES
(1, 1, 1, 2.4, 0.5, '°C'),
(2, 1, 2, 1.1, 0.4, '%'),
(3, 2, 3, 0.8, 1.3, 'hPa'),
(4, 2, 4, 1, 1, 'hPa');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sensor`
--

DROP TABLE IF EXISTS `tbl_sensor`;
CREATE TABLE `tbl_sensor` (
  `id_sensor` int(11) NOT NULL,
  `nama_sensor` varchar(100) NOT NULL,
  `satuan_sensor` varchar(50) DEFAULT NULL,
  `ruangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sensor`
--

INSERT INTO `tbl_sensor` (`id_sensor`, `nama_sensor`, `satuan_sensor`, `ruangan`) VALUES
(1, 'DHT-1', 'Celcius', 'GKU'),
(2, 'BMP-1', 'HPa', 'GKU'),
(3, 'Buzzer-1', '-', 'GKU');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email_user` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `username`, `email_user`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', 'admin@example.com', '12345', 'Admin'),
(2, 'Ramadhani', 'Rama', 'rmdhani733@gmail.com', '1234', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_aktuator`
--
ALTER TABLE `tbl_aktuator`
  ADD PRIMARY KEY (`id_aktuator`),
  ADD KEY `id_sensor` (`id_sensor`);

--
-- Indexes for table `tbl_kontrol`
--
ALTER TABLE `tbl_kontrol`
  ADD PRIMARY KEY (`id_kontrol`),
  ADD KEY `id_aktuator` (`id_aktuator`),
  ADD KEY `id_sensor` (`id_sensor`);

--
-- Indexes for table `tbl_log_anomali`
--
ALTER TABLE `tbl_log_anomali`
  ADD PRIMARY KEY (`id_anomali`),
  ADD KEY `id_sensor` (`id_sensor`);

--
-- Indexes for table `tbl_log_sensor`
--
ALTER TABLE `tbl_log_sensor`
  ADD PRIMARY KEY (`id_log_sensor`),
  ADD KEY `id_sensor` (`id_sensor`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tbl_log_user`
--
ALTER TABLE `tbl_log_user`
  ADD PRIMARY KEY (`id_log_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tbl_panduan`
--
ALTER TABLE `tbl_panduan`
  ADD PRIMARY KEY (`id_panduan`);

--
-- Indexes for table `tbl_parameter`
--
ALTER TABLE `tbl_parameter`
  ADD PRIMARY KEY (`id_parameter`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_sensor` (`id_sensor`);

--
-- Indexes for table `tbl_sensor`
--
ALTER TABLE `tbl_sensor`
  ADD PRIMARY KEY (`id_sensor`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_aktuator`
--
ALTER TABLE `tbl_aktuator`
  MODIFY `id_aktuator` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_kontrol`
--
ALTER TABLE `tbl_kontrol`
  MODIFY `id_kontrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_log_anomali`
--
ALTER TABLE `tbl_log_anomali`
  MODIFY `id_anomali` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_log_sensor`
--
ALTER TABLE `tbl_log_sensor`
  MODIFY `id_log_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `tbl_log_user`
--
ALTER TABLE `tbl_log_user`
  MODIFY `id_log_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `tbl_panduan`
--
ALTER TABLE `tbl_panduan`
  MODIFY `id_panduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_parameter`
--
ALTER TABLE `tbl_parameter`
  MODIFY `id_parameter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_sensor`
--
ALTER TABLE `tbl_sensor`
  MODIFY `id_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_aktuator`
--
ALTER TABLE `tbl_aktuator`
  ADD CONSTRAINT `tbl_aktuator_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `tbl_sensor` (`id_sensor`);

--
-- Constraints for table `tbl_kontrol`
--
ALTER TABLE `tbl_kontrol`
  ADD CONSTRAINT `tbl_kontrol_ibfk_1` FOREIGN KEY (`id_aktuator`) REFERENCES `tbl_aktuator` (`id_aktuator`),
  ADD CONSTRAINT `tbl_kontrol_ibfk_2` FOREIGN KEY (`id_sensor`) REFERENCES `tbl_sensor` (`id_sensor`);

--
-- Constraints for table `tbl_log_anomali`
--
ALTER TABLE `tbl_log_anomali`
  ADD CONSTRAINT `tbl_log_anomali_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `tbl_sensor` (`id_sensor`);

--
-- Constraints for table `tbl_log_sensor`
--
ALTER TABLE `tbl_log_sensor`
  ADD CONSTRAINT `tbl_log_sensor_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `tbl_sensor` (`id_sensor`),
  ADD CONSTRAINT `tbl_log_sensor_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`);

--
-- Constraints for table `tbl_log_user`
--
ALTER TABLE `tbl_log_user`
  ADD CONSTRAINT `tbl_log_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`);

--
-- Constraints for table `tbl_parameter`
--
ALTER TABLE `tbl_parameter`
  ADD CONSTRAINT `tbl_parameter_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`),
  ADD CONSTRAINT `tbl_parameter_ibfk_2` FOREIGN KEY (`id_sensor`) REFERENCES `tbl_sensor` (`id_sensor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
