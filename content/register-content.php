<?php
// blogwebsite/content/register-content.php
// File này được include bởi dangky.php

// Đảm bảo BASE_PATH được định nghĩa nếu không sẽ không có đường dẫn chính xác cho action form
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}
?>

<div class="register-container">
    <h2>Đăng Ký</h2>

    <?php if (!empty($errorMessage)): ?>
    <p class="message error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
    <p class="message success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH; ?>/dangky.php" method="POST">
        <div class="form-group">
            <label for="username">Tên đăng ký:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>"
                placeholder="Nhập tên đăng nhập" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                <span class="password-toggle" onclick="togglePasswordVisibility()">&#128065;</span>
            </div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>"
                placeholder="Nhập địa chỉ email" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng ký</button>
    </form>
    <div class="login-link">
        <p>Đăng nhập nếu có tài khoản? <a href="<?php echo BASE_PATH; ?>/dangnhap.php">Đăng nhập</a></p>
    </div>
</div>

<script>
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