<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

// 1. Lấy các tham số từ URL
$category = isset($_GET['category']) ? $_GET['category'] : null;
$min_price = isset($_GET['min']) ? (int)$_GET['min'] : 0;
$max_price = isset($_GET['max']) ? (int)$_GET['max'] : 999999999;

try {
    // 2. Xây dựng câu truy vấn SQL động (Mặc định lọc theo giá)
    $sql = "SELECT * FROM products WHERE price >= ? AND price <= ?";
    $params = [$min_price, $max_price];

    // 3. Nếu có danh mục thì nối thêm điều kiện vào SQL
    if ($category) {
        $sql .= " AND category_slug = ?";
        $params[] = $category;
    }

    // 4. Sắp xếp mới nhất lên đầu
    $sql .= " ORDER BY id DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // 5. Trả về kết quả JSON
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);

} catch (PDOException $e) {
    // Trả về lỗi nếu Database có vấn đề
    echo json_encode(["error" => $e->getMessage()]);
}
?>