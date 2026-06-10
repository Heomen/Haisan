<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$role_id = $_SESSION['role_id'] ?? 3;

function getFriendlyUserAgent($userAgent) {
    if (empty($userAgent) || $userAgent == 'Unknown') return 'Không rõ';
    $os = "Không rõ";
    $browser = "Không rõ";
    if (preg_match('/windows|win32/i', $userAgent))         $os = 'Windows';
    elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'macOS';
    elseif (preg_match('/iphone|ipad/i', $userAgent))        $os = 'iOS';
    elseif (preg_match('/android/i', $userAgent))            $os = 'Android';
    elseif (preg_match('/linux/i', $userAgent))              $os = 'Linux';
    if (preg_match('/edge|edg/i', $userAgent))              $browser = 'Edge';
    elseif (preg_match('/chrome|crios/i', $userAgent))      $browser = 'Chrome';
    elseif (preg_match('/safari/i', $userAgent))            $browser = 'Safari';
    elseif (preg_match('/firefox|fxios/i', $userAgent))     $browser = 'Firefox';
    elseif (preg_match('/opera|opr/i', $userAgent))         $browser = 'Opera';
    return "$browser ($os)";
}

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-shield-halved me-2"></i> Nhật ký &amp; Lịch sử Đăng nhập</h4>
    </div>

    <!-- Bộ lọc tìm kiếm -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:15px;">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2 align-items-center">
                <input type="hidden" name="controller" value="loginLog">
                <input type="hidden" name="action" value="index">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm theo tên đăng nhập hoặc họ tên..." value="{$search}" style="border-radius:0 10px 10px 0;font-size:0.85rem;">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm fw-bold" style="border-radius:10px;font-size:0.85rem;" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="Thành công"
HTML;
echo ($status == 'Thành công') ? ' selected' : '';
echo <<<HTML
>Thành công</option>
                        <option value="Thất bại"
HTML;
echo ($status == 'Thất bại') ? ' selected' : '';
echo <<<HTML
>Thất bại</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng hiển thị nhật ký -->
    <div class="card border-0 shadow-sm" style="border-radius:15px;overflow:hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                    <thead class="bg-light text-dark fw-bold">
                        <tr>
                            <th class="ps-4 py-3" style="width:185px;">Thời gian</th>
                            <th class="py-3">Tên đăng nhập</th>
                            <th class="py-3">Họ và tên</th>
                            <th class="py-3">Thiết bị / Trình duyệt</th>
                            <th class="py-3 text-center" style="width:135px;">Trạng thái</th>
                            <th class="py-3 text-center" style="width:80px;">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

if (count($logs) > 0) {
    foreach ($logs as $log) {
        $id   = (int)$log['id'];
        $time = date('d/m/Y H:i:s', strtotime($log['login_time']));
        $delete_url = "index.php?controller=loginLog&action=delete&id={$id}";

        if ($log['status'] == 'Thành công') {
            $status_badge = '<span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center" style="background-color:#e8f5e9;color:#2e7d32;font-size:0.8rem;"><i class="fa-solid fa-circle-check me-1"></i>Thành công</span>';
        } else {
            $status_badge = '<span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center" style="background-color:#ffebee;color:#c62828;font-size:0.8rem;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Thất bại</span>';
        }

        $friendly_ua = getFriendlyUserAgent($log['user_agent']);
        $full_ua  = htmlspecialchars($log['user_agent']);
        $username = htmlspecialchars($log['username']);
        $fullname = htmlspecialchars($log['full_name']);
        $ip       = htmlspecialchars($log['ip_address']);

        echo <<<HTML
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:0.82rem;"><i class="fa-regular fa-clock me-1"></i>{$time}</td>
                            <td class="fw-bold text-dark">{$username}</td>
                            <td>{$fullname}</td>
                            <td class="text-secondary" title="{$full_ua}"><i class="fa-solid fa-laptop me-1"></i>{$friendly_ua}</td>
                            <td class="text-center">{$status_badge}</td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 btn-delete-log"
                                    data-url="{$delete_url}"
                                    data-username="{$username}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteLogModal"
                                    title="Xóa bản ghi này"
                                    style="font-size:0.78rem;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
HTML;
    }
} else {
    echo <<<HTML
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                Không tìm thấy lịch sử đăng nhập nào khớp với điều kiện lọc.
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

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteLogModal" tabindex="-1" aria-labelledby="deleteLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <!-- Header đỏ -->
            <div class="modal-header border-0 text-white d-flex align-items-center" style="background:#e53935;padding:18px 24px;">
                <span class="fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Xóa bản ghi đăng nhập
                </span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body -->
            <div class="modal-body text-center px-5 py-4">
                <div class="mb-3" style="font-size:3.5rem;color:#e53935;">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Bạn có chắc chắn muốn xóa?</h5>
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    Hành động này sẽ xóa vĩnh viễn bản ghi đăng nhập của tài khoản
                    <strong id="deleteLogUsername" class="text-danger"></strong>
                    khỏi hệ thống.<br>Bạn không thể hoàn tác sau khi đã xóa!
                </p>
            </div>
            <!-- Footer -->
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal" style="color:#555;">Hủy bỏ</button>
                <a href="#" id="confirmDeleteLogBtn" class="btn btn-danger fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-trash-can me-1"></i> Có, Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete-log').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url      = this.getAttribute('data-url');
            var username = this.getAttribute('data-username');
            document.getElementById('confirmDeleteLogBtn').setAttribute('href', url);
            document.getElementById('deleteLogUsername').textContent = username;
        });
    });
});
</script>
HTML;

require_once 'views/layout/footer.php';
?>
