<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-heart me-2 text-danger"></i> Sản phẩm ưa chuộng</h4>
        </div>
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
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Hình ảnh</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 45%">Tên món chế biến</th>
                            <th class="px-4 py-3 border-0 text-muted text-center" style="width: 20%">Số lượng đặt</th>
                            <th class="px-4 py-3 border-0 text-muted text-end" style="width: 20%">Món ưa thích</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

if(!empty($products)) {
    foreach($products as $prod) {
        $id = $prod['id'];
        $name = htmlspecialchars($prod['name']);
        $img = htmlspecialchars($prod['image']);
        $count = intval($prod['order_count']);
        $type = $prod['type'];
        $is_fav = intval($prod['is_favorite']);

        // Trạng thái ưa thích hiện tại
        $fav_status = '';
        if ($is_fav === 1) {
            $fav_status = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 ms-2" style="font-size: 0.7rem;"><i class="fa-solid fa-heart me-1"></i>Đang hiện trên Website</span>';
        }

        // Nút thiết lập món ưa thích
        if ($is_fav === 1) {
            $fav_btn = <<<HTML
            <a href="index.php?controller=menu&action=toggleFavorite&id={$id}&type={$type}" class="btn btn-danger rounded-pill btn-sm px-3 fw-bold">
                <i class="fa-solid fa-heart me-1"></i> Bỏ ưa thích
            </a>
HTML;
        } else {
            $fav_btn = <<<HTML
            <a href="index.php?controller=menu&action=toggleFavorite&id={$id}&type={$type}" class="btn btn-outline-danger rounded-pill btn-sm px-3 fw-bold">
                <i class="fa-regular fa-heart me-1"></i> Đặt ưa thích
            </a>
HTML;
        }

        echo <<<HTML
        <tr>
            <td class="px-4 py-3">
                <img src="{$img}" alt="{$name}" class="rounded-3 object-fit-cover shadow-sm" style="width: 60px; height: 60px;" onerror="this.src='https://via.placeholder.com/60?text=No+Image'">
            </td>
            <td class="px-4 py-3">
                <h6 class="mb-0 fw-bold text-dark d-inline">{$name}</h6>
                {$fav_status}
            </td>
            <td class="px-4 py-3 text-center fw-bold text-dark">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">{$count} lượt</span>
            </td>
            <td class="px-4 py-3 text-end">
                {$fav_btn}
            </td>
        </tr>
HTML;
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="4" class="text-center py-5 text-muted">
            <i class="fa-solid fa-heart-crack fa-3x mb-3 text-light"></i><br>
            Chưa có món chế biến nào. Hãy thêm món chế biến trong phần Quản lý Thực đơn.
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
HTML;

require_once 'views/layout/footer.php';
?>
