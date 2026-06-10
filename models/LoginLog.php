<?php
class LoginLog {
    private $conn;
    private $table_name = "login_logs";

    public $id;
    public $user_type;
    public $username;
    public $full_name;
    public $ip_address;
    public $user_agent;
    public $login_time;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
        
        // Tự động tạo bảng login_logs nếu chưa tồn tại
        try {
            $this->conn->query("SELECT 1 FROM `{$this->table_name}` LIMIT 1");
        } catch (PDOException $e) {
            try {
                $query = "CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_type` VARCHAR(50) NOT NULL,
                    `username` VARCHAR(100) NOT NULL,
                    `full_name` VARCHAR(100) NOT NULL,
                    `ip_address` VARCHAR(50) DEFAULT 'Unknown',
                    `user_agent` VARCHAR(255) DEFAULT 'Unknown',
                    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `status` VARCHAR(20) DEFAULT 'Thành công'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $this->conn->exec($query);
            } catch (PDOException $ex) {
                // Bỏ qua lỗi nếu có
            }
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_type=:user_type, username=:username, full_name=:full_name, ip_address=:ip_address, user_agent=:user_agent, status=:status";
        
        $stmt = $this->conn->prepare($query);

        $this->user_type = htmlspecialchars(strip_tags($this->user_type));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        
        if (empty($this->ip_address)) {
            $this->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        $this->ip_address = htmlspecialchars(strip_tags($this->ip_address));
        
        if (empty($this->user_agent)) {
            $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        }
        $this->user_agent = htmlspecialchars(strip_tags($this->user_agent));
        
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":user_type", $this->user_type);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":ip_address", $this->ip_address);
        $stmt->bindParam(":user_agent", $this->user_agent);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read($search = '', $user_type = '', $status = '') {
        $query = "SELECT * FROM " . $this->table_name;
        
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(username LIKE :search OR full_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($user_type)) {
            $conditions[] = "user_type = :user_type";
            $params[':user_type'] = $user_type;
        }

        if (!empty($status)) {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        return $stmt;
    }

    public function deleteOne($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $id = (int)$id;
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>
