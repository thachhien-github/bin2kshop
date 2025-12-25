<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/db.php';

// Lấy tổng doanh thu theo ngày trong 7 ngày gần nhất
$query = "SELECT DATE(created_at) as date, SUM(total_price) as total 
          FROM orders 
          WHERE status = 'Completed' 
          GROUP BY DATE(created_at) 
          ORDER BY date ASC 
          LIMIT 7";

$stmt = $conn->prepare($query);
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($stats);
?>