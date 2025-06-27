<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// Xóa người dùng nếu có yêu cầu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setAlert('success', 'Xóa người dùng thành công');
    } else {
        setAlert('danger', 'Đã xảy ra lỗi khi xóa người dùng');
    }
    
    // Chuyển hướng để tránh gửi lại form khi refresh
    header("Location: index.php");
    exit();
}

// Tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $whereClause = "WHERE username LIKE ? OR email LIKE ?";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types = 'ss';
}

// Lấy danh sách người dùng
$query = "SELECT id, username, email, created_at FROM users $whereClause ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="mb-4">
    <a href="/blogwebsite/admin/dashboard.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Quay về Dashboard
    </a>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý người dùng</h1>
    </div>

    <!-- Tìm kiếm -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4">
            <form action="" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách người dùng -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tài khoản</th>
                            <th>Email</th>
                            <th>Ngày đăng ký</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="fw-bold"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($user['username']) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $user['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            <!-- Modal xác nhận xóa -->
                                            <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Xác nhận xóa người dùng</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Bạn có chắc chắn muốn xóa người dùng "<strong><?= htmlspecialchars($user['username']) ?></strong>"?
                                                            <p class="text-danger mt-2 mb-0">Lưu ý: Hành động này không thể hoàn tác.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <a href="index.php?delete=<?= $user['id'] ?>" class="btn btn-danger">Xóa</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <?php if (!empty($search)): ?>
                                        Không tìm thấy người dùng phù hợp với từ khóa tìm kiếm.
                                    <?php else: ?>
                                        Chưa có người dùng nào đăng ký.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-footer.php'; ?>
