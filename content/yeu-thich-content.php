<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<p style='text-align: center; color: red;'>Vui lòng đăng nhập để xem danh sách yêu thích.</p>";
    return;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT p.* FROM posts p
        INNER JOIN favorite_posts f ON p.id = f.post_id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="favorite-wrapper">
    <div class="section-title">
        <h1>Bài viết <em>yêu thích</em></h1>
    </div>

    <div class="dishes-section">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="dish-card" data-post-id="<?= $row['id'] ?>">
                    <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $row['id'] ?>">
                        <img src="<?= htmlspecialchars($row['thumbnail']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    </a>
                    <div class="favorite-icon" title="Nhấn để gỡ khỏi yêu thích">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="dish-content">
                        <h3>
                            <a href="<?= BASE_PATH ?>/monan/congthuc.php?id=<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h3>
                        <p class="dish-description"><?= htmlspecialchars($row['overview']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-favorites-message">Bạn chưa thêm bài viết nào vào danh sách yêu thích.</p>
        <?php endif; ?>
        <?php $stmt->close(); ?>
    </div>
</div>

<?php $conn->close(); ?>