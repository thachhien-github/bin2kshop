<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php'; // Đường dẫn tới file kết nối db của bạn

try {
    // Lấy 10 thông báo mới nhất chưa đọc hoặc vừa mới nhận
    $query = "SELECT id, title, message, created_at, is_read 
              FROM notifications 
              ORDER BY created_at DESC 
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $notifications = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifications[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "message" => $row['message'],
            "time" => time_ago($row['created_at']), // Hàm format thời gian bên dưới
            "is_read" => (int)$row['is_read']
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $notifications
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Hàm tính thời gian "cách đây bao lâu"
function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 86400);

    if ($seconds <= 60) return "Vừa xong";
    else if ($minutes <= 60) return "$minutes phút trước";
    else if ($hours <= 24) return "$hours giờ trước";
    else return "$days ngày trước";
}
?>