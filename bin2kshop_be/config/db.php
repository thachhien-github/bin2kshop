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
$db_name = "if0_40760190_bin2kshop_db"; 
$username = "if0_40760190";
$password = "KJNwXbhvec9"; // Hãy đảm bảo mật khẩu vPanel này chính xác

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
    
    // FIX DỨT ĐIỂM LỖI "3 GIỜ TRƯỚC"
    // Thiết lập múi giờ cho MySQL là Việt Nam (+7)
    $conn->exec("SET time_zone = '+07:00'");
    
    // Thiết lập múi giờ cho PHP (để hàm time() trong time_ago chạy đúng)
    date_default_timezone_set('Asia/Ho_Chi_Minh');

} catch(PDOException $exception) {
    header('Content-Type: application/json');
    // Trả về JSON chuẩn để React không bị lỗi cú pháp khi parse
    echo json_encode([
        "status" => "error", 
        "message" => "Lỗi kết nối DB: " . $exception->getMessage()
    ]);
    exit;
}
// KHÔNG DÙNG THẺ ĐÓNG ?>