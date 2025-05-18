<?php
session_start();
require_once __DIR__ . '/config.php'; // Database connection settings

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin.';
        header('Location: ../pages/login.php');
        exit;
    }

    // Hash password
    $passwordHash = hash('sha256', $password);

    // Connect to database
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        $_SESSION['error'] = 'Lỗi kết nối cơ sở dữ liệu.';
        header('Location: ../pages/login.php');
        exit;
    }

    // Check credentials
    $stmt = $mysqli->prepare('SELECT id, name, role FROM users WHERE email = ? AND password = ?');
    $stmt->bind_param('ss', $email, $passwordHash);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $role);
        $stmt->fetch();
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;

        $_SESSION['success'] = 'Đăng nhập thành công.';

        $stmt->close();
        $mysqli->close();

        if ($role === 'admin') {
            header('Location: admin_profile.php');
        } else {
            header('Location: user_profile.php');
        }
        exit;
    } else {
        $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
        $stmt->close();
        $mysqli->close();
        header('Location: ../pages/login.php');
        exit;
    }
} else {
    header('Location: ../pages/login.php');
    exit;
}
?>
