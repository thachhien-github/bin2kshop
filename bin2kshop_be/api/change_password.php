<?php
header("Content-Type: application/json; charset=UTF-8");

// Đồng bộ cách gọi file db giống create_order.php
include '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

// Kiểm tra dữ liệu đầu vào
if (empty($data->user_id) || empty($data->current_password) || empty($data->new_password)) {
    echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin!"]);
    exit();
}

$user_id = $data->user_id;
$current_password = $data->current_password; 
$new_password = $data->new_password;

try {
    // 1. Lấy mật khẩu đã hash từ DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // 2. Kiểm tra mật khẩu cũ bằng password_verify
    if ($user && password_verify($current_password, $user['password'])) {
        
        // 3. Hash mật khẩu mới
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($updateStmt->execute([$hashed_new_password, $user_id])) {
            echo json_encode(["status" => "success", "message" => "Đổi mật khẩu thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi cập nhật cơ sở dữ liệu!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Mật khẩu hiện tại không chính xác!"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()]);
}
?>