<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$role_id = $_SESSION['role_id'] ?? 3;

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Danh sách Bộ phận</h4>
HTML;

if($role_id <= 2) {
    echo <<<HTML
        <a href="index.php?controller=department&action=create" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Thêm Bộ phận
        </a>
HTML;
}

echo <<<HTML
    </div>
HTML;

if(isset($_GET['msg'])) {
    $msg_text = '';
    switch($_GET['msg']) {
        case 'created': $msg_text = 'Đã thêm bộ phận thành công!'; break;
        case 'updated': $msg_text = 'Đã cập nhật bộ phận thành công!'; break;
        case 'deleted': $msg_text = 'Đã xóa bộ phận thành công!'; break;
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
        <table class="table mb-0">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th width="20%">Mã Bộ Phận</th>
                    <th width="35%">Tên Bộ Phận</th>
                    <th>Người phụ trách</th>
HTML;

if($role_id <= 2) {
    echo '<th width="15%" class="text-center">Thao tác</th>';
}

echo <<<HTML
                </tr>
            </thead>
            <tbody>
HTML;

if(count($departments) > 0) {
    foreach($departments as $dept) {
        $id = $dept['id'];
        $code = htmlspecialchars($dept['code']);
        $name = htmlspecialchars($dept['name']);
        $manager = htmlspecialchars($dept['manager_name'] ?? '');
        $managerDisplay = $manager ? $manager : '<span class="text-muted fst-italic">Chưa cập nhật</span>';
        
        echo <<<HTML
        <tr>
            <td>#{$id}</td>
            <td><span class="badge bg-secondary">{$code}</span></td>
            <td class="fw-bold">{$name}</td>
            <td>{$managerDisplay}</td>
HTML;
        
        if($role_id <= 2) {
            echo <<<HTML
            <td class="text-center">
                <a href="index.php?controller=department&action=edit&id={$id}" class="btn btn-sm btn-outline-primary" title="Sửa">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a href="index.php?controller=department&action=delete&id={$id}" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa bộ phận này? (Các nhân viên và ca làm việc liên quan có thể bị ảnh hưởng)')">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </td>
HTML;
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu bộ phận nào.</td></tr>';
}

echo <<<HTML
            </tbody>
        </table>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
