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
$products = [];
$selected_product = null;
$discount_type = '';
$discount_value = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $discount_type = $_POST['discount_type'] ?? '';
    $discount_value = $_POST['discount_value'] ?? 0;

    if (!$product_id || !is_numeric($product_id)) {
        $error = 'Vui lòng chọn sản phẩm hợp lệ.';
    } elseif (!in_array($discount_type, ['percent', 'fixed'])) {
        $error = 'Loại giảm giá không hợp lệ.';
    } elseif (!is_numeric($discount_value) || $discount_value < 0) {
        $error = 'Giá trị giảm giá không hợp lệ.';
    } else {
        // Lấy thông tin sản phẩm
        $stmt = $mysqli->prepare('SELECT price FROM products WHERE id = ?');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if (!$product) {
            $error = 'Sản phẩm không tồn tại.';
        } else {
            $original_price = $product['price'];
            if ($discount_type === 'percent') {
                if ($discount_value > 100) {
                    $error = 'Phần trăm giảm giá không được lớn hơn 100%.';
                } else {
                    // Cập nhật discount_percent, xóa discount_fixed
                    $stmt = $mysqli->prepare('UPDATE products SET discount_percent = ?, discount_fixed = NULL WHERE id = ?');
                    $stmt->bind_param('di', $discount_value, $product_id);
                    if ($stmt->execute()) {
                        $success = 'Giảm giá phần trăm đã được áp dụng thành công.';
                    } else {
                        $error = 'Lỗi khi cập nhật giảm giá.';
                    }
                    $stmt->close();
                }
            } else { // fixed
                if ($discount_value > $original_price) {
                    $error = 'Số tiền giảm giá không được lớn hơn giá gốc.';
                } else {
                    // Cập nhật discount_fixed, xóa discount_percent
                    $stmt = $mysqli->prepare('UPDATE products SET discount_fixed = ?, discount_percent = NULL WHERE id = ?');
                    $stmt->bind_param('di', $discount_value, $product_id);
                    if ($stmt->execute()) {
                        $success = 'Giảm giá số tiền cố định đã được áp dụng thành công.';
                    } else {
                        $error = 'Lỗi khi cập nhật giảm giá.';
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// Lấy danh sách sản phẩm
$result = $mysqli->query('SELECT id, product_name, price, image_url, discount_percent, discount_fixed FROM products ORDER BY product_name ASC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $result->free();
}

if (isset($product_id) && is_numeric($product_id)) {
    foreach ($products as $p) {
        if ($p['id'] == $product_id) {
            $selected_product = $p;
            break;
        }
    }
}

$mysqli->close();
?>
<?php include '../components/admin_sidebar.html'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Giảm giá sản phẩm - Shop Ô Tô</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #f9a825;
            margin-bottom: 20px;
            text-align: center;
        }
        form {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }
        label {
            display: block;
            font-weight: 700;
            margin-top: 15px;
            font-size: 1.1rem;
            color: #f9a825;
        }
        select, input[type="number"] {
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
            max-width: 600px;
            margin: 15px auto;
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
        .product-info {
            margin-top: 20px;
            background-color: #1f1f1f;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(249, 168, 37, 0.7);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-info img {
            width: 120px;
            height: auto;
            border-radius: 8px;
        }
        .product-info div {
            color: #f9a825;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .back-link {
            margin-top: 20px;
            color: #f9a825;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            text-align: center;
        }
        .back-link:hover {
            color: #c7a500;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include '../components/admin_sidebar.html'; ?>
    <div class="container">
        <h1>Giảm giá sản phẩm</h1>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="product_id">Chọn sản phẩm:</label>
            <select id="product_id" name="product_id" required onchange="this.form.submit()">
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>" <?php if (isset($product_id) && $product_id == $product['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($selected_product): ?>
                <div class="product-info">
                    <img src="<?php echo htmlspecialchars($selected_product['image_url']); ?>" alt="<?php echo htmlspecialchars($selected_product['product_name']); ?>" />
                    <div>
                        <?php echo htmlspecialchars($selected_product['product_name']); ?><br />
                        Giá gốc: <?php echo number_format($selected_product['price'], 0, ',', '.'); ?> VND
                    </div>
                </div>

                <label for="discount_type">Loại giảm giá:</label>
                <select id="discount_type" name="discount_type" required>
                    <option value="percent" <?php if ($discount_type === 'percent') echo 'selected'; ?>>Phần trăm (%)</option>
                    <option value="fixed" <?php if ($discount_type === 'fixed') echo 'selected'; ?>>Số tiền cố định (VND)</option>
                </select>

                <label for="discount_value">Giá trị giảm giá:</label>
                <input type="number" id="discount_value" name="discount_value" min="0" step="0.01" value="<?php echo htmlspecialchars($discount_value); ?>" required />

                <button type="submit">Áp dụng giảm giá</button>
            <?php endif; ?>
        </form>
        <a href="../php/admin_profile.php" class="back-link">Quay về trang quản lý</a>
    </div>
</body>
</html>
