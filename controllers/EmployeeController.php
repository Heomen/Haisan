<?php
require_once 'models/Employee.php';
require_once 'models/Department.php';

class EmployeeController {
    private $db;
    private $employee;
    private $department;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->employee = new Employee($this->db);
        $this->department = new Department($this->db);
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function index() {
        $stmt = $this->employee->read();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/employees/index.php';
    }

    private function handleUpload() {
        if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(in_array($ext, $allowed)) {
                $new_name = uniqid() . '.' . $ext;
                $dest = 'uploads/avatars/' . $new_name;
                if(move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                    return $new_name;
                }
            }
        }
        return '';
    }

    public function create() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=employee&action=index");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->employee->code = $_POST['code'] ?? ('NV' . time());
            $this->employee->full_name = $_POST['name'];
            $this->employee->dob = $_POST['dob'];
            $this->employee->phone = $_POST['phone'];
            $this->employee->address = $_POST['address'];
            $this->employee->department_id = $_POST['department_id'] ? $_POST['department_id'] : null;
            
            $avatar = $this->handleUpload();
            $this->employee->avatar = $avatar;

            if ($this->employee->create()) {
                header("Location: index.php?controller=employee&action=index&msg=created");
                exit();
            } else {
                $error = "Không thể thêm nhân viên.";
            }
        }
        
        $dept_stmt = $this->department->read();
        $departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/employees/create.php';
    }

    public function edit() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=employee&action=index");
            exit();
        }

        $this->employee->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        $this->employee->readOne();
        $old_avatar = $this->employee->avatar;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->employee->code = $_POST['code'] ?? $this->employee->code;
            $this->employee->full_name = $_POST['name'];
            $this->employee->dob = $_POST['dob'];
            $this->employee->phone = $_POST['phone'];
            $this->employee->address = $_POST['address'];
            $this->employee->department_id = $_POST['department_id'] ? $_POST['department_id'] : null;
            
            $avatar = $this->handleUpload();
            if($avatar) {
                $this->employee->avatar = $avatar;
                if($old_avatar && file_exists('uploads/avatars/' . $old_avatar)) {
                    unlink('uploads/avatars/' . $old_avatar);
                }
            } else {
                $this->employee->avatar = $old_avatar;
            }

            if ($this->employee->update()) {
                header("Location: index.php?controller=employee&action=index&msg=updated");
                exit();
            } else {
                $error = "Không thể cập nhật nhân viên.";
            }
        }
        
        $dept_stmt = $this->department->read();
        $departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/employees/edit.php';
    }

    public function delete() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=employee&action=index");
            exit();
        }

        $this->employee->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        $this->employee->readOne();
        $avatar = $this->employee->avatar;

        if ($this->employee->delete()) {
            if($avatar && file_exists('uploads/avatars/' . $avatar)) {
                unlink('uploads/avatars/' . $avatar);
            }
            header("Location: index.php?controller=employee&action=index&msg=deleted");
            exit();
        }
    }
}
?>
