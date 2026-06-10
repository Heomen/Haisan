<?php
require_once 'models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        $error = '';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = md5($_POST['password']);

            $this->user->username = $username;
            $this->user->password = $password;

            $stmt = $this->user->login();
            
            require_once 'models/LoginLog.php';
            $log = new LoginLog($this->db);
            $log->user_type = 'Quản trị';
            $log->username = $username;

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['role_name'] = $row['role_name'];

                // Ghi log thành công
                $log->full_name = $row['full_name'];
                $log->status = 'Thành công';
                $log->create();

                header("Location: index.php?controller=dashboard&action=index");
                exit();
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng, hoặc tài khoản bị khóa.";
                
                // Ghi log thất bại
                $log->full_name = 'Không xác định';
                $stmt_check = $this->db->prepare("SELECT full_name FROM users WHERE username = ? LIMIT 1");
                $stmt_check->execute([$username]);
                if ($stmt_check->rowCount() > 0) {
                    $log->full_name = $stmt_check->fetch(PDO::FETCH_ASSOC)['full_name'];
                }
                
                $log->status = 'Thất bại';
                $log->create();
            }
        }
        require_once 'views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>
