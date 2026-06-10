<?php
require_once 'models/User.php';

class DashboardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function index() {
        // Lấy thống kê cơ bản từ DB
        $stats = [
            'total_revenue' => 0,
            'total_customers' => 0,
            'total_employees' => 0,
            'total_orders' => 0
        ];

        // 1. Tổng doanh thu (tất cả các đơn hàng hoàn thành)
        $query_revenue = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'Completed'";
        $stmt = $this->db->prepare($query_revenue);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $stats['total_revenue'] = $row['revenue'] ?? 0;
        }

        // 2. Số lượng đơn đặt bàn
        $query_res = "SELECT COUNT(id) as res_count FROM reservations";
        $stmt = $this->db->prepare($query_res);
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['res_count'] ?? 0;

        // 3. Số lượng nhân viên
        $query_emp = "SELECT COUNT(id) as emp_count FROM employees";
        $stmt = $this->db->prepare($query_emp);
        $stmt->execute();
        $stats['total_employees'] = $stmt->fetch(PDO::FETCH_ASSOC)['emp_count'] ?? 0;

        // 4. Lấy dữ liệu biểu đồ doanh thu 12 tháng của năm hiện tại
        $current_year = date('Y');
        $chart_labels = [];
        $chart_revenues = [];
        for ($m = 1; $m <= 12; $m++) {
            $month_date = $current_year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $month_label = "Tháng " . $m;
            
            $query_chart = "SELECT SUM(total_amount) as revenue FROM orders WHERE DATE_FORMAT(order_date, '%Y-%m') = :month AND status = 'Completed'";
            $stmt = $this->db->prepare($query_chart);
            $stmt->execute([':month' => $month_date]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $chart_labels[] = $month_label;
            $chart_revenues[] = round(($row['revenue'] ?? 0) / 1000000, 2);
        }

        require_once 'views/dashboard/index.php';
    }

    public function export() {
        $stats = [
            'total_revenue' => 0,
            'total_customers' => 0,
            'total_employees' => 0,
            'total_orders' => 0
        ];

        // 1. Tổng doanh thu (tất cả các đơn hàng hoàn thành)
        $query_revenue = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'Completed'";
        $stmt = $this->db->prepare($query_revenue);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $stats['total_revenue'] = $row['revenue'] ?? 0;
        }

        // 2. Số lượng đơn đặt bàn
        $query_res = "SELECT COUNT(id) as res_count FROM reservations";
        $stmt = $this->db->prepare($query_res);
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['res_count'] ?? 0;

        // 3. Số lượng nhân viên
        $query_emp = "SELECT COUNT(id) as emp_count FROM employees";
        $stmt = $this->db->prepare($query_emp);
        $stmt->execute();
        $stats['total_employees'] = $stmt->fetch(PDO::FETCH_ASSOC)['emp_count'] ?? 0;

        $filename = "Bao_cao_tong_quan_he_thong.xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $revenue = (float)$stats['total_revenue'];
        $orders = (int)$stats['total_orders'];
        $employees = (int)$stats['total_employees'];
        $report_date = date('d/m/Y H:i:s');

        echo <<<HTML
        <h2>BÁO CÁO THỐNG KÊ TỔNG QUAN HỆ THỐNG</h2>
        <p>Ngày xuất báo cáo: {$report_date}</p>
        <br>
        <table border="1">
            <thead>
                <tr style="background-color: #0073C2; color: white; font-weight: bold; text-align: center;">
                    <th colspan="2" style="padding: 10px;">Chỉ số tổng quan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; width: 250px;">Tổng Doanh Thu</td>
                    <td style="text-align: right; font-weight: bold; color: #1e3a8a;">{$revenue} đ</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Đơn Đặt Bàn</td>
                    <td style="text-align: right;">{$orders} đơn</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Nhân Viên</td>
                    <td style="text-align: right;">{$employees} nhân sự</td>
                </tr>
            </tbody>
        </table>
HTML;
        exit();
    }
}
?>
