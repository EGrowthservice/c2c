<?php

namespace App\Helpers;

use App\Helpers\Session;

$error = Session::get('error');
$success = Session::get('success');
if ($error) {
    Session::unset('error');
}
if ($success) {
    Session::unset('success');
}

include __DIR__ . '/../layouts/header.php';
?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="cart container">
        <h1 class="mb-5">Giỏ hàng</h1>
        <div class="row">
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if (empty($cartItems)): ?>
                    <p class="text-muted">Giỏ hàng của bạn đang trống.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                foreach ($cartItems as $item):
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $subtotal += $itemTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <img src="/Uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </td>
                                        <td><?= number_format($item['price'], 2) ?> VND</td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td><?= number_format($itemTotal, 2) ?> VND</td>
                                        <td>
                                            <a href="/cart/remove/<?= htmlspecialchars($item['product_id']) ?>" class="btn btn-sm text-danger js-remove-cart" data-id="<?= htmlspecialchars($item['product_id']) ?>" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="text-end">
                            <p><strong>Tổng phụ:</strong> <?= number_format($subtotal, 2) ?> VND</p>
                            <p><strong>VAT (10%):</strong> <?= number_format($subtotal * 0.1, 2) ?> VND</p>
                            <p><strong>Tổng cộng:</strong> <?= number_format($subtotal * 1.1, 2) ?> VND</p>
                            <a href="/checkout" class="btn btn-primary">Thanh toán</a>
                        </div>
                    </div>
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
        const removeButtons = document.querySelectorAll('.js-remove-cart');
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-id');
                Swal.fire({
                    icon: 'warning',
                    title: 'Xác nhận',
                    text: 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/cart/remove/' + productId, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                Swal.fire({
                                    icon: data.success ? 'success' : 'error',
                                    title: data.success ? 'Thành công' : 'Lỗi',
                                    text: data.message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: data.success ? '#3085d6' : '#d33',
                                    timer: data.success ? 2000 : null,
                                    timerProgressBar: true
                                }).then(() => {
                                    if (data.success) {
                                        window.location.reload();
                                    }
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: 'Đã xảy ra lỗi khi xóa sản phẩm!',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#d33'
                                });
                            });
                    }
                });
            });
        });
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>