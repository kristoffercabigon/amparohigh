-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2024 at 04:55 AM
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
-- Database: `amparohigh`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `commentor` varchar(75) NOT NULL,
  `request_id` int(11) NOT NULL,
  `reference_no` varchar(100) NOT NULL,
  `registrar_id` int(11) NOT NULL,
  `requestor_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `commented_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_type` tinyint(4) NOT NULL,
  `registrar_seen` tinyint(4) NOT NULL,
  `requestor_seen` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `commentor`, `request_id`, `reference_no`, `registrar_id`, `requestor_id`, `comment_text`, `commented_at`, `user_type`, `registrar_seen`, `requestor_seen`) VALUES
(1, 'Krizza Lyn', 29, '20240822', 63, 29, 'fasdfasf', '2024-08-25 10:56:40', 1, 1, 0),
(2, 'Krizza Lyn', 29, '20240822', 63, 29, 'fasdfasfa', '2024-08-25 10:56:42', 1, 1, 0),
(3, 'Krizza Lyn', 29, '20240822', 63, 29, 'fasdfasfa', '2024-08-25 10:56:43', 1, 1, 0),
(4, 'Krizza Lyn', 29, '20240822lHqc7', 63, 29, 'fsafasdfasfs', '2024-08-25 12:42:55', 1, 1, 0),
(5, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasfaf', '2024-08-25 09:41:03', 0, 0, 1),
(6, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fsdafasfasdf', '2024-08-25 09:41:01', 0, 0, 1),
(7, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasfasf', '2024-08-25 09:41:09', 0, 0, 1),
(8, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasfas', '2024-08-25 09:40:51', 0, 0, 1),
(9, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasfafas', '2024-08-25 09:41:07', 0, 0, 1),
(10, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasf', '2024-08-25 09:41:12', 0, 0, 1),
(11, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasdfas', '2024-08-25 09:40:55', 0, 0, 1),
(12, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasfasfdda', '2024-08-25 09:40:58', 0, 0, 1),
(13, 'Krizza Lyn', 29, '20240822lHqc7', 63, 29, 'fsadfasfasf', '2024-08-25 12:25:27', 1, 1, 0),
(14, 'Krizza Lyn', 29, '20240822lHqc7', 63, 29, 'fasfasfasf', '2024-08-25 11:13:43', 1, 1, 0),
(15, 'Dianne Reyes', 29, 'Array', 63, 29, 'fsdfasfasf', '2024-08-25 09:44:50', 0, 0, 0),
(16, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'fasdfasdf', '2024-08-25 09:45:49', 0, 0, 0),
(17, 'Dianne Reyes', 29, '20240822lHqc7', 63, 29, 'hansel', '2024-08-25 09:46:02', 0, 0, 0),
(18, 'Jessa Mahinon', 32, '202408241k25v', 63, 32, 'fasdfasdf', '2024-08-25 12:25:16', 1, 1, 0),
(19, 'Lara Montenegro', 22, '20240822Fd4xe', 64, 22, 'maam', '2024-08-26 01:27:39', 1, 0, 0),
(20, 'Lara Montenegro', 22, '20240822Fd4xe', 64, 22, 'maaam', '2024-08-26 01:27:49', 1, 0, 0),
(21, 'Lara Montenegro', 22, '20240822Fd4xe', 64, 22, 'maaaaam', '2024-08-26 01:27:56', 1, 0, 0),
(22, 'John Ibayan', 27, '20240822W9I17', 64, 27, 'maam?', '2024-08-26 01:51:18', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `docus`
--

CREATE TABLE `docus` (
  `id` int(11) NOT NULL,
  `documents` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `docus`
--

INSERT INTO `docus` (`id`, `documents`) VALUES
(1, 'Form 137/ SF10'),
(2, 'Card/ Form 138/ SF9'),
(3, 'Good Moral'),
(4, 'Copy of Diploma'),
(5, 'Upper Percentage'),
(6, 'GWA Certification'),
(7, 'Certificate of Enrollment'),
(8, 'Certificate of Graduation/ Completion'),
(9, 'CAV - Certification, Authentication, Verification'),
(10, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `docu_status`
--

CREATE TABLE `docu_status` (
  `id` int(11) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `docu_status`
--

INSERT INTO `docu_status` (`id`, `status`) VALUES
(1, 'Waiting'),
(2, 'Processing'),
(3, 'Missing'),
(4, 'On hold'),
(5, 'Ready'),
(6, 'Released');

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `id` int(11) NOT NULL,
  `level` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`id`, `level`) VALUES
(1, 'Junior High School'),
(2, 'Senior High School');

-- --------------------------------------------------------

--
-- Table structure for table `purpose`
--

CREATE TABLE `purpose` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `purposelist_id` int(11) NOT NULL,
  `purpose_remarks` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purpose`
