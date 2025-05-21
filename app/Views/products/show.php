<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Xác định đường dẫn dựa trên trạng thái đăng nhập
$userLink = Session::get('user') ? '/profile' : '/login';
$currentUserId = Session::get('user')['id'] ?? null;

// Bao gồm header
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/./linkcss.php';
?>

<main class="pt-5">
    <div class="container">
        <div class="mb-5"></div>
        <section class="shop-single">
            <div class="row g-4">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <?php if (!empty($product['image'])): ?>
                        <img src="/Uploads/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded-3" alt="<?= htmlspecialchars($product['title']) ?>">
                    <?php else: ?>
                        <div class="swiper-slide">
                            <img src="https://via.placeholder.com/600x600?text=Không+có+hình+ảnh" class="img-fluid rounded-3" alt="No Image">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="col-lg-6">
                    <div class="product-details p-4 bg-white shadow-sm rounded-3">
                        <h1 class="fs-3 fw-bold mb-3"><?= htmlspecialchars($product['title']) ?></h1>
                        <div class="product-price mb-3 d-flex align-items-center">
                            <span class="money price fs-2 fw-bold" style="color:#856404"><?= htmlspecialchars(number_format($product['price'], 0, ',', '.')) ?> VND</span>
                        </div>

                        <p class="mb-4 text-muted"><?= htmlspecialchars($product['description'] ?? 'Không có mô tả') ?></p>
                        <p class="mb-4"><strong>Người bán:</strong> <a href="/seller/<?= htmlspecialchars($product['user_id']) ?>" class="text-decoration-none text-primary"><?= htmlspecialchars($product['username'] ?? 'Không xác định') ?></a></p>
                        <p><strong>Lượt xem:</strong> <?php echo htmlspecialchars($product['views'] ?? 0); ?></p>

                        <!-- Actions -->
                        <div class="product-actions mb-4 d-flex gap-3">
                            <?php if ($currentUserId && $product['user_id'] == $currentUserId): ?>
                                <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-warning btn-md fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i> Sửa sản phẩm
                                </a>
                            <?php else: ?>
                                <?php if (Session::get('user')): ?>
                                    <button class="btn btn-primary btn-md fw-semibold add-to-cart" data-product-id="<?= $product['id'] ?>">
                                        <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                                    </button>
                                    <button class="btn btn-outline-danger btn-md fw-semibold add-to-favorites" data-product-id="<?= $product['id'] ?>">
                                        <i class="bi bi-heart-fill me-1"></i> Yêu thích
                                    </button>
                                    <a href="/reports/create/<?= $product['id'] ?>" class="btn btn-outline-secondary btn-md fw-semibold">
                                        <i class="bi bi-exclamation-circle me-1"></i> Báo cáo người bán
                                    </a>
                                <?php else: ?>
                                    <div class="login-required alert alert-warning d-flex align-items-center gap-2">
                                        <i class="bi bi-exclamation-circle"></i>
                                        Vui lòng <a href="/login" class="text-decoration-underline fw-semibold">đăng nhập</a> để thêm vào giỏ hàng!
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs for Description, Additional Info, Reviews -->
            <div class="mt-5">
                <ul class="nav nav-tabs border-bottom-0" id="product-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-semibold active" id="description-tab" data-bs-toggle="tab" href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Mô tả</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-semibold" id="reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">Đánh giá</a>
                    </li>
                </ul>
                <div class="tab-content p-4 bg-white shadow-sm rounded-3 mt-2" id="product-tabs-content">
                    <!-- Description Tab -->
                    <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="description-tab">
                        <h4 class="fw-bold mb-3">Mô tả sản phẩm</h4>
                        <p class="text-muted"><?= htmlspecialchars($product['description'] ?? 'Không có mô tả') ?></p>
                    </div>
                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <h4 class="fw-bold mb-3">Đánh giá sản phẩm</h4>
                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
    .product-details {
        transition: all 0.3s ease;
    }

    .product-details:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .product-price .money {
        font-weight: 700;
    }

    .product-rating .stars .star {
        font-size: 1.2rem;
        margin-right: 4px;
    }

    .product-actions .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .product-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .login-required {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 8px;
        padding: 12px 20px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .login-required:hover {
        background-color: #fff8e1;
        box-shadow: 0 2px 8px rgba(255, 221, 107, 0.4);
    }

    .nav-tabs .nav-link {
        color: #555;
        padding: 0.75rem 1.5rem;
        border: none;
        border-bottom: 3px solid transparent;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    @media (max-width: 576px) {
        .product-gallery .swiper-slide img {
            max-height: 400px;
        }

        .product-actions .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .product-details {
            padding: 1rem;
        }
    }
</style>

<?php
// Bao gồm footer
include __DIR__ . '/../layouts/footer.php';
?>

<script src="/assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
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
                            window.location.href = '/cart'; // Chuyển hướng đến giỏ hàng
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

        // Thêm vào yêu thích
        $('.add-to-favorites').on('click', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const $button = $(this);
            $.ajax({
                url: '/favorites/add',
                method: 'POST',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Sản phẩm đã được thêm vào danh sách yêu thích!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $button.toggleClass('btn-outline-danger btn-danger');
                        $button.find('i').toggleClass('bi-heart-fill bi-heart');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: response.message || 'Có lỗi xảy ra khi thêm vào yêu thích.',
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