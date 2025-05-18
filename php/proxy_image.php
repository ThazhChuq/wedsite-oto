<?php
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'URL ảnh không được cung cấp.';
    exit;
}

$url = $_GET['url'];

// Validate URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'URL ảnh không hợp lệ.';
    exit;
}

// Get image content
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; ShopOtoBot/1.0)');
$image_data = curl_exec($ch);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || !$image_data) {
    http_response_code(404);
    echo 'Không thể lấy ảnh từ URL.';
    exit;
}

// Set content type header and output image
header('Content-Type: ' . $content_type);
echo $image_data;
?>
