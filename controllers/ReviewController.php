<?php
require_once 'models/Review.php';

class ReviewController {
    private $db;
    private $review;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->review = new Review($this->db);
    }

    // Frontend: Gửi đánh giá
    public function submit() {
        // Chỉ khách hàng đăng nhập mới được gửi đánh giá
        if (!isset($_SESSION['customer_id'])) {
            header("Location: index.php?review_error=need_login#reviews");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
            $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

            if (empty($comment)) {
                header("Location: index.php?review_error=empty_comment#reviews");
                exit();
            }

            if ($rating < 1 || $rating > 5) {
                header("Location: index.php?review_error=invalid_rating#reviews");
                exit();
            }

            $this->review->customer_id = $_SESSION['customer_id'];
            $this->review->rating = $rating;
            $this->review->comment = $comment;

            if ($this->review->create()) {
                header("Location: index.php?review_success=1#reviews");
                exit();
            } else {
                header("Location: index.php?review_error=save_failed#reviews");
                exit();
            }
        } else {
            header("Location: index.php");
            exit();
        }
    }

    // Backend: Danh sách đánh giá cho Admin
    public function index() {
        $this->checkAdminAuth();
        
        $stmt = $this->review->readAll();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once 'views/review/index.php';
    }

    // Backend: Phê duyệt đánh giá (status = 1)
    public function approve() {
        $this->checkAdminAuth();
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->review->updateStatus($id, 1);
            header("Location: index.php?controller=review&action=index&msg=approved");
            exit();
        }
        header("Location: index.php?controller=review&action=index");
        exit();
    }

    // Backend: Không phê duyệt đánh giá (status = 2)
    public function reject() {
        $this->checkAdminAuth();
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->review->updateStatus($id, 2);
            header("Location: index.php?controller=review&action=index&msg=rejected");
            exit();
        }
        header("Location: index.php?controller=review&action=index");
        exit();
    }

    // Backend: Xóa đánh giá
    public function delete() {
        $this->checkAdminAuth();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            if ($this->review->delete($id)) {
                header("Location: index.php?controller=review&action=index&msg=deleted");
            } else {
                header("Location: index.php?controller=review&action=index&msg=delete_failed");
            }
        } else {
            header("Location: index.php?controller=review&action=index");
        }
        exit();
    }

    // Kiểm tra quyền Admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }
}
?>
