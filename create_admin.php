<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Kiểm tra xem bảng roles có tồn tại không, nếu không thì tạo
    $conn->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )");

    // Thêm role Admin nếu chưa có
    $checkRole = $conn->query("SELECT id FROM roles WHERE id = 1");
    if ($checkRole->rowCount() == 0) {
        $conn->exec("INSERT INTO roles (id, name) VALUES (1, 'Super Admin'), (2, 'Admin'), (3, 'Staff')");
        echo "Đã tạo bảng roles.<br>";
    }

    // Kiểm tra bảng users
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        role_id INT DEFAULT 1,
        status TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id)
    )");

    // Xóa tài khoản admin cũ nếu có, để tạo lại với mật khẩu mới
    $conn->exec("DELETE FROM users WHERE username = 'admin'");

    // Thêm tài khoản admin, mật khẩu 12345 được mã hóa MD5
    $password_hash = md5('12345');
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role_id, status) 
                            VALUES ('admin', :password, 'Quản Trị Viên', 1, 1)");
    $stmt->execute([':password' => $password_hash]);

    echo "<h3 style='color:green'>✅ Tạo tài khoản Admin thành công!</h3>";
    echo "<strong>Tên đăng nhập:</strong> admin<br>";
    echo "<strong>Mật khẩu:</strong> 12345<br><br>";
    echo "<a href='admin' style='padding:10px 20px;background:#0d6efd;color:white;border-radius:5px;text-decoration:none;'>→ Đăng nhập ngay</a>";

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
