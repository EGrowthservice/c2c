<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Kiểm tra đăng nhập
if (!Session::get('user')) {
    Session::set('error', 'Vui lòng đăng nhập để xem chi tiết tài khoản!');
    header('Location: /login');
    exit;
}

// Xác định đường dẫn
$userLink = Session::get('user') ? '/profile' : '/login';

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
    <section class="my-account container">




        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-lg-3">
                <ul class="account-nav">
                    <li><a href="/profile" class="account-nav__link">Tổng quan</a></li>
                    <li><a href="/profile/orders" class="account-nav__link">Đơn hàng</a></li>
                    <li><a href="/profile/products" class="account-nav__link">Sản phẩm</a></li>
                    <li><a href="/profile/account-details" class="account-nav__link active">Chi tiết tài khoản</a></li>
                    <li><a href="/logout" class="account-nav__link">Đăng xuất</a></li>
                </ul>
            </div>

            <!-- Nội dung chính -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <p><strong>Tên:</strong> <?= htmlspecialchars(Session::get('user')['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars(Session::get('user')['email']) ?></p>
                        <a href="/profile/change-password" class="btn btn-primary">Đổi mật khẩu</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Tích hợp SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/plugins/bootstrap-slider.min.js"></script>
<script src="assets/js/plugins/swiper.min.js"></script>
<script>
    // Tránh tải trùng lặp countdown.js và theme.js
    if (!window.countdownLoaded) {
        document.write('<script src="assets/js/plugins/countdown.js"><\/script>');
        window.countdownLoaded = true;
    }
    if (!window.themeLoaded) {
        document.write('<script src="assets/js/theme.js"><\/script>');
        window.themeLoaded = true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Hiển thị thông báo từ Session
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

<style>
    .account-nav {
        list-style: none;
        padding: 0;
    }

    .account-nav__link {
        display: block;
        padding: 10px 0;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .account-nav__link:hover,
    .account-nav__link.active {
        color: #007bff;
    }

    .card {
        border-radius: 10px;
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>