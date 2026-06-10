<?php
$error_html = '';
if (!empty($error)) {
    $error_html = <<<HTML
        <div class="alert alert-danger p-2 text-center" role="alert">
            <i class="fa-solid fa-circle-exclamation me-1"></i> {$error}
        </div>
HTML;
}

echo <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Quản lý Hải Sản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        /* Glassmorphism card */
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-out;
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-weight: 700;
            margin-top: 15px;
            color: #fff;
            letter-spacing: 1px;
        }

        .login-header i {
            font-size: 3rem;
            color: #00d2ff;
            text-shadow: 0 0 15px rgba(0, 210, 255, 0.5);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            padding-left: 45px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00d2ff;
            box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .btn-login {
            background: linear-gradient(to right, #00d2ff, #3a7bd5);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 210, 255, 0.5);
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffb3b3;
            border-radius: 10px;
        }
        
        /* Bubble animations */
        .bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: rise linear infinite;
        }
        @keyframes rise {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(-1000px) scale(1.5); opacity: 0; }
        }
    </style>
</head>
<body>

    <div class="bubbles" id="bubbles"></div>

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-fish-fins"></i>
            <h2>Haisan System</h2>
            <p class="text-light opacity-75">Đăng nhập để tiếp tục</p>
        </div>

        {$error_html}

        <form action="index.php?controller=auth&action=login" method="POST">
            <div class="form-group">
                <i class="fa-solid fa-user input-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng nhập
            </button>
        </form>
    </div>

    <script>
        const bubblesContainer = document.getElementById('bubbles');
        for (let i = 0; i < 15; i++) {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble');
            const size = Math.random() * 60 + 20 + 'px';
            bubble.style.width = size;
            bubble.style.height = size;
            bubble.style.left = Math.random() * 100 + 'vw';
            bubble.style.animationDuration = Math.random() * 8 + 5 + 's';
            bubble.style.animationDelay = Math.random() * 5 + 's';
            bubblesContainer.appendChild(bubble);
        }
    </script>
</body>
</html>
HTML;
?>
