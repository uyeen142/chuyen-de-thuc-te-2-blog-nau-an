<?php
// BLOGWEBSITE/monan/monan.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

$pageTitle = "Món Ăn | Bếp Anh Tài";

// Cập nhật đường dẫn cho extraCss để trỏ đến vị trí mới trong thư mục assets
$extraCss = BASE_PATH . "/assets/monan.css"; 


$contentPage = __DIR__ . '/../content/monan-content.php'; 

include __DIR__ . '/../layout.php';
?>
