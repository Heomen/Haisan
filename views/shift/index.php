<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$role_id = $_SESSION['role_id'] ?? 3;

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-clock me-2"></i> Danh sách Ca làm việc</h4>
HTML;

if($role_id <= 2) {
    echo <<<HTML
        <a href="index.php?controller=shift&action=create" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Thêm Ca làm việc
        </a>
HTML;
}

echo <<<HTML
    </div>
HTML;

if(isset($_GET['msg'])) {
    $msg_text = '';
    switch($_GET['msg']) {
        case 'created': $msg_text = 'Đã thêm ca làm việc thành công!'; break;
        case 'updated': $msg_text = 'Đã cập nhật ca làm việc thành công!'; break;
        case 'deleted': $msg_text = 'Đã xóa ca làm việc thành công!'; break;
    }
    if($msg_text) {
        echo <<<HTML
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {$msg_text}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    }
}

echo <<<HTML
    <div class="table-custom">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th width="15%">Mã Ca</th>
                    <th width="20%">Tên Ca</th>
                    <th width="20%">Bộ phận</th>
                    <th width="15%">Giờ bắt đầu</th>
                    <th width="15%">Giờ kết thúc</th>
HTML;

if($role_id <= 2) {
    echo '<th width="7%" class="text-center">Thao tác</th>';
}

echo <<<HTML
                </tr>
            </thead>
            <tbody>
HTML;

if(count($shifts) > 0) {
    foreach($shifts as $item) {
        $id = $item['id'];
        $code = htmlspecialchars($item['code']);
        $name = htmlspecialchars($item['name']);
        $dept_name = $item['department_name'] ? htmlspecialchars($item['department_name']) : 'Chưa phân bổ';
        $start = $item['start_time'] ? date('H:i', strtotime($item['start_time'])) : '-';
        $end = $item['end_time'] ? date('H:i', strtotime($item['end_time'])) : '-';
        
        echo <<<HTML
        <tr>
            <td class="text-muted">#{$id}</td>
            <td class="fw-bold text-dark">{$code}</td>
            <td>{$name}</td>
            <td><span class="badge bg-info text-dark">{$dept_name}</span></td>
            <td>{$start}</td>
            <td>{$end}</td>
HTML;
        
        if($role_id <= 2) {
            echo <<<HTML
            <td class="text-center">
                <a href="index.php?controller=shift&action=edit&id={$id}" class="btn btn-sm btn-outline-primary" title="Sửa">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a href="index.php?controller=shift&action=delete&id={$id}" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa ca làm việc này vĩnh viễn?')">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </td>
HTML;
        }
        echo '</tr>';
    }
} else {
    $colspan = ($role_id <= 2) ? 7 : 6;
    echo '<tr><td colspan="'.$colspan.'" class="text-center py-4 text-muted">Chưa có dữ liệu ca làm việc nào.</td></tr>';
}

echo <<<HTML
            </tbody>
        </table>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
