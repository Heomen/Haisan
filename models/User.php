<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $full_name;
    public $email;
    public $phone;
    public $role_id;
    public $role_name;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        $query = "SELECT u.*, r.name as role_name FROM " . $this->table_name . " u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.username = :username AND u.password = :password AND u.status = 1";
        
        $stmt = $this->conn->prepare($query);
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        
        $stmt->execute();
        return $stmt;
    }
}
?>
