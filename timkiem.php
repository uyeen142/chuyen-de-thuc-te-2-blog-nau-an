<?php
// blogwebsite/timkiem/timkiem.php

// 1. Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

// Bắt đầu session (nếu cần cho các tính năng tương lai như yêu thích)
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// 2. Include file kết nối cơ sở dữ liệu và các hàm chung
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// 3. Định nghĩa các biến cho layout và tiêu đề trang
$pageTitle = "Kết Quả Tìm Kiếm | Bếp Anh Tài";
$extraCss = "monan/common-monan.css"; // Có thể dùng lại CSS của trang danh mục

$search_query = trim($_GET['q'] ?? ''); // Lấy từ khóa tìm kiếm

$searchResults = []; // Mảng để lưu trữ kết quả tìm kiếm

if ($conn) {
    if (!empty($search_query)) {
        // Truy vấn tìm kiếm bài viết trong bảng 'posts'
        // Tìm kiếm trong tiêu đề và tổng quan
        $query = "SELECT id, title, thumbnail, overview FROM posts WHERE title LIKE ? OR overview LIKE ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $search_param = "%" . $search_query . "%";
            $stmt->bind_param("ss", $search_param, $search_param);
            $stmt->execute();
            $result = $stmt->get_result();

            // Lấy user ID nếu đã đăng nhập để kiểm tra trạng thái yêu thích
            $current_user_id = $_SESSION['user_id'] ?? null;

            while ($post = $result->fetch_assoc()) {
                $is_liked = false;
                if ($current_user_id) {
                    // Kiểm tra trạng thái yêu thích
                    $like_check_stmt = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
                    if ($like_check_stmt) {
                        $like_check_stmt->bind_param("ii", $current_user_id, $post['id']);
                        $like_check_stmt->execute();
                        $like_check_stmt->store_result();
                        if ($like_check_stmt->num_rows > 0) {
                            $is_liked = true;
                        }
                        $like_check_stmt->close();
                    }
                }
                $searchResults[] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'thumbnail' => $post['thumbnail'],
                    'overview' => $post['overview'],
                    'is_liked' => $is_liked,
                ];
            }
            $stmt->close();
        } else {
            error_log("Prepare statement failed in timkiem.php: " . $conn->error);
        }
    }
} else {
    error_log("Database connection is not established in timkiem.php");
}

// 4. Chỉ định file content chung mà layout.php sẽ include
// Chúng ta sẽ tạo một file mới để hiển thị kết quả tìm kiếm, nhưng có thể tái sử dụng cấu trúc của monan-category-content.php
$contentPage = __DIR__ . '/../content/search-results-content.php';

// Các biến cần thiết cho search-results-content.php
$categoryTitle = "Kết Quả Tìm Kiếm cho: '" . htmlspecialchars($search_query) . "'";
$categoryDescription = empty($searchResults) ? "Không tìm thấy kết quả nào." : "Tìm thấy " . count($searchResults) . " kết quả.";
$dishesData = $searchResults; // Đổi tên biến để khớp với template

// 5. Include layout.php để hiển thị toàn bộ trang với header, footer và nội dung động
include __DIR__ . '/../layout.php';

// Lưu ý: Không đóng thẻ PHP nếu đây là dòng cuối cùng của file
// ?>