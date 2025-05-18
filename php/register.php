<?php
session_start();
require_once __DIR__ . '/config.php'; // Database connection settings

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin.';
        header('Location: ../pages/register.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Email không hợp lệ.';
        header('Location: ../pages/register.php');
        exit;
    }

    // Hash password
    $passwordHash = hash('sha256', $password);

    // Connect to database
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        $_SESSION['error'] = 'Lỗi kết nối cơ sở dữ liệu.';
        header('Location: ../pages/register.php');
        exit;
    }

    // Check if email already exists
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'Email đã được đăng ký.';
        $stmt->close();
        $mysqli->close();
        header('Location: ../pages/register.php');
        exit;
    }
    $stmt->close();

    // Insert new user with role 'user'
    $stmt = $mysqli->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "user")');
    $stmt->bind_param('sss', $name, $email, $passwordHash);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Đăng ký thành công.';
        $stmt->close();
        $mysqli->close();
        // Redirect to user_profile.php after successful registration
        header('Location: user_profile.php');
        exit;
    } else {
        $_SESSION['error'] = 'Lỗi khi đăng ký.';
        $stmt->close();
        $mysqli->close();
        header('Location: ../pages/register.php');
        exit;
    }
} else {
    header('Location: ../pages/register.php');
    exit;
}
?>
