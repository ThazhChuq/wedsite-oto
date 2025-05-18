<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang người dùng</title>
</head>
<body>
    <h1>Chào mừng bạn, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Đây là trang hồ sơ người dùng.</p>
    <a href="../php/logout.php">Đăng xuất</a>
</body>
</html>
