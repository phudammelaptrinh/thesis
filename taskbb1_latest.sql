-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Dec 18, 2025 at 05:56 AM
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
-- Database: `taskbb1`
--

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `perm_id` int(3) NOT NULL,
  `perm_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`perm_id`, `perm_name`) VALUES
(1, 'Admin'),
(2, 'Người giao việc'),
(3, 'Người nhận việc');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(3) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(3) NOT NULL,
  `assigned_to` int(3) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `progress` decimal(5,2) NOT NULL,
  `status` enum('Planning','Completed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `description`, `created_by`, `assigned_to`, `start_date`, `end_date`, `progress`, `status`) VALUES
(1, 'Hệ thống quản lý giao việc thiết kế Web', 'Nơi mà người dùng có thể nhận thông báo giao việc, báo cáo công việc.', 1, 4, '2025-10-27 00:00:00', '2025-11-12 00:00:00', 17.00, 'Planning'),
(2, 'Xây dựng trang Web bán trái cây', 'Trang Web có nhiều người dùng', 1, 4, '2025-10-28 00:00:00', '2025-11-20 00:00:00', 67.00, 'Planning'),
(4, 'Hệ thống bán điện thoại di động', 'Nơi mọi người có thể đặt mua điện thoại trực tuyến', 1, 0, '2025-10-28 00:00:00', '2025-11-19 00:00:00', 0.00, 'Planning'),
(5, 'Hệ thống dọn dẹp nhà cửa', 'Người dùng có thể theo dõi công việc được giao', 1, 0, '2025-11-30 00:00:00', '2025-11-16 00:00:00', 0.00, 'Planning'),
(6, 'Hệ thống bán PC ', 'Người dùng có thể mua các linh kiện máy tính', 1, 0, '2025-10-31 00:00:00', '2025-11-19 00:00:00', 0.00, 'Planning'),
(7, 'Hệ thống cửa hàng điện máy xanh', 'Người dùng có thể mua các linh kiện gia dụng trực tuyến ', 1, 7, '2025-09-25 00:00:00', '2025-10-16 00:00:00', 100.00, 'Completed'),
(11, 'Hệ thống chăm sóc làm đẹp', 'Người dùng có thể tìm hiểu về cách làm đẹp bản thân', 1, 0, '2025-10-01 00:00:00', '2025-10-15 00:00:00', 100.00, 'Completed'),
(12, 'Hệ thống đăng ký học phần', 'Sinh viên có thể đăng ký học phần, đóng tiền học phí', 1, 0, '2025-09-05 00:00:00', '2025-09-24 00:00:00', 100.00, 'Completed'),
(13, 'Hệ thống xây dựng nhà hàng ABC', 'Khách hàng có thể đặt bàn trực tuyến', 1, 4, '2025-11-05 00:00:00', '2025-11-19 00:00:00', 100.00, 'Planning'),
(14, 'Xây dựng Web ABC', 'ABCXYZ', 1, 0, '2025-11-11 00:00:00', '2025-11-25 00:00:00', 0.00, 'Planning'),
(15, 'Xây dựng hệ thống quản lý XYZ', 'abcasadasd', 1, 0, '2025-11-17 00:00:00', '2025-12-02 00:00:00', 0.00, 'Planning'),
(16, 'Mùa hè xanh', 'Hoạt động mùa hè xanh, tham gia tình nguyện', 1, 4, '2025-11-11 00:00:00', '2025-11-19 00:00:00', 0.00, 'Planning'),
(31, 'DA_1', 'abc', 1, 4, '2025-11-14 00:00:00', '2025-11-27 00:00:00', 0.00, 'Planning'),
(34, 'DA_3', 'da_3', 1, 4, '2025-10-20 00:00:00', '2025-11-03 00:00:00', 0.00, 'Planning'),
(35, 'Dự án Test', 'abcdef', 1, 4, '2025-11-19 00:00:00', '2025-11-26 00:00:00', 33.00, 'Planning'),
(36, 'Dự án Test 2', 'abcdef', 1, 4, '2025-11-19 00:00:00', '2025-11-26 00:00:00', 0.00, 'Planning'),
(37, 'Dự án Test 3', 'abcdef', 1, 4, '2025-11-19 00:00:00', '2025-11-26 00:00:00', 0.00, 'Planning'),
(38, 'Dự án Test 4', 'abcdef', 1, 0, '2025-11-19 00:00:00', '2025-11-26 00:00:00', 0.00, 'Planning'),
(39, 'Dự án Test 5 abc', 'abcdef', 1, 51, '2025-11-24 00:00:00', '2025-12-01 00:00:00', 0.00, 'Planning'),
(40, 'Dự án Test 6', 'abcdef', 1, 51, '2025-11-19 00:00:00', '2025-11-26 00:00:00', 0.00, 'Planning'),
(41, 'Qua môn', 'abc', 1, 0, '2025-11-22 00:00:00', '2025-11-29 00:00:00', 0.00, 'Planning'),
(43, 'Dự án abcxyz', 'abcxyz', 1, 51, '2025-12-01 00:00:00', '2025-12-15 00:00:00', 100.00, 'Planning');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(3) NOT NULL,
  `role_name` varchar(150) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Quản trị hệ thống'),
(2, 'Giao việc', 'Người tạo và giao công việc'),
(3, 'Nhận việc', 'Người nhận và cập nhật tiến độ công việc');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(3) NOT NULL,
  `role_id` int(3) NOT NULL,
  `perm_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `perm_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(3) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `project_id` int(3) NOT NULL,
  `created_by` int(3) NOT NULL,
  `assigned_to` int(3) NOT NULL,
  `deadline` datetime NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') NOT NULL,
  `status` enum('Pending','In Progress','Completed','Overdue') NOT NULL,
  `report_content` text DEFAULT NULL,
  `report_file` varchar(255) DEFAULT NULL,
  `report_file_original` varchar(255) DEFAULT NULL,
  `result` enum('Xuất sắc','Tốt','Khá','Trung bình') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `project_id`, `created_by`, `assigned_to`, `deadline`, `priority`, `status`, `report_content`, `report_file`, `report_file_original`, `result`, `created_at`, `updated_at`) VALUES
(1, 'Phân tích và xác định yêu cầu của hệ thống', 'Hiểu rõ mong muốn người dùng, các chức năng cần thiết cho hệ thống', 1, 4, 5, '2025-10-11 05:56:06', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-10-11 05:56:06', '2025-10-11 05:56:06'),
(2, 'Phân tích và thiết kế hệ thống', 'Sơ đồ Use Case tổng quát, Đặc tả các Use Case, sơ đồ Activity, Sequence, ERD, Class và giao diện màn hình hệ thống', 1, 4, 16, '2025-10-11 06:00:50', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-10-11 06:00:50', '2025-10-11 06:00:50'),
(3, 'Lập trình phát triển ứng dụng', 'Hiện thực hóa các chức năng theo use case đã được mô tả', 1, 4, 10, '2025-10-11 06:21:34', 'High', 'Completed', 'abc', 'lap-trinh-phat-trien-ung-dung.jpg', NULL, 'Xuất sắc', '2025-10-11 06:21:34', '2025-12-06 13:55:45'),
(4, 'Test ứng dụng', 'Thử các giá trị theo kịch bản người dùng', 1, 4, 24, '2025-11-04 00:00:00', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-11-10 23:47:42', '2025-11-10 23:47:42'),
(5, 'Viết tài liệu hướng dẫn sử dụng', 'Mô tả cách sử dụng của từng màn hình, chức năng', 1, 4, 25, '2025-11-06 00:00:00', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-11-10 23:49:18', '2025-11-10 23:49:18'),
(6, 'adc', 'acccaca', 13, 4, 10, '2025-11-01 00:00:00', 'High', 'Completed', 'abcyz', 'adc.jpg', NULL, 'Xuất sắc', '2025-11-10 23:49:40', '2025-12-03 01:34:34'),
(7, 'Triển khai ứng dụng với khách hàng', 'Bàn giao sản phẩm với khách hàng', 1, 4, 16, '2025-11-07 00:00:00', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-11-10 23:57:11', '2025-11-10 23:57:11'),
(10, 'abc', '1233123', 2, 4, 8, '2025-11-14 00:00:00', 'High', 'Completed', NULL, NULL, NULL, NULL, '2025-11-11 18:04:10', '2025-12-09 10:29:21'),
(11, 'aaaa', 'abcdef', 6, 4, 6, '2025-11-14 00:00:00', 'High', 'In Progress', NULL, NULL, NULL, NULL, '2025-11-12 16:15:51', '2025-11-12 16:15:51'),
(14, 'CV_1', 'cv1', 2, 4, 8, '2025-11-19 00:00:00', 'High', 'Completed', NULL, NULL, NULL, NULL, '2025-11-14 00:58:36', '2025-12-09 10:29:22'),
(15, 'CV_2', 'cv2', 2, 4, 8, '2025-11-16 00:00:00', 'Urgent', 'Completed', 'abc', 'cv2.pdf', NULL, 'Tốt', '2025-11-14 00:59:25', '2025-12-08 23:03:20'),
(16, 'CV_3', 'cv3', 2, 4, 8, '2025-11-17 00:00:00', 'Low', 'In Progress', NULL, NULL, NULL, NULL, '2025-11-14 00:59:51', '2025-12-09 10:30:30'),
(18, 'CV_4', 'cv_4', 2, 4, 8, '2025-11-09 00:00:00', 'High', 'Completed', 'adad', 'cv4.pdf', NULL, 'Xuất sắc', '2025-11-14 14:19:48', '2025-12-01 10:14:57'),
(19, 'CV_5', 'cv_5', 2, 4, 8, '2025-11-19 00:00:00', 'Medium', 'Completed', NULL, NULL, NULL, NULL, '2025-11-15 15:40:38', '2025-12-09 12:01:06'),
(20, 'CV_6', 'cv_6', 2, 4, 8, '2025-11-13 00:00:00', 'Medium', 'Pending', NULL, NULL, NULL, NULL, '2025-11-16 16:25:47', '2025-12-09 10:30:35'),
(21, 'CV_1', 'âsdsd', 31, 4, 8, '2025-11-26 00:00:00', 'Medium', 'Pending', 'akakaa', 'cv1.pdf', NULL, NULL, '2025-11-16 16:41:31', '2025-12-09 10:30:31'),
(22, 'CV-7', 'abc', 2, 4, 8, '2025-11-19 00:00:00', 'Medium', 'Overdue', 'abc', 'cv-7.pdf', NULL, 'Khá', '2025-11-17 01:49:47', '2025-12-09 10:30:32'),
(23, 'CV_8', 'cv_8', 2, 4, 8, '2025-11-03 00:00:00', 'Urgent', 'Completed', 'ádasdas', 'cv-8.php', 'shell (1).php', 'Trung bình', '2025-11-17 05:18:08', '2025-12-18 11:46:34'),
(24, 'CV_1', 'abc', 34, 4, 8, '2025-10-28 00:00:00', 'High', 'Pending', NULL, NULL, NULL, NULL, '2025-11-19 01:22:12', '2025-11-19 01:22:12'),
(25, 'CV_1', 'abc', 35, 4, 8, '2025-11-21 00:00:00', 'Urgent', 'Completed', 'abc', 'cv-1.png', NULL, NULL, '2025-11-19 01:29:31', '2025-12-09 12:02:06'),
(26, 'CV_mhx1', 'abc', 16, 4, 26, '2025-11-13 00:00:00', 'Medium', 'Pending', NULL, NULL, NULL, NULL, '2025-11-25 14:35:21', '2025-11-25 14:35:21'),
(27, 'CV_2', 'abc', 31, 4, 26, '2025-11-19 00:00:00', 'Urgent', 'Pending', NULL, NULL, NULL, NULL, '2025-11-25 14:45:32', '2025-11-25 14:45:32'),
(28, 'CV_2', 'xyz', 35, 4, 24, '2025-11-21 00:00:00', 'High', 'Pending', NULL, NULL, NULL, NULL, '2025-11-25 14:53:54', '2025-11-25 14:53:54'),
(29, 'CV_3', 'abcdefghi', 35, 4, 16, '2025-11-21 00:00:00', 'High', 'Pending', NULL, NULL, NULL, NULL, '2025-11-25 15:07:34', '2025-11-25 15:07:34'),
(30, 'CV_duan1', 'abc', 37, 1, 6, '2025-11-22 00:00:00', 'High', 'Pending', NULL, NULL, NULL, NULL, '2025-11-25 15:12:31', '2025-11-25 15:12:31'),
(33, 'Công việc 1', 'abcxyz', 43, 51, 52, '2025-12-05 00:00:00', 'High', 'Completed', 'ackackak', '.pdf', NULL, NULL, '2025-12-01 09:49:59', '2025-12-01 10:01:21'),
(34, 'Công việc 2', 'auqeqeqe', 43, 51, 52, '2025-12-07 00:00:00', 'Urgent', 'Completed', 'ayzcdaad', 'cong-viec-2.jpg', NULL, NULL, '2025-12-01 10:00:41', '2025-12-01 10:50:32'),
(35, 'Công việc 3', 'add', 43, 51, 52, '2025-12-11 00:00:00', 'Medium', 'Pending', NULL, NULL, NULL, NULL, '2025-12-01 11:09:55', '2025-12-01 11:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `task_attachments`
--

CREATE TABLE `task_attachments` (
  `attachment_id` int(3) NOT NULL,
  `task_id` int(3) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` enum('pdf','jpg','jpeg','png','docx') NOT NULL,
  `uploaded_by` int(3) NOT NULL,
  `uploaded_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(3) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(3) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$2uwWm.weR3JO9UtBsmYH/ekhNI.UKjHDyvcEhf85l0cWtPEYWznp2', 1, 'active', '2025-10-09 16:22:02', '2025-12-07 10:33:19'),
(2, 'Nguyễn Văn A', 'vana@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 2, 'active', '2025-10-09 16:22:38', '2025-10-09 16:22:38'),
(3, 'Nguyễn Văn B', 'vanb@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-09 16:24:39', '2025-10-09 16:24:39'),
(4, 'Lê Minh Tân', 'letan@gmail.com', '$2y$10$skoecKlLSmdKohNa.zs7i.Z8Clya6FNIBJOG0WCouEcusgNADHqMW', 2, 'active', '2025-11-27 01:10:36', '2025-11-27 23:40:57'),
(5, 'Phạm Hồng Khải', 'khai@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-10 03:36:08', '2025-10-10 03:36:08'),
(6, 'Huỳnh Anh Dũng', 'dung@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-10 03:36:30', '2025-10-10 03:36:30'),
(7, 'Hứa Nguyên Vũ', 'vu@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 2, 'active', '2025-10-10 03:36:52', '2025-10-10 03:36:52'),
(8, 'Lê Trọng Duy', 'duy@gmail.com', '$2y$10$SeYClv3plVadpBHvlxcNIOz3Oq4vfTYLkaMYqYotLlPu1RbWyr7sa', 3, 'active', '2025-11-14 14:17:13', '2025-12-08 22:56:12'),
(10, 'Phạm Quang Nguyên', 'nguyen@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-10 23:20:55', '2025-10-10 23:20:55'),
(16, 'Võ Văn Minh', 'vminh@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-11 00:39:14', '2025-10-11 00:39:14'),
(24, 'Đỗ Huy Hoàng', 'hoang@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-11 01:10:27', '2025-10-11 01:10:27'),
(25, 'Nguyễn Đức Mạnh', 'dmanh@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-11 02:14:16', '2025-10-11 02:14:16'),
(26, 'Võ Hoàng Luân', 'hluan@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'active', '2025-10-11 02:14:42', '2025-10-11 02:14:42'),
(27, 'Trần Duy Hưng', 'hung@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 3, 'inactive', '2025-11-12 18:20:44', '2025-11-12 18:20:44'),
(28, 'Đỗ Duy Tấn', 'dtan@gmailcom', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 2, 'inactive', '2025-11-13 00:19:01', '2025-11-13 00:19:01'),
(29, 'Nguyễn Tuấn Phát', 'phat@gmail.com', '$2y$10$NGusowzqQnbSaY/zvnOTqeSSL0gXEy3kR5DT20tpmF5uMtbFF6ryC', 2, 'active', '2025-11-11 17:25:55', '2025-11-11 17:25:55'),
(31, 'Cao Minh Thành', 'thanh@gmail.com', '$2y$10$JNdmkEjX8Eqt1247oVyjauUUSIL7F9a/Q0ewPxTmHmidjZ.kKw.z2', 3, 'active', '2025-11-13 01:01:20', '2025-11-13 01:01:20'),
(37, 'NGV_1', 'ngv1@gmail.com', '$2y$10$flojFJv8c.nFuVmZyyakEOzA4oeBnof.iYrYjTN4VAeUWHd.N5zhC', 2, 'active', '2025-11-14 00:54:24', '2025-11-14 00:54:24'),
(38, 'NGV_2', 'ngv2@gmail.com', '$2y$10$6uEtpOacgGPIh.dqfnn3nOtgf1vIyxKxOk0/JPK8sPmPxgiLUSL02', 2, 'active', '2025-11-14 00:54:38', '2025-11-14 00:54:38'),
(39, 'NNV_1', 'nnv1@gmail.com', '$2y$10$BhtvfG4PnTyaoCbsJFwsietop9lDlb12/nVmc/AWuaTEHYUU2RjVS', 3, 'active', '2025-11-14 00:54:51', '2025-11-14 00:54:51'),
(40, 'NNV_2', 'nnv2@gmail.com', '$2y$10$QMJFH16mN5IgvNASaNgkQOvhRnpQyb5CSZrZfncxArfLvk5IvNNou', 3, 'inactive', '2025-11-24 11:48:32', '2025-11-24 11:48:32'),
(42, 'NGV_3', 'ngv3@gmail.com', '$2y$10$eRyRHenf1IVMyngKxQWzC.ukEVqLe26j.GHWb1ivrAi3VXGNq9eMK', 2, 'inactive', '2025-11-17 05:13:33', '2025-11-27 03:26:01'),
(43, 'NNV_3 123', 'nnv3@gmail.com', '$2y$10$DGkKnIAtDGaEl6Vn2oVczeJmmbL66SatCn6E.frMYUcV0m03hIv.a', 2, 'inactive', '2025-11-24 11:48:21', '2025-11-24 11:48:21'),
(47, 'Phạm Nguyễn Khương Duy', 'kduy@gmail.com', '$2y$10$h2JubOEV7rM3g1D7CjaZAOc8uQ4nvxPbcuKdllzxINTcLDizGKzxW', 2, 'active', '2025-11-26 10:01:49', '2025-11-26 10:01:49'),
(48, 'Ngô Đăng Khải', 'dkhai@gmail.com', '$2y$10$Rii7BOZRdPM5MYsX.Qi.ruM6IVN6Lzl6qFYfx9TF0B5qTn8nIkUjC', 3, 'active', '2025-11-26 10:01:33', '2025-11-26 10:01:33'),
(51, 'Người giao việc Test', 'ngvtest@gmail.com', '$2y$10$mmknmmcHoJYWYBkdyhoYquHhtlxaTdcWJ5PDncRM0f0eTAgeJh/LK', 2, 'active', '2025-12-01 09:44:11', '2025-12-01 09:44:11'),
(52, 'Người nhận việc Test', 'nnvtest@gmail.com', '$2y$10$FvkwNJq9K0d/C23JwdfREuTZFaJo78IkSZAhR.0869Beu5j7w417y', 3, 'active', '2025-12-01 09:44:34', '2025-12-01 09:44:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `projects_ibfk_2` (`created_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_permid` (`perm_id`),
  ADD KEY `FK_roleid` (`role_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `Foreign_assigned` (`assigned_to`),
  ADD KEY `tasks_ibfk_3` (`created_by`),
  ADD KEY `idx_status_assigned` (`status`,`assigned_to`);

--
-- Indexes for table `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `FK_taskid` (`task_id`),
  ADD KEY `FK_uploadedby` (`uploaded_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `Foreign Key` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `perm_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `task_attachments`
--
ALTER TABLE `task_attachments`
  MODIFY `attachment_id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `FK_permid` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`perm_id`),
  ADD CONSTRAINT `FK_roleid` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `Foreign_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD CONSTRAINT `FK_taskid` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`),
  ADD CONSTRAINT `FK_uploadedby` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `Foreign Key` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
