<?php
require_once 'models/Shift.php';
require_once 'models/Department.php';

class ShiftController {
    private $db;
    private $shift;
    private $department;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->shift = new Shift($this->db);
        $this->department = new Department($this->db);
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // Tự động khởi tạo bảng shifts và chèn dữ liệu mẫu nếu chưa có
        try {
            $this->db->query("SELECT 1 FROM `shifts` LIMIT 1");
        } catch (PDOException $e) {
            try {
                // Tạo bảng shifts
                $createTableQuery = "
                    CREATE TABLE IF NOT EXISTS `shifts` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `code` varchar(20) NOT NULL,
                      `name` varchar(100) NOT NULL,
                      `department_id` int(11) NOT NULL,
                      `start_time` time DEFAULT NULL,
                      `end_time` time DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `code` (`code`),
                      KEY `department_id` (`department_id`),
                      CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                $this->db->exec($createTableQuery);

                // Lấy ID bộ phận đầu tiên có sẵn làm mặc định
                $deptCheck = $this->db->query("SELECT id FROM `departments` LIMIT 1");
                $dept = $deptCheck->fetch(PDO::FETCH_ASSOC);
                $deptId = $dept ? $dept['id'] : 1;

                // Chèn 2 ca làm việc mẫu: Ca sáng và Ca chiều
                $insertQuery = "
                    INSERT INTO `shifts` (`code`, `name`, `department_id`, `start_time`, `end_time`) VALUES
                    ('CS-MCT', 'Ca sáng', :dept_id, '06:00:00', '14:00:00'),
                    ('CC-MCT', 'Ca chiều', :dept_id, '14:00:00', '22:00:00')
                    ON DUPLICATE KEY UPDATE `name`=`name`;
                ";
                $stmt = $this->db->prepare($insertQuery);
                $stmt->execute([':dept_id' => $deptId]);
            } catch (PDOException $ex) {
                // Bỏ qua lỗi nếu không thể tạo bảng tự động
            }
        }
    }

    public function index() {
        $stmt = $this->shift->read();
        $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/shift/index.php';
    }

    public function create() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=shift&action=index");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->shift->code = $_POST['code'] ?? ('SH' . time());
            $this->shift->name = $_POST['name'];
            $this->shift->department_id = $_POST['department_id'];
            $this->shift->start_time = $_POST['start_time'];
            $this->shift->end_time = $_POST['end_time'];

            if ($this->shift->create()) {
                header("Location: index.php?controller=shift&action=index&msg=created");
                exit();
            } else {
                $error = "Không thể thêm ca làm việc. Mã ca làm việc có thể đã tồn tại.";
            }
        }
        
        $dept_stmt = $this->department->read();
        $departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/shift/create.php';
    }

    public function edit() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=shift&action=index");
            exit();
        }

        $this->shift->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        $this->shift->readOne();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->shift->code = $_POST['code'] ?? $this->shift->code;
            $this->shift->name = $_POST['name'];
            $this->shift->department_id = $_POST['department_id'];
            $this->shift->start_time = $_POST['start_time'];
            $this->shift->end_time = $_POST['end_time'];

            if ($this->shift->update()) {
                header("Location: index.php?controller=shift&action=index&msg=updated");
                exit();
            } else {
                $error = "Không thể cập nhật ca làm việc.";
            }
        }
        
        $dept_stmt = $this->department->read();
        $departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/shift/edit.php';
    }

    public function delete() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=shift&action=index");
            exit();
        }

        $this->shift->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        if ($this->shift->delete()) {
            header("Location: index.php?controller=shift&action=index&msg=deleted");
            exit();
        }
    }
}
?>
