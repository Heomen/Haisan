<?php
require_once 'models/Reservation.php';

class ReservationController {
    public function __construct() {
        // Kiểm tra xem user có đang ở trong Admin Panel không
        if (isset($_GET['action']) && $_GET['action'] != 'submit_booking') {
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=auth&action=login");
                exit();
            }
        }
    }

    // Hiển thị danh sách cho Admin
    public function index() {
        $reservation = new Reservation();
        
        $status_filter = isset($_GET['status']) ? $_GET['status'] : null;
        $stmt = $reservation->readAll($status_filter);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map location IDs to names (hardcoded for now to match frontend)
        $locations = [
            1 => '18 Trần Kim Xuyến',
            2 => '75A Trần Hưng Đạo',
            3 => 'Tháp C Golden Palace'
        ];

        require_once 'views/reservation/index.php';
    }

    // Xử lý cập nhật trạng thái (Ajax hoặc POST)
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
            $reservation = new Reservation();
            if ($reservation->updateStatus($_POST['id'], $_POST['status'])) {
                $_SESSION['success'] = "Cập nhật trạng thái thành công!";
            } else {
                $_SESSION['error'] = "Không thể cập nhật trạng thái!";
            }
        }
        header("Location: index.php?controller=reservation&action=index");
        exit();
    }

    // Xóa đặt bàn
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $reservation = new Reservation();
            if ($reservation->delete($_POST['id'])) {
                $_SESSION['success'] = "Đã xóa yêu cầu đặt bàn thành công!";
            } else {
                $_SESSION['error'] = "Không thể xóa yêu cầu đặt bàn!";
            }
        }
        header("Location: index.php?controller=reservation&action=index");
        exit();
    }

    // API: Nhận yêu cầu đặt bàn từ Website Khách hàng
    public function submit_booking() {
        if (!isset($_SESSION['customer_id'])) {
            header("Location: index.php?booking_error=need_login");
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $reservation = new Reservation();
            $reservation->customer_name = $_POST['name'];
            $reservation->phone = $_POST['phone'];
            $reservation->location_id = $_POST['location'];
            $reservation->reservation_date = $_POST['date'];
            $reservation->reservation_time = $_POST['time'];
            
            // Xử lý Ghi chú, Ưu đãi và Sinh nhật
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
            $offer = isset($_POST['offer']) ? $_POST['offer'] : 'none';
            $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
            
            $offer_text = '';
            if ($offer == 'offer1') $offer_text = 'Ưu đãi 1: Tháng 3 của nàng';
            elseif ($offer == 'offer2') $offer_text = 'Ưu đãi 2: Hải sản đang bơi';
            elseif ($offer == 'offer3') {
                $offer_text = 'Ưu đãi 3: Tiệc sinh nhật';
                if (!empty($birthday)) {
                    $offer_text .= ' (SN: ' . date('d/m/Y', strtotime($birthday)) . ')';
                }
            }

            if (!empty($offer_text)) {
                $notes = "[Ưu đãi: " . $offer_text . "] " . $notes;
            }
            $reservation->notes = trim($notes);
            
            $pre_order = isset($_POST['pre_order']) ? $_POST['pre_order'] : '';
            if (is_array($pre_order)) {
                $pre_order = implode(', ', $pre_order);
            }

            // Kiểm tra xem món đặt trước có còn hàng hay không trước khi tiến hành tạo đặt bàn
            if (!empty($pre_order)) {
                require_once 'config/database.php';
                $db = (new Database())->getConnection();
                $dishes = explode(',', $pre_order);
                foreach ($dishes as $dish) {
                    $dish = trim($dish);
                    if (empty($dish)) continue;

                    $stmt_menu = $db->prepare("SELECT quantity, status FROM menu_items WHERE name = :dish OR dish_1_name = :dish OR dish_2_name = :dish LIMIT 1");
                    $stmt_menu->execute([':dish' => $dish]);
                    $item = $stmt_menu->fetch(PDO::FETCH_ASSOC);

                    if ($item) {
                        if (intval($item['quantity']) <= 0 || $item['status'] !== 'active') {
                            header("Location: index.php?booking_error=inactive_dish&dish_name=" . urlencode($dish));
                            exit();
                        }
                    }
                }
            }

            $reservation->pre_order = $pre_order;

            if ($reservation->create()) {
                header("Location: index.php?booking_success=1");
            } else {
                header("Location: index.php?booking_error=1");
            }
        }
    }

    // Hiển thị danh sách hóa đơn đặt bàn của khách hàng
    public function bills() {
        $reservation = new Reservation();
        $status_filter = (isset($_GET['status']) && $_GET['status'] !== 'all') ? $_GET['status'] : null;
        $stmt = $reservation->readAll($status_filter);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $locations = [
            1 => '18 Trần Kim Xuyến',
            2 => '75A Trần Hưng Đạo',
            3 => 'Tháp C Golden Palace'
        ];

        $prices_map = $this->getDishPricesMap();

        require_once 'views/reservation/bills.php';
    }

    // Xuất danh sách hóa đơn ra file Excel (.xls)
    public function export_bills() {
        $reservation = new Reservation();
        $status_filter = (isset($_GET['status']) && $_GET['status'] !== 'all') ? $_GET['status'] : null;
        $stmt = $reservation->readAll($status_filter);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $locations = [
            1 => '18 Trần Kim Xuyến',
            2 => '75A Trần Hưng Đạo',
            3 => 'Tháp C Golden Palace'
        ];

        $prices_map = $this->getDishPricesMap();

        // Cấu hình header để trình duyệt tải file Excel .xls
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Danh_sach_hoa_don_" . date('Ymd_His') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Output UTF-8 BOM để hiển thị đúng dấu tiếng Việt trong Excel
        echo "\xEF\xBB\xBF";
        
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background-color: #0c4a6e; color: #ffffff; font-weight: bold; height: 35px;">';
        echo '<th>Mã hóa đơn</th>';
        echo '<th>Tên khách hàng</th>';
        echo '<th>Số điện thoại</th>';
        echo '<th>Chi nhánh</th>';
        echo '<th>Ngày đặt</th>';
        echo '<th>Giờ đặt</th>';
        echo '<th>Món ăn đặt trước</th>';
        echo '<th>Tổng tiền (Dự kiến)</th>';
        echo '<th>Trạng thái</th>';
        echo '<th>Ngày tạo</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($reservations as $res) {
            $id = '#RES-' . $res['id'];
            $name = htmlspecialchars($res['customer_name']);
            $phone = htmlspecialchars($res['phone']);
            $location = $locations[$res['location_id']] ?? 'Không xác định';
            $date = date('d/m/Y', strtotime($res['reservation_date']));
            $time = date('H:i', strtotime($res['reservation_time']));
            $pre_order = htmlspecialchars($res['pre_order'] ?? '');
            
            // Tính tổng tiền
            $total = $this->calculateReservationTotal($res['pre_order'], $prices_map);
            $total_formatted = number_format($total, 0, ',', '.') . ' đ';

            $statusText = '';
            switch($res['status']) {
                case 'pending': $statusText = 'Chờ xác nhận'; break;
                case 'confirmed': $statusText = 'Đã xác nhận'; break;
                case 'completed': $statusText = 'Đã hoàn thành'; break;
                case 'cancelled': $statusText = 'Đã hủy'; break;
            }

            $created_at = date('d/m/Y H:i:s', strtotime($res['created_at']));

            echo '<tr style="height: 25px;">';
            echo '<td>' . $id . '</td>';
            echo '<td>' . $name . '</td>';
            echo '<td>' . $phone . '</td>';
            echo '<td>' . $location . '</td>';
            echo '<td>' . $date . '</td>';
            echo '<td>' . $time . '</td>';
            echo '<td>' . $pre_order . '</td>';
            echo '<td style="text-align: right; font-weight: bold;">' . $total_formatted . '</td>';
            echo '<td>' . $statusText . '</td>';
            echo '<td>' . $created_at . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        exit();
    }

    // Lấy bảng ánh xạ giá món ăn từ cơ sở dữ liệu và fallback
    private function getDishPricesMap() {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT name, price, dish_1_name, dish_1_price, dish_2_name, dish_2_price FROM menu_items");
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        $fallbackMap = [
            'Mực nhồi thịt' => 180000, 'Mực xào dứa' => 150000,
            'Cháo bào ngư' => 120000, 'Bào ngư sống' => 250000,
            'Bề bề hấp' => 350000, 'Bề bề rang muối' => 380000,
            'Cá song chiên' => 450000, 'Cá song kho' => 420000,
            'Cua rang muối' => 480000, 'Cua sốt me' => 480000,
            'Cua hoàng đế hấp' => 1200000, 'Càng cua hấp' => 600000,
            'Cá bơn chiên' => 390000, 'Cá bơn nướng' => 420000,
            'Tu hài hấp xả ớt' => 280000, 'Tu hài nướng mỡ hành' => 290000,
            'Tôm hùm hấp' => 850000, 'Tôm hùm sốt' => 890000,
            'Ốc luộc' => 90000, 'Ốc trứng muối' => 120000
        ];

        foreach ($items as $item) {
            if (!empty($item['name']) && !empty($item['price'])) {
                $map[trim($item['name'])] = floatval($item['price']);
            }
            if (!empty($item['dish_1_name'])) {
                $price = floatval(preg_replace('/[^0-9]/', '', $item['dish_1_price'] ?? ''));
                $map[trim($item['dish_1_name'])] = $price > 0 ? $price : 150000;
            }
            if (!empty($item['dish_2_name'])) {
                $price = floatval(preg_replace('/[^0-9]/', '', $item['dish_2_price'] ?? ''));
                $map[trim($item['dish_2_name'])] = $price > 0 ? $price : 150000;
            }
        }

        return array_merge($fallbackMap, $map);
    }

    // Tính tổng tiền dự kiến của các món đặt trước
    private function calculateReservationTotal($pre_order_str, $prices_map) {
        if (empty($pre_order_str)) {
            return 0;
        }
        $dishes = explode(',', $pre_order_str);
        $total = 0;
        foreach ($dishes as $dish) {
            $dish = trim($dish);
            if (empty($dish)) continue;
            $total += $prices_map[$dish] ?? 0;
        }
        return $total;
    }
}
?>