--

INSERT INTO `purpose` (`id`, `request_id`, `purposelist_id`, `purpose_remarks`) VALUES
(2, 2, 1, 'UCC'),
(3, 3, 1, 'UCC'),
(6, 6, 1, 'UCC'),
(11, 11, 1, 'UCC'),
(13, 13, 1, 'UCC'),
(15, 15, 1, 'UCC'),
(16, 16, 1, 'UCC'),
(17, 17, 1, 'UCC'),
(20, 20, 1, 'OLFU'),
(21, 21, 1, 'UCC'),
(22, 22, 1, 'Philsca'),
(23, 23, 1, 'OLFU'),
(24, 24, 1, 'OLFU'),
(25, 25, 2, 'Abroad'),
(26, 26, 2, 'Abroad'),
(27, 27, 2, 'Abroad'),
(28, 28, 1, 'UCC'),
(29, 28, 3, NULL),
(30, 28, 4, NULL),
(31, 29, 1, 'UP'),
(32, 30, 3, NULL),
(33, 32, 3, NULL),
(34, 32, 4, NULL),
(35, 33, 3, NULL),
(36, 33, 4, NULL),
(37, 34, 3, NULL),
(38, 34, 4, NULL),
(39, 35, 1, 'UCC'),
(40, 36, 2, 'Abroad');

-- --------------------------------------------------------

--
-- Table structure for table `purposelist`
--

