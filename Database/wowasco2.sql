-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 03:38 PM
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
-- Database: `wowasco2`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `asset_type` varchar(100) NOT NULL,
  `subtype` varchar(100) NOT NULL,
  `serial_number` varchar(150) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `purchase_date` date NOT NULL,
  `date_added` date NOT NULL,
  `status` enum('Active','Inactive','Faulty') DEFAULT 'Active',
  `asset_value` decimal(15,2) NOT NULL,
  `depreciated_value` decimal(15,2) DEFAULT 0.00,
  `net_value` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_name`, `asset_type`, `subtype`, `serial_number`, `location`, `purchase_date`, `date_added`, `status`, `asset_value`, `depreciated_value`, `net_value`, `created_at`) VALUES
(1, 'smart meter', 'Field asset', 'Fixed Asset', 'GHTY%^', 'wote', '2026-04-01', '2026-04-10', 'Active', 30000.00, 156.94, 29843.06, '2026-04-10 11:07:42'),
(2, 'desktop', 'Office Asset', 'Fixed Asset', 'JHNJTU78T', 'wote office', '2026-04-09', '2026-04-10', 'Active', 50000.00, 44.90, 49955.10, '2026-04-10 13:01:39'),
(3, 'desktop', 'Office Asset', 'Fixed Asset', 'HJGY67TY', 'wote office', '2026-04-09', '2026-04-10', 'Active', 50000.00, 44.90, 49955.10, '2026-04-10 13:02:05'),
(5, 'tractor', 'Field Asset', 'Fixed Asset', 'GHTF56TYH', 'wote ', '2026-04-09', '2026-04-10', 'Active', 3500000.00, 3142.41, 3496857.59, '2026-04-10 13:19:11'),
(7, 'smart meter', 'Smart Meter', 'Fixed Asset', 'JKHY67TY', 'wote kasarani', '2026-04-06', '2026-04-13', 'Active', 50000.00, 202.28, 49797.72, '2026-04-13 07:11:36'),
(9, 'laptop', 'Field Asset', 'Digital Asset', 'HJNUYT56TY', 'wote office', '2026-04-13', '2026-04-13', 'Active', 140000.00, 29.75, 139970.25, '2026-04-13 07:18:32'),
(12, 'wowosco billing system', 'Office Asset', 'Digital Asset', 'gfhfhfhfh', 'wote office', '2026-04-13', '2026-04-13', 'Active', 500000.00, 199.61, 499800.39, '2026-04-13 15:29:09'),
(13, 'motor vehicle', 'Field Asset', 'Fixed Asset', 'HJGBTY67', 'wote', '2026-03-31', '2026-04-14', 'Active', 2500000.00, 19893.80, 2480106.20, '2026-04-14 10:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `bill_month` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumption`
--

CREATE TABLE `consumption` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `month` varchar(20) DEFAULT NULL,
  `units_used` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer1`
--

CREATE TABLE `customer1` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `meter_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_interactions`
--

CREATE TABLE `customer_interactions` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `action` varchar(100) NOT NULL,
  `interaction_type` varchar(100) NOT NULL,
  `staff_assigned` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_interactions`
--

