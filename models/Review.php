<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    public $id;
    public $customer_id;
    public $rating;
    public $comment;
    public $status; // 0: Chờ duyệt, 1: Đã phê duyệt, 2: Không phê duyệt
    public $created_at;

    public function __construct($db = null) {
        if ($db === null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $db;
        }
    }

    // Gửi đánh giá mới từ khách hàng (mặc định status = 0 - Chờ duyệt)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_id = :customer_id, rating = :rating, comment = :comment, status = 0";
        
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));

        // Gán tham số
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy các đánh giá đã được duyệt (status = 1) để hiện trên website
    public function readApproved() {
        $query = "SELECT r.*, c.full_name, c.username 
                  FROM " . $this->table_name . " r 
                  JOIN customers c ON r.customer_id = c.id 
                  WHERE r.status = 1 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy tất cả đánh giá cho admin quản lý
    public function readAll() {
        $query = "SELECT r.*, c.full_name, c.username, c.phone, c.email 
                  FROM " . $this->table_name . " r 
                  JOIN customers c ON r.customer_id = c.id 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Cập nhật trạng thái phê duyệt (1: Phê duyệt, 2: Không phê duyệt)
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $status = htmlspecialchars(strip_tags($status));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    // Xóa đánh giá
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $id = (int)$id;
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
