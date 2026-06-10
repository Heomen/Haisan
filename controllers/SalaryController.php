<?php
require_once 'models/Salary.php';
require_once 'models/Employee.php';

class SalaryController {
    private $db;
    private $salary;
    private $employee;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->salary = new Salary($this->db);
        $this->employee = new Employee($this->db);
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // Tự động khởi tạo bảng salaries và chèn dữ liệu mẫu nếu chưa có
        try {
            $this->db->query("SELECT 1 FROM `salaries` LIMIT 1");
            
            // Kiểm tra xem cột salary_month và salary_year có tồn tại không
            try {
                $this->db->query("SELECT `salary_month` FROM `salaries` LIMIT 1");
            } catch (PDOException $colEx) {
                // Thêm các cột mới nếu chưa tồn tại
                $this->db->exec("ALTER TABLE `salaries` ADD COLUMN `salary_month` INT NOT NULL DEFAULT " . date('m'));
                $this->db->exec("ALTER TABLE `salaries` ADD COLUMN `salary_year` INT NOT NULL DEFAULT " . date('Y'));
                
                // Xóa UNIQUE KEY cũ
                try {
                    $this->db->exec("ALTER TABLE `salaries` DROP INDEX `employee_id`");
                } catch (PDOException $dropEx) {}
                
                // Thêm UNIQUE KEY mới
                try {
                    $this->db->exec("ALTER TABLE `salaries` ADD UNIQUE KEY `employee_month_year` (`employee_id`, `salary_month`, `salary_year`)");
                } catch (PDOException $addEx) {}
            }
        } catch (PDOException $e) {
            try {
                // Tạo bảng salaries hoàn toàn mới nếu chưa tồn tại
                $createTableQuery = "
                    CREATE TABLE IF NOT EXISTS `salaries` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `employee_id` int(11) NOT NULL,
                      `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
                      `allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
                      `fine` decimal(12,2) NOT NULL DEFAULT 0.00,
                      `salary_month` int(11) NOT NULL DEFAULT " . date('m') . ",
                      `salary_year` int(11) NOT NULL DEFAULT " . date('Y') . ",
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `employee_month_year` (`employee_id`, `salary_month`, `salary_year`),
                      CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                $this->db->exec($createTableQuery);

                // Lấy các nhân viên hiện tại để seed bảng lương
                $empCheck = $this->db->query("SELECT id FROM `employees` LIMIT 2");
                $emps = $empCheck->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($emps) > 0) {
                    $insertQuery = "
                        INSERT INTO `salaries` (`employee_id`, `base_salary`, `allowance`, `fine`, `salary_month`, `salary_year`) VALUES
                        (:emp1, 15000000.00, 2000000.00, 500000.00, " . date('m') . ", " . date('Y') . ")
                    ";
                    $params = [':emp1' => $emps[0]];
                    if (count($emps) > 1) {
                        $insertQuery .= ", (:emp2, 12000000.00, 1500000.00, 200000.00, " . date('m') . ", " . date('Y') . ")";
                        $params[':emp2'] = $emps[1];
                    }
                    $stmt = $this->db->prepare($insertQuery);
                    $stmt->execute($params);
                }
            } catch (PDOException $ex) {
                // Bỏ qua lỗi nếu không tạo được bảng tự động
            }
        }
    }

    public function index() {
        $selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        $stmt = $this->salary->read($selected_month, $selected_year);
        $salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/salary/index.php';
    }

    public function create() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=salary&action=index");
            exit();
        }

        $selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->salary->employee_id = $_POST['employee_id'];
            $this->salary->base_salary = $_POST['base_salary'];
            $this->salary->allowance = $_POST['allowance'] ? $_POST['allowance'] : 0.00;
            $this->salary->fine = $_POST['fine'] ? $_POST['fine'] : 0.00;
            $this->salary->salary_month = $_POST['salary_month'] ? $_POST['salary_month'] : $selected_month;
            $this->salary->salary_year = $_POST['salary_year'] ? $_POST['salary_year'] : $selected_year;

            if ($this->salary->create()) {
                header("Location: index.php?controller=salary&action=index&msg=created&month=" . $this->salary->salary_month . "&year=" . $this->salary->salary_year);
                exit();
            } else {
                $error = "Không thể tạo bảng lương cho nhân viên này. Nhân viên có thể đã có bảng lương trong tháng/năm này.";
            }
        }
        
        $emp_stmt = $this->salary->readUnassignedEmployees($selected_month, $selected_year);
        $employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/salary/create.php';
    }

    public function edit() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=salary&action=index");
            exit();
        }

        $this->salary->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        $this->salary->readOne();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->salary->employee_id = $_POST['employee_id'];
            $this->salary->base_salary = $_POST['base_salary'];
            $this->salary->allowance = $_POST['allowance'] ? $_POST['allowance'] : 0.00;
            $this->salary->fine = $_POST['fine'] ? $_POST['fine'] : 0.00;
            $this->salary->salary_month = $_POST['salary_month'] ? $_POST['salary_month'] : date('m');
            $this->salary->salary_year = $_POST['salary_year'] ? $_POST['salary_year'] : date('Y');

            if ($this->salary->update()) {
                header("Location: index.php?controller=salary&action=index&msg=updated&month=" . $this->salary->salary_month . "&year=" . $this->salary->salary_year);
                exit();
            } else {
                $error = "Không thể cập nhật bảng lương.";
            }
        }
        
        // Lấy tất cả nhân viên để hiển thị tên khi sửa
        $all_emp_stmt = $this->employee->read();
        $employees = $all_emp_stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/salary/edit.php';
    }

    public function delete() {
        if($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=salary&action=index");
            exit();
        }

        $this->salary->id = isset($_GET['id']) ? $_GET['id'] : die('Error: Missing ID.');
        if ($this->salary->delete()) {
            header("Location: index.php?controller=salary&action=index&msg=deleted");
            exit();
        }
    }

    public function export() {
        $selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        $stmt = $this->salary->read($selected_month, $selected_year);
        $salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Bang_luong_Thang_" . $selected_month . "_" . $selected_year . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF";

        echo <<<HTML
        <table border="1">
            <thead>
                <tr style="background-color: #0073C2; color: white; font-weight: bold; text-align: center;">
                    <th style="padding: 10px;">Mã nhân viên</th>
                    <th style="padding: 10px;">Tên nhân viên</th>
                    <th style="padding: 10px;">Lương cơ bản (đ)</th>
                    <th style="padding: 10px;">Phụ cấp (đ)</th>
                    <th style="padding: 10px;">Phạt (đ)</th>
                    <th style="padding: 10px;">Tổng lương (đ)</th>
                </tr>
            </thead>
            <tbody>
HTML;

        if (count($salaries) > 0) {
            foreach ($salaries as $item) {
                $code = htmlspecialchars($item['employee_code']);
                $name = htmlspecialchars($item['employee_name']);
                $base = (float)$item['base_salary'];
                $allowance = (float)$item['allowance'];
                $fine = (float)$item['fine'];
                $total = $base + $allowance - $fine;

                echo "<tr>
                        <td style=\"text-align: center;\">{$code}</td>
                        <td>{$name}</td>
                        <td style=\"text-align: right;\">{$base}</td>
                        <td style=\"text-align: right;\">{$allowance}</td>
                        <td style=\"text-align: right;\">{$fine}</td>
                        <td style=\"text-align: right; font-weight: bold;\">{$total}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan=\"6\" style=\"text-align: center;\">Không có dữ liệu bảng lương trong tháng {$selected_month}/{$selected_year}.</td></tr>";
        }

        echo <<<HTML
            </tbody>
        </table>
HTML;
        exit();
    }
}
?>
