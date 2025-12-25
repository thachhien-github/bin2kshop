<?php
include '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $data['id']);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Đã xóa sản phẩm!"]);
        }
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Lỗi: " . $e->getMessage()]);
    }
}
?>