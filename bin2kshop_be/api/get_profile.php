<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/db.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id > 0) {
    // 1. Lấy thông tin user
    $query = "SELECT id, name, email, role, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 2. Lấy đơn hàng chính xác theo ID người dùng
        $stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt_orders->execute([$user_id]);
        $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "profile" => $user,
            "orders" => $orders
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không tìm thấy người dùng"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID không hợp lệ"]);
}
?>