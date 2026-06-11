<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

// Calculate some general stats for cards
$totalInvoices = count($reservations);
$totalRevenueExpected = 0;
$completedCount = 0;
$pendingCount = 0;

foreach ($reservations as $res) {
    $resTotal = 0;
    if (!empty($res['pre_order'])) {
        $dishes = explode(',', $res['pre_order']);
        foreach ($dishes as $dish) {
            $dish = trim($dish);
            $resTotal += $prices_map[$dish] ?? 0;
        }
    }
    $totalRevenueExpected += $resTotal;
    
    if ($res['status'] === 'completed') {
        $completedCount++;
    } elseif ($res['status'] === 'pending') {
        $pendingCount++;
    }
}

$active_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$active_class_all = ($active_status === 'all' || empty($active_status)) ? 'btn-primary text-white' : 'btn-light text-secondary';
$active_class_pending = ($active_status === 'pending') ? 'btn-primary text-white' : 'btn-light text-secondary';
$active_class_confirmed = ($active_status === 'confirmed') ? 'btn-primary text-white' : 'btn-light text-secondary';
$active_class_completed = ($active_status === 'completed') ? 'btn-primary text-white' : 'btn-light text-secondary';
$active_class_cancelled = ($active_status === 'cancelled') ? 'btn-primary text-white' : 'btn-light text-secondary';

echo <<<HTML
<main class="main-content">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i> Hóa đơn khách hàng đặt bàn</h4>
            <p class="text-muted small mb-0">Quản lý, tính tổng tiền dự kiến và xuất báo cáo excel các đơn đặt bàn từ hệ thống.</p>
        </div>
        <div>
            <a href="index.php?controller=reservation&action=export_bills&status={$active_status}" class="btn btn-success fw-bold px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-file-excel fs-5"></i> Xuất File Excel
            </a>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-receipt fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Tổng Hóa Đơn</div>
                        <h4 class="fw-bold mb-0 text-dark">{$totalInvoices}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Tổng Doanh Thu (Tạm tính)</div>
                        <h4 class="fw-bold mb-0 text-success">
HTML;
echo number_format($totalRevenueExpected, 0, ',', '.') . ' đ';
echo <<<HTML
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-circle-check fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Đã Hoàn Thành</div>
                        <h4 class="fw-bold mb-0 text-dark">{$completedCount}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-hourglass-half fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Đang Chờ Duyệt</div>
                        <h4 class="fw-bold mb-0 text-dark">{$pendingCount}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="index.php?controller=reservation&action=bills" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold fs-7 shadow-sm border-0 {$active_class_all}">Tất cả</a>
        <a href="index.php?controller=reservation&action=bills&status=pending" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold fs-7 shadow-sm border-0 {$active_class_pending}">Chờ xác nhận</a>
        <a href="index.php?controller=reservation&action=bills&status=confirmed" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold fs-7 shadow-sm border-0 {$active_class_confirmed}">Đã xác nhận</a>
        <a href="index.php?controller=reservation&action=bills&status=completed" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold fs-7 shadow-sm border-0 {$active_class_completed}">Đã hoàn thành</a>
        <a href="index.php?controller=reservation&action=bills&status=cancelled" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold fs-7 shadow-sm border-0 {$active_class_cancelled}">Đã hủy</a>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th class="px-4 py-3 border-0 text-muted" style="width: 10%">Mã HĐ</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 18%">Khách hàng</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 12%">Số điện thoại</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 18%">Thời gian đặt</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 15%">Chi nhánh</th>
                            <th class="px-4 py-3 border-0 text-muted" style="width: 12%">Trạng thái</th>
                            <th class="px-4 py-3 border-0 text-muted text-end" style="width: 15%">Tổng tiền (Dự kiến)</th>
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
        $pre_order = htmlspecialchars($res['pre_order'] ?? '');
        
        // Calculate invoice total
        $invoiceTotal = 0;
        $detailsListHtml = '';
        if (!empty($pre_order)) {
            $dishes = explode(',', $pre_order);
            foreach ($dishes as $dish) {
                $dish = trim($dish);
                if (empty($dish)) continue;
                $price = $prices_map[$dish] ?? 0;
                $invoiceTotal += $price;
                
                $detailsListHtml .= '<div class="d-flex justify-content-between border-bottom border-light py-1" style="font-size: 11px;">';
                $detailsListHtml .= '<span>' . $dish . '</span>';
                $detailsListHtml .= '<span class="fw-semibold text-primary">' . number_format($price, 0, ',', '.') . 'đ</span>';
                $detailsListHtml .= '</div>';
            }
        }
        
        $badgeClass = '';
        $statusText = '';
        switch($res['status']) {
            case 'pending': $badgeClass = 'bg-warning text-dark'; $statusText = 'Chờ xác nhận'; break;
            case 'confirmed': $badgeClass = 'bg-primary text-white'; $statusText = 'Đã xác nhận'; break;
            case 'completed': $badgeClass = 'bg-success text-white'; $statusText = 'Đã hoàn thành'; break;
            case 'cancelled': $badgeClass = 'bg-danger text-white'; $statusText = 'Đã hủy'; break;
        }

        echo <<<HTML
        <tr style="border-top: 1px solid #f1f5f9;">
            <td class="px-4 py-3 fw-bold text-muted">#RES-{$id}</td>
            <td class="px-4 py-3 fw-bold text-dark">{$name}</td>
            <td class="px-4 py-3 text-secondary">{$phone}</td>
            <td class="px-4 py-3">
                <div class="fw-bold">{$date}</div>
                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>{$time}</small>
            </td>
            <td class="px-4 py-3">{$location}</td>
            <td class="px-4 py-3">
                <span class="badge {$badgeClass} rounded-pill px-3 py-2" style="font-size: 11px;">{$statusText}</span>
            </td>
            <td class="px-4 py-3 text-end fw-bold text-danger">
HTML;
        echo number_format($invoiceTotal, 0, ',', '.') . ' đ';
        echo <<<HTML
            </td>
        </tr>
HTML;

        // Details row
        if (!empty($detailsListHtml) || !empty($notes)) {
            echo '<tr style="background-color: #fafbfd; border-top: none;">';
            echo '<td colspan="7" class="px-4 py-2 pt-0" style="border-top: none;">';
            echo '<div class="row g-2">';
            
            if (!empty($detailsListHtml)) {
                echo '<div class="col-md-6">';
                echo '<div class="p-2 bg-white rounded border border-light">';
                echo '<div class="fw-bold text-muted mb-1 small"><i class="fa-solid fa-utensils me-1"></i> Món ăn đặt trước:</div>';
                echo $detailsListHtml;
                echo '</div>';
                echo '</div>';
            }
            
            if (!empty($notes)) {
                echo '<div class="col-md-6">';
                echo '<div class="p-2 bg-white rounded border border-light h-100">';
                echo '<div class="fw-bold text-muted mb-1 small"><i class="fa-solid fa-comment-dots me-1"></i> Ghi chú đặt bàn:</div>';
                echo '<div class="small text-secondary" style="font-size: 11px; white-space: pre-wrap;">' . $notes . '</div>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fa-solid fa-file-invoice fa-3x mb-3 text-light"></i><br>
            Không tìm thấy hóa đơn nào phù hợp.
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