INSERT INTO `customer_interactions` (`id`, `customer_name`, `action`, `interaction_type`, `staff_assigned`, `notes`, `logged_at`) VALUES
(1, 'joshua charlse', 'call', 'complain', 'jane judith', 'metedosconnection', '2026-04-13 18:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `employee_number` varchar(50) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sha` decimal(10,2) DEFAULT 0.00,
  `nssf` decimal(10,2) DEFAULT 0.00,
  `housing_levy` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `gross_salary` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) DEFAULT 0.00,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_name`, `employee_number`, `position`, `department`, `basic_salary`, `status`, `created_at`, `sha`, `nssf`, `housing_levy`, `tax`, `gross_salary`, `net_salary`, `bank_name`, `account_number`) VALUES
(1, 'janet dow', '2090564758', 'accountant', 'Accounts', 60000.00, 'Active', '2026-04-10 09:22:21', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(2, 'janet dow', '2090675867', 'Accountant', 'Accounts', 0.00, 'Active', '2026-04-10 09:29:52', 2750.00, 1080.00, 1500.00, 30000.00, 100000.00, 64670.00, NULL, NULL),
(3, 'cate becks', '2098675648', 'receptionist', 'Administration', 0.00, 'Active', '2026-04-10 09:36:18', 1375.00, 1080.00, 750.00, 15000.00, 50000.00, 31795.00, NULL, NULL),
(4, 'justin day', '20904546575', 'water engineer', 'water', 0.00, 'Active', '2026-04-13 13:23:47', 2750.00, 1080.00, 1500.00, 30000.00, 100000.00, 64670.00, 'kenya commercial', '14456787909'),
(5, 'jackson don', '202013465758', 'ICT Officer', 'ICT', 0.00, 'Active', '2026-04-13 14:23:25', 2475.00, 1080.00, 1350.00, 27000.00, 90000.00, 58095.00, 'Cooperative', '145675434');

-- --------------------------------------------------------

--
-- Table structure for table `infrastructure`
--

CREATE TABLE `infrastructure` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `asset_category` varchar(50) DEFAULT NULL,
  `activity` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `infrastructure`
--

INSERT INTO `infrastructure` (`id`, `name`, `type`, `asset_category`, `activity`, `location`, `latitude`, `longitude`, `status`, `photo`) VALUES
(4, 'wowasco billing system', 'software', 'Digital Asset', 'Maintenance', 'online', NULL, NULL, 'Active', 'uploads/1776170424_ai.png'),
(5, 'laptop', 'hardware', 'Digital Asset', 'Repair', 'wote office', NULL, NULL, 'Active', 'uploads/1776170474_Screenshot 2026-03-24 144515.png');

-- --------------------------------------------------------

--
-- Table structure for table `invoices2`
--

CREATE TABLE `invoices2` (
  `id` int(11) NOT NULL,
  `meter_serial` varchar(100) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `billing_period` date NOT NULL,
  `pumped_volume` decimal(10,2) DEFAULT 0.00,
  `unit_rate` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices2`
--

INSERT INTO `invoices2` (`id`, `meter_serial`, `customer_id`, `meter_id`, `customer_name`, `billing_period`, `pumped_volume`, `unit_rate`, `amount`, `status`, `created_at`) VALUES
(1, 'BNTY674R', NULL, NULL, 'Joy Dickson', '0000-00-00', 731.50, 50.00, 36575.00, 'Unpaid', '2026-04-10 09:00:20'),
(2, 'NMHTY56ER', NULL, NULL, 'benard john', '0000-00-00', 1765.00, 50.00, 88250.00, 'Unpaid', '2026-04-10 09:00:20'),
(3, 'DFGH56TY', NULL, NULL, 'dan kiarie', '0000-00-00', 2166.00, 50.00, 108300.00, 'Unpaid', '2026-04-10 09:00:20'),
(4, 'NMHJ67yu', NULL, NULL, 'john doe', '0000-00-00', 2340.00, 50.00, 117000.00, 'Unpaid', '2026-04-10 09:00:20'),
(5, 'NMHJ674FT', NULL, NULL, 'john duck', '0000-00-00', 1776.00, 50.00, 88800.00, 'Unpaid', '2026-04-10 09:00:20'),
(6, 'SDWE34RT', NULL, NULL, 'wickliff jay', '0000-00-00', 2251.00, 50.00, 112550.00, 'Unpaid', '2026-04-10 09:00:20'),
(7, 'JKHYt56fv', NULL, NULL, 'janice cate', '0000-00-00', 1537.00, 50.00, 76850.00, 'Unpaid', '2026-04-10 09:00:20'),
(8, 'BNGHTR45', NULL, NULL, 'County treasury', '0000-00-00', 2304.00, 50.00, 115200.00, 'Unpaid', '2026-04-10 09:00:20'),
(9, 'ASFG56T', NULL, NULL, 'county commissioner building', '0000-00-00', 2040.00, 50.00, 102000.00, 'Unpaid', '2026-04-10 09:00:20'),
(10, 'JKHY67TY', NULL, NULL, 'green space', '0000-00-00', 1852.00, 50.00, 92600.00, 'Unpaid', '2026-04-10 09:00:20');

-- --------------------------------------------------------

--
-- Table structure for table `lodge_reports`
--

