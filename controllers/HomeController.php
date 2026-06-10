<?php
require_once 'models/Menu.php';

class HomeController {
    public function index() {
        // Lấy danh sách thực đơn từ Database
        $menu = new Menu();
        $stmt = $menu->readAll(); // Lấy tất cả để hiển thị trạng thái
        $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách đánh giá đã duyệt
        require_once 'models/Review.php';
        $review = new Review();
        $stmt_reviews = $review->readApproved();
        $approved_reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/home/index.php';
    }

    public function ai_chat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            if (empty($message)) {
                echo json_encode(['reply' => 'Bạn hãy nhập câu hỏi để tôi tư vấn nhé!']);
                exit;
            }

            // Lấy danh sách thực đơn & giá cả thực tế từ DB để so khớp
            $menu = new Menu();
            $stmt = $menu->readAll();
            $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Xử lý trực tiếp và trả về câu trả lời thông minh dựa trên dữ liệu website
            $reply = $this->getOfflineReply($message, $menu_items);

            header('Content-Type: application/json');
            echo json_encode(['reply' => $reply]);
            exit;
        }
    }

    /**
     * Trả lời thông minh giới hạn trong phạm vi website.
     * Phân tích câu hỏi và trả lời dựa trên dữ liệu thực đơn trong DB và thông tin nhà hàng.
     */
    private function getOfflineReply($message, $menu_items) {
        $msg = mb_strtolower($message, 'UTF-8');

        // --- 1. Kiểm tra câu hỏi ngoài phạm vi (Out of Scope) ---
        $web_keywords = [
            'chào', 'hello', 'hi', 'cơ sở', 'chi nhánh', 'địa chỉ', 'địa điểm', 'đường', 
            'trần hưng đạo', 'trần kim xuyến', 'golden palace', 'mễ trì', 'ưu đãi', 'khuyến mãi', 
            'giảm giá', 'giảm', 'tặng', 'sinh nhật', 'bánh kem', 'trang trí', 'tiệc', 'đặt bàn', 
            'đặt chỗ', 'book', 'thực đơn', 'menu', 'món', 'ăn', 'uống', 'giá', 'bao nhiêu', 
            'tiền', 'cua', 'ghẹ', 'tôm', 'cá', 'mực', 'bào ngư', 'bề bề', 'tu hài', 'ốc', 
            'sashimi', 'lẩu', 'hải sản', 'nhà hàng', 'seafood', 'giới thiệu', 'review', 'đánh giá', 'phản hồi'
        ];
        
        $is_related = false;
        foreach ($web_keywords as $kw) {
            if (strpos($msg, $kw) !== false) {
                $is_related = true;
                break;
            }
        }
        
        $unrelated_keywords = [
            'thời tiết', 'chính trị', 'tin tức', 'lập trình', 'code', 'python', 'java', 'html', 
            'css', 'game', 'phim', 'nhạc', 'ca sĩ', 'diễn viên', 'đá bóng', 'thể thao', 'tỷ số', 
            'học tập', 'toán', 'lý', 'hóa', 'tiếng anh', 'dịch', 'viết hộ', 'làm thơ'
        ];
        $is_unrelated = false;
        foreach ($unrelated_keywords as $ukw) {
            if (strpos($msg, $ukw) !== false) {
                $is_unrelated = true;
                break;
            }
        }

        if (!$is_related || $is_unrelated) {
            return '🤖 Xin lỗi quý khách, tôi là trợ lý ảo của <b>SeaFood</b> và chỉ có thể hỗ trợ các thông tin liên quan đến <b>nhà hàng, thực đơn, cơ sở, ưu đãi, dịch vụ đặt bàn</b> của SeaFood thôi ạ. Quý khách có câu hỏi nào về nhà hàng không ạ? 😊';
        }

        // --- 2. Định nghĩa các cờ phân tích câu hỏi ---
        $has_branch1 = preg_match('/(cơ sở 1|chi nhánh 1|trần kim xuyến|kim xuyến|18)/u', $msg);
        $has_branch2 = preg_match('/(cơ sở 2|chi nhánh 2|trần hưng đạo|hưng đạo|75a)/u', $msg);
        $has_branch3 = preg_match('/(cơ sở 3|chi nhánh 3|golden palace|palace|tháp c|mễ trì)/u', $msg);
        $has_promo = preg_match('/(ưu đãi|khuyến mãi|giảm giá|khuyến mại|sale|giảm|tặng|quà)/u', $msg);
        $has_location = preg_match('/(ở đâu|địa chỉ|địa điểm|đường|chỉ đường|ở nhà hàng nào)/u', $msg);

        // --- 3. Xử lý các câu hỏi kết hợp Cơ sở + Khuyến mãi ---
        if ($has_branch2 && $has_promo) {
            return '🎁 **Ưu đãi đặc biệt tại cơ sở 2 (75A Trần Hưng Đạo):**<br>- Giảm giá cực sốc **1.5 triệu/kg** đối với Tôm hùm bông.<br>- Giảm **50%** cho các loại hải sản: Cá song (các loại), Cua, Ghẹ, Cá tầm.<br>- Tặng gói trang trí 1 triệu & Bánh kem cho tiệc sinh nhật trong tháng. Quý khách có muốn đặt bàn tại đây không ạ? 🦞';
        }
        if ($has_branch1 && $has_promo) {
            return '🎁 **Ưu đãi tại cơ sở 1 (18 Trần Kim Xuyến):**<br>Áp dụng các chương trình chung toàn hệ thống:<br>- Giảm **1 triệu/kg** Cua King Crab (áp dụng tháng 3 cho bàn có khách nữ).<br>- Giảm **1 triệu/kg** Tôm hùm bông.<br>- Tặng gói trang trí 1 triệu & Bánh kem cho tiệc sinh nhật. Quý khách có muốn đặt bàn tại đây không ạ? ✨';
        }
        if ($has_branch3 && $has_promo) {
            return '🎁 **Ưu đãi tại cơ sở 3 (Tháp C Golden Palace):**<br>Áp dụng các chương trình chung toàn hệ thống:<br>- Giảm **1 triệu/kg** Cua King Crab (áp dụng tháng 3 cho bàn có khách nữ).<br>- Giảm **1 triệu/kg** Tôm hùm bông.<br>- Tặng gói trang trí 1 triệu & Bánh kem cho tiệc sinh nhật. Quý khách có muốn đặt bàn tại đây không ạ? ✨';
        }

        // --- 4. Xử lý các câu hỏi Địa chỉ cơ sở cụ thể ---
        if ($has_branch1 && $has_location) {
            return '📍 **SeaFood Cơ sở 1:** 18 Trần Kim Xuyến. Không gian rộng rãi, sang trọng với bể hải sản tươi sống trực tiếp. Quý khách có muốn đặt bàn tại cơ sở này không ạ? 😊';
        }
        if ($has_branch2 && $has_location) {
            return '📍 **SeaFood Cơ sở 2:** 75A Trần Hưng Đạo. Nằm ở trung tâm thành phố, hiện có rất nhiều ưu đãi giảm tới 50% hải sản đang bơi. Quý khách có muốn đặt bàn tại đây không ạ? 🦞';
        }
        if ($has_branch3 && $has_location) {
            return '📍 **SeaFood Cơ sở 3:** Tháp C Golden Palace (Mễ Trì). Không gian đầm ấm, rất phù hợp cho liên hoan gia đình và công ty. Quý khách có muốn đặt bàn tại đây không ạ? ✨';
        }

        // --- 5. So sánh giá đắt nhất / rẻ nhất dựa trên Database thực tế ---
        if (preg_match('/(rẻ nhất|thấp nhất|giá rẻ)/u', $msg)) {
            $cheapest = null;
            foreach ($menu_items as $item) {
                if (isset($item['status']) && $item['status'] !== 'active') continue;
                if ($cheapest === null || $item['price'] < $cheapest['price']) {
                    $cheapest = $item;
                }
            }
            if ($cheapest) {
                $p = number_format($cheapest['price'], 0, ',', '.');
                $u = $cheapest['price_unit'] ?? 'kg';
                return "💡 Món có giá tươi sống rẻ nhất hiện tại là <b>{$cheapest['name']}</b> với giá chỉ <b>{$p}đ/{$u}</b>. Quý khách có thể chọn món này để chuẩn bị bữa ăn tiết kiệm mà vẫn tươi ngon nhé! 🐟";
            }
        }
        if (preg_match('/(đắt nhất|cao nhất|sang nhất|quý hiếm)/u', $msg)) {
            $expensive = null;
            foreach ($menu_items as $item) {
                if (isset($item['status']) && $item['status'] !== 'active') continue;
                if ($expensive === null || $item['price'] > $expensive['price']) {
                    $expensive = $item;
                }
            }
            if ($expensive) {
                $p = number_format($expensive['price'], 0, ',', '.');
                $u = $expensive['price_unit'] ?? 'kg';
                return "👑 Món cao cấp và có giá cao nhất hiện tại là <b>{$expensive['name']}</b> với giá <b>{$p}đ/{$u}</b>. Đây là món hải sản thượng hạng, cực kỳ thích hợp cho các bữa tiệc sang trọng! 🦞";
            }
        }

        // --- 6. Tra cứu giá / món ăn cụ thể từ Database (Có độ ưu tiên khớp tên dài nhất) ---
        $matched_item = null;
        $max_match_len = 0;
        foreach ($menu_items as $item) {
            $item_name = mb_strtolower($item['name'], 'UTF-8');
            if (strpos($msg, $item_name) !== false) {
                if (mb_strlen($item_name, 'UTF-8') > $max_match_len) {
                    $matched_item = $item;
                    $max_match_len = mb_strlen($item_name, 'UTF-8');
                }
            }
        }
        if (!$matched_item) {
            // Thử khớp theo từ khóa ngắn hơn (ví dụ: gõ "tôm" khớp "tôm hùm bông")
            foreach ($menu_items as $item) {
                $item_name = mb_strtolower($item['name'], 'UTF-8');
                $words = explode(' ', $item_name);
                foreach ($words as $word) {
                    if (mb_strlen($word, 'UTF-8') >= 3 && strpos($msg, $word) !== false) {
                        if (mb_strlen($item_name, 'UTF-8') > $max_match_len) {
                            $matched_item = $item;
                            $max_match_len = mb_strlen($item_name, 'UTF-8');
                        }
                    }
                }
            }
        }

        if ($matched_item) {
            $price = number_format($matched_item['price'], 0, ',', '.');
            $unit = $matched_item['price_unit'] ?? 'kg';
            $status = (isset($matched_item['status']) && $matched_item['status'] == 'active') ? '🟢 Đang phục vụ' : '🔴 Ngừng phục vụ';
            
            $reply = "🦞 **Thông tin món: {$matched_item['name']}**<br>";
            $reply .= "- **Trạng thái**: {$status}<br>";
            $reply .= "- **Giá tươi sống gốc**: {$price}đ/{$unit}<br>";
            if (!empty($matched_item['dish_1_name'])) {
                $reply .= "- **Món chế biến gợi ý 1**: {$matched_item['dish_1_name']} (Giá: {$matched_item['dish_1_price']})<br>";
            }
            if (!empty($matched_item['dish_2_name'])) {
                $reply .= "- **Món chế biến gợi ý 2**: {$matched_item['dish_2_name']} (Giá: {$matched_item['dish_2_price']})<br>";
            }
            return $reply;
        }

        // --- 7. Chấm điểm các chủ đề chung (Scoring) ---
        $scores = [
            'greetings' => 0,
            'location' => 0,
            'promotion' => 0,
            'booking' => 0,
            'menu' => 0,
            'story' => 0,
            'review' => 0
        ];

        if (preg_match('/(xin chào|hello|hi|chào|hey|alo|ơi)/u', $msg)) $scores['greetings'] += 3;
        if (preg_match('/(ở đâu|địa chỉ|địa điểm|chi nhánh|cơ sở|nhà hàng nào|cửa hàng ở)/u', $msg)) $scores['location'] += 3;
        if (preg_match('/(ưu đãi|khuyến mãi|giảm giá|khuyến mại|sale|giảm|tặng|quà|sinh nhật|bánh kem|trang trí)/u', $msg)) $scores['promotion'] += 3;
        if (preg_match('/(đặt bàn|đặt chỗ|book|giữ chỗ|đặt trước|đăng ký bàn)/u', $msg)) $scores['booking'] += 5;
        if (preg_match('/(thực đơn|menu|món ăn|ăn gì|gợi ý|tư vấn món|có món gì|ngon|món ngon|đặc sản)/u', $msg)) $scores['menu'] += 3;
        if (preg_match('/(giới thiệu|câu chuyện|cam kết|tại sao|nhất|thông tin nhà hàng)/u', $msg)) $scores['story'] += 3;
        if (preg_match('/(đánh giá|review|phản hồi|nhận xét|chất lượng)/u', $msg)) $scores['review'] += 3;

        arsort($scores);
        $highest_topic = key($scores);
        $highest_score = current($scores);

        if ($highest_score >= 3) {
            switch ($highest_topic) {
                case 'greetings':
                    return 'Xin chào quý khách! 🦐 Tôi là trợ lý ảo SeaFood. Tôi có thể giải đáp thông tin về **thực đơn**, **giá cả**, **các chương trình ưu đãi**, **địa chỉ chi nhánh** hoặc hướng dẫn quý khách **đặt bàn**. Quý khách cần hỗ trợ thông tin gì ạ? 😊';
                
                case 'location':
                    return '📍 **SeaFood hiện có 3 cơ sở phục vụ quý khách:**<br>1️⃣ **Cơ sở 1**: 18 Trần Kim Xuyến.<br>2️⃣ **Cơ sở 2**: 75A Trần Hưng Đạo (Cơ sở trung tâm, nhiều ưu đãi nhất).<br>3️⃣ **Cơ sở 3**: Tháp C Golden Palace.<br><br>Quý khách muốn chọn cơ sở nào để chúng em tư vấn đặt bàn và ưu đãi ạ? 🦞';
                
                case 'promotion':
                    return '🎉 **Các chương trình ưu đãi cực hot tại SeaFood:**<br>1️⃣ **Cua King Crab**: Giảm **1 triệu/kg** (áp dụng tháng 3 cho bàn có khách nữ).<br>2️⃣ **Tôm hùm bông**: Giảm **1 triệu/kg** (Cơ sở 75A Trần Hưng Đạo giảm **1.5 triệu/kg**).<br>3️⃣ **Giảm 50%** Cá song, Cua, Ghẹ, Cá tầm (chỉ áp dụng tại 75A Trần Hưng Đạo).<br>4️⃣ **Tiệc sinh nhật**: Tặng gói trang trí 1 triệu & Bánh kem. 🎂';
                
                case 'booking':
                    return '📋 Để đặt bàn nhanh nhất, quý khách vui lòng kéo xuống biểu mẫu **"Đặt bàn"** ngay bên dưới trang chủ này, điền đầy đủ: Tên, Số điện thoại, Cơ sở muốn dùng bữa, Thời gian và chọn món ăn chuẩn bị trước (nếu có). Chúng em sẽ liên hệ xác nhận ngay lập tức ạ! 📞✨';
                
                case 'menu':
                    $active_items = [];
                    foreach ($menu_items as $item) {
                        if (isset($item['status']) && $item['status'] !== 'active') continue;
                        $p = number_format($item['price'], 0, ',', '.');
                        $u = $item['price_unit'] ?? 'kg';
                        $active_items[] = "<b>{$item['name']}</b> ({$p}đ/{$u})";
                    }
                    if (!empty($active_items)) {
                        $list = implode(', ', array_slice($active_items, 0, 5));
                        $more = count($active_items) > 5 ? '... và nhiều hơn nữa!' : '';
                        return "🦞 **Thực đơn hải sản nổi bật hôm nay:**<br>{$list}{$more}<br><br>Bạn cần tra cứu giá món cụ thể nào không, hãy gõ tên món để tôi hiển thị nhé! 😋";
                    }
                    return 'Thực đơn đang được cập nhật, quý khách vui lòng quay lại sau ạ! 🙏';
                
                case 'story':
                    return '🌊 **Hệ thống siêu thị hải sản SeaFood** tự hào với cam kết:<br>✨ *\'Con gì đang bơi chúng tôi đều có, Con gì đang bơi chúng tôi mới nấu\'*.<br>Chúng tôi sở hữu 5 chữ Nhất: **Đa dạng nhất, chất lượng nhất, tươi ngon nhất, giá hợp lý nhất và đông vui nhất**. Ngoài ra, nhà hàng phục vụ các món ăn văn hóa đặc sắc như Lẩu thuyền chài, Sashimi Thuận buồm xuôi gió... 🚢✨';
                
                case 'review':
                    return '💬 **Đánh giá từ khách hàng:**<br>SeaFood luôn nhận được sự tin yêu của quý khách với điểm đánh giá trung bình cao. Quý khách có thể xem các đánh giá thực tế của khách hàng đã trải nghiệm ở phần **\'Đánh giá từ khách hàng\'** ngay trên trang chủ. Quý khách đã đăng nhập tài khoản cũng có thể gửi đánh giá cho chúng em tại đó! 🥰';
            }
        }

        // --- 8. Mặc định khi không phân loại được rõ ràng ---
        return 'Cảm ơn quý khách đã quan tâm đến SeaFood! 🦐 Tôi là trợ lý ảo hỗ trợ thông tin nhà hàng. Quý khách có thể hỏi tôi về:<br>🔹 **Thực đơn & Giá cả** từng món hải sản.<br>🔹 **Địa chỉ** và **Khuyến mãi** tại 3 cơ sở.<br>🔹 Hướng dẫn **Đặt bàn** trực tuyến.<br><br>Hãy nhập câu hỏi để tôi giải đáp ngay nhé! 😊';
    }
}
?>
