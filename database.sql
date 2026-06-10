-- phpMyAdmin SQL Dump
-- Database: `haisan_db`

CREATE DATABASE IF NOT EXISTS `haisan_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `haisan_db`;

-- --------------------------------------------------------

-- Table structure for table `roles`
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL, -- Super Admin, Admin, User
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Toàn quyền hệ thống'),
(2, 'Admin', 'Quyền thêm/sửa/xóa'),
(3, 'User', 'Chỉ xem');

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1, -- 1: Active, 0: Locked
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mật khẩu mặc định là '123456' (đã hash md5 hoặc bcrypt)
-- Ở đây tạm dùng md5('123456') = 'e10adc3949ba59abbe56e057f20f883e'
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role_id`, `status`) VALUES
(1, 'superadmin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản Trị Tối Cao', 'super@haisan.com', '0123456789', 1, 1),
(2, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản Trị Viên', 'admin@haisan.com', '0987654321', 2, 1),
(3, 'user', 'e10adc3949ba59abbe56e057f20f883e', 'Nhân Viên Xem', 'user@haisan.com', '0112233445', 3, 1);

-- --------------------------------------------------------

-- Table structure for table `departments`
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL, -- Bếp, Phục vụ, Kho, Thu ngân...
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `code`, `name`) VALUES
(1, 'BP01', 'Bếp'),
(2, 'BP02', 'Phục vụ'),
(3, 'BP03', 'Kho'),
(4, 'BP04', 'Thu ngân');

-- --------------------------------------------------------

-- Table structure for table `shifts`
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL, -- Ca sáng, Ca chiều, Ca tối
  `department_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shifts` (`id`, `code`, `name`, `department_id`, `start_time`, `end_time`) VALUES
(1, 'CS-BEP', 'Ca sáng', 1, '06:00:00', '14:00:00'),
(2, 'CC-BEP', 'Ca chiều', 1, '14:00:00', '22:00:00');

-- --------------------------------------------------------

-- Table structure for table `employees`
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL, -- Vị trí: Bếp trưởng, Phục vụ bàn...
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employees` (`id`, `code`, `full_name`, `dob`, `gender`, `phone`, `email`, `address`, `department_id`, `position`) VALUES
(1, 'NV001', 'Nguyễn Văn Hải', '1990-05-15', 'Nam', '0901234567', 'hai.bep@haisan.com', '123 Đường Biển, Quận 1', 1, 'Bếp trưởng'),
(2, 'NV002', 'Trần Thị San', '1995-08-20', 'Nữ', '0912345678', 'san.pv@haisan.com', '456 Cảng Cá, Quận 4', 2, 'Tổ trưởng phục vụ');

-- --------------------------------------------------------

-- Bảng giả định cho Dashboard (Thống kê)
CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menu_categories` (`id`, `name`) VALUES (1, 'Hải sản tươi sống'), (2, 'Đồ uống');

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `price`, `quantity`) VALUES
(1, 1, 'Tôm hùm Alaska nướng bơ tỏi', 1500000.00, 10),
(2, 1, 'Cua Hoàng Đế rang me', 2000000.00, 15),
(3, 2, 'Bia Heineken', 25000.00, 100);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `customer_count` int(11) NOT NULL DEFAULT 1,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Completed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `order_date`, `total_amount`, `customer_count`, `status`) VALUES
(1, '2023-10-01 18:30:00', 3500000.00, 4, 'Completed'),
(2, '2023-10-01 19:00:00', 1550000.00, 2, 'Completed'),
(3, '2023-10-02 12:00:00', 25000.00, 1, 'Completed');

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 1500000.00),
(2, 1, 2, 1, 2000000.00),
(3, 2, 1, 1, 1500000.00),
(4, 2, 3, 2, 25000.00),
(5, 3, 3, 1, 25000.00);
