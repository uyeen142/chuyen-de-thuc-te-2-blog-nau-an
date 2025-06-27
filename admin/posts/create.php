<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/blogwebsite/admin/admin-header.php';

// ✅ Xử lý thêm bài viết – CHỈ MỘT LẦN GỌI DUY NHẤT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $overview = trim($_POST['overview'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // Kiểm tra thiếu thông tin
    if (empty($title) || empty($overview) || empty($category)) {
        setAlert('danger', 'Vui lòng nhập đầy đủ thông tin bắt buộc.');
        header('Location: create.php');
        exit();
    }

    // Upload thumbnail
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $thumbnail = uploadImage($_FILES['thumbnail'], 'thumbnails');
        if (!$thumbnail) {
            setAlert('danger', 'Lỗi upload thumbnail.');
            header('Location: create.php');
            exit();
        }
    } else {
        setAlert('danger', 'Vui lòng chọn ảnh thumbnail.');
        header('Location: create.php');
        exit();
    }

    // Thêm vào bảng posts
    $stmt = $conn->prepare("INSERT INTO posts (title, thumbnail, overview, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $thumbnail, $overview, $category);
    if (!$stmt->execute()) {
        setAlert('danger', 'Lỗi khi thêm bài viết: ' . $conn->error);
        header('Location: create.php');
        exit();
    }
    $post_id = $stmt->insert_id;
    $stmt->close();

    // Thêm nguyên liệu
    $ingredient_names = $_POST['ingredient_name'] ?? [];
    $ingredient_amounts = $_POST['ingredient_amount'] ?? [];
    if (!empty($ingredient_names)) {
        $stmt_ing = $conn->prepare("INSERT INTO ingredients (post_id, name, amount) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($ingredient_names); $i++) {
            if (!empty($ingredient_names[$i]) && !empty($ingredient_amounts[$i])) {
                $stmt_ing->bind_param("iss", $post_id, $ingredient_names[$i], $ingredient_amounts[$i]);
                $stmt_ing->execute();
            }
        }
        $stmt_ing->close();
    }

    // Thêm bước thực hiện
    $step_descriptions = $_POST['step_description'] ?? [];
    if (!empty($step_descriptions)) {
        $stmt_step = $conn->prepare("INSERT INTO steps (post_id, description, image, step_order) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < count($step_descriptions); $i++) {
            if (!empty($step_descriptions[$i])) {
                $step_image = '';
                if (isset($_FILES['step_image']['name'][$i]) && $_FILES['step_image']['error'][$i] === 0) {
                    $file = [
                        'name' => $_FILES['step_image']['name'][$i],
                        'type' => $_FILES['step_image']['type'][$i],
                        'tmp_name' => $_FILES['step_image']['tmp_name'][$i],
                        'error' => $_FILES['step_image']['error'][$i],
                        'size' => $_FILES['step_image']['size'][$i]
                    ];
                    $step_image = uploadImage($file, 'steps');
                }
                $step_order = $i + 1;
                $stmt_step->bind_param("issi", $post_id, $step_descriptions[$i], $step_image, $step_order);
                $stmt_step->execute();
            }
        }
        $stmt_step->close();
    }

    // Gửi thông báo đến người dùng
    $users = $conn->query("SELECT id FROM users");
    while ($user = $users->fetch_assoc()) {
        $message = "Bài viết mới: $title";
        $link = "monan/congthuc.php?id=$post_id";
        $notify = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
        $notify->bind_param("iss", $user['id'], $message, $link);
        $notify->execute();
        $notify->close();
    }

    setAlert('success', 'Thêm bài viết thành công!');
    header('Location: index.php');
    exit();
}
?>

<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="/blogwebsite/admin/posts/" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h2 mb-0">Thêm bài đăng mới</h1>
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
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Ảnh minh họa (thumbnail) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" required>
                            <img id="thumbnail-preview" src="" alt="Thumbnail preview" class="form-image-preview mt-2 d-none">
                        </div>
                        
                        <div class="mb-3">
                            <label for="overview" class="form-label">Mô tả tổng quan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="overview" name="overview" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Chọn danh mục</option>
                                <option value="mon_an_man">Món Ăn Mặn</option>
                                <option value="mon_chay">Món Chay</option>
                                <option value="mon_an_vat">Món Ăn Vặt</option>
                                <option value="mon_nuoc">Nước Uống</option>
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
                            <div class="row mb-3 ingredient-row">
                                <div class="col-1 text-center">
                                    <span class="badge bg-secondary">1</span>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="ingredient_name[]" class="form-control" placeholder="Tên nguyên liệu" required>
                                </div>
                                <div class="col-4">
                                    <input type="text" name="ingredient_amount[]" class="form-control" placeholder="Số lượng" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-danger remove-ingredient">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
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
                            <div class="card mb-3 step-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Bước <span class="step-number">1</span></h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-step">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                                        <textarea name="step_description[]" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Hình ảnh minh họa (tùy chọn)</label>
                                        <input type="file" name="step_image[]" class="form-control step-image" accept="image/*">
                                        <img src="" class="form-image-preview mt-2 d-none" style="max-height: 150px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Đăng bài</h5>
                        <hr>
                        
                        <p class="text-muted small">Sau khi điền đầy đủ thông tin, nhấn nút "Đăng bài" để đăng bài viết lên trang chủ.</p>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Đăng bài
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