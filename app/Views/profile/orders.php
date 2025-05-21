<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Kiểm tra đăng nhập
if (!Session::get('user')) {
    Session::set('error', 'Vui lòng đăng nhập để xem đơn hàng!');
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
        </nav>

        <div class="row">
            <!-- Sidebar Menu -->
            <?php include __DIR__ . '/./layouts/nav.php'; ?>


            <!-- Nội dung chính -->
            <div class="col-lg-9">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if (empty($orders)): ?>
                    <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Sản phẩm</th>
                                    <th>Người mua</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($order['id']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($order['image'])): ?>
                                                    <img src="/Uploads/<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($order['title']) ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                <?php endif; ?>
                                                <?= htmlspecialchars($order['title']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                                        <td><?= number_format($order['total_price'], 0, ',', '.') ?> VND</td>
                                        <td>
                                            <?php
                                            $statusClass = match ($order['status']) {
                                                'pending' => 'secondary',     // Trạng thái chờ xử lý
                                                'processing' => 'primary',    // Đang xử lý
                                                'shipped' => 'info',          // Đã giao cho đơn vị vận chuyển
                                                'delivered' => 'success',     // Đã giao hàng
                                                'cancelled' => 'danger',      // Đã hủy
                                                default => 'dark',            // Không xác định
                                            };

                                            $statusText = match ($order['status']) {
                                                'pending' => 'Chờ xử lý',
                                                'processing' => 'Đang xử lý',
                                                'shipped' => 'Đã gửi hàng',
                                                'delivered' => 'Đã giao',
                                                'cancelled' => 'Đã hủy',
                                                default => 'Không rõ',
                                            };

                                            $statusTooltip = match ($order['status']) {
                                                'pending' => 'Đơn hàng đang chờ được xử lý bởi hệ thống.',
                                                'processing' => 'Đơn hàng đang được chuẩn bị để giao.',
                                                'shipped' => 'Đơn hàng đã được gửi cho đơn vị vận chuyển.',
                                                'delivered' => 'Đơn hàng đã được giao đến khách hàng.',
                                                'cancelled' => 'Đơn hàng đã bị hủy bởi khách hàng hoặc hệ thống.',
                                                default => 'Trạng thái đơn hàng không xác định.',
                                            };
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?> badge-status"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="<?= htmlspecialchars($statusTooltip) ?>">
                                                <?= htmlspecialchars($statusText) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a href="/seller/orders/update/<?= htmlspecialchars($order['id']) ?>" class="btn btn-primary btn-sm js-update-order" data-id="<?= htmlspecialchars($order['id']) ?>">Cập nhật</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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

        // Xử lý cập nhật đơn hàng
        const updateButtons = document.querySelectorAll('.js-update-order');
        if (updateButtons.length > 0) {
            updateButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-id');
                    window.location.href = '/seller/orders/update/' + orderId;
                });
            });
        }
    });
</script>

<style>
    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

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

    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }

    .btn-sm {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }

    .badge {
        font-size: 0.9rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.85rem;
        }

        .btn-sm {
            font-size: 0.75rem;
        }
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>