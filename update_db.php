<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if columns exist, if not, add them
    $columns = [
        'price_unit' => "VARCHAR(50) DEFAULT 'đ/kg'",
        'image_url' => "VARCHAR(255) DEFAULT 'https://via.placeholder.com/300x200?text=No+Image'",
        'description' => "TEXT",
        'status' => "ENUM('active', 'inactive') DEFAULT 'active'"
    ];

    foreach ($columns as $column => $definition) {
        try {
            // Attempt to add column
            $query = "ALTER TABLE menu_items ADD COLUMN $column $definition";
            $conn->exec($query);
            echo "Đã thêm cột $column vào bảng menu_items.<br>\n";
        } catch(PDOException $e) {
            // Error 1060: Duplicate column name (column already exists)
            if ($e->getCode() == '42S21') {
                echo "Cột $column đã tồn tại trong menu_items.<br>\n";
            } else {
                echo "Lỗi khi thêm cột $column: " . $e->getMessage() . "<br>\n";
            }
        }
    }

    // Add pre_order to reservations
    try {
        $query = "ALTER TABLE reservations ADD COLUMN pre_order VARCHAR(255) DEFAULT NULL";
        $conn->exec($query);
        echo "Đã thêm cột pre_order vào bảng reservations.<br>\n";
    } catch(PDOException $e) {
        if ($e->getCode() == '42S21') {
            echo "Cột pre_order đã tồn tại trong reservations.<br>\n";
        } else {
            echo "Lỗi khi thêm cột pre_order: " . $e->getMessage() . "<br>\n";
        }
    }

    echo "<br>Hoàn tất cập nhật CSDL.";

} catch(PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}
?>