CREATE TABLE `purposelist` (
  `id` int(11) NOT NULL,
  `purposelists` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purposelist`
--

INSERT INTO `purposelist` (`id`, `purposelists`) VALUES
(1, 'for School Requirement'),
(2, 'for Employment'),
(3, 'for VISA Applicaiton'),
(4, 'for TESDA Applicaiton'),
(5, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `registrar`
--

CREATE TABLE `registrar` (
  `id` int(11) NOT NULL,
  `reg_name` varchar(50) NOT NULL,
  `reg_image` varchar(255) NOT NULL,
  `reg_email` varchar(75) NOT NULL,
  `reg_password` varchar(75) NOT NULL,
  `verification_code` varchar(50) DEFAULT NULL,
  `verified_at` text DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrar`
--

INSERT INTO `registrar` (`id`, `reg_name`, `reg_image`, `reg_email`, `reg_password`, `verification_code`, `verified_at`, `token`, `expires`) VALUES
(58, 'Rose Angcao', 'images/ANGCAO.png', 'roseangcao@gmail.com', '123asd', '982345', '2024-08-21 08:34:05', NULL, NULL),
(59, 'Elaine Aring', 'images/ARING.png', 'elaingaring@gmail.com', '123asd', '578913', '2024-08-21 08:34:05', NULL, NULL),
(60, 'Daniel Santa', 'images/DANIEL.png', 'danielsanta@gmail.com', '123asd', '378125', '2024-08-21 08:34:05', NULL, NULL),
(61, 'Rowelyn Gudio', 'images/GUDIO.png', 'rowelyngudio@gmail.com', '123asd', '341982', '2024-08-21 08:34:05', NULL, NULL),
(62, 'Belle Guillermo', 'images/GUILLERMO.png', 'belleguillermo@gmail.com', '123asd', '197852', '2024-08-21 08:34:05', NULL, NULL),
(63, 'Dianne Reyes', 'images/REYES.png', 'diannereyes@gmail.com', '123asd', '175482', '2024-08-21 08:34:05', NULL, NULL),
(64, 'Jelly Solanoy', 'images/SALANOY.png', 'jellysolanoy@gmail.com', '123asd', '247815', '2024-08-21 08:34:05', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE `relationship` (
  `id` int(11) NOT NULL,
  `relationship` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationship`
--

INSERT INTO `relationship` (`id`, `relationship`) VALUES
(1, 'I\'m the student'),
(2, 'Father'),
(3, 'Mother'),
(4, 'Uncle'),
(5, 'Aunt'),
(6, 'Sibling'),
(7, 'Cousin'),
(8, 'Son'),
(9, 'Daughter'),
(10, 'Nephew'),
(11, 'Niece'),
(12, 'Grandchild'),
(13, 'Wife'),
(14, 'Husband'),
(15, 'Friend');

-- --------------------------------------------------------

--
-- Table structure for table `reqdocu`
--

CREATE TABLE `reqdocu` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `docus_id` int(11) NOT NULL,
  `document_remarks` varchar(25) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `document_status_id` int(11) DEFAULT NULL,
  `note_notif` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reqdocu`
--

INSERT INTO `reqdocu` (`id`, `request_id`, `docus_id`, `document_remarks`, `notes`, `document_status_id`, `note_notif`) VALUES
(2, 2, 2, NULL, 'nak kunin mo na', 2, 0),
(3, 3, 2, NULL, 'wala dito nak', 2, 0),
(6, 6, 2, NULL, '', 2, 0),
(15, 11, 1, NULL, '', 1, 0),
(16, 11, 2, NULL, '', 1, 0),
(19, 13, 1, NULL, '', 2, 0),
(20, 13, 2, NULL, '', 2, 0),
(23, 15, 1, NULL, '', 2, 0),
(24, 15, 2, NULL, '', 2, 0),
(25, 16, 1, NULL, '', 1, 0),
(26, 16, 2, NULL, '', 1, 0),
(27, 17, 1, NULL, '', 2, 0),
(28, 17, 2, NULL, '', 2, 0),
(33, 20, 2, NULL, '', 2, 0),
(34, 21, 1, NULL, '', 1, 0),
(35, 22, 3, NULL, '', 2, 0),
(36, 23, 2, NULL, '', 1, 0),
(37, 24, 1, NULL, '', 1, 0),
(38, 25, 1, NULL, '', 1, 0),
(39, 26, 1, NULL, '', 2, 0),
(40, 27, 1, NULL, 'nak wala dito yung form 137, nareleased na ata. di ko mahanap e', 2, 0),
(41, 28, 1, NULL, '', 2, 0),
(42, 28, 2, NULL, '', 2, 0),
(43, 28, 6, NULL, '', 2, 0),
(44, 29, 1, NULL, '', 2, 0),
(45, 30, 2, NULL, 'Di mahanap yung card mo nak, baka nasa adviser mo pa', 3, 0),
(46, 32, 1, NULL, '', 2, 0),
(47, 32, 2, NULL, '', 2, 0),
(48, 33, 1, NULL, NULL, 2, 0),
(49, 33, 2, NULL, 'baka nasa adviser mo pa yung card', 2, 0),
(50, 34, 1, NULL, '', 1, 0),
(51, 34, 2, NULL, '', 1, 0),
(52, 35, 3, NULL, '', 1, 0),
(53, 35, 4, NULL, '', 1, 0),
(54, 36, 2, NULL, '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `requestor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assisted_by_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `pin` int(11) NOT NULL,
  `request_date` datetime NOT NULL,
  `sent_email_at` datetime DEFAULT NULL,
  `date_released` datetime DEFAULT NULL,
  `upload_requirements` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `status_id`, `level_id`, `requestor_id`, `student_id`, `assisted_by_id`, `reference_no`, `pin`, `request_date`, `sent_email_at`, `date_released`, `upload_requirements`) VALUES
(2, 2, 2, 2, 2, 62, '20240818GlyT4', 9073, '2024-08-18 06:45:26', NULL, NULL, 1),
(3, 2, 2, 3, 3, 62, '20240818SVlJd', 6417, '2024-08-18 06:48:08', NULL, NULL, 0),
(6, 2, 2, 6, 6, 63, '20240818ZqMGi', 1864, '2024-08-18 07:17:47', NULL, NULL, 0),
(11, 1, 1, 11, 11, NULL, '20240819tQXzN', 6428, '2024-08-19 21:08:33', NULL, NULL, 0),
(13, 1, 1, 13, 13, NULL, '20240819ZZajY', 5083, '2024-08-19 21:09:07', NULL, NULL, 0),
(15, 1, 1, 15, 15, NULL, '20240819XBMHh', 1853, '2024-08-19 21:09:20', NULL, NULL, 0),
(16, 1, 1, 16, 16, NULL, '20240819rmL27', 1554, '2024-08-19 21:09:26', NULL, NULL, 0),
(17, 1, 1, 17, 17, NULL, '20240819OaQTP', 3219, '2024-08-19 21:09:30', NULL, NULL, 0),
(20, 2, 1, 20, 20, 63, '20240822paJjd', 680, '2024-08-22 07:58:42', NULL, NULL, 0),
(21, 1, 1, 21, 21, NULL, '202408221LVud', 8378, '2024-08-22 08:02:04', NULL, NULL, 0),
(22, 2, 1, 22, 22, 64, '20240822Fd4xe', 9117, '2024-08-22 08:04:53', NULL, NULL, 1),
(23, 1, 2, 23, 23, NULL, '20240822cKAl0', 4989, '2024-08-22 13:54:27', NULL, NULL, 0),
(24, 1, 2, 24, 24, NULL, '20240822SWcoY', 5667, '2024-08-22 15:09:10', NULL, NULL, 0),
(25, 1, 1, 25, 25, NULL, '20240822rXuQ2', 7478, '2024-08-22 15:12:04', NULL, NULL, 0),
(26, 2, 2, 26, 26, 64, '20240822C3wwD', 9531, '2024-08-22 15:14:25', NULL, NULL, 0),
(27, 2, 1, 27, 27, 64, '20240822W9I17', 1695, '2024-08-22 15:16:16', NULL, NULL, 1),
(28, 2, 1, 28, 28, 63, '20240822afnzh', 3434, '2024-08-22 15:25:41', NULL, NULL, 1),
(29, 2, 1, 29, 29, 63, '20240822lHqc7', 8886, '2024-08-22 15:29:33', NULL, NULL, 1),
(30, 2, 2, 30, 30, 63, '20240822qooUb', 7141, '2024-08-22 15:31:18', NULL, NULL, 1),
(32, 2, 1, 32, 32, 63, '202408241k25v', 5492, '2024-08-24 08:42:56', NULL, NULL, 1),
(33, 2, 1, 33, 33, 63, '20240824wuC2T', 7432, '2024-08-24 08:45:11', NULL, NULL, 1),
(34, 1, 1, 34, 34, NULL, '20240826DQDXJ', 9732, '2024-08-26 10:30:17', NULL, NULL, 0),
(35, 1, 1, 35, 35, NULL, '20240826J7ojk', 2577, '2024-08-26 10:32:31', NULL, NULL, 0),
(36, 1, 1, 36, 36, NULL, '20240826CaM1u', 6895, '2024-08-26 10:34:12', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `requestor`
--

CREATE TABLE `requestor` (
  `id` int(11) NOT NULL,
  `req_name` varchar(100) NOT NULL,
  `relationship_id` int(11) NOT NULL,
  `req_contact_no` varchar(25) NOT NULL,
  `req_email` varchar(100) NOT NULL,
  `signature` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requestor`
--

INSERT INTO `requestor` (`id`, `req_name`, `relationship_id`, `req_contact_no`, `req_email`, `signature`) VALUES
(2, 'Leonardo Vicente', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240818GlyT4.jpeg'),
(3, 'John Kennedy', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240818SVlJd.jpeg'),
(6, 'Sandy Jones', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240818ZqMGi.jpeg'),
(11, 'Clark Arthur', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240819tQXzN.jpeg'),
(13, 'Clark Arthur', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240819ZZajY.jpeg'),
(15, 'Clark Arthur', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240819XBMHh.jpeg'),
(16, 'Clark Arthur', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240819rmL27.jpeg'),
(17, 'Clark Arthur', 1, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240819OaQTP.jpeg'),
(20, 'Shaira Divina', 3, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240822paJjd.jpeg'),
(21, 'Antonio David', 3, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/202408221LVud.jpeg'),
(22, 'Lara Montenegro', 3, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240822Fd4xe.jpeg'),
(23, 'James Dylan', 2, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240822cKAl0.jpeg'),
(24, 'Arthur De Leon', 2, '09278147238', 'kristoffercabigon@gmail.com', 'images/signatures/20240822SWcoY.jpeg'),
(25, 'Jenny Lasegna', 6, '09278144565', 'jennylasegna@gmail.com', 'images/signatures/20240822rXuQ2.jpeg'),
(26, 'Gabriel Dimalanta', 7, '09456465487', 'gabriel01@gmail.com', 'images/signatures/20240822C3wwD.jpeg'),
(27, 'John Ibayan', 10, '09354321321', 'johnibayan@gmail.com', 'images/signatures/20240822W9I17.jpeg'),
(28, 'Justine Reyes', 2, '09324651321', 'justinereyes@gmail.com', 'images/signatures/20240822afnzh.jpeg'),
(29, 'Krizza Lyn', 13, '09875631296', 'krizzalyn@gmail.com', 'images/signatures/20240822lHqc7.jpeg'),
(30, 'Hermes Perez', 4, '09576457654', 'hermesperez@gmail.com', 'images/signatures/20240822qooUb.jpeg'),
(32, 'Jessa Mahinon', 3, '09213216541', 'jessamahinon@gmail.com', 'images/signatures/202408241k25v.jpeg'),
(33, 'Xyrine Mangubat', 3, '09213216541', 'jessamahinon@gmail.com', 'images/signatures/20240824wuC2T.jpeg'),
(34, 'Jessa Mahinon', 3, '09256423143', 'jessamahinon@gmail.com', 'images/signatures/20240826DQDXJ.jpeg'),
(35, 'Hazel Manlangit', 3, '09321313546', 'hazel@gmail.com', 'images/signatures/20240826J7ojk.jpeg'),
(36, 'Iza Mercado', 5, '09213246546', 'iza@gmail.com', 'images/signatures/20240826CaM1u.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status`) VALUES
(1, 'In queue'),
(2, 'Processing'),
(3, 'On hold'),
(4, 'Ready to claim'),
(5, 'Released');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `stud_lastname` varchar(100) NOT NULL,
  `stud_firstname` varchar(100) NOT NULL,
  `stud_midname` varchar(100) DEFAULT NULL,
  `stud_suffix` varchar(25) DEFAULT NULL,
  `grade` varchar(100) NOT NULL,
  `section` varchar(25) NOT NULL,
  `sylastattended` varchar(25) NOT NULL,
  `stud_contact_no` varchar(50) DEFAULT NULL,
  `stud_email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `stud_lastname`, `stud_firstname`, `stud_midname`, `stud_suffix`, `grade`, `section`, `sylastattended`, `stud_contact_no`, `stud_email`) VALUES
(2, 'Arcega', 'John', 'Delacruz', 'Jr.', 'fdasasfasd', 'Humss - A', '2023 - 2024', '09278147238', 'k.tofferorig@gmail.com'),
(3, 'Evans', 'Samantha', NULL, NULL, 'fdasasfasd', 'Humss - A', '2023 - 2024', '09278147238', 'k.tofferorig@gmail.com'),
(6, 'Lambo', 'Clark', NULL, NULL, 'Grade 12', 'ICT - A', '2023 - 2024', '09278147238', 'k.tofferorig@gmail.com'),
(11, 'Cabigon', 'Kristoffer', NULL, NULL, '10', 'Mabini', '2023 - 2024', NULL, NULL),
(13, 'Cabigon', 'Kristoffer', NULL, NULL, '10', 'Mabini', '2023 - 2024', NULL, NULL),
(15, 'Cabigon', 'Kristoffer', NULL, NULL, '10', 'Mabini', '2023 - 2024', NULL, NULL),
(16, 'Cabigon', 'Kristoffer', NULL, NULL, '10', 'Mabini', '2023 - 2024', NULL, NULL),
(17, 'Cabigon', 'Kristoffer', NULL, NULL, '10', 'Mabini', '2023 - 2024', NULL, NULL),
(20, 'Santiago', 'Jan-jan', NULL, NULL, '10', 'A', '2022 - 2024', NULL, NULL),
(21, 'Smith', 'Jason', NULL, NULL, '10', 'A', '2022 - 2024', NULL, NULL),
(22, 'Smith', 'Jason', NULL, NULL, '10', 'A', '2022 - 2024', NULL, NULL),
(23, 'Ibarra', 'Jarren', NULL, NULL, '10', 'A', '2022 - 2024', NULL, NULL),
(24, 'Naig', 'Joey', NULL, NULL, '10', 'A', '2022 - 2024', NULL, NULL),
(25, 'Lasegna', 'Hans', NULL, NULL, '9', 'J', '2020 - 2021', NULL, NULL),
(26, 'Dimalanta', 'Chris', NULL, NULL, '12', 'Stem - C', '2021 - 2022', NULL, NULL),
(27, 'Ibayan', 'Lander', NULL, NULL, '8', 'A', '2020 - 2021', NULL, NULL),
(28, 'Reyes', 'Lance', NULL, NULL, '7', 'Z', '2022 - 2023', NULL, NULL),
(29, 'Lyn', 'Dave', NULL, NULL, '12', 'A', '2023-2024', NULL, NULL),
(30, 'Perez', 'Cassandra', NULL, NULL, '11', 'ABM -B', '2022-2023', NULL, NULL),
(32, 'Mahinon', 'Jaspher', NULL, NULL, '10', 'B', '2023 - 2024', NULL, NULL),
(33, 'Mangubat', 'Andrea', NULL, NULL, '10', 'B', '2023 - 2024', NULL, NULL),
(34, 'Mahinon', 'Jaspher', NULL, NULL, '10', 'B', '2023-2024', NULL, NULL),
(35, 'Manlangit', 'John Carlo', NULL, NULL, '9', 'Rizal', '2020 - 2021', NULL, NULL),
(36, 'Mercado', 'Bernard', NULL, NULL, '8', 'Z', '2021 - 2022', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_docus`
--

CREATE TABLE `uploaded_docus` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `req_name` varchar(255) NOT NULL,
  `reference_no` varchar(255) NOT NULL,
  `registrar_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `filetype` varchar(100) NOT NULL,
  `reg_seen` tinyint(4) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_docus`
--

INSERT INTO `uploaded_docus` (`id`, `request_id`, `req_name`, `reference_no`, `registrar_id`, `filename`, `filesize`, `filetype`, `reg_seen`, `upload_date`) VALUES
(1, 32, 'Jessa Mahinon', '202408241k25v', 63, '454257187_4574165026055534_3555687334801931765_n.jpg', 129320, 'image/jpeg', 0, '2024-08-26 02:52:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registrar_id` (`registrar_id`),
  ADD KEY `requestor_id` (`requestor_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `docus`
--
ALTER TABLE `docus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `docu_status`
--
ALTER TABLE `docu_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purpose`
--
ALTER TABLE `purpose`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `purposes` (`purposelist_id`);

--
-- Indexes for table `purposelist`
--
ALTER TABLE `purposelist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrar`
--
ALTER TABLE `registrar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relationship`
--
ALTER TABLE `relationship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reqdocu`
--
ALTER TABLE `reqdocu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `docus_id` (`docus_id`),
  ADD KEY `reqdocu_ibfk_3` (`document_status_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `requestor_id` (`requestor_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `assisted_by_id` (`assisted_by_id`);

--
-- Indexes for table `requestor`
--
ALTER TABLE `requestor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grade_id` (`grade`);

--
-- Indexes for table `uploaded_docus`
--
ALTER TABLE `uploaded_docus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `registrar_id` (`registrar_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `docus`
--
ALTER TABLE `docus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `docu_status`
--
ALTER TABLE `docu_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purpose`
--
ALTER TABLE `purpose`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `purposelist`
--
ALTER TABLE `purposelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrar`
--
ALTER TABLE `registrar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `relationship`
--
ALTER TABLE `relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reqdocu`
--
ALTER TABLE `reqdocu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `requestor`
--
ALTER TABLE `requestor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `uploaded_docus`
--
ALTER TABLE `uploaded_docus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`registrar_id`) REFERENCES `registrar` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`requestor_id`) REFERENCES `requestor` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`);

--
-- Constraints for table `purpose`
--
ALTER TABLE `purpose`
  ADD CONSTRAINT `purpose_ibfk_1` FOREIGN KEY (`purposelist_id`) REFERENCES `purposelist` (`id`),
  ADD CONSTRAINT `purpose_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`);

--
-- Constraints for table `reqdocu`
--
ALTER TABLE `reqdocu`
  ADD CONSTRAINT `reqdocu_ibfk_1` FOREIGN KEY (`document_status_id`) REFERENCES `docu_status` (`id`),
  ADD CONSTRAINT `reqdocu_ibfk_2` FOREIGN KEY (`docus_id`) REFERENCES `docus` (`id`),
  ADD CONSTRAINT `reqdocu_ibfk_3` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`);

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`assisted_by_id`) REFERENCES `registrar` (`id`),
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`),
  ADD CONSTRAINT `request_ibfk_3` FOREIGN KEY (`requestor_id`) REFERENCES `requestor` (`id`),
  ADD CONSTRAINT `request_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`),
  ADD CONSTRAINT `request_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`);

--
-- Constraints for table `uploaded_docus`
--
ALTER TABLE `uploaded_docus`
  ADD CONSTRAINT `uploaded_docus_ibfk_1` FOREIGN KEY (`registrar_id`) REFERENCES `registrar` (`id`),
  ADD CONSTRAINT `uploaded_docus_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
