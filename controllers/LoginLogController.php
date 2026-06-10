<?php
require_once 'models/LoginLog.php';

class LoginLogController {
    private $db;
    private $loginLog;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // Chỉ Super Admin (role_id = 1) hoặc Admin (role_id = 2) được xem lịch sử đăng nhập
        if ($_SESSION['role_id'] > 2) {
            header("Location: index.php?controller=dashboard&action=index");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->loginLog = new LoginLog($this->db);
    }

    public function index() {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $user_type = isset($_GET['user_type']) ? trim($_GET['user_type']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        $stmt = $this->loginLog->read($search, $user_type, $status);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/login_log/index.php';
    }

    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->loginLog->deleteOne($id);
        }
        header("Location: index.php?controller=loginLog&action=index");
        exit();
    }
}
?>
