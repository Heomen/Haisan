<?php
$title = isset($page_title) ? htmlspecialchars($page_title) : 'SeaFood';

// Check customer auth state
$customer_logged_in = isset($_SESSION['customer_id']);
$customer_auth_html = '';
if ($customer_logged_in) {
    $cust_name = htmlspecialchars($_SESSION['customer_name']);
    $customer_auth_html = <<<HTML
    <div class="auth-buttons d-flex gap-2 ms-auto align-items-center">
        <span class="text-white small me-1"><i class="fa-solid fa-circle-user text-warning me-1"></i> Xin chào, {$cust_name}!</span>
        <a href="index.php?controller=customer&action=logout" class="btn btn-outline-light btn-sm rounded-pill px-3" style="font-size: 13px;">Đăng xuất</a>
    </div>
HTML;
} else {
    $customer_auth_html = <<<HTML
    <div class="auth-buttons d-flex gap-2 ms-auto align-items-center">
        <button class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="openAuthModal('login')" style="font-size: 13px;">Đăng nhập</button>
        <button class="btn btn-warning btn-sm text-dark rounded-pill px-3 fw-bold" onclick="openAuthModal('register')" style="font-size: 13px;">Đăng ký</button>
    </div>
HTML;
}

echo <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #333;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        .main-header {
            background-color: #0073C2;
            padding: 10px 0;
            position: relative;
            z-index: 100;
        }
        .main-header .nav-link {
            color: white !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            padding: 10px 20px !important;
            transition: color 0.3s;
        }
        .main-header .nav-link:hover { color: #ffc107 !important; }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo-placeholder {
            color: white;
            font-weight: bold;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-placeholder i {
            color: #ff3b30;
            font-size: 32px;
        }
        
        /* Mobile Hamburger Button */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px 10px;
            transition: color 0.2s;
        }
        .mobile-menu-toggle:hover { color: #ffc107; }
        
        /* Mobile Header Layout */
        .header-desktop-row { display: flex; }
        .header-mobile-bar {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
        }
        .mobile-nav-collapse {
            display: none;
            background: #005fa3;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding: 15px 0;
            animation: mobileNavSlideDown 0.3s ease-out;
        }
        .mobile-nav-collapse.show { display: block; }
        @keyframes mobileNavSlideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .mobile-nav-collapse .nav {
            flex-direction: column;
            align-items: center;
        }
        .mobile-nav-collapse .nav-link {
            padding: 12px 20px !important;
            font-size: 15px;
            width: 100%;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .mobile-nav-collapse .nav-link:last-child { border-bottom: none; }
        .mobile-nav-collapse .auth-buttons {
            justify-content: center;
            padding: 15px 20px 5px;
        }

        /* Auth Modal Glassmorphism Styling */
        .auth-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            transition: all 0.3s ease;
        }
        .auth-modal-box {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            padding: 35px;
            position: relative;
            animation: authModalSlideIn 0.3s ease-out;
        }
        @keyframes authModalSlideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .auth-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
            transition: color 0.2s;
            background: none;
            border: none;
        }
        .auth-modal-close:hover {
            color: #0f172a;
        }
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 25px;
        }
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            font-weight: 600;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }
        .auth-tab.active {
            color: #0073C2;
            border-bottom: 2px solid #0073C2;
        }
        .auth-modal-box .form-control {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            transition: all 0.3s;
        }
        .auth-modal-box .form-control:focus {
            border-color: #0073C2;
            box-shadow: 0 0 0 4px rgba(0,115,194,0.1);
        }
        .btn-auth-submit {
            background: #0073C2;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,115,194,0.2);
            margin-top: 10px;
        }
        .btn-auth-submit:hover {
            background: #005fa3;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0,115,194,0.3);
        }
        .auth-error-msg {
            color: #ef4444;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
            display: none;
            align-items: center;
            gap: 8px;
        }
        
        /* Search Bar Styling */
        .search-container {
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 2px 5px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }
        .search-container:focus-within {
            background: rgba(255, 255, 255, 0.25);
            border-color: #ffc107;
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.2);
        }
        .search-container input {
            background: transparent !important;
            border: none !important;
            color: white !important;
            font-size: 13px !important;
            width: 110px;
            transition: width 0.3s ease;
            box-shadow: none !important;
            padding: 4px 8px !important;
        }
        .search-container input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .search-container input:focus {
            width: 160px;
        }
        .search-container .btn-search {
            color: white;
            background: transparent;
            border: none;
            padding: 4px 8px;
            font-size: 13px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .search-container .btn-search:hover {
            color: #ffc107;
        }

        /* ======================== MOBILE RESPONSIVE ======================== */
        @media (max-width: 991px) {
            .header-desktop-row { display: none !important; }
            .header-mobile-bar { display: flex !important; }
            .mobile-menu-toggle { display: inline-block; }
        }
        
        @media (max-width: 768px) {
            .auth-modal-box {
                margin: 15px;
                padding: 25px 20px;
                border-radius: 15px;
                max-width: 100%;
            }
            .auth-tabs { margin-bottom: 18px; }
            .auth-tab { padding: 10px; font-size: 14px; }
            .auth-modal-box .form-control { padding: 10px 15px; font-size: 13px; }
            .btn-auth-submit { padding: 11px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <!-- Desktop Navigation -->
            <div class="row align-items-center header-desktop-row">
                <div class="col-md-4">
                    <ul class="nav justify-content-end">
                        <li class="nav-item"><a href="#story" class="nav-link">CÂU CHUYỆN</a></li>
                        <li class="nav-item"><a href="#menu" class="nav-link">THỰC ĐƠN</a></li>
                    </ul>
                </div>
                <div class="col-md-2 logo-container">
                    <a href="index.php" class="text-decoration-none logo-placeholder">
                        <i class="fa-solid fa-shrimp"></i> SEAFOOD
                    </a>
                </div>
                <div class="col-md-6 d-flex justify-content-between align-items-center">
                    <ul class="nav justify-content-start">
                        <li class="nav-item"><a href="#locations" class="nav-link">ĐỊA ĐIỂM</a></li>
                        <li class="nav-item"><a href="#offers" class="nav-link">ƯU ĐÃI</a></li>
                        <li class="nav-item"><a href="#" onclick="toggleChat(); return false;" class="nav-link text-warning fw-bold" style="color: #ffc107 !important;"><i class="fa-solid fa-robot"></i> AI TƯ VẤN</a></li>
                    </ul>
                    {$customer_auth_html}
                </div>
            </div>
            <!-- Mobile Navigation -->
            <div class="header-mobile-bar">
                <a href="index.php" class="text-decoration-none logo-placeholder" style="font-size:20px;">
                    <i class="fa-solid fa-shrimp"></i> SEAFOOD
                </a>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                    <i id="mobile-menu-icon" class="fa-solid fa-bars"></i>
                </button>
            </div>
            <div id="mobileNavCollapse" class="mobile-nav-collapse">
                <ul class="nav">
                    <li class="nav-item"><a href="#story" class="nav-link" onclick="closeMobileMenu()">CÂU CHUYỆN</a></li>
                    <li class="nav-item"><a href="#menu" class="nav-link" onclick="closeMobileMenu()">THỰC ĐƠN</a></li>
                    <li class="nav-item"><a href="#locations" class="nav-link" onclick="closeMobileMenu()">ĐỊA ĐIỂM</a></li>
                    <li class="nav-item"><a href="#offers" class="nav-link" onclick="closeMobileMenu()">ƯU ĐÃI</a></li>
                    <li class="nav-item"><a href="#booking" class="nav-link" onclick="closeMobileMenu()">ĐẶT BÀN</a></li>
                    <li class="nav-item"><a href="#reviews" class="nav-link" onclick="closeMobileMenu()">ĐÁNH GIÁ</a></li>
                    <li class="nav-item"><a href="#" onclick="toggleChat(); closeMobileMenu(); return false;" class="nav-link" style="color: #ffc107 !important;"><i class="fa-solid fa-robot"></i> AI TƯ VẤN</a></li>
                </ul>
                {$customer_auth_html}
            </div>
        </div>
    </header>

    <!-- Auth Modal HTML -->
    <div id="authModal" class="auth-modal-overlay" onclick="closeAuthModal(event)">
        <div class="auth-modal-box" onclick="event.stopPropagation()">
            <button class="auth-modal-close" onclick="closeAuthModal(event)">&times;</button>
            
            <div class="auth-tabs">
                <div id="tab-login" class="auth-tab active" onclick="switchAuthTab('login')">Đăng nhập</div>
                <div id="tab-register" class="auth-tab" onclick="switchAuthTab('register')">Đăng ký</div>
            </div>

            <!-- Error message container -->
            <div id="auth-error" class="auth-error-msg">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="auth-error-text"></span>
            </div>

            <!-- Login Form -->
            <form id="form-customer-login" onsubmit="handleAuthSubmit(event, 'login')">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập tên đăng nhập">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu">
                </div>
                <button type="submit" class="btn-auth-submit">Đăng nhập</button>
            </form>

            <!-- Register Form -->
            <form id="form-customer-register" onsubmit="handleAuthSubmit(event, 'register')" style="display: none;">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Họ và tên *</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Số điện thoại * <span style="font-weight:400;color:#94a3b8;">(10 chữ số)</span></label>
                    <input type="tel" name="phone" class="form-control" required placeholder="Nhập số điện thoại (10 số)" pattern="[0-9]{10}" maxlength="10" minlength="10">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Tên đăng nhập *</label>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập tên đăng nhập">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Email (Tùy chọn)</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com">
                </div>
                <button type="submit" class="btn-auth-submit">Đăng ký ngay</button>
            </form>
        </div>
    </div>

    <script>
        function openAuthModal(tab = 'login') {
            document.getElementById('authModal').style.display = 'flex';
            document.getElementById('auth-error').style.display = 'none';
            switchAuthTab(tab);
        }

        function closeAuthModal(event) {
            document.getElementById('authModal').style.display = 'none';
        }

        function switchAuthTab(tab) {
            const tabLogin = document.getElementById('tab-login');
            const tabRegister = document.getElementById('tab-register');
            const formLogin = document.getElementById('form-customer-login');
            const formRegister = document.getElementById('form-customer-register');
            const errorContainer = document.getElementById('auth-error');

            errorContainer.style.display = 'none';

            if (tab === 'login') {
                tabLogin.classList.add('active');
                tabRegister.classList.remove('active');
                formLogin.style.display = 'block';
                formRegister.style.display = 'none';
            } else {
                tabRegister.classList.add('active');
                tabLogin.classList.remove('active');
                formRegister.style.display = 'block';
                formLogin.style.display = 'none';
            }
        }

        function handleAuthSubmit(event, action) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const errorContainer = document.getElementById('auth-error');
            const errorText = document.getElementById('auth-error-text');
            const submitBtn = form.querySelector('.btn-auth-submit');
            
            if (action === 'register') {
                const phone = formData.get('phone');
                if (!/^[0-9]{10}$/.test(phone)) {
                    errorText.innerText = 'Số điện thoại phải đủ 10 chữ số (chỉ chứa số).';
                    errorContainer.style.display = 'flex';
                    return;
                }
                const password = formData.get('password');
                if (password.length < 6) {
                    errorText.innerText = 'Mật khẩu phải có tối thiểu 6 ký tự.';
                    errorContainer.style.display = 'flex';
                    return;
                }
            }

            const originalBtnText = submitBtn.innerText;
            submitBtn.innerText = 'Đang xử lý...';
            submitBtn.disabled = true;
            errorContainer.style.display = 'none';

            fetch(`index.php?controller=customer&action=\${action}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    errorText.innerText = data.message;
                    errorContainer.style.display = 'flex';
                    submitBtn.innerText = originalBtnText;
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                errorText.innerText = 'Có lỗi hệ thống xảy ra. Vui lòng thử lại sau.';
                errorContainer.style.display = 'flex';
                submitBtn.innerText = originalBtnText;
                submitBtn.disabled = false;
                console.error(err);
            });
        }

        function searchFood(event) {
            if (event) event.preventDefault();
            const input = document.getElementById('menu-search-input');
            if (!input) return;
            const query = input.value.toLowerCase().trim();
            const clearBtn = document.getElementById('menu-search-clear');
            if (clearBtn) clearBtn.style.display = query.length > 0 ? 'block' : 'none';

            const items = document.querySelectorAll('.seafood-item');
            const dishItems = document.querySelectorAll('.seafood-dish-item');
            
            if (items.length === 0 && dishItems.length === 0) {
                window.location.href = 'index.php?search=' + encodeURIComponent(query) + '#menu';
                return;
            }

            if (!query) {
                // Hiện lại khu vực món ưa chuộng riêng biệt
                const favSection = document.getElementById('favorite-dishes-container');
                if (favSection) favSection.style.display = 'block';

                // Hiện tất cả nguyên liệu chính (parent) và ẩn các món chế biến lẻ trong lưới chính
                items.forEach(item => {
                    item.style.display = 'block';
                });
                dishItems.forEach(dish => {
                    dish.style.display = 'none';
                });
                hasResults = true;
            } else {
                // Ẩn khu vực món ưa chuộng riêng biệt khi đang tìm kiếm
                const favSection = document.getElementById('favorite-dishes-container');
                if (favSection) favSection.style.display = 'none';

                // Nếu có từ khóa:
                // 1. Hiện nguyên liệu chính (parent) nếu tên nguyên liệu khớp
                items.forEach(item => {
                    const name = item.getAttribute('data-name').toLowerCase();
                    if (name.includes(query)) {
                        item.style.display = 'block';
                        hasResults = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // 2. Hiện món chế biến lẻ nếu tên món chế biến khớp
                dishItems.forEach(dish => {
                    const dishName = dish.getAttribute('data-dish-name').toLowerCase();
                    if (dishName.includes(query)) {
                        dish.style.display = 'block';
                        hasResults = true;
                    } else {
                        dish.style.display = 'none';
                    }
                });
            }

            const noResultsMsg = document.getElementById('no-results-message');
            if (!hasResults) {
                if (!noResultsMsg) {
                    const container = document.querySelector('#menu .row.g-4');
                    if (container) {
                        const msg = document.createElement('div');
                        msg.id = 'no-results-message';
                        msg.className = 'col-12 text-center text-muted py-5';
                        msg.innerHTML = '<i class="fa-solid fa-face-frown fa-2x mb-3 text-secondary"></i><p class="fs-5">Không tìm thấy món ăn nào phù hợp với từ khóa của bạn.</p>';
                        container.appendChild(msg);
                    }
                } else {
                    noResultsMsg.style.display = 'block';
                }
            } else {
                if (noResultsMsg) noResultsMsg.style.display = 'none';
            }
        }

        function clearSearch() {
            const input = document.getElementById('menu-search-input');
            const clearBtn = document.getElementById('menu-search-clear');
            if (input) { input.value = ''; input.focus(); }
            if (clearBtn) clearBtn.style.display = 'none';
            
            // Hiện lại khu vực món ưa chuộng riêng biệt
            const favSection = document.getElementById('favorite-dishes-container');
            if (favSection) favSection.style.display = 'block';

            document.querySelectorAll('.seafood-item').forEach(item => item.style.display = 'block');
            document.querySelectorAll('.seafood-dish-item').forEach(item => {
                item.style.display = 'none';
            });
            const noResultsMsg = document.getElementById('no-results-message');
            if (noResultsMsg) noResultsMsg.style.display = 'none';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            if (searchQuery) {
                const input = document.getElementById('menu-search-input');
                if (input) {
                    input.value = searchQuery;
                    setTimeout(searchFood, 100);
                }
            }
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNavCollapse');
            const icon = document.getElementById('mobile-menu-icon');
            if (nav.classList.contains('show')) {
                nav.classList.remove('show');
                icon.className = 'fa-solid fa-bars';
            } else {
                nav.classList.add('show');
                icon.className = 'fa-solid fa-xmark';
            }
        }
        function closeMobileMenu() {
            const nav = document.getElementById('mobileNavCollapse');
            const icon = document.getElementById('mobile-menu-icon');
            if (nav) { nav.classList.remove('show'); }
            if (icon) { icon.className = 'fa-solid fa-bars'; }
        }
    </script>
HTML;
?>
