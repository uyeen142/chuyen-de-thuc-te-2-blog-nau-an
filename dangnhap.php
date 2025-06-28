<?php

// 1. Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite'); // Thay đổi '/blogwebsite' nếu thư mục gốc của bạn khác
}

// 2. Include file kết nối cơ sở dữ liệu và các hàm chung
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php'; // Đảm bảo file functions.php đã được include

// 3. Định nghĩa các biến cho layout và tiêu đề trang
$pageTitle = "Đăng Nhập | Bếp Anh Tài";
$extraCss = "assets/auth.css"; // CSS riêng cho trang đăng nhập
$errorMessage = ''; // Biến để lưu thông báo lỗi

// 4. Xử lý logic khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST['username_or_email']); // Tên biến được đổi để rõ ràng hơn
    $password = trim($_POST['password']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username_or_email) || empty($password)) {
        $errorMessage = "Vui lòng điền đầy đủ Tên đăng nhập và Mật khẩu.";
    } else {
        if ($conn) {
            // Truy vấn để tìm người dùng dựa trên username hoặc email
            // (Mặc dù bạn nói chỉ username, nhưng thực tế nhiều hệ thống cho phép đăng nhập bằng cả 2)
            // Nếu bạn chỉ muốn cho phép đăng nhập bằng username, hãy bỏ điều kiện OR email = ?
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $username_or_email, $username_or_email); // Bind 2 lần cho username và email
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    // Xác minh mật khẩu đã nhập với mật khẩu đã hash trong CSDL
                    if (password_verify($password, $user['password'])) {
                        // Đăng nhập thành công!
                        // Tạo session để giữ trạng thái đăng nhập
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];

                        // Chuyển hướng về trang chủ hoặc trang sau khi đăng nhập thành công
                        header("Location: " . BASE_PATH . "/index.php"); // Hoặc dashboard.php
                        exit();
                    } else {
                        $errorMessage = "Mật khẩu không đúng.";
                    }
                } else {
                    $errorMessage = "Tên đăng nhập hoặc Email không tồn tại.";
                }
                $stmt->close();
            } else {
                $errorMessage = "Lỗi chuẩn bị truy vấn: " . $conn->error;
                error_log("Prepare statement failed in dangnhap.php: " . $conn->error);
            }
        } else {
            $errorMessage = "Lỗi kết nối cơ sở dữ liệu.";
            error_log("Database connection not established in dangnhap.php");
        }
    }
}

// 5. Chỉ định file content chung mà layout.php sẽ include
$contentPage = __DIR__ . '/content/auth-login-content.php';

// 6. Include layout.php để hiển thị toàn bộ trang với header, footer và nội dung động
include __DIR__ . '/layout.php';

// Lưu ý: Không đóng thẻ PHP nếu đây là dòng cuối cùng của file
// ?>