<?php
// index.php (Nằm ở thư mục gốc BLOGWEBSITE/)

// 1. Định nghĩa các biến cần thiết cho layout.php
$pageTitle = "Trang chủ - Bếp Anh Tài"; // Tiêu đề hiển thị trên tab trình duyệt
$extraCss = "assets/homepage.css";      // CSS riêng cho bố cục nội dung trang chủ
// Không cần liên kết đến assets/pagination.css nữa

//Tìm kiếm
$searchQuery = $_GET['q'] ?? '';

// 2. Chỉ định file nội dung chính của trang này.
// Đường dẫn từ index.php đến content/trang-chu-content.php
$contentPage = 'content/trang-chu-content.php'; 

// 3. Cuối cùng, include file layout.php để hiển thị toàn bộ trang
include 'layout.php';
?>