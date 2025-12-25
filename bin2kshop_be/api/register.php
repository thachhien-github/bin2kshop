<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

// Lấy dữ liệu từ React gửi lên
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['name']) && !empty($data['email']) && !empty($data['password'])) {
    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password'];

    // 1. Kiểm tra email đã tồn tại chưa
    $checkEmail = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmtCheck = $conn->prepare($checkEmail);
    $stmtCheck->execute([$email]);

    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email này đã được sử dụng!"]);
        exit;
    }

    // 2. Mã hóa mật khẩu (Password Hash)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Thêm User mới (Mặc định role là 'user')
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$name, $email, $hashed_password])) {
        echo json_encode(["success" => true, "message" => "Đăng ký thành công!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi hệ thống, vui lòng thử lại."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập đầy đủ thông tin."]);
}
?>