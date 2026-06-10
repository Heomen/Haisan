<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo '<h2>🔧 Khởi tạo & Đồng bộ Cơ sở dữ liệu từ Localhost</h2>';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die('<p style="color:red">❌ Không thể kết nối database!</p>');
}

echo '<p style="color:green">✅ Kết nối database thành công!</p>';

// ============= BƯỚC 1: Xóa các bảng cũ để tránh xung đột =============
echo '<h3>Bước 1: Dọn dẹp dữ liệu cũ (Xóa bảng cũ)</h3>';
$db->exec('SET FOREIGN_KEY_CHECKS = 0');
$db->exec('DROP TABLE IF EXISTS `order_items`');
$db->exec('DROP TABLE IF EXISTS `orders`');
$db->exec('DROP TABLE IF EXISTS `login_logs`');
$db->exec('DROP TABLE IF EXISTS `salaries`');
$db->exec('DROP TABLE IF EXISTS `reviews`');
$db->exec('DROP TABLE IF EXISTS `customers`');
$db->exec('DROP TABLE IF EXISTS `reservations`');
$db->exec('DROP TABLE IF EXISTS `menu_items`');
$db->exec('DROP TABLE IF EXISTS `menu_categories`');
$db->exec('DROP TABLE IF EXISTS `employees`');
$db->exec('DROP TABLE IF EXISTS `shifts`');
$db->exec('DROP TABLE IF EXISTS `departments`');
$db->exec('DROP TABLE IF EXISTS `users`');
$db->exec('DROP TABLE IF EXISTS `roles`');
$db->exec('SET FOREIGN_KEY_CHECKS = 1');
echo '<p style="color:green">✅ Đã xóa tất cả các bảng cũ.</p>';

// ============= BƯỚC 2: Tạo lại tất cả các bảng từ schema Local =============
echo '<h3>Bước 2: Tạo các bảng mới với schema đồng bộ</h3>';
$db->exec('SET FOREIGN_KEY_CHECKS = 0');
$sql_roles = 'CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_roles);
    echo '<p>✅ Tạo bảng <b>roles</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>roles</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_users = 'CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_users);
    echo '<p>✅ Tạo bảng <b>users</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>users</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_departments = 'CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_departments_manager` (`manager_id`),
  CONSTRAINT `fk_departments_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_departments);
    echo '<p>✅ Tạo bảng <b>departments</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>departments</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_shifts = 'CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_shifts);
    echo '<p>✅ Tạo bảng <b>shifts</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>shifts</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_employees = 'CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum(\'Nam\',\'Nữ\',\'Khác\') DEFAULT \'Nam\',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_employees);
    echo '<p>✅ Tạo bảng <b>employees</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>employees</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_menu_categories = 'CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_menu_categories);
    echo '<p>✅ Tạo bảng <b>menu_categories</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>menu_categories</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_menu_items = 'CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_unit` varchar(50) DEFAULT \'đ/kg\',
  `image_url` varchar(255) DEFAULT \'https://via.placeholder.com/300x200?text=No+Image\',
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `status` enum(\'active\',\'inactive\') DEFAULT \'active\',
  `dish_1_name` varchar(100) DEFAULT NULL,
  `dish_1_price` varchar(100) DEFAULT NULL,
  `dish_1_image` varchar(255) DEFAULT NULL,
  `dish_2_name` varchar(100) DEFAULT NULL,
  `dish_2_price` varchar(100) DEFAULT NULL,
  `dish_2_image` varchar(255) DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `dish_1_is_favorite` tinyint(1) DEFAULT 0,
  `dish_2_is_favorite` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_menu_items);
    echo '<p>✅ Tạo bảng <b>menu_items</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>menu_items</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_reservations = 'CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum(\'pending\',\'confirmed\',\'completed\',\'cancelled\') DEFAULT \'pending\',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pre_order` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
