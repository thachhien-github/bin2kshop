<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->order_id) && !empty($data->status)) {
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if($stmt->execute([$data->status, $data->order_id])) {
        echo json_encode(["message" => "Cập nhật trạng thái thành công!"]);
    } else {
        echo json_encode(["message" => "Không thể cập nhật trạng thái."]);
    }
} else {
    echo json_encode(["message" => "Dữ liệu không đầy đủ."]);
}
?>