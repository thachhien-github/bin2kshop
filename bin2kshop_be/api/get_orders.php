<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

// Truy vấn lấy tất cả đơn hàng, mới nhất xếp trên đầu
$query = "SELECT * FROM orders ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($orders);
?>