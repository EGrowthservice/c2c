<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Xác định đường dẫn dựa trên trạng thái đăng nhập
$userLink = Session::get('user') ? '/profile' : '/login';

// Bao gồm header
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/./linkcss.php';

?>

<main class="pt-90">
    <div class="mb-4 pb-5"></div>
    <div class="mb-4 pb-3"></div>
    <section class="shop-main container">
        <!-- Actions and Filters -->
        <div class="shop-acs row">
            <div class="col-auto d-flex align-items-center mb-4">
                <form method="GET" id="search-form" action="/products/search" class="search-field position-relative">
                    <div class="position-relative">
                        <input
                            id="search-input"
                            class="search-field__input w-100 border rounded-1 form-select"
                            type="text"
                            name="keyword"
                            value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>"
                            placeholder="Tìm kiếm sản phẩm...">
                        <button class="btn-icon search-popup__submit pb-0 me-2" type="submit">
                            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_search" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-auto ms-auto d-flex align-items-center mb-4">
                <div class="dropdown">
                    <select class="form-select w-auto" onchange="location = this.value;">
                        <option value="/products?sort=latest" <?php if (!isset($_GET['sort']) || $_GET['sort'] === 'latest') echo 'selected'; ?>>Mới nhất</option>
                        <option value="/products?sort=featured" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'featured') echo 'selected'; ?>>Nổi bật</option>
                        <option value="/products?sort=popular" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'popular') echo 'selected'; ?>>Phổ biến</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="products-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php if (empty($products)): ?>
                <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                        <div class="pc__img-wrapper">
                            <a href="/products/<?= htmlspecialchars($product['id']) ?>">
                                <?php if (!empty($product['image'])): ?>
                                    <img loading="lazy" src="/Uploads/<?= htmlspecialchars($product['image']) ?>" class="pc__img" alt="<?= htmlspecialchars($product['title']) ?>" style="height: 300px; object-fit: cover;">
                                <?php else: ?>
                                    <img loading="lazy" src="https://via.placeholder.com/300x300?text=Không+hình" class="pc__img" alt="No Image" style="height: 300px; object-fit: cover;">
                                <?php endif; ?>
                            </a>

                        </div>
                        <div class="pc__info position-relative">
                            <h6 class="pc__title"><a href="/products/<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['title']) ?></a></h6>
                            <div class="product-card__price d-flex">
                                <span class="money price"><?= htmlspecialchars(number_format($product['price'], 0, ',', '.')) ?> VND</span>
                            </div>
                            <p class="card-text text-muted">Người đăng: <?= htmlspecialchars($product['username'] ?? 'Không xác định') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination (Placeholder, adjust based on your backend logic) -->
        <div class="products-grid__footer d-flex justify-content-center">
            <ul class="pagination">
                <?php
                $baseUrl = '/products?';
                if (!empty($_GET['keyword'])) {
                    $baseUrl .= 'keyword=' . urlencode($_GET['keyword']) . '&';
                }
                if (!empty($_GET['sort'])) {
                    $baseUrl .= 'sort=' . urlencode($_GET['sort']) . '&';
                }

                // Nút "Trước"
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=' . ($page - 1) . '">Trước</a></li>';
                }

                // Các số trang
                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . 'page=' . $i . '">' . $i . '</a></li>';
                }

                // Nút "Sau"
                if ($page < $totalPages) {
                    echo '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=' . ($page + 1) . '">Sau</a></li>';
                }
                ?>
            </ul>
        </div>

    </section>
</main>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 10px;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .pc__img {
        transition: opacity 0.2s;
    }

    .pc__img:hover {
        opacity: 0.9;
    }

    .pc__atc {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s, transform 0.3s;
    }

    .product-card:hover .pc__atc {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addWishlistButtons = document.querySelectorAll('.js-add-wishlist');
        addWishlistButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                fetch('/favorites/add/' + productId, {
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
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Đã xảy ra lỗi khi thêm vào yêu thích!',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    });
            });
        });
    });
</script>
<?php
// Bao gồm footer
include __DIR__ . '/../layouts/footer.php';
?>