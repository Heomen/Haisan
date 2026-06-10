<?php
require_once 'models/Department.php';

class DepartmentController {
    private $db;
    private $department;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->department = new Department($this->db);
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function index() {
        $stmt = $this->department->read();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/departments/index.php';
    }

    public function create() {
        // Chỉ admin hoặc super admin mới được tạo
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=department&action=index");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->department->code = $_POST['code'];
            $this->department->name = $_POST['name'];
            $this->department->manager_name = isset($_POST['manager_name']) ? $_POST['manager_name'] : '';

            if ($this->department->create()) {
                header("Location: index.php?controller=department&action=index&msg=created");
                exit();
            } else {
                $error = "Không thể thêm bộ phận. Mã bộ phận có thể đã tồn tại.";
            }
        }
        require_once 'views/departments/create.php';
    }

    public function edit() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=department&action=index");
            exit();
        }

        $this->department->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        $this->department->readOne();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->department->code = $_POST['code'];
            $this->department->name = $_POST['name'];
            $this->department->manager_name = isset($_POST['manager_name']) ? $_POST['manager_name'] : '';

            if ($this->department->update()) {
                header("Location: index.php?controller=department&action=index&msg=updated");
                exit();
            } else {
                $error = "Không thể cập nhật bộ phận.";
            }
        }
        require_once 'views/departments/edit.php';
    }

    public function delete() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=department&action=index");
            exit();
        }

        $this->department->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        if ($this->department->delete()) {
            header("Location: index.php?controller=department&action=index&msg=deleted");
            exit();
        }
    }
}
?>
