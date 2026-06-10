<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$id = htmlspecialchars($this->employee->id);
$code = htmlspecialchars($this->employee->code ?? '');
$name = htmlspecialchars($this->employee->full_name);
$dob = htmlspecialchars($this->employee->dob ?? '');
$phone = htmlspecialchars($this->employee->phone);
$address = htmlspecialchars($this->employee->address);
$department_id = $this->employee->department_id;
$avatar = $this->employee->avatar;

$avatar_path = 'assets/img/default-avatar.png';
if(!empty($avatar) && file_exists('uploads/avatars/' . $avatar)) {
    $avatar_path = 'uploads/avatars/' . $avatar;
}

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 font-weight-bold text-dark">Chỉnh sửa Nhân viên</h4>
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
            <form action="index.php?controller=employee&action=edit&id={$id}" method="POST" enctype="multipart/form-data">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <img src="{$avatar_path}" class="rounded-circle shadow-sm border" style="width: 100px; height: 100px; object-fit: cover; margin-bottom: 10px;">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label fw-bold">Mã NV <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required value="{$code}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="name" class="form-label fw-bold">Tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="{$name}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avatar" class="form-label fw-bold">Thay đổi Ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        <small class="text-muted">Bỏ trống nếu không đổi ảnh.</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dob" class="form-label fw-bold">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{$dob}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{$phone}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label fw-bold">Bộ phận</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">-- Chọn bộ phận --</option>
HTML;
foreach($departments as $dept) {
    $selected = ($dept['id'] == $department_id) ? 'selected' : '';
    echo '<option value="'.$dept['id'].'" '.$selected.'>'.htmlspecialchars($dept['name']).'</option>';
}
echo <<<HTML
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" value="{$address}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-2"></i> Lưu cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
