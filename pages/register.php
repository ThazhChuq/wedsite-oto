<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../styles/auth.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <link rel="stylesheet" href="../styles/main.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-top">
        <div class="logo">
            <a href="../index.php" aria-label="Trang chủ">
                <img src="../images/shop oto.png" alt="Logo" />
                <span class="shop-name">Shop Ô Tô</span>
            </a>
        </div>
    </div>
    <div class="navbar-bottom">
        <button class="hamburger" aria-label="Menu" title="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="4" width="24" height="2" fill="white"/>
                <rect y="11" width="24" height="2" fill="white"/>
                <rect y="18" width="24" height="2" fill="white"/>
            </svg>
        </button>
        <input type="search" class="search-bar" placeholder="Tìm kiếm..." aria-label="Tìm kiếm" />
        <button class="cart" aria-label="Giỏ hàng" title="Giỏ hàng">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="7" cy="20" r="2" fill="white"/>
                <circle cx="17" cy="20" r="2" fill="white"/>
                <path d="M1 1H5L7.68 14.39C7.77 14.87 8.18 15.22 8.67 15.22H19.55C20.05 15.22 20.5 14.83 20.62 14.34L22.62 7.34C22.7 7.07 22.7 6.77 22.62 6.5C22.5 6.01 22.05 5.62 21.55 5.62H8.92L8.27 3H1V1Z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="profile" aria-label="Profile" title="Profile">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="12" r="8" stroke="white" stroke-width="2"/>
                <path d="M4 28C4 21.3726 10.3726 16 16 16C21.6274 16 28 21.3726 28 28" stroke="white" stroke-width="2"/>
            </svg>
        </button>
    </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar" aria-hidden="true">
    <button class="sidebar-close" aria-label="Close sidebar">&times;</button>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="#">Trang chủ</a></li>
            <li><a href="#">Xe</a></li>
            <li><a href="#">Giới thiệu</a></li>
            <li><a href="#">Liên hệ</a></li>
        </ul>
        <hr />
        <div class="sidebar-filters">
            <h3>Bộ lọc tìm kiếm</h3>
            <label>
                Hãng xe:
                <select>
                    <option>Tất cả</option>
                    <option>McLaren</option>
                    <option>Koenigsegg</option>
                    <option>Aston Martin</option>
                    <option>Lamborghini</option>
                    <option>Ferrari</option>
                    <option>Mercedes-Benz</option>
                    <option>SP Automotive</option>
                    <option>Pagani</option>
                    <option>Bugatti</option>
                    <option>Rolls-Royce</option>
                </select>
            </label>
            <label>
                Mức giá:
                <select>
                    <option>Tất cả</option>
                    <option>Dưới 7 tỷ</option>
                    <option>7 tỷ - 15 tỷ</option>
                    <option>Trên 15 tỷ</option>
                </select>
            </label>
            <label>
                Loại xe:
                <select>
                    <option>Tất cả</option>
                    <option>Sedan</option>
                    <option>SUV</option>
                    <option>Pickup</option>
                    <option>Coupe</option>
                    <option>Convertible</option>
                    <option>Hatchback</option>
                    <option>Minivan</option>
                    <option>Wagon</option>
                    <option>Electric</option>
                </select>
            </label>
        </div>
        <hr />
        <ul>
            <li><a href="#">Hồ sơ</a></li>
            <li><a href="#">Đơn hàng</a></li>
        </ul>
        <hr />
        <ul>
            <li><a href="#">Hỗ trợ</a></li>
            <li><a href="#">Liên hệ</a></li>
        </ul>
    </nav>
</aside>

    <div class="auth-container">
        <form class="auth-form" method="POST" action="../php/register.php" novalidate>
            <h1 class="auth-title">Đăng ký</h1>
            <?php if ($error): ?>
                <p class="auth-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="auth-success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <label for="name" class="auth-label">Tên:</label>
            <input type="text" id="name" name="name" class="auth-input" required autocomplete="name" />
            <label for="email" class="auth-label">Email:</label>
            <input type="email" id="email" name="email" class="auth-input" required autocomplete="email" />
            <label for="password" class="auth-label">Mật khẩu:</label>
            <input type="password" id="password" name="password" class="auth-input" required autocomplete="new-password" />
            <button type="submit" class="auth-button">Đăng ký</button>
            <p class="auth-footer">Bạn đã có tài khoản? <a href="login.php" class="auth-link">Đăng nhập</a></p>
        </form>
    </div>
</body>
</html>
