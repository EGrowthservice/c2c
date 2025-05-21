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
    <section class="checkout container">
        <h1 class="mb-5">Thanh toán</h1>
        <div class="row">
            <div class="col-lg-6">
                <h3>Chi tiết giao hàng</h3>
                <form method="POST" action="/checkout/process" id="checkoutForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và tên *</label>
                        <input type="text" class="form-control form-control_md" name="fullname" id="fullname" required>
                        <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại *</label>
                        <input type="text" class="form-control form-control_md" name="phone" id="phone" required>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
                    </div>
                    <div class="mb-3">
                        <label for="pincode" class="form-label">Mã bưu điện *</label>
                        <input type="text" class="form-control form-control_md" name="pincode" id="pincode" required>
                        <div class="invalid-feedback">Vui lòng nhập mã bưu điện.</div>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">Tỉnh/Thành phố *</label>
                        <input type="text" class="form-control form-control_md" name="state" id="state" required>
                        <div class="invalid-feedback">Vui lòng nhập tỉnh/thành phố.</div>
                    </div>
                    <div class="mb-3">
                        <label for="town_city" class="form-label">Quận/Huyện *</label>
                        <input type="text" class="form-control form-control_md" name="town_city" id="town_city" required>
                        <div class="invalid-feedback">Vui lòng nhập quận/huyện.</div>
                    </div>
                    <div class="mb-3">
                        <label for="house_no" class="form-label">Số nhà, Tên tòa nhà *</label>
                        <input type="text" class="form-control form-control_md" name="house_no" id="house_no" required>
                        <div class="invalid-feedback">Vui lòng nhập số nhà.</div>
                    </div>
                    <div class="mb-3">
                        <label for="road_name" class="form-label">Tên đường, Khu vực *</label>
                        <input type="text" class="form-control form-control_md" name="road_name" id="road_name" required>
                        <div class="invalid-feedback">Vui lòng nhập tên đường.</div>
                    </div>
                    <div class="mb-3">
                        <label for="landmark" class="form-label">Điểm mốc *</label>
                        <input type="text" class="form-control form-control_md" name="landmark" id="landmark" required>
                        <div class="invalid-feedback">Vui lòng nhập điểm mốc.</div>
                    </div>
            </div>
            <div class="col-lg-6">
                <h3>Đơn hàng của bạn</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        foreach ($cartItems as $item):
                            $itemSubtotal = $item['price'] * $item['quantity'];
                            $itemVat = $itemSubtotal * 0.1;
                            $itemTotal = $itemSubtotal + $itemVat;
                            $subtotal += $itemTotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?> x <?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= number_format($itemTotal, 2) ?> VND</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Tổng cộng</th>
                            <td><?= number_format($subtotal, 2) ?> VND</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="payment_method" value="cod" id="cod" checked>
                        <label class="form-check-label" for="cod">Thanh toán khi nhận hàng</label>
                        <p>Thanh toán bằng tiền mặt khi nhận hàng.</p>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="payment_method" value="vnpay" id="vnpay">
                        <label class="form-check-label" for="vnpay">VNPay</label>
                        <p>Thanh toán trực tuyến qua VNPay.</p>
                    </div>
                </div>
                <p>Dữ liệu cá nhân của bạn sẽ được sử dụng để xử lý đơn hàng, hỗ trợ trải nghiệm trên website này, và các mục đích khác được mô tả trong chính sách bảo mật của chúng tôi.</p>
                <button type="submit" class="btn btn-primary w-100">Đặt hàng</button>
                </form>
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
        const form = document.getElementById('checkoutForm');
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.classList.add('was-validated');
                return;
            }
        });

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