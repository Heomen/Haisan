<?php
$page_title = 'SeaFood - Nhà Hàng Hải Sản';
include __DIR__ . '/../layout/home_header.php';

$cust_name = isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) : '';
$cust_phone = isset($_SESSION['customer_phone']) ? htmlspecialchars($_SESSION['customer_phone']) : '';

// ==================== CSS ====================
echo <<<CSS
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .main-banner { width: 100%; height: 600px; overflow: hidden; position: relative; }
    .main-banner img { width: 100%; height: 100%; object-fit: cover; object-position: center; }

    .hero-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #ffffff 0%, #e8f4f8 100%);
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    .story-title { font-family: 'Playfair Display', serif; color: #0073C2; font-weight: 700; font-size: 36px; margin-bottom: 30px; text-transform: uppercase; }
    .story-content { font-size: 17px; line-height: 1.8; color: #475569; text-align: justify; }
    .story-content p { margin-bottom: 20px; }
    .story-content b, .story-content i { color: #0f172a; }
    .btn-detail {
        border: 1px solid #0073C2; color: #0073C2; background: transparent;
        padding: 10px 30px; border-radius: 30px; font-size: 13px; font-weight: 600;
        text-transform: uppercase; transition: all 0.3s;
        display: inline-flex; align-items: center; gap: 10px; text-decoration: none;
    }
    .btn-detail:hover { background-color: #0073C2; color: white; }

    .menu-section { background-color: #fff; }
    .menu-card {
        border-radius: 15px; overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transition: transform 0.3s; background: #fff; height: 100%; border: 1px solid #f1f5f9;
        position: relative; cursor: pointer;
    }
    .menu-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .menu-card img { width: 100%; height: 200px; object-fit: cover; }
    .menu-info { padding: 25px; text-align: center; }
    .menu-name { font-size: 19px; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
    .menu-price { color: #e11d48; font-weight: 700; font-size: 17px; margin: 0; }

    .status-badge {
        position: absolute; top: 15px; right: 15px;
        padding: 5px 15px; border-radius: 30px; font-size: 11px; font-weight: 700;
        z-index: 5; text-transform: uppercase;
    }
    .badge-active { background: #22c55e; color: white; }
    .badge-inactive { background: #ef4444; color: white; }
    .favorite-badge {
        position: absolute; top: 15px; left: 15px;
        padding: 5px 15px; border-radius: 30px; font-size: 11px; font-weight: 700;
        z-index: 5; text-transform: uppercase;
        background: #ef4444; color: white;
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
    }

    .offers-section { background: linear-gradient(to bottom, #eff6ff 0%, #ffffff 100%); padding: 80px 0; }
    .offers-header { text-align: center; margin-bottom: 50px; }
    .offers-main-title { font-family: 'Playfair Display', serif; color: #0073C2; font-size: 40px; text-transform: uppercase; font-weight: 700; margin-bottom: 15px; }

    .booking-section { background-color: #f8fafc; padding: 80px 0; }
    .booking-form-box {
        background: #fff; max-width: 850px; margin: 0 auto;
        padding: 50px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    }
    .booking-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
    .booking-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; color: #0f172a; margin: 0; }
    .lang-toggle { display: flex; border: 1.5px solid #e2e8f0; border-radius: 30px; overflow: hidden; font-size: 13px; font-weight: 600; }
    .lang-toggle span { padding: 7px 20px; cursor: pointer; transition: all 0.2s; }
    .lang-toggle .active { background: #0073C2; color: #fff; }
    .booking-form-box .form-control, .booking-form-box .form-select {
        padding: 16px 25px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        margin-bottom: 25px; font-size: 15px; color: #334155; transition: all 0.3s;
    }
    .booking-form-box .form-control:focus, .booking-form-box .form-select:focus {
        border-color: #0073C2; box-shadow: 0 0 0 4px rgba(0,115,194,0.1);
    }
    .btn-submit-booking {
        background: #0073C2; border: none; color: #fff;
        padding: 15px 40px; border-radius: 30px; font-weight: 600;
        text-transform: uppercase; display: block; margin: 0 auto; transition: all 0.3s; cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,115,194,0.3);
    }
    .btn-submit-booking:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,115,194,0.4); }

    .offer-banner-row { border-radius: 20px; overflow: hidden; display: flex; align-items: stretch; margin-bottom: 50px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    @media (max-width: 768px) { .offer-banner-row { flex-direction: column; } }
    .offer-image { flex: 0 0 45%; min-height: 400px; position: relative; overflow: hidden; }
    .offer-image img { width: 100%; height: 100%; object-fit: cover; }
    .offer-details { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
    .offer-details-title { font-family: 'Playfair Display', serif; font-size: 24px; color: #0f172a; font-weight: 700; margin-bottom: 25px; line-height: 1.4; }
    .offer-details-text { font-size: 17px; color: #475569; line-height: 1.8; margin-bottom: 15px; }

    .offer-badge {
        position: absolute; top: 0; right: 0;
        background: linear-gradient(45deg, #0073C2, #00A3FF);
        color: white; padding: 10px 25px;
        border-radius: 0 0 0 20px; font-weight: 800; font-size: 15px;
        box-shadow: -5px 5px 15px rgba(0,0,0,0.1); z-index: 10;
        letter-spacing: 1px;
    }

    /* Modal Món Chế Biến */
    .dish-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px);
        display: none; justify-content: center; align-items: center; z-index: 1000;
        transition: all 0.3s ease;
    }
    .dish-modal-content {
        background: #fff; width: 90%; max-width: 800px; border-radius: 25px;
        padding: 40px; position: relative; animation: modalSlideUp 0.4s ease-out;
    }
    @keyframes modalSlideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .dish-modal-close {
        position: absolute; top: 20px; right: 25px; font-size: 28px; cursor: pointer; color: #64748b; transition: color 0.2s;
    }
    .dish-modal-close:hover { color: #0f172a; }
    .dish-modal-title { font-family: 'Playfair Display', serif; font-size: 28px; color: #0073C2; text-align: center; margin-bottom: 35px; text-transform: uppercase; }
    .dishes-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
    @media (max-width: 600px) { .dishes-grid { grid-template-columns: 1fr; } }
    .dish-item { text-align: center; }
    .dish-img-placeholder {
        width: 100%; height: 220px; background: #f1f5f9; border-radius: 15px;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        color: #94a3b8; border: 2px dashed #e2e8f0; margin-bottom: 15px;
    }
    .dish-img-placeholder i { font-size: 40px; margin-bottom: 10px; }
    .dish-name { font-weight: 600; color: #334155; font-size: 18px; }

    .footer-locations {
        background-color: #1a365d;
        background-image: linear-gradient(rgba(26,54,93,0.85), rgba(26,54,93,0.95));
        color: #fff; padding: 80px 0 40px; text-align: center; position: relative;
    }
    .footer-locations::before {
        content: ''; position: absolute; top: -1px; left: 0;
        width: 100%; height: 50px; background: #fff;
        border-radius: 0 0 50% 50% / 0 0 100% 100%;
    }
    .footer-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; margin-bottom: 40px; text-transform: uppercase; color: #ffc107; }
    
    /* Location Cards Styling */
    .location-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        backdrop-filter: blur(5px);
        text-align: left;
    }
    .location-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: #ffc107;
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.15);
    }
    .location-img-wrapper {
        width: 100%;
        height: 220px;
        overflow: hidden;
    }
    .location-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .location-card:hover .location-img {
        transform: scale(1.08);
    }
    .location-info {
        padding: 20px;
    }
    .location-name {
        font-size: 18px;
        font-weight: 600;
        color: #ffc107;
        margin-bottom: 8px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .location-address {
        font-size: 14px;
        color: #e2e8f0;
        line-height: 1.5;
        margin: 0;
    }
    .contact-row { display: flex; justify-content: center; align-items: center; gap: 20px; margin-top: 40px; margin-bottom: 20px; }
    .hotline-text { font-size: 20px; font-weight: 600; color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px 30px; border-radius: 30px; background: rgba(0,0,0,0.2); }

    .floating-actions {
        position: fixed; right: 20px; top: 50%; transform: translateY(-50%);
        display: flex; flex-direction: column; gap: 15px; z-index: 100; align-items: flex-end;
    }
    .floating-btn {
        width: 50px; height: 50px; border-radius: 50%;
        display: flex; justify-content: center; align-items: center;
        color: white; font-size: 24px; text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s;
    }
    .floating-btn:hover { transform: scale(1.1); color: white; }
    .btn-book { width: auto; border-radius: 5px; background-color: #0073C2; font-size: 14px; font-weight: bold; padding: 10px 15px; margin-bottom: 5px; }
    .btn-crab { background-color: #0073C2; border: 2px solid white; font-size: 30px; color: #ff3b30; }
    .btn-zalo { background-color: #0068FF; font-size: 14px; font-weight: bold; }
    .btn-phone { background-color: #4CAF50; }
    .btn-top { background-color: #e0e0e0; color: #555; width: 40px; height: 40px; font-size: 16px; margin-top: 20px; }
    .btn-top:hover { color: #333; }

    /* Custom Alert CSS */
    .custom-alert-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center;
        z-index: 9999; backdrop-filter: blur(4px); transition: all 0.3s;
    }
    .custom-alert-box {
        background: white; padding: 40px; border-radius: 20px;
        max-width: 450px; width: 90%; text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3); transform: scale(0.9); transition: all 0.3s;
    }
    .custom-alert-overlay.show .custom-alert-box { transform: scale(1); }
    .custom-alert-box h4 { color: #0f1b4c; font-weight: 700; margin-bottom: 20px; font-size: 22px; }
    .custom-alert-box p { color: #64748b; margin-bottom: 30px; font-size: 16px; line-height: 1.6; }
    .btn-close-alert {
        background: #0073C2; color: white; border: none;
        padding: 12px 40px; border-radius: 30px; font-weight: 600;
        cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,115,194,0.3);
    }
    .btn-close-alert:hover { background: #005fa3; transform: translateY(-2px); }

    /* Choices.js Custom Overrides for booking form */
    .booking-form-box .choices {
        margin-bottom: 25px;
    }
    .booking-form-box .choices__inner {
        padding: 10px 25px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        background-color: white; font-size: 15px; color: #334155; transition: all 0.3s;
        min-height: 56px; display: flex; align-items: center;
    }
    .booking-form-box .is-focused .choices__inner {
        border-color: #0073C2; box-shadow: 0 0 0 4px rgba(0,115,194,0.1);
    }
    .booking-form-box .choices__list--multiple .choices__item {
        background-color: #0073C2; border: 1px solid #005fa3;
        border-radius: 5px; padding: 4px 10px;
    }

    /* Reviews Section Styles */
    .reviews-section {
        background-color: #f8fafc;
        padding: 80px 0;
        border-top: 1px solid #e2e8f0;
    }
    .review-summary-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        text-align: center;
        border: 1px solid #f1f5f9;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .review-average-num {
        font-size: 48px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 10px;
    }
    .review-stars-display {
        color: #f59e0b;
        font-size: 20px;
        margin-bottom: 10px;
    }
    .review-total-count {
        color: #64748b;
        font-size: 14px;
    }
    .review-list-container {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 15px;
    }
    .review-list-container::-webkit-scrollbar {
        width: 6px;
    }
    .review-list-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .review-list-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .review-list-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .review-card-item {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .review-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.04);
    }
    .review-user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 12px;
    }
    .review-user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: #0073C2;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,115,194,0.15);
    }
    .review-user-meta h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
    }
    .review-user-meta .review-date {
        font-size: 12px;
        color: #94a3b8;
    }
    .review-stars {
        color: #f59e0b;
        font-size: 13px;
    }
    .review-content {
        color: #475569;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }
    .review-form-card {
        background: white;
        border-radius: 15px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    .review-form-title {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 25px;
    }
    .star-select-container {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    .star-select-label {
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin: 0;
    }
    .interactive-stars {
        display: flex;
        gap: 5px;
    }
    .interactive-star-icon {
        font-size: 28px;
        color: #cbd5e1;
        cursor: pointer;
        transition: color 0.2s, transform 0.1s;
    }
    .interactive-star-icon:hover {
        transform: scale(1.1);
    }
    .interactive-star-icon.active {
        color: #f59e0b;
    }
    .review-auth-prompt {
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        border: 1.5px dashed #bfdbfe;
        border-radius: 15px;
        padding: 40px 30px;
        text-align: center;
    }
    .review-auth-prompt p {
        color: #475569;
        font-size: 15px;
        margin-bottom: 20px;
    }
</style>
CSS;

// ==================== BANNER ====================
echo <<<HTML
<section class="main-banner">
    <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
                <img src="images/banner.jpg" class="d-block w-100 h-100" style="object-fit:cover;object-position:center;" alt="Banner 1">
            </div>
            <div class="carousel-item h-100">
                <img src="images/banner2.jpg" class="d-block w-100 h-100" style="object-fit:cover;object-position:center;" alt="Banner 2">
            </div>
            <div class="carousel-item h-100">
                <img src="images/banner3.jpg" class="d-block w-100 h-100" style="object-fit:cover;object-position:center;" alt="Banner 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
HTML;

// ==================== HERO / STORY ====================
echo <<<HTML
<section id="story" class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 pe-lg-4 d-flex flex-column">
                <div class="mb-5">
                    <h1 class="story-title">CÂU CHUYỆN THẾ GIỚI HẢI SẢN</h1>
                    <div class="story-content">
                        <p>
                            Ra đời bằng tình yêu cùng khát vọng tôn vinh ẩm thực Việt và là cầu nối giữa ngư dân các vùng biển từ Bắc vào Nam,
                            tới thực khách yêu thích hải sản bằng cam kết <i>"Con gì đang bơi chúng tôi đều có", "Con gì đang bơi chúng tôi mới nấu"</i>.
                            SeaFood còn cung cấp <b>"Dịch vụ ẩm thực hải sản hạnh phúc và thịnh vượng"</b>, cũng như tự hào khi sở hữu 5 chữ "Nhất":
                            <i>Đa dạng nhất, chất lượng nhất, tươi ngon nhất, giá cả hợp lý nhất và đông vui nhất.</i>
                        </p>
                        <p>
                            Bên cạnh đó, chúng tôi còn là nhà hàng duy nhất <i>phát triển các món ăn văn hóa</i>
                            như: Lẩu thuyền chài cá song đen; Sashimi cá bơn vàng Thuận buồm xuôi gió; Tôm hùm bông Lộc biển phát tài...
                        </p>
                        <p>
                            Không chỉ trân trọng từng giá trị của nền ẩm thực nước nhà, SeaFood còn đóng góp một phần quảng bá,
                            giới thiệu những món ăn đa dạng, mang "quốc hồn quốc túy" của ẩm thực Việt đến bạn bè quốc tế.
                        </p>
                    </div>
                </div>
                <img src="images/đàu bếp và cua 2.jpg" class="img-fluid rounded shadow-sm w-100 mt-auto" alt="Dịch vụ ẩm thực">
            </div>
            <div class="col-lg-6 ps-lg-4 mt-5 mt-lg-0">
                <img src="images/đầu bếp và cua.jpg" class="img-fluid rounded shadow-sm w-100 mb-4" alt="Đầu bếp và Cua">
                <img src="images/đầu bếp và cua 3.jpg" class="img-fluid rounded shadow-sm w-100" alt="Món văn hóa đặc sắc">
            </div>
        </div>
    </div>
</section>
HTML;

// ==================== OFFERS ====================
echo <<<HTML
<section id="offers" class="offers-section">
    <div class="container">
        <div class="offers-header">
            <h2 class="offers-main-title">ƯU ĐÃI SEAFOOD</h2>
        </div>

        <div class="offer-banner-row bg-white">
            <span class="offer-badge">ƯU ĐÃI 1</span>
            <div class="offer-image">
                <img src="images/ưu đãi 1.jpg" alt="Ưu đãi Cua Hoàng Đế">
            </div>
            <div class="offer-details">
                <h3 class="offer-details-title">
                    THÁNG 3 CỦA NÀNG – ƯU ĐÃI 1 TRIỆU/KG CUA HOÀNG ĐẾ
                    <i class="fa-solid fa-crown text-warning"></i>
                </h3>
                <p class="offer-details-text">
                    <i class="fa-solid fa-gift text-danger"></i> Áp dụng cho bàn tiệc có khách nữ.<br>
                    <i class="fa-solid fa-clock text-info"></i> Thời gian đến hết 31/3/2027
                </p>
                <p class="offer-details-text">
                    Tháng của nàng không gì bằng món quà đúng thời điểm! Đây chính là lúc tặng nàng bữa tiệc ấn tượng
                    với cua Hoàng Đế sở hữu hương vị chất lượng hàng đầu thế giới và đang đạt độ ngon sung mãn nhất trong năm.
                </p>
            </div>
        </div>

        <div class="offer-banner-row bg-white">
            <span class="offer-badge">ƯU ĐÃI 2</span>
            <div class="offer-image">
                <img src="images/ưu đãi 2.jpg" alt="Ưu đãi hải sản">
            </div>
            <div class="offer-details">
                <h3 class="offer-details-title" style="color:#0f1b4c;text-transform:uppercase;">
                    HẢI SẢN ĐANG BƠI – ƯU ĐÃI GIÁ HỜI
                </h3>
                <ul class="list-unstyled" style="color:#334155;line-height:1.8;font-size:15px;">
                    <li class="mb-2 d-flex"><i class="fa-solid fa-fish-fins mt-1 me-2" style="color:#0284c7;"></i><span>Cua King Crab: Giảm 1tr/kg.</span></li>
                    <li class="mb-2 d-flex"><i class="fa-solid fa-fish-fins mt-1 me-2" style="color:#0284c7;"></i><span>Tôm hùm bông (các size): Giảm 1tr/kg (riêng 75A Trần Hưng Đạo ưu đãi 1tr5/kg).</span></li>
                    <li class="mb-2 d-flex"><i class="fa-solid fa-fish-fins mt-1 me-2" style="color:#0284c7;"></i><span>Cá Song đỏ: Giảm 500k/kg.</span></li>
                    <li class="mb-2 d-flex"><i class="fa-solid fa-fish-fins mt-1 me-2" style="color:#0284c7;"></i><span>Bào ngư (các loại): Giảm 1tr/kg.</span></li>
                    <li class="mb-2 d-flex"><i class="fa-solid fa-fish-fins mt-1 me-2" style="color:#0284c7;"></i><span>Giảm 50% các loại hải sản gồm: Cá song (các loại), cua, ghẹ, cá tầm tại 75A Trần Hưng Đạo.</span></li>
                </ul>
            </div>
        </div>

        <div class="offer-banner-row bg-white">
            <span class="offer-badge">ƯU ĐÃI 3</span>
            <div class="offer-image">
                <img src="images/ưu đãi 3.jpg" alt="Ưu đãi Sinh Nhật">
            </div>
            <div class="offer-details">
                <h3 class="offer-details-title">TIỆC SINH NHẬT TRỌN VẸN – TẶNG GÓI TRANG TRÍ &amp; BÁNH KEM</h3>
                <p class="offer-details-text">
                    <i class="fa-solid fa-cake-candles text-warning"></i> Áp dụng cho khách có sinh nhật trong tháng.<br>
                    <i class="fa-solid fa-clock text-info"></i> Áp dụng vô thời hạn
                </p>
                <p class="offer-details-text">
                    Ghi dấu tuổi mới thật hoành tráng tại SeaFood. Chúng tôi dành tặng bạn một chiếc bánh kem cao cấp
                    và gói trang trí sinh nhật trị giá 1 triệu đồng để ngày vui thêm phần lung linh, đáng nhớ.
                </p>
            </div>
        </div>
    </div>
</section>
HTML;

// ==================== MENU ====================
echo '<section id="menu" class="menu-section py-5">';
echo '<div class="container">';
echo '<h2 class="text-center story-title mb-4">THỰC ĐƠN HẢI SẢN NỔI BẬT</h2>';
echo <<<HTML
<div class="d-flex justify-content-center mb-5">
    <form class="menu-search-form" onsubmit="searchFood(event)" style="width:100%;max-width:480px;">
        <div class="menu-search-container">
            <i class="fa-solid fa-magnifying-glass menu-search-icon"></i>
            <input type="text" id="menu-search-input" oninput="searchFood()" placeholder="Tìm kiếm món ăn... (vd: Tôm Hùm, Cua)" autocomplete="off">
            <button type="button" id="menu-search-clear" onclick="clearSearch()" style="display:none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </form>
</div>
<style>
    .menu-search-container {
        position: relative;
        display: flex;
        align-items: center;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        padding: 8px 20px;
        box-shadow: 0 4px 20px rgba(0,115,194,0.08);
        transition: all 0.3s ease;
    }
    .menu-search-container:focus-within {
        border-color: #0073C2;
        box-shadow: 0 4px 25px rgba(0,115,194,0.18);
    }
    .menu-search-icon {
        color: #0073C2;
        font-size: 16px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .menu-search-container input {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        flex: 1;
        font-size: 15px;
        color: #334155;
        background: transparent;
        padding: 4px 0 !important;
        margin-bottom: 0 !important;
    }
    .menu-search-container input::placeholder {
        color: #94a3b8;
    }
    #menu-search-clear {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 16px;
        cursor: pointer;
        padding: 0 0 0 10px;
        transition: color 0.2s;
        flex-shrink: 0;
    }
    #menu-search-clear:hover { color: #ef4444; }
    #no-results-message { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
HTML;
$favDishesList = [];
$inactiveDishes = [];

if (!empty($menu_items)) {
    foreach ($menu_items as $item) {
        $name = htmlspecialchars($item['name']);
        $status = $item['status'];
        $cardOpacity = ($status == 'active') ? '' : 'style="opacity: 0.7; filter: grayscale(50%);"';

        $dish1 = !empty($item['dish_1_name']) ? $item['dish_1_name'] : '';
        $dish2 = !empty($item['dish_2_name']) ? $item['dish_2_name'] : '';
        $dishImg1 = !empty($item['dish_1_image']) ? $item['dish_1_image'] : '';
        $dishImg2 = !empty($item['dish_2_image']) ? $item['dish_2_image'] : '';
        $dishPrice1 = !empty($item['dish_1_price']) ? $item['dish_1_price'] : '';
        $dishPrice2 = !empty($item['dish_2_price']) ? $item['dish_2_price'] : '';

        // Phục hồi fallback nếu món db rỗng
        $hasDbDishes = !empty($dish1) || !empty($dish2);
        if (!$hasDbDishes) {
            if (stripos($name, 'Mực') !== false) {
                $dish1 = 'Mực nhồi thịt'; $dish2 = 'Mực xào dứa';
                $dishImg1 = 'images/mực nhồi thịt.jpg'; $dishImg2 = 'images/Mực xào rứa.jpg';
                $dishPrice1 = '180.000 đ/đĩa'; $dishPrice2 = '150.000 đ/đĩa';
            } elseif (stripos($name, 'Bào Ngư') !== false) {
                $dish1 = 'Cháo bào ngư'; $dish2 = 'Bào ngư sống';
                $dishImg1 = 'images/cháo bào ngư.jpg'; $dishImg2 = 'images/bào ngư sống.jpg';
                $dishPrice1 = '120.000 đ/bát'; $dishPrice2 = '250.000 đ/đĩa';
            } elseif (stripos($name, 'Bề Bề') !== false) {
                $dish1 = 'Bề bề hấp'; $dish2 = 'Bề bề rang muối';
                $dishImg1 = 'images/bề bề hấp.jpg'; $dishImg2 = 'images/bề bề rang muối.jpg';
                $dishPrice1 = '350.000 đ/đĩa'; $dishPrice2 = '380.000 đ/đĩa';
            } elseif (stripos($name, 'Cá Song') !== false) {
                $dish1 = 'Cá song chiên'; $dish2 = 'Cá song kho';
                $dishImg1 = 'images/cá song chiên.jpg'; $dishImg2 = 'images/cá song kho.jpg';
                $dishPrice1 = '450.000 đ/đĩa'; $dishPrice2 = '420.000 đ/niêu';
            } elseif (stripos($name, 'Cua Cà Mau') !== false) {
                $dish1 = 'Cua rang muối'; $dish2 = 'Cua sốt me';
                $dishImg1 = 'images/cua rang muối.jpg'; $dishImg2 = 'images/cua sốt me.jpg';
                $dishPrice1 = '480.000 đ/đĩa'; $dishPrice2 = '480.000 đ/đĩa';
            } elseif (stripos($name, 'Cua Hoàng Đế') !== false) {
                $dish1 = 'Cua hoàng đế hấp'; $dish2 = 'Càng cua hấp';
                $dishImg1 = 'images/cua hoàng đế hấp.jpg'; $dishImg2 = 'images/càng cua hấp.jpg';
                $dishPrice1 = '1.200.000 đ/đĩa'; $dishPrice2 = '600.000 đ/đĩa';
            } elseif (stripos($name, 'Cá Bơn') !== false) {
                $dish1 = 'Cá bơn chiên'; $dish2 = 'Cá bơn nướng';
                $dishImg1 = 'images/cá bơn chiên.jpg'; $dishImg2 = 'images/cá bơn nướng.jpg';
                $dishPrice1 = '390.000 đ/đĩa'; $dishPrice2 = '420.000 đ/đĩa';
            } elseif (stripos($name, 'Tu Hài') !== false) {
                $dish1 = 'Tu hài hấp xả ớt'; $dish2 = 'Tu hài nướng mỡ hành';
                $dishImg1 = 'images/tu hài hấp xả ớt.jpg'; $dishImg2 = 'images/tu hài nướng mỡ hành.jpg';
                $dishPrice1 = '280.000 đ/đĩa'; $dishPrice2 = '290.000 đ/đĩa';
            } elseif (stripos($name, 'Tôm Hùm') !== false) {
                $dish1 = 'Tôm hùm hấp'; $dish2 = 'Tôm hùm sốt';
                $dishImg1 = 'images/tôm hùm hấp.jpg'; $dishImg2 = 'images/tôm hùm sốt .jpg';
                $dishPrice1 = '850.000 đ/con'; $dishPrice2 = '890.000 đ/con';
            } elseif (stripos($name, 'Ốc Hương') !== false) {
                $dish1 = 'Ốc luộc'; $dish2 = 'Ốc trứng muối';
                $dishImg1 = 'images/ốc luộc.jpg'; $dishImg2 = 'images/ốc trứng muối.jpg';
                $dishPrice1 = '90.000 đ/đĩa'; $dishPrice2 = '120.000 đ/đĩa';
            } else {
                $dish1 = $name . ' Hấp Thủy Nhiệt / Nướng Mỡ Hành';
                $dish2 = $name . ' Rang Muối / Cháy Tỏi / Lẩu Thuyền Chài';
                $dishPrice1 = '250.000 đ/đĩa';
                $dishPrice2 = '280.000 đ/đĩa';
            }
        }

        if ($status == 'inactive') {
            $inactiveDishes[] = $dish1;
            $inactiveDishes[] = $dish2;
        }

        if (!empty($dish1) && isset($item['dish_1_is_favorite']) && $item['dish_1_is_favorite'] == 1) {
            $favDishesList[] = [
                'parent_name' => $name,
                'dish_name' => $dish1,
                'dish_image' => !empty($dishImg1) ? $dishImg1 : 'https://via.placeholder.com/300x200?text=Mon+Che+Bien',
                'dish_price' => $dishPrice1,
                'card_opacity' => $cardOpacity,
                'dish1' => $dish1, 'dish2' => $dish2, 'dishImg1' => $dishImg1, 'dishImg2' => $dishImg2, 'dishPrice1' => $dishPrice1, 'dishPrice2' => $dishPrice2
            ];
        }
        if (!empty($dish2) && isset($item['dish_2_is_favorite']) && $item['dish_2_is_favorite'] == 1) {
            $favDishesList[] = [
                'parent_name' => $name,
                'dish_name' => $dish2,
                'dish_image' => !empty($dishImg2) ? $dishImg2 : 'https://via.placeholder.com/300x200?text=Mon+Che+Bien',
                'dish_price' => $dishPrice2,
                'card_opacity' => $cardOpacity,
                'dish1' => $dish1, 'dish2' => $dish2, 'dishImg1' => $dishImg1, 'dishImg2' => $dishImg2, 'dishPrice1' => $dishPrice1, 'dishPrice2' => $dishPrice2
            ];
        }
    }
}

// 1. Render khu vực Món Ngon Ưa Chuộng (riêng biệt phía trên)
if (!empty($favDishesList)) {
    echo '<div id="favorite-dishes-container" class="favorite-dishes-section mb-5 w-100">';
    echo '  <h3 class="text-center mb-4" style="font-family: \'Playfair Display\', serif; font-weight: 700; color: #ef4444; letter-spacing: 1px;"><i class="fa-solid fa-heart me-2"></i>MÓN NGON ƯA CHUỘNG</h3>';
    echo '  <div class="row g-4 justify-content-center">';
    foreach ($favDishesList as $fav) {
        echo <<<FAV
        <div class="col-md-4 col-lg-3" style="cursor:pointer;" onclick="showDishModal('{$fav['parent_name']}', '{$fav['dish1']}', '{$fav['dish2']}', '{$fav['dishImg1']}', '{$fav['dishImg2']}', '{$fav['dishPrice1']}', '{$fav['dishPrice2']}')">
            <div class="menu-card" {$fav['card_opacity']}>
                <span class="status-badge" style="background:#ef4444;color:#fff;"><i class="fa-solid fa-heart me-1"></i>Ưa thích</span>
                <img src="{$fav['dish_image']}" alt="{$fav['dish_name']}" style="width:100%;height:200px;object-fit:cover;">
                <div class="menu-info">
                    <h3 class="menu-name">{$fav['dish_name']}</h3>
                    <p class="menu-price">{$fav['dish_price']}</p>
                    <p style="font-size:12px;color:#94a3b8;margin:6px 0 0;"><i class="fa-solid fa-fish me-1"></i>Nguyên liệu: {$fav['parent_name']}</p>
                </div>
            </div>
        </div>
FAV;
    }
    echo '  </div>';
    echo '  <hr class="my-5" style="border-top: 1px dashed #cbd5e1;">';
    echo '</div>';
}

// 2. Render lưới thực đơn hải sản chính
echo '<div class="row g-4">';

if (!empty($menu_items)) {
    foreach ($menu_items as $item) {
        $img    = htmlspecialchars($item['image_url']);
        $name   = htmlspecialchars($item['name']);
        $price  = number_format($item['price'], 0, ',', '.');
        $unit   = htmlspecialchars($item['price_unit']);
        $status = $item['status'];
        
        $statusText = ($status == 'active') ? 'Đang phục vụ' : 'Ngừng phục vụ';
        $statusClass = ($status == 'active') ? 'badge-active' : 'badge-inactive';
        $cardOpacity = ($status == 'active') ? '' : 'style="opacity: 0.7; filter: grayscale(50%);"';

        $dish1 = !empty($item['dish_1_name']) ? $item['dish_1_name'] : '';
        $dish2 = !empty($item['dish_2_name']) ? $item['dish_2_name'] : '';
        $dishImg1 = !empty($item['dish_1_image']) ? $item['dish_1_image'] : '';
        $dishImg2 = !empty($item['dish_2_image']) ? $item['dish_2_image'] : '';
        $dishPrice1 = !empty($item['dish_1_price']) ? $item['dish_1_price'] : '';
        $dishPrice2 = !empty($item['dish_2_price']) ? $item['dish_2_price'] : '';

        $hasDbDishes = !empty($dish1) || !empty($dish2);

        if (!$hasDbDishes) {
            if (stripos($name, 'Mực') !== false) {
                $dish1 = 'Mực nhồi thịt'; $dish2 = 'Mực xào dứa';
                $dishImg1 = 'images/mực nhồi thịt.jpg'; $dishImg2 = 'images/Mực xào rứa.jpg';
                $dishPrice1 = '180.000 đ/đĩa'; $dishPrice2 = '150.000 đ/đĩa';
            } elseif (stripos($name, 'Bào Ngư') !== false) {
                $dish1 = 'Cháo bào ngư'; $dish2 = 'Bào ngư sống';
                $dishImg1 = 'images/cháo bào ngư.jpg'; $dishImg2 = 'images/bào ngư sống.jpg';
                $dishPrice1 = '120.000 đ/bát'; $dishPrice2 = '250.000 đ/đĩa';
            } elseif (stripos($name, 'Bề Bề') !== false) {
                $dish1 = 'Bề bề hấp'; $dish2 = 'Bề bề rang muối';
                $dishImg1 = 'images/bề bề hấp.jpg'; $dishImg2 = 'images/bề bề rang muối.jpg';
                $dishPrice1 = '350.000 đ/đĩa'; $dishPrice2 = '380.000 đ/đĩa';
            } elseif (stripos($name, 'Cá Song') !== false) {
                $dish1 = 'Cá song chiên'; $dish2 = 'Cá song kho';
                $dishImg1 = 'images/cá song chiên.jpg'; $dishImg2 = 'images/cá song kho.jpg';
                $dishPrice1 = '450.000 đ/đĩa'; $dishPrice2 = '420.000 đ/niêu';
            } elseif (stripos($name, 'Cua Cà Mau') !== false) {
                $dish1 = 'Cua rang muối'; $dish2 = 'Cua sốt me';
                $dishImg1 = 'images/cua rang muối.jpg'; $dishImg2 = 'images/cua sốt me.jpg';
                $dishPrice1 = '480.000 đ/đĩa'; $dishPrice2 = '480.000 đ/đĩa';
            } elseif (stripos($name, 'Cua Hoàng Đế') !== false) {
                $dish1 = 'Cua hoàng đế hấp'; $dish2 = 'Càng cua hấp';
                $dishImg1 = 'images/cua hoàng đế hấp.jpg'; $dishImg2 = 'images/càng cua hấp.jpg';
                $dishPrice1 = '1.200.000 đ/đĩa'; $dishPrice2 = '600.000 đ/đĩa';
            } elseif (stripos($name, 'Cá Bơn') !== false) {
                $dish1 = 'Cá bơn chiên'; $dish2 = 'Cá bơn nướng';
                $dishImg1 = 'images/cá bơn chiên.jpg'; $dishImg2 = 'images/cá bơn nướng.jpg';
                $dishPrice1 = '390.000 đ/đĩa'; $dishPrice2 = '420.000 đ/đĩa';
            } elseif (stripos($name, 'Tu Hài') !== false) {
                $dish1 = 'Tu hài hấp xả ớt'; $dish2 = 'Tu hài nướng mỡ hành';
                $dishImg1 = 'images/tu hài hấp xả ớt.jpg'; $dishImg2 = 'images/tu hài nướng mỡ hành.jpg';
                $dishPrice1 = '280.000 đ/đĩa'; $dishPrice2 = '290.000 đ/đĩa';
            } elseif (stripos($name, 'Tôm Hùm') !== false) {
                $dish1 = 'Tôm hùm hấp'; $dish2 = 'Tôm hùm sốt';
                $dishImg1 = 'images/tôm hùm hấp.jpg'; $dishImg2 = 'images/tôm hùm sốt .jpg';
                $dishPrice1 = '850.000 đ/con'; $dishPrice2 = '890.000 đ/con';
            } elseif (stripos($name, 'Ốc Hương') !== false) {
                $dish1 = 'Ốc luộc'; $dish2 = 'Ốc trứng muối';
                $dishImg1 = 'images/ốc luộc.jpg'; $dishImg2 = 'images/ốc trứng muối.jpg';
                $dishPrice1 = '90.000 đ/đĩa'; $dishPrice2 = '120.000 đ/đĩa';
            } else {
                $dish1 = $name . ' Hấp Thủy Nhiệt / Nướng Mỡ Hành';
                $dish2 = $name . ' Rang Muối / Cháy Tỏi / Lẩu Thuyền Chài';
                $dishPrice1 = '250.000 đ/đĩa';
                $dishPrice2 = '280.000 đ/đĩa';
            }
        }

        $d1IsFav = (isset($item['dish_1_is_favorite']) && $item['dish_1_is_favorite'] == 1) ? 1 : 0;
        $d2IsFav = (isset($item['dish_2_is_favorite']) && $item['dish_2_is_favorite'] == 1) ? 1 : 0;

        echo <<<CARD
        <div class="col-md-4 col-lg-3 seafood-item" data-name="{$name}" data-dishes="{$dish1}|{$dish2}" onclick="showDishModal('{$name}', '{$dish1}', '{$dish2}', '{$dishImg1}', '{$dishImg2}', '{$dishPrice1}', '{$dishPrice2}')" style="cursor:pointer;">
            <div class="menu-card" {$cardOpacity}>
                <span class="status-badge {$statusClass}">{$statusText}</span>
                <img src="{$img}" alt="{$name}">
                <div class="menu-info">
                    <h3 class="menu-name">{$name}</h3>
                    <p class="menu-price">{$price} {$unit}</p>
                </div>
            </div>
        </div>
CARD;

        // Dish cards trong lưới chính: luôn ẩn (display:none) ở trạng thái bình thường. Chỉ hiện khi khách tìm kiếm.
        if (!empty($dish1)) {
            $d1Img = !empty($dishImg1) ? $dishImg1 : 'https://via.placeholder.com/300x200?text=Mon+Che+Bien';
            echo <<<DISH1
        <div class="col-md-4 col-lg-3 seafood-dish-item" data-dish-name="{$dish1}" data-is-favorite="{$d1IsFav}" style="display:none;cursor:pointer;" onclick="showDishModal('{$name}', '{$dish1}', '{$dish2}', '{$dishImg1}', '{$dishImg2}', '{$dishPrice1}', '{$dishPrice2}')">
            <div class="menu-card" {$cardOpacity}>
                <span class="status-badge" style="background:#0073C2;color:#fff;">Chế biến</span>
                <img src="{$d1Img}" alt="{$dish1}" style="width:100%;height:200px;object-fit:cover;">
                <div class="menu-info">
                    <h3 class="menu-name">{$dish1}</h3>
                    <p class="menu-price">{$dishPrice1}</p>
                    <p style="font-size:12px;color:#94a3b8;margin:6px 0 0;"><i class="fa-solid fa-fish me-1"></i>Nguyên liệu: {$name}</p>
                </div>
            </div>
        </div>
DISH1;
        }

        if (!empty($dish2)) {
            $d2Img = !empty($dishImg2) ? $dishImg2 : 'https://via.placeholder.com/300x200?text=Mon+Che+Bien';
            echo <<<DISH2
        <div class="col-md-4 col-lg-3 seafood-dish-item" data-dish-name="{$dish2}" data-is-favorite="{$d2IsFav}" style="display:none;cursor:pointer;" onclick="showDishModal('{$name}', '{$dish1}', '{$dish2}', '{$dishImg1}', '{$dishImg2}', '{$dishPrice1}', '{$dishPrice2}')">
            <div class="menu-card" {$cardOpacity}>
                <span class="status-badge" style="background:#0073C2;color:#fff;">Chế biến</span>
                <img src="{$d2Img}" alt="{$dish2}" style="width:100%;height:200px;object-fit:cover;">
                <div class="menu-info">
                    <h3 class="menu-name">{$dish2}</h3>
                    <p class="menu-price">{$dishPrice2}</p>
                    <p style="font-size:12px;color:#94a3b8;margin:6px 0 0;"><i class="fa-solid fa-fish me-1"></i>Nguyên liệu: {$name}</p>
                </div>
            </div>
        </div>
DISH2;
        }
    }
} else {
    echo '<div class="col-12 text-center text-muted py-4"><p>Thực đơn đang được cập nhật...</p></div>';
}

echo '</div>';
echo '</div>';
echo '</section>';

// ==================== REVIEWS SECTION ====================
echo '<section id="reviews" class="reviews-section">';
echo '<div class="container">';
echo '<h2 class="text-center story-title mb-5">ĐÁNH GIÁ TỪ KHÁCH HÀNG</h2>';

// Tính toán rating trung bình
$total_reviews = count($approved_reviews);
$avg_rating = 5.0;
if ($total_reviews > 0) {
    $sum_rating = 0;
    foreach ($approved_reviews as $rev) {
        $sum_rating += $rev['rating'];
    }
    $avg_rating = round($sum_rating / $total_reviews, 1);
}

echo '<div class="row g-4">';

// Cột trái: Thống kê & Danh sách đánh giá
echo '<div class="col-lg-7">';
echo '<div class="row g-4 mb-4">';
echo '  <div class="col-md-5">';
echo '      <div class="review-summary-card">';
echo '          <div class="review-average-num">' . number_format($avg_rating, 1) . '</div>';
echo '          <div class="review-stars-display">';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= round($avg_rating)) {
                            echo '<i class="fa-solid fa-star"></i>';
                        } else {
                            echo '<i class="fa-regular fa-star"></i>';
                        }
                    }
echo '          </div>';
echo '          <div class="review-total-count">Dựa trên ' . $total_reviews . ' đánh giá</div>';
echo '      </div>';
echo '  </div>';
echo '  <div class="col-md-7 d-flex flex-column justify-content-center">';
echo '      <h4 class="fw-bold mb-3 text-dark">Phản hồi thực tế</h4>';
echo '      <p class="text-muted" style="font-size: 14px; line-height: 1.6;">Tất cả đánh giá đều được gửi từ những khách hàng đã trải nghiệm dịch vụ tại hệ thống nhà hàng SeaFood. Sự đóng góp của quý khách giúp chúng tôi nâng cao chất lượng mỗi ngày!</p>';
echo '  </div>';
echo '</div>'; // End stats row

echo '<div class="review-list-container">';
if (empty($approved_reviews)) {
    echo '  <div class="text-center text-muted py-5 bg-white rounded-3 border border-light">';
    echo '      <i class="fa-regular fa-comments fa-3x mb-3 text-secondary"></i>';
    echo '      <p class="mb-0">Chưa có đánh giá nào được hiển thị. Hãy là người đầu tiên gửi đánh giá nhé!</p>';
    echo '  </div>';
} else {
    foreach ($approved_reviews as $rev) {
        $r_name = htmlspecialchars($rev['full_name']);
        $r_initial = strtoupper(substr($r_name, 0, 1));
        $r_stars = intval($rev['rating']);
        $r_comment = nl2br(htmlspecialchars($rev['comment']));
        $r_date = date('d/m/Y H:i', strtotime($rev['created_at']));
        
        echo '  <div class="review-card-item">';
        echo '      <div class="review-user-info">';
        echo '          <div class="review-user-avatar">' . $r_initial . '</div>';
        echo '          <div class="review-user-meta">';
        echo '              <h5 class="mb-1">' . $r_name . '</h5>';
        echo '              <div class="d-flex align-items-center gap-2">';
        echo '                  <div class="review-stars">';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $r_stars) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
        echo '                  </div>';
        echo '                  <span class="review-date">' . $r_date . '</span>';
        echo '              </div>';
        echo '          </div>';
        echo '      </div>';
        echo '      <p class="review-content">' . $r_comment . '</p>';
        echo '  </div>';
    }
}
echo '</div>'; // End review-list-container
echo '</div>'; // End col-lg-7

// Cột phải: Form gửi đánh giá hoặc yêu cầu đăng nhập
echo '<div class="col-lg-5">';
if ($customer_logged_in) {
    echo '  <div class="review-form-card">';
    echo '      <h3 class="review-form-title">Gửi đánh giá của bạn</h3>';
    echo '      <form action="index.php?controller=review&action=submit" method="POST" onsubmit="return validateReviewForm()">';
    echo '          <div class="star-select-container">';
    echo '              <span class="star-select-label">Đánh giá của bạn:</span>';
    echo '              <div class="interactive-stars" id="interactive-stars">';
    echo '                  <i class="fa-solid fa-star interactive-star-icon active" data-value="1"></i>';
    echo '                  <i class="fa-solid fa-star interactive-star-icon active" data-value="2"></i>';
    echo '                  <i class="fa-solid fa-star interactive-star-icon active" data-value="3"></i>';
    echo '                  <i class="fa-solid fa-star interactive-star-icon active" data-value="4"></i>';
    echo '                  <i class="fa-solid fa-star interactive-star-icon active" data-value="5"></i>';
    echo '              </div>';
    echo '              <input type="hidden" name="rating" id="review-rating-input" value="5">';
    echo '          </div>';
    echo '          <div class="mb-4">';
    echo '              <label class="form-label small fw-bold text-muted">Nhận xét của bạn</label>';
    echo '              <textarea name="comment" id="review-comment-textarea" class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về món ăn, thái độ phục vụ và không gian nhà hàng..." required style="padding:15px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; resize:none;"></textarea>';
    echo '          </div>';
    echo '          <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill" style="background-color:#0073C2; border:none; box-shadow:0 4px 15px rgba(0,115,194,0.3);">GỬI ĐÁNH GIÁ</button>';
    echo '      </form>';
    echo '  </div>';
} else {
    echo '  <div class="review-auth-prompt bg-white shadow-sm border border-light h-100 d-flex flex-column justify-content-center align-items-center">';
    echo '      <div class="mb-3 text-primary"><i class="fa-solid fa-user-lock fa-3x text-info"></i></div>';
    echo '      <h4 class="fw-bold text-dark mb-2">Đăng nhập để đánh giá</h4>';
    echo '      <p class="text-muted px-4">Chỉ những khách hàng đã đăng nhập tài khoản trên hệ thống mới được phép gửi đánh giá trải nghiệm dịch vụ.</p>';
    echo '      <button class="btn btn-warning px-4 py-2 text-dark fw-bold rounded-pill shadow-sm" onclick="openAuthModal(\'login\')">Đăng nhập ngay</button>';
    echo '  </div>';
}
echo '</div>'; // End col-lg-5

echo '</div>'; // End row
echo '</div>'; // End container
echo '</section>';

// ==================== KHUNG HIỂN THỊ MÓN GỢI Ý ====================
echo <<<HTML
<div id="dishModal" class="dish-modal-overlay" onclick="closeDishModal(event)">
    <div class="dish-modal-content" onclick="event.stopPropagation()">
        <span class="dish-modal-close" onclick="document.getElementById('dishModal').style.display='none'">&times;</span>
        <h2 id="modalSeafoodName" class="dish-modal-title">Gợi ý chế biến</h2>
        <div class="dishes-grid">
            <div class="dish-item">
                <div id="modalImgContainer1" class="dish-img-placeholder">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div id="modalDish1" class="dish-name">Tên món 1</div>
                <div id="modalPrice1" class="dish-price mt-1" style="font-size: 15px; color: #e11d48; font-weight: 700;"></div>
            </div>
            <div class="dish-item">
                <div id="modalImgContainer2" class="dish-img-placeholder">
                    <i class="fa-solid fa-fire-burner"></i>
                </div>
                <div id="modalDish2" class="dish-name">Tên món 2</div>
                <div id="modalPrice2" class="dish-price mt-1" style="font-size: 15px; color: #e11d48; font-weight: 700;"></div>
            </div>
        </div>
    </div>
</div>
HTML;

// ==================== BOOKING SECTION ====================
$today = date('Y-m-d');
$inactiveDishesJson = json_encode($inactiveDishes);
$is_customer_logged_in_js = isset($_SESSION['customer_id']) ? 'true' : 'false';

echo <<<HTML
<section id="booking" class="booking-section">
    <div class="container">
        <div class="booking-form-box">
            <div class="booking-title-row">
                <h3 class="booking-title" id="booking-title">Đặt bàn</h3>
                <div class="lang-toggle">
                    <span id="btn-vi" class="active" onclick="changeLang('vi')">VI</span>
                    <span id="btn-en" onclick="changeLang('en')">EN</span>
                </div>
            </div>
            <form action="index.php?controller=reservation&action=submit_booking" method="POST" onsubmit="return validateBooking()">
                <input type="text" name="name" id="field-name" class="form-control" placeholder="Nhập tên của bạn" value="{$cust_name}" required>
                <input type="tel" name="phone" id="field-phone" class="form-control" placeholder="Số điện thoại" value="{$cust_phone}" required>
                <select name="location" id="field-location" class="form-select" required onchange="checkAutoOffer()">
                    <option value="">Chọn nhà hàng</option>
                    <option value="1">18 Trần Kim Xuyến</option>
                    <option value="2">75A Trần Hưng Đạo</option>
                    <option value="3">Tháp C Golden Palace</option>
                </select>
                <select name="pre_order[]" id="field-pre-order" multiple>
                    <optgroup label="Mực Ống">
                        <option value="Mực nhồi thịt">Mực nhồi thịt</option>
                        <option value="Mực xào dứa">Mực xào dứa</option>
                    </optgroup>
                    <optgroup label="Bào Ngư">
                        <option value="Cháo bào ngư">Cháo bào ngư</option>
                        <option value="Bào ngư sống">Bào ngư sống</option>
                    </optgroup>
                    <optgroup label="Bề Bề">
                        <option value="Bề bề hấp">Bề bề hấp</option>
                        <option value="Bề bề rang muối">Bề bề rang muối</option>
                    </optgroup>
                    <optgroup label="Cá Song">
                        <option value="Cá song chiên">Cá song chiên</option>
                        <option value="Cá song kho">Cá song kho</option>
                    </optgroup>
                    <optgroup label="Cua Cà Mau">
                        <option value="Cua rang muối">Cua rang muối</option>
                        <option value="Cua sốt me">Cua sốt me</option>
                    </optgroup>
                    <optgroup label="Cua Hoàng Đế">
                        <option value="Cua hoàng đế hấp">Cua hoàng đế hấp</option>
                        <option value="Càng cua hấp">Càng cua hấp</option>
                    </optgroup>
                    <optgroup label="Cá Bơn">
                        <option value="Cá bơn chiên">Cá bơn chiên</option>
                        <option value="Cá bơn nướng">Cá bơn nướng</option>
                    </optgroup>
                    <optgroup label="Tu Hài">
                        <option value="Tu hài hấp xả ớt">Tu hài hấp xả ớt</option>
                        <option value="Tu hài nướng mỡ hành">Tu hài nướng mỡ hành</option>
                    </optgroup>
                    <optgroup label="Tôm Hùm">
                        <option value="Tôm hùm hấp">Tôm hùm hấp</option>
                        <option value="Tôm hùm sốt">Tôm hùm sốt</option>
                    </optgroup>
                    <optgroup label="Ốc Hương">
                        <option value="Ốc luộc">Ốc luộc</option>
                        <option value="Ốc trứng muối">Ốc trứng muối</option>
                    </optgroup>
                </select>
                <select name="offer" id="field-offer" class="form-select mb-3" onchange="toggleBirthday()">
                    <option value="none" selected>Không chọn ưu đãi</option>
                    <option value="offer1">Ưu đãi 1: Tháng 3 của nàng (Giảm 1tr/kg Cua King Crab)</option>
                    <option value="offer2">Ưu đãi 2: Hải sản đang bơi (Giảm 1tr5/kg Tôm hùm bông tại 75A Trần Hưng Đạo)</option>
                    <option value="offer3">Ưu đãi 3: Tiệc sinh nhật trọn vẹn (Tặng gói trang trí & Bánh kem)</option>
                </select>
                <div id="birthday-wrapper" style="display: none; margin-bottom: 20px;">
                    <label class="form-label small fw-bold text-muted" id="label-birthday">Ngày tháng năm sinh:</label>
                    <input type="date" name="birthday" id="field-birthday" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="date" name="date" id="field-date" class="form-control" value="{$today}" required onchange="checkAutoOffer()">
                    </div>
                    <div class="col-md-6">
                        <select name="time" id="field-time" class="form-select" required>
                            <option value="">Khung giờ</option>
                            <option value="11:00">11:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                        </select>
                    </div>
                </div>
                <input type="text" name="notes" id="field-notes" class="form-control" placeholder="Ghi chú yêu cầu">
                <button type="submit" id="btn-submit-booking" class="btn-submit-booking">ĐẶT BÀN NGAY</button>
            </form>
        </div>
    </div>
</section>

<!-- Custom Alert HTML -->
<div id="custom-alert" class="custom-alert-overlay" style="display:none;">
    <div class="custom-alert-box">
        <div class="mb-3 text-warning"><i class="fa-solid fa-circle-exclamation fa-3x"></i></div>
        <h4 id="alert-title">SeaFood chân thành xin lỗi</h4>
        <p id="alert-message"></p>
        <button onclick="closeAlert()" id="btn-close-alert" class="btn-close-alert">Đã hiểu</button>
    </div>
</div>

<script>
    const isCustomerLoggedIn = {$is_customer_logged_in_js};
    const inactiveDishes = {$inactiveDishesJson};
    const langData = {
        vi: {
            title: 'Đặt bàn',
            name: 'Nhập tên của bạn',
            phone: 'Số điện thoại',
            location: 'Chọn nhà hàng',
            locations: ['Chọn nhà hàng', '18 Trần Kim Xuyến', '75A Trần Hưng Đạo', 'Tháp C Golden Palace'],
            offers: [
                'Không chọn ưu đãi',
                'Ưu đãi 1: Tháng 3 của nàng (Giảm 1tr/kg Cua King Crab)',
                'Ưu đãi 2: Hải sản đang bơi (Giảm 1tr5/kg Tôm hùm bông tại 75A Trần Hưng Đạo)',
                'Ưu đãi 3: Tiệc sinh nhật trọn vẹn (Tặng gói trang trí & Bánh kem)'
            ],
            pre_order: 'Đặt trước món (Không bắt buộc)',
            birthday: 'Ngày tháng năm sinh:',
            time: 'Khung giờ',
            times: ['Khung giờ', '11:00', '18:00', '19:00'],
            notes: 'Ghi chú yêu cầu',
            submit: 'ĐẶT BÀN NGAY',
            errOffer1: 'Ưu đãi 1 chỉ áp dụng khi đặt bàn trong tháng 3.',
            errOffer2: 'Ưu đãi 2 chỉ áp dụng tại nhà hàng 75A Trần Hưng Đạo.',
            errInactive: 'Món "{dish}" hiện đang ngừng phục vụ. Vui lòng chọn món khác!',
            successTitle: 'SeaFood xin cảm ơn',
            successMsg: 'Đặt bàn thành công! Chúng tôi sẽ liên hệ lại sớm nhất.',
            errorTitle: 'SeaFood chân thành xin lỗi',
            errorMsg: 'Có lỗi xảy ra, vui lòng thử lại.',
            alertClose: 'Đã hiểu'
        },
        en: {
            title: 'Booking Table',
            name: 'Enter your name',
            phone: 'Phone number',
            location: 'Select restaurant',
            locations: ['Select restaurant', '18 Tran Kim Xuyen', '75A Tran Hung Dao', 'Tower C Golden Palace'],
            offers: [
                'No offer',
                'Offer 1: Her March (1M/kg off King Crab)',
                'Offer 2: Fresh Seafood (1.5M/kg off Lobster at 75A Tran Hung Dao)',
                'Offer 3: Full Birthday Party (Free Decoration & Cake)'
            ],
            pre_order: 'Pre-order dishes (Optional)',
            birthday: 'Date of Birth:',
            time: 'Time slot',
            times: ['Time slot', '11:00', '18:00', '19:00'],
            notes: 'Special requests',
            submit: 'BOOK NOW',
            errOffer1: 'Offer 1 is only available for bookings in March.',
            errOffer2: 'Offer 2 is only available at 75A Tran Hung Dao restaurant.',
            errInactive: 'Dish "{dish}" is currently unavailable. Please choose another one!',
            successTitle: 'SeaFood Thank You',
            successMsg: 'Booking successful! We will contact you soon.',
            errorTitle: 'SeaFood Sincerely Apologizes',
            errorMsg: 'An error occurred, please try again.',
            alertClose: 'Got it'
        }
    };

    function changeLang(lang) {
        document.getElementById('btn-vi').classList.remove('active');
        document.getElementById('btn-en').classList.remove('active');
        document.getElementById('btn-' + lang).classList.add('active');

        const data = langData[lang];
        document.getElementById('booking-title').innerText = data.title;
        document.getElementById('field-name').placeholder = data.name;
        document.getElementById('field-phone').placeholder = data.phone;
        
        // Update choices placeholder if initialized
        if (window.preOrderChoices) {
            document.querySelector('.choices__input--cloned').placeholder = data.pre_order;
        }
        
        const locSelect = document.getElementById('field-location');
        for (let i = 0; i < locSelect.options.length; i++) {
            if (data.locations[i]) locSelect.options[i].text = data.locations[i];
        }

        const offerSelect = document.getElementById('field-offer');
        for (let i = 0; i < offerSelect.options.length; i++) {
            if (data.offers[i]) offerSelect.options[i].text = data.offers[i];
        }

        const timeSelect = document.getElementById('field-time');
        for (let i = 0; i < timeSelect.options.length; i++) {
            if (data.times[i]) timeSelect.options[i].text = data.times[i];
        }

        document.getElementById('label-birthday').innerText = data.birthday;
        document.getElementById('field-notes').placeholder = data.notes;
        document.getElementById('btn-submit-booking').innerText = data.submit;
        document.getElementById('btn-close-alert').innerText = data.alertClose;
    }

    function checkAutoOffer() {
        const dateVal = document.getElementById('field-date').value;
        const locVal = document.getElementById('field-location').value;
        const offerSelect = document.getElementById('field-offer');

        const date = new Date(dateVal);
        const month = date.getMonth() + 1; // 1-12

        if (month === 3) {
            offerSelect.value = 'offer1';
        } else if (locVal === '2') {
            offerSelect.value = 'offer2';
        }
        
        toggleBirthday();
    }

    function toggleBirthday() {
        const offerVal = document.getElementById('field-offer').value;
        const birthdayWrapper = document.getElementById('birthday-wrapper');
        const birthdayInput = document.getElementById('field-birthday');

        if (offerVal === 'offer3') {
            birthdayWrapper.style.display = 'block';
            birthdayInput.required = true;
        } else {
            birthdayWrapper.style.display = 'none';
            birthdayInput.required = false;
        }
    }

    function validateBooking() {
        if (!isCustomerLoggedIn) {
            showCustomAlert('Vui lòng đăng nhập để đặt bàn!', 'error');
            setTimeout(() => {
                openAuthModal('login');
            }, 1800);
            return false;
        }
        const dateVal = document.getElementById('field-date').value;
        const locVal = document.getElementById('field-location').value;
        const offerVal = document.getElementById('field-offer').value;
        const preOrderSelect = document.getElementById('field-pre-order');
        
        const currentLang = document.getElementById('btn-en').classList.contains('active') ? 'en' : 'vi';
        const data = langData[currentLang];

        if (preOrderSelect && preOrderSelect.selectedOptions) {
            const selectedDishes = Array.from(preOrderSelect.selectedOptions).map(opt => opt.value);
            for (let dish of selectedDishes) {
                if (inactiveDishes.includes(dish)) {
                    showCustomAlert(data.errInactive.replace('{dish}', dish), 'error');
                    return false;
                }
            }
        }
        
        if (offerVal === 'none' || offerVal === '') return true;

        const date = new Date(dateVal);
        const month = date.getMonth() + 1;

        if (offerVal === 'offer1' && month !== 3) {
            showCustomAlert(data.errOffer1, 'error');
            return false;
        }
        if (offerVal === 'offer2' && locVal !== '2') {
            showCustomAlert(data.errOffer2, 'error');
            return false;
        }

        return true;
    }

    function showCustomAlert(message, type = 'success') {
        const currentLang = document.getElementById('btn-en').classList.contains('active') ? 'en' : 'vi';
        const data = langData[currentLang];
        
        const titleElem = document.getElementById('alert-title');
        const iconContainer = document.querySelector('.custom-alert-box .mb-3');
        
        if (type === 'success') {
            titleElem.innerText = data.successTitle;
            iconContainer.innerHTML = '<i class="fa-solid fa-circle-check fa-3x text-success"></i>';
        } else {
            titleElem.innerText = data.errorTitle;
            iconContainer.innerHTML = '<i class="fa-solid fa-circle-exclamation fa-3x text-warning"></i>';
        }

        document.getElementById('alert-message').innerText = message;
        const alertOverlay = document.getElementById('custom-alert');
        alertOverlay.style.display = 'flex';
        setTimeout(() => alertOverlay.classList.add('show'), 10);
    }

    function closeAlert() {
        const alertOverlay = document.getElementById('custom-alert');
        alertOverlay.classList.remove('show');
        setTimeout(() => {
            alertOverlay.style.display = 'none';
            // Xóa tham số trên URL để tránh hiện lại khi reload
            const url = new URL(window.location);
            url.searchParams.delete('booking_success');
            url.searchParams.delete('booking_error');
            window.history.replaceState({}, '', url);
        }, 300);
    }

    function showDishModal(seafood, dish1, dish2, img1, img2, price1, price2) {
        document.getElementById('modalSeafoodName').innerText = seafood;
        document.getElementById('modalDish1').innerText = dish1;
        document.getElementById('modalDish2').innerText = dish2;
        document.getElementById('modalPrice1').innerText = price1 ? "Giá tham khảo: " + price1 : "";
        document.getElementById('modalPrice2').innerText = price2 ? "Giá tham khảo: " + price2 : "";
        
        const container1 = document.getElementById('modalImgContainer1');
        const container2 = document.getElementById('modalImgContainer2');
        
        if (img1) {
            container1.innerHTML = `<img src="\${img1}" style="width:100%; height:100%; object-fit:cover; border-radius:15px;">`;
            container1.style.border = 'none';
        } else {
            container1.innerHTML = '<i class="fa-solid fa-utensils"></i>';
            container1.style.border = '2px dashed #e2e8f0';
        }
        
        if (img2) {
            container2.innerHTML = `<img src="\${img2}" style="width:100%; height:100%; object-fit:cover; border-radius:15px;">`;
            container2.style.border = 'none';
        } else {
            container2.innerHTML = '<i class="fa-solid fa-fire-burner"></i>';
            container2.style.border = '2px dashed #e2e8f0';
        }
        
        document.getElementById('dishModal').style.display = 'flex';
    }

    function closeDishModal(e) {
        if (e.target.id === 'dishModal') {
            document.getElementById('dishModal').style.display = 'none';
        }
    }

    window.onload = function() {
        checkAutoOffer();
        
        // Khởi tạo Choices.js cho Select Đặt trước món
        const currentLang = document.getElementById('btn-en').classList.contains('active') ? 'en' : 'vi';
        const data = langData[currentLang];
        window.preOrderChoices = new Choices('#field-pre-order', {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: data.pre_order,
            searchPlaceholderValue: 'Tìm kiếm món ăn...',
            itemSelectText: 'Nhấn để chọn',
            noResultsText: 'Không tìm thấy món ăn',
            noChoicesText: 'Không có món ăn nào',
        });
        document.querySelector('.choices__input--cloned').placeholder = data.pre_order;
        
        // Kiểm tra kết quả từ server
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('booking_success')) {
            showCustomAlert(data.successMsg, 'success');
        } else if (urlParams.has('booking_error')) {
            const err = urlParams.get('booking_error');
            if (err === 'need_login') {
                showCustomAlert('Vui lòng đăng nhập để đặt bàn!', 'error');
                setTimeout(() => {
                    openAuthModal('login');
                }, 1800);
            } else if (err === 'inactive_dish') {
                const dishName = urlParams.get('dish_name') || '';
                const msg = data.errInactive.replace('{dish}', dishName);
                showCustomAlert(msg, 'error');
            } else {
                showCustomAlert(data.errorMsg, 'error');
            }
        }
    };

    // JS logic for interactive stars selection
    document.addEventListener('DOMContentLoaded', () => {
        const stars = document.querySelectorAll('.interactive-star-icon');
        const ratingInput = document.getElementById('review-rating-input');
        
        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = value;
                    
                    // Cập nhật trạng thái active
                    stars.forEach(s => {
                        const v = parseInt(s.getAttribute('data-value'));
                        if (v <= value) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseover', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    stars.forEach(s => {
                        const v = parseInt(s.getAttribute('data-value'));
                        if (v <= value) {
                            s.style.color = '#fbbf24'; // Highlight star on hover
                        } else {
                            s.style.color = '#cbd5e1';
                        }
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    const currentValue = parseInt(ratingInput.value);
                    stars.forEach(s => {
                        const v = parseInt(s.getAttribute('data-value'));
                        if (v <= currentValue) {
                            s.style.color = '#f59e0b'; // Restore star color
                        } else {
                            s.style.color = '#cbd5e1';
                        }
                    });
                });
            });
        }
        
        // Kiểm tra kết quả gửi đánh giá từ URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('review_success')) {
            showCustomAlert('Cảm ơn bạn đã gửi đánh giá! Đánh giá của bạn đang chờ quản trị viên phê duyệt.', 'success');
            // Xóa tham số trên URL để tránh hiện lại khi reload
            const url = new URL(window.location);
            url.searchParams.delete('review_success');
            window.history.replaceState({}, '', url);
        } else if (urlParams.has('review_error')) {
            const err = urlParams.get('review_error');
            let msg = 'Gửi đánh giá thất bại. Vui lòng thử lại sau.';
            if (err === 'need_login') {
                msg = 'Vui lòng đăng nhập để gửi đánh giá!';
                setTimeout(() => { openAuthModal('login'); }, 1800);
            } else if (err === 'empty_comment') {
                msg = 'Vui lòng nhập nội dung đánh giá!';
            } else if (err === 'invalid_rating') {
                msg = 'Số sao đánh giá không hợp lệ!';
            }
            showCustomAlert(msg, 'error');
            
            const url = new URL(window.location);
            url.searchParams.delete('review_error');
            window.history.replaceState({}, '', url);
        }
    });

    function validateReviewForm() {
        const comment = document.getElementById('review-comment-textarea').value.trim();
        if (comment === '') {
            showCustomAlert('Vui lòng nhập nội dung nhận xét!', 'error');
            return false;
        }
        return true;
    }
</script>
HTML;

// ==================== FOOTER ====================
echo <<<HTML
<footer id="locations" class="footer-locations">
    <div class="container">
        <h3 class="footer-title">HỆ THỐNG NHÀ HÀNG SEAFOOD</h3>
        <div class="row g-4 justify-content-center">
            <!-- Địa chỉ 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="location-card">
                    <div class="location-img-wrapper">
                        <img src="images/Địa chỉ 1.jpg" alt="Địa chỉ 1" class="location-img">
                    </div>
                    <div class="location-info">
                        <h4 class="location-name"><i class="fa-solid fa-location-dot"></i> Địa chỉ 1</h4>
                        <p class="location-address">18 Trần Kim Xuyến, Yên Hoà, HN</p>
                    </div>
                </div>
            </div>
            <!-- Địa chỉ 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="location-card">
                    <div class="location-img-wrapper">
                        <img src="images/Địa chỉ 2.jpg" alt="Địa chỉ 2" class="location-img">
                    </div>
                    <div class="location-info">
                        <h4 class="location-name"><i class="fa-solid fa-location-dot"></i> Địa chỉ 2</h4>
                        <p class="location-address">75A Trần Hưng Đạo, Cửa Nam, HN</p>
                    </div>
                </div>
            </div>
            <!-- Địa chỉ 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="location-card">
                    <div class="location-img-wrapper">
                        <img src="images/Địa chỉ 3.jpg" alt="Địa chỉ 3" class="location-img">
                    </div>
                    <div class="location-info">
                        <h4 class="location-name"><i class="fa-solid fa-location-dot"></i> Địa chỉ 3</h4>
                        <p class="location-address">Tháp C Golden Palace, 99 Mễ Trì, Từ Liêm, HN</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-row">
            <p class="hotline-text">Hotline: 1900 636061</p>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
HTML;

include __DIR__ . '/../layout/home_footer.php';
?>
