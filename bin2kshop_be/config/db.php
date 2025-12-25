<?php
// Cấu hình CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// THÔNG TIN CẤU HÌNH TỪ INFINITYFREE
$host = "sql301.infinityfree.com";
$db_name = "if0_40760190_bin2kshop_db"; // ĐÃ SỬA: Thêm tiền tố if0_40760190_
$username = "if0_40760190";
$password = "KJNwXbhvec9";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
    // Không nên echo thành công ở đây để tránh làm hỏng dữ liệu JSON trả về của các file API khác
} catch(PDOException $exception) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Lỗi kết nối: " . $exception->getMessage()]);
    exit;
}
?>