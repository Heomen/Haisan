<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Thêm Ca làm việc Mới</h4>
        <a href="index.php?controller=shift&action=index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
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
            <form action="index.php?controller=shift&action=create" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label fw-bold">Mã ca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required placeholder="VD: CS-BEP">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label fw-bold">Tên ca làm việc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="VD: Ca sáng, Ca chiều...">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="department_id" class="form-label fw-bold">Bộ phận <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">-- Chọn bộ phận --</option>
HTML;
foreach($departments as $dept) {
    echo '<option value="'.$dept['id'].'">'.htmlspecialchars($dept['name']).'</option>';
}
echo <<<HTML
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="start_time" class="form-label fw-bold">Giờ bắt đầu</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" value="06:00">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="end_time" class="form-label fw-bold">Giờ kết thúc</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" value="14:00">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-2"></i> Lưu ca làm việc
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
