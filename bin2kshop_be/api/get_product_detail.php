<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $query = "SELECT id, name, price, stock, image, description, isNew, created_at FROM products WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(["message" => "Không tìm thấy sản phẩm"]);
    }
}
?>