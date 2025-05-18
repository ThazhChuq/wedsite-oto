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
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $vehicle_types = $_POST['vehicle_types'] ?? [];
    $image_url = $_POST['image_url'] ?? '';
    $additional_image_urls = $_POST['additional_image_urls'] ?? [];

    if (!$product_id || !is_numeric($product_id)) {
        $error = 'ID sản phẩm không hợp lệ.';
    } elseif (empty($product_name) || empty($price)) {
        $error = 'Tên sản phẩm và giá không được để trống.';
    } else {
        // Cập nhật thông tin sản phẩm
        $stmt = $mysqli->prepare('UPDATE products SET product_name = ?, price = ?, description = ?, brand = ?, image_url = ? WHERE id = ?');
        $stmt->bind_param('sdsssi', $product_name, $price, $description, $brand, $image_url, $product_id);
        if ($stmt->execute()) {
            // Xóa ảnh phụ cũ
            $stmt_del = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
            $stmt_del->bind_param('i', $product_id);
            $stmt_del->execute();
            $stmt_del->close();

            // Thêm ảnh phụ mới
            if (is_array($additional_image_urls)) {
                $stmt_img = $mysqli->prepare('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)');
                foreach ($additional_image_urls as $img_url) {
                    $img_url = trim($img_url);
                    if (!empty($img_url)) {
                        $stmt_img->bind_param('is', $product_id, $img_url);
                        $stmt_img->execute();
                    }
                }
                $stmt_img->close();
            }

            // Xóa loại xe cũ
            $stmt_del_vehicle = $mysqli->prepare('DELETE FROM product_vehicle_types WHERE product_id = ?');
            $stmt_del_vehicle->bind_param('i', $product_id);
            $stmt_del_vehicle->execute();
            $stmt_del_vehicle->close();

            // Thêm loại xe mới
            if (!empty($vehicle_types)) {
                $stmt_vehicle = $mysqli->prepare('INSERT INTO product_vehicle_types (product_id, vehicle_type) VALUES (?, ?)');
                foreach ($vehicle_types as $type) {
                    $stmt_vehicle->bind_param('is', $product_id, $type);
                    $stmt_vehicle->execute();
                }
                $stmt_vehicle->close();
            }

            $success = 'Cập nhật sản phẩm thành công.';
        } else {
            $error = 'Lỗi khi cập nhật sản phẩm.';
        }
        $stmt->close();
    }
}

$product_id = $_GET['id'] ?? null;
$product_details = null;
$additional_images = [];

if ($product_id && is_numeric($product_id)) {
    $stmt = $mysqli->prepare('SELECT id, product_name, price, description, brand, image_url FROM products WHERE id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_details = $result->fetch_assoc();
    $stmt->close();

    if ($product_details) {
        $stmt = $mysqli->prepare('SELECT image_url FROM product_images WHERE product_id = ?');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $additional_images = [];
        while ($row = $res->fetch_assoc()) {
            $additional_images[] = $row['image_url'];
        }
        $stmt->close();
    }
} else {
    // Nếu không có id, lấy danh sách sản phẩm để chọn
    $result = $mysqli->query('SELECT id, product_name FROM products ORDER BY id DESC');
    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $result->free();
    }
}

$mysqli->close();
?>
<?php include '../components/admin_sidebar.html'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Chỉnh sửa sản phẩm - Shop Ô Tô</title>
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
        h1 {
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
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px 12px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            background-color: #333;
            color: #e0e0e0;
            font-size: 1rem;
            box-sizing: border-box;
            resize: vertical;
        }
        textarea {
            min-height: 80px;
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
    <?php include '../components/admin_sidebar.html'; ?>
    <div class="container">
        <h1>Chỉnh sửa sản phẩm</h1>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($product_details): ?>
        <form method="POST" action="">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>" />
            <label for="product_name">Tên sản phẩm:</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_details['product_name']); ?>" required />
            <label for="price">Giá:</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product_details['price']); ?>" step="0.01" required />
            <label for="description">Mô tả:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($product_details['description']); ?></textarea>
            <label for="brand">Hãng xe:</label>
            <select id="brand" name="brand" required>
                <?php
                $brands = [
                    'McLaren',
                    'Koenigsegg',
                    'Aston Martin',
                    'Lamborghini',
                    'Ferrari',
                    'Mercedes-Benz',
                    'SP Automotive',
                    'Pagani',
                    'Bugatti',
                    'Rolls-Royce'
                ];
                foreach ($brands as $brand) {
                    $selected = ($brand === $product_details['brand']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($brand) . '" ' . $selected . '>' . htmlspecialchars($brand) . '</option>';
                }
                ?>
            </select>

            <label>Loại xe:</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                <?php
                $vehicle_types = [
                    'Sedan', 'SUV', 'Pickup', 'Coupe', 'Convertible', 'Hatchback', 'Minivan', 'Wagon', 'Electric'
                ];

                // Lấy loại xe đã chọn cho sản phẩm
                $selected_vehicle_types = [];
                $stmt = $mysqli->prepare('SELECT vehicle_type FROM product_vehicle_types WHERE product_id = ?');
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $selected_vehicle_types[] = $row['vehicle_type'];
                }
                $stmt->close();

                foreach ($vehicle_types as $type) {
                    $checked = in_array($type, $selected_vehicle_types) ? 'checked' : '';
                    echo '<label style="display: flex; align-items: center; gap: 5px; color: #e0e0e0;">';
                    echo '<input type="checkbox" name="vehicle_types[]" value="' . htmlspecialchars($type) . '" ' . $checked . ' />';
                    echo htmlspecialchars($type);
                    echo '</label>';
                }
                ?>
            </div>

            <label for="image_url">URL ảnh đại diện:</label>
            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product_details['image_url']); ?>" />
            <label for="additional_image_urls">URL ảnh phụ (mỗi URL trên một dòng):</label>
            <textarea id="additional_image_urls" name="additional_image_urls[]"><?php echo htmlspecialchars(implode("\n", $additional_images)); ?></textarea>
            <button type="submit">Lưu thay đổi</button>
        </form>
        <?php else: ?>
            <div class="product-list" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 900px; margin: 0 auto;">
                <div style="background-color: #1f1f1f; border-radius: 12px; padding: 15px; width: 200px; box-sizing: border-box; box-shadow: 0 0 10px rgba(249, 168, 37, 0.7); text-align: center;">
                    <img src="https://cdn.tuoitrethudo.vn/stores/news_dataimages/tuoitrethudocomvn/082018/14/18/mso-tiet-lo-hai-phien-ban-dac-biet-moi-cua-sieu-xe-mclaren-720s-49-.8435.jpg" alt="MCLAREN 720S" style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 10px;">
                    <div style="color: #f9a825; font-weight: 700; margin-bottom: 5px;">MCLAREN 720S</div>
                    <div style="color: #e0e0e0; margin-bottom: 10px;">10 VND</div>
                    <a href="../pages/edit_product.php?id=2" style="display: inline-block; padding: 8px 12px; background-color: #f9a825; color: #121212; border-radius: 6px; text-decoration: none; font-weight: 600;">Chỉnh sửa</a>
                </div>
            </div>
        <?php endif; ?>
        <a href="../php/admin_profile.php" class="back-link">Quay về trang quản lý</a>
    </div>
</body>
</html>
