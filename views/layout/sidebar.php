<?php
$current_controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$dashboard_active = ($current_controller == 'dashboard') ? 'active' : '';
$menu_active = ($current_controller == 'menu' && $action != 'favorites') ? 'active' : '';
$favorites_active = ($current_controller == 'menu' && $action == 'favorites') ? 'active' : '';
$reservation_active = ($current_controller == 'reservation' && $action == 'index') ? 'active' : '';
$bills_active = ($current_controller == 'reservation' && $action == 'bills') ? 'active' : '';
$review_active = ($current_controller == 'review') ? 'active' : '';
$employee_active = ($current_controller == 'employee') ? 'active' : '';
$department_active = ($current_controller == 'department') ? 'active' : '';
$user_active = ($current_controller == 'user') ? 'active' : '';
$salary_active = ($current_controller == 'salary') ? 'active' : '';
$login_log_active = ($current_controller == 'loginLog') ? 'active' : '';

$controller_titles = [
    'dashboard' => 'Tổng quan',
    'menu' => 'Quản lý Thực đơn',
    'reservation' => 'Quản lý Đặt bàn',
    'review' => 'Các Đánh Giá',
    'employee' => 'Quản lý Nhân viên',
    'salary' => 'Quản lý Lương',
    'loginLog' => 'Lịch sử Đăng nhập'
];
$display_title = $controller_titles[$current_controller] ?? ucfirst($current_controller);
if ($current_controller == 'menu' && $action == 'favorites') {
    $display_title = 'Sản phẩm ưa chuộng';
}
if ($current_controller == 'reservation' && $action == 'bills') {
    $display_title = 'Hóa đơn đặt bàn';
}

$role_id = $_SESSION['role_id'] ?? 3;
$full_name = $_SESSION['full_name'] ?? 'User';
$role_name = $_SESSION['role_name'] ?? 'Role';
$avatar_initial = strtoupper(substr($full_name, 0, 1));

echo <<<HTML
<!-- Sidebar Overlay for Mobile -->
<div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleAdminSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <h3><i class="fa-solid fa-fish-fins me-2"></i>Haisan</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php?controller=dashboard&action=index" class="{$dashboard_active}">
                <i class="fa-solid fa-chart-pie"></i> Tổng quan
            </a>
        </li>
        <li class="mt-4 px-3 text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Quản lý Website</li>
        <li>
            <a href="index.php?controller=menu&action=index" class="{$menu_active}">
                <i class="fa-solid fa-utensils"></i> Quản lý Thực đơn
            </a>
        </li>
        <li>
            <a href="index.php?controller=menu&action=favorites" class="{$favorites_active}">
                <i class="fa-solid fa-heart"></i> Sản phẩm ưa chuộng
            </a>
        </li>
        <li>
            <a href="index.php?controller=reservation&action=index" class="{$reservation_active}">
                <i class="fa-solid fa-calendar-check"></i> Quản lý Đặt bàn
            </a>
        </li>
        <li>
            <a href="index.php?controller=reservation&action=bills" class="{$bills_active}">
                <i class="fa-solid fa-file-invoice-dollar"></i> Hóa đơn đặt bàn
            </a>
        </li>
        <li>
            <a href="index.php?controller=review&action=index" class="{$review_active}">
                <i class="fa-solid fa-star"></i> Các Đánh Giá
            </a>
        </li>

        <li class="mt-4 px-3 text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Quản trị Nội bộ</li>
        <li>
            <a href="index.php?controller=employee&action=index" class="{$employee_active}">
                <i class="fa-solid fa-users"></i> Quản lý Nhân viên
            </a>
        </li>

        <li>
            <a href="index.php?controller=salary&action=index" class="{$salary_active}">
                <i class="fa-solid fa-money-bill-wave"></i> Quản lý Lương
            </a>
        </li>
HTML;

if ($role_id <= 2) {
    echo <<<HTML
        <li>
            <a href="index.php?controller=loginLog&action=index" class="{$login_log_active}">
                <i class="fa-solid fa-shield-halved"></i> Lịch sử Đăng nhập
            </a>
        </li>
HTML;
}

echo <<<HTML
    </ul>
</nav>

<!-- Top Navbar -->
<header class="top-navbar">
    <div class="navbar-left d-flex align-items-center gap-3">
        <button class="admin-menu-toggle" onclick="toggleAdminSidebar()" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h4 class="m-0 text-dark fw-bold">{$display_title}</h4>
    </div>
    <div class="navbar-right">
        <div class="user-profile dropdown">
            <div class="user-info text-end d-none d-md-block">
                <div class="fw-bold text-dark">{$full_name}</div>
                <div class="text-muted" style="font-size: 0.8rem;">{$role_name}</div>
            </div>
            <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                {$avatar_initial}
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-key me-2 text-muted"></i> Đổi mật khẩu</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="index.php?controller=auth&action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</header>

<script>
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
    // Close sidebar when clicking a menu link on mobile
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 991) {
                const sidebar = document.getElementById('adminSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
</script>
HTML;
?>
