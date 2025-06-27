<?php
// BLOGWEBSITE/content/monan-category-content.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/blogwebsite');
}

if (!isset($categoryTitle) || !isset($categoryDescription) || !isset($dishesData)) {
    $categoryTitle = "Danh mục không xác định";
    $categoryDescription = "Đã xảy ra lỗi khi tải thông tin danh mục hoặc chưa có dữ liệu.";
    $dishesData = [];
}
?>

<div class="container monan-category-page">
    <div class="intro-box">
        <h1 class="section-title"><?php echo htmlspecialchars($categoryTitle); ?></h1>
        <p class="intro-text"><?php echo htmlspecialchars($categoryDescription); ?></p>
        
        <form action="" method="GET" class="search-bar-inner">
            <input type="text" placeholder="Tìm kiếm bài viết..." class="search-input-inner" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <button type="submit" class="search-button-inner">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="dishes-grid">
        <?php if (!empty($dishesData)): ?>
            <?php foreach ($dishesData as $dish): ?>
                <?php
                    $displayTitle = preg_replace('/^Hướng Dẫn\s*/iu', '', $dish['title']);
                    $isFavorited = isset($_SESSION['user_id']) && in_array($dish['id'], $favorited_posts ?? []);
                ?>
                <div class="dish-card" data-post-id="<?= $dish['id'] ?>">
                    <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $dish['id'] ?>">
                        <img src="<?= htmlspecialchars($dish['thumbnail']) ?>" alt="<?= htmlspecialchars($dish['title']) ?>" class="dish-img">
                    </a>

                    <div class="favorite-icon <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>"
                         title="<?= isset($_SESSION['user_id']) ? 'Thêm vào yêu thích' : 'Cần đăng nhập để thêm yêu thích' ?>">
                        <i class="<?= $isFavorited ? 'fas' : 'far' ?> fa-heart"></i>
                    </div>

                    <div class="dish-content">
                        <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $dish['id'] ?>" class="dish-title">
                            <?= htmlspecialchars($displayTitle); ?>
                        </a>
                        <p class="dish-description"><?= htmlspecialchars(truncateText($dish['overview'], 150)); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center w-100">Hiện chưa có món ăn nào trong danh mục này.</p>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/pagination.php'; ?>
</div>