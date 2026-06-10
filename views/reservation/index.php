<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Quản lý Yêu cầu Đặt bàn</h4>
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

echo <<<HTML
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 5%">ID</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Khách hàng</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Số điện thoại</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 20%">Thời gian đặt</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 20%">Chi nhánh</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Trạng thái</th>
                            <th class="px-4 py-3 border-0 text-muted text-end" style="width: 10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

if(!empty($reservations)) {
    foreach($reservations as $res) {
        $id = $res['id'];
        $name = htmlspecialchars($res['customer_name']);
        $phone = htmlspecialchars($res['phone']);
        $date = date('d/m/Y', strtotime($res['reservation_date']));
        $time = date('H:i', strtotime($res['reservation_time']));
        $location = $locations[$res['location_id']] ?? 'Không xác định';
        $notes = htmlspecialchars($res['notes']);
        
        $badgeClass = '';
        $statusText = '';
        switch($res['status']) {
            case 'pending': $badgeClass = 'bg-warning text-dark'; $statusText = 'Chờ xác nhận'; break;
            case 'confirmed': $badgeClass = 'bg-primary text-white'; $statusText = 'Đã xác nhận'; break;
            case 'completed': $badgeClass = 'bg-success text-white'; $statusText = 'Đã hoàn thành'; break;
            case 'cancelled': $badgeClass = 'bg-danger text-white'; $statusText = 'Đã hủy'; break;
        }

        echo <<<HTML
        <tr>
            <td class="px-4 py-3 fw-bold text-muted">#{$id}</td>
            <td class="px-4 py-3 fw-bold text-dark">{$name}</td>
            <td class="px-4 py-3 text-primary fw-bold">{$phone}</td>
            <td class="px-4 py-3">
                <div class="fw-bold">{$date}</div>
                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>{$time}</small>
            </td>
            <td class="px-4 py-3">{$location}</td>
            <td class="px-4 py-3">
                <span class="badge {$badgeClass} rounded-pill px-3 py-2">{$statusText}</span>
            </td>
            <td class="px-4 py-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <form action="index.php?controller=reservation&action=update_status" method="POST">
                                <input type="hidden" name="id" value="{$id}">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="dropdown-item text-primary"><i class="fa-solid fa-check me-2"></i> Xác nhận</button>
                            </form>
                        </li>
                        <li>
                            <form action="index.php?controller=reservation&action=update_status" method="POST">
                                <input type="hidden" name="id" value="{$id}">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="dropdown-item text-success"><i class="fa-solid fa-check-double me-2"></i> Hoàn thành</button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="index.php?controller=reservation&action=update_status" method="POST">
                                <input type="hidden" name="id" value="{$id}">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-xmark me-2"></i> Hủy đơn</button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger fw-bold" onclick="showDeleteModal({$id})">
                                <i class="fa-solid fa-trash me-2"></i> Xóa đơn
                            </button>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
HTML;

        $pre_order = htmlspecialchars($res['pre_order'] ?? '');
        if(!empty($notes) || !empty($pre_order)) {
            $preOrderHtml = !empty($pre_order) ? "<strong>Món đặt trước:</strong> " . $pre_order : "";
            $notesHtml = !empty($notes) ? "<strong>Ghi chú:</strong> {$notes}" : "";
            $separator = (!empty($preOrderHtml) && !empty($notesHtml)) ? "<br>" : "";
            echo <<<HTML
            <tr style="border-top: none;">
                <td colspan="7" class="px-4 pb-3 pt-0" style="border-top: none;">
                    <div class="p-2 bg-light rounded text-muted small">
                        {$preOrderHtml}{$separator}{$notesHtml}
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
            <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-light"></i><br>
            Chưa có yêu cầu đặt bàn nào.
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-danger text-white border-0 rounded-top-4">
        <h5 class="modal-title fw-bold" id="deleteModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Xóa yêu cầu đặt bàn</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-5">
        <div class="mb-4 text-danger"><i class="fa-regular fa-trash-can fa-4x mb-2"></i></div>
        <h4 class="fw-bold mb-3 text-dark">Bạn có chắc chắn muốn xóa?</h4>
        <p class="text-muted mb-0 px-3">Hành động này sẽ xóa vĩnh viễn đơn đặt bàn khỏi hệ thống. Bạn không thể hoàn tác sau khi đã xóa!</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-light px-4 py-2 fw-bold text-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
        <form action="index.php?controller=reservation&action=delete" method="POST" class="m-0">
            <input type="hidden" name="id" id="delete-id-input" value="">
            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">Có, Xóa vĩnh viễn</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function showDeleteModal(id) {
    document.getElementById('delete-id-input').value = id;
    var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}
</script>
HTML;

require_once 'views/layout/footer.php';
?>
