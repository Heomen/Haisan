<?php
require_once 'config/database.php';

class Menu {
    private $conn;
    private $table_name = "menu_items";

    public $id;
    public $name;
    public $price;
    public $price_unit;
    public $image_url;
    public $description;
    public $quantity;
    public $status;
    public $created_at;

    public $dish_1_name;
    public $dish_1_price;
    public $dish_1_image;
    public $dish_2_name;
    public $dish_2_price;
    public $dish_2_image;
    public $is_favorite;
    public $dish_1_is_favorite;
    public $dish_2_is_favorite;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();

        // Tự động kiểm tra và thêm các cột ưa thích nếu chưa có
        try {
            $stmt = $this->conn->query("SHOW COLUMNS FROM `menu_items` LIKE 'is_favorite'");
            if ($stmt->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE `menu_items` ADD COLUMN `is_favorite` TINYINT(1) DEFAULT 0");
            }
            $stmt1 = $this->conn->query("SHOW COLUMNS FROM `menu_items` LIKE 'dish_1_is_favorite'");
            if ($stmt1->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE `menu_items` ADD COLUMN `dish_1_is_favorite` TINYINT(1) DEFAULT 0");
            }
            $stmt2 = $this->conn->query("SHOW COLUMNS FROM `menu_items` LIKE 'dish_2_is_favorite'");
            if ($stmt2->rowCount() == 0) {
                $this->conn->exec("ALTER TABLE `menu_items` ADD COLUMN `dish_2_is_favorite` TINYINT(1) DEFAULT 0");
            }
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu có
        }
    }

    // Đọc tất cả món ăn
    public function readAll($status = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($status) {
            $query .= " WHERE status = :status";
        }
        $query .= " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        $stmt->execute();
        return $stmt;
    }

    // Lấy thông tin 1 món
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->price = $row['price'];
            $this->price_unit = $row['price_unit'];
            $this->image_url = $row['image_url'];
            $this->description = $row['description'];
            $this->quantity = $row['quantity'] ?? 0;
            $this->status = $row['status'];
            $this->dish_1_name = $row['dish_1_name'];
            $this->dish_1_price = $row['dish_1_price'];
            $this->dish_1_image = $row['dish_1_image'];
            $this->dish_2_name = $row['dish_2_name'];
            $this->dish_2_price = $row['dish_2_price'];
            $this->dish_2_image = $row['dish_2_image'];
            $this->is_favorite = $row['is_favorite'] ?? 0;
            $this->dish_1_is_favorite = $row['dish_1_is_favorite'] ?? 0;
            $this->dish_2_is_favorite = $row['dish_2_is_favorite'] ?? 0;
            return true;
        }
        return false;
    }

    // Thêm mới món ăn
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, price=:price, price_unit=:price_unit, image_url=:image_url, description=:description, quantity=:quantity, status=:status,
                      dish_1_name=:dish_1_name, dish_1_price=:dish_1_price, dish_1_image=:dish_1_image,
                      dish_2_name=:dish_2_name, dish_2_price=:dish_2_price, dish_2_image=:dish_2_image";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->price_unit = htmlspecialchars(strip_tags($this->price_unit));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->quantity = intval($this->quantity);
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->dish_1_name = htmlspecialchars(strip_tags($this->dish_1_name));
        $this->dish_1_price = htmlspecialchars(strip_tags($this->dish_1_price));
        $this->dish_1_image = htmlspecialchars(strip_tags($this->dish_1_image));
        $this->dish_2_name = htmlspecialchars(strip_tags($this->dish_2_name));
        $this->dish_2_price = htmlspecialchars(strip_tags($this->dish_2_price));
        $this->dish_2_image = htmlspecialchars(strip_tags($this->dish_2_image));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":price_unit", $this->price_unit);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":dish_1_name", $this->dish_1_name);
        $stmt->bindParam(":dish_1_price", $this->dish_1_price);
        $stmt->bindParam(":dish_1_image", $this->dish_1_image);
        $stmt->bindParam(":dish_2_name", $this->dish_2_name);
        $stmt->bindParam(":dish_2_price", $this->dish_2_price);
        $stmt->bindParam(":dish_2_image", $this->dish_2_image);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cập nhật món ăn
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, price=:price, price_unit=:price_unit, image_url=:image_url, description=:description, quantity=:quantity, status=:status,
                      dish_1_name=:dish_1_name, dish_1_price=:dish_1_price, dish_1_image=:dish_1_image,
                      dish_2_name=:dish_2_name, dish_2_price=:dish_2_price, dish_2_image=:dish_2_image
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->price_unit = htmlspecialchars(strip_tags($this->price_unit));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->quantity = intval($this->quantity);
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->dish_1_name = htmlspecialchars(strip_tags($this->dish_1_name));
        $this->dish_1_price = htmlspecialchars(strip_tags($this->dish_1_price));
        $this->dish_1_image = htmlspecialchars(strip_tags($this->dish_1_image));
        $this->dish_2_name = htmlspecialchars(strip_tags($this->dish_2_name));
        $this->dish_2_price = htmlspecialchars(strip_tags($this->dish_2_price));
        $this->dish_2_image = htmlspecialchars(strip_tags($this->dish_2_image));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":price_unit", $this->price_unit);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":dish_1_name", $this->dish_1_name);
        $stmt->bindParam(":dish_1_price", $this->dish_1_price);
        $stmt->bindParam(":dish_1_image", $this->dish_1_image);
        $stmt->bindParam(":dish_2_name", $this->dish_2_name);
        $stmt->bindParam(":dish_2_price", $this->dish_2_price);
        $stmt->bindParam(":dish_2_image", $this->dish_2_image);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa món ăn
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cập nhật trạng thái yêu thích
    public function updateFavorite($id, $column, $value) {
        if (!in_array($column, ['is_favorite', 'dish_1_is_favorite', 'dish_2_is_favorite'])) {
            return false;
        }
        $query = "UPDATE " . $this->table_name . " SET {$column} = :val WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':val' => intval($value),
            ':id' => intval($id)
        ]);
    }
}
?>
