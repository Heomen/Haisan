<?php
/**
 * Migration: Thêm cột quantity (số lượng) vào bảng menu_items
 * - Hỗ trợ chẩn đoán lỗi chi tiết
 
 
 */
require_once 'config/database.php';

echo "<h2>🔧 Hệ thống cập nhật CSDL tự động</h2>";

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn === null) {
        echo "<div style='color:red; font-weight:bold; padding: 15px; border: 1px solid red; background-color: #fee;'>";
        echo "❌ KHÔNG THỂ KẾT NỐI DATABASE!<br><br>";
        echo "<b>Cách khắc phục:</b><br>";
        echo "1. Đảm bảo MySQL trong XAMPP đang hiển thị màu xanh lá cây (đang chạy).<br>";
        echo "2. Nếu bạn vừa khôi phục thư mục XAMPP, hãy chắc chắn bạn đã sao chép thư mục cơ sở dữ liệu <b>ql_haisan</b> từ <code>C:\\xampp\\mysql\\data_old</code> sang <code>C:\\xampp\\mysql\\data</code>.";
        echo "</div>";
        exit();
    }

    // Kiểm tra xem bảng menu_items có tồn tại hay không
    try {
        $checkTable = $conn->query("SELECT 1 FROM menu_items LIMIT 1");
    } catch (PDOException $e) {
        echo "<div style='color:orange; font-weight:bold; padding: 15px; border: 1px solid orange; background-color: #fffde6; margin-bottom: 20px;'>";
        echo "⚠️ CẢNH BÁO: Bảng <b>menu_items</b> chưa tồn tại trong cơ sở dữ liệu <u>ql_haisan</u>!<br><br>";
        echo "<b>Cách khắc phục:</b><br>";
        echo "Hãy chạy liên kết này trước để khởi tạo toàn bộ các bảng mặc định: <a href='setup_web_db.php' target='_blank'><b>Khởi tạo bảng (setup_web_db.php)</b></a><br>";
        echo "Sau đó chạy liên kết này để thêm dữ liệu mẫu: <a href='sync_menu.php' target='_blank'><b>Đồng bộ dữ liệu mẫu (sync_menu.php)</b></a><br>";
        echo "Cuối cùng, hãy tải lại trang này để thêm cột Số lượng.";
        echo "</div>";
        exit();
    }

    // Thêm cột quantity vào bảng menu_items
    try {
        $query = "ALTER TABLE menu_items ADD COLUMN quantity INT NOT NULL DEFAULT 0 AFTER description";
        $conn->exec($query);
        echo "<p style='color:green; font-weight:bold;'>✅ Đã thêm thành công cột 'quantity' vào bảng menu_items.</p>";
    } catch(PDOException $e) {
        // Lỗi cột đã tồn tại (SQLSTATE 42S21 hoặc code 1060)
        if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color:blue;'>ℹ️ Cột 'quantity' đã có sẵn trong bảng menu_items (Không cần tạo lại).</p>";
        } else {
            echo "<p style='color:red;'>❌ Lỗi khi thêm cột quantity: " . $e->getMessage() . "</p>";
        }
    }

    // Cập nhật tất cả món hiện tại có status='active' sẽ có quantity mặc định = 10
    try {
        $query = "UPDATE menu_items SET quantity = 10 WHERE quantity = 0 AND status = 'active'";
        $affected = $conn->exec($query);
        echo "<p style='color:green; font-weight:bold;'>✅ Đã cập nhật số lượng mặc định (10) cho {$affected} món đang hoạt động.</p>";
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Lỗi khi cập nhật quantity mặc định: " . $e->getMessage() . "</p>";
    }

    echo "<br><div style='padding:15px; background-color:#e6ffe6; border:1px solid green; border-radius:5px;'>";
    echo "🎉 <b>Hoàn tất cập nhật CSDL!</b> Mọi thứ đã sẵn sàng. Bạn có thể quay lại trang <a href='index.php?controller=menu&action=index'><b>Quản lý Thực đơn</b></a>.";
    echo "</div>";

} catch(PDOException $e) {
    echo "<div style='color:red; padding: 15px; border: 1px solid red; background-color: #fee;'>";
    echo "❌ LỖI HỆ THỐNG: " . $e->getMessage();
    echo "</div>";
}
?>
