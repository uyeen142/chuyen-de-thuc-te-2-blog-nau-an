<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// Nếu đã đăng nhập, chuyển đến dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        setAlert('danger', 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu');
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu (trong trường hợp đơn giản nhất, password là admin123)
            if ($password === 'admin123') {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                setAlert('danger', 'Tên đăng nhập hoặc mật khẩu không chính xác');
            }
        } else {
            setAlert('danger', 'Tên đăng nhập hoặc mật khẩu không chính xác');
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white mx-auto mb-3" style="width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                        <h2 class="font-weight-bold">Đăng nhập Admin</h2>
                        <p class="text-muted">Nhập thông tin đăng nhập để truy cập vào trang quản trị</p>
                    </div>
                    
                    <?php showAlert(); ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4 text-muted">
                        <small>Tài khoản demo: admin / admin123</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-footer.php'; ?>
