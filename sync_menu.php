<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Vô hiệu hóa kiểm tra khóa ngoại tạm thời và xóa sạch dữ liệu cũ
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Thử xóa khóa ngoại và cột category_id để không bị vướng khi Insert (bo qua loi neu khong ton tai)
    try { $conn->exec("ALTER TABLE menu_items DROP FOREIGN KEY menu_items_ibfk_1"); } catch(Exception $e) {}
    try { $conn->exec("ALTER TABLE menu_items DROP COLUMN category_id"); } catch(Exception $e) {}

    // Xóa order_items (nếu có) để tránh lỗi dính khóa ngoại
    // Xóa order_items (nếu có) để tránh lỗi dính khóa ngoại
    $conn->exec("TRUNCATE TABLE order_items");
    // Xóa menu_items
    $conn->exec("TRUNCATE TABLE menu_items");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    $items = [
        [
            'name' => 'Tôm Hùm Bông', 'price' => 2500000, 'price_unit' => 'đ/kg', 'image_url' => 'images/tôm hùm.jpg', 'description' => 'Tôm hùm tươi sống bơi tại bể',
            'dish_1_name' => 'Tôm hùm hấp', 'dish_1_price' => '1.200.000 đ/con', 'dish_1_image' => 'images/tôm hùm hấp.jpg',
            'dish_2_name' => 'Tôm hùm sốt', 'dish_2_price' => '1.350.000 đ/con', 'dish_2_image' => 'images/tôm hùm sốt .jpg'
        ],
        [
            'name' => 'Cua Hoàng Đế', 'price' => 3200000, 'price_unit' => 'đ/kg', 'image_url' => 'images/cua hoangde.jpg', 'description' => 'Cua King Crab nhập khẩu',
            'dish_1_name' => 'Cua hoàng đế hấp', 'dish_1_price' => '2.500.000 đ/con', 'dish_1_image' => 'images/cua hoàng đế hấp.jpg',
            'dish_2_name' => 'Càng cua hấp', 'dish_2_price' => '1.200.000 đ/dĩa', 'dish_2_image' => 'images/càng cua hấp.jpg'
        ],
        [
            'name' => 'Cua Cà Mau', 'price' => 850000, 'price_unit' => 'đ/kg', 'image_url' => 'images/cua ca mau.jpg', 'description' => 'Cua gạch, cua thịt Cà Mau',
            'dish_1_name' => 'Cua rang muối', 'dish_1_price' => '250.000 đ/đĩa', 'dish_1_image' => 'images/cua rang muối.jpg',
            'dish_2_name' => 'Cua sốt me', 'dish_2_price' => '280.000 đ/đĩa', 'dish_2_image' => 'images/cua sốt me.jpg'
        ],
        [
            'name' => 'Bề Bề Chúa', 'price' => 1200000, 'price_unit' => 'đ/kg', 'image_url' => 'images/be be .jpg', 'description' => 'Bề bề loại lớn tươi sống',
            'dish_1_name' => 'Bề bề hấp', 'dish_1_price' => '350.000 đ/đĩa', 'dish_1_image' => 'images/bề bề hấp.jpg',
            'dish_2_name' => 'Bề bề rang muối', 'dish_2_price' => '380.000 đ/đĩa', 'dish_2_image' => 'images/bề bề rang muối.jpg'
        ],
        [
            'name' => 'Cá Song Đen', 'price' => 650000, 'price_unit' => 'đ/kg', 'image_url' => 'images/ca song.jpg', 'description' => 'Cá song tươi sống',
            'dish_1_name' => 'Cá song chiên', 'dish_1_price' => '450.000 đ/con', 'dish_1_image' => 'images/cá song chiên.jpg',
            'dish_2_name' => 'Cá song kho', 'dish_2_price' => '480.000 đ/nồi', 'dish_2_image' => 'images/cá song kho.jpg'
        ],
        [
            'name' => 'Cá Bơn Vàng', 'price' => 1800000, 'price_unit' => 'đ/kg', 'image_url' => 'images/cá bơn.jpg', 'description' => 'Cá bơn Hàn Quốc',
            'dish_1_name' => 'Cá bơn chiên', 'dish_1_price' => '550.000 đ/con', 'dish_1_image' => 'images/cá bơn chiên.jpg',
            'dish_2_name' => 'Cá bơn nướng', 'dish_2_price' => '580.000 đ/con', 'dish_2_image' => 'images/cá bơn nướng.jpg'
        ],
        [
            'name' => 'Tu Hài Canada', 'price' => 1100000, 'price_unit' => 'đ/kg', 'image_url' => 'images/tu hai canada.jpg', 'description' => 'Tu hài nhập khẩu',
            'dish_1_name' => 'Tu hài hấp xả ớt', 'dish_1_price' => '180.000 đ/đĩa', 'dish_1_image' => 'images/tu hài hấp xả ớt.jpg',
            'dish_2_name' => 'Tu hài nướng mỡ hành', 'dish_2_price' => '200.000 đ/đĩa', 'dish_2_image' => 'images/tu hài nướng mỡ hành.jpg'
        ],
        [
            'name' => 'Bào Ngư Hàn Quốc', 'price' => 1250000, 'price_unit' => 'đ/kg', 'image_url' => 'images/bao ngu.jpg', 'description' => 'Bào ngư tươi sống',
            'dish_1_name' => 'Cháo bào ngư', 'dish_1_price' => '150.000 đ/bát', 'dish_1_image' => 'images/cháo bào ngư.jpg',
            'dish_2_name' => 'Bào ngư sống', 'dish_2_price' => '300.000 đ/con', 'dish_2_image' => 'images/bào ngư sống.jpg'
        ],
        [
            'name' => 'Ốc Hương', 'price' => 650000, 'price_unit' => 'đ/kg', 'image_url' => 'images/oc hương.jpg', 'description' => 'Ốc hương size lớn',
            'dish_1_name' => 'Ốc luộc', 'dish_1_price' => '120.000 đ/đĩa', 'dish_1_image' => 'images/ốc luộc.jpg',
            'dish_2_name' => 'Ốc trứng muối', 'dish_2_price' => '180.000 đ/đĩa', 'dish_2_image' => 'images/ốc trứng muối.jpg'
        ],
        [
            'name' => 'Mực Ống', 'price' => 350000, 'price_unit' => 'đ/kg', 'image_url' => 'images/muc ong.jpg', 'description' => 'Mực ống tươi câu',
            'dish_1_name' => 'Mực nhồi thịt', 'dish_1_price' => '220.000 đ/đĩa', 'dish_1_image' => 'images/mực nhồi thịt.jpg',
            'dish_2_name' => 'Mực xào dứa', 'dish_2_price' => '190.000 đ/đĩa', 'dish_2_image' => 'images/Mực xào rứa.jpg'
        ]
    ];

    $query = "INSERT INTO menu_items (name, price, price_unit, image_url, description, status,
              dish_1_name, dish_1_price, dish_1_image, dish_2_name, dish_2_price, dish_2_image) 
              VALUES (:name, :price, :price_unit, :image_url, :description, 'active',
              :d1_name, :d1_price, :d1_img, :d2_name, :d2_price, :d2_img)";
    
    $stmt = $conn->prepare($query);

    foreach ($items as $item) {
        $stmt->execute([
            ':name' => $item['name'],
            ':price' => $item['price'],
            ':price_unit' => $item['price_unit'],
            ':image_url' => $item['image_url'],
            ':description' => $item['description'],
            ':d1_name' => $item['dish_1_name'],
            ':d1_price' => $item['dish_1_price'],
            ':d1_img' => $item['dish_1_image'],
            ':d2_name' => $item['dish_2_name'],
            ':d2_price' => $item['dish_2_price'],
            ':d2_img' => $item['dish_2_image']
        ]);
    }

    echo "Đã đồng bộ thực đơn thành công.";

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
