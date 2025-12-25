<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/db.php';

// Nhận dữ liệu từ $_POST
$name = $_POST['name'] ?? null;
$price = $_POST['price'] ?? null;
$stock = $_POST['stock'] ?? 0;
$category = $_POST['category'] ?? null;
$description = $_POST['description'] ?? "";

if ($name && $price && $category && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Tạo tên file duy nhất
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    
    // Đảm bảo thư mục uploads tồn tại
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // CÁCH SỬA QUAN TRỌNG: 
        // Thay vì dùng http://localhost/..., hãy dùng đường dẫn tương đối hoặc domain động
        // Ở đây ta dùng domain động để tự thích ứng với https://bin2kshop.rf.gd
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $imageUrl = $protocol . "://" . $host . "/bin2kshop_be/uploads/" . $fileName;

        try {
            $sql = "INSERT INTO products (name, price, stock, category_slug, image, description) 
                    VALUES (:name, :price, :stock, :category, :image, :description)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name'        => $name,
                ':price'       => $price,
                ':stock'       => $stock,
                ':category'    => $category,
                ':image'       => $imageUrl,
                ':description' => $description
            ]);
            echo json_encode(["status" => "success", "message" => "Thêm sản phẩm thành công!", "image_url" => $imageUrl]);
        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Lỗi Database: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi di chuyển file vào thư mục uploads!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu thông tin hoặc file ảnh!"]);
}
?>