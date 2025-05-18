<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

require_once '../php/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('Lỗi kết nối cơ sở dữ liệu.');
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = false;

// Xử lý cập nhật thông tin cá nhân (tên, email)
if (isset($_POST['update_info'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($name === '' || $email === '') {
        $message = 'Tên và email không được để trống.';
        $error = true;
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $email, $user_id);
        if ($stmt->execute()) {
            $message = 'Cập nhật thông tin thành công.';
        } else {
            $message = 'Lỗi khi cập nhật thông tin.';
            $error = true;
        }
        $stmt->close();
    }
}

// Xử lý đổi mật khẩu
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $message = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
        $error = true;
    } else {
        // Lấy mật khẩu hiện tại từ database
        $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Kiểm tra mật khẩu hiện tại
        if (!password_verify($current_password, $hashed_password)) {
            $message = 'Mật khẩu hiện tại không đúng.';
            $error = true;
        } else {
            // Cập nhật mật khẩu mới
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param('si', $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                $message = 'Đổi mật khẩu thành công.';
            } else {
                $message = 'Lỗi khi đổi mật khẩu.';
                $error = true;
            }
            $stmt->close();
        }
    }
}

// Lấy thông tin user hiện tại
$stmt = $mysqli->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $role);
$stmt->fetch();
$stmt->close();

$mysqli->close();
?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

require_once '../php/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('Lỗi kết nối cơ sở dữ liệu.');
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = false;

// Xử lý cập nhật thông tin cá nhân (tên, email)
if (isset($_POST['update_info'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($name === '' || $email === '') {
        $message = 'Tên và email không được để trống.';
        $error = true;
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $email, $user_id);
        if ($stmt->execute()) {
            $message = 'Cập nhật thông tin thành công.';
        } else {
            $message = 'Lỗi khi cập nhật thông tin.';
            $error = true;
        }
        $stmt->close();
    }
}

// Xử lý đổi mật khẩu
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $message = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
        $error = true;
    } else {
        // Lấy mật khẩu hiện tại từ database
        $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Kiểm tra mật khẩu hiện tại
        if (!password_verify($current_password, $hashed_password)) {
            $message = 'Mật khẩu hiện tại không đúng.';
            $error = true;
        } else {
            // Cập nhật mật khẩu mới
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param('si', $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                $message = 'Đổi mật khẩu thành công.';
            } else {
                $message = 'Lỗi khi đổi mật khẩu.';
                $error = true;
            }
            $stmt->close();
        }
    }
}

// Lấy thông tin user hiện tại
$stmt = $mysqli->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $role);
$stmt->fetch();
$stmt->close();

$mysqli->close();
?>
<?php include '../components/admin_sidebar.html'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thông tin Admin - Shop Ô Tô</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        h1 {
            color: #f9a825;
            text-align: center;
            margin-bottom: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 700;
            color: #f9a825;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            background-color: #333;
            color: #e0e0e0;
            font-size: 1rem;
            box-sizing: border-box;
        }
        button {
            margin-top: 25px;
            width: 100%;
            background-color: #f9a825;
            border: none;
            padding: 14px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #121212;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #c7a500;
        }
        .message {
            margin-top: 15px;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            text-align: center;
            font-size: 1.1rem;
        }
        .error {
            background-color: #b00020;
            color: #fff;
        }
        .success {
            background-color: #388e3c;
            color: #fff;
        }
        .section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include '../components/admin_sidebar.html'; ?>
    <div class="container">
        <h1>Thông tin Admin</h1>
        <?php if ($message): ?>
            <div class="message <?php echo $error ? 'error' : 'success'; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="section" style="display: flex; gap: 40px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <h2>Thông tin cá nhân</h2>
                <form method="POST" action="">
                    <input type="hidden" name="update_info" value="1" />
                    <label for="name">Tên:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required />
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required />
                    <label>Quyền:</label>
                    <input type="text" value="<?php echo htmlspecialchars($role); ?>" disabled />
                    <button type="submit">Cập nhật thông tin</button>
                </form>
            </div>

            <div style="flex: 1; min-width: 280px;">
                <h2>Đổi mật khẩu</h2>
                <form method="POST" action="">
                    <input type="hidden" name="change_password" value="1" />
                    <label for="current_password">Mật khẩu hiện tại:</label>
                    <input type="password" id="current_password" name="current_password" required />
                    <label for="new_password">Mật khẩu mới:</label>
                    <input type="password" id="new_password" name="new_password" required />
                    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                    <button type="submit">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
