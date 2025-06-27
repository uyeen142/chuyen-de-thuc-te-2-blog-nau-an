<?php
// Kiểm tra đăng nhập admin
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Chuyển hướng nếu chưa đăng nhập
function checkLogin() {
    if (!isLoggedIn()) {
        header("Location: /blogwebsite/admin/index.php");
        exit();
    }
}

// Upload hình ảnh
function uploadImage($file, $folder = 'general') {
    if (!$file['name']) return false;
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/blogwebsite/uploads/" . $folder . "/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Kiểm tra định dạng file
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "/blogwebsite/uploads/" . $folder . "/" . $filename;
    } else {
        return false;
    }
}

// Hiển thị thông báo
function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Hiển thị thông báo và xóa
function showAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $type = $alert['type'] == 'success' ? 'success' : 'danger';
        
        echo '<div class="alert alert-'. $type .' alert-dismissible fade show" role="alert">';
        echo $alert['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['alert']);
    }
}

// Lấy thông tin bài viết theo ID
function getPostById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) return null;
    
    $post = $result->fetch_assoc();
    
    // Lấy nguyên liệu
    $stmt = $conn->prepare("SELECT * FROM ingredients WHERE post_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $post['ingredients'] = $ingredients;
    
    // Lấy các bước
    $stmt = $conn->prepare("SELECT * FROM steps WHERE post_id = ? ORDER BY step_order ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $steps = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $post['steps'] = $steps;
    
    return $post;
}
// Hàm cắt ngắn văn bản (được sử dụng trong monan-category-content.php)
if (!function_exists('truncateText')) {
    function truncateText($text, $maxLength) {
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength);
            // Tìm vị trí của khoảng trắng cuối cùng để không cắt ngang từ
            $text = substr($text, 0, strrpos($text, ' ')) . '...';
        }
        return $text;
    }
}

// Hàm kiểm tra người dùng đã đăng nhập chưa (được sử dụng trong scripts.js cho nút yêu thích)
// Giả định bạn lưu user_id vào session khi người dùng đăng nhập
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        // Đảm bảo session đã được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); 
    }
}

if (!function_exists('paginateArray')) {
    function paginateArray(array $data, int $perPage = 6): array {
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalDishes = count($data);
        $totalPages = (int)ceil($totalDishes / $perPage);
        $startIndex = ($currentPage - 1) * $perPage;
        $pagedData = array_slice($data, $startIndex, $perPage);

        // Gán vào biến toàn cục để pagination.php có thể dùng được
        $GLOBALS['currentPage'] = $currentPage;
        $GLOBALS['totalPages'] = $totalPages;

        return $pagedData;
    }
}

// Lưu ý: Hàm setAlert và showAlert của bạn đã có sẵn, không cần thêm lại.
// Hàm getAndClearAlert tôi từng gợi ý là phiên bản khác của showAlert,
// nhưng vì bạn đã có showAlert rồi nên không cần thiết phải thêm getAndClearAlert.

?>