CREATE TABLE `lodge_reports` (
  `id` int(11) NOT NULL,
  `interaction_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `lodge_status` varchar(50) DEFAULT 'Pending',
  `escalation_notes` text DEFAULT NULL,
  `escalated_to` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `solved_at` datetime DEFAULT NULL,
  `turnaround_time` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lodge_reports`
--

INSERT INTO `lodge_reports` (`id`, `interaction_id`, `customer_name`, `lodge_status`, `escalation_notes`, `escalated_to`, `created_at`, `solved_at`, `turnaround_time`) VALUES
(1, 1, 'joshua charlse', 'Pending', '', '', '2026-04-15 15:17:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meters`
--

CREATE TABLE `meters` (
  `id` int(11) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `customer_type` varchar(50) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `meter_type` varchar(100) NOT NULL,
  `installation_date` date NOT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meters`
--

INSERT INTO `meters` (`id`, `serial_number`, `model`, `customer_type`, `customer_name`, `meter_type`, `installation_date`, `zone`, `created_at`, `status`) VALUES
(1, 'BNTY674R', NULL, 'Personal', 'Joy Dickson', 'smart meter', '2026-04-10', 'wote kasarani', '2026-04-10 07:53:04', 'Active'),
(2, 'NMHTY56ER', NULL, 'Personal', 'benard john', 'smart meter', '2026-04-10', 'wote westlands', '2026-04-10 07:58:12', 'Active'),
(3, 'DFGH56TY', NULL, 'Businesses', 'dan kiarie', 'smart meter', '2026-04-10', 'wote shimo', '2026-04-10 07:58:50', 'Active'),
(4, 'NMHJ67yu', NULL, 'Businesses', 'john doe', 'smart meter', '2026-04-10', 'wote calosci', '2026-04-10 07:59:31', 'Active'),
(6, 'SDWE34RT', NULL, 'Residential', 'wickliff jay', 'smart meter', '2026-04-10', 'wote kileleshwa', '2026-04-10 08:00:55', 'Active'),
(7, 'JKHYt56fv', NULL, 'Residential', 'janice cate', 'smart meter', '2026-04-10', 'wote kasarani', '2026-04-10 08:01:36', 'Active'),
(9, 'ASFG56T', NULL, 'Government Entities', 'county commissioner building', 'smart meter', '2026-04-10', 'wote town center', '2026-04-10 08:03:09', 'Active'),
(18, 'MN-YUH676T', 'E0285', 'Residential', 'peter patric', 'smart meter', '2026-04-18', 'KundaKindu', '2026-04-16 09:42:34', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `meter_alerts`
--

CREATE TABLE `meter_alerts` (
  `id` int(11) NOT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `alert_type` varchar(100) DEFAULT 'general',
  `message` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meter_alerts`
--

INSERT INTO `meter_alerts` (`id`, `meter_id`, `alert_type`, `message`, `status`, `created_at`) VALUES
(1, NULL, 'System', 'Initial alert setup', 'Active', '2026-04-10 08:26:17');

-- --------------------------------------------------------

--
-- Table structure for table `meter_readings`
--

CREATE TABLE `meter_readings` (
  `id` int(11) NOT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `reading_value` decimal(10,2) NOT NULL,
  `reading_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meter_readings`
--

INSERT INTO `meter_readings` (`id`, `meter_id`, `reading_value`, `reading_date`, `created_at`) VALUES
(1, 1, 120.50, '2026-04-10', '2026-04-10 08:30:04'),
(2, 1, 150.00, '2026-04-09', '2026-04-10 08:30:04'),
(3, 1, 130.20, '2026-04-08', '2026-04-10 08:30:04'),
(4, 1, 170.80, '2026-04-07', '2026-04-10 08:30:04'),
(5, 1, 160.00, '2026-04-06', '2026-04-10 08:30:04'),
(6, 9, 360.00, '2026-04-10', '2026-04-10 08:33:58'),
(7, 9, 486.00, '2026-04-09', '2026-04-10 08:33:58'),
(8, 9, 312.00, '2026-04-08', '2026-04-10 08:33:58'),
(9, 9, 213.00, '2026-04-07', '2026-04-10 08:33:58'),
(10, 9, 235.00, '2026-04-06', '2026-04-10 08:33:58'),
(11, 9, 219.00, '2026-04-05', '2026-04-10 08:33:58'),
(12, 9, 215.00, '2026-04-04', '2026-04-10 08:33:58'),
(13, 8, 320.00, '2026-04-10', '2026-04-10 08:33:58'),
(14, 8, 367.00, '2026-04-09', '2026-04-10 08:33:58'),
(15, 8, 177.00, '2026-04-08', '2026-04-10 08:33:58'),
(16, 8, 322.00, '2026-04-07', '2026-04-10 08:33:58'),
(17, 8, 255.00, '2026-04-06', '2026-04-10 08:33:58'),
(18, 8, 420.00, '2026-04-05', '2026-04-10 08:33:58'),
(19, 8, 443.00, '2026-04-04', '2026-04-10 08:33:58'),
(20, 3, 98.00, '2026-04-10', '2026-04-10 08:33:58'),
(21, 3, 455.00, '2026-04-09', '2026-04-10 08:33:58'),
(22, 3, 478.00, '2026-04-08', '2026-04-10 08:33:58'),
(23, 3, 153.00, '2026-04-07', '2026-04-10 08:33:58'),
(24, 3, 394.00, '2026-04-06', '2026-04-10 08:33:58'),
(25, 3, 137.00, '2026-04-05', '2026-04-10 08:33:58'),
(26, 3, 451.00, '2026-04-04', '2026-04-10 08:33:58'),
(27, 10, 185.00, '2026-04-10', '2026-04-10 08:33:58'),
(28, 10, 223.00, '2026-04-09', '2026-04-10 08:33:58'),
(29, 10, 331.00, '2026-04-08', '2026-04-10 08:33:58'),
(30, 10, 107.00, '2026-04-07', '2026-04-10 08:33:58'),
(31, 10, 410.00, '2026-04-06', '2026-04-10 08:33:58'),
(32, 10, 157.00, '2026-04-05', '2026-04-10 08:33:58'),
(33, 10, 439.00, '2026-04-04', '2026-04-10 08:33:58'),
(34, 7, 357.00, '2026-04-10', '2026-04-10 08:33:58'),
(35, 7, 430.00, '2026-04-09', '2026-04-10 08:33:58'),
(36, 7, 54.00, '2026-04-08', '2026-04-10 08:33:58'),
(37, 7, 87.00, '2026-04-07', '2026-04-10 08:33:58'),
(38, 7, 140.00, '2026-04-06', '2026-04-10 08:33:58'),
(39, 7, 204.00, '2026-04-05', '2026-04-10 08:33:58'),
(40, 7, 265.00, '2026-04-04', '2026-04-10 08:33:58'),
(41, 5, 93.00, '2026-04-10', '2026-04-10 08:33:58'),
(42, 5, 284.00, '2026-04-09', '2026-04-10 08:33:58'),
(43, 5, 470.00, '2026-04-08', '2026-04-10 08:33:58'),
(44, 5, 271.00, '2026-04-07', '2026-04-10 08:33:58'),
(45, 5, 130.00, '2026-04-06', '2026-04-10 08:33:58'),
(46, 5, 112.00, '2026-04-05', '2026-04-10 08:33:58'),
(47, 5, 416.00, '2026-04-04', '2026-04-10 08:33:58'),
(48, 4, 359.00, '2026-04-10', '2026-04-10 08:33:58'),
(49, 4, 396.00, '2026-04-09', '2026-04-10 08:33:58'),
(50, 4, 390.00, '2026-04-08', '2026-04-10 08:33:58'),
(51, 4, 124.00, '2026-04-07', '2026-04-10 08:33:58'),
(52, 4, 147.00, '2026-04-06', '2026-04-10 08:33:58'),
(53, 4, 466.00, '2026-04-05', '2026-04-10 08:33:58'),
(54, 4, 458.00, '2026-04-04', '2026-04-10 08:33:58'),
(55, 2, 227.00, '2026-04-10', '2026-04-10 08:33:58'),
(56, 2, 274.00, '2026-04-09', '2026-04-10 08:33:58'),
(57, 2, 318.00, '2026-04-08', '2026-04-10 08:33:58'),
(58, 2, 339.00, '2026-04-07', '2026-04-10 08:33:58'),
(59, 2, 67.00, '2026-04-06', '2026-04-10 08:33:58'),
(60, 2, 277.00, '2026-04-05', '2026-04-10 08:33:58'),
(61, 2, 263.00, '2026-04-04', '2026-04-10 08:33:58'),
(62, 6, 356.00, '2026-04-10', '2026-04-10 08:33:58'),
(63, 6, 432.00, '2026-04-09', '2026-04-10 08:33:58'),
(64, 6, 449.00, '2026-04-08', '2026-04-10 08:33:58'),
(65, 6, 240.00, '2026-04-07', '2026-04-10 08:33:58'),
(66, 6, 260.00, '2026-04-06', '2026-04-10 08:33:58'),
(67, 6, 460.00, '2026-04-05', '2026-04-10 08:33:58'),
(68, 6, 54.00, '2026-04-04', '2026-04-10 08:33:58'),
(69, 11, 401.00, '2026-04-14', '2026-04-14 10:01:12'),
(70, 11, 350.00, '2026-04-13', '2026-04-14 10:01:12'),
(71, 11, 192.00, '2026-04-12', '2026-04-14 10:01:12'),
(72, 11, 387.00, '2026-04-11', '2026-04-14 10:01:12'),
(73, 11, 276.00, '2026-04-10', '2026-04-14 10:01:12'),
(74, 11, 402.00, '2026-04-09', '2026-04-14 10:01:12'),
(75, 11, 208.00, '2026-04-08', '2026-04-14 10:01:12'),
(76, 12, 239.00, '2026-04-14', '2026-04-14 10:01:12'),
(77, 12, 156.00, '2026-04-13', '2026-04-14 10:01:12'),
(78, 12, 165.00, '2026-04-12', '2026-04-14 10:01:12'),
(79, 12, 374.00, '2026-04-11', '2026-04-14 10:01:12'),
(80, 12, 95.00, '2026-04-10', '2026-04-14 10:01:12'),
(81, 12, 260.00, '2026-04-09', '2026-04-14 10:01:12'),
(82, 12, 311.00, '2026-04-08', '2026-04-14 10:01:12'),
(83, 14, 459.00, '2026-04-14', '2026-04-14 10:01:12'),
(84, 14, 351.00, '2026-04-13', '2026-04-14 10:01:12'),
(85, 14, 297.00, '2026-04-12', '2026-04-14 10:01:12'),
(86, 14, 67.00, '2026-04-11', '2026-04-14 10:01:12'),
(87, 14, 121.00, '2026-04-10', '2026-04-14 10:01:12'),
(88, 14, 336.00, '2026-04-09', '2026-04-14 10:01:12'),
(89, 14, 303.00, '2026-04-08', '2026-04-14 10:01:12'),
(90, 13, 394.00, '2026-04-14', '2026-04-14 10:01:12'),
(91, 13, 284.00, '2026-04-13', '2026-04-14 10:01:12'),
(92, 13, 253.00, '2026-04-12', '2026-04-14 10:01:12'),
(93, 13, 334.00, '2026-04-11', '2026-04-14 10:01:12'),
(94, 13, 229.00, '2026-04-10', '2026-04-14 10:01:12'),
(95, 13, 100.00, '2026-04-09', '2026-04-14 10:01:12'),
(96, 13, 271.00, '2026-04-08', '2026-04-14 10:01:12'),
(97, 18, 98.00, '2026-04-16', '2026-04-16 10:03:28'),
(98, 18, 333.00, '2026-04-15', '2026-04-16 10:03:28'),
(99, 18, 326.00, '2026-04-14', '2026-04-16 10:03:28'),
(100, 18, 491.00, '2026-04-13', '2026-04-16 10:03:28'),
(101, 18, 187.00, '2026-04-12', '2026-04-16 10:03:28'),
(102, 18, 208.00, '2026-04-11', '2026-04-16 10:03:28'),
(103, 18, 57.00, '2026-04-10', '2026-04-16 10:03:28');

-- --------------------------------------------------------

--
-- Table structure for table `meter_zone_map`
--

CREATE TABLE `meter_zone_map` (
  `id` int(11) NOT NULL,
  `meter_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `payroll_month` varchar(20) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `nssf` decimal(10,2) DEFAULT NULL,
  `sha` decimal(10,2) DEFAULT NULL,
  `housing_levy` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department` varchar(100) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `payroll_month`, `gross_salary`, `nssf`, `sha`, `housing_levy`, `tax`, `net_salary`, `created_at`, `department`, `employee_name`, `position`, `bank_name`, `account_number`, `status`) VALUES
(1, 272727272, NULL, 100000.00, 1080.00, 2750.00, 1500.00, 30000.00, 64670.00, '2026-04-13 14:49:26', 'ICT', 'jackson dave', 'ICT Officer', 'KCB', '35635353', 'Active'),
(2, 0, NULL, 200000.00, 1080.00, 5500.00, 3000.00, 60000.00, 130420.00, '2026-04-14 10:02:55', 'Accounts', 'Dinah John', 'accountant', 'KCB', '4545454545', 'Active'),
(3, 2147483647, NULL, 200000.00, 1080.00, 5500.00, 3000.00, 60000.00, 130420.00, '2026-04-14 10:08:44', 'ICT', 'benard John', 'ICT Officer', 'KCB', '6767676767', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_runs`
--

CREATE TABLE `payroll_runs` (
  `id` int(11) NOT NULL,
  `month` varchar(20) NOT NULL,
  `year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_runs`
--

INSERT INTO `payroll_runs` (`id`, `month`, `year`, `created_at`) VALUES
(1, 'April', 2026, '2026-04-10 09:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_run_id` int(11) NOT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `nssf` decimal(10,2) DEFAULT NULL,
  `sha` decimal(10,2) DEFAULT NULL,
  `housing_levy` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `employee_id`, `payroll_run_id`, `gross_salary`, `nssf`, `sha`, `housing_levy`, `tax`, `net_salary`, `created_at`) VALUES
(1, 1, 1, 60000.00, 1080.00, 1650.00, 900.00, 18000.00, 38370.00, '2026-04-10 09:34:25'),
(2, 2, 1, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2026-04-10 09:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `rationing_schedule`
--

CREATE TABLE `rationing_schedule` (
  `id` int(11) NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `area` varchar(150) DEFAULT NULL,
  `time_slot` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Test me', 'testme', 'testme@gmail.com', '$2y$10$7w4cAu/vAofGdWyavEVRL.85rqaQCa79NHPxz.w3tDxl25PhggTyK', '2026-04-10 07:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` int(11) NOT NULL,
  `zone_name` varchar(100) DEFAULT NULL,
  `zone_code` varchar(50) DEFAULT NULL,
  `zone_color` varchar(20) DEFAULT '#3388ff',
  `zone_polygon` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consumption`
--
ALTER TABLE `consumption`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer1`
--
ALTER TABLE `customer1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_meter` (`serial_number`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_interactions`
--
ALTER TABLE `customer_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_name` (`customer_name`),
  ADD KEY `idx_logged_at` (`logged_at`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infrastructure`
--
ALTER TABLE `infrastructure`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices2`
--
ALTER TABLE `invoices2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lodge_reports`
--
ALTER TABLE `lodge_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meters`
--
ALTER TABLE `meters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meter_serial` (`serial_number`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- Indexes for table `meter_alerts`
--
ALTER TABLE `meter_alerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meter_zone_map`
--
ALTER TABLE `meter_zone_map`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meter_id` (`meter_id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rationing_schedule`
--
ALTER TABLE `rationing_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumption`
--
ALTER TABLE `consumption`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer1`
--
ALTER TABLE `customer1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_interactions`
--
ALTER TABLE `customer_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `infrastructure`
--
ALTER TABLE `infrastructure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices2`
--
ALTER TABLE `invoices2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lodge_reports`
--
ALTER TABLE `lodge_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meters`
--
ALTER TABLE `meters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `meter_alerts`
--
ALTER TABLE `meter_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meter_readings`
--
ALTER TABLE `meter_readings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `meter_zone_map`
--
ALTER TABLE `meter_zone_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rationing_schedule`
--
ALTER TABLE `rationing_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer1`
--
ALTER TABLE `customer1`
  ADD CONSTRAINT `fk_customer_meter` FOREIGN KEY (`serial_number`) REFERENCES `meters` (`serial_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meter_zone_map`
--
ALTER TABLE `meter_zone_map`
  ADD CONSTRAINT `meter_zone_map_ibfk_1` FOREIGN KEY (`meter_id`) REFERENCES `meters` (`id`),
  ADD CONSTRAINT `meter_zone_map_ibfk_2` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
