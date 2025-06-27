<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/includes/config.php';

// Xóa session
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: index.php");
exit();
?>
