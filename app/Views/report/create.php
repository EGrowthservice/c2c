<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Xác định đường dẫn dựa trên trạng thái đăng nhập
$userLink = Session::get('user') ? '/profile' : '/login';

// Lấy thông tin sản phẩm để hiển thị
use App\Models\Product;

$productModel = new Product();
$product = $productModel->getProductById($productId);

// Xử lý thông báo
$error = Session::get('error');
$success = Session::get('success');
if ($error) {
    Session::unset('error');
}
if ($success) {
    Session::unset('success');
}

// Bao gồm header
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../products/linkcss.php';
?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">


        <ul class="nav nav-tabs mb-5" id="report_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="report-tab" data-bs-toggle="tab" href="#tab-item-report"
                    role="tab" aria-controls="tab-item-report" aria-selected="true">Báo cáo người dùng</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="report_tab_content">
            <div class="tab-pane fade show active" id="tab-item-report" role="tabpanel" aria-labelledby="report-tab">
                <div class="login-form">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    <form id="report-form" method="POST" action="/reports/create/<?= $productId ?>" name="report-form" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <textarea class="form-control form-control_gray" name="reason" id="reason" rows="5" required></textarea>
                            <label for="reason">Lý do báo cáo *</label>
                        </div>
                        <button class="btn btn-danger w-100 text-uppercase" type="submit">Gửi báo cáo</button>
                        <div class="customer-option mt-4 text-center">
                            <a href="/products/<?= $productId ?>" class="btn-text">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Tích hợp SweetAlert2 và xử lý AJAX -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('report-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
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
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (data.success) {
                        // Quay lại trang chi tiết sản phẩm
                        window.location.href = '/products/<?= $productId ?>';
                    }
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Đã xảy ra lỗi khi gửi báo cáo!',
                    confirmButtonText: 'OK'
                });
            });
    });
</script>

<?php
// Bao gồm footer
include __DIR__ . '/../layouts/footer.php';
?>