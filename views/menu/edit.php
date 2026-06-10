<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$id = $menu->id;
$img = htmlspecialchars($menu->image_url);
$name = htmlspecialchars($menu->name);
$price = $menu->price;
$unit = htmlspecialchars($menu->price_unit);
$desc = htmlspecialchars($menu->description);
$active_selected = ($menu->status == 'active') ? 'selected' : '';
$inactive_selected = ($menu->status == 'inactive') ? 'selected' : '';
$quantity = intval($menu->quantity ?? 0);

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark">Chỉnh sửa Món ăn</h4>
        <a href="index.php?controller=menu&action=index" class="btn btn-light rounded-pill px-4 shadow-sm border">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <form action="index.php?controller=menu&action=edit&id={$id}" method="POST" enctype="multipart/form-data">
                        
                        <div class="text-center mb-4">
                            <img src="{$img}" alt="Current Image" class="rounded-4 shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #fff;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên món ăn <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required value="{$name}">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Giá bán <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control form-control-lg bg-light border-0" required value="{$price}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Đơn vị tính</label>
                                <input type="text" name="price_unit" class="form-control form-control-lg bg-light border-0" value="{$unit}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Cập nhật hình ảnh</label>
                            <input type="file" name="image" class="form-control form-control-lg bg-light border-0" accept="image/*">
                            <small class="text-muted mt-1 d-block">Bỏ trống nếu không muốn thay đổi ảnh hiện tại.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control form-control-lg bg-light border-0" required min="0" value="{$quantity}" placeholder="VD: 50">
                            <small class="text-muted mt-1 d-block"><i class="fa-solid fa-circle-info me-1"></i>Trạng thái tự động: số lượng = 0 → "Ngừng phục vụ", số lượng > 0 → "Đang phục vụ".</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control form-control-lg bg-light border-0" rows="4">{$desc}</textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold">Trạng thái phục vụ</label>
                            <select name="status" class="form-select form-select-lg bg-light border-0">
                                <option value="active" {$active_selected}>Đang phục vụ</option>
                                <option value="inactive" {$inactive_selected}>Ngừng phục vụ</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
HTML;

require_once 'views/layout/footer.php';
?>
