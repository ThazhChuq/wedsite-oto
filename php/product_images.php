<?php
require_once 'config.php';

function addProductImages($mysqli, $productId, $imageUrls) {
    $stmt = $mysqli->prepare('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)');
    foreach ($imageUrls as $url) {
        $stmt->bind_param('is', $productId, $url);
        $stmt->execute();
    }
    $stmt->close();
}

function getProductImages($mysqli, $productId) {
    $stmt = $mysqli->prepare('SELECT image_url FROM product_images WHERE product_id = ?');
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_url'];
    }
    $stmt->close();
    return $images;
}
?>
