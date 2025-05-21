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

<main class="pt-5">
    <div class="container">
        <div class="mb-5"></div>
        <section class="favorites">
            <h1 class="fs-2 fw-bold mb-4">Sản phẩm yêu thích</h1>
            <div class="row">
                <div class="col-12">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (empty($favorites)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-heart fs-1 text-muted mb-3"></i>
                            <p class="text-muted fs-5">Bạn chưa có sản phẩm yêu thích nào.</p>
                            <a href="/products" class="btn btn-primary btn-md fw-semibold">Khám phá sản phẩm</a>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($favorites as $item): ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm rounded-3">
                                        <img src="/Uploads/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                                            <p class="card-text text-success fw-bold"><?= number_format($item['price'], 0, ',', '.') ?> VND</p>
                                            <div class="d-flex gap-2">
                                                <a href="/products/<?= htmlspecialchars($item['product_id']) ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                                    <i class="bi bi-eye me-1"></i> Xem chi tiết
                                                </a>
                                                <button class="btn btn-outline-primary btn-sm add-to-cart" data-product-id="<?= htmlspecialchars($item['product_id']) ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm js-remove-favorite" data-id="<?= htmlspecialchars($item['product_id']) ?>" title="Xóa khỏi yêu thích">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
    .favorites .card {
        transition: all 0.3s ease;
    }

    .favorites .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .favorites .card-img-top {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .favorites .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .favorites .btn-sm {
        padding: 0.5rem 1rem;
        border-radius: 6px;
    }

    .favorites .btn:hover {
        transform: translateY(-2px);
    }

    .alert-dismissible {
        border-radius: 8px;
        padding: 1rem 1.5rem;
    }

    .text-center .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
    }

    @media (max-width: 576px) {
        .favorites .card-img-top {
            height: 150px;
        }

        .favorites .card-title {
            font-size: 1rem;
        }

        .favorites .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Xóa sản phẩm yêu thích
        $('.js-remove-favorite').on('click', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            Swal.fire({
                icon: 'warning',
                title: 'Xác nhận',
                text: 'Bạn có chắc muốn xóa sản phẩm này khỏi yêu thích?',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/favorites/remove/' + productId,
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: response.message || 'Sản phẩm đã được xóa khỏi yêu thích!',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: response.message || 'Không thể xóa sản phẩm khỏi yêu thích!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Đã xảy ra lỗi khi xóa sản phẩm: ' + (xhr.responseJSON?.message || 'Lỗi kết nối server!'),
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        });

        // Thêm vào giỏ hàng
        $('.add-to-cart').on('click', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Sản phẩm đã được thêm vào giỏ hàng!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '/cart';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: response.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Lỗi kết nối server.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>