<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../products/linkcss.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Cập nhật đơn hàng #<?= $order['id'] ?></h2>
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
            <form method="POST" action="/seller/orders/update/<?= $order['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control" required>
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đã giao hàng</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Đã nhận</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số vận đơn</label>
                    <input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Hãng vận chuyển</label>
                    <input type="text" name="carrier" class="form-control" value="<?= htmlspecialchars($order['carrier'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="/seller/orders" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
<style>
    .card {
        border-radius: 10px;
    }

    .form-control {
        font-size: 0.9rem;
    }

    @media (max-width: 576px) {
        .card-body {
            font-size: 0.9rem;
        }

        .btn {
            padding: 0.25rem 0.5rem;
        }
    }
</style>
<?php include __DIR__ . '/../layouts/footer.php'; ?>