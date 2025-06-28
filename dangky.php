<?php

// 1. Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite'); // Thay đổi '/blogwebsite' nếu thư mục gốc của bạn khác
}

// 2. Include file kết nối cơ sở dữ liệu và các hàm chung
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php'; // Đảm bảo file functions.php đã được include

// 3. Định nghĩa các biến cho layout và tiêu đề trang
$pageTitle = "Đăng Ký Tài Khoản | Bếp Anh Tài";
$extraCss = "assets/register.css"; // CSS riêng cho trang đăng ký
$errorMessage = ''; // Biến để lưu thông báo lỗi
$successMessage = ''; // Biến để lưu thông báo thành công

// 4. Xử lý logic khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password) || empty($email)) {
        $errorMessage = "Vui lòng điền đầy đủ tất cả các trường.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Địa chỉ email không hợp lệ.";
    } else {
        // Kiểm tra xem username hoặc email đã tồn tại chưa
        if ($conn) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $username, $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $errorMessage = "Tên đăng nhập hoặc Email đã tồn tại. Vui lòng chọn tên khác.";
                } else {
                    // Hash mật khẩu trước khi lưu vào CSDL để bảo mật
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Chèn dữ liệu người dùng mới vào bảng users
                    $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                    if ($insert_stmt) {
                        $insert_stmt->bind_param("sss", $username, $email, $hashed_password);
                        if ($insert_stmt->execute()) {
                            $successMessage = "Đăng ký tài khoản thành công! Bạn có thể <a href='" . BASE_PATH . "/dangnhap.php'>đăng nhập</a> ngay bây giờ.";
                            // Sau khi đăng ký thành công, có thể xóa dữ liệu trong form
                            $username = '';
                            $email = '';
                        } else {
                            $errorMessage = "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại. Lỗi: " . $insert_stmt->error;
                            error_log("Insert user failed: " . $insert_stmt->error);
                        }
                        $insert_stmt->close();
                    } else {
                        $errorMessage = "Lỗi chuẩn bị truy vấn chèn dữ liệu: " . $conn->error;
                        error_log("Prepare insert statement failed: " . $conn->error);
                    }
                }
                $stmt->close();
            } else {
                $errorMessage = "Lỗi chuẩn bị truy vấn kiểm tra tồn tại: " . $conn->error;
                error_log("Prepare check statement failed: " . $conn->error);
            }
        } else {
            $errorMessage = "Lỗi kết nối cơ sở dữ liệu.";
            error_log("Database connection not established in dangky.php");
        }
    }
}

// 5. Chỉ định file content chung mà layout.php sẽ include
$contentPage = __DIR__ . '/content/register-content.php';

// 6. Include layout.php để hiển thị toàn bộ trang với header, footer và nội dung động
include __DIR__ . '/layout.php';

// Lưu ý: Không đóng thẻ PHP nếu đây là dòng cuối cùng của file
// ?>