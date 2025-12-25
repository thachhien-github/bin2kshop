<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

// Hỗ trợ cả phương thức GET (truyền id qua URL) hoặc POST (truyền JSON)
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    $data = json_decode(file_get_contents("php://input"));
    $id = isset($data->id) ? $data->id : null;
}

if ($id) {
    try {
        // Cập nhật thông báo cụ thể hoặc cập nhật tất cả nếu id = 'all'
        if ($id === 'all') {
            $query = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
            $stmt = $conn->prepare($query);
            $stmt->execute();
        } else {
            $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
        }

        echo json_encode([
            "status" => "success",
            "message" => "Đã đánh dấu thông báo là đã đọc"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Lỗi: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu ID thông báo"
    ]);
}
?>