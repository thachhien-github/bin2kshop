<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->customer_name) && !empty($data->cart)) {
    try {
        $conn->beginTransaction();
    
        // 1. Lưu vào bảng orders (Thêm trường user_id)
        $query = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        // Lấy user_id từ React gửi lên (có thể null nếu khách vãng lai)
        $user_id = isset($data->user_id) ? $data->user_id : null;
        
        $stmt->execute([
            $user_id, 
            $data->customer_name, 
            $data->customer_phone, 
            $data->customer_address, 
            $data->total_price
        ]);
        
        $order_id = $conn->lastInsertId();
    
        // 2. Lưu chi tiết và TRỪ KHO
        $query_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($query_item);
    
        $query_update_stock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt_stock = $conn->prepare($query_update_stock);
    
        foreach($data->cart as $item) {
            $stmt_item->execute([$order_id, $item->id, $item->quantity, $item->price]);
            $stmt_stock->execute([$item->quantity, $item->id, $item->quantity]);
            
            if ($stmt_stock->rowCount() === 0) {
                throw new Exception("Sản phẩm " . $item->name . " đã hết hàng hoặc không đủ số lượng!");
            }
        }

        // --- BẮT ĐẦU THÊM THÔNG BÁO CHO ADMIN ---
        $noti_title = "Đơn hàng mới #" . $order_id;
        $noti_msg = "Khách hàng " . $data->customer_name . " vừa đặt một đơn hàng trị giá " . number_format($data->total_price) . "đ";
        $query_noti = "INSERT INTO notifications (title, message, type) VALUES (?, ?, 'new_order')";
        $stmt_noti = $conn->prepare($query_noti);
        $stmt_noti->execute([$noti_title, $noti_msg]);
        // --- KẾT THÚC THÊM THÔNG BÁO ---

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Đặt hàng thành công!", "order_id" => $order_id]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>