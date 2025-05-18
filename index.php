<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shop Ô Tô</title>
    <link rel="stylesheet" href="styles/main.css" />
    <link rel="stylesheet" href="styles/navbar.css" />
</head>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('Lỗi kết nối cơ sở dữ liệu.');
}

$result = $mysqli->query('SELECT id, product_name, price, image_url, discount_percent, discount_fixed, increase_percent, increase_fixed FROM products ORDER BY id DESC');
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['additional_images'] = [];
        // Fetch additional images for this product
        $stmt = $mysqli->prepare('SELECT image_url FROM product_images WHERE product_id = ?');
        $stmt->bind_param('i', $row['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($img = $res->fetch_assoc()) {
            $row['additional_images'][] = $img['image_url'];
        }
        $stmt->close();

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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shop Ô Tô</title>
    <link rel="stylesheet" href="styles/main.css" />
    <link rel="stylesheet" href="styles/navbar.css" />
    <style>
.product-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    justify-content: space-between;
    margin: 20px auto;
    max-width: 1200px;
}
.product-card {
    background-color: #1f1f1f;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(249, 168, 37, 0.7);
    width: 100%;
    padding: 15px;
    color: #e0e0e0;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.product-card img {
    max-width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}
        .product-name {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .product-price {
            color: #f9a825;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>
    <style>
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 0.9rem;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
            z-index: 10;
            user-select: none;
        }
    </style>
    <style>
        .increase-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 0.9rem;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
            z-index: 10;
            user-select: none;
        }
        .product-price-increase {
            color: white;
            font-weight: bold;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar component -->
    <div id="navbar-container"></div>

    <div class="product-list">
    <?php if (count($products) === 0): ?>
        <p>Chưa có sản phẩm nào.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div style="position: relative; display: inline-block;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                    <?php
                    $discount_percent = $product['discount_percent'];
                    $discount_fixed = $product['discount_fixed'];
                    $increase_percent = $product['increase_percent'] ?? null;
                    $increase_fixed = $product['increase_fixed'] ?? null;
                    ?>
                    <?php if ($discount_percent !== null && $discount_percent != 0 || $discount_fixed !== null && $discount_fixed != 0): ?>
                        <div class="discount-badge">
                            <?php
                            if ($discount_percent !== null && $discount_percent != 0) {
                                echo '-' . rtrim(rtrim(number_format($discount_percent, 2), '0'), '.') . '%';
                            } elseif ($discount_fixed !== null && $discount_fixed != 0) {
                                echo '- ' . number_format($discount_fixed, 0, ',', '.') . ' VND';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($increase_percent !== null && $increase_percent != 0 || $increase_fixed !== null && $increase_fixed != 0): ?>
                        <div class="increase-badge">
                            <?php
                            if ($increase_percent !== null && $increase_percent != 0) {
                                echo '+' . rtrim(rtrim(number_format($increase_percent, 2), '0'), '.') . '%';
                            } elseif ($increase_fixed !== null && $increase_fixed != 0) {
                                echo '+ ' . number_format($increase_fixed, 0, ',', '.') . ' VND';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                <?php
                $original_price = $product['price'];
                $discounted_price = $original_price;
                $discount_label = '';

                if ($discount_percent !== null) {
                    $discounted_price = $original_price * (1 - $discount_percent / 100);
                    $discount_label = '-' . $discount_percent . '%';
                } elseif ($discount_fixed !== null) {
                    $discounted_price = $original_price - $discount_fixed;
                    $discount_label = '- ' . number_format($discount_fixed, 0, ',', '.') . ' VND';
                }
                ?>
                <?php if ($discount_percent !== null || $discount_fixed !== null): ?>
                    <div class="product-price-original" style="text-decoration: line-through; color: gray;"><?php echo number_format($original_price, 0, ',', '.') . ' VND'; ?></div>
                    <div class="product-price-discount" style="color: red; font-weight: bold;"><?php echo number_format($discounted_price, 0, ',', '.') . ' VND'; ?></div>
                <?php elseif ($increase_percent !== null || $increase_fixed !== null): ?>
                    <div class="product-price-original" style="text-decoration: line-through; color: gray;"><?php echo number_format($original_price, 0, ',', '.') . ' VND'; ?></div>
                    <div class="product-price-increase" style="color: white; font-weight: bold;"><?php
                        if ($increase_percent !== null) {
                            $increased_price = $original_price * (1 + $increase_percent / 100);
                            echo number_format($increased_price, 0, ',', '.') . ' VND';
                        } elseif ($increase_fixed !== null) {
                            $increased_price = $original_price + $increase_fixed;
                            echo number_format($increased_price, 0, ',', '.') . ' VND';
                        }
                    ?></div>
                <?php else: ?>
                    <div class="product-price"><?php echo number_format($original_price, 0, ',', '.') . ' VND'; ?></div>
                <?php endif; ?>
                <?php if (!empty($product['additional_images'])): ?>
                    <div class="additional-images" style="margin-top: 10px; display: flex; gap: 5px; justify-content: center;">
                        <?php foreach ($product['additional_images'] as $img_url): ?>
                            <?php
                            // Fix for multiple URLs concatenated in one string separated by spaces or commas
                            $urls = preg_split('/[\s,]+/', trim($img_url));
                            foreach ($urls as $url):
                                if (!empty($url)):
                            ?>
                                <img src="<?php echo htmlspecialchars($url); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="width: 50px; height: auto; border-radius: 4px; box-shadow: 0 0 5px rgba(249, 168, 37, 0.7);" />
                            <?php
                                endif;
                            endforeach;
                            ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal for login prompt -->
<div id="login-modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-desc">
    <div class="modal-content">
        <button class="modal-close" aria-label="Close modal">&times;</button>
        <h2 id="modal-title">Thông báo</h2>
        <p id="modal-desc">Bạn vui lòng đăng nhập để xem nhiều ưu đãi và theo dõi sản phẩm yêu thích của bạn</p>
        <div class="modal-buttons">
            <button id="modal-login-btn">Đăng nhập</button>
            <button id="modal-register-btn">Đăng ký</button>
        </div>
    </div>
</div>

<script src="scripts/navbar.js"></script>
<script>
    // Load Navbar component dynamically
    fetch('components/Navbar.html')
        .then(response => response.text())
        .then(html => {
            document.getElementById('navbar-container').innerHTML = html;

            // After loading navbar, update profile button behavior based on session
            const profileButton = document.querySelector('.profile');
            if (profileButton) {
                profileButton.addEventListener('click', () => {
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            window.location.href = 'php/admin_profile.php';
                        <?php else: ?>
                            window.location.href = 'php/user_profile.php';
                        <?php endif; ?>
                    <?php else: ?>
                        const loginModal = document.getElementById('login-modal');
                        if (loginModal) {
                            loginModal.classList.remove('hidden');
                        }
                    <?php endif; ?>
                });
            }

            if (typeof initNavbar === 'function') {
                initNavbar();
            }
        });

    // Add event listeners for modal buttons after DOM content loaded
    document.addEventListener('DOMContentLoaded', () => {
        const modalLoginBtn = document.getElementById('modal-login-btn');
        const modalRegisterBtn = document.getElementById('modal-register-btn');
        const loginModal = document.getElementById('login-modal');

        if (modalLoginBtn) {
            modalLoginBtn.addEventListener('click', () => {
                window.location.href = 'pages/login.php';
            });
        }

        if (modalRegisterBtn) {
            modalRegisterBtn.addEventListener('click', () => {
                window.location.href = 'pages/register.php';
            });
        }

        // Optional: close modal on close button click
        const modalClose = loginModal ? loginModal.querySelector('.modal-close') : null;
        if (modalClose) {
            modalClose.addEventListener('click', () => {
                loginModal.classList.add('hidden');
            });
        }
    });
</script>
</body>
</html>
