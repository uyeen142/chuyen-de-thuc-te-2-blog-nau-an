<div class="container congthuc-wrapper">
    <!-- TILE BANNER -->
    <div class="congthuc-banner-thumbnail"
         style="background-image: url('<?= htmlspecialchars($recipe['thumbnail']) ?>');">
    </div>

    <!-- GRID 2 CỘT -->
    <div class="congthuc-grid">
        <!-- CỘT TRÁI -->
        <div class="congthuc-main">
            <h1 class="congthuc-title"><?= htmlspecialchars($recipe['title']) ?></h1>
            <div class="congthuc-meta">
                Ngày đăng: <?= date('d/m/Y', strtotime($recipe['created_at'])) ?>
                <?php if (!empty($recipe['updated_at']) && $recipe['updated_at'] !== $recipe['created_at']): ?>
                    - Cập nhật: <?= date('d/m/Y', strtotime($recipe['updated_at'])) ?>
                <?php endif; ?>
            </div>

            <p class="congthuc-overview"><?= nl2br(htmlspecialchars($recipe['overview'])) ?></p>

            <div class="congthuc-steps">
                <h2>Các bước thực hiện</h2>
                <ol>
                    <?php foreach ($steps as $step): ?>
                        <li>
                            <strong>Bước <?= $step['order'] ?>:</strong>
                            <p><?= nl2br(htmlspecialchars($step['description'])) ?></p>
                            <?php if (!empty($step['image'])): ?>
                                <img src="<?= $step['image'] ?>" alt="Ảnh bước <?= $step['order'] ?>">
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <!-- CỘT PHẢI: NGUYÊN LIỆU -->
        <div class="congthuc-sidebar">
            <div class="congthuc-ingredients-box">
                <h2>Nguyên liệu cần chuẩn bị</h2>
                <ul>
                    <?php foreach ($ingredients as $ingredient): ?>
                        <li><?= $ingredient ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- CÂU CHÚC CUỐI TRANG -->
<p class="congthuc-footer-chuc">Bếp anh Tài chúc cả nhà mình thành công!</p>
