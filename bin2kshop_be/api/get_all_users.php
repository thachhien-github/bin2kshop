<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

try {
    // Truy vấn lấy danh sách user, sắp xếp người mới nhất lên đầu
    // Không lấy trường password để bảo mật
    $query = "SELECT id, name, email, role, created_at 
              FROM users 
              ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        // Trả về danh sách dưới dạng mảng JSON
        echo json_encode($users);
    } else {
        // Nếu chưa có user nào (trường hợp hiếm)
        echo json_encode([]);
    }

} catch (PDOException $e) {
    // Trả về lỗi nếu có vấn đề về SQL
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage()
    ]);
}
?>