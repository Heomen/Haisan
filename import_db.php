<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "<h2>🔧 Khởi tạo và Sửa lỗi Database</h2>";

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("<p style='color:red'>❌ Không thể kết nối database!</p>");
}

echo "<p style='color:green'>✅ Kết nối database thành công!</p>";

// ============= BƯỚC 1: Xóa các bảng cũ để tạo lại đúng schema =============
echo "<h3>Bước 1: Dọn dẹp dữ liệu cũ (Xóa bảng cũ)</h3>";
$db->exec("SET FOREIGN_KEY_CHECKS = 0");

$tables_to_drop = [
    'order_items', 'orders', 'menu_items', 'menu_categories', 
    'reviews', 'salaries', 'login_logs', 'employees', 
    'shifts', 'departments', 'reservations', 'customers', 'users', 'roles'
];

foreach ($tables_to_drop as $table) {
    try {
        $db->exec("DROP TABLE IF EXISTS `$table`");
        echo "<p>🗑️ Đã xóa bảng <b>$table</b></p>";
    } catch (PDOException $e) {
        echo "<p style='color:orange'>⚠️ Lỗi khi xóa bảng $table: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
$db->exec("SET FOREIGN_KEY_CHECKS = 1");

// ============= BƯỚC 2: Tạo lại tất cả các bảng với schema chính xác =============
echo "<h3>Bước 2: Tạo các bảng mới</h3>";

$tables = [
    "roles" => "CREATE TABLE `roles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `description` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "users" => "CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) DEFAULT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `role_id` int(11) NOT NULL DEFAULT 1,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "departments" => "CREATE TABLE `departments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `code` varchar(20) NOT NULL,
        `name` varchar(100) NOT NULL,
        `manager_name` varchar(255) NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "shifts" => "CREATE TABLE `shifts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `code` varchar(20) NOT NULL,
        `name` varchar(100) NOT NULL,
        `department_id` int(11) NOT NULL,
        `start_time` time DEFAULT NULL,
        `end_time` time DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "employees" => "CREATE TABLE `employees` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `code` varchar(20) NOT NULL,
        `full_name` varchar(100) NOT NULL,
        `avatar` varchar(255) NULL,
        `dob` date DEFAULT NULL,
        `gender` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
        `phone` varchar(20) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `address` varchar(255) DEFAULT NULL,
        `department_id` int(11) DEFAULT NULL,
        `position` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "menu_items" => "CREATE TABLE `menu_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `price` DECIMAL(10,2) NOT NULL,
        `price_unit` VARCHAR(50) DEFAULT 'đ/kg',
        `image_url` VARCHAR(255),
        `description` TEXT,
        `quantity` INT NOT NULL DEFAULT 0,
        `status` ENUM('active', 'inactive') DEFAULT 'active',
        `dish_1_name` VARCHAR(255) DEFAULT NULL,
        `dish_1_price` VARCHAR(100) DEFAULT NULL,
        `dish_1_image` VARCHAR(255) DEFAULT NULL,
        `dish_2_name` VARCHAR(255) DEFAULT NULL,
        `dish_2_price` VARCHAR(100) DEFAULT NULL,
        `dish_2_image` VARCHAR(255) DEFAULT NULL,
        `is_favorite` TINYINT(1) DEFAULT 0,
        `dish_1_is_favorite` TINYINT(1) DEFAULT 0,
        `dish_2_is_favorite` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "reservations" => "CREATE TABLE `reservations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_name` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `location_id` INT,
        `reservation_date` DATE NOT NULL,
        `reservation_time` TIME NOT NULL,
        `notes` TEXT,
        `pre_order` VARCHAR(255) DEFAULT NULL,
        `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "customers" => "CREATE TABLE `customers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) UNIQUE NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `email` VARCHAR(100) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "reviews" => "CREATE TABLE `reviews` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_id` INT NOT NULL,
        `rating` INT NOT NULL DEFAULT 5,
        `comment` TEXT,
        `status` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "salaries" => "CREATE TABLE `salaries` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `employee_id` INT NOT NULL,
        `month` INT NOT NULL,
        `year` INT NOT NULL,
        `basic_salary` DECIMAL(12,2) DEFAULT 0,
        `bonus` DECIMAL(12,2) DEFAULT 0,
        `deduction` DECIMAL(12,2) DEFAULT 0,
        `total` DECIMAL(12,2) DEFAULT 0,
        `notes` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "login_logs" => "CREATE TABLE `login_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `login_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `ip_address` VARCHAR(50),
        `user_agent` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "menu_categories" => "CREATE TABLE `menu_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "orders" => "CREATE TABLE `orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_date` datetime NOT NULL DEFAULT current_timestamp(),
        `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
        `customer_count` int(11) NOT NULL DEFAULT 1,
        `status` enum('Pending','Completed','Cancelled') DEFAULT 'Completed',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "order_items" => "CREATE TABLE `order_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_id` int(11) NOT NULL,
        `menu_item_id` int(11) NOT NULL,
        `quantity` int(11) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tables as $name => $sql) {
    try {
        $db->exec($sql);
        echo "<p>✅ Tạo bảng <b>$name</b> - Thành công</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>❌ Tạo bảng <b>$name</b> thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// ============= BƯỚC 3: Nhập dữ liệu mẫu =============
echo "<h3>Bước 3: Nhập dữ liệu mẫu</h3>";

// Roles
try {
    $db->exec("INSERT INTO `roles` (`id`, `name`, `description`) VALUES
        (1, 'Super Admin', 'Toàn quyền hệ thống'),
        (2, 'Admin', 'Quyền thêm/sửa/xóa'),
        (3, 'User', 'Chỉ xem')");
    echo "<p>✅ Nhập dữ liệu <b>roles</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập roles thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Users
try {
    $db->exec("INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role_id`, `status`) VALUES
        (1, 'superadmin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản Trị Tối Cao', 'super@haisan.com', '0123456789', 1, 1),
        (2, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản Trị Viên', 'admin@haisan.com', '0987654321', 2, 1),
        (3, 'user', 'e10adc3949ba59abbe56e057f20f883e', 'Nhân Viên Xem', 'user@haisan.com', '0112233445', 3, 1)");
    echo "<p>✅ Nhập dữ liệu <b>users</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập users thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Departments
try {
    $db->exec("INSERT INTO `departments` (`id`, `code`, `name`) VALUES
        (1, 'BP01', 'Bếp'),
        (2, 'BP02', 'Phục vụ'),
        (3, 'BP03', 'Kho'),
        (4, 'BP04', 'Thu ngân')");
    echo "<p>✅ Nhập dữ liệu <b>departments</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập departments thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Shifts
try {
    $db->exec("INSERT INTO `shifts` (`id`, `code`, `name`, `department_id`, `start_time`, `end_time`) VALUES
        (1, 'CS-BEP', 'Ca sáng', 1, '06:00:00', '14:00:00'),
        (2, 'CC-BEP', 'Ca chiều', 1, '14:00:00', '22:00:00')");
    echo "<p>✅ Nhập dữ liệu <b>shifts</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập shifts thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Employees
try {
    $db->exec("INSERT INTO `employees` (`id`, `code`, `full_name`, `dob`, `gender`, `phone`, `email`, `address`, `department_id`, `position`) VALUES
        (1, 'NV001', 'Nguyễn Văn Hải', '1990-05-15', 'Nam', '0901234567', 'hai.bep@haisan.com', '123 Đường Biển, Quận 1', 1, 'Bếp trưởng'),
        (2, 'NV002', 'Trần Thị San', '1995-08-20', 'Nữ', '0912345678', 'san.pv@haisan.com', '456 Cảng Cá, Quận 4', 2, 'Tổ trưởng phục vụ')");
    echo "<p>✅ Nhập dữ liệu <b>employees</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập employees thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Menu Items (Correct full schema)
try {
    $items = [
        ['Tôm Hùm Bông', 2500000, 'đ/kg', 'images/tôm hùm.jpg', 'Tôm hùm tươi sống bơi tại bể', 'Tôm hùm hấp', '1.200.000 đ/con', 'images/tôm hùm hấp.jpg', 'Tôm hùm sốt', '1.350.000 đ/con', 'images/tôm hùm sốt .jpg'],
        ['Cua Hoàng Đế', 3200000, 'đ/kg', 'images/cua hoangde.jpg', 'Cua King Crab nhập khẩu', 'Cua hoàng đế hấp', '2.500.000 đ/con', 'images/cua hoàng đế hấp.jpg', 'Càng cua hấp', '1.200.000 đ/dĩa', 'images/càng cua hấp.jpg'],
        ['Cua Cà Mau', 850000, 'đ/kg', 'images/cua ca mau.jpg', 'Cua gạch, cua thịt Cà Mau', 'Cua rang muối', '250.000 đ/đĩa', 'images/cua rang muối.jpg', 'Cua sốt me', '280.000 đ/đĩa', 'images/cua sốt me.jpg'],
        ['Bề Bề Chúa', 1200000, 'đ/kg', 'images/be be .jpg', 'Bề bề loại lớn tươi sống', 'Bề bề hấp', '350.000 đ/đĩa', 'images/bề bề hấp.jpg', 'Bề bề rang muối', '380.000 đ/đĩa', 'images/bề bề rang muối.jpg'],
        ['Cá Song Đen', 650000, 'đ/kg', 'images/ca song.jpg', 'Cá song tươi sống', 'Cá song chiên', '450.000 đ/con', 'images/cá song chiên.jpg', 'Cá song kho', '480.000 đ/nồi', 'images/cá song kho.jpg'],
        ['Cá Bơn Vàng', 1800000, 'đ/kg', 'images/cá bơn.jpg', 'Cá bơn Hàn Quốc', 'Cá bơn chiên', '550.000 đ/con', 'images/cá bơn chiên.jpg', 'Cá bơn nướng', '580.000 đ/con', 'images/cá bơn nướng.jpg'],
        ['Tu Hài Canada', 1100000, 'đ/kg', 'images/tu hai canada.jpg', 'Tu hài nhập khẩu', 'Tu hài hấp xả ớt', '180.000 đ/đĩa', 'images/tu hài hấp xả ớt.jpg', 'Tu hài nướng mỡ hành', '200.000 đ/đĩa', 'images/tu hài nướng mỡ hành.jpg'],
        ['Bào Ngư Hàn Quốc', 1250000, 'đ/kg', 'images/bao ngu.jpg', 'Bào ngư tươi sống', 'Cháo bào ngư', '150.000 đ/bát', 'images/cháo bào ngư.jpg', 'Bào ngư sống', '300.000 đ/con', 'images/bào ngư sống.jpg'],
        ['Ốc Hương', 650000, 'đ/kg', 'images/oc hương.jpg', 'Ốc hương size lớn', 'Ốc luộc', '120.000 đ/đĩa', 'images/ốc luộc.jpg', 'Ốc trứng muối', '180.000 đ/đĩa', 'images/ốc trứng muối.jpg'],
        ['Mực Ống', 350000, 'đ/kg', 'images/muc ong.jpg', 'Mực ống tươi câu', 'Mực nhồi thịt', '220.000 đ/đĩa', 'images/mực nhồi thịt.jpg', 'Mực xào dứa', '190.000 đ/đĩa', 'images/Mực xào rứa.jpg']
    ];

    $stmt = $db->prepare("INSERT INTO menu_items (name, price, price_unit, image_url, description, quantity, status,
        dish_1_name, dish_1_price, dish_1_image, dish_2_name, dish_2_price, dish_2_image) 
        VALUES (?, ?, ?, ?, ?, 10, 'active', ?, ?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $stmt->execute($item);
    }
    echo "<p>✅ Nhập <b>" . count($items) . "</b> món vào <b>menu_items</b> thành công</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Nhập menu_items thất bại: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br><div style='padding:15px; background-color:#e6ffe6; border:1px solid green; border-radius:5px;'>";
echo "🎉 <b>Hoàn tất sửa lỗi & Khởi tạo database!</b> Bạn có thể truy cập <a href='index.php'><b>Trang chủ</b></a>.";
echo "</div>";
?>
