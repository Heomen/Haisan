<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-utensils me-2"></i> Quản lý Thực đơn</h4>
        <a href="index.php?controller=menu&action=create" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Thêm món ăn mới
        </a>
    </div>
HTML;

if(isset($_SESSION['success'])) {
    $msg = $_SESSION['success'];
    unset($_SESSION['success']);
    echo <<<HTML
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {$msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
HTML;
}

if(isset($_SESSION['error'])) {
    $msg = $_SESSION['error'];
    unset($_SESSION['error']);
    echo <<<HTML
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i> {$msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
HTML;
}

echo <<<HTML
<style>
    button[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
</style>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 5%">ID</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 10%">Hình ảnh</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 22%">Tên món ăn</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 13%">Giá bán</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 10%">Số lượng</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 13%">Trạng thái</th>
                            <th class="px-4 py-3 border-0 text-muted text-end" style="width: 17%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

if(!empty($menu_items)) {
    foreach($menu_items as $item) {
        $id = $item['id'];
        $img = htmlspecialchars($item['image_url']);
        $name = htmlspecialchars($item['name']);
        $desc = htmlspecialchars($item['description']);
        $price = number_format($item['price'], 0, ',', '.');
        $unit = htmlspecialchars($item['price_unit']);
        $quantity = intval($item['quantity'] ?? 0);
        
        $dish_1_name = htmlspecialchars($item['dish_1_name'] ?? '');
        $dish_1_price = htmlspecialchars($item['dish_1_price'] ?? '');
        $dish_1_img = htmlspecialchars($item['dish_1_image'] ?? '');
        $dish_2_name = htmlspecialchars($item['dish_2_name'] ?? '');
        $dish_2_price = htmlspecialchars($item['dish_2_price'] ?? '');
        $dish_2_img = htmlspecialchars($item['dish_2_image'] ?? '');
        
        $has_dishes = !empty($dish_1_name) || !empty($dish_2_name);
        
        $status_badge = ($item['status'] == 'active') 
            ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Đang phục vụ</span>'
            : '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Ngừng phục vụ</span>';
        
        $qty_class = ($quantity <= 0) ? 'text-danger fw-bold' : 'text-dark fw-bold';
        $qty_display = ($quantity <= 0) ? '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Hết hàng</span>' : '<span class="fw-bold text-dark">' . $quantity . '</span>';
            
        $toggle_btn = '';
        if ($has_dishes) {
            $toggle_btn = <<<HTML
            <button class="btn btn-sm btn-light border-0 rounded-circle me-2" type="button" data-bs-toggle="collapse" data-bs-target="#dishes-row-{$id}" aria-expanded="false" style="width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; padding:0; vertical-align:middle;">
                <i class="fa-solid fa-chevron-down small text-muted" style="transition: transform 0.2s;"></i>
            </button>
HTML;
        } else {
            $toggle_btn = '<div style="display:inline-block; width:36px;"></div>';
        }
        
        echo <<<HTML
        <tr>
            <td class="px-4 py-3 fw-bold text-muted">#{$id}</td>
            <td class="px-4 py-3">
                <img src="{$img}" alt="{$name}" class="rounded-3 object-fit-cover shadow-sm" style="width: 60px; height: 60px;">
            </td>
            <td class="px-4 py-3">
                <div class="d-flex align-items-center">
                    {$toggle_btn}
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">{$name}</h6>
                        <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">{$desc}</small>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 fw-bold text-primary">
                {$price} {$unit}
            </td>
            <td class="px-4 py-3">{$qty_display}</td>
            <td class="px-4 py-3">{$status_badge}</td>
            <td class="px-4 py-3 text-end">
                <a href="index.php?controller=menu&action=edit&id={$id}" class="btn btn-light btn-sm rounded-circle me-2" title="Chỉnh sửa">
                    <i class="fa-solid fa-pen text-primary"></i>
                </a>
                <button type="button"
                    class="btn btn-light btn-sm rounded-circle btn-delete-item"
                    data-url="index.php?controller=menu&action=delete&id={$id}"
                    data-name="{$name}"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteItemModal"
                    title="Xóa">
                    <i class="fa-solid fa-trash text-danger"></i>
                </button>
            </td>
        </tr>
HTML;

        if ($has_dishes) {
            $dish_1_html = '';
            if (!empty($dish_1_name)) {
                $d1_img = !empty($dish_1_img) ? $dish_1_img : 'https://via.placeholder.com/50?text=No+Image';
                $dish_1_html = <<<HTML
                <div class="col-md-6" id="dish-card-{$id}-1">
                    <div class="d-flex align-items-center gap-3 bg-white p-2 rounded-3 shadow-sm border position-relative">
                        <img src="{$d1_img}" class="rounded-3 object-fit-cover border flex-shrink-0" style="width: 45px; height: 45px;" onerror="this.src='https://via.placeholder.com/50?text=No+Image'">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{$dish_1_name}</div>
                            <div class="text-danger fw-bold small">{$dish_1_price}</div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <button class="btn btn-light btn-sm rounded-circle border-0 btn-edit-dish"
                                style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                                title="Sửa món chế biến"
                                data-id="{$id}"
                                data-slot="1"
                                data-name="{$dish_1_name}"
                                data-price="{$dish_1_price}"
                                data-img="{$dish_1_img}">
                                <i class="fa-solid fa-pen text-primary" style="font-size:0.7rem;"></i>
                            </button>
                            <button class="btn btn-light btn-sm rounded-circle border-0 btn-clear-dish"
                                style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                                title="Xóa món chế biến"
                                data-id="{$id}"
                                data-slot="1"
                                data-name="{$dish_1_name}">
                                <i class="fa-solid fa-trash text-danger" style="font-size:0.7rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>
HTML;
            } else {
                $dish_1_html = <<<HTML
                <div class="col-md-6" id="dish-card-{$id}-1">
                    <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2 btn-add-dish" 
                        data-id="{$id}" data-slot="1" style="border-style:dashed;">
                        <i class="fa-solid fa-plus me-1"></i> Thêm món chế biến 1
                    </button>
                </div>
HTML;
            }
            
            $dish_2_html = '';
            if (!empty($dish_2_name)) {
                $d2_img = !empty($dish_2_img) ? $dish_2_img : 'https://via.placeholder.com/50?text=No+Image';
                $dish_2_html = <<<HTML
                <div class="col-md-6" id="dish-card-{$id}-2">
                    <div class="d-flex align-items-center gap-3 bg-white p-2 rounded-3 shadow-sm border position-relative">
                        <img src="{$d2_img}" class="rounded-3 object-fit-cover border flex-shrink-0" style="width: 45px; height: 45px;" onerror="this.src='https://via.placeholder.com/50?text=No+Image'">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{$dish_2_name}</div>
                            <div class="text-danger fw-bold small">{$dish_2_price}</div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <button class="btn btn-light btn-sm rounded-circle border-0 btn-edit-dish"
                                style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                                title="Sửa món chế biến"
                                data-id="{$id}"
                                data-slot="2"
                                data-name="{$dish_2_name}"
                                data-price="{$dish_2_price}"
                                data-img="{$dish_2_img}">
                                <i class="fa-solid fa-pen text-primary" style="font-size:0.7rem;"></i>
                            </button>
                            <button class="btn btn-light btn-sm rounded-circle border-0 btn-clear-dish"
                                style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                                title="Xóa món chế biến"
                                data-id="{$id}"
                                data-slot="2"
                                data-name="{$dish_2_name}">
                                <i class="fa-solid fa-trash text-danger" style="font-size:0.7rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>
HTML;
            } else {
                $dish_2_html = <<<HTML
                <div class="col-md-6" id="dish-card-{$id}-2">
                    <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2 btn-add-dish"
                        data-id="{$id}" data-slot="2" style="border-style:dashed;">
                        <i class="fa-solid fa-plus me-1"></i> Thêm món chế biến 2
                    </button>
                </div>
HTML;
            }

            echo <<<HTML
            <tr class="collapse" id="dishes-row-{$id}">
                <td colspan="7" class="bg-light p-3" style="border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                    <div class="px-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size:0.7rem;">Món ăn chế biến gợi ý</span>
                        </div>
                        <div class="row g-2">
                            {$dish_1_html}
                            {$dish_2_html}
                        </div>
                    </div>
                </td>
            </tr>
HTML;
        }
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fa-solid fa-folder-open fa-3x mb-3 text-light"></i><br>
            Chưa có món ăn nào trong thực đơn.
        </td>
    </tr>
HTML;
}

echo <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Xác nhận Xóa món chính -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa món ăn
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3.5rem;color:#e53935;"><i class="fa-solid fa-trash-can"></i></div>
                <h5 class="fw-bold text-dark mb-2">Bạn có chắc chắn muốn xóa?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Hành động này sẽ xóa vĩnh viễn món <strong id="deleteItemName" class="text-danger"></strong>
                    khỏi thực đơn.<br>Bạn không thể hoàn tác sau khi đã xóa!
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy bỏ</button>
                <a href="#" id="confirmDeleteItemBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Có, Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa/Thêm Món Chế Biến -->
<div class="modal fade" id="editDishModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-utensils"></i> <span id="editDishModalTitle">Sửa Món Chế Biến</span>
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editDishForm" enctype="multipart/form-data">
                    <input type="hidden" id="editDishItemId" name="item_id">
                    <input type="hidden" id="editDishSlot" name="slot">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên món chế biến <span class="text-danger">*</span></label>
                        <input type="text" id="editDishName" name="dish_name" class="form-control bg-light border-0" required placeholder="VD: Mực nhồi thịt">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá tham khảo</label>
                        <input type="text" id="editDishPrice" name="dish_price" class="form-control bg-light border-0" placeholder="VD: 180.000 đ/đĩa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh món</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="editDishImgPreview" src="" class="rounded-3 border object-fit-cover" style="width:60px;height:60px;display:none;" onerror="this.style.display='none'">
                            <span id="editDishImgNone" class="text-muted small">Chưa có ảnh</span>
                        </div>
                        <input type="file" name="dish_image" id="editDishImageFile" class="form-control bg-light border-0" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-end gap-2 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy</button>
                <button type="button" id="saveDishBtn" class="btn btn-primary fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Xóa Món Chế Biến -->
<div class="modal fade" id="deleteDishModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa món chế biến
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3rem;color:#e53935;"><i class="fa-solid fa-circle-xmark"></i></div>
                <h5 class="fw-bold text-dark mb-2">Xóa món chế biến?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Xóa <strong id="deleteDishName" class="text-danger"></strong> khỏi danh sách gợi ý?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteDishBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Xóa món ăn chính
document.querySelectorAll('.btn-delete-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('confirmDeleteItemBtn').setAttribute('href', this.dataset.url);
        document.getElementById('deleteItemName').textContent = this.dataset.name;
    });
});

// Chevron rotate on collapse toggle
document.addEventListener('show.bs.collapse', function(e) {
    const target = e.target;
    const btn = document.querySelector('[data-bs-target="#' + target.id + '"]');
    if (btn) btn.setAttribute('aria-expanded', 'true');
});
document.addEventListener('hide.bs.collapse', function(e) {
    const target = e.target;
    const btn = document.querySelector('[data-bs-target="#' + target.id + '"]');
    if (btn) btn.setAttribute('aria-expanded', 'false');
});

// Mở modal sửa/thêm món chế biến
document.querySelectorAll('.btn-edit-dish, .btn-add-dish').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id   = this.dataset.id;
        const slot = this.dataset.slot;
        const name  = this.dataset.name  || '';
        const price = this.dataset.price || '';
        const img   = this.dataset.img   || '';
        const isAdd = this.classList.contains('btn-add-dish');

        document.getElementById('editDishModalTitle').textContent = isAdd ? 'Thêm Món Chế Biến' : 'Sửa Món Chế Biến';
        document.getElementById('editDishItemId').value  = id;
        document.getElementById('editDishSlot').value    = slot;
        document.getElementById('editDishName').value    = name;
        document.getElementById('editDishPrice').value   = price;
        document.getElementById('editDishImageFile').value = '';

        const preview = document.getElementById('editDishImgPreview');
        const none    = document.getElementById('editDishImgNone');
        if (img) {
            preview.src = img;
            preview.style.display = 'block';
            none.style.display = 'none';
        } else {
            preview.style.display = 'none';
            none.style.display = 'inline';
        }

        new bootstrap.Modal(document.getElementById('editDishModal')).show();
    });
});

