<?php
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

$revenue = number_format($stats['total_revenue'], 0, ',', '.');
$orders = $stats['total_orders'];
$employees = $stats['total_employees'];

$chart_labels_json = json_encode($chart_labels);
$chart_revenues_json = json_encode($chart_revenues);

echo <<<HTML
<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-chart-pie me-2"></i> Tổng quan báo cáo</h4>
        <a href="index.php?controller=dashboard&action=export" class="btn btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center btn-sm fw-bold py-2" style="font-size: 0.85rem;">
            <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <div class="stat-info">
                    <h5>Tổng Doanh Thu</h5>
                    <h3>{$revenue}đ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h5>Đơn Đặt Bàn</h5>
                    <h3>{$orders}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="stat-info">
                    <h5>Nhân Viên</h5>
                    <h3>{$employees}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-line me-2 text-primary"></i> Biểu Đồ Doanh Thu Thực Tế</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="revenueChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    window.onload = function() {
        if (typeof Chart !== 'undefined') {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {$chart_labels_json},
                    datasets: [{
                        label: 'Doanh thu (Triệu VNĐ)',
                        data: {$chart_revenues_json},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + context.raw + ' Triệu VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: {
                                callback: function(value) {
                                    return value + ' Tr';
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    };
</script>
HTML;

require_once 'views/layout/footer.php';
?>
