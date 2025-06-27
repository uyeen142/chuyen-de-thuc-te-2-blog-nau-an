<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// Xóa bài viết nếu có yêu cầu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];

    // 🔥 Xóa các thông báo liên quan đến bài viết
    $link = "monan/congthuc.php?id=$id";
    $delNoti = $conn->prepare("DELETE FROM notifications WHERE link = ?");
    $delNoti->bind_param("s", $link);
    $delNoti->execute();
    $delNoti->close();

    // ✅ Xóa bài viết
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        setAlert('success', 'Xóa bài viết thành công (bao gồm thông báo liên quan)');
    } else {
        setAlert('danger', 'Đã xảy ra lỗi khi xóa bài viết');
    }

    // 👉 Chuyển hướng để tránh gửi lại form khi refresh
    header("Location: index.php");
    exit();
}

// Tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $whereClause = "WHERE title LIKE ?";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $types = 's';
}

// Lấy danh sách bài viết
$query = "SELECT id, title, thumbnail, overview, created_at, updated_at FROM posts $whereClause ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="mb-4">
    <a href="/blogwebsite/admin/dashboard.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Quay về Dashboard
    </a>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý bài viết</h1>
        <a href="/blogwebsite/admin/posts/create.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm bài đăng mới
        </a>
    </div>

    <!-- Tìm kiếm -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4">
            <form action="" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm bài viết..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách bài viết -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Ngày tạo</th>
                            <th>Cập nhật</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($posts) > 0): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($post['thumbnail']) ?>" class="img-thumbnail" alt="<?= htmlspecialchars($post['title']) ?>">
                                    </td>
                                    <td>
                                        <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 300px;"><?= htmlspecialchars($post['overview']) ?></p>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($post['updated_at'])) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="/blogwebsite/admin/posts/edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $post['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            <!-- Modal xác nhận xóa -->
                                            <div class="modal fade" id="deleteModal<?= $post['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Xác nhận xóa</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Bạn có chắc chắn muốn xóa bài viết "<strong><?= htmlspecialchars($post['title']) ?></strong>"?
                                                            <p class="text-danger mt-2 mb-0">Lưu ý: Hành động này không thể hoàn tác.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <a href="index.php?delete=<?= $post['id'] ?>" class="btn btn-danger">Xóa</a>
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
                                <td colspan="5" class="text-center py-4">
                                    <?php if (!empty($search)): ?>
                                        Không tìm thấy bài viết phù hợp với từ khóa tìm kiếm.
                                    <?php else: ?>
                                        Chưa có bài viết nào. Hãy thêm bài viết mới!
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
