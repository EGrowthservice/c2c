<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Hồ sơ người bán: <?= htmlspecialchars($seller['username']) ?></h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Điểm đánh giá trung bình:</strong> <?= number_format($seller['average_rating'], 2) ?> / 5 (<?= $seller['rating_count'] ?> đánh giá)</p>
            <?php if (\App\Helpers\Session::get('user')): ?>
                <a href="/sellers/rate/<?= $sellerId ?>" class="btn btn-primary btn-sm">Đánh giá người bán</a>
            <?php endif; ?>
        </div>
    </div>
    <h3 class="mb-3">Đánh giá từ người mua</h3>
    <?php if (empty($ratings)): ?>
        <p class="text-muted">Chưa có đánh giá nào.</p>
    <?php else: ?>
        <?php foreach ($ratings as $rating): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <p><strong><?= htmlspecialchars($rating['buyer_name']) ?></strong>: <?= str_repeat('⭐', $rating['rating']) ?></p>
                    <p><?= htmlspecialchars($rating['comment'] ?? 'Không có bình luận') ?></p>
                    <small class="text-muted"><?= $rating['created_at'] ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<style>
    .card { border-radius: 10px; }
    .card-body { font-size: 0.9rem; }
    @media (max-width: 576px) {
        .card-body { font-size: 0.85rem; }
        .btn-sm { padding: 0.25rem 0.5rem; }
    }
</style>
<?php include __DIR__ . '/../layouts/footer.php'; ?>