// Lưu món chế biến (AJAX)
document.getElementById('saveDishBtn').addEventListener('click', function() {
    const form   = document.getElementById('editDishForm');
    const formData = new FormData(form);
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

    fetch('index.php?controller=menu&action=updateDish', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi';
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editDishModal')).hide();
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể lưu'));
        }
    })
    .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi'; alert('Lỗi kết nối!'); });
});

// Mở modal xóa món chế biến
let _deleteDishId = null, _deleteDishSlot = null;
document.querySelectorAll('.btn-clear-dish').forEach(function(btn) {
    btn.addEventListener('click', function() {
        _deleteDishId   = this.dataset.id;
        _deleteDishSlot = this.dataset.slot;
        document.getElementById('deleteDishName').textContent = this.dataset.name;
        new bootstrap.Modal(document.getElementById('deleteDishModal')).show();
    });
});

// Xác nhận xóa món chế biến
document.getElementById('confirmDeleteDishBtn').addEventListener('click', function() {
    if (!_deleteDishId || !_deleteDishSlot) return;
    const btn = this;
    btn.disabled = true;

    const formData = new FormData();
    formData.append('item_id', _deleteDishId);
    formData.append('slot', _deleteDishSlot);

    fetch('index.php?controller=menu&action=deleteDish', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteDishModal')).hide();
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể xóa'));
        }
    })
    .catch(() => { btn.disabled = false; alert('Lỗi kết nối!'); });
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
