<?php
class Salary {
    private $conn;
    private $table_name = "salaries";

    public $id;
    public $employee_id;
    public $employee_code;
    public $employee_name;
    public $base_salary;
    public $allowance;
    public $fine;
    public $salary_month;
    public $salary_year;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($month = null, $year = null) {
        $query = "SELECT s.*, e.code as employee_code, e.full_name as employee_name 
                  FROM " . $this->table_name . " s
                  JOIN employees e ON s.employee_id = e.id";
        
        $conditions = [];
        $params = [];
        
        if ($month !== null && $month !== '') {
            $conditions[] = "s.salary_month = :month";
            $params[':month'] = $month;
        }
        if ($year !== null && $year !== '') {
            $conditions[] = "s.salary_year = :year";
            $params[':year'] = $year;
        }
        
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY s.id DESC";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET employee_id=:employee_id, base_salary=:base_salary, allowance=:allowance, fine=:fine, salary_month=:salary_month, salary_year=:salary_year";
        $stmt = $this->conn->prepare($query);

        $this->employee_id = htmlspecialchars(strip_tags($this->employee_id));
        $this->base_salary = htmlspecialchars(strip_tags($this->base_salary));
        $this->allowance = htmlspecialchars(strip_tags($this->allowance));
        $this->fine = htmlspecialchars(strip_tags($this->fine));
        $this->salary_month = htmlspecialchars(strip_tags($this->salary_month));
        $this->salary_year = htmlspecialchars(strip_tags($this->salary_year));

        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":base_salary", $this->base_salary);
        $stmt->bindParam(":allowance", $this->allowance);
        $stmt->bindParam(":fine", $this->fine);
        $stmt->bindParam(":salary_month", $this->salary_month);
        $stmt->bindParam(":salary_year", $this->salary_year);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT s.*, e.code as employee_code, e.full_name as employee_name 
                  FROM " . $this->table_name . " s
                  JOIN employees e ON s.employee_id = e.id
                  WHERE s.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->employee_id = $row['employee_id'];
            $this->employee_code = $row['employee_code'];
            $this->employee_name = $row['employee_name'];
            $this->base_salary = $row['base_salary'];
            $this->allowance = $row['allowance'];
            $this->fine = $row['fine'];
            $this->salary_month = $row['salary_month'];
            $this->salary_year = $row['salary_year'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET employee_id=:employee_id, base_salary=:base_salary, allowance=:allowance, fine=:fine, salary_month=:salary_month, salary_year=:salary_year 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->employee_id = htmlspecialchars(strip_tags($this->employee_id));
        $this->base_salary = htmlspecialchars(strip_tags($this->base_salary));
        $this->allowance = htmlspecialchars(strip_tags($this->allowance));
        $this->fine = htmlspecialchars(strip_tags($this->fine));
        $this->salary_month = htmlspecialchars(strip_tags($this->salary_month));
        $this->salary_year = htmlspecialchars(strip_tags($this->salary_year));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":base_salary", $this->base_salary);
        $stmt->bindParam(":allowance", $this->allowance);
        $stmt->bindParam(":fine", $this->fine);
        $stmt->bindParam(":salary_month", $this->salary_month);
        $stmt->bindParam(":salary_year", $this->salary_year);
        $stmt->bindParam(":id", $this->id);

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

    // Lấy danh sách nhân viên chưa có bảng lương theo tháng và năm
    public function readUnassignedEmployees($month = null, $year = null) {
        if ($month === null) $month = date('m');
        if ($year === null) $year = date('Y');

        $query = "SELECT id, code, full_name 
                  FROM employees 
                  WHERE id NOT IN (
                      SELECT employee_id FROM " . $this->table_name . " 
                      WHERE salary_month = :month AND salary_year = :year
                  )
                  ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt;
    }
}
?>
