<?php
// blogwebsite/content/auth-login-content.php
// File này được include bởi dangnhap.php

// Đảm bảo BASE_PATH được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}
?>

<div class="login-container">
    <h2>Đăng Nhập</h2>

    <?php if (!empty($errorMessage)): ?>
    <p class="message error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH; ?>/dangnhap.php" method="POST">
        <div class="form-group">
            <label for="username_or_email">Tên đăng nhập:</label>
            <input type="text" id="username_or_email" name="username_or_email"
                value="<?php echo htmlspecialchars($username_or_email ?? ''); ?>"
                placeholder="Nhập tên đăng nhập của bạn" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                <span class="password-toggle" onclick="togglePasswordVisibility()">&#128065;</span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
    </form>
    <div class="register-link">
        <p>Chưa có tài khoản? <a href="<?php echo BASE_PATH; ?>/dangky.php">Đăng ký ngay</a></p>
    </div>
</div>

<script>
// Hàm JavaScript để hiện/ẩn mật khẩu, dùng chung với trang đăng ký
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.innerHTML = '&#128064;'; // Open eye
    } else {
        passwordField.type = 'password';
        toggleIcon.innerHTML = '&#128065;'; // Closed eye
    }
}
</script>