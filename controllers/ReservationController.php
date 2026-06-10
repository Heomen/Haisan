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
}
?>
