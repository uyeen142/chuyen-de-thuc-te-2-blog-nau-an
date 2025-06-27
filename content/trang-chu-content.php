<?php
require_once __DIR__ . '/../includes/config.php';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$posts_per_page_initial = 6;
$show_all_posts = isset($_GET['show_all']) && $_GET['show_all'] === 'true';

// Lấy tổng số bài viết
if ($search !== '') {
    $total_posts_sql = "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ?";
    $stmtCount = $conn->prepare($total_posts_sql);
    $like = "%{$search}%";
    $stmtCount->bind_param('s', $like);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $total_posts_row = $resultCount->fetch_assoc();
    $total_posts = $total_posts_row['total'] ?? 0;
    $stmtCount->close();
} else {
    $total_posts_sql = "SELECT COUNT(*) AS total FROM posts";
    $total_posts_result = $conn->query($total_posts_sql);
    $total_posts_row = $total_posts_result->fetch_assoc();
    $total_posts = $total_posts_row['total'] ?? 0;
}

// Truy vấn SQL để lấy bài viết
$limit_clause = $show_all_posts ? "" : "LIMIT ?";
if ($search !== '') {
    $sql = "SELECT id, title, thumbnail, overview FROM posts WHERE title LIKE ? ORDER BY id DESC $limit_clause";
    $params = [$like];
    $types = "s";
    if (!$show_all_posts) {
        $params[] = $posts_per_page_initial;
        $types .= "i";
    }
} else {
    $sql = "SELECT id, title, thumbnail, overview FROM posts ORDER BY id DESC $limit_clause";
    $params = [];
    $types = "";
    if (!$show_all_posts) {
        $params[] = $posts_per_page_initial;
        $types .= "i";
    }
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh sách bài đã yêu thích
$favorited_posts = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $favStmt = $conn->prepare("SELECT post_id FROM favorite_posts WHERE user_id = ?");
    $favStmt->bind_param("i", $userId);
    $favStmt->execute();
    $favResult = $favStmt->get_result();
    while ($favRow = $favResult->fetch_assoc()) {
        $favorited_posts[] = $favRow['post_id'];
    }
    $favStmt->close();
}
?>

<div class="hero-section">
    <div class="background-overlay"></div>
    <div class="hero-content">
        <h2 class="blog-title">Blog nấu ăn</h2>
        <h1 class="cooking-slogan">Học nấu ăn cùng <span class="great-vibes">Bếp Anh Tài</span></h1>
    </div>
</div>

<div class="content-wrapper">
    <div class="section-title">
        <h1>Tất cả <em>bài viết</em></h1>
    </div>

    <?php if ($search !== ''): ?>
        <div class="search-info">
            Kết quả tìm kiếm cho: <strong><?= htmlspecialchars($search) ?></strong> (<?= $total_posts ?> kết quả)
        </div>
    <?php endif; ?>

    <div class="dishes-section">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="dish-card" data-post-id="<?= $row['id'] ?>">
                    <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $row['id'] ?>">
                        <img src="<?= htmlspecialchars($row['thumbnail']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    </a>
                    <div class="favorite-icon <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>"
                        title="<?= isset($_SESSION['user_id']) ? 'Thêm vào yêu thích' : 'Cần đăng nhập để thêm yêu thích' ?>">
                        <i class="<?= (isset($_SESSION['user_id']) && in_array($row['id'], $favorited_posts)) ? 'fas' : 'far' ?> fa-heart"></i>
                    </div>
                    <div class="dish-content">
                        <h3>
                            <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h3>
                        <p class="dish-description"><?= htmlspecialchars($row['overview']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có bài viết nào.</p>
        <?php endif; ?>
        <?php $stmt->close(); ?>
    </div>

    <?php if (!$show_all_posts && $total_posts > $posts_per_page_initial): ?>
        <div class="view-all-wrapper">
            <a href="?show_all=true" class="view-all-btn">Xem tất cả</a>
        </div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
