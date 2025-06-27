<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/includes/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/includes/functions.php';

$current_uri = $_SERVER['REQUEST_URI'];
$is_login_page = strpos($current_uri, '/admin/index.php') !== false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản trị</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/blogwebsite/assets/admin/css/admin.css">
  <link rel="stylesheet" href="/blogwebsite/assets/admin/css/style.css">
  <style>
    .top-navbar {
        background-color: #ffffff;
        padding: 18px 40px;
        border-bottom: 1px solid #f0f0f0;
        position: sticky;
        top: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .top-navbar .logo-img {
        height: 55px;
    }
    .nav-links a {
        margin-left: 28px;
        font-weight: 600;
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s ease;
        font-size: 16px;
    }
    .nav-links a:hover,
    .nav-links a.active {
        color: #004f9f;
    }
    @media (max-width: 768px) {
        .nav-links {
            display: none;
        }
    }
  </style>
</head>
<body>
<?php
if (!$is_login_page) {
    checkLogin();
?>
    <header class="top-navbar">
        <a href="/blogwebsite/admin/dashboard.php">
            <img src="/blogwebsite/images/logo.png" alt="Logo" class="logo-img">
        </a>
        <nav class="nav-links">
            <a href="/blogwebsite/admin/dashboard.php" class="<?= strpos($current_uri, 'dashboard.php') !== false ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="/blogwebsite/admin/posts/" class="<?= strpos($current_uri, '/admin/posts') !== false ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> Bài viết
            </a>
            <a href="/blogwebsite/admin/users/" class="<?= strpos($current_uri, '/admin/users') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Người dùng
            </a>
            <a href="/blogwebsite/admin/logout.php" class="text-danger">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </nav>
    </header>

    <main class="container py-4">
<?php
}
?>
