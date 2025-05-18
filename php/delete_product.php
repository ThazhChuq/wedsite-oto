<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id || !is_numeric($product_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid product ID']);
        exit;
    }

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection error']);
        exit;
    }

    // Delete additional images
    $stmt = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();

    // Delete product
    $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['success' => 'Product deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete product']);
    }
    $stmt->close();
    $mysqli->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
