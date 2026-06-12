-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 04:07 PM
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
-- Database: `hostelhub_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent','leave') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `date`, `status`, `remarks`, `marked_by`, `created_at`) VALUES
(2, 8, '2026-05-14', 'present', NULL, 1, '2026-05-14 06:40:41');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `submitted_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `student_id`, `title`, `description`, `category`, `priority`, `status`, `submitted_date`, `created_at`) VALUES
(2, 7, 'xvsakgdajgduws', 'kjcjxzgcjgzuc', NULL, 'medium', 'resolved', NULL, '2026-05-14 04:10:42'),
(3, 8, 'no water', 'dfjgdsjfus', NULL, 'medium', 'resolved', NULL, '2026-05-14 06:45:45'),
(4, 9, 'no water', 'griegsuegfu', NULL, 'medium', 'resolved', NULL, '2026-05-14 08:34:09');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_updates`
--

CREATE TABLE `complaint_updates` (
  `update_id` int(11) NOT NULL,
  `complaint_id` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `update_message` text DEFAULT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `fee_type` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','paid','partial','overdue') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `student_id`, `fee_type`, `amount`, `due_date`, `status`, `remarks`, `created_at`) VALUES
(8, 6, 'Hostel Fee', 5000.00, '2026-05-13', 'paid', NULL, '2026-05-13 18:08:34'),
(9, 7, 'Hostel Fee', 5000.00, '2026-05-14', 'paid', NULL, '2026-05-14 04:09:44'),
(10, 7, 'MESS', 4273.00, '2026-05-14', 'pending', NULL, '2026-05-14 05:34:58'),
(11, 7, 'MESS', 12324.00, '2026-05-08', 'pending', NULL, '2026-05-14 05:36:14'),
(12, 7, 'MESS', 123.00, '2026-05-14', 'paid', NULL, '2026-05-14 05:44:10'),
(13, 8, 'Hostel Fee', 5000.00, '2026-05-14', 'paid', NULL, '2026-05-14 06:37:15'),
(14, 8, 'event', 5000.00, '2026-05-15', 'pending', NULL, '2026-05-14 06:39:31'),
(15, 9, 'Hostel Fee', 5000.00, '2026-05-14', 'paid', NULL, '2026-05-14 08:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `fee_payments`
--

CREATE TABLE `fee_payments` (
  `payment_id` int(11) NOT NULL,
  `fee_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','upi','bank','card') DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `proof_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_payments`
--

