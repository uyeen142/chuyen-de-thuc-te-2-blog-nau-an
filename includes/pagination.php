<?php
if (!isset($totalPages) || !isset($currentPage)) {
    return;
}
?>
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a href="<?php echo buildPageLink($currentPage - 1); ?>" class="page-link">&lt; Trở về</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?php echo buildPageLink($i); ?>" class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?php echo buildPageLink($currentPage + 1); ?>" class="page-link">Tiếp &gt;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
// Hàm hỗ trợ giữ nguyên query string khác
function buildPageLink(int $page): string {
    $qs = $_GET;
    $qs['page'] = $page;

    $path = $_SERVER['PHP_SELF']; // Đường dẫn hiện tại như /monan/chay.php
    return $path . '?' . http_build_query($qs);
}

?>
