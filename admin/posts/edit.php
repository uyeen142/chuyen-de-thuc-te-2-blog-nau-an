<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// Kiểm tra ID bài viết
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setAlert('danger', 'ID bài viết không hợp lệ');
    header('Location: index.php');
    exit();
}

$post_id = $_GET['id'];
$post = getPostById($conn, $post_id);

if (!$post) {
    setAlert('danger', 'Không tìm thấy bài viết');
    header('Location: index.php');
    exit();
}

// Xử lý cập nhật bài viết
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $overview = $_POST['overview'] ?? '';
    $category = $_POST['category'] ?? 'mon_an_man'; // Thêm biến category

    // Kiểm tra dữ liệu
    if (empty($title)) {
        setAlert('danger', 'Vui lòng nhập tiêu đề bài viết');
    } elseif (empty($overview)) {
        setAlert('danger', 'Vui lòng nhập mô tả tổng quan');
    } else {
        // Upload thumbnail nếu có
        $thumbnail = $post['thumbnail'];
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
            $new_thumbnail = uploadImage($_FILES['thumbnail'], 'thumbnails');
            if ($new_thumbnail) {
                $thumbnail = $new_thumbnail;
            }
        }
        
        // Cập nhật bài viết, bao gồm category
        $stmt = $conn->prepare("UPDATE posts SET title = ?, thumbnail = ?, overview = ?, category = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $thumbnail, $overview, $category, $post_id); // Thêm 's' cho category

        if ($stmt->execute()) {
            // Xóa nguyên liệu cũ
            $conn->query("DELETE FROM ingredients WHERE post_id = $post_id");
            
            // Thêm nguyên liệu mới
            $ingredient_names = $_POST['ingredient_name'] ?? [];
            $ingredient_amounts = $_POST['ingredient_amount'] ?? [];
            
            if (count($ingredient_names) > 0) {
                $stmt = $conn->prepare("INSERT INTO ingredients (post_id, name, amount) VALUES (?, ?, ?)");
                
                for ($i = 0; $i < count($ingredient_names); $i++) {
                    if (!empty($ingredient_names[$i]) && !empty($ingredient_amounts[$i])) {
                        $stmt->bind_param("iss", $post_id, $ingredient_names[$i], $ingredient_amounts[$i]);
                        $stmt->execute();
                    }
                }
            }
            
            // Xóa các bước cũ
            $conn->query("DELETE FROM steps WHERE post_id = $post_id");
            
            // Thêm các bước mới
            $step_descriptions = $_POST['step_description'] ?? [];
            
            if (count($step_descriptions) > 0) {
                $stmt = $conn->prepare("INSERT INTO steps (post_id, description, image, step_order) VALUES (?, ?, ?, ?)");
                
                for ($i = 0; $i < count($step_descriptions); $i++) {
                    if (!empty($step_descriptions[$i])) {
                        // Upload hình ảnh cho bước (nếu có)
                        $step_image = $_POST['existing_step_image'][$i] ?? '';
                        
                        if (isset($_FILES['step_image']['name'][$i]) && !empty($_FILES['step_image']['name'][$i])) {
                            $file = [
                                'name' => $_FILES['step_image']['name'][$i],
                                'type' => $_FILES['step_image']['type'][$i],
                                'tmp_name' => $_FILES['step_image']['tmp_name'][$i],
                                'error' => $_FILES['step_image']['error'][$i],
                                'size' => $_FILES['step_image']['size'][$i]
                            ];
                            
                            if ($file['error'] == 0) {
                                $new_step_image = uploadImage($file, 'steps');
                                if ($new_step_image) {
                                    $step_image = $new_step_image;
                                }
                            }
                        }
                        
                        $step_order = $i + 1;
                        $stmt->bind_param("issi", $post_id, $step_descriptions[$i], $step_image, $step_order);
                        $stmt->execute();
                    }
                }
            }
            
            setAlert('success', 'Cập nhật bài viết thành công');
            header('Location: index.php');
            exit();
        } else {
            setAlert('danger', 'Đã xảy ra lỗi khi cập nhật bài viết');
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="/blogwebsite/admin/posts/" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h2 mb-0">Chỉnh sửa bài đăng</h1>
    </div>
    
    <?php showAlert(); ?>
    
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin cơ bản</h5>
                        <hr>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề bài đăng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Ảnh minh họa (thumbnail)</label>
                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                            <small class="text-muted">Để trống nếu không muốn thay đổi ảnh thumbnail</small>
                            <div class="mt-2">
                                <img src="<?= htmlspecialchars($post['thumbnail']) ?>" alt="Current thumbnail" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="overview" class="form-label">Mô tả tổng quan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="overview" name="overview" rows="4" required><?= htmlspecialchars($post['overview']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Chọn danh mục</option>
                                <option value="mon_an_man" <?= ($post['category'] == 'mon_an_man') ? 'selected' : '' ?>>Món Ăn Mặn</option>
                                <option value="mon_chay" <?= ($post['category'] == 'mon_chay') ? 'selected' : '' ?>>Món Chay</option>
                                <option value="mon_an_vat" <?= ($post['category'] == 'mon_an_vat') ? 'selected' : '' ?>>Món Ăn Vặt</option>
                                <option value="mon_nuoc" <?= ($post['category'] == 'mon_nuoc') ? 'selected' : '' ?>>Nước Uống</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Nguyên liệu</h5>
                            <button type="button" id="add-ingredient" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus-circle me-1"></i> Thêm nguyên liệu
                            </button>
                        </div>
                        <hr>
                        
                        <div id="ingredients-container">
                            <?php foreach ($post['ingredients'] as $index => $ingredient): ?>
                                <div class="row mb-3 ingredient-row">
                                    <div class="col-1 text-center">
                                        <span class="badge bg-secondary"><?= $index + 1 ?></span>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="ingredient_name[]" class="form-control" placeholder="Tên nguyên liệu" value="<?= htmlspecialchars($ingredient['name']) ?>" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="text" name="ingredient_amount[]" class="form-control" placeholder="Số lượng" value="<?= htmlspecialchars($ingredient['amount']) ?>" required>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-outline-danger remove-ingredient">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Các bước thực hiện</h5>
                            <button type="button" id="add-step" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus-circle me-1"></i> Thêm bước
                            </button>
                        </div>
                        <hr>
                        
                        <div id="steps-container">
                            <?php foreach ($post['steps'] as $index => $step): ?>
                                <div class="card mb-3 step-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Bước <span class="step-number"><?= $index + 1 ?></span></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-step">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                                            <textarea name="step_description[]" class="form-control" rows="3" required><?= htmlspecialchars($step['description']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hình ảnh minh họa (tùy chọn)</label>
                                            <input type="file" name="step_image[]" class="form-control step-image" accept="image/*">
                                            <input type="hidden" name="existing_step_image[]" value="<?= htmlspecialchars($step['image'] ?? '') ?>">
                                            <?php if (!empty($step['image'])): ?>
                                                <div class="mt-2">
                                                    <img src="<?= htmlspecialchars($step['image']) ?>" alt="Minh họa bước <?= $index + 1 ?>" class="img-thumbnail" style="max-height: 150px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Cập nhật bài viết</h5>
                        <hr>
                        
                        <p class="text-muted small">Sau khi chỉnh sửa thông tin, nhấn nút "Cập nhật" để lưu thay đổi.</p>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Cập nhật
                            </button>
                            <a href="/blogwebsite/admin/posts/" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-footer.php'; ?>