INSERT INTO `fee_payments` (`payment_id`, `fee_id`, `student_id`, `amount_paid`, `payment_method`, `transaction_id`, `payment_date`, `received_by`, `remarks`, `created_at`, `payment_screenshot`, `verification_status`, `verified_by`, `verified_at`, `status`, `proof_image`) VALUES
(1, 1, 1, 5000.00, 'upi', '25654545', '2026-05-07 22:48:41', NULL, NULL, '2026-05-07 17:18:41', '1778174321_image.png', 'approved', 1, '2026-05-09 22:48:56', 'Active', NULL),
(2, 8, 6, 5000.00, 'upi', '25654545545845844', '2026-05-13 23:55:06', NULL, NULL, '2026-05-13 18:25:06', '1778696706_image.png', 'approved', 1, '2026-05-13 23:55:57', 'Active', NULL),
(4, 10, 7, 4273.00, 'upi', '987875878758578222222', '2026-05-14 11:05:26', NULL, NULL, '2026-05-14 05:35:26', '1778736926_spiderman-the-web-lord-t8-1920x1080.jpg', 'pending', NULL, NULL, 'Active', NULL),
(5, 11, 7, NULL, NULL, '987875878758578222222444444444', NULL, NULL, NULL, '2026-05-14 05:36:51', NULL, 'pending', NULL, NULL, 'pending', '1778737011_9476522.jpg'),
(6, 9, 7, NULL, NULL, '32333333333333333212434', NULL, NULL, NULL, '2026-05-14 05:41:39', NULL, 'pending', NULL, NULL, 'pending', '1778737299_5714.jpg'),
(7, 12, 7, 123.00, 'upi', '9878758787585787', '2026-05-14 11:16:57', NULL, NULL, '2026-05-14 05:46:57', '1778737617_3484.jpg', 'approved', 1, '2026-05-14 11:17:37', 'Active', '1778737617_3484.jpg'),
(8, 14, 8, 5000.00, 'upi', '987875878758578555', '2026-05-14 12:13:36', NULL, NULL, '2026-05-14 06:43:36', '1778741016_spider-man-across-3840x2160-10140.jpg', 'rejected', 1, '2026-05-14 12:18:18', 'Active', NULL),
(9, 13, 8, 5000.00, 'upi', '7898q3962', '2026-05-14 12:13:47', NULL, NULL, '2026-05-14 06:43:47', '1778741027_ChatGPT_Image_May_7_2026_11_25_30_PM.png', 'approved', 1, '2026-05-14 12:18:17', 'Active', NULL),
(10, 15, 9, 5000.00, 'upi', '75325767676576575', '2026-05-14 14:03:37', NULL, NULL, '2026-05-14 08:33:37', '1778747617_spider-man-miles-3840x2160-12888.jpeg', 'approved', 1, '2026-05-14 14:05:41', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `food_ratings`
--

CREATE TABLE `food_ratings` (
  `rating_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_ratings`
--

INSERT INTO `food_ratings` (`rating_id`, `student_id`, `menu_id`, `rating`, `comments`, `created_at`) VALUES
(1, 6, 1, 4, 'gyud', '2026-05-13 18:08:58'),
(2, 8, 6, 4, 'odiugishds', '2026-05-14 06:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `hostel_blocks`
--

CREATE TABLE `hostel_blocks` (
  `block_id` int(11) NOT NULL,
  `block_name` varchar(50) DEFAULT NULL,
  `block_type` enum('boys','girls','mixed') DEFAULT NULL,
  `total_floors` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostel_blocks`
--

INSERT INTO `hostel_blocks` (`block_id`, `block_name`, `block_type`, `total_floors`, `description`, `created_at`) VALUES
(1, 'A Block', NULL, NULL, 'Main Hostel Block', '2026-05-06 22:03:23'),
(2, 'B', 'boys', 1, '', '2026-05-06 22:06:41'),
(3, 'C', 'boys', 1, '', '2026-05-06 22:06:49'),
(4, 'D', 'boys', 1, '', '2026-05-06 22:06:55'),
(5, 'A', 'boys', 1, '', '2026-05-13 17:42:35'),
(6, 'A', 'boys', 1, '', '2026-05-13 17:42:47'),
(7, 'B', 'boys', 1, '', '2026-05-13 17:42:56'),
(8, 'gg', 'boys', 4, 'eee', '2026-05-14 06:37:59'),
(9, 'C', 'boys', 5, 'kdgg', '2026-05-14 08:29:50');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` enum('available','damaged','out_of_stock') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `leave_type` varchar(100) DEFAULT 'Normal Leave',
  `leave_from` date NOT NULL,
  `leave_to` date NOT NULL,
  `reason` text NOT NULL,
  `medical_proof` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`leave_id`, `student_id`, `leave_type`, `leave_from`, `leave_to`, `reason`, `medical_proof`, `status`, `created_at`) VALUES
(2, 7, 'Sick Leave', '2026-05-07', '2026-05-08', 'rfsfsdfsdf', '1778731903_spiderman-the-web-lord-t8-1920x1080.jpg', 'Approved', '2026-05-14 04:11:43'),
(3, 8, 'Sick Leave', '2026-05-15', '2026-05-15', 'bjasgdhv', '1778741130_spider-man-miles-3840x2160-12888.jpeg', 'Approved', '2026-05-14 06:45:30'),
(4, 9, 'Normal Leave', '2026-05-16', '2026-05-16', 'sick', '', 'Pending', '2026-05-14 08:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `mess_bills`
--

CREATE TABLE `mess_bills` (
  `bill_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `month` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `generated_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mess_menu`
--

CREATE TABLE `mess_menu` (
  `menu_id` int(11) NOT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `breakfast` text DEFAULT NULL,
  `lunch` text DEFAULT NULL,
  `dinner` text DEFAULT NULL,
  `snacks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mess_menu`
--

INSERT INTO `mess_menu` (`menu_id`, `day_of_week`, `breakfast`, `lunch`, `dinner`, `snacks`, `created_at`) VALUES
(6, 'Monday', 'ghgschgsa', 'asjashj', 'sagasv', 'vada', '2026-05-14 06:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `notice_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `audience` enum('all','students','parents','staff') DEFAULT NULL,
  `posted_by` int(11) DEFAULT NULL,
  `posted_date` datetime DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`notice_id`, `title`, `description`, `audience`, `posted_by`, `posted_date`, `expiry_date`, `created_at`) VALUES
(1, 'Welcome To HostelHub', 'System initialized successfully.', 'all', NULL, '2026-05-07 03:33:23', NULL, '2026-05-06 22:03:23');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`parent_id`, `user_id`, `father_name`, `mother_name`, `phone`, `email`, `occupation`, `address`, `city`, `state`, `created_at`) VALUES
(1, 3, 'Rajesh Kumar', 'Meera Rajesh', '9999999999', 'parent@hostelhub.com', NULL, 'Calicut', 'Calicut', 'Kerala', '2026-05-06 22:03:23'),
(2, 5, 'sudhevan', NULL, '', NULL, NULL, NULL, NULL, NULL, '2026-05-07 16:44:02'),
(3, 7, 'sudhevan', NULL, '07306257449', NULL, NULL, NULL, NULL, NULL, '2026-05-09 18:01:02'),
(4, 9, 'gopi@gmail.com', NULL, '7306257445', NULL, NULL, NULL, NULL, NULL, '2026-05-13 17:27:38'),
(5, 11, 'SREENIVASAN', NULL, '7306257449', NULL, NULL, NULL, NULL, NULL, '2026-05-13 18:06:29'),
(6, 13, 'SREENIVASAN', NULL, '7306257449', NULL, NULL, NULL, NULL, NULL, '2026-05-13 18:08:34'),
(7, 15, 'arsfyiogckl;jbk. nl', NULL, '2345678098', NULL, NULL, NULL, NULL, NULL, '2026-05-14 04:09:44'),
(8, 17, 'sudheesh', NULL, '9876545678', NULL, NULL, NULL, NULL, NULL, '2026-05-14 06:37:15'),
(9, 19, 'anoop', NULL, '6717865715', NULL, NULL, NULL, NULL, NULL, '2026-05-14 08:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `block_id` int(11) DEFAULT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `current_occupancy` int(11) DEFAULT 0,
  `room_type` enum('single','double','triple','dormitory') DEFAULT NULL,
  `status` enum('available','full','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `block_id`, `room_number`, `floor`, `capacity`, `current_occupancy`, `room_type`, `status`, `created_at`) VALUES
(5, 2, '202', 2, 2, 2, 'double', 'available', '2026-05-09 17:42:07'),
(6, 8, '900', 3, 5, 2, 'single', 'available', '2026-05-14 06:38:18'),
(7, 9, '', 4, 3, 0, 'single', 'available', '2026-05-14 08:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `room_allocations`
--

CREATE TABLE `room_allocations` (
  `allocation_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `allocation_date` date DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allocated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_allocations`
--

INSERT INTO `room_allocations` (`allocation_id`, `student_id`, `room_id`, `allocation_date`, `checkout_date`, `status`, `remarks`, `created_at`, `allocated_date`) VALUES
(7, 7, 5, '2026-05-14', NULL, '', NULL, '2026-05-14 04:09:57', NULL),
(8, 8, 5, '2026-05-14', NULL, 'active', NULL, '2026-05-14 06:37:35', NULL),
(9, 7, 5, '2026-05-14', NULL, 'active', NULL, '2026-05-14 06:38:36', NULL),
(10, 6, 6, '2026-05-14', NULL, 'active', NULL, '2026-05-14 06:38:53', NULL),
(11, 9, 6, '2026-05-14', NULL, 'active', NULL, '2026-05-14 08:29:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `admission_no` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` enum('active','left','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `parent_id`, `admission_no`, `full_name`, `gender`, `dob`, `course`, `department`, `year`, `phone`, `email`, `address`, `blood_group`, `photo`, `join_date`, `status`, `created_at`) VALUES
(6, 14, 6, '1920', 'Aswin sreenivas', 'male', '2006-06-13', 'ct', 'COMPUTER ENGINEERING', 3, '2346645545', 'aswin.sreenivas005@gmail.com', 'mananthavady', 'o', NULL, '2026-05-13', 'active', '2026-05-13 18:08:34'),
(7, 16, 7, '9090', 'kithu', 'male', '2011-05-14', 'DIPLOMA', 'CT', 3, '2435678908', 'aswin.sreenvas005@gmail.com', 'mananthavady\r\nsteelland', 'O+', NULL, '2026-05-14', 'active', '2026-05-14 04:09:44'),
(8, 18, 8, '1294', 'aswin s', 'male', '2011-05-04', 'DIPLOMA', 'ct', 2, '0987654345', 'aswin.sras005@gmail.com', 'mananthavady\r\nsteelland', 'b+', NULL, '2026-05-14', 'active', '2026-05-14 06:37:15'),
(9, 20, 9, '2011', 'anay', 'male', '2011-06-14', 'btecch', 'ct', 2, '8789896687', 'anay@gmail.com', 'oihiheiheir', 'B+', NULL, '2026-05-14', 'active', '2026-05-14 08:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','parent','head') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `email`, `phone`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', 'admin@hostelhub.com', NULL, 'active', NULL, '2026-05-06 22:03:23'),
(3, 'parent', 'parent123', 'parent', 'parent@hostelhub.com', NULL, 'active', NULL, '2026-05-06 22:03:23'),
(7, 'sudhevan', '112112', 'parent', NULL, '07306257449', 'active', NULL, '2026-05-09 18:01:02'),
(9, 'gopi', '$2y$10$FXAsSeasB/ts61PzHLHz3eRKJFLR6.V9qGT10DqiRlMNSsk.Tj9DO', 'parent', NULL, '7306257445', 'active', NULL, '2026-05-13 17:27:38'),
(11, 'aswin', '$2y$10$YI2u8bhuL4tFbG48f/Kg2e0YdyWyJsY6IA2y7D9uCadtKLdaN8oUK', 'parent', NULL, '7306257449', 'active', NULL, '2026-05-13 18:06:29'),
(13, 'sreenivas', '$2y$10$hzlrTInUonHDDBFA9/v42up2.MEo8bT/X9v86IrCq/7Oc53hE.TQu', 'parent', NULL, '7306257449', 'active', NULL, '2026-05-13 18:08:34'),
(14, 'aswin1', '$2y$10$eHUXGV6aUbZS65AREYBDreKKgw1fcatOQvv24jGrcEZ.sdo7VByZG', 'student', 'aswin.sreenivas005@gmail.com', '7306257449', 'active', NULL, '2026-05-13 18:08:34'),
(15, 'babu', '$2y$10$QKZBmm/MkM23ssRr9J7X2utqKz001EcRDuI6.4zHG.cZjUCEJzDBi', 'parent', NULL, '2345678098', 'active', NULL, '2026-05-14 04:09:44'),
(16, 'kithu', '$2y$10$D7nQt3jlkGPOvBG678Qh3.QxjUCIp8ZzVYzUa9Pd5DrIQiHjOTfDu', 'student', 'aswin.sreenvas005@gmail.com', '2435678908', 'active', NULL, '2026-05-14 04:09:44'),
(17, 'sudheesh', '$2y$10$IMewCothY1rl/Cqo9yWT3ebh4Z986gtl6y8PjdtJpYFRNvJkW.ntK', 'parent', NULL, '9876545678', 'active', NULL, '2026-05-14 06:37:15'),
(18, 'aswinsu', '$2y$10$l2SMdn7nE2kMLmVA7y3IJ.b3EZcRrPfyAMvD7cO2v5lcP6atHjZHy', 'student', 'aswin.sras005@gmail.com', '0987654345', 'active', NULL, '2026-05-14 06:37:15'),
(19, 'anoop', '$2y$10$O2Gy0Qfeb0qVDjJyF2TxmeMbHHkbn3aeY1FBWzL0Q1emg8Lbq4dEe', 'parent', NULL, '6717865715', 'active', NULL, '2026-05-14 08:28:39'),
(20, 'anay', '12345', 'student', 'anay@gmail.com', '8789896687', 'active', NULL, '2026-05-14 08:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `visitor_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `id_proof` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `visitor_name`, `phone`, `student_id`, `relation`, `check_in`, `check_out`, `id_proof`, `remarks`, `created_at`) VALUES
(1, 'anusree', '8086090654', 4, 'Sister', '2026-05-14 06:01:01', '2026-05-14 06:04:15', NULL, NULL, '09:31:01'),
(2, 'wersdgtyhuj', '1234567890', 6, 'Sister', '2026-05-14 06:01:39', '2026-05-14 06:04:14', NULL, NULL, '09:31:39'),
(3, '=-uiyftudytfsgr', '5366767567', 3, 'Father', '2026-05-14 06:03:17', '2026-05-14 06:04:12', NULL, NULL, '09:33:17'),
(4, 'sudheesh', '9876543234', 8, 'Father', '2026-05-14 08:39:59', '2026-05-14 08:40:09', NULL, NULL, '12:09:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`);

--
-- Indexes for table `complaint_updates`
--
ALTER TABLE `complaint_updates`
  ADD PRIMARY KEY (`update_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`);

--
-- Indexes for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `food_ratings`
--
ALTER TABLE `food_ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `hostel_blocks`
--
ALTER TABLE `hostel_blocks`
  ADD PRIMARY KEY (`block_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_id`);

--
-- Indexes for table `mess_bills`
--
ALTER TABLE `mess_bills`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `mess_menu`
--
ALTER TABLE `mess_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`notice_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_allocations`
--
ALTER TABLE `room_allocations`
  ADD PRIMARY KEY (`allocation_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`visitor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint_updates`
--
ALTER TABLE `complaint_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `fee_payments`
--
ALTER TABLE `fee_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `food_ratings`
--
ALTER TABLE `food_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hostel_blocks`
--
ALTER TABLE `hostel_blocks`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mess_bills`
--
ALTER TABLE `mess_bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mess_menu`
--
ALTER TABLE `mess_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room_allocations`
--
ALTER TABLE `room_allocations`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
