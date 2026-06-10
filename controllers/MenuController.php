<?php
require_once 'models/Menu.php';

class MenuController {
    public function __construct() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function index() {
        $menu = new Menu();
        $stmt = $menu->readAll();
        $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/menu/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $menu = new Menu();
            $menu->name = $_POST['name'];
            $menu->price = $_POST['price'];
            $menu->price_unit = $_POST['price_unit'] ?? 'đ/kg';
            $menu->description = $_POST['description'] ?? '';
            $menu->quantity = intval($_POST['quantity'] ?? 0);
            // Tự động chuyển trạng thái khi số lượng = 0
            if ($menu->quantity <= 0) {
                $menu->status = 'inactive';
            } else {
                $menu->status = $_POST['status'] ?? 'active';
            }
            $menu->dish_1_name = $_POST['dish_1_name'] ?? '';
            $menu->dish_1_price = $_POST['dish_1_price'] ?? '';
            $menu->dish_2_name = $_POST['dish_2_name'] ?? '';
            $menu->dish_2_price = $_POST['dish_2_price'] ?? '';
            
            // Xử lý upload ảnh
            $menu->image_url = 'https://via.placeholder.com/300x200?text=No+Image'; // Mặc định
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "menu_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $menu->image_url = "images/" . $new_file_name;
                }
            }

            // Xử lý upload ảnh món chế biến 1
            $menu->dish_1_image = '';
            if(isset($_FILES['dish_1_image']) && $_FILES['dish_1_image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["dish_1_image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "dish1_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                if(move_uploaded_file($_FILES["dish_1_image"]["tmp_name"], $target_file)) {
                    $menu->dish_1_image = "images/" . $new_file_name;
                }
            }

            // Xử lý upload ảnh món chế biến 2
            $menu->dish_2_image = '';
            if(isset($_FILES['dish_2_image']) && $_FILES['dish_2_image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["dish_2_image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "dish2_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                if(move_uploaded_file($_FILES["dish_2_image"]["tmp_name"], $target_file)) {
                    $menu->dish_2_image = "images/" . $new_file_name;
                }
            }

            if ($menu->create()) {
                $_SESSION['success'] = "Thêm món ăn thành công!";
                header("Location: index.php?controller=menu&action=index");
                exit();
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
        require_once 'views/menu/create.php';
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=menu&action=index");
            exit();
        }

        $menu = new Menu();
        if (!$menu->readOne($_GET['id'])) {
            header("Location: index.php?controller=menu&action=index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lưu lại các giá trị dish và ảnh hiện tại từ DB (đã readOne ở trên)
            $existing_dish_1_name  = $menu->dish_1_name;
            $existing_dish_1_price = $menu->dish_1_price;
            $existing_dish_1_image = $menu->dish_1_image;
            $existing_dish_2_name  = $menu->dish_2_name;
            $existing_dish_2_price = $menu->dish_2_price;
            $existing_dish_2_image = $menu->dish_2_image;
            $existing_image_url    = $menu->image_url;

            $menu->name = $_POST['name'];
            $menu->price = $_POST['price'];
            $menu->price_unit = $_POST['price_unit'];
            $menu->description = $_POST['description'];
            $menu->quantity = intval($_POST['quantity'] ?? 0);
            // Tự động chuyển trạng thái dựa theo số lượng
            if ($menu->quantity <= 0) {
                $menu->status = 'inactive';
            } else {
                // Khi số lượng > 0, tự động chuyển về "Đang phục vụ"
                $menu->status = 'active';
            }

            // Giữ nguyên thông tin món chế biến từ DB (không cho form edit ghi đè)
            $menu->dish_1_name  = $existing_dish_1_name;
            $menu->dish_1_price = $existing_dish_1_price;
            $menu->dish_1_image = $existing_dish_1_image;
            $menu->dish_2_name  = $existing_dish_2_name;
            $menu->dish_2_price = $existing_dish_2_price;
            $menu->dish_2_image = $existing_dish_2_image;
            $menu->image_url    = $existing_image_url;

            // Xử lý upload ảnh nếu có cập nhật
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "menu_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $menu->image_url = "images/" . $new_file_name;
                }
            }

            // Upload ảnh món chế biến 1 chỉ cập nhật nếu có file mới
            if(isset($_FILES['dish_1_image']) && $_FILES['dish_1_image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["dish_1_image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "dish1_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                if(move_uploaded_file($_FILES["dish_1_image"]["tmp_name"], $target_file)) {
                    $menu->dish_1_image = "images/" . $new_file_name;
                }
            }

            // Upload ảnh món chế biến 2 chỉ cập nhật nếu có file mới
            if(isset($_FILES['dish_2_image']) && $_FILES['dish_2_image']['error'] == 0) {
                $target_dir = "images/";
                $file_extension = pathinfo($_FILES["dish_2_image"]["name"], PATHINFO_EXTENSION);
                $new_file_name = "dish2_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_file_name;
                if(move_uploaded_file($_FILES["dish_2_image"]["tmp_name"], $target_file)) {
                    $menu->dish_2_image = "images/" . $new_file_name;
                }
            }

            if ($menu->update()) {
                $_SESSION['success'] = "Cập nhật món ăn thành công!";
                header("Location: index.php?controller=menu&action=index");
                exit();
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }

        require_once 'views/menu/edit.php';
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $menu = new Menu();
            $menu->id = $_GET['id'];
            if ($menu->delete()) {
                $_SESSION['success'] = "Xóa món ăn thành công!";
            } else {
                $_SESSION['error'] = "Không thể xóa món ăn này!";
            }
        }
        header("Location: index.php?controller=menu&action=index");
        exit();
    }

    // AJAX: Cập nhật món chế biến (dish_1 hoặc dish_2)
    public function updateDish() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }

        $item_id   = intval($_POST['item_id'] ?? 0);
        $slot      = intval($_POST['slot'] ?? 0);
        $dish_name = trim($_POST['dish_name'] ?? '');
        $dish_price= trim($_POST['dish_price'] ?? '');

        if (!$item_id || !in_array($slot, [1, 2]) || empty($dish_name)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit();
        }

        $prefix = "dish_{$slot}";

        // Đọc ảnh hiện tại từ DB
        $menu = new Menu();
        if (!$menu->readOne($item_id)) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy món ăn']);
            exit();
        }

        $img_field  = "dish_{$slot}_image";
        $dish_image = $menu->$img_field ?? '';

        // Xử lý upload ảnh mới nếu có
        if (isset($_FILES['dish_image']) && $_FILES['dish_image']['error'] == 0) {
            $target_dir = "images/";
            $ext = pathinfo($_FILES['dish_image']['name'], PATHINFO_EXTENSION);
            $new_name = "dish{$slot}_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['dish_image']['tmp_name'], $target_dir . $new_name)) {
                $dish_image = $target_dir . $new_name;
            }
        }

        // Cập nhật DB
        require_once 'config/database.php';
        $db = (new Database())->getConnection();

        $name_col  = "dish_{$slot}_name";
        $price_col = "dish_{$slot}_price";
        $img_col   = "dish_{$slot}_image";

        $sql = "UPDATE menu_items SET {$name_col}=:name, {$price_col}=:price, {$img_col}=:img WHERE id=:id";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            ':name'  => htmlspecialchars(strip_tags($dish_name)),
            ':price' => htmlspecialchars(strip_tags($dish_price)),
            ':img'   => $dish_image,
            ':id'    => $item_id,
        ]);

        echo json_encode(['success' => $ok, 'message' => $ok ? '' : 'Không thể lưu vào database']);
        exit();
    }

    // AJAX: Xóa thông tin món chế biến (dish_1 hoặc dish_2)
    public function deleteDish() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }

        $item_id = intval($_POST['item_id'] ?? 0);
        $slot    = intval($_POST['slot'] ?? 0);

        if (!$item_id || !in_array($slot, [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit();
        }

        require_once 'config/database.php';
        $db = (new Database())->getConnection();

        $name_col  = "dish_{$slot}_name";
        $price_col = "dish_{$slot}_price";
        $img_col   = "dish_{$slot}_image";

        $sql = "UPDATE menu_items SET {$name_col}=NULL, {$price_col}=NULL, {$img_col}=NULL WHERE id=:id";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([':id' => $item_id]);

        echo json_encode(['success' => $ok, 'message' => $ok ? '' : 'Không thể xóa']);
        exit();
    }

    // Trang hiển thị danh sách sản phẩm ưa chuộng (sắp xếp theo lượt đặt)
    public function favorites() {
        $menu = new Menu();
        $stmt_menu = $menu->readAll();
        $menu_items = $stmt_menu->fetchAll(PDO::FETCH_ASSOC);

        // Đếm lượt đặt của từng món từ bảng reservations ở trạng thái completed
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        $stmt_res = $db->query("SELECT pre_order FROM reservations WHERE status = 'completed'");
        $counts = [];
        while ($row = $stmt_res->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['pre_order'])) {
                $dishes = explode(',', $row['pre_order']);
                foreach ($dishes as $d) {
                    $d = trim($d);
                    if (empty($d)) continue;
                    if (!isset($counts[$d])) {
                        $counts[$d] = 0;
                    }
                    $counts[$d]++;
                }
            }
        }

        // Tạo danh sách "sản phẩm" tổng hợp (chỉ bao gồm các món chế biến)
        $products = [];
        foreach ($menu_items as $item) {
            // 1. Món chế biến 1
            if (!empty($item['dish_1_name'])) {
                $products[] = [
                    'id' => $item['id'],
                    'name' => $item['dish_1_name'],
                    'image' => $item['dish_1_image'] ?: 'https://via.placeholder.com/300x200?text=Mon+Che+Bien',
                    'order_count' => $counts[$item['dish_1_name']] ?? 0,
                    'type' => 'dish_1',
                    'is_favorite' => $item['dish_1_is_favorite'] ?? 0
                ];
            }

            // 2. Món chế biến 2
            if (!empty($item['dish_2_name'])) {
                $products[] = [
                    'id' => $item['id'],
                    'name' => $item['dish_2_name'],
                    'image' => $item['dish_2_image'] ?: 'https://via.placeholder.com/300x200?text=Mon+Che+Bien',
                    'order_count' => $counts[$item['dish_2_name']] ?? 0,
                    'type' => 'dish_2',
                    'is_favorite' => $item['dish_2_is_favorite'] ?? 0
                ];
            }
        }

        // Sắp xếp giảm dần theo số lượng đặt, nếu bằng nhau thì sắp xếp theo bảng chữ cái tên món
        usort($products, function($a, $b) {
            if ($b['order_count'] === $a['order_count']) {
                return strcmp($a['name'], $b['name']);
            }
            return $b['order_count'] - $a['order_count'];
        });

        require_once 'views/menu/favorites.php';
    }

    // Action bật/tắt trạng thái ưa thích của sản phẩm/món chế biến
    public function toggleFavorite() {
        if (isset($_GET['id']) && isset($_GET['type'])) {
            $id = intval($_GET['id']);
            $type = $_GET['type'];
            
            $column = '';
            if ($type === 'parent') {
                $column = 'is_favorite';
            } elseif ($type === 'dish_1') {
                $column = 'dish_1_is_favorite';
            } elseif ($type === 'dish_2') {
                $column = 'dish_2_is_favorite';
            }

            if (!empty($column)) {
                $menu = new Menu();
                if ($menu->readOne($id)) {
                    $current_val = 0;
                    if ($type === 'parent') {
                        $current_val = $menu->is_favorite;
                    } elseif ($type === 'dish_1') {
                        $current_val = $menu->dish_1_is_favorite;
                    } elseif ($type === 'dish_2') {
                        $current_val = $menu->dish_2_is_favorite;
                    }

                    $new_val = $current_val ? 0 : 1;
                    if ($menu->updateFavorite($id, $column, $new_val)) {
                        $_SESSION['success'] = "Cập nhật món ưa thích thành công!";
                    } else {
                        $_SESSION['error'] = "Không thể cập nhật món ưa thích!";
                    }
                }
            }
        }
        header("Location: index.php?controller=menu&action=favorites");
        exit();
    }
}
?>
