<?php
header("Content-Type: application/json; charset=UTF-8");

// Đồng bộ cách gọi file db.php
include '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Vui lòng nhập đầy đủ email và mật khẩu."]);
    exit();
}

$email = $data['email'];
$password = $data['password'];

try {
    // 1. Tìm user theo email
    $query = "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Kiểm tra mật khẩu (Sử dụng cơ chế hash đồng bộ với trang đổi mật khẩu)
    if ($user && password_verify($password, $user['password'])) {
        // Xóa mật khẩu khỏi mảng trước khi trả về FE
        unset($user['password']);
        
        echo json_encode([
            "success" => true, 
            "message" => "Đăng nhập thành công!",
            "user" => $user
        ]);
    } else {
        // Trả về 401 Unauthorized nếu sai thông tin
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Email hoặc mật khẩu không chính xác."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Lỗi hệ thống: " . $e->getMessage()]);
}
?>