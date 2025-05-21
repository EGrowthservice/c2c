<?php

namespace App\Helpers;

use App\Helpers\Session;
use App\Config\Database;

$db = (new Database())->getConnection();
$stmt = $db->prepare("
    SELECT o.*, od.fullname, od.phone, od.town_city, od.state, p.title AS product_title
    FROM orders o 
    JOIN order_detail od ON o.id = od.order_id 
    JOIN products p ON o.product_id = p.id
    WHERE o.id = ? AND o.buyer_id = ?
");
$stmt->execute([$orderId, Session::get('user')['id']]);
$order = $stmt->fetch(\PDO::FETCH_ASSOC);

$error = Session::get('error');
$success = Session::get('success');
if ($error) {
    Session::unset('error');
}
if ($success) {
    Session::unset('success');
}

include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../products/linkcss.php';
?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="confirmation container">
        <h1 class="mb-5">Xác nhận đơn hàng</h1>
        <div class="row">
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($order): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng #<?= htmlspecialchars($order['id']) ?></h5>
                            <p><strong>Sản phẩm:</strong> <?= htmlspecialchars($order['product_title']) ?></p>
                            <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status']) ?></p>
                            <p><strong>Tổng tiền:</strong> <?= number_format($order['total_price'], 2) ?> VND</p>
                            <p><strong>Số lượng:</strong> <?= htmlspecialchars($order['quantity']) ?></p>
                            <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['town_city'] . ', ' . $order['state']) ?></p>
                            <?php if ($order['tracking_number'] && $order['carrier']): ?>
                                <p><strong>Mã theo dõi:</strong> <?= htmlspecialchars($order['tracking_number']) ?></p>
                                <p><strong>Đơn vị vận chuyển:</strong> <?= htmlspecialchars($order['carrier']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Không tìm thấy đơn hàng.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($error): ?>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: '<?= htmlspecialchars($error) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
        <?php if ($success): ?>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: '<?= htmlspecialchars($success) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            });
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>