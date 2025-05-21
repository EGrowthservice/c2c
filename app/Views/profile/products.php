<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Kiểm tra đăng nhập
if (!Session::get('user')) {
    Session::set('error', 'Vui lòng đăng nhập để xem sản phẩm!');
    header('Location: /login');
    exit;
}

// Xác định đường dẫn
$userLink = Session::get('user') ? '/profile' : '/login';
$currentUserId = Session::get('user')['id'] ?? null;

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
            <?php include __DIR__ . '/./layouts/nav.php'; ?>


            <!-- Nội dung chính -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Sản phẩm đã đăng</h4>
                    <a href="/products/create" class="btn btn-success">Đăng sản phẩm mới</a>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <div class="row">
                    <?php if (empty($products)): ?>
                        <p class="text-muted">Bạn chưa đăng sản phẩm nào.</p>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="/Uploads/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/200x200?text=Không+có+hình" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                        <p class="card-text"><?= number_format($product['price'], 0, ',', '.') ?> VND</p>
                                        <p class="card-text">
                                            <?php
                                            $statusClass = match ($product['status']) {
                                                'approved' => 'success',
                                                'pending' => 'warning',
                                                default => 'danger',
                                            };
                                            $statusText = match ($product['status']) {
                                                'approved' => 'Đã duyệt',
                                                'pending' => 'Chờ duyệt',
                                                default => 'Từ chối',
                                            };
                                            $statusTooltip = match ($product['status']) {
                                                'approved' => 'Sản phẩm đã được phê duyệt và hiển thị công khai.',
                                                'pending' => 'Sản phẩm đang chờ quản trị viên phê duyệt.',
                                                default => 'Sản phẩm bị từ chối do không đáp ứng yêu cầu.',
                                            };
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?> badge-status" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($statusTooltip) ?>">
                                                <?= htmlspecialchars($statusText) ?>
                                            </span>
                                        </p>
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <a href="/products/<?= htmlspecialchars($product['id']) ?>" class="btn btn-sm m-1 text-primary" title="Xem sản phẩm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/products/edit/<?= htmlspecialchars($product['id']) ?>" class="btn btn-sm m-1 text-warning" title="Sửa sản phẩm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/products/delete/<?= htmlspecialchars($product['id']) ?>" class="btn btn-sm m-1 text-danger js-delete-product" data-id="<?= htmlspecialchars($product['id']) ?>" title="Xóa sản phẩm">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Tích hợp SweetAlert2 và Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        // Khởi tạo tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

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

        // Xử lý xóa sản phẩm
        const deleteButtons = document.querySelectorAll('.js-delete-product');
        if (deleteButtons.length > 0) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-id');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Xác nhận',
                        text: 'Bạn có chắc muốn xóa sản phẩm này?',
                        showCancelButton: true,
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/products/delete/' + productId, {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': '<?= Session::get('csrf_token') ?>'
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
        }
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
        transition: transform 0.2s;
        border-radius: 10px;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-img-top {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .btn-sm {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }

    .badge-status {
        font-size: 1rem;
        padding: 0.5em 1em;
        font-weight: 600;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    @media (max-width: 576px) {
        .card {
            font-size: 0.85rem;
        }

        .btn-sm {
            font-size: 0.75rem;
        }

        .badge-status {
            font-size: 0.9rem;
        }
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>