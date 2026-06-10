<?php
/**
 * Admin entry point
 * Truy cập: localhost/Haisan/admin
 * Sẽ chuyển hướng đến trang đăng nhập quản trị
 */
session_start();

// Nếu đã đăng nhập, vào thẳng Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php?controller=dashboard&action=index");
    exit();
}

// Chưa đăng nhập thì vào trang Login
header("Location: ../index.php?controller=auth&action=login");
exit();
?>
