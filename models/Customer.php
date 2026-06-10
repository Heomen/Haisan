<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $id;
    public $username;
    public $password;
    public $full_name;
    public $phone;
    public $email;
    public $created_at;

    public function __construct($db = null) {
        if ($db === null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $db;
        }
    }

    // Check if username already exists
    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Register customer
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, password=:password, full_name=:full_name, phone=:phone, email=:email";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        if ($this->email) {
            $this->email = htmlspecialchars(strip_tags($this->email));
        } else {
            $this->email = null;
        }

        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Login customer
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE username = :username AND password = :password LIMIT 1";
        
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
