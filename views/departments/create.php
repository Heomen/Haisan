<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Thêm Bộ phận Mới</h4>
        <a href="index.php?controller=department&action=index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
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
            <form action="index.php?controller=department&action=create" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label fw-bold">Mã bộ phận</label>
                        <input type="text" class="form-control" id="code" name="code" required placeholder="VD: BP01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="name" class="form-label fw-bold">Tên bộ phận</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="VD: Bếp, Phục vụ...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="manager_name" class="form-label fw-bold">Người phụ trách</label>
                        <input type="text" class="form-control" id="manager_name" name="manager_name" placeholder="Tên người quản lý...">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-2"></i> Lưu dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
