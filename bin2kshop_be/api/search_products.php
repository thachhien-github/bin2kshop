<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

// Lấy từ khóa từ tham số URL (?keyword=...)
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

if (!empty($keyword)) {
    // Tìm sản phẩm có tên chứa từ khóa (không phân biệt hoa thường)
    $query = "SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute(["%$keyword%"]);
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} else {
    // Nếu không có từ khóa, trả về mảng rỗng
    echo json_encode([]);
}
?>