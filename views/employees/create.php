<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Thêm Nhân viên Mới</h4>
        <a href="index.php?controller=employee&action=index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
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
            <form action="index.php?controller=employee&action=create" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label fw-bold">Mã NV <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required placeholder="VD: NV01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="name" class="form-label fw-bold">Tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ và tên...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avatar" class="form-label fw-bold">Ảnh đại diện (Avatar)</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        <small class="text-muted">Hỗ trợ: JPG, PNG. Bỏ trống dùng mặc định.</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dob" class="form-label fw-bold">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="dob">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="VD: 0987654321">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label fw-bold">Bộ phận</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">-- Chọn bộ phận --</option>
HTML;
foreach($departments as $dept) {
    echo '<option value="'.$dept['id'].'">'.htmlspecialchars($dept['name']).'</option>';
}
echo <<<HTML
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ cư trú...">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-2"></i> Lưu nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
