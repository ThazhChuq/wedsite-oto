<?php
session_start();
require_once '../php/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('Lỗi kết nối cơ sở dữ liệu.');
}

$error = '';
$success = '';

// Xử lý xóa người dùng
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id === $_SESSION['user_id']) {
        $error = 'Bạn không thể xóa chính mình.';
    } else {
        $stmt = $mysqli->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $delete_id);
        if ($stmt->execute()) {
            $success = 'Xóa người dùng thành công.';
        } else {
            $error = 'Lỗi khi xóa người dùng.';
        }
        $stmt->close();
    }
}

// Lấy danh sách người dùng
$result = $mysqli->query('SELECT id, name, email, role FROM users ORDER BY id DESC');
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
}

$mysqli->close();
?>
<?php include '../components/admin_sidebar.html'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý người dùng - Shop Ô Tô</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #f9a825;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            max-width: 900px;
            margin: 0 auto 30px;
            border-collapse: collapse;
            background-color: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background-color: #f9a825;
            color: #121212;
            font-weight: 700;
        }
        tr:hover {
            background-color: #333;
        }
        a.button {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 5px;
            background-color: #f9a825;
            color: #121212;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        a.button:hover {
            background-color: #c7a500;
        }
        .message {
            max-width: 900px;
            margin: 10px auto;
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
        .add-user-button {
            max-width: 900px;
            margin: 0 auto 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <?php include '../components/admin_sidebar.html'; ?>
    <div class="container">
        <h1>Quản lý người dùng</h1>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <div class="add-user-button">
            <a href="user_add.php" class="button">Thêm người dùng mới</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) === 0): ?>
                    <tr><td colspan="5" style="text-align:center;">Không có người dùng nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <a href="user_edit.php?id=<?php echo $user['id']; ?>" class="button">Sửa</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="user_management.php?delete_id=<?php echo $user['id']; ?>" class="button" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?');">Xóa</a>
                                <?php else: ?>
                                    <span style="color: #888;">Không thể xóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="../php/admin_profile.php" class="button" style="max-width: 900px; display: block; margin: 0 auto;">Quay về trang quản lý</a>
    </div>
</body>
</html>
