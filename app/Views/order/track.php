<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Theo dõi đơn hàng #<?= $order['id'] ?></h2>
    <?php if ($error = \App\Helpers\Session::get('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php \App\Helpers\Session::unset('error'); ?>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sản phẩm: <?= htmlspecialchars($order['title']) ?></h5>
                    <p>Người bán: <?= htmlspecialchars($order['seller_name']) ?></p>
                    <p>Tổng tiền: <?= number_format($order['total_price'], 0, ',', '.') ?> VND</p>
                    <p>Trạng thái: 
                        <span class="badge bg-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <?php if ($order['tracking_number'] && $order['carrier']): ?>
                        <p>Số vận đơn: <?= htmlspecialchars($order['tracking_number']) ?></p>
                        <p>Hãng vận chuyển: <?= htmlspecialchars($order['carrier']) ?></p>
                        <a href="https://track.carrier.com/?track=<?= urlencode($order['tracking_number']) ?>" class="btn btn-info btn-sm" target="_blank">Kiểm tra vận chuyển</a>
                    <?php else: ?>
                        <p class="text-muted">Chưa có thông tin vận chuyển.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .card { border-radius: 10px; }
    .badge { font-size: 0.9rem; }
    @media (max-width: 576px) {
        .card-body { font-size: 0.9rem; }
        .btn-sm { padding: 0.25rem 0.5rem; }
    }
</style>
<?php include __DIR__ . '/../layouts/footer.php'; ?>