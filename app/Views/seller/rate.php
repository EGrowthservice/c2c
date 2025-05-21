<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Đánh giá người bán</h2>
    <?php if ($error = \App\Helpers\Session::get('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php \App\Helpers\Session::unset('error'); ?>
    <?php endif; ?>
    <?php if ($success = \App\Helpers\Session::get('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php \App\Helpers\Session::unset('success'); ?>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="/sellers/rate/<?= $sellerId ?>">
                <div class="mb-3">
                    <label class="form-label">Điểm số (1-5)</label>
                    <select name="rating" class="form-control" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bình luận</label>
                    <textarea name="comment" class="form-control" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                <a href="/products" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
<style>
    .card { border-radius: 10px; }
    .form-control { font-size: 0.9rem; }
    @media (max-width: 576px) {
        .card-body { font-size: 0.9rem; }
        .btn { padding: 0.25rem 0.5rem; }
    }
</style>
<?php include __DIR__ . '/../layouts/footer.php'; ?>