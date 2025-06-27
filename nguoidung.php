<?php
// blogwebsite/nguoidung/nguoidung.php

// 1. Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

// Bắt đầu session
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
    header("Location: " . BASE_PATH . "/auth/dangnhap.php");
    exit();
}

// 2. Include file kết nối cơ sở dữ liệu và các hàm chung
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// 3. Định nghĩa các biến cho layout và tiêu đề trang
$pageTitle = "Thông Tin Tài Khoản | Bếp Anh Tài";
$extraCss = "nguoidung/profile.css"; // CSS riêng cho trang cá nhân (sẽ tạo ở bước 3)

$user_id = $_SESSION['user_id'];
$userData = [];
$likedPostsCount = 0;

if ($conn) {
    // Lấy thông tin người dùng từ bảng 'users'
    $user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    if ($user_stmt) {
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($user_result->num_rows == 1) {
            $userData = $user_result->fetch_assoc();
        }
        $user_stmt->close();
    } else {
        error_log("Prepare user info statement failed: " . $conn->error);
    }

    // Đếm số bài viết mà người dùng đã thích
    $likes_count_stmt = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE user_id = ?");
    if ($likes_count_stmt) {
        $likes_count_stmt->bind_param("i", $user_id);
        $likes_count_stmt->execute();
        $likes_count_result = $likes_count_stmt->get_result();
        $row = $likes_count_result->fetch_assoc();
        $likedPostsCount = $row['total_likes'];
        $likes_count_stmt->close();
    } else {
        error_log("Prepare likes count statement failed: " . $conn->error);
    }

} else {
    error_log("Database connection not established in nguoidung.php");
}

// 4. Chỉ định file content chung mà layout.php sẽ include
$contentPage = __DIR__ . '/../content/user-profile-content.php';

// 5. Include layout.php để hiển thị toàn bộ trang với header, footer và nội dung động
include __DIR__ . '/../layout.php';
// ?>