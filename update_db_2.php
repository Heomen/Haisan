<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Cập nhật CSDL (Phần 2)</h2>";

try {
    $sql1 = "ALTER TABLE departments ADD COLUMN manager_name VARCHAR(255) NULL AFTER name;";
    $conn->exec($sql1);
    echo "Đã thêm cột manager_name vào bảng departments.<br>";
} catch (PDOException $e) {
    echo "Lưu ý (departments): Cột có thể đã tồn tại.<br>";
}

try {
    $sql2 = "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        avatar VARCHAR(255) NULL,
        dob DATE NULL,
        phone VARCHAR(20) NULL,
        address TEXT NULL,
        department_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
    )";
    $conn->exec($sql2);
    echo "Đã tạo bảng employees thành công.<br>";
} catch (PDOException $e) {
    echo "Lỗi tạo bảng employees: " . $e->getMessage() . "<br>";
}

echo "<h3>Cập nhật hoàn tất!</h3>";
?>
