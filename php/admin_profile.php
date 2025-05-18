<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang quản lý hệ thống Admin</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px;
        }
        header {
            width: 100%;
            padding: 20px;
            background-color: #1f1f1f;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            color: #f9a825;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .home-button {
            position: absolute;
            left: 20px;
            background: none;
            border: none;
            cursor: pointer;
            color: #f9a825;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .home-button svg {
            width: 24px;
            height: 24px;
            fill: #f9a825;
        }
        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            max-width: 900px;
            width: 100%;
        }
        .admin-button {
            background-color: #1f1f1f;
            border: 2px solid #f9a825;
            color: #f9a825;
            padding: 15px 25px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            flex: 1 1 200px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .admin-button:hover {
            background-color: #f9a825;
            color: #121212;
        }
        main {
            width: 100%;
            max-width: 900px;
            text-align: center;
            margin-top: 40px;
        }
        .logout-link {
            color: #f9a825;
            font-weight: bold;
            display: block;
            margin-top: 40px;
            text-decoration: none;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>
    <button class="home-button" aria-label="Quay về trang chính" onclick="window.location.href='../pages/add_product.php'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
    </button>
    ĐÂY LÀ TRANG QUẢN Ý HỆ THỐNG ADMIN
</header>
<main>
    <h1>Chào mừng quản trị viên, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Đây là trang quản lý hệ thống dành cho admin.</p>
    <a href="../php/logout.php" class="logout-link">Đăng xuất</a>
    <div class="button-container" role="navigation" aria-label="Các thao tác quản lý admin">
        <a href="../pages/add_product.php" class="admin-button">Thêm sản phẩm</a>
        <a href="../pages/delete_product.php" class="admin-button">Xóa sản phẩm</a>
        <a href="../pages/edit_product.php" class="admin-button">Sửa sản phẩm</a>
        <a href="../pages/discount_product.php" class="admin-button">Giảm giá sản phẩm</a>
        <a href="../pages/increase_price_product.php" class="admin-button">Tăng giá sản phẩm</a>
        <a href="../pages/admin_info.php" class="admin-button">Thông tin admin</a>
        <a href="../pages/user_management.php" class="admin-button">Quản lý người dùng</a>
        <a href="../pages/sales_report.php" class="admin-button">Báo cáo doanh thu</a>
        <a href="../pages/order_report.php" class="admin-button">Báo cáo đơn hàng</a>
</main>
</body>
</html>
