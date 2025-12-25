<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : die();

// Lấy thông tin sản phẩm bằng cách JOIN bảng order_items với bảng products
$query = "SELECT oi.*, p.name, p.image 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.id 
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
?>