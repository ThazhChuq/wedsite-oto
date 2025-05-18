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
$product_details = null;
$additional_images = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id || !is_numeric($product_id)) {
        $error = 'ID sản phẩm không hợp lệ.';
    } else {
        // Lấy thông tin sản phẩm
        $stmt = $mysqli->prepare('SELECT product_name, price, description, brand, image_url FROM products WHERE id = ?');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_details = $result->fetch_assoc();
        $stmt->close();

        if (!$product_details) {
            $error = 'Sản phẩm không tồn tại.';
        } else {
            // Lấy ảnh phụ
            $stmt = $mysqli->prepare('SELECT image_url FROM product_images WHERE product_id = ?');
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $additional_images = [];
            while ($row = $res->fetch_assoc()) {
                $additional_images[] = $row['image_url'];
            }
            $stmt->close();

            if (isset($_POST['confirm_delete'])) {
                // Xóa ảnh phụ trong DB
                $stmt = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $stmt->close();

                // Xóa sản phẩm
                $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
                $stmt->bind_param('i', $product_id);
                if ($stmt->execute()) {
                    $success = 'Xóa sản phẩm và ' . (count($additional_images) + 1) . ' URL ảnh thành công.';
                    $product_details = null;
                    $additional_images = [];
                } else {
                    $error = 'Lỗi khi xóa sản phẩm.';
                }
                $stmt->close();
            }
        }
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Xóa sản phẩm - Shop Ô Tô</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        h1, h2 {
            color: #f9a825;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
            width: 100%;
            max-width: 700px;
            box-sizing: border-box;
        }
        label {
            display: block;
            font-weight: 700;
            margin-top: 15px;
            font-size: 1.1rem;
            color: #f9a825;
        }
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            background-color: #333;
            color: #e0e0e0;
            font-size: 1rem;
            box-sizing: border-box;
            transition: background-color 0.3s ease;
        }
        select:hover, select:focus {
            background-color: #444;
            outline: none;
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
            max-width: 700px;
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
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            color: #f9a825;
            box-shadow: 0 0 10px rgba(249, 168, 37, 0.<create_file>
<path>pages/delete_product.php</path>
<content>
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
$product_details = null;
$additional_images = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id || !is_numeric($product_id)) {
        $error = 'ID sản phẩm không hợp lệ.';
    } else {
        // Lấy thông tin sản phẩm
        $stmt = $mysqli->prepare('SELECT product_name, price, description, brand, image_url FROM products WHERE id = ?');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_details = $result->fetch_assoc();
        $stmt->close();

        if (!$product_details) {
            $error = 'Sản phẩm không tồn tại.';
        } else {
            // Lấy ảnh phụ
            $stmt = $mysqli->prepare('SELECT image_url FROM product_images WHERE product_id = ?');
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $additional_images = [];
            while ($row = $res->fetch_assoc()) {
                $additional_images[] = $row['image_url'];
            }
            $stmt->close();

            if (isset($_POST['confirm_delete'])) {
                // Xóa ảnh phụ trong DB
                $stmt = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $stmt->close();

                // Xóa sản phẩm
                $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
                $stmt->bind_param('i', $product_id);
                if ($stmt->execute()) {
                    $success = 'Xóa sản phẩm và ' . (count($additional_images) + 1) . ' URL ảnh thành công.';
                    $product_details = null;
                    $additional_images = [];
                } else {
                    $error = 'Lỗi khi xóa sản phẩm.';
                }
                $stmt->close();
            }
        }
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
<?php include '../components/admin_sidebar.html'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Xóa sản phẩm - Shop Ô Tô</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            padding-top: 60px; /* Add top padding to avoid overlap with fixed navbar */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        h1, h2 {
            color: #f9a825;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
            width: 100%;
            max-width: 700px;
            box-sizing: border-box;
        }
        label {
            display: block;
            font-weight: 700;
            margin-top: 15px;
            font-size: 1.1rem;
            color: #f9a825;
        }
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            background-color: #333;
            color: #e0e0e0;
            font-size: 1rem;
            box-sizing: border-box;
            transition: background-color 0.3s ease;
        }
        select:hover, select:focus {
            background-color: #444;
            outline: none;
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
            max-width: 700px;
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
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            color: #f9a825;
            box-shadow: 0 0 10px rgba(249, 168, 37, 0.7);
        }
        .product-info h2 {
            margin-top: 0;
        }
        .product-info p {
            margin: 8px 0;
            font-size: 1rem;
        }
        .product-info img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin-top: 15px;
            box-shadow: 0 0 8px rgba(249, 168, 37, 0.7);
        }
        .additional-images {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        .additional-images img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(249, 168, 37, 0.7);
        }
        label.confirm-label {
            display: flex;
            align-items: center;
            margin-top: 20px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            color: #f9a825;
        }
        label.confirm-label input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .back-link {
            display: block;
            max-width: 700px;
            margin: 30px auto 0;
            text-align: center;
            color: #f9a825;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #c7a500;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="page-content" style="display: flex; justify-content: center; align-items: center; flex-direction: column; min-height: 80vh;">
        <h1>Xóa sản phẩm</h1>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="" style="width: 100%; max-width: 700px;">
            <label for="product_id">Chọn sản phẩm cần xóa:</label>
            <select id="product_id" name="product_id" required onchange="this.form.submit()">
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['id']); ?>" <?php if ($product_details && isset($product_details['id']) && $product_details['id'] == $product['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($product_details): ?>
                <div class="product-info">
                    <h2>Thông tin sản phẩm</h2>
                    <p><strong>Tên:</strong> <?php echo htmlspecialchars($product_details['product_name']); ?></p>
                    <p><strong>Giá:</strong> <?php echo number_format($product_details['price'], 0, ',', '.') . ' VND'; ?></p>
                    <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($product_details['description'])); ?></p>
                    <p><strong>Hãng xe:</strong> <?php echo htmlspecialchars($product_details['brand']); ?></p>
                    <p><strong>Ảnh đại diện:</strong></p>
                    <img src="<?php echo htmlspecialchars($product_details['image_url']); ?>" alt="Ảnh đại diện" style="max-width: 100%; height: auto; max-height: 300px; border-radius: 8px;" />
                    <?php if (!empty($additional_images)): ?>
                        <p><strong>Ảnh phụ:</strong></p>
                        <div class="additional-images">
                            <?php foreach ($additional_images as $img_url): ?>
                                <img src="<?php echo htmlspecialchars($img_url); ?>" alt="Ảnh phụ" style="max-width: 100px; height: auto; max-height: 75px; border-radius: 6px;" />
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <label class="confirm-label">
                    <input type="checkbox" name="confirm_delete" required />
                    Tôi xác nhận muốn xóa sản phẩm này
                </label>
                <button type="submit">Xóa sản phẩm</button>
            <?php endif; ?>
        </form>
        <a href="../php/admin_profile.php" class="back-link">Quay về trang quản lý</a>
    </div>
    <script>
        // Tự động submit form khi chọn sản phẩm
        document.getElementById('product_id').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
</create_file>
