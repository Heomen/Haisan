<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$emp_id = $this->salary->employee_id;
$emp_name = htmlspecialchars($this->salary->employee_name);
$emp_code = htmlspecialchars($this->salary->employee_code);
$base = (float)$this->salary->base_salary;
$allowance = (float)$this->salary->allowance;
$fine = (float)$this->salary->fine;

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Chỉnh sửa Bảng lương</h4>
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
            <form action="index.php?controller=salary&action=edit&id={$this->salary->id}" method="POST">
                <input type="hidden" name="employee_id" value="{$emp_id}">
                <input type="hidden" name="salary_month" value="{$this->salary->salary_month}">
                <input type="hidden" name="salary_year" value="{$this->salary->salary_year}">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Nhân viên</label>
                        <input type="text" class="form-control bg-light" value="{$emp_name} ({$emp_code})" readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Bảng lương của</label>
                        <input type="text" class="form-control bg-light" value="Tháng {$this->salary->salary_month} / Năm {$this->salary->salary_year}" readonly disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="base_salary" class="form-label fw-bold">Lương cơ bản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="base_salary" name="base_salary" value="{$base}" required min="0" placeholder="VD: 10000000">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="allowance" class="form-label fw-bold">Phụ cấp</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="allowance" name="allowance" min="0" value="{$allowance}" placeholder="VD: 1500000">
                            <span class="input-group-text">đ</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fine" class="form-label fw-bold">Phạt</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="fine" name="fine" min="0" value="{$fine}" placeholder="VD: 200000">
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
    
    // Initial run to calculate total on load
    calculateTotal();
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
