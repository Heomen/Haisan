<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark">Thêm Món Ăn Mới</h4>
        <a href="index.php?controller=menu&action=index" class="btn btn-light rounded-pill px-4 shadow-sm border">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <form action="index.php?controller=menu&action=create" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên món ăn <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required placeholder="VD: Tôm hùm bông">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Giá bán <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control form-control-lg bg-light border-0" required placeholder="VD: 2500000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Đơn vị tính</label>
                                <input type="text" name="price_unit" class="form-control form-control-lg bg-light border-0" value="đ/kg" placeholder="VD: đ/kg, đ/con">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control form-control-lg bg-light border-0" required min="0" value="0" placeholder="VD: 50">
                            <small class="text-muted mt-1 d-block"><i class="fa-solid fa-circle-info me-1"></i>Khi số lượng = 0, trạng thái sẽ tự động chuyển sang "Ngừng phục vụ".</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Hình ảnh món ăn</label>
                            <input type="file" name="image" class="form-control form-control-lg bg-light border-0" accept="image/*">
                            <small class="text-muted mt-1 d-block">Định dạng hỗ trợ: JPG, PNG, JPEG.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control form-control-lg bg-light border-0" rows="4" placeholder="Mô tả sơ lược về món ăn, xuất xứ, cách chế biến..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái phục vụ</label>
                            <select name="status" class="form-select form-select-lg bg-light border-0">
                                <option value="active">Đang phục vụ</option>
                                <option value="inactive">Ngừng phục vụ</option>
                            </select>
                        </div>

                        <!-- Món chế biến gợi ý 1 -->
                        <div class="card border-0 bg-light p-4 mb-4 rounded-3">
                            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-utensils me-2"></i>Món Chế Biến Gợi Ý 1</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tên món chế biến 1</label>
                                    <input type="text" name="dish_1_name" class="form-control bg-white border-0" placeholder="VD: Mực nhồi thịt">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Giá tham khảo 1</label>
                                    <input type="text" name="dish_1_price" class="form-control bg-white border-0" placeholder="VD: 180.000 đ/đĩa">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Hình ảnh món chế biến 1</label>
                                <input type="file" name="dish_1_image" class="form-control bg-white border-0" accept="image/*">
                            </div>
                        </div>

                        <!-- Món chế biến gợi ý 2 -->
                        <div class="card border-0 bg-light p-4 mb-4 rounded-3">
                            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-fire-burner me-2"></i>Món Chế Biến Gợi Ý 2</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tên món chế biến 2</label>
                                    <input type="text" name="dish_2_name" class="form-control bg-white border-0" placeholder="VD: Mực xào dứa">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Giá tham khảo 2</label>
                                    <input type="text" name="dish_2_price" class="form-control bg-white border-0" placeholder="VD: 150.000 đ/đĩa">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Hình ảnh món chế biến 2</label>
                                <input type="file" name="dish_2_image" class="form-control bg-white border-0" accept="image/*">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Món Ăn
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
