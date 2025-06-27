<?php
// BLOGWEBSITE/monan/chay.php

// 1. Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

// 2. Include file kết nối cơ sở dữ liệu và các hàm chung
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php'; // DÒNG NÀY ĐÃ ĐƯỢC THÊM VÀO HOẶC ĐẢM BẢO KHÔNG BỊ COMMENT
// 3. Định nghĩa các biến cho layout và tiêu đề trang
$pageTitle = "Món Ăn Chay | Bếp Anh Tài";
$extraCss = BASE_PATH . "/assets/common-monan.css";

// 4. XÓA BỎ $extraHeadContent: Như đã giải thích trước đó, phần này không cần thiết ở đây.
/*
$extraHeadContent = '
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
    <script>
        window.addEventListener("DOMContentLoaded", () => {
            const logoImg = document.querySelector(".header-left .logo-img");
            if (logoImg) logoImg.src = "images/logo.png";

            const fbImg = document.querySelector("footer .social-icons img[alt=\'Facebook\']");
            const ttImg = document.querySelector("footer .social-icons img[alt=\'TikTok\']");
            if (fbImg) fbImg.src = "images/logoface.png";
            if (ttImg) ttImg.src = "images/logotiktok.png";
        });
    </script>
';
*/

// 5. Dữ liệu cụ thể cho trang danh mục này
$categoryTitle = "Món Ăn Chay";
$categoryDescription = "Khám phá những món chay thanh đạm, tốt cho sức khỏe và vẫn đầy đủ hương vị, phù hợp cho mọi bữa ăn.";
$categorySlug = 'mon_chay'; // Giữ nguyên 'mon_chay' vì CSDL của bạn đang dùng nó

$dishesData = []; // Khởi tạo mảng rỗng để chứa dữ liệu từ DB

// 6. Truy vấn cơ sở dữ liệu để lấy các bài viết thuộc danh mục này
if ($conn) { // Đảm bảo kết nối CSDL thành công
    $search = $_GET['q'] ?? ''; // Lấy từ khóa tìm kiếm nếu có
    $query = "SELECT id, title, thumbnail, overview FROM posts WHERE category = ?";
    $params = [$categorySlug];
    $types = 's';

    if (!empty($search)) {
        $query .= " AND title LIKE ?";
        $params[] = "%" . $search . "%";
        $types .= 's';
    }
    
    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts_for_category = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 7. Chuyển đổi dữ liệu từ DB sang định dạng mà monan-category-content.php mong đợi
        foreach ($posts_for_category as $post) {
            $dishesData[] = [
                'id' => $post['id'],
                'title' => $post['title'],
                'thumbnail' => $post['thumbnail'], // ĐÃ SỬA: Đảm bảo key là 'thumbnail'
                'overview' => $post['overview'],   // ĐÃ SỬA: Truyền overview nguyên bản
            ];
        }
    } else {
        error_log("Prepare statement failed: " . $conn->error);
    }
} else {
    error_log("Database connection is not established in chay.php");
}

// PHÂN TRANG
$dishesData = paginateArray($dishesData, 6);

// 8. Chỉ định file content chung mà layout.php sẽ include
$contentPage = __DIR__ . '/../content/monan-category-content.php';
$favorited_posts = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $favStmt = $conn->prepare("SELECT post_id FROM favorite_posts WHERE user_id = ?");
    $favStmt->bind_param("i", $userId);
    $favStmt->execute();
    $favResult = $favStmt->get_result();
    while ($favRow = $favResult->fetch_assoc()) {
        $favorited_posts[] = $favRow['post_id'];
    }
    $favStmt->close();
}

// 9. Include layout.php để hiển thị toàn bộ trang với header, footer và nội dung động
include __DIR__ . '/../layout.php';
?>