try {
    $db->exec($sql_reservations);
    echo '<p>✅ Tạo bảng <b>reservations</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>reservations</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_customers = 'CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_customers);
    echo '<p>✅ Tạo bảng <b>customers</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>customers</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_reviews = 'CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_reviews);
    echo '<p>✅ Tạo bảng <b>reviews</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>reviews</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_salaries = 'CREATE TABLE `salaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine` decimal(12,2) NOT NULL DEFAULT 0.00,
  `salary_month` int(11) NOT NULL DEFAULT 5,
  `salary_year` int(11) NOT NULL DEFAULT 2026,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  UNIQUE KEY `employee_month_year` (`employee_id`,`salary_month`,`salary_year`),
  CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_salaries);
    echo '<p>✅ Tạo bảng <b>salaries</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>salaries</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_login_logs = 'CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `ip_address` varchar(50) DEFAULT \'Unknown\',
  `user_agent` varchar(255) DEFAULT \'Unknown\',
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT \'Thành công\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_login_logs);
    echo '<p>✅ Tạo bảng <b>login_logs</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>login_logs</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_orders = 'CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `customer_count` int(11) NOT NULL DEFAULT 1,
  `status` enum(\'Pending\',\'Completed\',\'Cancelled\') DEFAULT \'Completed\',
  `reservation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_orders);
    echo '<p>✅ Tạo bảng <b>orders</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>orders</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$sql_order_items = 'CREATE TABLE `order_items` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
try {
    $db->exec($sql_order_items);
    echo '<p>✅ Tạo bảng <b>order_items</b> - Thành công</p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Tạo bảng <b>order_items</b> thất bại: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$db->exec('SET FOREIGN_KEY_CHECKS = 1');
echo '<p style="color:green">✅ Hoàn tất tạo cấu trúc bảng.</p>';

// ============= BƯỚC 3: Đồng bộ toàn bộ dữ liệu từ các bảng local =============
echo '<h3>Bước 3: Đồng bộ dữ liệu</h3>';
$db->exec('SET FOREIGN_KEY_CHECKS = 0');
// Dữ liệu cho bảng roles
try {
    $stmt = $db->prepare('INSERT INTO `roles` (`id`, `name`, `description`) VALUES (:id, :name, :description)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'name' => 'Super Admin',
  'description' => 'Toàn quyền hệ thống',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'name' => 'Admin',
  'description' => 'Quyền thêm/sửa/xóa',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'name' => 'User',
  'description' => 'Chỉ xem',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>roles</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>roles</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng users
try {
    $stmt = $db->prepare('INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role_id`, `status`, `created_at`) VALUES (:id, :username, :password, :full_name, :email, :phone, :role_id, :status, :created_at)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'username' => 'superadmin',
  'password' => 'e10adc3949ba59abbe56e057f20f883e',
  'full_name' => 'Quản Trị Tối Cao',
  'email' => 'super@haisan.com',
  'phone' => '0123456789',
  'role_id' => '1',
  'status' => '1',
  'created_at' => '2026-05-14 11:08:23',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'username' => 'user',
  'password' => 'e10adc3949ba59abbe56e057f20f883e',
  'full_name' => 'Nhân Viên Xem',
  'email' => 'user@haisan.com',
  'phone' => '0112233445',
  'role_id' => '3',
  'status' => '1',
  'created_at' => '2026-05-14 11:08:23',
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'username' => 'admin',
  'password' => '827ccb0eea8a706c4c34a16891f84e7b',
  'full_name' => 'Quản Trị Viên',
  'email' => NULL,
  'phone' => NULL,
  'role_id' => '1',
  'status' => '1',
  'created_at' => '2026-05-14 16:53:10',
));
    $count++;
    $stmt->execute(array (
  'id' => '5',
  'username' => 'hoi',
  'password' => '202cb962ac59075b964b07152d234b70',
  'full_name' => 'omg',
  'email' => 'huuwebgywr@gmail.com',
  'phone' => '1232425235',
  'role_id' => '3',
  'status' => '1',
  'created_at' => '2026-05-21 08:25:11',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>users</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>users</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng departments
try {
    $stmt = $db->prepare('INSERT INTO `departments` (`id`, `code`, `name`, `manager_id`, `manager_name`) VALUES (:id, :code, :name, :manager_id, :manager_name)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'code' => 'BP01',
  'name' => 'Bếp',
  'manager_id' => '4',
  'manager_name' => NULL,
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'code' => 'BP02',
  'name' => 'Phục vụ',
  'manager_id' => '2',
  'manager_name' => NULL,
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'code' => 'BP03',
  'name' => 'Kho',
  'manager_id' => '5',
  'manager_name' => NULL,
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'code' => 'BP04',
  'name' => 'Thu ngân',
  'manager_id' => NULL,
  'manager_name' => NULL,
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>departments</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>departments</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng shifts
try {
    $stmt = $db->prepare('INSERT INTO `shifts` (`id`, `code`, `name`, `department_id`, `start_time`, `end_time`) VALUES (:id, :code, :name, :department_id, :start_time, :end_time)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'code' => 'CS-BEP',
  'name' => 'Ca sáng',
  'department_id' => '1',
  'start_time' => '06:00:00',
  'end_time' => '14:00:00',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'code' => 'CC-BEP',
  'name' => 'Ca chiều',
  'department_id' => '1',
  'start_time' => '14:00:00',
  'end_time' => '22:00:00',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>shifts</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>shifts</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng employees
try {
    $stmt = $db->prepare('INSERT INTO `employees` (`id`, `code`, `full_name`, `avatar`, `dob`, `gender`, `phone`, `email`, `address`, `department_id`, `position`) VALUES (:id, :code, :full_name, :avatar, :dob, :gender, :phone, :email, :address, :department_id, :position)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'code' => 'NV01',
  'full_name' => 'Tuấn đốc cờ Le',
  'avatar' => '6a0aa8e9c31bb.jpg',
  'dob' => '2004-03-15',
  'gender' => 'Nam',
  'phone' => '0901234567',
  'email' => 'hai.bep@haisan.com',
  'address' => '123 Đường Biển, Quận 1',
  'department_id' => '2',
  'position' => 'Bếp trưởng',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'code' => 'NV02',
  'full_name' => 'Bình hai Bi',
  'avatar' => '6a0aa8b3187fb.jpg',
  'dob' => '2004-05-28',
  'gender' => 'Nữ',
  'phone' => '0912345678',
  'email' => 'san.pv@haisan.com',
  'address' => '456 Cảng Cá, Quận 4',
  'department_id' => '2',
  'position' => 'Tổ trưởng phục vụ',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'code' => 'NV03',
  'full_name' => 'Thuận sờ mốc Kơ',
  'avatar' => '6a0aa937d2b88.jpg',
  'dob' => '2004-11-11',
  'gender' => 'Nam',
  'phone' => '0912345678',
  'email' => NULL,
  'address' => 'Vô gia cư',
  'department_id' => '3',
  'position' => NULL,
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'code' => 'NV04',
  'full_name' => 'Thuận bớt Đây',
  'avatar' => '6a0aa96e1c930.jpg',
  'dob' => '2004-03-12',
  'gender' => 'Nam',
  'phone' => '32553225',
  'email' => NULL,
  'address' => 'Vô gia cư',
  'department_id' => '1',
  'position' => NULL,
));
    $count++;
    $stmt->execute(array (
  'id' => '5',
  'code' => 'NV05',
  'full_name' => 'Thành mê Ngủ',
  'avatar' => '6a0aa9cb3bf6d.jpg',
  'dob' => '2004-09-12',
  'gender' => 'Nam',
  'phone' => '0912345678',
  'email' => NULL,
  'address' => 'Vô gia cư',
  'department_id' => '4',
  'position' => NULL,
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>employees</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>employees</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng menu_categories
try {
    $stmt = $db->prepare('INSERT INTO `menu_categories` (`id`, `name`) VALUES (:id, :name)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'name' => 'Hải sản tươi sống',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'name' => 'Đồ uống',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>menu_categories</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>menu_categories</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng menu_items
try {
    $stmt = $db->prepare('INSERT INTO `menu_items` (`id`, `name`, `price`, `price_unit`, `image_url`, `description`, `quantity`, `status`, `dish_1_name`, `dish_1_price`, `dish_1_image`, `dish_2_name`, `dish_2_price`, `dish_2_image`, `is_favorite`, `dish_1_is_favorite`, `dish_2_is_favorite`) VALUES (:id, :name, :price, :price_unit, :image_url, :description, :quantity, :status, :dish_1_name, :dish_1_price, :dish_1_image, :dish_2_name, :dish_2_price, :dish_2_image, :is_favorite, :dish_1_is_favorite, :dish_2_is_favorite)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'name' => 'Tôm Hùm Bông',
  'price' => '2500000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/tôm hùm.jpg',
  'description' => 'Tôm hùm tươi sống bơi tại bể',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Tôm hùm hấp',
  'dish_1_price' => '1.200.000 đ/con',
  'dish_1_image' => 'images/tôm hùm hấp.jpg',
  'dish_2_name' => 'Tôm hùm sốt',
  'dish_2_price' => '1.350.000 đ/con',
  'dish_2_image' => 'images/tôm hùm sốt .jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'name' => 'Cua Hoàng Đế',
  'price' => '3200000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/cua hoangde.jpg',
  'description' => 'Cua King Crab nhập khẩu',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Cua hoàng đế hấp',
  'dish_1_price' => '2.500.000 đ/con',
  'dish_1_image' => 'images/cua hoàng đế hấp.jpg',
  'dish_2_name' => 'Càng cua hấp',
  'dish_2_price' => '1.200.000 đ/dĩa',
  'dish_2_image' => 'images/càng cua hấp.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'name' => 'Cua Cà Mau',
  'price' => '850000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/cua ca mau.jpg',
  'description' => 'Cua gạch, cua thịt Cà Mau',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Cua rang muối',
  'dish_1_price' => '250.000 đ/đĩa',
  'dish_1_image' => 'images/cua rang muối.jpg',
  'dish_2_name' => 'Cua sốt me',
  'dish_2_price' => '280.000 đ/đĩa',
  'dish_2_image' => 'images/cua sốt me.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'name' => 'Bề Bề Chúa',
  'price' => '1200000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/be be .jpg',
  'description' => 'Bề bề loại lớn tươi sống',
  'quantity' => '10',
  'status' => 'active',
  'dish_1_name' => 'Bề bề hấp',
  'dish_1_price' => '350.000 đ/đĩa',
  'dish_1_image' => 'images/bề bề hấp.jpg',
  'dish_2_name' => 'Bề bề rang muối',
  'dish_2_price' => '380.000 đ/đĩa',
  'dish_2_image' => 'images/bề bề rang muối.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '1',
  'dish_2_is_favorite' => '1',
));
    $count++;
    $stmt->execute(array (
  'id' => '5',
  'name' => 'Cá Song Đen',
  'price' => '650000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/ca song.jpg',
  'description' => 'Cá song tươi sống',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Cá song chiên',
  'dish_1_price' => '450.000 đ/con',
  'dish_1_image' => 'images/cá song chiên.jpg',
  'dish_2_name' => 'Cá song kho',
  'dish_2_price' => '480.000 đ/nồi',
  'dish_2_image' => 'images/cá song kho.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '6',
  'name' => 'Cá Bơn Vàng',
  'price' => '1800000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/cá bơn.jpg',
  'description' => 'Cá bơn Hàn Quốc',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Cá bơn chiên',
  'dish_1_price' => '550.000 đ/con',
  'dish_1_image' => 'images/cá bơn chiên.jpg',
  'dish_2_name' => 'Cá bơn nướng',
  'dish_2_price' => '580.000 đ/con',
  'dish_2_image' => 'images/cá bơn nướng.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '7',
  'name' => 'Tu Hài Canada',
  'price' => '1100000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/tu hai canada.jpg',
  'description' => 'Tu hài nhập khẩu',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Tu hài hấp xả ớt',
  'dish_1_price' => '180.000 đ/đĩa',
  'dish_1_image' => 'images/tu hài hấp xả ớt.jpg',
  'dish_2_name' => 'Tu hài nướng mỡ hành',
  'dish_2_price' => '200.000 đ/đĩa',
  'dish_2_image' => 'images/tu hài nướng mỡ hành.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '8',
  'name' => 'Bào Ngư Hàn Quốc',
  'price' => '1250000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/bao ngu.jpg',
  'description' => 'Bào ngư tươi sống',
  'quantity' => '10',
  'status' => 'active',
  'dish_1_name' => 'Cháo bào ngư',
  'dish_1_price' => '150.000 đ/bát',
  'dish_1_image' => 'images/cháo bào ngư.jpg',
  'dish_2_name' => 'Bào ngư sống',
  'dish_2_price' => '300.000 đ/con',
  'dish_2_image' => 'images/bào ngư sống.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '1',
));
    $count++;
    $stmt->execute(array (
  'id' => '9',
  'name' => 'Ốc Hương',
  'price' => '650000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/oc hương.jpg',
  'description' => 'Ốc hương size lớn',
  'quantity' => '12',
  'status' => 'active',
  'dish_1_name' => 'Ốc luộc',
  'dish_1_price' => '120.000 đ/đĩa',
  'dish_1_image' => 'images/ốc luộc.jpg',
  'dish_2_name' => 'Ốc trứng muối',
  'dish_2_price' => '180.000 đ/đĩa',
  'dish_2_image' => 'images/ốc trứng muối.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    $stmt->execute(array (
  'id' => '10',
  'name' => 'Mực Ống',
  'price' => '350000.00',
  'price_unit' => 'đ/kg',
  'image_url' => 'images/muc ong.jpg',
  'description' => 'Mực ống tươi câu',
  'quantity' => '10',
  'status' => 'active',
  'dish_1_name' => 'Mực nhồi thịt',
  'dish_1_price' => '220.000 đ/đĩa',
  'dish_1_image' => 'images/mực nhồi thịt.jpg',
  'dish_2_name' => 'Mực xào dứa',
  'dish_2_price' => '190.000 đ/đĩa',
  'dish_2_image' => 'images/Mực xào rứa.jpg',
  'is_favorite' => '0',
  'dish_1_is_favorite' => '0',
  'dish_2_is_favorite' => '0',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>menu_items</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>menu_items</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng reservations
try {
    $stmt = $db->prepare('INSERT INTO `reservations` (`id`, `customer_name`, `phone`, `location_id`, `reservation_date`, `reservation_time`, `notes`, `status`, `created_at`, `pre_order`) VALUES (:id, :customer_name, :phone, :location_id, :reservation_date, :reservation_time, :notes, :status, :created_at, :pre_order)');
    $count = 0;
    $stmt->execute(array (
  'id' => '20',
  'customer_name' => 'Hợi Quang',
  'phone' => '0912345678',
  'location_id' => '1',
  'reservation_date' => '2026-06-08',
  'reservation_time' => '11:00:00',
  'notes' => '',
  'status' => 'completed',
  'created_at' => '2026-06-08 15:16:45',
  'pre_order' => 'Mực nhồi thịt, Mực xào dứa',
));
    $count++;
    $stmt->execute(array (
  'id' => '21',
  'customer_name' => 'Hợi Quang',
  'phone' => '0912345678',
  'location_id' => '1',
  'reservation_date' => '2026-06-08',
  'reservation_time' => '11:00:00',
  'notes' => '',
  'status' => 'completed',
  'created_at' => '2026-06-08 15:17:14',
  'pre_order' => 'Cháo bào ngư, Bào ngư sống',
));
    $count++;
    $stmt->execute(array (
  'id' => '22',
  'customer_name' => 'Hợi Quang',
  'phone' => '0912345678',
  'location_id' => '1',
  'reservation_date' => '2026-06-08',
  'reservation_time' => '11:00:00',
  'notes' => '',
  'status' => 'completed',
  'created_at' => '2026-06-08 15:17:47',
  'pre_order' => 'Bề bề hấp, Bề bề rang muối',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>reservations</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>reservations</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng customers
try {
    $stmt = $db->prepare('INSERT INTO `customers` (`id`, `username`, `password`, `full_name`, `phone`, `email`, `created_at`) VALUES (:id, :username, :password, :full_name, :phone, :email, :created_at)');
    $count = 0;
    $stmt->execute(array (
  'id' => '2',
  'username' => 'acd',
  'password' => 'e10adc3949ba59abbe56e057f20f883e',
  'full_name' => 'Hợi Quang',
  'phone' => '0912345678',
  'email' => 'hahhhh@gmail.com',
  'created_at' => '2026-05-21 08:52:41',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'username' => 'asd',
  'password' => 'e10adc3949ba59abbe56e057f20f883e',
  'full_name' => 'asd',
  'phone' => '0901234567',
  'email' => 'tranheo739@gmail.com',
  'created_at' => '2026-06-05 15:02:38',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>customers</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>customers</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng reviews
try {
    $stmt = $db->prepare('INSERT INTO `reviews` (`id`, `customer_id`, `rating`, `comment`, `status`, `created_at`) VALUES (:id, :customer_id, :rating, :comment, :status, :created_at)');
    $count = 0;
    $stmt->execute(array (
  'id' => '3',
  'customer_id' => '2',
  'rating' => '1',
  'comment' => 'Tôm hơi zai :))',
  'status' => '1',
  'created_at' => '2026-06-10 10:49:13',
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'customer_id' => '3',
  'rating' => '1',
  'comment' => 'Cua hơi nhiều chân',
  'status' => '1',
  'created_at' => '2026-06-10 10:50:02',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>reviews</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>reviews</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng salaries
try {
    $stmt = $db->prepare('INSERT INTO `salaries` (`id`, `employee_id`, `base_salary`, `allowance`, `fine`, `salary_month`, `salary_year`) VALUES (:id, :employee_id, :base_salary, :allowance, :fine, :salary_month, :salary_year)');
    $count = 0;
    $stmt->execute(array (
  'id' => '1',
  'employee_id' => '4',
  'base_salary' => '15000000.00',
  'allowance' => '2000000.00',
  'fine' => '500000.00',
  'salary_month' => '5',
  'salary_year' => '2026',
));
    $count++;
    $stmt->execute(array (
  'id' => '2',
  'employee_id' => '1',
  'base_salary' => '12000000.00',
  'allowance' => '1500000.00',
  'fine' => '200000.00',
  'salary_month' => '5',
  'salary_year' => '2026',
));
    $count++;
    $stmt->execute(array (
  'id' => '3',
  'employee_id' => '2',
  'base_salary' => '20000000.00',
  'allowance' => '100000.00',
  'fine' => '5000000.00',
  'salary_month' => '5',
  'salary_year' => '2026',
));
    $count++;
    $stmt->execute(array (
  'id' => '4',
  'employee_id' => '3',
  'base_salary' => '200000.00',
  'allowance' => '20000.00',
  'fine' => '100000.00',
  'salary_month' => '5',
  'salary_year' => '2026',
));
    $count++;
    $stmt->execute(array (
  'id' => '5',
  'employee_id' => '5',
  'base_salary' => '1000000.00',
  'allowance' => '100000.00',
  'fine' => '100000.00',
  'salary_month' => '5',
  'salary_year' => '2026',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>salaries</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>salaries</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng login_logs
try {
    $stmt = $db->prepare('INSERT INTO `login_logs` (`id`, `user_type`, `username`, `full_name`, `ip_address`, `user_agent`, `login_time`, `status`) VALUES (:id, :user_type, :username, :full_name, :ip_address, :user_agent, :login_time, :status)');
    $count = 0;
    $stmt->execute(array (
  'id' => '5',
  'user_type' => 'Khách hàng',
  'username' => 'acd',
  'full_name' => 'Hợi Quang',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-05-21 14:58:37',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '6',
  'user_type' => 'Khách hàng',
  'username' => 'acd',
  'full_name' => 'Hợi Quang',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-05-21 16:38:38',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '7',
  'user_type' => 'Quản trị',
  'username' => 'admin',
  'full_name' => 'Quản Trị Viên',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-05-22 19:28:27',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '8',
  'user_type' => 'Quản trị',
  'username' => 'admin',
  'full_name' => 'Quản Trị Viên',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-05-22 19:41:08',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '18',
  'user_type' => 'Quản trị',
  'username' => 'admin',
  'full_name' => 'Quản Trị Viên',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 09:30:24',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '19',
  'user_type' => 'Quản trị',
  'username' => 'admin',
  'full_name' => 'Quản Trị Viên',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 10:33:09',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '20',
  'user_type' => 'Quản trị',
  'username' => 'admin',
  'full_name' => 'Quản Trị Viên',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 10:48:03',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '21',
  'user_type' => 'Khách hàng',
  'username' => 'acd',
  'full_name' => 'Hợi Quang',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 10:48:27',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '22',
  'user_type' => 'Khách hàng',
  'username' => 'asd',
  'full_name' => 'asd',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 10:49:29',
  'status' => 'Thành công',
));
    $count++;
    $stmt->execute(array (
  'id' => '23',
  'user_type' => 'Khách hàng',
  'username' => 'acd',
  'full_name' => 'Hợi Quang',
  'ip_address' => '::1',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
  'login_time' => '2026-06-10 10:52:14',
  'status' => 'Thành công',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>login_logs</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>login_logs</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng orders
try {
    $stmt = $db->prepare('INSERT INTO `orders` (`id`, `order_date`, `total_amount`, `customer_count`, `status`, `reservation_id`) VALUES (:id, :order_date, :total_amount, :customer_count, :status, :reservation_id)');
    $count = 0;
    $stmt->execute(array (
  'id' => '144',
  'order_date' => '2026-06-08 11:00:00',
  'total_amount' => '410000.00',
  'customer_count' => '4',
  'status' => 'Completed',
  'reservation_id' => '19',
));
    $count++;
    $stmt->execute(array (
  'id' => '145',
  'order_date' => '2026-06-08 11:00:00',
  'total_amount' => '410000.00',
  'customer_count' => '4',
  'status' => 'Completed',
  'reservation_id' => '20',
));
    $count++;
    $stmt->execute(array (
  'id' => '148',
  'order_date' => '2026-06-08 11:00:00',
  'total_amount' => '730000.00',
  'customer_count' => '4',
  'status' => 'Completed',
  'reservation_id' => '22',
));
    $count++;
    $stmt->execute(array (
  'id' => '149',
  'order_date' => '2026-06-08 11:00:00',
  'total_amount' => '450000.00',
  'customer_count' => '4',
  'status' => 'Completed',
  'reservation_id' => '21',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>orders</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>orders</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Dữ liệu cho bảng order_items
try {
    $stmt = $db->prepare('INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES (:id, :order_id, :menu_item_id, :quantity, :price)');
    $count = 0;
    $stmt->execute(array (
  'id' => '369',
  'order_id' => '144',
  'menu_item_id' => '10',
  'quantity' => '1',
  'price' => '220000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '370',
  'order_id' => '144',
  'menu_item_id' => '10',
  'quantity' => '1',
  'price' => '190000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '371',
  'order_id' => '145',
  'menu_item_id' => '10',
  'quantity' => '1',
  'price' => '220000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '372',
  'order_id' => '145',
  'menu_item_id' => '10',
  'quantity' => '1',
  'price' => '190000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '377',
  'order_id' => '148',
  'menu_item_id' => '4',
  'quantity' => '1',
  'price' => '350000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '378',
  'order_id' => '148',
  'menu_item_id' => '4',
  'quantity' => '1',
  'price' => '380000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '379',
  'order_id' => '149',
  'menu_item_id' => '8',
  'quantity' => '1',
  'price' => '150000.00',
));
    $count++;
    $stmt->execute(array (
  'id' => '380',
  'order_id' => '149',
  'menu_item_id' => '8',
  'quantity' => '1',
  'price' => '300000.00',
));
    $count++;
    echo '<p style="color:green">✅ Đã đồng bộ ' . $count . ' dòng dữ liệu vào bảng <b>order_items</b></p>';
} catch (PDOException $e) {
    echo '<p style="color:red">❌ Lỗi đồng bộ dữ liệu bảng <b>order_items</b>: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

$db->exec('SET FOREIGN_KEY_CHECKS = 1');
echo '<br><div style="padding:15px; background-color:#e6ffe6; border:1px solid green; border-radius:5px;">';
echo '🎉 <b>Hoàn tất đồng bộ dữ liệu quản lý!</b> <a href="index.php"><b>→ Về trang chủ</b></a>';
echo '</div>';
