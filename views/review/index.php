<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

// Tính toán các số liệu thống kê
$total_count = count($reviews);
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;
foreach ($reviews as $r) {
    if ($r['status'] == 0) $pending_count++;
    elseif ($r['status'] == 1) $approved_count++;
    elseif ($r['status'] == 2) $rejected_count++;
}

// Xử lý thông báo
$msg_html = '';
if (isset($_GET['msg'])) {
    $msg_type = $_GET['msg'];
    if ($msg_type == 'approved') {
        $msg_html = <<<HTML
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> Đã phê duyệt đánh giá thành công! Đánh giá này hiện đã được hiển thị công khai trên website.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    } elseif ($msg_type == 'deleted') {
        $msg_html = <<<HTML
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-trash-can me-2"></i> Đã xóa đánh giá thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    } elseif ($msg_type == 'delete_failed') {
        $msg_html = <<<HTML
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> Không thể xóa đánh giá. Vui lòng thử lại!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    } elseif ($msg_type == 'rejected') {
        $msg_html = <<<HTML
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> Đã chuyển trạng thái đánh giá thành "Không phê duyệt". Đánh giá này hiện không hiển thị trên website.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    }
}

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-star me-2 text-warning"></i> Quản lý Các Đánh Giá</h4>
    </div>

    {$msg_html}

    <!-- Thống kê -->
    <div class="row g-4 mb-4">
        <!-- Tổng đánh giá -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-comments fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Tổng đánh giá</h6>
                        <h3 class="fw-bold mb-0 text-dark">{$total_count}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chờ duyệt -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-hourglass-half fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Đang chờ duyệt</h6>
                        <h3 class="fw-bold mb-0 text-warning">{$pending_count}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Đã phê duyệt -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Đã phê duyệt</h6>
                        <h3 class="fw-bold mb-0 text-success">{$approved_count}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Không phê duyệt -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Không phê duyệt</h6>
                        <h3 class="fw-bold mb-0 text-danger">{$rejected_count}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách đánh giá -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 5%">ID</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 20%">Khách hàng</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Số sao</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 30%">Nội dung nhận xét</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 12%">Ngày gửi</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 13%">Trạng thái</th>
                            <th class="px-4 py-3 border-0 text-muted text-end" style="width: 20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

if (!empty($reviews)) {
    foreach ($reviews as $rev) {
        $id = $rev['id'];
        $customer_name = htmlspecialchars($rev['full_name']);
        $username = htmlspecialchars($rev['username']);
        $phone = htmlspecialchars($rev['phone'] ?? '');
        $email = htmlspecialchars($rev['email'] ?? '');
        
        $rating = intval($rev['rating']);
        $comment = nl2br(htmlspecialchars($rev['comment']));
        $created_at = date('d/m/Y H:i', strtotime($rev['created_at']));
        $status = intval($rev['status']);

        // Badge trạng thái
        if ($status == 0) {
            $status_badge = '<span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">Chờ duyệt</span>';
        } elseif ($status == 1) {
            $status_badge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Đã phê duyệt</span>';
        } else {
            $status_badge = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Không phê duyệt</span>';
        }

        // Tạo chuỗi sao hiển thị
        $stars_html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars_html .= '<i class="fa-solid fa-star text-warning me-1" style="font-size: 13px;"></i>';
            } else {
                $stars_html .= '<i class="fa-regular fa-star text-secondary me-1" style="font-size: 13px;"></i>';
            }
        }

        // Nút hành động
        $actions = '';
        if ($status != 1) {
            $actions .= '<a href="index.php?controller=review&action=approve&id=' . $id . '" class="btn btn-success btn-sm rounded-pill px-3 me-2 fw-bold text-white shadow-sm">';
            $actions .= '<i class="fa-solid fa-check me-1"></i> Phê duyệt</a>';
        }
        if ($status != 2) {
            $actions .= '<a href="index.php?controller=review&action=reject&id=' . $id . '" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2 fw-bold shadow-sm">';
            $actions .= '<i class="fa-solid fa-ban me-1"></i> Không phê duyệt</a>';
        }
        $actions .= '<button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm btn-delete-review"
            data-id="' . $id . '" data-name="' . htmlspecialchars($rev['full_name']) . '"
            data-bs-toggle="modal" data-bs-target="#deleteReviewModal">';
        $actions .= '<i class="fa-solid fa-trash-can me-1"></i> Xóa</button>';

        echo <<<HTML
        <tr>
            <td class="px-4 py-3 fw-bold text-muted">#{$id}</td>
            <td class="px-4 py-3">
                <div class="fw-bold text-dark">{$customer_name}</div>
                <div class="text-muted small">Tên TK: {$username}</div>
                <div class="text-muted small">SĐT: {$phone}</div>
            </td>
            <td class="px-4 py-3">
                <div class="d-flex align-items-center">
                    {$stars_html}
                </div>
            </td>
            <td class="px-4 py-3 text-wrap" style="max-width: 300px; font-size: 14px; color: #475569;">
                {$comment}
            </td>
            <td class="px-4 py-3 text-muted small">
                {$created_at}
            </td>
            <td class="px-4 py-3">
                {$status_badge}
            </td>
            <td class="px-4 py-3 text-end text-nowrap">
                {$actions}
            </td>
        </tr>
HTML;
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fa-solid fa-folder-open fa-3x mb-3 text-light"></i><br>
            Chưa có đánh giá nào được gửi từ khách hàng.
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
echo <<<HTML
<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-labelledby="deleteReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div class="modal-header border-0 text-white d-flex align-items-center" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa đánh giá
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3.5rem;color:#e53935;">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Bạn có chắc chắn muốn xóa?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Hành động này sẽ xóa vĩnh viễn đánh giá của khách hàng
                    <strong id="deleteReviewName" class="text-danger"></strong>
                    khỏi hệ thống.<br>Bạn không thể hoàn tác sau khi đã xóa!
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy bỏ</button>
                <a href="#" id="confirmDeleteReviewBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Có, Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete-review').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id   = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            document.getElementById('deleteReviewName').textContent = name;
            document.getElementById('confirmDeleteReviewBtn').setAttribute('href', 'index.php?controller=review&action=delete&id=' + id);
        });
    });
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
