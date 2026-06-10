<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Thêm Bảng lương Mới</h4>
        <a href="index.php?controller=salary&action=index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
HTML;

if(!empty($error)) {
    echo <<<HTML
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation me-2"></i> {$error}
    </div>
HTML;
}

echo <<<HTML
            <form action="index.php?controller=salary&action=create" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="salary_month" class="form-label fw-bold">Dành cho Tháng <span class="text-danger">*</span></label>
                        <select class="form-select" id="salary_month" name="salary_month" required>
                            {$month_options}
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="salary_year" class="form-label fw-bold">Dành cho Năm <span class="text-danger">*</span></label>
                        <select class="form-select" id="salary_year" name="salary_year" required>
                            {$year_options}
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_id" class="form-label fw-bold">Nhân viên <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">-- Chọn nhân viên --</option>
HTML;
foreach($employees as $emp) {
    echo '<option value="'.$emp['id'].'">'.htmlspecialchars($emp['full_name']).' ('.htmlspecialchars($emp['code']).')</option>';
}
echo <<<HTML
                        </select>
                        <small class="text-muted">Chỉ hiển thị nhân viên chưa được thiết lập bảng lương.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="base_salary" class="form-label fw-bold">Lương cơ bản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control calc-salary" id="base_salary" name="base_salary" required min="0" placeholder="VD: 10000000">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="allowance" class="form-label fw-bold">Phụ cấp</label>
                        <div class="input-group">
                            <input type="number" class="form-control calc-salary" id="allowance" name="allowance" min="0" value="0" placeholder="VD: 1500000">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fine" class="form-label fw-bold">Phạt</label>
                        <div class="input-group">
                            <input type="number" class="form-control calc-salary" id="fine" name="fine" min="0" value="0" placeholder="VD: 200000">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-dark">Tổng lương thực nhận (Dự kiến)</label>
                        <div class="form-control-plaintext text-primary fw-bold fs-5 ps-2" id="total_display">0 đ</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-2"></i> Lưu bảng lương
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseInput = document.getElementById('base_salary');
    const allowanceInput = document.getElementById('allowance');
    const fineInput = document.getElementById('fine');
    const totalDisplay = document.getElementById('total_display');

    function calculateTotal() {
        const base = parseFloat(baseInput.value) || 0;
        const allowance = parseFloat(allowanceInput.value) || 0;
        const fine = parseFloat(fineInput.value) || 0;
        const total = base + allowance - fine;
        
        totalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    baseInput.addEventListener('input', calculateTotal);
    allowanceInput.addEventListener('input', calculateTotal);
    fineInput.addEventListener('input', calculateTotal);
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
