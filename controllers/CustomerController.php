<?php
require_once 'models/Customer.php';

class CustomerController {
    private $customer;

    public function __construct() {
        $this->customer = new Customer();
    }

    public function login() {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            if (empty($username) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.']);
                exit();
            }

            $this->customer->username = $username;
            $this->customer->password = md5($password);

            $stmt = $this->customer->login();
            
            require_once 'models/LoginLog.php';
            $database = new Database();
            $db = $database->getConnection();
            $log = new LoginLog($db);
            $log->user_type = 'Khách hàng';
            $log->username = $username;

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['customer_id'] = $row['id'];
                $_SESSION['customer_name'] = $row['full_name'];
                $_SESSION['customer_phone'] = $row['phone'];
                $_SESSION['customer_email'] = $row['email'];
                $_SESSION['customer_username'] = $row['username'];

                // Ghi log đăng nhập thành công
                $log->full_name = $row['full_name'];
                $log->status = 'Thành công';
                $log->create();

                echo json_encode(['status' => 'success', 'message' => 'Đăng nhập thành công!']);
                exit();
            } else {
                // Ghi log đăng nhập thất bại
                $log->full_name = 'Không xác định';
                $stmt_check = $db->prepare("SELECT full_name FROM customers WHERE username = ? LIMIT 1");
                $stmt_check->execute([$username]);
                if ($stmt_check->rowCount() > 0) {
                    $log->full_name = $stmt_check->fetch(PDO::FETCH_ASSOC)['full_name'];
                }
                
                $log->status = 'Thất bại';
                $log->create();

                echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
                exit();
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ.']);
        exit();
    }

    public function register() {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';

            if (empty($username) || empty($password) || empty($full_name) || empty($phone)) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.']);
                exit();
            }

            // Check if username exists
            if ($this->customer->usernameExists($username)) {
                echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập này đã tồn tại trên hệ thống.']);
                exit();
            }

            $this->customer->username = $username;
            $this->customer->password = md5($password);
            $this->customer->full_name = $full_name;
            $this->customer->phone = $phone;
            $this->customer->email = !empty($email) ? $email : null;

            if ($this->customer->register()) {
                // Auto log in after register
                $_SESSION['customer_id'] = $this->customer->id;
                $_SESSION['customer_name'] = $this->customer->full_name;
                $_SESSION['customer_phone'] = $this->customer->phone;
                $_SESSION['customer_email'] = $this->customer->email;
                $_SESSION['customer_username'] = $this->customer->username;

                // Ghi log đăng nhập thành công sau khi đăng ký
                require_once 'models/LoginLog.php';
                $database = new Database();
                $db = $database->getConnection();
                $log = new LoginLog($db);
                $log->user_type = 'Khách hàng';
                $log->username = $username;
                $log->full_name = $full_name;
                $log->status = 'Thành công';
                $log->create();

                echo json_encode(['status' => 'success', 'message' => 'Đăng ký tài khoản thành công!']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Đăng ký thất bại. Vui lòng thử lại sau.']);
                exit();
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ.']);
        exit();
    }

    public function logout() {
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_name']);
        unset($_SESSION['customer_phone']);
        unset($_SESSION['customer_email']);
        unset($_SESSION['customer_username']);
        header("Location: index.php");
        exit();
    }
}
?>
