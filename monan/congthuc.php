<?php
// /blogwebsite/monan/congthuc.php - Controller cho trang chi tiết công thức

// Đảm bảo BASE_PATH đã được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

// 1. Include file cấu hình và kết nối database
require_once __DIR__ . '/../includes/config.php';

$pageTitle = "Chi tiết công thức | Bếp Anh Tài";
$extraCss = BASE_PATH . "/assets/congthuc.css"; // CSS riêng cho trang chi tiết công thức

$recipe = null;
$ingredients = [];
$steps = [];

// 2. Lấy ID của bài viết từ URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = (int)$_GET['id']; // Ép kiểu sang số nguyên để đảm bảo an toàn

    // 3. Truy vấn cơ sở dữ liệu để lấy thông tin bài viết (công thức)
    // RẤT QUAN TRỌNG: Đã BỎ cột 'content' khỏi truy vấn vì nó KHÔNG CÓ TRONG BẢNG 'posts' của bạn
    $stmt = $conn->prepare("SELECT id, title, thumbnail, overview, created_at, updated_at FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $recipe = $result->fetch_assoc();

        // 4. Lấy danh sách nguyên liệu liên quan
        // Dựa trên blog_nau_an.sql, bảng `ingredients` có cột `name` và `amount`.
        $stmt_ing = $conn->prepare("SELECT name, amount FROM ingredients WHERE post_id = ? ORDER BY id ASC");
        $stmt_ing->bind_param("i", $post_id);
        $stmt_ing->execute();
        $res_ing = $stmt_ing->get_result();
        while ($row = $res_ing->fetch_assoc()) {
            // Kết hợp tên và số lượng thành một chuỗi (ví dụ: "Đường: 100g")
            $ingredients[] = htmlspecialchars($row['name']) . (!empty($row['amount']) ? ': ' . htmlspecialchars($row['amount']) : '');
        }
        $stmt_ing->close(); // Đóng statement

        // 5. Lấy danh sách các bước thực hiện liên quan
        // Bảng `steps` có cột `description`, `step_order`, `image`.
        $stmt_steps = $conn->prepare("SELECT step_order, description, image FROM steps WHERE post_id = ? ORDER BY step_order ASC");
        $stmt_steps->bind_param("i", $post_id);
        $stmt_steps->execute();
        $res_steps = $stmt_steps->get_result();
        while ($row = $res_steps->fetch_assoc()) {
            $steps[] = [
                'order' => $row['step_order'],
                'description' => htmlspecialchars($row['description']),
                'image' => !empty($row['image']) ? htmlspecialchars($row['image']) : null // Thêm cột ảnh cho bước nếu có
            ];
        }
        $stmt_steps->close(); // Đóng statement

    } else {
        // Bài viết không tồn tại, chuyển hướng đến trang 404 (hoặc trang tổng quan)
        header("Location: " . BASE_PATH . "/404.php"); // Bạn cần tạo file 404.php
        exit();
    }
    // Đóng statement chính (cho posts) sau khi sử dụng
    $stmt->close();
} else {
    // Không có ID bài viết được cung cấp, chuyển hướng về trang tổng quan món ăn
    header("Location: " . BASE_PATH . "/monan/monan.php");
    exit();
}

// Đóng kết nối DB sau khi hoàn tất tất cả các truy vấn cần thiết
$conn->close();

// 6. Chỉ định file nội dung chính cho trang này
$contentPage = __DIR__ . '/../content/congthuc-chi-tiet.php';

// 7. Include layout.php để hiển thị toàn bộ trang
include __DIR__ . '/../layout.php';
?>