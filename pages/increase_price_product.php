<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../php/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('Lỗi kết nối cơ sở dữ liệu.');
}

$message = '';
$error = false;
$selected_product = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $increase_percent = $_POST['increase_percent'] ?? null;
    $increase_fixed = $_POST['increase_fixed'] ?? null;

    if ($product_id !== null && $product_id !== '') {
        // Nếu form submit cập nhật tăng giá
        if (isset($_POST['increase_percent']) || isset($_POST['increase_fixed'])) {
            $stmt = $mysqli->prepare("UPDATE products SET increase_percent = ?, increase_fixed = ? WHERE id = ?");
            $stmt->bind_param('ddi', $increase_percent, $increase_fixed, $product_id);
            if ($stmt->execute()) {
                $message = 'Cập nhật tăng giá thành công.';
            } else {
                $message = 'Lỗi khi cập nhật tăng giá.';
                $error = true;
            }
            $stmt->close();
        }
        // Lấy thông tin sản phẩm được chọn để hiển thị
        $stmt = $mysqli->prepare("SELECT id, product_name, price, increase_percent, increase_fixed, image_url FROM products WHERE id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $selected_product = $result->fetch_assoc();
        $stmt->close();
    }
}

$result = $mysqli->query('SELECT id, product_name FROM products ORDER BY id DESC');
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
    <title>Tăng giá sản phẩm - Shop Ô Tô</title>
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
        <h1>Tăng giá sản phẩm</h1>
        <?php if ($message): ?>
            <div class="message <?php echo $error ? 'error' : 'success'; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="product_id">Chọn sản phẩm:</label>
            <select id="product_id" name="product_id" required onchange="this.form.submit()">
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['id']); ?>" <?php if ($selected_product && $selected_product['id'] == $product['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($selected_product): ?>
                <div class="product-info">
                    <img src="<?php echo htmlspecialchars($selected_product['image_url']); ?>" alt="<?php echo htmlspecialchars($selected_product['product_name']); ?>" />
                    <div>
                        <?php echo htmlspecialchars($selected_product['product_name']); ?><br />
                        Giá gốc: <?php echo number_format($selected_product['price'], 0, ',', '.') . ' VND'; ?><br />
                        Tăng giá (%): <input type="number" step="0.01" name="increase_percent" value="<?php echo htmlspecialchars($selected_product['increase_percent']); ?>" min="0" max="" /><br />
                        Tăng giá (VND): <input type="number" step="1" name="increase_fixed" value="<?php echo htmlspecialchars($selected_product['increase_fixed']); ?>" min="0" max="" /><br />
                        <button type="submit">Cập nhật</button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
        <a href="../php/admin_profile.php" class="back-link">Quay về trang quản lý</a>
    </div>
</body>
</html>
