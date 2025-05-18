<?php
session_start();
require_once '../php/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../php/product_images.php';

    $product_name = trim($_POST['product_name'] ?? '');
    $price = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $vehicle_types = $_POST['vehicle_types'] ?? [];
    $image_urls = $_POST['image_url'] ?? [];

    if (empty($product_name) || empty($price) || empty($description) || empty($brand) || empty($image_urls)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin.';
        header('Location: ../pages/add_product.php');
        exit;
    }

    // Validate each image URL
    foreach ($image_urls as $url) {
        if (!filter_var(trim($url), FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'URL ảnh không hợp lệ: ' . htmlspecialchars($url);
            header('Location: ../pages/add_product.php');
            exit;
        }
    }

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        $_SESSION['error'] = 'Lỗi kết nối cơ sở dữ liệu.';
        header('Location: ../pages/add_product.php');
        exit;
    }

    // Insert product with first image as main image
    $main_image_url = trim(array_shift($image_urls));
    $stmt = $mysqli->prepare('INSERT INTO products (product_name, price, description, brand, image_url) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sdsss', $product_name, $price, $description, $brand, $main_image_url);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Insert additional images into product_images table
        addProductImages($mysqli, $product_id, $image_urls);

        // Insert vehicle types into product_vehicle_types table
        if (!empty($vehicle_types)) {
            $stmt_vehicle = $mysqli->prepare('INSERT INTO product_vehicle_types (product_id, vehicle_type) VALUES (?, ?)');
            foreach ($vehicle_types as $type) {
                $stmt_vehicle->bind_param('is', $product_id, $type);
                $stmt_vehicle->execute();
            }
            $stmt_vehicle->close();
        }

        $mysqli->close();
        $_SESSION['success'] = 'Thêm sản phẩm thành công.';
        header('Location: ../pages/add_product.php');
        exit;
    } else {
        $_SESSION['error'] = 'Lỗi khi thêm sản phẩm.';
        $stmt->close();
        $mysqli->close();
        header('Location: ../pages/add_product.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thêm sản phẩm - Shop Ô Tô</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/navbar.css" />
    <style>
        .container {
            max-width: 700px;
            margin: 80px auto 40px auto;
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(249, 168, 37, 0.7);
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h1 {
            text-align: center;
            color: #f9a825;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="number"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
            background-color: #333;
            color: #e0e0e0;
            font-size: 1rem;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button[type="submit"] {
            margin-top: 20px;
            width: 100%;
            background-color: #f9a825;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #121212;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #c7a500;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .error {
            background-color: #b00020;
            color: #fff;
        }
        .success {
            background-color: #388e3c;
            color: #fff;
        }
        .home-button {
            margin-top: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: transparent;
            border: none;
            color: #f9a825;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
        }
        .home-button svg {
            fill: #f9a825;
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
<?php include '../components/admin_sidebar.html'; ?>
<div class="container">
    <h1>TRANG THÊM SẢN PHẨM</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="message error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="message success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    ?>
    <form action="" method="POST" novalidate>
        <label for="product_name">Tên sản phẩm:</label>
        <input type="text" id="product_name" name="product_name" required />

        <label for="price">Giá (VND):</label>
        <input type="number" id="price" name="price" required min="0" step="1000" />

        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" rows="4"></textarea>

    <label for="brand">Hãng xe:</label>
    <select id="brand" name="brand" required style="width: 100%; padding: 8px 10px; margin-top: 5px; border-radius: 5px; border: none; background-color: #333; color: #e0e0e0; font-size: 1rem; box-sizing: border-box;">
        <option value="">Chọn hãng xe</option>
        <option>McLaren</option>
        <option>Koenigsegg</option>
        <option>Aston Martin</option>
        <option>Lamborghini</option>
        <option>Ferrari</option>
        <option>Mercedes-Benz</option>
        <option>SP Automotive</option>
        <option>Pagani</option>
        <option>Bugatti</option>
        <option>Rolls-Royce</option>
    </select>

    <label>Loại xe:</label>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
        <?php
        $vehicle_types = [
            'Sedan', 'SUV', 'Pickup', 'Coupe', 'Convertible', 'Hatchback', 'Minivan', 'Wagon', 'Electric'
        ];
        foreach ($vehicle_types as $type) {
            echo '<label style="display: flex; align-items: center; gap: 5px; color: #e0e0e0;">';
            echo '<input type="checkbox" name="vehicle_types[]" value="' . htmlspecialchars($type) . '" />';
            echo htmlspecialchars($type);
            echo '</label>';
        }
        ?>
    </div>

    <label for="image_url">URL ảnh sản phẩm:</label>
    <div id="image-urls-container">
        <input type="url" id="image_url" name="image_url[]" required />
    </div>
    <button type="button" id="add-image-url" style="margin-top: 8px; background-color: #f9a825; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-weight: 600;">+</button>

    <button type="submit">Thêm sản phẩm</button>
</form>
    <script>
        document.getElementById('add-image-url').addEventListener('click', function() {
            const container = document.getElementById('image-urls-container');
            const input = document.createElement('input');
            input.type = 'url';
            input.name = 'image_url[]';
            input.required = true;
            input.style.width = '100%';
            input.style.padding = '8px 10px';
            input.style.marginTop = '5px';
            input.style.borderRadius = '5px';
            input.style.border = 'none';
            input.style.backgroundColor = '#333';
            input.style.color = '#e0e0e0';
            input.style.fontSize = '1rem';
            input.style.boxSizing = 'border-box';
            container.appendChild(input);
        });
    </script>
<button class="home-button" aria-label="Quay về trang quản lý" onclick="window.location.href='/shop_oto/php/admin_profile.php'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
        </svg>
        Quay về trang quản lý
    </button>
</div>
</body>
</html>
<?php
?>
