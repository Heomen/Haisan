<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$role_id = $_SESSION['role_id'] ?? 3;

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-users me-2"></i> Danh sách Nhân viên</h4>
HTML;

if($role_id <= 2) {
    echo <<<HTML
        <a href="index.php?controller=employee&action=create" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Thêm Nhân viên
        </a>
HTML;
}

echo <<<HTML
    </div>
HTML;

if(isset($_GET['msg'])) {
    $msg_text = '';
    switch($_GET['msg']) {
        case 'created': $msg_text = 'Đã thêm nhân viên thành công!'; break;
        case 'updated': $msg_text = 'Đã cập nhật nhân viên thành công!'; break;
        case 'deleted': $msg_text = 'Đã xóa nhân viên thành công!'; break;
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
                    <th width="5%">ID</th>
                    <th width="8%" class="text-center">Avatar</th>
                    <th width="18%">Tên nhân viên</th>
                    <th width="10%">Ngày sinh</th>
                    <th width="12%">Điện thoại</th>
                    <th width="18%">Địa chỉ</th>
                    <th width="14%">Bộ phận</th>
HTML;

if($role_id <= 2) {
    echo '<th width="15%" class="text-center">Thao tác</th>';
}

echo <<<HTML
                </tr>
            </thead>
            <tbody>
HTML;

if(count($employees) > 0) {
    foreach($employees as $emp) {
        $id = $emp['id'];
        $name = htmlspecialchars($emp['full_name']);
        $code = htmlspecialchars($emp['code'] ?? 'N/A');
        
        $avatar_path = 'assets/img/default-avatar.png'; // Fallback nếu chưa có
        if(!empty($emp['avatar']) && file_exists('uploads/avatars/' . $emp['avatar'])) {
            $avatar_path = 'uploads/avatars/' . $emp['avatar'];
        } else {
            // Hiển thị chữ cái đầu nếu không có ảnh
            $first_char = strtoupper(substr($name, 0, 1));
            $avatar_html = '<div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mx-auto" style="width: 40px; height: 40px; font-weight: bold;">'.$first_char.'</div>';
        }

        if(isset($avatar_html)) {
            $avatar_display = $avatar_html;
            unset($avatar_html);
        } else {
            $avatar_display = '<img src="'.$avatar_path.'" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">';
        }

        $dob = $emp['dob'] ? date('d/m/Y', strtotime($emp['dob'])) : '<span class="text-muted">-</span>';
        $phone = $emp['phone'] ? htmlspecialchars($emp['phone']) : '<span class="text-muted">-</span>';
        $address = $emp['address'] ? htmlspecialchars($emp['address']) : '<span class="text-muted">-</span>';
        $dept_name = $emp['department_name'] ? htmlspecialchars($emp['department_name']) : 'Chưa phân bổ';
        
        echo <<<HTML
        <tr>
            <td class="text-muted">#{$id} <br><small class="text-secondary">{$code}</small></td>
            <td class="text-center">{$avatar_display}</td>
            <td class="fw-bold text-dark">{$name}</td>
            <td>{$dob}</td>
            <td>{$phone}</td>
            <td>{$address}</td>
            <td><span class="badge bg-info text-dark">{$dept_name}</span></td>
HTML;
        
        if($role_id <= 2) {
            echo <<<HTML
            <td class="text-center">
                <a href="index.php?controller=employee&action=edit&id={$id}" class="btn btn-sm btn-outline-primary" title="Sửa">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button"
                    class="btn btn-sm btn-outline-danger btn-delete-emp"
                    data-url="index.php?controller=employee&action=delete&id={$id}"
                    data-name="{$name}"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteEmpModal"
                    title="Xóa">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
HTML;
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8" class="text-center py-4 text-muted">Chưa có dữ liệu nhân viên nào.</td></tr>';
}

echo <<<HTML
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Xác nhận Xóa Nhân viên -->
<div class="modal fade" id="deleteEmpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa nhân viên
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3.5rem;color:#e53935;"><i class="fa-solid fa-trash-can"></i></div>
                <h5 class="fw-bold text-dark mb-2">Bạn có chắc chắn muốn xóa?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Hành động này sẽ xóa vĩnh viễn nhân viên <strong id="deleteEmpName" class="text-danger"></strong>
                    khỏi hệ thống.<br>Bạn không thể hoàn tác sau khi đã xóa!
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy bỏ</button>
                <a href="#" id="confirmDeleteEmpBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Có, Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-delete-emp').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteEmpBtn').setAttribute('href', this.dataset.url);
        document.getElementById('deleteEmpName').textContent = this.dataset.name;
    });
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
