<?php
require_once 'config/database.php';

class Reservation {
    private $conn;
    private $table_name = "reservations";

    public $id;
    public $customer_name;
    public $phone;
    public $location_id;
    public $reservation_date;
    public $reservation_time;
    public $notes;
    public $pre_order;
    public $status;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Đọc tất cả đặt bàn, có thể lọc theo trạng thái
    public function readAll($status_filter = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($status_filter) {
            $query .= " WHERE status = :status";
        }
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($status_filter) {
            $stmt->bindParam(':status', $status_filter);
        }

        $stmt->execute();
        return $stmt;
    }

    // Lấy thông tin 1 đặt bàn
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->customer_name = $row['customer_name'];
            $this->phone = $row['phone'];
            $this->location_id = $row['location_id'];
            $this->reservation_date = $row['reservation_date'];
            $this->reservation_time = $row['reservation_time'];
            $this->notes = $row['notes'];
            $this->pre_order = $row['pre_order'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Khách hàng tạo mới đặt bàn từ website
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_name=:customer_name, phone=:phone, location_id=:location_id, 
                      reservation_date=:reservation_date, reservation_time=:reservation_time, 
                      notes=:notes, pre_order=:pre_order, status='pending'";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->customer_name = htmlspecialchars(strip_tags($this->customer_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->location_id = htmlspecialchars(strip_tags($this->location_id));
        $this->reservation_date = htmlspecialchars(strip_tags($this->reservation_date));
        $this->reservation_time = htmlspecialchars(strip_tags($this->reservation_time));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->pre_order = htmlspecialchars(strip_tags($this->pre_order));

        // bind values
        $stmt->bindParam(":customer_name", $this->customer_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":location_id", $this->location_id);
        $stmt->bindParam(":reservation_date", $this->reservation_date);
        $stmt->bindParam(":reservation_time", $this->reservation_time);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":pre_order", $this->pre_order);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Admin cập nhật trạng thái đặt bàn
    public function updateStatus($id, $new_status) {
        // Lấy trạng thái và món đặt trước hiện tại
        $stmt_check = $this->conn->prepare("SELECT status, pre_order FROM " . $this->table_name . " WHERE id = ?");
        $stmt_check->execute([$id]);
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        $current_status = $row ? $row['status'] : null;
        $pre_order = $row ? $row['pre_order'] : '';

        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $new_status = htmlspecialchars(strip_tags($new_status));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            // Giảm số lượng nếu chuyển sang completed từ trạng thái khác completed
            // Mỗi món phụ được đặt giảm đi 1 đơn vị của món cha tương ứng
            if ($new_status === 'completed' && $current_status !== 'completed') {
                if (!empty($pre_order)) {
                    $dishes = explode(',', $pre_order);
                    foreach ($dishes as $dish) {
                        $dish = trim($dish);
                        if (empty($dish)) continue;

                        $stmt_menu = $this->conn->prepare("SELECT id, quantity, status FROM menu_items WHERE name = :dish OR dish_1_name = :dish OR dish_2_name = :dish LIMIT 1");
                        $stmt_menu->execute([':dish' => $dish]);
                        $item = $stmt_menu->fetch(PDO::FETCH_ASSOC);

                        if ($item) {
                            $new_qty = max(0, intval($item['quantity']) - 1);
                            $new_status_menu = ($new_qty <= 0) ? 'inactive' : $item['status'];

                            $stmt_update = $this->conn->prepare("UPDATE menu_items SET quantity = :qty, status = :status WHERE id = :id");
                            $stmt_update->execute([
                                ':qty' => $new_qty,
                                ':status' => $new_status_menu,
                                ':id' => $item['id']
                            ]);
                        }
                    }
                }
            }

            // Tăng lại số lượng (hoàn trả) nếu chuyển từ completed sang trạng thái khác
            // Mỗi món phụ hoàn trả 1 đơn vị cho món cha tương ứng
            if ($new_status !== 'completed' && $current_status === 'completed') {
                if (!empty($pre_order)) {
                    $dishes = explode(',', $pre_order);
                    foreach ($dishes as $dish) {
                        $dish = trim($dish);
                        if (empty($dish)) continue;

                        $stmt_menu = $this->conn->prepare("SELECT id, quantity, status FROM menu_items WHERE name = :dish OR dish_1_name = :dish OR dish_2_name = :dish LIMIT 1");
                        $stmt_menu->execute([':dish' => $dish]);
                        $item = $stmt_menu->fetch(PDO::FETCH_ASSOC);

                        if ($item) {
                            $new_qty = intval($item['quantity']) + 1;
                            $new_status_menu = ($item['status'] === 'inactive') ? 'active' : $item['status'];

                            $stmt_update = $this->conn->prepare("UPDATE menu_items SET quantity = :qty, status = :status WHERE id = :id");
                            $stmt_update->execute([
                                ':qty' => $new_qty,
                                ':status' => $new_status_menu,
                                ':id' => $item['id']
                            ]);
                        }
                    }
                }
            }

            $this->syncReservationToOrder($id);
            return true;
        }
        return false;
    }

    // Đồng bộ đặt bàn sang bảng orders khi hoàn thành
    public function syncReservationToOrder($reservation_id) {
        $reservation = new Reservation();
        if (!$reservation->readOne($reservation_id)) {
            return;
        }

        $db = $this->conn;

        if ($reservation->status === 'completed') {
            $pre_order = $reservation->pre_order;
            $items_to_insert = [];
            $total_amount = 0;

            if (!empty($pre_order)) {
                $dishes = explode(',', $pre_order);
                foreach ($dishes as $dish) {
                    $dish = trim($dish);
                    if (empty($dish)) continue;

                    // Tra cứu giá từ menu_items
                    $stmt = $db->prepare("SELECT id, name, price, dish_1_name, dish_1_price, dish_2_name, dish_2_price FROM menu_items WHERE name = :dish OR dish_1_name = :dish OR dish_2_name = :dish LIMIT 1");
                    $stmt->execute([':dish' => $dish]);
                    $item = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($item) {
                        $menu_item_id = $item['id'];
                        $price = 0;
                        if ($item['dish_1_name'] === $dish) {
                            $price = floatval(preg_replace('/[^0-9]/', '', $item['dish_1_price']));
                        } elseif ($item['dish_2_name'] === $dish) {
                            $price = floatval(preg_replace('/[^0-9]/', '', $item['dish_2_price']));
                        } else {
                            $price = floatval($item['price']);
                        }

                        // Nếu không parse được giá thì dùng giá trị mặc định tránh lỗi
                        if ($price <= 0) {
                            $price = 150000;
                        }

                        $items_to_insert[] = [
                            'menu_item_id' => $menu_item_id,
                            'price' => $price,
                            'quantity' => 1
                        ];
                        $total_amount += $price;
                    }
                }
            }

            // Nếu đơn hàng rỗng (không đặt trước món nào), cho giá trị mặc định là 500.000đ để có doanh thu thực tế
            if ($total_amount <= 0) {
                $total_amount = 500000;
            }

            // Kiểm tra xem đơn hàng đã tồn tại cho đặt bàn này chưa
            $stmt_check = $db->prepare("SELECT id FROM orders WHERE reservation_id = :res_id LIMIT 1");
            $stmt_check->execute([':res_id' => $reservation_id]);
            $order = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $order_id = $order['id'];
                // Cập nhật đơn hàng
                $stmt_update = $db->prepare("UPDATE orders SET total_amount = :total, order_date = :order_date, status = 'Completed' WHERE id = :id");
                $stmt_update->execute([
                    ':total' => $total_amount,
                    ':order_date' => $reservation->reservation_date . ' ' . $reservation->reservation_time,
                    ':id' => $order_id
                ]);

                // Xóa các món cũ của đơn hàng
                $stmt_del_items = $db->prepare("DELETE FROM order_items WHERE order_id = ?");
                $stmt_del_items->execute([$order_id]);
            } else {
                // Tạo đơn hàng mới
                $stmt_insert = $db->prepare("INSERT INTO orders (order_date, total_amount, customer_count, status, reservation_id) VALUES (:order_date, :total, 4, 'Completed', :res_id)");
                $stmt_insert->execute([
                    ':order_date' => $reservation->reservation_date . ' ' . $reservation->reservation_time,
                    ':total' => $total_amount,
                    ':res_id' => $reservation_id
                ]);
                $order_id = $db->lastInsertId();
            }

            // Chèn các món ăn mới vào order_items
            if (!empty($items_to_insert)) {
                $stmt_item = $db->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (:order_id, :menu_item_id, :quantity, :price)");
                foreach ($items_to_insert as $itm) {
                    $stmt_item->execute([
                        ':order_id' => $order_id,
                        ':menu_item_id' => $itm['menu_item_id'],
                        ':quantity' => $itm['quantity'],
                        ':price' => $itm['price']
                    ]);
                }
            }
        } else {
            // Nếu chuyển trạng thái khác 'completed', xóa đơn hàng đồng bộ để không tính doanh thu
            $stmt_del = $db->prepare("DELETE FROM orders WHERE reservation_id = :res_id");
            $stmt_del->execute([':res_id' => $reservation_id]);
        }
    }

    // Xóa đặt bàn
    public function delete($id) {
        // Lấy trạng thái và pre_order trước khi xóa
        $stmt_check = $this->conn->prepare("SELECT status, pre_order FROM " . $this->table_name . " WHERE id = ?");
        $stmt_check->execute([$id]);
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        $current_status = $row ? $row['status'] : null;
        $pre_order      = $row ? $row['pre_order'] : '';

        if ($current_status === 'completed') {
            // Đơn đã hoàn thành: hàng đã tiêu thụ thực tế
            // → KHÔNG hoàn lại số lượng menu
            // → GIỮ LẠI bản ghi orders (để không mất dữ liệu doanh thu)
        } else {
            // Đơn chưa hoàn thành: số lượng menu chưa bị trừ
            // → KHÔNG cần hoàn lại số lượng
            // → Xóa bản ghi orders liên quan (nếu có) vì chưa thành doanh thu
            $stmt_del = $this->conn->prepare("DELETE FROM orders WHERE reservation_id = ?");
            $stmt_del->execute([$id]);
        }

        $id = htmlspecialchars(strip_tags($id));
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
