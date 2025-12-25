<?php
include '../config/db.php';

// Nhận dữ liệu từ $_POST (FormData gửi qua)
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$price = $_POST['price'] ?? null;
$stock = $_POST['stock'] ?? 0;
$category = $_POST['category'] ?? null;
$description = $_POST['description'] ?? "";

if ($id && $name && $price && $category) {
    try {
        // 1. Lấy thông tin sản phẩm cũ để biết đường dẫn ảnh hiện tại
        $stmt_old = $conn->prepare("SELECT image FROM products WHERE id = :id");
        $stmt_old->execute([':id' => $id]);
        $old_product = $stmt_old->fetch(PDO::FETCH_ASSOC);
        $imageUrl = $old_product['image']; // Mặc định giữ ảnh cũ

        // 2. Kiểm tra nếu có upload file mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file = $_FILES['image'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = '../uploads/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $imageUrl = "http://localhost/bin2kshop-project/bin2kshop_be/uploads/" . $fileName;
                
            }
        }

        // 3. Tiến hành cập nhật Database
        $sql = "UPDATE products 
                SET name = :name, 
                    price = :price, 
                    stock = :stock,
                    category_slug = :category, 
                    image = :image,
                    description = :description
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id'           => $id,
            ':name'         => $name,
            ':price'        => $price,
            ':stock'        => $stock,
            ':category'     => $category,
            ':image'        => $imageUrl,
            ':description'  => $description
        ]);

        echo json_encode(["status" => "success", "message" => "Cập nhật sản phẩm thành công!"]);
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Lỗi Database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu cập nhật!"]);
}
?>