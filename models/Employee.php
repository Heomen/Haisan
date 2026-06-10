<?php
class Employee {
    private $conn;
    private $table_name = "employees";

    public $id;
    public $code;
    public $full_name;
    public $avatar;
    public $dob;
    public $phone;
    public $address;
    public $department_id;
    public $department_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT e.*, d.name as department_name 
                  FROM " . $this->table_name . " e
                  LEFT JOIN departments d ON e.department_id = d.id
                  ORDER BY e.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET code=:code, full_name=:full_name, avatar=:avatar, dob=:dob, phone=:phone, address=:address, department_id=:department_id";
        $stmt = $this->conn->prepare($query);

        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->dob = htmlspecialchars(strip_tags($this->dob));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->department_id = $this->department_id ? htmlspecialchars(strip_tags($this->department_id)) : null;

        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":avatar", $this->avatar);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":department_id", $this->department_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->code = $row['code'];
            $this->full_name = $row['full_name'];
            $this->avatar = $row['avatar'];
            $this->dob = $row['dob'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->department_id = $row['department_id'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET code=:code, full_name=:full_name, avatar=:avatar, dob=:dob, phone=:phone, address=:address, department_id=:department_id 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->dob = htmlspecialchars(strip_tags($this->dob));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->department_id = $this->department_id ? htmlspecialchars(strip_tags($this->department_id)) : null;
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":avatar", $this->avatar);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":department_id", $this->department_id);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

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
}
?>
