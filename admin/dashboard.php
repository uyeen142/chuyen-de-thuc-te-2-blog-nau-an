<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// Đếm số bài viết
$posts_query = $conn->query("SELECT COUNT(id) as total_posts FROM posts");
$posts_count = $posts_query->fetch_assoc()['total_posts'];

// Đếm số người dùng
$users_query = $conn->query("SELECT COUNT(id) as total_users FROM users");
$users_count = $users_query->fetch_assoc()['total_users'];

// Lấy bài viết mới nhất
$latest_posts_query = $conn->query("SELECT id, title, thumbnail, created_at FROM posts ORDER BY created_at DESC LIMIT 5");
$latest_posts = $latest_posts_query->fetch_all(MYSQLI_ASSOC);

// Lấy người dùng mới đăng ký
$latest_users_query = $conn->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$latest_users = $latest_users_query->fetch_all(MYSQLI_ASSOC);
?>

<div class="text-center my-5">
    <div class="p-4 rounded-4 shadow-sm border border-2 border-danger-subtle d-inline-block" style="background-color: #fff5f5; max-width: 500px;">
        <h2 class="fw-bold mb-2" style="color: #B81B1B;">
            👋 Xin chào, Admin!
        </h2>
        <p class="mb-0 text-secondary">
            Một ngày tuyệt vời và quản trị hệ thống thật hiệu quả nhé! ✨
        </p>
    </div>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <a href="/blogwebsite/admin/posts/create.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm bài đăng mới
        </a>
    </div>

    <!-- Thống kê -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng số bài viết</h6>
                            <h2 class="fw-bold"><?= $posts_count ?></h2>
                        </div>
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng số người dùng</h6>
                            <h2 class="fw-bold"><?= $users_count ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Trung bình bước nấu ăn</h6>
                            <?php
                            $avg_query = $conn->query("SELECT AVG(step_count) as avg_steps FROM (SELECT post_id, COUNT(*) as step_count FROM steps GROUP BY post_id) as step_counts");
                            $avg_steps = $avg_query->fetch_assoc()['avg_steps'] ?? 0;
                            ?>
                            <h2 class="fw-bold"><?= number_format($avg_steps, 1) ?></h2>
                        </div>
                        <i class="fas fa-chart-bar fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bài viết gần đây -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Bài viết gần đây</h5>
                </div>
                <div class="card-body">
                    <?php if (count($latest_posts) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($latest_posts as $post): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <img src="<?= htmlspecialchars($post['thumbnail']) ?>" class="img-thumbnail" alt="<?= htmlspecialchars($post['title']) ?>" style="width: 60px; height: 60px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?= htmlspecialchars($post['title']) ?></h6>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($post['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="/blogwebsite/admin/posts/" class="text-decoration-none">Xem tất cả bài viết</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có bài viết nào</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Người dùng mới đăng ký -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Người dùng mới đăng ký</h5>
                </div>
                <div class="card-body">
                    <?php if (count($latest_users) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($latest_users as $user): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="fw-bold"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?= htmlspecialchars($user['username']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                        </div>
                                        <div>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="/blogwebsite/admin/users/" class="text-decoration-none">Xem tất cả người dùng</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có người dùng nào đăng ký</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-footer.php'; ?>
