<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$role_id = $_SESSION['role_id'] ?? 3;
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

$month_options = '';
for ($m = 1; $m <= 12; $m++) {
    $selected = ($m == $selected_month) ? 'selected' : '';
    $month_options .= "<option value='{$m}' {$selected}>Tháng {$m}</option>";
}

$year_options = '';
$current_y = (int)date('Y');
for ($y = $current_y - 3; $y <= $current_y + 2; $y++) {
    $selected = ($y == $selected_year) ? 'selected' : '';
    $year_options .= "<option value='{$y}' {$selected}>Năm {$y}</option>";
}

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-money-bill-wave me-2"></i> Bảng quản lý Lương</h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="GET" action="index.php" class="d-flex align-items-center m-0">
                <input type="hidden" name="controller" value="salary">
                <input type="hidden" name="action" value="index">
                <div class="input-group input-group-sm border shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff; padding: 2px 4px;">
                    <span class="input-group-text bg-white border-0 text-primary fw-bold pe-1">
                        <i class="fa-regular fa-calendar-days text-primary"></i>
                    </span>
                    <select name="month" class="form-select border-0 bg-transparent fw-bold text-dark" style="min-width: 110px; font-size: 0.85rem; cursor: pointer; box-shadow: none;" onchange="this.form.submit()">
                        {$month_options}
                    </select>
                    <span class="border-start my-1" style="border-color: #cbd5e1 !important;"></span>
                    <select name="year" class="form-select border-0 bg-transparent fw-bold text-dark" style="min-width: 100px; font-size: 0.85rem; cursor: pointer; box-shadow: none;" onchange="this.form.submit()">
                        {$year_options}
                    </select>
                </div>
            </form>
            
            <a href="index.php?controller=salary&action=export&month={$selected_month}&year={$selected_year}" class="btn btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center btn-sm fw-bold py-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
            </a>
HTML;

if($role_id <= 2) {
    echo <<<HTML
            <a href="index.php?controller=salary&action=create&month={$selected_month}&year={$selected_year}" class="btn btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center btn-sm fw-bold py-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus me-2"></i> Thêm Bảng lương
            </a>
HTML;
}

echo <<<HTML
        </div>
    </div>
HTML;

if(isset($_GET['msg'])) {
    $msg_text = '';
    switch($_GET['msg']) {
        case 'created': $msg_text = 'Đã thêm bảng lương thành công!'; break;
        case 'updated': $msg_text = 'Đã cập nhật bảng lương thành công!'; break;
        case 'deleted': $msg_text = 'Đã xóa bảng lương thành công!'; break;
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
                    <th width="15%">Mã nhân viên</th>
                    <th width="20%">Tên nhân viên</th>
                    <th width="15%">Lương</th>
                    <th width="15%">Phụ cấp</th>
                    <th width="15%">Phạt</th>
                    <th width="15%">Tổng</th>
HTML;

if($role_id <= 2) {
    echo '<th width="5%" class="text-center">Thao tác</th>';
}

echo <<<HTML
                </tr>
            </thead>
            <tbody>
HTML;

if(count($salaries) > 0) {
    foreach($salaries as $item) {
        $id = $item['id'];
        $code = htmlspecialchars($item['employee_code']);
        $name = htmlspecialchars($item['employee_name']);
        
        $base = (float)$item['base_salary'];
        $allowance = (float)$item['allowance'];
        $fine = (float)$item['fine'];
        $total = $base + $allowance - $fine;

        $base_formatted = number_format($base, 0, ',', '.') . ' đ';
        $allowance_formatted = number_format($allowance, 0, ',', '.') . ' đ';
        $fine_formatted = number_format($fine, 0, ',', '.') . ' đ';
        $total_formatted = number_format($total, 0, ',', '.') . ' đ';
        
        echo <<<HTML
        <tr>
            <td class="text-secondary fw-bold">{$code}</td>
            <td class="fw-bold text-dark">{$name}</td>
            <td class="text-primary fw-bold">{$base_formatted}</td>
            <td class="text-success">{$allowance_formatted}</td>
            <td class="text-danger">{$fine_formatted}</td>
            <td class="text-dark fw-bold" style="font-size: 1.05rem;">{$total_formatted}</td>
HTML;
        
        if($role_id <= 2) {
            echo <<<HTML
            <td class="text-center">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="index.php?controller=salary&action=edit&id={$id}" class="btn btn-sm btn-outline-primary" title="Sửa">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger btn-delete-salary"
                        data-url="index.php?controller=salary&action=delete&id={$id}"
                        data-name="{$name}"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteSalaryModal"
                        title="Xóa">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </td>
HTML;
        }
        echo '</tr>';
    }
} else {
    $colspan = ($role_id <= 2) ? 7 : 6;
    echo '<tr><td colspan="'.$colspan.'" class="text-center py-4 text-muted">Chưa có dữ liệu bảng lương nào.</td></tr>';
}

echo <<<HTML
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Xác nhận Xóa Bảng Lương -->
<div class="modal fade" id="deleteSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa bảng lương
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3.5rem;color:#e53935;"><i class="fa-solid fa-trash-can"></i></div>
                <h5 class="fw-bold text-dark mb-2">Bạn có chắc chắn muốn xóa?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Hành động này sẽ xóa vĩnh viễn bảng lương của <strong id="deleteSalaryName" class="text-danger"></strong>
                    khỏi hệ thống.<br>Bạn không thể hoàn tác sau khi đã xóa!
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy bỏ</button>
                <a href="#" id="confirmDeleteSalaryBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Có, Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-delete-salary').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteSalaryBtn').setAttribute('href', this.dataset.url);
        document.getElementById('deleteSalaryName').textContent = this.dataset.name;
    });
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
